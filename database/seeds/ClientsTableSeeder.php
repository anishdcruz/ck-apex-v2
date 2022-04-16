<?php

use Illuminate\Database\Seeder;
use Faker\Factory;
use App\Client;

class ClientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        Client::truncate();

        foreach(range(1, 100) as $i) {
            $client = new Client([
                'user_id' => 1,
                'person' => $faker->name,
                'company' => $faker->company,
                'email' => $faker->unique()->safeEmail,
                'work_phone' => $faker->phoneNumber,
                'mobile_number' => $faker->phoneNumber,
                'currency_id' => 1,
                'billing_address' => $faker->address,
                'shipping_address' => $faker->address,
                'total_revenue' => 0
            ]);

            $client->save();
        }
    }
}
