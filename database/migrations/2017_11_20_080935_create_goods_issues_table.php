<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_issues', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('client_id')->unsigned();
            $table->string('number')->unique();
            $table->integer('sales_order_id')->unsigned();
            $table->date('date');
            $table->tinyInteger('status_id');
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('goods_issue_items', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('goods_issue_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('sales_order_item_id')->unsigned();
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
        Schema::dropIfExists('goods_issues');
        Schema::dropIfExists('goods_issue_items');
    }
}
