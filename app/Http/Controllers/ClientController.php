<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;
use App\Invoice\Invoice;
use DB;

class ClientController extends Controller
{
    public function index()
    {
        return api([
            'data' => Client::search()
        ]);
    }

    public function search()
    {
        $results = Client::with('currency')
            ->orderBy('company')
            ->when(request('q'), function($query) {
                $query->where('company', 'like', '%'.request('q').'%')
                    ->orWhere('person', 'like', '%'.request('q').'%')
                    ->orWhere('email', 'like', '%'.request('q').'%');
            })
            ->limit(6)
            ->get(['id', 'person', 'company', 'currency_id'])
            ->when(request('with') == 'invoices', function($clients) {
                return $clients->map(function($client) {
                    $client->invoices = $client->invoices()->whereIn('status_id', [2, 3])
                        ->get([
                            'amount_paid', 'total', 'date', 'status_id', 'due_date',
                            'number', 'id as invoice_id',
                            DB::raw('0 as amount_applied')
                        ]);
                    return $client;
                });
            });

        return api([
            'results' => $results
        ]);
    }

    public function create()
    {
        $form = array_merge([
            'person' => '',
            'company' => '',
            'email' => '',
            'work_phone' => '',
            'mobile_number' => '',
            'billing_address' => '',
            'shipping_address' => ''
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
            'person' => 'required|max:255',
            'company' => 'required|max:255',
            'email' => 'required|email',
            'mobile_number' => 'sometimes|max:255',
            'work_phone' => 'sometimes|max:255',
            'billing_address' => 'required|max:3000',
            'shipping_address' => 'required|max:3000',
            'currency_id' => 'required|integer|exists:currencies,id'
        ]);

        $model = new Client;
        $model->fill($request->all());
        $model->user_id = auth()->id();

        $model->save();

        return api([
            'saved' => true,
            'id' => $model->id
        ]);

    }

    public function show($id)
    {
        $client = Client::with(['currency'])->findOrFail($id);
        $stats = [
            'total_revenue' => $client->total_revenue,
            'account_receivable' => $client->invoices()->whereIn('status_id', [2, 3])->sum(DB::raw('total - amount_paid')),
            'unused_credit' => $client->unused_credit,
            'advance_payments' => $client->advancePayments()->whereIn('status_id', [1, 2])->count(),
            'open_sales_orders' => $client->salesOrders()->whereIn('status_id', [3])->count(),
            'unpaid_invoices' => $client->invoices()->whereIn('status_id', [2, 3])->count()
        ];
        return api([
            'data' => $client,
            'stats' => $stats
        ]);
    }

    public function edit($id)
    {
        return api([
            'form' => Client::with(['currency'])->findOrFail($id)
        ]);
    }

    public function update(Request $request, $id)
    {
        $model = Client::findOrFail($id);

        $request->validate([
            'person' => 'required|max:255',
            'company' => 'required|max:255',
            'email' => 'required|email|unique:clients,email,'.$model->id.',id',
            'mobile_number' => 'sometimes|max:255',
            'work_phone' => 'sometimes|max:255',
            'billing_address' => 'required|max:3000',
            'shipping_address' => 'required|max:3000',
            'currency_id' => 'required|integer|exists:currencies,id'
        ]);

        $model->fill($request->all());
        $model->save();

        return api([
            'saved' => true,
            'id' => $model->id
        ]);
    }

    public function destroy($id)
    {
        $model = Client::findOrFail($id);

        // check whether this particular client belongs to
        $invoices = $model->invoices()->count();
        $quotations = $model->quotations()->count();
        $salesOrders = $model->salesOrders()->count();
        $advancePayments = $model->advancePayments()->count();
        $clientPayments = $model->payments()->count();

        // invoice, etc.
        // if yes provide warning

        if($invoices || $salesOrders || $advancePayments || $quotations || $clientPayments) {
            return api([
                'message' => 'Delete all the client relations first',
                'errors' => []
            ], 422);
        }

        $model->delete();

        return api([
            'deleted' => true
        ]);
    }

    public function showInvoices($id)
    {
        $client = Client::findOrFail($id);

        $model = $client->invoices()
            ->with(['currency'])
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return api([
            'model' => $model
        ]);
    }

    public function showQuotations($id)
    {
        $client = Client::findOrFail($id);

        $model = $client->quotations()
            ->with(['currency'])
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return api([
            'model' => $model
        ]);
    }

    public function showSalesOrders($id)
    {
        $client = Client::findOrFail($id);

        $model = $client->salesOrders()
            ->with(['currency'])
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return api([
            'model' => $model
        ]);
    }

    public function showAdvancePayments($id)
    {
        $client = Client::findOrFail($id);

        $model = $client->advancePayments()
            ->with(['currency'])
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return api([
            'model' => $model
        ]);
    }

    public function showPayments($id)
    {
        $client = Client::findOrFail($id);

        $model = $client->payments()
            ->with(['currency'])
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return api([
            'model' => $model
        ]);
    }
}
