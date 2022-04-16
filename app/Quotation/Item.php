<?php

namespace App\Quotation;

use Illuminate\Database\Eloquent\Model;
use App\Product\Product;

class Item extends Model
{
    protected $table = 'quotation_items';

    protected $fillable = [
        'product_id', 'qty', 'unit_price'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function taxes()
    {
        return $this->hasMany(Tax::class, 'quotation_item_id', 'id');
    }
}
