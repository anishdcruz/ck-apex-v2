<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SalesOrder\SalesOrder;
use App\Quotation\Quotation;
use App\Client;
use DB;

class SalesOrderController extends Controller
{
    public function index()
    {
        return api([
            'data' => SalesOrder::with(['client', 'currency'])->search()
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'client_id' => 'sometimes|required|integer|exists:clients,id'
        ]);

        $form = [
            'client_id' => null,
            'client' => null,
            'number' => counter()->next('sales_order'),
            'reference' => null,
            'date' => date('Y-m-d'),
            'terms' => null,
            'items' => [
                [
                    'product' => null,
                    'product_id' => null,
                    'unit_price' => 0,
                    'qty' => 1,
                    'taxes' => []
                ]
            ]
        ];

        if($request->has('client_id')) {
            $client = Client::with(['currency'])->findOrFail($request->client_id);

            array_set($form, 'client_id', $client->id);
            array_set($form, 'client', $client);
            array_set($form, 'currency_id', $client->currency->id);
            array_set($form, 'currency', $client->currency);

        } else {
            $form = array_merge($form, currency()->defaultToArray());
        }

        return api([
            'form' => $form
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|integer|exists:clients,id',
            'currency_id' => 'required|integer|exists:currencies,id',
            'quotation_id' => 'sometimes|required|exists:quotations,id',
            'reference' => 'nullable|max:255',
            'date' => 'required|date_format:Y-m-d',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.qty' => 'required|numeric|min:0',
            'items.*.taxes.*.name' => 'required|max:255',
            'items.*.taxes.*.rate' => 'required|numeric|min:0',
            'items.*.taxes.*.tax_authority' => 'required|max:255',
            'terms' => 'nullable|max:2000',
            'document' => 'nullable|image|max:2048'
        ]);

        $model = new SalesOrder();
        $model->fill($request->except('items', 'document'));

        $model->user_id = auth()->id();
        $model->status_id = SalesOrder::DRAFT;

         // upload document if exists
        if($request->hasFile('document') && $request->file('document')->isValid()) {
            // store in public uploads folder by default
           if($fileName = uploadFile($request->document)) {
                $model->document = $fileName;
           }
        }

        $model->quotation_id = $request->get('quotation_id', null);

        $items = collect($request->items);
        $model->sub_total = $items->sum(function($item) {
            return $item['qty'] * $item['unit_price'];
        });

        // add tax
        $totalTax = $items->reduce(function($carry, $item) {
            if(isset($item['taxes']) && count($item['taxes']) > 0) {
                $taxes = collect($item['taxes'])->reduce(function($c, $tax)  use ($item) {
                    return $c + (($item['unit_price'] * $item['qty']) * $tax['rate'] / 100);
                }, 0);
                return $carry + $taxes;
            } else {
                return 0;
            }
        }, 0);

        // total tax
        $model->total = $model->sub_total + $totalTax;

        $model = DB::transaction(function() use ($model, $request) {

            $model->number = counter()->next('sales_order');

            $model->storeHasMany([
                'items.taxes' => $request->items
            ]);

            // update parent quotation to sales ordered
            if($model->quotation_id) {
               $quotation = $model->quotation;
               if(in_array($quotation->status_id, [Quotation::ACCEPTED, Quotation::SENT])) {
                   $quotation->status_id = Quotation::SALES_ORDERED;
                   $quotation->save();
               }
           }

            counter()->increment('sales_order');

            return $model;
        });

        return api([
            'saved' => true,
            'id' => $model->id
        ]);
    }

    public function show($id)
    {
        $data = SalesOrder::with(['items.product','items.taxes', 'client', 'currency', 'quotation'])
            ->findOrFail($id);
        return api([
            'data' => $data
        ]);
    }

    public function pdf($id, Request $request)
    {
        $data = SalesOrder::with(['items.product','items.taxes', 'client', 'currency', 'quotation'])
            ->findOrFail($id);

        $doc  = 'docs.sales_order';

        if($request->has('mode') && $request->mode == 'pick') {
            $doc = 'docs.pick';
        }

        return pdf($doc, $data);
    }

    public function edit($id, Request $request)
    {
        $form = SalesOrder::with(['items.product', 'items.taxes', 'client', 'currency'])->findOrFail($id);

        if($request->has('mode')) {
            switch ($request->mode) {
                case 'clone':

                    $form->number = counter()->next('sales_order');
                    $form->date = date('Y-m-d');
                    $form->reference = null;
                    unset($form->quotation_id);
                    unset($form->document);
                    break;

                case 'invoice':

                    $form->number = counter()->next('invoice');
                    $form->date = date('Y-m-d');
                    $form->reference = null;
                    $form->parent_id = $form->id;
                    $form->parent_type = 'sales_order';
                    unset($form->quotation_id);
                    unset($form->document);
                    break;
                case 'goods_issue':
                    $form->sales_order_id = $form->id;
                    $form->sales_order_number = $form->number;
                    $form->number = counter()->next('goods_issue');
                    $form->date = date('Y-m-d');
                    $form->reference = null;
                    unset($form->quotation_id);
                    unset($form->document);
                    $items = $form->items->map(function($query) {
                        $query->qty = $query->qty - $query->qty_issued;
                        if($query->qty > 0) {
                            $query->qty_issued = 0;
                            return $query;
                        }
                    })->reject(function($item) {
                        return is_null($item);
                    });
                    unset($form->items);
                    $form->items = $items;
                    break;
                    break;

                default:
                    abort(404, 'Invalid Mode');
                    break;
            }
        } else {
            // abort if not editable
            abort_if(!$form->is_editable, 404);
        }

        unset($form->document);

        return api([
            'form' => $form
        ]);
    }

    public function update($id, Request $request)
    {
        $model = SalesOrder::findOrFail($id);

        // abort if not editable
        abort_if(!$model->is_editable, 404);

        $request->validate([
            'client_id' => 'required|integer|exists:clients,id',
            'currency_id' => 'required|integer|exists:currencies,id',
            'reference' => 'nullable|max:255',
            'date' => 'required|date_format:Y-m-d',
            'items' => 'required|array|min:1',
            'items.*.id' => 'sometimes|integer|exists:sales_order_items,id,sales_order_id,'.$model->id,
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.qty' => 'required|numeric|min:0',
            'items.*.taxes.*.name' => 'required|max:255',
            'items.*.taxes.*.rate' => 'required|numeric|min:0',
            'items.*.taxes.*.tax_authority' => 'required|max:255',
            'terms' => 'nullable|max:2000',
            'document' => 'nullable|image|max:2048'
        ]);

        $model->fill($request->except('items'));

        // upload document if exists
        if($request->hasFile('document') && $request->file('document')->isValid()) {
            // store in public uploads folder by default
           if($fileName = uploadFile($request->document)) {
                // overwrite previous uploaded file
                deleteFile($model->document);
                $model->document = $fileName;
           }
        }

        $items = collect($request->items);
        $model->sub_total = $items->sum(function($item) {
            return $item['qty'] * $item['unit_price'];
        });

         // add tax
        $totalTax = $items->reduce(function($carry, $item) {
            if(isset($item['taxes']) && count($item['taxes']) > 0) {
                $taxes = collect($item['taxes'])->reduce(function($c, $tax)  use ($item) {
                    return $c + (($item['unit_price'] * $item['qty']) * $tax['rate'] / 100);
                }, 0);
                return $carry + $taxes;
            } else {
                return 0;
            }
        }, 0);
        // total tax
        $model->total = $model->sub_total + $totalTax;

        $model = DB::transaction(function() use ($model, $request) {

            $model->updateHasMany([
                'items.taxes' => $request->items
            ]);

            return $model;
        });

        return api([
            'saved' => true,
            'id' => $model->id
        ]);
    }

    public function markAs($id, Request $request)
    {
        $model = SalesOrder::findOrFail($id);

        $request->validate([
            'status' => 'required|integer|in:2,3,4,5,6'
        ]);

        switch ($request->status) {
            case (SalesOrder::SENT) :
                // must be draft
                abort_if(!in_array($model->status_id, [
                    SalesOrder::DRAFT
                ]), 404);

                $model->status_id = SalesOrder::SENT;
                break;

            case (SalesOrder::CONFIRMED) :
                // must be draft
                abort_if(!in_array($model->status_id, [
                    SalesOrder::DRAFT, SalesOrder::SENT,
                    SalesOrder::HOLD
                ]), 404);

                $model->status_id = SalesOrder::CONFIRMED;
                break;

            case (SalesOrder::HOLD) :
                // must be draft
                abort_if(!in_array($model->status_id, [
                    SalesOrder::DRAFT, SalesOrder::SENT,
                    SalesOrder::CONFIRMED
                ]), 404);

                $model->status_id = SalesOrder::HOLD;
                break;

            case (SalesOrder::CLOSED) :
                // must be draft
                abort_if(!in_array($model->status_id, [
                    SalesOrder::CONFIRMED
                ]), 404);

                $model->status_id = SalesOrder::CLOSED;
                break;

            case (SalesOrder::VOID) :
                // must be draft
                abort_if(!in_array($model->status_id, [
                    SalesOrder::DRAFT, SalesOrder::SENT,
                    SalesOrder::CONFIRMED,  SalesOrder::CLOSED,
                    SalesOrder::HOLD
                ]), 404);

                $model->status_id = SalesOrder::VOID;
                break;

            default:
                abort(404, 'Invalid Operation');
                break;
        }

        $model->save();

        return api([
            'saved' => true,
            'id' => $model->id,
            'status_id' => $model->status_id,
            'is_editable' => $model->is_editable
        ]);
    }

    public function destroy($id)
    {
        $model = SalesOrder::findOrFail($id);

        // check whether this particular sales order belongs to
        $invoices = $model->invoices()->count();

        // invoice, etc.
        // if yes provide warning

        if($invoices || !$model->is_editable) {
            return api([
                'message' => 'Delete all the sales order relations first',
                'errors' => []
            ], 422);
        }

        $model->items()->delete();

        // delete uploaded file
        deleteFile($model->document);

        $model->delete();

        return api([
            'deleted' => true
        ]);
    }
}
