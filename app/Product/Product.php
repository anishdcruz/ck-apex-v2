<?php

namespace App\Product;

use Illuminate\Database\Eloquent\Model;
use App\Support\HasManyRelation;
use App\Support\Search;
use App\Currency;

class Product extends Model
{
    use Search;
    use HasManyRelation;

    protected $table = 'products';

    protected $search = [
        'item_code', 'description'
    ];

    protected $columns = [
        'id', 'item_code', 'description', 'unit_price',
        'created_at'
    ];

    protected $fillable = [
        'description', 'unit_price', 'currency_id'
    ];

    protected $appends = [
        'text'
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'product_id', 'id');
    }

    public function taxes()
    {
        return $this->hasMany(Tax::class, 'product_id', 'id');
    }

    public function getTextAttribute()
    {
        return $this->attributes['item_code'] .' - '. $this->attributes['description'];;
    }
}
