<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Support\Search;
use App\Bill\Bill;
use App\PurchaseOrder\PurchaseOrder;
use App\Expense;
use App\VendorPayment\VendorPayment;
use App\Product\Product;
use App\Product\Item as ProductItem;
use App\ReceiveOrder\ReceiveOrder;

class Vendor extends Model
{
    use Search;

    protected $search = [
        'person', 'company', 'email', 'mobile_number', 'work_phone',
        'billing_address', 'shipping_address', 'payment_details'
    ];

    protected $columns = [
        'id', 'person', 'company', 'email', 'mobile_number',
        'work_phone', 'billing_address', 'shipping_address', 'payment_details',
        'total_revenue', 'created_at'
    ];

    protected $fillable = [
        'person', 'company', 'email', 'mobile_number', 'work_phone',
        'billing_address', 'shipping_address', 'payment_details',
        'currency_id'
    ];

    protected $appends = [
        'text'
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function receiveOrders()
    {
        return $this->hasMany(ReceiveOrder::class);
    }

    public function payments()
    {
        return $this->hasMany(VendorPayment::class);
    }

    public function products()
    {
        return $this->hasMany(ProductItem::class, 'vendor_id', 'id');
    }

    public function getTextAttribute()
    {
        if(is_null($this->attributes['company'])) {
            return $this->attributes['person'];
        }

        return $this->attributes['person'] .' - '. $this->attributes['company'];;
    }

}
