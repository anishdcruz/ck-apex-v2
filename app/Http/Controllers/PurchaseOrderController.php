<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PurchaseOrder\PurchaseOrder;
use App\Vendor;
use DB;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        return api([
            'data' => PurchaseOrder::with(['vendor', 'currency'])->search()
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'vendor_id' => 'sometimes|required|integer|exists:vendors,id'
        ]);

        $form = [
            'vendor_id' => null,
            'vendor' => null,
            'number' => counter()->next('purchase_order'),
            'reference' => null,
            'date' => date('Y-m-d'),
            'terms' => null,
            'items' => [
                [
                    'product' => null,
                    'product_id' => null,
                    'vendor_reference' => null,
                    'unit_price' => 0,
                    'qty' => 1,
                ]
            ]
        ];

        if($request->has('vendor_id')) {
            $vendor = Vendor::with(['currency'])->findOrFail($request->vendor_id);

            array_set($form, 'vendor_id', $vendor->id);
            array_set($form, 'vendor', $vendor);
            array_set($form, 'currency_id', $vendor->currency->id);
            array_set($form, 'currency', $vendor->currency);

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
            'vendor_id' => 'required|integer|exists:vendors,id',
            'currency_id' => 'required|integer|exists:currencies,id',
            'reference' => 'nullable|max:255',
            'date' => 'required|date_format:Y-m-d',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.vendor_reference' => 'required|alpha_dash|max:255',
            'items.*.qty' => 'required|numeric|min:0',
            'terms' => 'nullable|max:2000'
        ]);

        $model = new PurchaseOrder();
        $model->fill($request->except('items'));

        $model->user_id = auth()->id();
        $model->status_id = PurchaseOrder::DRAFT;

        $model->total = collect($request->items)->sum(function($item) {
            return $item['qty'] * $item['unit_price'];
        });

        $model = DB::transaction(function() use ($model, $request) {

            $model->number = counter()->next('purchase_order');
            $model->storeHasMany([
                'items' => $request->items
            ]);

            counter()->increment('purchase_order');

            return $model;
        });

        return api([
            'saved' => true,
            'id' => $model->id
        ]);
    }

    public function show($id)
    {
        return api([
            'data' => PurchaseOrder::with(['items.product', 'vendor', 'currency'])->findOrFail($id)
        ]);
    }

    public function pdf($id, Request $request)
    {
        $data = PurchaseOrder::with(['items.product', 'vendor', 'currency'])->findOrFail($id);

        $doc  = 'docs.purchase_order';

        if($request->has('mode') && $request->mode == 'receive') {
            $doc = 'docs.receive';
            $items = $data->items->map(function($query) {
                $query->qty = $query->qty - $query->qty_received;
                if($query->qty > 0) {
                    $query->qty_received = 0;
                    return $query;
                }
            })->reject(function($item) {
                return is_null($item);
            });
            unset($data->items);
            $data->items = $items;
        }

        return pdf($doc, $data);
    }

    public function edit($id, Request $request)
    {
        $form = PurchaseOrder::with(['items.product', 'vendor', 'currency'])->findOrFail($id);

        if($request->has('mode')) {
            switch ($request->mode) {
                case 'clone':

                    $form->number = counter()->next('purchase_order');
                    $form->date = date('Y-m-d');
                    $form->reference = null;
                    break;

                case 'bill':
                    $form->purchase_order_id = $form->id;
                    $form->number = counter()->next('bill');
                    $form->date = null;
                    $form->due_date = null;
                    $form->reference = null;
                    $form->note = null;
                    $form->terms = null;
                    break;
                case 'receive_order':
                    // i
                    $form->purchase_order_id = $form->id;
                    $form->purchase_order_number = $form->number;
                    $form->number = counter()->next('receive_order');
                    $form->date = null;
                    $form->note = null;
                    $items = $form->items->map(function($query) {
                        $query->qty = $query->qty - $query->qty_received;
                        if($query->qty > 0) {
                            $query->qty_received = 0;
                            return $query;
                        }
                    })->reject(function($item) {
                        return is_null($item);
                    });
                    unset($form->items);
                    $form->items = $items;
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
        $model = PurchaseOrder::findOrFail($id);

        // abort if not editable
        abort_if(!$model->is_editable, 404);

        $request->validate([
            'vendor_id' => 'required|integer|exists:vendors,id',
            'currency_id' => 'required|integer|exists:currencies,id',
            'reference' => 'nullable|max:255',
            'date' => 'required|date_format:Y-m-d',
            'items' => 'required|array|min:1',
            'items.*.id' => 'sometimes|integer|exists:purchase_order_items,id,purchase_order_id,'.$model->id,
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.vendor_reference' => 'required|alpha_dash|max:255',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.qty' => 'required|numeric|min:0',
            'terms' => 'nullable|max:2000'
        ]);

        $model->fill($request->except('items'));

        $model->total = collect($request->items)->sum(function($item) {
            return $item['qty'] * $item['unit_price'];
        });

        $model = DB::transaction(function() use ($model, $request) {

            $model->updateHasMany([
                'items' => $request->items
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
        $model = PurchaseOrder::findOrFail($id);

        $request->validate([
            'status' => 'required|integer|in:2,3,5,6'
        ]);

        switch ($request->status) {
            case (PurchaseOrder::SENT) :
                // must be draft
                abort_if(!in_array($model->status_id, [
                    PurchaseOrder::DRAFT
                ]), 404);

                $model->status_id = PurchaseOrder::SENT;
                break;

            case (PurchaseOrder::CONFIRMED) :
                // must be draft
                abort_if(!in_array($model->status_id, [
                    PurchaseOrder::SENT,
                    PurchaseOrder::CANCELLED
                ]), 404);

                $model->status_id = PurchaseOrder::CONFIRMED;
                break;

            case (PurchaseOrder::CANCELLED) :
                // must be draft
                abort_if(!in_array($model->status_id, [
                    PurchaseOrder::DRAFT, PurchaseOrder::SENT,
                    PurchaseOrder::CONFIRMED
                ]), 404);

                $model->status_id = PurchaseOrder::CANCELLED;
                break;
            case (PurchaseOrder::CLOSED) :
                // must be confirmed
                abort_if(!in_array($model->status_id, [
                    PurchaseOrder::CONFIRMED
                ]), 404);

                $model->status_id = PurchaseOrder::CLOSED;
                break;

            default:
                abort(404, 'Invalid Operation');
                break;
        }

        $model->save();

        return api([
            'saved' => true,
            'id' => $model->id,
            'is_editable' => $model->is_editable,
            'status_id' => $model->status_id
        ]);
    }

    public function destroy($id)
    {
        $model = PurchaseOrder::findOrFail($id);

        // check whether this particular purchase order belongs to
        $bills = $model->bills()->count();

        // if yes provide warning

        if($bills || !$model->is_editable) {
            return api([
                'message' => 'Delete all the purchase order relations first',
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
