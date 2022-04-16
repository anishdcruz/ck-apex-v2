<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Currency;

class CurrencyController extends Controller
{
    public function search()
    {
        $results = Currency::orderBy('name')
            ->when(request('q'), function($query) {
                $query->where('name', 'like', '%'.request('q').'%')
                    ->orWhere('code', 'like', '%'.request('q').'%');
            })
            ->limit(6)
            ->get();

        return api([
            'results' => $results
        ]);
    }
}
