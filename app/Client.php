<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Support\Search;
use App\Invoice\Invoice;
use App\Quotation\Quotation;
use App\SalesOrder\SalesOrder;
use App\ClientPayment\ClientPayment;
use App\AdvancePayment\AdvancePayment;

class Client extends Model
{
    use Search;

    protected $search = [
        'person', 'company', 'email', 'mobile_number',
        'work_phone', 'billing_address',
        'shipping_address'
    ];

    protected $columns = [
        'id', 'person', 'company', 'email', 'mobile_number',
        'work_phone', 'billing_address', 'shipping_address',
        'total_revenue', 'created_at'
    ];

    protected $fillable = [
        'person', 'company', 'email', 'mobile_number',
        'work_phone', 'billing_address',
        'shipping_address',
        'currency_id'
    ];

    protected $appends = [
        'text'
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'client_id', 'id');
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class, 'client_id', 'id');
    }

    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class, 'client_id', 'id');
    }

    public function advancePayments()
    {
        return $this->hasMany(AdvancePayment::class, 'client_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany(ClientPayment::class, 'client_id', 'id');
    }

    public function getTextAttribute()
    {
        if(is_null($this->attributes['company'])) {
            return $this->attributes['person'];
        }

        return $this->attributes['person'] .' - '. $this->attributes['company'];;
    }
}
