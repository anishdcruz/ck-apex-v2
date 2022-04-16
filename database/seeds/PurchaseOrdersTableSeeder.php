<?php

use Illuminate\Database\Seeder;
use App\PurchaseOrder\PurchaseOrder;
use App\PurchaseOrder\Item;
use Faker\Factory;

class PurchaseOrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        PurchaseOrder::truncate();
        Item::truncate();

        foreach(range(1, 25) as $i) {
            $po = PurchaseOrder::create([
                'user_id' => 1,
                'vendor_id' => $faker->numberBetween(1, 50),
                'number' => 'PO-'.$faker->unique()->numberBetween(100000, 900000),
                'reference' => mt_rand(0, 1) ? 'Q-'.$faker->numberBetween(1000, 9000) : null,
                'currency_id' => 1,
                'date' => $faker->date,
                'total' => $faker->numberBetween(10000, 90000),
                'terms' => $faker->text,
                'status_id' => $faker->numberBetween(1, 5)
            ]);

            foreach(range(1, mt_rand(1, 10)) as $j) {
                Item::create([
                    'purchase_order_id' => $po->id,
                    'product_id' => $faker->numberBetween(1, 30),
                    'vendor_reference' => $faker->numberBetween(5000000, 9000000),
                    'qty' => $faker->numberBetween(1, 12),
                    'unit_price' => $faker->numberBetween(100, 900),
                ]);
            }
        }
    }
}
