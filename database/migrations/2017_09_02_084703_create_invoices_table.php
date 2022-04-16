<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('client_id')->unsigned();
            $table->string('number')->unique();
            $table->integer('currency_id')->unsigned();
            $table->string('reference')->nullable();
            $table->integer('invoiceable_id')->nullable();
            $table->string('invoiceable_type')->nullable();
            $table->date('date');
            $table->date('due_date')->nullable();
            $table->double('sub_total');
            $table->double('total');
            $table->text('terms')->nullable();
            $table->tinyInteger('status_id');
            $table->double('amount_paid')->default(0);
            $table->timestamps();
        });

        Schema::create('invoice_items', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->float('qty');
            $table->double('unit_price');
            $table->timestamps();
        });

        Schema::create('invoice_item_taxes', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_item_id')->unsigned();
            $table->string('name');
            $table->float('rate');
            $table->string('tax_authority');
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
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoice_item_taxes');
    }
}
