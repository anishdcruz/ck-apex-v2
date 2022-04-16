<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product\Product;
use DB;

class ProductController extends Controller
{
    public function index()
    {
        return api([
            'data' => Product::with(['currency'])->search()
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'vendor_id' => 'sometimes|required|integer|exists:vendors,id'
        ]);

        if(request()->has('vendor_id')) {
            $results = DB::table('products')
                ->select(
                    'products.id', 'products.unit_price',
                    DB::raw('concat(products.item_code, " - ", products.description) as text'),
                    'product_items.price as vendor_price', 'product_items.vendor_id',
                    'product_items.reference'
                )
                ->join('product_items', 'products.id', '=', 'product_items.product_id')
                ->join('vendors', 'vendors.id', '=', 'product_items.vendor_id')
                ->where('product_items.vendor_id', '=', request('vendor_id'))
                ->where(function($query) {
                    $query->where('products.item_code', 'like', '%'.request('q').'%')
                        ->orWhere('products.description', 'like', '%'.request('q').'%')
                        ->orWhere('product_items.reference', 'like', '%'.request('q').'%');
                })
                ->limit(6)
                ->get();
        } else {
            $results = Product::with('taxes')
                ->orderBy('item_code')
                ->when(request('q'), function($query) {
                    $query->where('item_code', 'like', '%'.request('q').'%')
                        ->orWhere('description', 'like', '%'.request('q').'%');
                })
                ->limit(6)
                ->get(['id', 'item_code', 'description', 'unit_price']);
        }

        return api([
            'results' => $results
        ]);
    }



    public function create()
    {
        $form = array_merge([
            'item_code' => counter()->next('product'),
            'unit_price' => 0,
            'description' => '',
            'has_inventory' => 0,
            'items' => [],
            'taxes' => []
        ],
            currency()->defaultToArray()
        );

        return api([
            'form' => $form
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|max:2000',
            'unit_price' => 'required|numeric|min:0',
            'currency_id' => 'required|integer|exists:currencies,id',
            'has_inventory' => 'required|boolean',
            'items' => 'sometimes|array',
            'items.*.reference' => 'required|max:255',
            'items.*.vendor_id' => 'required|integer|exists:vendors,id',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.currency_id' => 'required|integer|exists:currencies,id',
            'taxes.*.name' => 'required|max:255',
            'taxes.*.rate' => 'required|numeric|min:0',
            'taxes.*.tax_authority' => 'required|max:255'
        ]);

        $model = new Product;
        $model->fill($request->except('items', 'taxes'));

        $model->user_id = auth()->id();
        $model->has_inventory = $request->has_inventory;

        $result = DB::transaction(function() use ($model, $request) {
            $model->item_code = counter()->next('product');

            $model->storeHasMany([
                'items' => $request->items,
                'taxes' => $request->taxes
            ]);

            counter()->increment('product');

            return $model;
        });

        return api([
            'saved' => true,
            'id' => $result->id
        ]);
    }

    public function show($id)
    {
        $data = Product::with([
            'currency', 'items.currency', 'items.vendor', 'taxes'
        ])->findOrFail($id);

        return api([
            'data' => $data
        ]);
    }

    public function edit($id)
    {
        $form = Product::with(['currency', 'items.currency', 'items.vendor', 'taxes'])
            ->findOrFail($id);

        return api([
            'form' => $form
        ]);
    }

    public function update($id, Request $request)
    {
        $model = Product::findOrFail($id);

        $request->validate([
            'description' => 'required|max:2000',
            'unit_price' => 'required|numeric|min:0',
            'currency_id' => 'required|integer|exists:currencies,id',
            'has_inventory' => 'required|boolean',
            'items' => 'sometimes|array',
            'items.*.id' => 'sometimes|required|integer|exists:product_items,id,product_id,'.$model->id,
            'items.*.reference' => 'required|max:255',
            'items.*.vendor_id' => 'required|integer|exists:vendors,id',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.currency_id' => 'required|integer|exists:currencies,id',
            'taxes.*.name' => 'required|max:255',
            'taxes.*.rate' => 'required|numeric|min:0',
            'taxes.*.tax_authority' => 'required|max:255'
        ]);

        $qty = $model->qty_on_hand;
        $model->fill($request->except('items', 'taxes'));

        // only update if no qty in hand
        if($qty == 0) {
            if($request->has('has_inventory')) {
                $model->has_inventory = $request->has_inventory;
            }
        }

        $result = DB::transaction(function() use ($model, $request) {

            $model->updateHasMany([
                'items' => $request->items,
                'taxes' => $request->taxes
            ]);

            return $model;
        });

        return api([
            'saved' => true,
            'id' => $result->id
        ]);
    }

    public function destroy($id)
    {
        $model = Product::findOrFail($id);

        // check whether this particular product belongs to
        $items = $model->items()->count();

        // quotation
        $quotations = DB::table('quotation_items')
            ->where('product_id', $model->id)->count();

        // sales order
        $salesOrders = DB::table('sales_order_items')
            ->where('product_id', $model->id)->count();
        // invoice

        $invoices = DB::table('invoice_items')
            ->where('product_id', $model->id)->count();
        // purchase order

        $purchaseOrders = DB::table('purchase_order_items')
            ->where('product_id', $model->id)->count();
        // bills

        $bills = DB::table('bill_items')
            ->where('product_id', $model->id)->count();
        // if yes provide warning

        if($items || $quotations || $salesOrders || $invoices || $purchaseOrders || $bills) {
            return api([
                'message' => 'Delete all the product relations first',
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
