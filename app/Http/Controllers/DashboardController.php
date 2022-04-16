<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Invoice\Invoice;
use App\PurchaseOrder\PurchaseOrder;
use App\SalesOrder\SalesOrder;
use App\Bill\Bill;
use App\Vendor;
use App\Client;
use DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'accounts_receivable' => Invoice::whereIn('status_id', [2, 3])->sum(DB::raw('total - amount_paid')),
            'total_revenue' => Client::sum('total_revenue'),
            'open_sales_orders' => SalesOrder::whereIn('status_id', [3])->count(),
            'unpaid_invoices' => Invoice::whereIn('status_id', [2, 3])->count(),
            'accounts_payable' => Bill::whereIn('status_id', [1, 2])->sum(DB::raw('total - amount_paid')),
            'total_expense' => Vendor::sum('total_expense'),
            'open_purchase_orders' => PurchaseOrder::whereIn('status_id', [3])->count(),
            'unpaid_bills' => Bill::whereIn('status_id', [1, 2])->count(),
            'top_unpaid_invoices' => Invoice::with(['currency'])->whereIn('status_id', [2, 3])->limit(10)->orderBy('due_date')->get(['id', 'number', 'due_date', 'status_id', DB::raw('(total - amount_paid) as due_amount'), 'currency_id'])
        ];

        $data =  array_merge($data, currency()->defaultToArray());

        return api([
            'data' => $data,
            'income_expense_chart' => $this->getIncomeExpenseData()
        ]);
    }

    protected function getIncomeExpenseData()
    {
        // 28 days
        $start =  Carbon::now();
        $end = $start->copy()->subDays(28);
        $labels = $this->getDateRange($start, $end);
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'income',
                    'data' => $this->getIncomeData($start, $end, $labels),
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgb(54, 162, 235)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Expense',
                    'data' => $this->getExpenseData($start, $end, $labels),
                    'backgroundColor' => 'rgba(153, 102, 255, 0.2)',
                    'borderColor' => 'rgb(153, 102, 255)',
                    'borderWidth' => 1
                ]
            ]
        ];
    }

    protected function getDateRange($startDate, $endDate)
    {
        $dates = [];
        $start = $startDate->copy();
        $end = $endDate->copy();
        while ($end->lte($start)) {
            $dates[] = $end->copy()->format('M d');
            $end->addDay(3);
        }

        return $dates;
    }

    protected function getIncomeData($start, $end, $label)
    {
        $default = collect($label)->mapWithKeys(function($item) {
            return [$item => 0];
        });

        $cp = DB::table('client_payments')
            ->whereBetween('payment_date', [$end, $start])
            ->select('amount_received', 'payment_date')
            ->get();

        $ap = DB::table('advance_payments')
            ->whereBetween('payment_date', [$end, $start])
            ->select('amount_received', 'payment_date')
            ->get();

        $data = $ap->merge($cp)->groupBy('payment_date');

        $keyed = $default->mapWithKeys(function($key, $item) use ($data) {
            // get
            $date = Carbon::parse($item);

            $dates = [
                $date->copy()->format('Y-m-d'),
                $date->copy()->addDay(1)->format('Y-m-d'),
                $date->copy()->addDay(2)->format('Y-m-d')
            ];

            $sum = 0;
            foreach($dates as $key) {
                $items = $data->get($key);
                if(!is_null($items)) {
                    foreach($items as $item) {
                        $sum += $item->amount_received;
                    }
                }
            }

            return [$date->copy()->format('Y-m-d') => $sum];
        });

        return array_values($keyed->all());
    }

    protected function getExpenseData($start, $end, $label)
    {
        $default = collect($label)->mapWithKeys(function($item) {
            return [$item => 0];
        });

        $cp = DB::table('vendor_payments')
            ->whereBetween('payment_date', [$end, $start])
            ->select('amount_paid', 'payment_date')
            ->get();

        $ap = DB::table('expenses')
            ->whereBetween('payment_date', [$end, $start])
            ->select('amount_paid', 'payment_date')
            ->get();

        $data = $ap->merge($cp)->groupBy('payment_date');

        $keyed = $default->mapWithKeys(function($key, $item) use ($data) {
            // get
            $date = Carbon::parse($item);

            $dates = [
                $date->copy()->format('Y-m-d'),
                $date->copy()->addDay(1)->format('Y-m-d'),
                $date->copy()->addDay(2)->format('Y-m-d')
            ];

            $sum = 0;
            foreach($dates as $key) {
                $items = $data->get($key);
                if(!is_null($items)) {
                    foreach($items as $item) {
                        $sum += $item->amount_paid;
                    }
                }
            }

            return [$date->copy()->format('Y-m-d') => $sum];
        });

        return array_values($keyed->all());
    }

}
