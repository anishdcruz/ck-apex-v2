<?php

use Illuminate\Database\Seeder;
use App\AdvancePayment\AdvancePayment;
use App\AdvancePayment\Item;
use Faker\Factory;

class AdvancePaymentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        AdvancePayment::truncate();
        Item::truncate();

        foreach(range(1, 25) as $i) {
            AdvancePayment::create([
                'user_id' => 1,
                'client_id' => $faker->numberBetween(1, 50),
                'number' => 'AP-'.$faker->unique()->numberBetween(1000000, 9000000),
                'payment_reference' => mt_rand(0, 1) ? 'QOT-'.$faker->numberBetween(1000, 9000) : null,
                'quotation_id' => mt_rand(0, 1) ? $faker->numberBetween(1, 25) : null,
                'currency_id' => 1,
                'payment_date' => '2017-9-'.mt_rand(1, 28),
                'payment_mode' => 'cheque',
                'description' => $faker->text,
                'amount_received' => $faker->numberBetween(10000, 90000),
                'document' => 'ch-1.jpg',
                'status_id' => $faker->numberBetween(1, 3)
            ]);
        }
    }
}
