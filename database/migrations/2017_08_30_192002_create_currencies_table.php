<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->unique();
            $table->string('name');
            $table->tinyInteger('decimal_place');
            $table->timestamps();
        });

        $this->seedData();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currencies');
    }

    protected function seedData()
    {
        $currencies = json_decode(File::get(base_path('database/data/currency.json')));
        $processed = [];
        $today = today();

        foreach($currencies as $currency) {
            $processed[] = [
                'code' => $currency->code,
                'name' => $currency->name,
                'decimal_place' => $currency->decimal_digits,
                'created_at' => $today,
                'updated_at' => $today
            ];
        }

        DB::table('currencies')->insert($processed);
    }
}
