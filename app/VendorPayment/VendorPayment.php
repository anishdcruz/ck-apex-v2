<?php

namespace App\VendorPayment;

use Illuminate\Database\Eloquent\Model;
use App\Support\HasManyRelation;
use App\Support\Search;
use App\Currency;
use App\Vendor;

class VendorPayment extends Model
{
    use Search;
    use HasManyRelation;

    const PAID = 1;

    protected $table = 'vendor_payments';

    protected $search = [
        'number', 'payment_date', 'payment_mode', 'payment_reference',
        'amount_paid'
    ];

    protected $columns = [
        'id', 'number', 'payment_date', 'payment_mode', 'payment_reference',
        'amount_paid', 'created_at'
    ];

    protected $fillable = [
        'vendor_id', 'currency_id', 'payment_date', 'payment_mode',
        'payment_reference'
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'vendor_payment_id', 'id');
    }
}
