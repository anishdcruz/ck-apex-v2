<?php

namespace App\GoodsIssue;

use Illuminate\Database\Eloquent\Model;
use App\Product\Product;
use App\SalesOrder\Item as SalesOrderItem;

class Item extends Model
{
    protected $table = 'goods_issue_items';

    protected $fillable = [
        'product_id', 'sales_order_item_id', 'qty'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function SalesOrderItem()
    {
        return $this->belongsTo(SalesOrderItem::class, 'sales_order_item_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(GoodsIssue::class, 'goods_issue_id', 'id');
    }
}
