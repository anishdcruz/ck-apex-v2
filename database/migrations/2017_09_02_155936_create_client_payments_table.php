<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('number')->unique();
            $table->integer('client_id')->unsigned();
            $table->integer('currency_id')->unsigned();
            $table->double('amount_received');
            $table->date('payment_date');
            $table->string('payment_mode');
            $table->string('payment_reference')->nullable();
            $table->string('document')->nullable();
            $table->integer('status_id');
            $table->timestamps();
        });

        Schema::create('client_payment_items', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('client_payment_id')->unsigned();
            $table->integer('invoice_id')->unsigned();
            $table->double('amount_applied');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_payments');
        Schema::dropIfExists('client_payment_items');
    }
}
