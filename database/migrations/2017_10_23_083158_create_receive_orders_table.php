<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReceiveOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receive_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('vendor_id')->unsigned();
            $table->string('number')->unique();
            $table->integer('purchase_order_id')->unsigned();
            $table->date('date');
            $table->string('document')->nullable();
            $table->tinyInteger('status_id');
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('receive_order_items', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('receive_order_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('purchase_order_item_id')->unsigned();
            $table->float('qty');
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
        Schema::dropIfExists('receive_orders');
        Schema::dropIfExists('receive_order_items');
    }
}
