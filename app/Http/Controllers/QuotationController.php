<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Quotation\Quotation;
use App\Client;
use DB;

class QuotationController extends Controller
{
    public function index()
    {
        return api([
            'data' => Quotation::with(['client', 'currency'])->search()
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
            'number' => counter()->next('quotation'),
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
            'reference' => 'nullable|max:255',
            'date' => 'required|date_format:Y-m-d',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.qty' => 'required|numeric|min:0',
            'items.*.taxes.*.name' => 'required|max:255',
            'items.*.taxes.*.rate' => 'required|numeric|min:0',
            'items.*.taxes.*.tax_authority' => 'required|max:255',
            'terms' => 'nullable|max:2000'
        ]);

        $model = new Quotation();
        $model->fill($request->except('items'));

        $model->user_id = auth()->id();
        $model->status_id = Quotation::DRAFT;

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

            $model->number = counter()->next('quotation');
            $model->storeHasMany([
                'items.taxes' => $request->items
            ]);

            counter()->increment('quotation');

            return $model;
        });

        return api([
            'saved' => true,
            'id' => $model->id
        ]);
    }

    public function show($id)
    {
        $data = Quotation::with([
                'items.product', 'items.taxes', 'client', 'currency',
                'advancePayments.currency'
            ])
            ->findOrFail($id);
        return api([
            'data' => $data
        ]);
    }

    public function pdf($id)
    {
        $data = Quotation::with([
                'items.product', 'items.taxes', 'client', 'currency',
                'advancePayments.currency'
            ])
            ->findOrFail($id);
        return pdf('docs.quotation', $data);
    }

    public function edit($id, Request $request)
    {
        $form = Quotation::with(['items.product', 'items.taxes', 'client', 'currency'])->findOrFail($id);

        if($request->has('mode')) {
            switch ($request->mode) {
                case 'clone':

                    $form->number = counter()->next('quotation');
                    $form->date = date('Y-m-d');
                    $form->reference = null;

                    break;

                case 'sales_order':

                    $form->number = counter()->next('sales_order');
                    $form->date = date('Y-m-d');
                    $form->reference = null;
                    $form->quotation_id = $form->id;
                    break;

                case 'invoice':

                    $form->number = counter()->next('invoice');
                    $form->date = date('Y-m-d');
                    $form->reference = null;
                    $form->parent_id = $form->id;
                    $form->parent_type = 'quotation';
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
        $model = Quotation::findOrFail($id);

        // abort if not editable
        abort_if(!$model->is_editable, 404);

        $request->validate([
            'client_id' => 'required|integer|exists:clients,id',
            'currency_id' => 'required|integer|exists:currencies,id',
            'reference' => 'nullable|max:255',
            'date' => 'required|date_format:Y-m-d',
            'items' => 'required|array|min:1',
            'items.*.id' => 'sometimes|integer|exists:quotation_items,id,quotation_id,'.$model->id,
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
        $model = Quotation::findOrFail($id);

        $request->validate([
            'status' => 'required|integer|in:2,3,4'
        ]);

        switch ($request->status) {
            case (Quotation::SENT) :
                // must be draft
                abort_if(!in_array($model->status_id, [
                    Quotation::DRAFT
                ]), 404);

                $model->status_id = Quotation::SENT;
                break;

            case (Quotation::ACCEPTED) :
                // must be draft
                abort_if(!in_array($model->status_id, [
                    Quotation::DRAFT, Quotation::SENT,
                    Quotation::DECLINED
                ]), 404);

                $model->status_id = Quotation::ACCEPTED;
                break;

            case (Quotation::DECLINED) :
                // must be draft
                abort_if(!in_array($model->status_id, [
                    Quotation::DRAFT, Quotation::SENT,
                    Quotation::ACCEPTED
                ]), 404);

                $model->status_id = Quotation::DECLINED;
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
        $model = Quotation::findOrFail($id);

        // check whether this particular quotation belongs to
        $invoices = $model->invoices()->count();
        $salesOrders = $model->salesOrders()->count();
        $advancePayments = $model->advancePayments()->count();
        // invoice, etc.
        // if yes provide warning

        if($invoices || $salesOrders || $advancePayments || !$model->is_editable) {
            return api([
                'message' => 'Delete all the quotation relations first',
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
