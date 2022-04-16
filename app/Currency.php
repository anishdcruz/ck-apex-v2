<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'code', 'name', 'decimal_place'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function getTextAttribute()
    {
        return $this->attributes['name'].' ('.$this->attributes['code'].')';
    }

    protected $appends = ['text'];
}
