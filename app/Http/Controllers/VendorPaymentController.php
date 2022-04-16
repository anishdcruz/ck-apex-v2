<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\VendorPayment\VendorPayment;
use App\Bill\Bill;
use App\Vendor;
use DB;

class VendorPaymentController extends Controller
{
    public function index()
    {
        return api([
            'data' => VendorPayment::with(['vendor', 'currency'])->search()
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
            'number' => counter()->next('vendor_payment'),
            'payment_reference' => null,
            'payment_date' => date('Y-m-d'),
            'payment_mode' => 'cheque',
            'amount_paid' => 0,
            'items' => []
        ];

        if($request->has('vendor_id')) {
            $vendor = Vendor::with(['currency'])->findOrFail($request->vendor_id);

            array_set($form, 'vendor_id', $vendor->id);
            array_set($form, 'vendor', $vendor);
            array_set($form, 'currency_id', $vendor->currency->id);
            array_set($form, 'currency', $vendor->currency);

            // get all draft and partialy paid bills
            $bills = $vendor->bills()->whereIn('status_id', [1, 2])
                ->get([
                    'amount_paid', 'total', 'date', 'status_id', 'due_date',
                    'number', 'id as bill_id',
                    DB::raw('0 as amount_applied')
                ]);

            if($bills->count()) {
                array_set($form, 'items', $bills->toArray());
            }
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
            'payment_mode' => 'required|in:cheque,cash,bank_transfer',
            'payment_reference' => 'required_unless:payment_mode,cash',
            'document' => 'required_if:payment_mode,cheque|image|max:2048',
            'payment_date' => 'required|date_format:Y-m-d',
            'amount_paid' => 'required|numeric|min:1',
            'items' => 'required|array|min:1',
            'items.*.bill_id' => 'required|integer',
            'items.*.amount_applied' => ['required', 'numeric', 'min:0', 'bill_balance:items.*.bill_id']
        ]);

        $model = new VendorPayment();
        $model->fill($request->except('items'));

         // upload document if exists
        if($request->hasFile('document') && $request->file('document')->isValid()) {
            // store in public uploads folder by default
           if($fileName = uploadFile($request->document)) {
                $model->document = $fileName;
           }
        }

        $model->user_id = auth()->id();
        $model->status_id = VendorPayment::PAID;

        $items = collect($request->items)->map(function($item) {
            if($item['amount_applied'] > 0) {
                return $item;
            }
        })->reject(function($item) {
            return is_null($item);
        });

        // throw error if amount_applied is invalid

        if($items->sum('amount_applied') != $request->amount_paid) {
            return api([
                'errors' => [
                    'amount_paid' => ['Amount paid does not match amount applied']
                ]
            ], 422);
        }

        $model->amount_paid = $items->sum('amount_applied');

        $model = DB::transaction(function() use ($model, $items) {

            $model->number = counter()->next('vendor_payment');

            $model->storeHasMany([
                'items' => $items
            ]);

            //  1. update bills

            $model->items->each(function($item) {
                $bill = $item->bill;
                $amount = $bill->amount_paid + $item->amount_applied;

                if($amount > $bill->total) {
                    throw new Exception('Amount overflow');
                }

                $bill->amount_paid = $amount;
                $bill->status_id = Bill::PARTIALLY_PAID;

                if($bill->amount_paid == $bill->total) {
                    $bill->status_id = Bill::PAID;
                }

                $bill->save();
            });

            //  2. update vendor total_expense
            $vendor = $model->vendor;
            $vendor->total_expense = $vendor->total_expense + $model->amount_paid;
            $vendor->save();

            counter()->increment('vendor_payment');

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
            'data' => VendorPayment::with(['items.bill', 'vendor', 'currency'])->findOrFail($id)
        ]);
    }

    public function pdf($id)
    {
        $data = VendorPayment::with(['items.bill', 'vendor', 'currency'])->findOrFail($id);
        return pdf('docs.vendor_payment', $data);
    }
}
