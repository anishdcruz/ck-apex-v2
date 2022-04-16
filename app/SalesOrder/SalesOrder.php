<?php

namespace App\SalesOrder;

use Illuminate\Database\Eloquent\Model;
use App\Support\HasManyRelation;
use App\Quotation\Quotation;
use App\Invoice\Invoice;
use App\Support\Search;
use App\Currency;
use App\Client;

class SalesOrder extends Model
{
    use Search;
    use HasManyRelation;

    const DRAFT = 1;
    const SENT = 2;
    const CONFIRMED = 3;
    const HOLD = 4;
    const VOID = 5;
    const CLOSED = 6;
    const PARTIALLY_ISSUED = 7;
    const ISSUED = 8;

    protected $table = 'sales_orders';

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

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_id', 'id');
    }

    public function invoices()
    {
        return $this->morphMany(Invoice::class, 'invoiceable');
    }

    public function getIsEditableAttribute()
    {
        return in_array($this->attributes['status_id'], [1, 2]);
    }
}
