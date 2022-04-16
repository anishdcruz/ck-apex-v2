<?php

use Illuminate\Database\Seeder;
use App\Bill\Bill;
use App\Bill\Item;
use Faker\Factory;

class BillsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        Bill::truncate();
        Item::truncate();

        foreach(range(1, 25) as $i) {
            $quotation = Bill::create([
                'user_id' => 1,
                'vendor_id' => $faker->numberBetween(1, 50),
                'number' => 'SL-'.$faker->unique()->numberBetween(100000, 900000),
                'reference' => mt_rand(0, 1) ? 'Q-'.$faker->numberBetween(1000, 9000) : null,
                'purchase_order_id' => mt_rand(0, 1) ? $faker->numberBetween(1, 25) : null,
                'currency_id' => 1,
                'date' => $faker->date,
                'due_date' => $faker->date,
                'total' => $faker->numberBetween(10000, 90000),
                'terms' => $faker->text,
                'note' => $faker->text,
                'status_id' => $faker->numberBetween(1, 4),
                'amount_paid' => 0
            ]);

            foreach(range(1, mt_rand(1, 10)) as $j) {
                Item::create([
                    'bill_id' => $quotation->id,
                    'product_id' => $faker->numberBetween(1, 30),
                    'vendor_reference' => $faker->numberBetween(1000000, 9000000),
                    'qty' => $faker->numberBetween(1, 12),
                    'unit_price' => $faker->numberBetween(100, 900),
                ]);
            }
        }
    }
}
