<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SalesOrder\SalesOrder;
use App\GoodsIssue\GoodsIssue;
use Exception;
use DB;

class GoodsIssueController extends Controller
{
    public function index()
    {
        return api([
            'data' => GoodsIssue::with(['client'])->search()
        ]);
    }

    public function store(Request $request)
    {
        $so = SalesOrder::whereIn('status_id', [
                SalesOrder::CONFIRMED,
                SalesOrder::PARTIALLY_ISSUED
            ])
            ->findOrFail($request->sales_order_id);

        $this->validate($request, [
            'date' => 'required|date_format:Y-m-d',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer|exists:sales_order_items,id,sales_order_id,'.$so->id,
            'items.*.qty_issued' => 'required|numeric|min:0|sales_order_item:items.*.id'
        ]);

        $model = new GoodsIssue();
        $model->fill($request->except('items'));
        $model->sales_order_id = $so->id;
        $model->client_id = $so->client_id;

        $model->user_id = auth()->id();
        $model->status_id = GoodsIssue::ISSUED;

        $items = collect($request->items)->map(function($item) {
            if($item['qty_issued'] > 0) {
                $item['sales_order_item_id'] = $item['id'];
                $item['qty'] = $item['qty_issued'];
                return $item;
            }
        })->reject(function($item) {
            return is_null($item);
        });

        $model = DB::transaction(function() use ($model, $items, $so) {

            $model->number = counter()->next('goods_issue');

            $model->storeHasMany([
                'items' => $items
            ]);

            //  1. update so and items

            $model->items->each(function($item) {
                $soItem = $item->salesOrderItem;
                $amount = $soItem->qty_issued + $item->qty;
                $soItem->qty_issued = $amount;
                $soItem->save();

                $product = $item->product;
                $product->qty_on_hand = $product->qty_on_hand - $item->qty;
                $product->save();
            });

            // so status
            $status = SalesOrder::ISSUED;
            foreach($so->items as $item) {
                if($item->qty_issued < $item->qty) {
                    $status = SalesOrder::PARTIALLY_ISSUED;
                }
            }

            $so->status_id = $status;
            $so->save();

            counter()->increment('goods_issue');

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
            'data' => GoodsIssue::with(['items.product', 'client', 'salesOrder'])->findOrFail($id)
        ]);
    }

    public function pdf($id, Request $request)
    {
        $data = GoodsIssue::with(['items.product', 'client', 'salesOrder'])->findOrFail($id);

        $doc  = 'docs.goods_issue';

        return pdf($doc, $data);
    }
}
