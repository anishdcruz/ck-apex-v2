<?php

namespace App\Bill;

use Illuminate\Database\Eloquent\Model;
use App\Support\HasManyRelation;
use App\PurchaseOrder\PurchaseOrder;
use App\VendorPayment\Item as VendorPaymentItem;
use App\Support\Search;
use App\Currency;
use App\Vendor;

class Bill extends Model
{
    use Search;
    use HasManyRelation;

    const DRAFT = 1;
    const PARTIALLY_PAID = 2;
    const PAID = 3;
    const VOID = 4;

    protected $table = 'bills';

    protected $search = [
        'reference', 'date', 'due_date', 'terms', 'number', 'note'
    ];

    protected $columns = [
        'id', 'number', 'reference', 'date', 'due_date', 'total',
        'terms', 'note', 'created_at', 'status_id'
    ];

    protected $fillable = [
        'vendor_id', 'reference', 'currency_id', 'date',
        'due_date', 'term', 'note'
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

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id', 'id');
    }

    public function vendorPayments()
    {
        return $this->hasMany(VendorPaymentItem::class, 'bill_id', 'id');
    }

    public function getIsEditableAttribute()
    {
        return in_array($this->attributes['status_id'], [1]);
    }
}
