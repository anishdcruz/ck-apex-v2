<?php

namespace App\VendorPayment;

use Illuminate\Database\Eloquent\Model;
use App\Bill\Bill;

class Item extends Model
{
    protected $table = 'vendor_payment_items';

    protected $fillable = [
        'bill_id', 'amount_applied'
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(VendorPayment::class, 'vendor_payment_id', 'id');
    }
}
