<?php

namespace App\Support;

use App\Currency;

class CurrencyWrapper {

    protected $key = 'currency_id';

    /**
     * Get the default currency
     *
     * @return App\Currency or null
     */
    public function default()
    {
        $found = settings()->get($this->key);

        if(!is_null($found)) {
            // get from database with found ID
            return Currency::find($found);
        }

        return $found;
    }

    /**
     * Get the default currency in array
     *
     * @return array
     */
    public function defaultToArray()
    {
        $found = $this->default();

        $array = [
            'currency' => null,
            'currency_id' => null
        ];

        if(!is_null($found)) {
            array_set($array, 'currency', $found->toArray());
            array_set($array, 'currency_id', $found->id);
        }

        return $array;
    }
}
