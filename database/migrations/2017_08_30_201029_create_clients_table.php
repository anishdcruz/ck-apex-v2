<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('person');
            $table->string('company')->nullable();
            $table->string('email')->unique();
            $table->string('work_phone')->nullable();
            $table->string('mobile_number')->nullable();
            $table->integer('currency_id')->unsigned();
            $table->text('billing_address')->nullable();
            $table->text('shipping_address')->nullable();
            $table->double('total_revenue')->default(0);
            $table->double('unused_credit')->default(0);
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
        Schema::dropIfExists('clients');
    }
}
