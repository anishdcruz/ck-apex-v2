<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Invoice\Invoice;
use App\Quotation\Quotation;
use App\SalesOrder\SalesOrder;
use App\Client;
use DB;

class InvoiceController extends Controller
{
    public function index()
    {
        return api([
            'data' => Invoice::with('client', 'currency')->search()
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
            'number' => counter()->next('invoice'),
            'reference' => null,
            'date' => date('Y-m-d'),
            'due_date' => null,
            'terms' => null,
            'items' => [
                [
                    'product' => null,
                    'product_id' => null,
                    'unit_price' => 0,
                    'qty' => 1,
                    'tax' => []
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
            'reference' => 'nullable|max:255',
            'date' => 'required|date_format:Y-m-d',
            'due_date' => 'nullable|date_format:Y-m-d',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.qty' => 'required|numeric|min:0',
            'items.*.taxes.*.name' => 'required|max:255',
            'items.*.taxes.*.rate' => 'required|numeric|min:0',
            'items.*.taxes.*.tax_authority' => 'required|max:255',
            'terms' => 'nullable|max:2000',
            'parent_type' => 'sometimes|required|alpha_dash|in:quotation,sales_order',
            'parent_id' => 'required_with:parent_type|integer|exists:'.$request->parent_type.'s,id'
        ]);

        $model = new Invoice();
        $model->fill($request->except('items'));

        $model->user_id = auth()->id();
        $model->status_id = Invoice::DRAFT;
        $model->amount_paid = 0;


        // detemine the parent, if parent_id and parent_type present
        if($request->has('parent_type')) {
            switch ($request->parent_type) {
                case 'quotation':
                    $model->invoiceable_type = 'App\Quotation\Quotation';
                    $model->invoiceable_id = $request->parent_id;
                    break;

                case 'sales_order':
                    $model->invoiceable_type = 'App\SalesOrder\SalesOrder';
                    $model->invoiceable_id = $request->parent_id;
                    break;

                default:
                    abort(404, 'Invalid Operation');
                    break;
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

            $model->number = counter()->next('invoice');

            $model->storeHasMany([
                'items.taxes' => $request->items
            ]);

           //  update parent quotation to sales ordered
            if($model->invoiceable) {
               $invoiceable = $model->invoiceable;
               if($model->invoiceable_type == 'App\Quotation\Quotation') {
                    if(in_array($invoiceable->status_id, [Quotation::ACCEPTED, Quotation::SENT])) {
                        $invoiceable->status_id = Quotation::INVOICED;
                        $invoiceable->save();
                    }
               } else if($model->invoiceable_type == 'App\SalesOrder\SalesOrder') {
                    if(in_array($invoiceable->status_id, [SalesOrder::SENT, SalesOrder::CONFIRMED, SalesOrder::ISSUED])) {
                        $invoiceable->status_id = SalesOrder::CLOSED;
                        $invoiceable->save();
                    }
               }
           }

            counter()->increment('invoice');

            return $model;
        });

        return api([
            'saved' => true,
            'id' => $model->id
        ]);
    }

    public function show($id)
    {
        $data =  Invoice::with([
            'items.product', 'items.taxes', 'client', 'currency', 'invoiceable',
            'clientPayments.parent.currency',
            'advancePayments.parent.currency'
        ])
            ->findOrFail($id);
        return api([
            'data' => $data
        ]);
    }

    public function pdf($id, Request $request)
    {
        $data = Invoice::with(['items.product', 'items.taxes', 'client', 'currency', 'invoiceable'])->findOrFail($id);
        $doc  = 'docs.invoice';

        if($request->has('mode') && $request->mode == 'delivery') {
            $doc = 'docs.delivery';
        }

        return pdf($doc, $data);
    }

    public function edit($id, Request $request)
    {
        $form = Invoice::with(['items.product', 'items.taxes', 'client', 'currency'])->findOrFail($id);

        if($request->has('mode')) {
            switch ($request->mode) {
                case 'clone':

                    $form->number = counter()->next('invoice');
                    $form->date = date('Y-m-d');
                    $form->due_date = null;
                    $form->reference = null;
                    unset($form->invoiceable_id);
                    unset($form->invoiceable_type);
                    unset($form->amount_paid);
                    break;

                default:
                    abort(404, 'Invalid Mode');
                    break;
            }
        } else {
            // abort if not editable
            abort_if(!$form->is_editable, 404);
        }

        return api([
            'form' => $form
        ]);
    }

    public function update($id, Request $request)
    {
        $model = Invoice::findOrFail($id);

        // abort if not editable
        abort_if(!$model->is_editable, 404);

        $request->validate([
            'client_id' => 'required|integer|exists:clients,id',
            'currency_id' => 'required|integer|exists:currencies,id',
            'reference' => 'nullable|max:255',
            'date' => 'required|date_format:Y-m-d',
            'due_date' => 'nullable|date_format:Y-m-d',
            'items' => 'required|array|min:1',
            'items.*.id' => 'sometimes|integer|exists:invoice_items,id,invoice_id,'.$model->id,
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.qty' => 'required|numeric|min:0',
            'items.*.taxes.*.name' => 'required|max:255',
            'items.*.taxes.*.rate' => 'required|numeric|min:0',
            'items.*.taxes.*.tax_authority' => 'required|max:255',
            'terms' => 'nullable|max:2000'
        ]);

        $model->fill($request->except('items'));

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
        $model = Invoice::findOrFail($id);

        $request->validate([
            'status' => 'required|integer|in:2'
        ]);

        switch ($request->status) {
            case (Invoice::SENT) :
                // must be draft
                abort_if(!in_array($model->status_id, [
                    Invoice::DRAFT
                ]), 404);

                $model->status_id = Invoice::SENT;
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
        $model = Invoice::findOrFail($id);

        // check whether this particular invoice belongs to
        // payments etc.
        $clientPayments = $model->clientPayments()->count();
        $advancePayments = $model->advancePayments()->count();

        // if yes provide warning

        if($clientPayments || $advancePayments || !$model->is_editable) {
            return api([
                'message' => 'Delete all the invoice relations first',
                'errors' => []
            ], 422);
        }

        $model->items()->delete();
        $model->delete();

        return api([
            'deleted' => true
        ]);
    }
}
