<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Expense;
use App\Vendor;
use DB;

class ExpenseController extends Controller
{
    public function index()
    {
        return api([
            'data' => Expense::with(['vendor', 'currency'])->search()
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
            'number' => counter()->next('expense'),
            'payemnt_date' => date('Y-m-d'),
            'amount_paid' => 0,
            'description' => null
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
            'payment_date' => 'required|date_format:Y-m-d',
            'amount_paid' => 'required|numeric|min:1',
            'description' => 'required|max:2000',
            'document' => 'required|image|max:2048'
        ]);

        $model = new Expense();
        $model->fill($request->except('document'));

        $model->user_id = auth()->id();

         // upload document if exists
        if($request->hasFile('document') && $request->file('document')->isValid()) {
            // store in public uploads folder by default
           if($fileName = uploadFile($request->document)) {
                $model->document = $fileName;
           }
        }

        $model = DB::transaction(function() use ($model, $request) {

            $model->number = counter()->next('expense');

            $model->save();

            // update vendor total_expense
            $vendor = $model->vendor;
            $vendor->total_expense = $vendor->total_expense + $model->amount_paid;
            $vendor->save();

            counter()->increment('expense');

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
            'data' => Expense::with(['vendor', 'currency'])->findOrFail($id)
        ]);
    }

    public function pdf($id)
    {
        $data = Expense::with(['vendor', 'currency'])->findOrFail($id);
        return pdf('docs.expense', $data);
    }
}
