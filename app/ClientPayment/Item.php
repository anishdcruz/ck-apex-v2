<?php

namespace App\ClientPayment;

use Illuminate\Database\Eloquent\Model;
use App\Invoice\Invoice;

class Item extends Model
{
    protected $table = 'client_payment_items';

    protected $fillable = [
        'invoice_id', 'amount_applied'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(ClientPayment::class, 'client_payment_id', 'id');
    }
}
