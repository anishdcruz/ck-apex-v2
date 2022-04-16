<?php

namespace App\Invoice;

use Illuminate\Database\Eloquent\Model;
use App\Support\HasManyRelation;
use App\Support\Search;
use App\ClientPayment\Item as ClientPaymentItem;
use App\AdvancePayment\Item as AdvancePaymentItem;
use App\Currency;
use App\Client;

class Invoice extends Model
{
    use Search;
    use HasManyRelation;

    const DRAFT = 1;
    const SENT = 2;
    const PARTIALLY_PAID = 3;
    const PAID = 4;
    const VOID = 5;

    protected $table = 'invoices';

    protected $search = [
        'reference', 'date', 'due_date', 'terms', 'number'
    ];

    protected $columns = [
        'id', 'number', 'reference', 'date', 'due_date', 'sub_total',
        'discount', 'total', 'terms', 'amount_paid', 'status_id',
        'created_at'
    ];

    protected $fillable = [
        'client_id', 'reference', 'currency_id', 'date', 'due_date',
        'discount', 'terms'
    ];

    protected $appends = ['is_editable', 'is_overdue'];

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

    public function clientPayments()
    {
        return $this->hasMany(ClientPaymentItem::class, 'invoice_id', 'id');
    }

    public function advancePayments()
    {
        return $this->hasMany(AdvancePaymentItem::class, 'invoice_id', 'id');
    }

    /**
     * Get all of the owning invoiceable models.
     */
    public function invoiceable()
    {
        return $this->morphTo();
    }

    public function getIsEditableAttribute()
    {
        return in_array($this->attributes['status_id'], [1]);
    }

    public function getIsOverdueAttribute()
    {
        return is_null($this->attributes['due_date'])
            ? false
            : date('Y-m-d') > $this->attributes['due_date'];
    }

    public function getParenttypeAttribute()
    {
        $type = isset($this->attributes['invoiceable_type']) ? $this->attributes['invoiceable_type'] : null;
        switch ($type) {
            case 'App\Quotation\Quotation':
                return 'Quotation';
                break;

            case 'App\SalesOrder\SalesOrder':
                return 'SalesOrder';
                break;

            default:
                return null;
                break;
        }
    }
}
