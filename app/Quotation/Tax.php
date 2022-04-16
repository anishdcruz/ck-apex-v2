<?php

namespace App\Quotation;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $table = 'quotation_item_taxes';

    protected $fillable = [
        'name', 'rate', 'tax_authority'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'quotation_item_id', 'id');
    }
}
