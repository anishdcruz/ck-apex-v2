<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('vendor_id')->unsigned();
            $table->string('number')->unique();
            $table->integer('currency_id')->unsigned();
            $table->string('reference')->nullable();
            $table->integer('purchase_order_id')->nullable();
            $table->date('date');
            $table->date('due_date')->nullable();
            $table->double('total');
            $table->text('terms')->nullable();
            $table->string('document')->nullable();
            $table->tinyInteger('status_id');
            $table->text('note')->nullable();
            $table->double('amount_paid')->default(0);
            $table->timestamps();
        });

        Schema::create('bill_items', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('bill_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->string('vendor_reference');
            $table->float('qty');
            $table->double('unit_price');
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
        Schema::dropIfExists('bills');
        Schema::dropIfExists('bill_items');
    }
}
