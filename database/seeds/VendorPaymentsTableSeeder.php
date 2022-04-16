<?php

use Illuminate\Database\Seeder;
use App\VendorPayment\VendorPayment;
use App\VendorPayment\Item;
use Faker\Factory;

class VendorPaymentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        VendorPayment::truncate();
        Item::truncate();

        foreach(range(1, 50) as $i) {
            $payment = VendorPayment::create([
                'user_id' => 1,
                'vendor_id' => $faker->numberBetween(1, 25),
                'amount_paid' => $faker->numberBetween(1000, 3000),
                'number' => 'CP'.$faker->unique()->numberBetween(100000, 3000000),
                'payment_date' => '2017-8-'.mt_rand(6, 13),
                'payment_mode' => $faker->randomElement(['cheque', 'cash', 'bank_transfer']),
                'payment_reference' => '#asd'.$faker->numberBetween(10000, 900000),
                'document' => 'ch-1.jpg',
                'currency_id' => 1,
                'status_id' => 1
            ]);

            foreach(range(1, mt_rand(1, 3)) as $j) {
                Item::create([
                    'vendor_payment_id' => $payment->id,
                    'bill_id' => $faker->numberBetween(1, 25),
                    'amount_applied' => $faker->numberBetween(500, 3000)
                ]);
            }
        }
    }
}
