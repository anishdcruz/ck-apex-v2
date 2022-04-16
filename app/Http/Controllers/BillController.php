<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bill\Bill;
use App\PurchaseOrder\PurchaseOrder;
use App\Vendor;
use DB;

class BillController extends Controller
{
    public function index()
    {
        return api([
            'data' => Bill::with(['vendor', 'currency'])->search()
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
            'number' => counter()->next('bill'),
            'reference' => null,
            'date' => null,
            'due_date' => null,
            'terms' => null,
            'note' => null,
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
            'purchase_order_id' => 'sometimes|required|exists:purchase_orders,id',
            'reference' => 'nullable|max:255',
            'date' => 'required|date_format:Y-m-d',
            'due_date' => 'required|date_format:Y-m-d',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.vendor_reference' => 'required|alpha_dash|max:255',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.qty' => 'required|numeric|min:0',
            'terms' => 'nullable|max:2000',
            'note' => 'nullable|max:2000',
            'document' => 'sometimes|required|image|max:2048'
        ]);

        $model = new Bill();
        $model->fill($request->except('items'));

        $model->user_id = auth()->id();
        $model->status_id = Bill::DRAFT;
        $model->amount_paid = 0;

         // upload document if exists
        if($request->hasFile('document') && $request->file('document')->isValid()) {
            // store in public uploads folder by default
           if($fileName = uploadFile($request->document)) {
                $model->document = $fileName;
           }
        }

        $model->purchase_order_id = $request->get('purchase_order_id', null);

        $model->total = collect($request->items)->sum(function($item) {
            return $item['qty'] * $item['unit_price'];
        });

        $model = DB::transaction(function() use ($model, $request) {

            $model->number = counter()->next('bill');

            $model->storeHasMany([
                'items' => $request->items
            ]);

           //  update parent quotation to sales ordered
            if($model->purchaseOrder) {
               $purchaseOrder = $model->purchaseOrder;
               if(in_array($purchaseOrder->status_id, [PurchaseOrder::SENT, PurchaseOrder::CONFIRMED, PurchaseOrder::RECEIVED])) {
                   $purchaseOrder->status_id = PurchaseOrder::BILLED;
                   $purchaseOrder->save();
               }
           }

            counter()->increment('bill');

            return $model;
        });

        return api([
            'saved' => true,
            'id' => $model->id
        ]);
    }

    public function show($id)
    {
        $data = Bill::with([
            'items.product', 'vendor', 'currency', 'purchaseOrder',
            'vendorPayments.parent.currency'
        ])->findOrFail($id);
        return api([
            'data' => $data
        ]);
    }

    public function pdf($id)
    {
        $data = Bill::with(['items.product', 'vendor', 'currency', 'purchaseOrder'])
            ->findOrFail($id);
        return pdf('docs.bill', $data);
    }

    public function edit($id, Request $request)
    {
        $form = Bill::with(['items.product', 'vendor', 'currency'])->findOrFail($id);

        if($request->has('mode')) {
            switch ($request->mode) {
                case 'clone':

                    $form->number = counter()->next('bill');
                    $form->date = null;
                    $form->due_date = null;
                    $form->reference = null;
                    unset($form->purchase_order_id);

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
        $model = Bill::findOrFail($id);

        // abort if not editable
        abort_if(!$model->is_editable, 404);

        $request->validate([
            'vendor_id' => 'required|integer|exists:vendors,id',
            'currency_id' => 'required|integer|exists:currencies,id',
            'reference' => 'nullable|max:255',
            'date' => 'required|date_format:Y-m-d',
            'due_date' => 'required|date_format:Y-m-d',
            'items' => 'required|array|min:1',
            'items.*.id' => 'sometimes|integer|exists:bill_items,id,bill_id,'.$model->id,
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.vendor_reference' => 'required|alpha_dash|max:255',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.qty' => 'required|numeric|min:0',
            'terms' => 'nullable|max:2000',
            'note' => 'nullable|max:2000',
            'document' => 'sometimes|required|image|max:2048'
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

    public function destroy($id)
    {
        $model = Bill::findOrFail($id);

        // check whether this particular bill belongs to
        $vendorPayments = $model->vendorPayments()->count();

        // if yes provide warning

        if($vendorPayments || !$model->is_editable) {
            return api([
                'message' => 'Delete all the bill relations first',
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
