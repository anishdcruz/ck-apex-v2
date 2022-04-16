<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('client_id')->unsigned();
            $table->string('number')->unique();
            $table->integer('currency_id')->unsigned();
            $table->string('reference')->nullable();
            $table->integer('quotation_id')->nullable();
            $table->date('date');
            $table->double('sub_total');
            $table->double('total');
            $table->text('terms')->nullable();
            $table->string('document')->nullable();
            $table->tinyInteger('status_id');
            $table->timestamps();
        });

        Schema::create('sales_order_items', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('sales_order_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->float('qty');
            $table->float('qty_issued')->default(0);
            $table->double('unit_price');
            $table->timestamps();
        });

         Schema::create('sales_order_item_taxes', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('sales_order_item_id')->unsigned();
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
        Schema::dropIfExists('sales_orders');
        Schema::dropIfExists('sales_order_items');
        Schema::dropIfExists('sales_order_item_taxes');
    }
}
