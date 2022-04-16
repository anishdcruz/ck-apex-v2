<?php

namespace App\AdvancePayment;

use Illuminate\Database\Eloquent\Model;
use App\Support\HasManyRelation;
use App\Quotation\Quotation;
use App\Support\Search;
use App\Client;
use App\Currency;

class AdvancePayment extends Model
{
    use Search;
    use HasManyRelation;

    const RECEIVED = 1;
    const DEPOSITED = 2;
    const DRAWN = 3;

    protected $search = [
        'number', 'payment_date', 'payment_mode', 'payment_reference',
        'description'
    ];

    protected $columns = [
        'id', 'number', 'payment_date', 'payment_mode', 'payment_reference',
        'amount_received', 'description', 'created_at'
    ];

    protected $fillable = [
        'client_id', 'currency_id', 'payment_date', 'payment_mode',
        'payment_reference', 'description', 'amount_received'
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'advance_payment_id', 'id');
    }
}
