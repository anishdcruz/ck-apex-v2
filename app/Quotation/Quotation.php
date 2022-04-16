<?php

namespace App\Quotation;

use Illuminate\Database\Eloquent\Model;
use App\Support\HasManyRelation;
use App\Support\Search;
use App\AdvancePayment\AdvancePayment;
use App\SalesOrder\SalesOrder;
use App\Invoice\Invoice;
use App\Currency;
use App\Client;

class Quotation extends Model
{
    use Search;
    use HasManyRelation;

    const DRAFT = 1;
    const SENT = 2;
    const ACCEPTED = 3;
    const DECLINED = 4;
    const SALES_ORDERED = 5;
    const INVOICED = 6;

    protected $table = 'quotations';

    protected $search = [
        'reference', 'date', 'terms', 'number'
    ];

    protected $columns = [
        'id', 'number', 'reference', 'date', 'sub_total',
        'discount', 'total', 'terms', 'created_at'
    ];

    protected $fillable = [
        'client_id', 'reference', 'currency_id', 'date',
        'discount', 'terms'
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

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function invoices()
    {
        return $this->morphMany(Invoice::class, 'invoiceable');
    }

    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class, 'quotation_id', 'id');
    }

    public function advancePayments()
    {
        return $this->hasMany(AdvancePayment::class, 'quotation_id', 'id');
    }

    public function getIsEditableAttribute()
    {
        return in_array($this->attributes['status_id'], [1, 2]);
    }
}
