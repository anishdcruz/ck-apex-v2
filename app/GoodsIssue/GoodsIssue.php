<?php

namespace App\GoodsIssue;

use Illuminate\Database\Eloquent\Model;
use App\Support\HasManyRelation;
use App\Support\Search;
use App\Currency;
use App\Client;
use App\SalesOrder\SalesOrder;

class GoodsIssue extends Model
{
    use Search;
    use HasManyRelation;

    const ISSUED = 1;

    protected $table = 'goods_issues';

    protected $search = [
        'number', 'date', 'note'
    ];

    protected $columns = [
        'id', 'number', 'date', 'note', 'status_id', 'created_at'
    ];

    protected $fillable = [
        'client_id', 'date', 'note'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'goods_issue_id', 'id');
    }

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }
}
