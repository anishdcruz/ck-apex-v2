<?php

use Illuminate\Database\Seeder;
use App\Invoice\Invoice;
use App\Invoice\Item;
use App\Invoice\Tax;
use App\Product\Product;
use Faker\Factory;

class InvoicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        Invoice::truncate();
        Item::truncate();

        foreach(range(1, 25) as $i) {
            $invoice = Invoice::create([
                'user_id' => 1,
                'client_id' => $faker->numberBetween(1, 50),
                'number' => 'IN-'.$faker->unique()->numberBetween(100000, 900000),
                'reference' => mt_rand(0, 1) ? 'Q-'.$faker->numberBetween(1000, 9000) : null,
                'currency_id' => 1,
                'date' => $faker->date,
                'due_date' => $faker->date,
                'sub_total' => $faker->numberBetween(1000, 9000),
                'total' => $faker->numberBetween(10000, 90000),
                'terms' => $faker->text,
                'amount_paid' => 0,
                'status_id' => $faker->numberBetween(1, 5),
                'invoiceable_id' => $faker->numberBetween(1, 25),
                'invoiceable_type' => $faker->randomElement(['App\Quotation\Quotation', 'App\SalesOrder\SalesOrder'])
            ]);

            foreach(Product::inRandomOrder()->limit(mt_rand(1, 4))->get() as $j) {
                $item = Item::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $j->id,
                    'qty' => $faker->numberBetween(1, 12),
                    'unit_price' => $j->unit_price,
                ]);

                foreach($j->taxes as $tax) {
                    Tax::create([
                        'invoice_item_id' => $item->id,
                        'name' => $tax->name,
                        'rate' => $tax->rate,
                        'tax_authority' => $tax->tax_authority
                    ]);
                }
            }
        }
    }
}
