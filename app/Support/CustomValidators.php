<?php

namespace App\Support;

use Illuminate\Validation\Validator;
use DB;

class CustomValidators extends Validator {

    public function validateInvoiceBalance($attribute, $value, $parameters, $validator)
    {
        $amountApplied = $value;

        if($amountApplied == 0) {
            return true;
        }

        $keys = $this->getExplicitKeys($attribute);
        $parameters = $this->replaceAsterisksInParameters($parameters, $keys);
        $invoiceId = $this->getValue($parameters[0]);

        $invoice = DB::table('invoices')
            ->whereIn('status_id', [2, 3])
            ->where('id', $invoiceId)
            ->first();

        if(is_null($invoice)) {
            return false;
        }

        $balance = $invoice->total - $invoice->amount_paid;

        $amount = $invoice->amount_paid + $amountApplied;
        // 1. amount applied not greater than balance
        // 2. amount applied + paid not greater than total

        if($balance >= $amountApplied && $amount <= $invoice->total) {
            return true;
        }

        return false;
    }

    public function validateBillBalance($attribute, $value, $parameters, $validator)
    {
        $amountApplied = $value;

        if($amountApplied == 0) {
            return true;
        }

        $keys = $this->getExplicitKeys($attribute);
        $parameters = $this->replaceAsterisksInParameters($parameters, $keys);
        $invoiceId = $this->getValue($parameters[0]);

        $bill = DB::table('bills')
            ->whereIn('status_id', [1, 2])
            ->where('id', $invoiceId)
            ->first();

        if(is_null($bill)) {
            return false;
        }

        $balance = $bill->total - $bill->amount_paid;

        $amount = $bill->amount_paid + $amountApplied;
        // 1. amount applied not greater than balance
        // 2. amount applied + paid not greater than total

        if($balance >= $amountApplied && $amount <= $bill->total) {
            return true;
        }

        return false;
    }

    public function validatePurchaseOrderItem($attribute, $value, $parameters, $validator)
    {
        $qtyReceived = $value;

        if($qtyReceived == 0) {
            return true;
        }

        $keys = $this->getExplicitKeys($attribute);
        $parameters = $this->replaceAsterisksInParameters($parameters, $keys);
        $POItemId = $this->getValue($parameters[0]);


        $POItem = DB::table('purchase_order_items')
            ->where('id', $POItemId)
            ->first();


        if(is_null($POItem)) {
            return false;
        }

        $balance = $POItem->qty - $POItem->qty_received;

        $amount = $POItem->qty_received + $qtyReceived;
        // 1. amount applied not greater than balance
        // 2. amount applied + paid not greater than total

        if($balance >= $qtyReceived && $amount <= $POItem->qty) {
            return true;
        }

        return false;
    }

    public function validateSalesOrderItem($attribute, $value, $parameters, $validator)
    {
        $qtyIssued = $value;

        if($qtyIssued == 0) {
            return true;
        }

        $keys = $this->getExplicitKeys($attribute);
        $parameters = $this->replaceAsterisksInParameters($parameters, $keys);
        $POItemId = $this->getValue($parameters[0]);


        $POItem = DB::table('sales_order_items')
            ->where('id', $POItemId)
            ->first();


        if(is_null($POItem)) {
            return false;
        }

        $balance = $POItem->qty - $POItem->qty_issued;

        $amount = $POItem->qty_issued + $qtyIssued;
        // 1. amount applied not greater than balance
        // 2. amount applied + paid not greater than total

        if($balance >= $qtyIssued && $amount <= $POItem->qty) {
            return true;
        }

        return false;
    }
}
