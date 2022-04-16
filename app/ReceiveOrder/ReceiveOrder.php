<?php

namespace App\ReceiveOrder;

use Illuminate\Database\Eloquent\Model;
use App\Support\HasManyRelation;
use App\Support\Search;
use App\Currency;
use App\Vendor;
use App\PurchaseOrder\PurchaseOrder;

class ReceiveOrder extends Model
{
    use Search;
    use HasManyRelation;

    const RECEIVED = 1;

    protected $table = 'receive_orders';

    protected $search = [
        'number', 'date', 'note'
    ];

    protected $columns = [
        'id', 'number', 'date', 'note', 'status_id', 'created_at'
    ];

    protected $fillable = [
        'vendor_id', 'date', 'note'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'receive_order_id', 'id');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
