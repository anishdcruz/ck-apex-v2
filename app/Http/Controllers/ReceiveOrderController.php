<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PurchaseOrder\PurchaseOrder;
use App\ReceiveOrder\ReceiveOrder;
use Exception;
use DB;

class ReceiveOrderController extends Controller
{
    public function index()
    {
        return api([
            'data' => ReceiveOrder::with(['vendor'])->search()
        ]);
    }

    public function store(Request $request)
    {
        $po = PurchaseOrder::whereIn('status_id', [
                PurchaseOrder::CONFIRMED,
                PurchaseOrder::PARTIALLY_RECEIVED
            ])
            ->findOrFail($request->purchase_order_id);

        $this->validate($request, [
            'date' => 'required|date_format:Y-m-d',
            'document' => 'nullable|image|max:2048',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer|exists:purchase_order_items,id,purchase_order_id,'.$po->id,
            'items.*.qty_received' => 'required|numeric|min:0|purchase_order_item:items.*.id'
        ]);

        $model = new ReceiveOrder();
        $model->fill($request->except('items'));
        $model->purchase_order_id = $po->id;
        $model->vendor_id = $po->vendor_id;

         // upload document if exists
        if($request->hasFile('document') && $request->file('document')->isValid()) {
            // store in public uploads folder by default
           if($fileName = uploadFile($request->document)) {
                $model->document = $fileName;
           }
        }

        $model->user_id = auth()->id();
        $model->status_id = ReceiveOrder::RECEIVED;

        $items = collect($request->items)->map(function($item) {
            if($item['qty_received'] > 0) {
                $item['purchase_order_item_id'] = $item['id'];
                $item['qty'] = $item['qty_received'];
                return $item;
            }
        })->reject(function($item) {
            return is_null($item);
        });

        $model = DB::transaction(function() use ($model, $items, $po) {

            $model->number = counter()->next('receive_order');

            $model->storeHasMany([
                'items' => $items
            ]);

            //  1. update po and items

            $model->items->each(function($item) {
                $poItem = $item->purchaseOrderItem;
                $amount = $poItem->qty_received + $item->qty;
                $poItem->qty_received = $amount;
                $poItem->save();

                $product = $item->product;
                $product->qty_on_hand = $product->qty_on_hand + $item->qty;
                $product->save();
            });

            // po status
            $status = PurchaseOrder::RECEIVED;
            foreach($po->items as $item) {
                if($item->qty_received < $item->qty) {
                    $status = PurchaseOrder::PARTIALLY_RECEIVED;
                }
            }

            $po->status_id = $status;
            $po->save();

            counter()->increment('receive_order');

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
            'data' => ReceiveOrder::with(['items.product', 'vendor', 'purchaseOrder'])->findOrFail($id)
        ]);
    }

    public function pdf($id, Request $request)
    {
        $data = ReceiveOrder::with(['items.product', 'vendor', 'purchaseOrder'])->findOrFail($id);

        $doc  = 'docs.receive_order';

        return pdf($doc, $data);
    }
}
