<?php

use Illuminate\Database\Seeder;
use App\ClientPayment\ClientPayment;
use App\ClientPayment\Item;
use Faker\Factory;

class ClientPaymentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        ClientPayment::truncate();
        Item::truncate();

        foreach(range(1, 50) as $i) {
            $payment = ClientPayment::create([
                'user_id' => 1,
                'client_id' => $faker->numberBetween(1, 25),
                'amount_received' => $faker->numberBetween(1000, 3000),
                'number' => 'CP'.$faker->unique()->numberBetween(100000, 3000000),
                'payment_date' => '2017-9-'.mt_rand(6, 13),
                'payment_mode' => $faker->randomElement(['cheque', 'cash', 'bank_transfer']),
                'payment_reference' => '#asd'.$faker->numberBetween(10000, 900000),
                'document' => 'ch-1.jpg',
                'currency_id' => 1,
                'status_id' => 1
            ]);

            foreach(range(1, mt_rand(1, 3)) as $j) {
                Item::create([
                    'client_payment_id' => $payment->id,
                    'invoice_id' => $faker->numberBetween(1, 25),
                    'amount_applied' => $faker->numberBetween(500, 3000)
                ]);
            }
        }
    }
}
