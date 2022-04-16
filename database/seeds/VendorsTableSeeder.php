<?php

use Illuminate\Database\Seeder;
use Faker\Factory;
use App\Vendor;

class VendorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        Vendor::truncate();

        foreach(range(1, 100) as $i) {
            $vendor = new Vendor([
                'user_id' => 1,
                'person' => $faker->name,
                'company' => $faker->company,
                'email' => $faker->unique()->safeEmail,
                'work_phone' => $faker->phoneNumber,
                'mobile_number' => $faker->phoneNumber,
                'currency_id' => 1,
                'billing_address' => $faker->address,
                'shipping_address' => $faker->address,
                'payment_details' => $faker->text,
                'total_expense' => 0
            ]);

            $vendor->save();
        }
    }
}
