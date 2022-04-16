<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key')->unique();
            $table->string('value')->nullable();
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
        Schema::dropIfExists('settings');
    }

    protected function seedData()
    {
        $settings = [
            'currency_id' => null,
            'app_title' => 'Apex',
            'uploaded_logo' => null,
            'company_name' => null,
            'company_address' => null,
            'company_telephone' => null,
            'company_email' => null,
            'company_website' => null,
            'company_payment_details' => null,
            'sent_from_email' => null,
            'sent_from_name' => null,
            'global_bcc_email' => null,
            'footer_line_1' => null,
            'footer_line_2' => null,
            'footer_line_3' => null,
            'header' => null,
            'footer' => null,
            'header-html' => null,
            'footer-html' => null
        ];

        $processed = [];
        $today = today();

        foreach($settings as $key => $value) {
            $processed[] = [
                'key' => $key,
                'value' => $value,
                'created_at' => $today,
                'updated_at' => $today
            ];
        }

        DB::table('settings')->insert($processed);
    }
}
