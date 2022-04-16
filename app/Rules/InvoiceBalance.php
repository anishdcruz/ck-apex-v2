<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Validator;
use App\Client;

class InvoiceBalance extends Validator implements Rule
{
    protected $text = 'The :attribute must be valid balance.';

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // attribute: items.*.amount_applied
        // value: *
        dd($attribute);
        //1. get invoice number and validate
        // $keys = $this->getExplicitKeys($attribute);
        // dd($keys);
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->text;
    }
}
