<?php

namespace App\Product;

use Illuminate\Database\Eloquent\Model;
use App\Product\Product;

class Tax extends Model {

    protected $table = 'product_taxes';

    protected $fillable = [
        'name', 'rate', 'tax_authority'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
