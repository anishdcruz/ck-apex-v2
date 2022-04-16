<?php

namespace App\Bill;

use Illuminate\Database\Eloquent\Model;
use App\Product\Product;

class Item extends Model
{
    protected $table = 'bill_items';

    protected $fillable = [
        'product_id', 'qty', 'unit_price', 'vendor_reference'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
