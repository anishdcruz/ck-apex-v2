<?php

namespace App\PurchaseOrder;

use Illuminate\Database\Eloquent\Model;
use App\Product\Product;

class Item extends Model
{
    protected $table = 'purchase_order_items';

    protected $fillable = [
        'product_id', 'qty', 'unit_price', 'vendor_reference'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id', 'id');
    }
}
