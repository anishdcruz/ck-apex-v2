<?php

use Illuminate\Database\Seeder;
use App\SalesOrder\SalesOrder;
use App\SalesOrder\Item;
use App\SalesOrder\Tax;
use App\Product\Product;

use Faker\Factory;

class SalesOrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        SalesOrder::truncate();
        Item::truncate();

        foreach(range(1, 25) as $i) {
            $quotation = SalesOrder::create([
                'user_id' => 1,
                'client_id' => $faker->numberBetween(1, 50),
                'number' => 'SL-'.$faker->unique()->numberBetween(100000, 900000),
                'reference' => mt_rand(0, 1) ? 'Q-'.$faker->numberBetween(1000, 9000) : null,
                'quotation_id' => mt_rand(0, 1) ? $faker->numberBetween(1, 25) : null,
                'currency_id' => 1,
                'date' => $faker->date,
                'document' => mt_rand(0, 1) ? 'po-1.jpg' : null,
                'sub_total' => $faker->numberBetween(1000, 9000),
                'total' => $faker->numberBetween(10000, 90000),
                'terms' => $faker->text,
                'status_id' => $faker->numberBetween(1, 6)
            ]);

            foreach(Product::inRandomOrder()->limit(mt_rand(1, 4))->get() as $j) {
                $item = Item::create([
                    'sales_order_id' => $quotation->id,
                    'product_id' => $j->id,
                    'qty' => $faker->numberBetween(1, 12),
                    'unit_price' => $j->unit_price,
                ]);

                foreach($j->taxes as $tax) {
                    Tax::create([
                        'sales_order_item_id' => $item->id,
                        'name' => $tax->name,
                        'rate' => $tax->rate,
                        'tax_authority' => $tax->tax_authority
                    ]);
                }
            }
        }
    }
}
