<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Support\Search;
use App\Currency;
use App\Vendor;

class Expense extends Model
{
    use Search;

    protected $search = [
        'id', ' number', 'payment_date', 'amount_paid', 'description'
    ];

    protected $columns = [
        'id', 'number', 'payment_date', 'amount_paid', 'description',
        'created_at'
    ];

    protected $fillable = [
        'vendor_id', 'description', 'currency_id', 'payment_date',
        'amount_paid'
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
