<?php

use Illuminate\Database\Seeder;
use Faker\Factory;
use App\Expense;

class ExpensesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        Expense::truncate();

        foreach(range(1, 25) as $i) {
            $quotation = Expense::create([
                'user_id' => 1,
                'vendor_id' => $faker->numberBetween(1, 50),
                'number' => 'E-'.$faker->unique()->numberBetween(1000000, 9000000),
                'currency_id' => 1,
                'payment_date' => '2017-8-'.mt_rand(1, 29),
                'amount_paid' => $faker->numberBetween(1000, 9000),
                'description' => $faker->text,
                'document' => 'ex-1.jpg'
            ]);
        }
    }
}
