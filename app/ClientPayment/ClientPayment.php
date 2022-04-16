<?php

namespace App\ClientPayment;

use Illuminate\Database\Eloquent\Model;
use App\Support\HasManyRelation;
use App\Support\Search;
use App\Client;
use App\Currency;

class ClientPayment extends Model
{
    use Search;
    use HasManyRelation;

    const RECEIVED = 1;
    const DEPOSITED = 2;

    protected $table = 'client_payments';

    protected $search = [
        'number', 'payment_date', 'payment_mode', 'payment_reference',
        'amount_received'
    ];

    protected $columns = [
        'id', 'number', 'payment_date', 'payment_mode', 'payment_reference',
        'amount_received', 'created_at'
    ];

    protected $fillable = [
        'client_id', 'currency_id', 'payment_date', 'payment_mode',
        'payment_reference'
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'client_payment_id', 'id');
    }
}
