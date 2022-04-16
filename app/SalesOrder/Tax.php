<?php

namespace App\SalesOrder;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $table = 'sales_order_item_taxes';

    protected $fillable = [
        'name', 'rate', 'tax_authority'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'sales_order_item_id', 'id');
    }
}
