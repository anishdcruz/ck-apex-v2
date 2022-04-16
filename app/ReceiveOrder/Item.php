<?php

namespace App\ReceiveOrder;

use Illuminate\Database\Eloquent\Model;
use App\Product\Product;
use App\PurchaseOrder\Item as PurchaseOrderItem;

class Item extends Model
{
    protected $table = 'receive_order_items';

    protected $fillable = [
        'product_id', 'purchase_order_item_id', 'qty'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function PurchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'purchase_order_item_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(ReceiveOrder::class, 'receive_order_id', 'id');
    }
}
