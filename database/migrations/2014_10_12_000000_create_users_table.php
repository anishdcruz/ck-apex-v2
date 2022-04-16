<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');

            // optional
            $table->string('title')->nullable();
            $table->string('telephone')->nullable();
            $table->string('extension')->nullable();
            $table->string('mobile_number')->nullable();

            // login
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('is_admin')->default(0);
            $table->boolean('is_active')->default(0);

            // email
            $table->text('email_signature');

            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
