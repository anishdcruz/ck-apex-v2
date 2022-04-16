<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('item_code')->unique();
            $table->text('description');
            $table->double('unit_price');
            $table->boolean('has_inventory')->default(0);
            $table->integer('qty_on_hand')->default(0);
            $table->integer('currency_id')->unsigned();
            $table->timestamps();
        });

        Schema::create('product_items', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->unsigned();
            $table->integer('vendor_id')->unsigned();
            $table->string('reference');
            $table->double('price');
            $table->integer('currency_id')->unsigned();
            $table->timestamps();
        });

        Schema::create('product_taxes', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->unsigned();
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
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_items');
        Schema::dropIfExists('product_taxes');
    }
}
