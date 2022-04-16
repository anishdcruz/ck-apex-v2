<?php

namespace App\Invoice;

use Illuminate\Database\Eloquent\Model;
use App\Product\Product;

class Item extends Model
{
    protected $table = 'invoice_items';

    protected $fillable = [
        'product_id', 'qty', 'unit_price'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }

    public function taxes()
    {
        return $this->hasMany(Tax::class, 'invoice_item_id', 'id');
    }
}
