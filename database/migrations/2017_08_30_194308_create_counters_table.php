<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('counters', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key')->unique();
            $table->string('prefix')->unique();
            $table->string('value');
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
        Schema::dropIfExists('counters');
    }

    protected function seedData()
    {
        $counters = [
            ['product', 'PD-', 1000000],
            ['quotation', 'QT-', 1000000],
            ['sales_order', 'SO-', 1000000],
            ['advance_payment', 'AD-', 1000000],
            ['invoice', 'IN-', 1000000],
            ['client_payment', 'CP-', 1000000],
            ['expense', 'EX-', 1000000],
            ['purchase_order', 'PO-', 1000000],
            ['bill', 'BL-', 1000000],
            ['vendor_payment', 'VP-', 1000000],
            ['receive_order', 'RO-', 1000000],
            ['goods_issue', 'GI-', 1000000]
        ];

        $processed = [];
        $today = today();

        foreach($counters as $counter) {
            $processed[] = [
                'key' => $counter[0],
                'prefix' => $counter[1],
                'value' => $counter[2],
                'created_at' => $today,
                'updated_at' => $today
            ];
        }

        DB::table('counters')->insert($processed);
    }
}
