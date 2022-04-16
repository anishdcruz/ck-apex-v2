<?php

namespace App\Invoice;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $table = 'invoice_item_taxes';

    protected $fillable = [
        'name', 'rate', 'tax_authority'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'invoice_item_id', 'id');
    }
}
