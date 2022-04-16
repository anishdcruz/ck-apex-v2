<?php

namespace App\PurchaseOrder;

use Illuminate\Database\Eloquent\Model;
use App\Support\HasManyRelation;
use App\Support\Search;
use App\Currency;
use App\Vendor;
use App\Bill\Bill;

class PurchaseOrder extends Model
{
    use Search;
    use HasManyRelation;

    const DRAFT = 1;
    const SENT = 2;
    const CONFIRMED = 3;
    const BILLED = 4;
    const CANCELLED = 5;
    const CLOSED = 6;
    const RECEIVED = 7;
    const PARTIALLY_RECEIVED = 8;

    protected $table = 'purchase_orders';

    protected $search = [
        'reference', 'date', 'terms', 'number'
    ];

    protected $columns = [
        'id', 'number', 'reference', 'date', 'total',
        'terms', 'created_at'
    ];

    protected $fillable = [
        'vendor_id', 'reference', 'currency_id', 'date', 'terms'
    ];

    protected $appends = ['is_editable'];

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class, 'purchase_order_id', 'id');
    }

    public function getIsEditableAttribute()
    {
        return in_array($this->attributes['status_id'], [1, 2]);
    }

}
