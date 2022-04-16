<?php

namespace App\AdvancePayment;

use Illuminate\Database\Eloquent\Model;
use App\Invoice\Invoice;

class Item extends Model
{
    protected $table = 'advance_payment_items';

    protected $fillable = [
        'invoice_id', 'amount_applied'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(AdvancePayment::class, 'advance_payment_id', 'id');
    }
}
