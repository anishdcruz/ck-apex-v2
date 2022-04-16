<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Vendor;
use DB;

class VendorController extends Controller
{
    public function index()
    {
        return api([
            'data' => Vendor::search()
        ]);
    }

    public function search()
    {
        $results = Vendor::with('currency')
            ->orderBy('company')
            ->when(request('q'), function($query) {
                $query->where('company', 'like', '%'.request('q').'%')
                    ->orWhere('person', 'like', '%'.request('q').'%')
                    ->orWhere('email', 'like', '%'.request('q').'%');
            })
            ->limit(6)
            ->get(['id', 'person', 'company', 'currency_id'])
            ->when(request('with') == 'bills', function($vendors) {
                return $vendors->map(function($vendor) {
                    $vendor->bills = $vendor->bills()->whereIn('status_id', [1, 2])
                        ->get([
                            'amount_paid', 'total', 'date', 'status_id', 'due_date',
                            'number', 'id as bill_id',
                            DB::raw('0 as amount_applied')
                        ]);
                    return $vendor;
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
            'shipping_address' => '',
            'payment_details' => ''
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
            'payment_details' => 'nullable|max:3000',
            'currency_id' => 'required|integer|exists:currencies,id'
        ]);

        $model = new Vendor;
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
        $vendor = Vendor::with(['currency'])->findOrFail($id);

        $stats = [
            'total_expense' => $vendor->total_expense,
            'account_payable' => $vendor->bills()->whereIn('status_id', [1, 2])->sum(DB::raw('total - amount_paid')),
            'open_purchase_orders' => $vendor->purchaseOrders()->whereIn('status_id', [3])->count(),
            'unpaid_bills' => $vendor->bills()->whereIn('status_id', [1, 2])->count()
        ];
        return api([
            'data' => $vendor,
            'stats' => $stats
        ]);
    }

    public function showBills($id)
    {
        $vendor = Vendor::findOrFail($id);

        $model = $vendor->bills()
            ->with(['currency'])
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return api([
            'model' => $model
        ]);
    }

    public function showPurchaseOrders($id)
    {
        $vendor = Vendor::findOrFail($id);

        $model = $vendor->purchaseOrders()
            ->with(['currency'])
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return api([
            'model' => $model
        ]);
    }

    public function showRecevieOrders($id)
    {
        $vendor = Vendor::findOrFail($id);

        $model = $vendor->receiveOrders()
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return api([
            'model' => $model
        ]);
    }

    public function showExpenses($id)
    {
        $vendor = Vendor::findOrFail($id);

        $model = $vendor->expenses()
            ->with(['currency'])
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return api([
            'model' => $model
        ]);
    }

    public function showProducts($id)
    {
        $vendor = Vendor::findOrFail($id);

        $model = $vendor->products()
            ->with(['product', 'currency'])
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return api([
            'model' => $model
        ]);
    }

    public function showPayments($id)
    {
        $vendor = Vendor::findOrFail($id);

        $model = $vendor->payments()
            ->with(['currency'])
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return api([
            'model' => $model
        ]);
    }

    public function edit($id)
    {
        return api([
            'form' => Vendor::with(['currency'])->findOrFail($id)
        ]);
    }

    public function update(Request $request, $id)
    {
        $model = Vendor::findOrFail($id);

        $request->validate([
            'person' => 'required|max:255',
            'company' => 'required|max:255',
            'email' => 'required|email|unique:vendors,email,'.$model->id.',id',
            'mobile_number' => 'sometimes|max:255',
            'work_phone' => 'sometimes|max:255',
            'billing_address' => 'required|max:3000',
            'shipping_address' => 'required|max:3000',
            'payment_details' => 'nullable|max:3000',
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
        $model = Vendor::findOrFail($id);

        // check whether this particular vendor belongs to
        $bills = $model->bills()->count();
        $purchaseOrders = $model->purchaseOrders()->count();
        $expenses = $model->expenses()->count();
        $vendorPayments = $model->payments()->count();
        $products = $model->products()->count();

        // invoice, etc.
        // if yes provide warning

        if($products || $purchaseOrders || $expenses || $bills || $vendorPayments) {
            return api([
                'message' => 'Delete all the vendor relations first',
                'errors' => []
            ], 422);
        }

        $model->delete();

        return api([
            'deleted' => true
        ]);
    }
}
