<?php

use Illuminate\Database\Seeder;
use App\Quotation\Quotation;
use App\Quotation\Item;
use App\Quotation\Tax;
use App\Product\Product;
use Faker\Factory;

class QuotationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        Quotation::truncate();
        Item::truncate();
        Tax::truncate();

        foreach(range(1, 25) as $i) {
            $quotation = Quotation::create([
                'user_id' => 1,
                'client_id' => $faker->numberBetween(1, 50),
                'number' => 'QT-'.$faker->unique()->numberBetween(100000, 900000),
                'reference' => mt_rand(0, 1) ? 'INQ-'.$faker->numberBetween(1000, 9000) : null,
                'currency_id' => 1,
                'date' => $faker->date,
                'sub_total' => $faker->numberBetween(1000, 9000),
                'total' => $faker->numberBetween(10000, 90000),
                'terms' => $faker->text,
                'status_id' => $faker->numberBetween(1, 6)
            ]);

            foreach(Product::inRandomOrder()->limit(mt_rand(1, 4))->get() as $j) {
                $item = Item::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $j->id,
                    'qty' => $faker->numberBetween(1, 12),
                    'unit_price' => $j->unit_price,
                ]);

                foreach($j->taxes as $tax) {
                    Tax::create([
                        'quotation_item_id' => $item->id,
                        'name' => $tax->name,
                        'rate' => $tax->rate,
                        'tax_authority' => $tax->tax_authority
                    ]);
                }
            }
        }
    }
}
