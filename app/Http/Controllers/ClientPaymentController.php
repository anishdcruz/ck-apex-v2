<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ClientPayment\ClientPayment;
use App\Rules\InvoiceBalance;
use App\Invoice\Invoice;
use App\Client;
use Exception;
use DB;

class ClientPaymentController extends Controller
{
    public function index()
    {
        return api([
            'data' => ClientPayment::with(['client', 'currency'])->search()
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
            'number' => counter()->next('client_payment'),
            'payment_reference' => null,
            'payment_date' => date('Y-m-d'),
            'payment_mode' => 'cheque',
            'amount_received' => 0,
            'items' => []
        ];

        if($request->has('client_id')) {
            $client = Client::with(['currency'])->findOrFail($request->client_id);

            array_set($form, 'client_id', $client->id);
            array_set($form, 'client', $client);
            array_set($form, 'currency_id', $client->currency->id);
            array_set($form, 'currency', $client->currency);

            // get all sent and partialy paid invoices
            $invoices = $client->invoices()->whereIn('status_id', [2, 3])
                ->get([
                    'amount_paid', 'total', 'date', 'status_id', 'due_date',
                    'number', 'id as invoice_id',
                    DB::raw('0 as amount_applied')
                ]);

            if($invoices->count()) {
                array_set($form, 'items', $invoices->toArray());
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
            'client_id' => 'required|integer|exists:clients,id',
            'currency_id' => 'required|integer|exists:currencies,id',
            'payment_mode' => 'required|in:cheque,cash,bank_transfer',
            'payment_reference' => 'required_if:payment_mode,cheque',
            'document' => 'required_if:payment_mode,cheque|image|max:2048',
            'payment_date' => 'required|date_format:Y-m-d',
            'amount_received' => 'required|numeric|min:1',
            'items' => 'required|array|min:1',
            'items.*.invoice_id' => 'required|integer',
            'items.*.amount_applied' => ['required', 'numeric', 'min:0', 'invoice_balance:items.*.invoice_id']
        ]);

        $model = new ClientPayment();
        $model->fill($request->except('items'));

         // upload document if exists
        if($request->hasFile('document') && $request->file('document')->isValid()) {
            // store in public uploads folder by default
           if($fileName = uploadFile($request->document)) {
                $model->document = $fileName;
           }
        }

        $model->user_id = auth()->id();
        $model->status_id = ClientPayment::RECEIVED;


        // get only applied greater than zero
        $items = collect($request->items)->map(function($item) {
            if($item['amount_applied'] > 0) {
                return $item;
            }
        })->reject(function($item) {
            return is_null($item);
        });

        // throw error if amount_applied is invalid

        if($items->sum('amount_applied') != $request->amount_received) {
            return api([
                'errors' => [
                    'amount_received' => ['Amount recived does not match amount applied']
                ]
            ], 422);
        }

        $model->amount_received = $items->sum('amount_applied');

        $model = DB::transaction(function() use ($model, $items) {

            $model->number = counter()->next('client_payment');

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

            //  2. update client revenue
            $client = $model->client;
            $client->total_revenue = $client->total_revenue + $model->amount_received;
            $client->save();

            counter()->increment('client_payment');

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
            'data' => ClientPayment::with(['items.invoice', 'client', 'currency'])->findOrFail($id)
        ]);
    }

    public function pdf($id)
    {
        $data = ClientPayment::with(['items.invoice', 'client', 'currency'])->findOrFail($id);
        return pdf('docs.client_payment', $data);
    }
}
