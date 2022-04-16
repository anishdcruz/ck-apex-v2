<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Quotation\Quotation;
use App\Rules\InvoiceBalance;
use App\Invoice\Invoice;
use App\AdvancePayment\AdvancePayment;
use Exception;
use App\Client;
use DB;

class AdvancePaymentController extends Controller
{
    public function index()
    {
        return api([
            'data' => AdvancePayment::with(['client', 'currency'])->search()
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'client_id' => 'sometimes|required|integer|exists:clients,id',
            'quotation_id' => 'sometimes|required|integer|exists:quotations,id'
        ]);

        $form = [
            'client_id' => null,
            'client' => null,
            'number' => counter()->next('advance_payment'),
            'payment_mode' => 'cheque',
            'payment_reference' => null,
            'payment_date' => date('Y-m-d'),
            'amount_received' => 0,
            'description' => null
        ];

        if($request->has('quotation_id')) {
            $quotation = Quotation::with(['client', 'currency'])
                ->findOrFail($request->quotation_id);

            array_set($form, 'client_id', $quotation->client->id);
            array_set($form, 'client', $quotation->client);
            array_set($form, 'currency_id', $quotation->currency->id);
            array_set($form, 'currency', $quotation->currency);
            array_set($form, 'quotation_id', $quotation->id);
            array_set($form, 'description', 'Advance Payment for Quotation '.$quotation->number);

        } else if($request->has('client_id')) {
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
            'payment_mode' => 'required|in:cheque,cash,bank_transfer',
            'payment_reference' => 'required_if:payment_mode,cheque',
            'document' => 'required_if:payment_mode,cheque|image|max:2048',
            'payment_date' => 'required|date_format:Y-m-d',
            'amount_received' => 'required|numeric|min:1',
            'description' => 'required|max:2000',
        ]);

        $model = new AdvancePayment();
        $model->fill($request->except('document'));

        $model->user_id = auth()->id();
        $model->status_id = AdvancePayment::RECEIVED;

         // upload document if exists
        if($request->hasFile('document') && $request->file('document')->isValid()) {
            // store in public uploads folder by default
           if($fileName = uploadFile($request->document)) {
                $model->document = $fileName;
           }
        }

        $model->quotation_id = $request->get('quotation_id', null);

        $model = DB::transaction(function() use ($model, $request) {

            $model->number = counter()->next('advance_payment');
            $model->save();

            // update client unused credit amount
            $client = $model->client;
            $client->unused_credit = $client->unused_credit + $model->amount_received;
            $client->save();

            counter()->increment('advance_payment');

            return $model;
        });

        return api([
            'saved' => true,
            'id' => $model->id
        ]);
    }

    public function show($id)
    {
        $data = AdvancePayment::with(['client', 'currency', 'quotation', 'items.invoice'])
            ->findOrFail($id);
        return api([
            'data' => $data
        ]);
    }

    public function showInvoices($id)
    {
        $data = AdvancePayment::with(['client', 'currency'])
            ->whereStatusId(AdvancePayment::RECEIVED)
            ->findOrFail($id);
        $data->items = $data->client->invoices()->whereIn('status_id', [2, 3])
                ->get([
                    'amount_paid', 'total', 'date', 'status_id', 'due_date',
                    'number', 'id as invoice_id',
                    DB::raw('0 as amount_applied')
                ]);
        return api([
            'data' => $data
        ]);
    }

    public function applyInvoices($id, Request $request)
    {
        $model = AdvancePayment::whereStatusId(AdvancePayment::RECEIVED)
            ->findOrFail($id);

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.invoice_id' => 'required|integer',
            'items.*.amount_applied' => ['required', 'numeric', 'min:0',  'invoice_balance:items.*.invoice_id']
        ]);

        $items = collect($request->items)->map(function($item) {
            if($item['amount_applied'] > 0) {
                return $item;
            }
        })->reject(function($item) {
            return is_null($item);
        });

        // throw error if amount_applied is invalid or less
        if($items->sum('amount_applied') != $model->amount_received) {
            return api([
                'errors' => [
                    'amount_received' => ['Amount recived does not match amount applied']
                ]
            ], 422);
        }

        $model->applied_amount = $items->sum('amount_applied');
        $model->applied_date = date('Y-m-d');

        $model = DB::transaction(function() use ($model, $items) {
            $model->status_id = AdvancePayment::DRAWN;
            $model->storeHasMany([
                'items' => $items
            ]);

            //  1. update invoices

            $model->items->each(function($item) {
                $invoice = $item->invoice;
                $amount = $invoice->amount_paid + $item->amount_applied;

                if($amount > $invoice->total) {
                    throw new Exception('Amount overflow');
                }

                $invoice->amount_paid = $amount;
                $invoice->status_id = Invoice::PARTIALLY_PAID;

                if($invoice->amount_paid == $invoice->total) {
                    $invoice->status_id = Invoice::PAID;
                }

                $invoice->save();
            });

            //  2. update client revenue and also reduce unused credit
            $client = $model->client;
            $total = $model->applied_amount;
            $client->total_revenue = $client->total_revenue + $total;
            $client->unused_credit = $client->unused_credit - $total;
            $client->save();

            return $model;
        });

        return api([
            'saved' => true,
            'id' => $model->id
        ]);
    }

    public function pdf($id)
    {
        $data = AdvancePayment::with(['client', 'currency', 'quotation'])
            ->findOrFail($id);
        return pdf('docs.advance_payment', $data);
    }

    public function destroy($id)
    {
        $model = AdvancePayment::findOrFail($id);

        // check whether this particular advance payment belongs to
        // deposit, invoice, etc.
        // if yes provide warning

        if(true) {
            return api([
                'warning' => 'Delete all the advance payment relations first'
            ], 422);
        }

        $model->delete();

        return api([
            'deleted' => true
        ]);
    }
}
