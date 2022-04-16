<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('vendor_id')->unsigned();
            $table->string('number')->unique();
            $table->integer('currency_id')->unsigned();
            $table->string('reference')->nullable();
            $table->date('date');
            $table->double('total');
            $table->text('terms')->nullable();
            $table->tinyInteger('status_id');
            $table->boolean('is_received')->default(0);
            $table->timestamps();
        });

        Schema::create('purchase_order_items', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('purchase_order_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->string('vendor_reference');
            $table->float('qty');
            $table->float('qty_received')->default(0);
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
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('purchase_order_items');
    }
}
