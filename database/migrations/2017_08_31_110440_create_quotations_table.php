<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('client_id')->unsigned();
            $table->string('number')->unique();
            $table->integer('currency_id')->unsigned();
            $table->string('reference')->nullable();
            $table->date('date');
            $table->double('sub_total');
            $table->double('total');
            $table->text('terms')->nullable();
            $table->tinyInteger('status_id');
            $table->timestamps();
        });

        Schema::create('quotation_items', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('quotation_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->float('qty');
            $table->double('unit_price');
            $table->timestamps();
        });

        Schema::create('quotation_item_taxes', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('quotation_item_id')->unsigned();
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
        Schema::dropIfExists('quotations');
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotation_item_taxes');
    }
}
