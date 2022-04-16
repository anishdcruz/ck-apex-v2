<?php

namespace App\Product;

use Illuminate\Database\Eloquent\Model;
use App\Vendor;
use App\Currency;

class Item extends Model {

    protected $table = 'product_items';

    protected $fillable = [
        'vendor_id', 'reference', 'price', 'currency_id'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }
}
