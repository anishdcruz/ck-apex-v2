<?php

use Illuminate\Database\Seeder;
use Faker\Factory;
use App\Product\Product;
use App\Product\Item;
use App\Product\Tax;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        Product::truncate();
        Item::truncate();
        Tax::truncate();

        $products = [
            'IP 500 V2 Control Unit',
            'IP500 Digital Station 30',
            'IP500 Digital Station 30A',
            'IP500 Digital Station 30B',
            'IP500 Compact Rack Mount Kit',
            'IP500 Rack Mounting Kit',
            'IP500 Digital Station 16',
            'IP500 Digital Station 16A',
            'IP500 Digital Station 16B',
            'IP500 V2 Combination Card ATM',
            'IP500V2 VCM32 V2',
            'IP500 Phone 2 Card - 2 POT ports',
            'IP500 Phone 8 Card - 8 POT ports',
            'IP500 Digital Station 8 Card - 8 DS ports',
            'IP500 V2 Norstar TCM8 Card - 8 Norstar ports',
            'IP500 ETR Extension Card - 6 ETR ports',
            'IP500 V2 Analog Trunk 4 Card - 4 lines',
            'IP500 Single Universal T1/PRI Card',
            'IP500 Dual Universal T1/PRI Card',
            'IP500 4-Port Expansion Card',
            '9601 SIP Phone',
            '9608 IP Phone',
            '9611G IP Phone',
            '9621G IP phone',
            '9630G IP Phone',
            '9641G IP Phone',
            '9670G IP Phone',
            'SBM24 Expansion Module',
            'Gigabit Ethernet Adaptor',
            '1608 IP Phone'
        ];
        foreach($products as $i) {
            $product = new Product([
                'user_id' => 1,
                'item_code' => 'P'.$faker->unique()->numberBetween(1000000, 9000000),
                'description' => 'Avaya '.$i,
                'unit_price' => $faker->numberBetween(10, 500),
                'currency_id' => 1,
                'has_inventory' => 1
            ]);

            $product->save();
            foreach(range(0, mt_rand(0, 2)) as $j) {
                Tax::create([
                    'product_id' => $product->id,
                    'name' => $faker->randomElement(['GST', 'VAT', 'CES']),
                    'rate' => $faker->randomElement([3, 6, 9, 10, 17]),
                    'tax_authority' => 'Tax Authority'
                ]);
            }
            foreach(range(1, mt_rand(1, 3)) as $j) {
                Item::create([
                    'product_id' => $product->id,
                    'vendor_id' => $faker->numberBetween(1, 100),
                    'reference' => $faker->numberBetween(2000000, 9000000),
                    'price' => $faker->numberBetween(5, 300),
                    'currency_id' => 1
                ]);
            }
        }
    }
}
