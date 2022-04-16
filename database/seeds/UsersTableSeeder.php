<?php

use Illuminate\Database\Seeder;
use Faker\Factory;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        User::truncate();

        // test admin
        User::create([
            'name' => $faker->name,
            'title' => 'Admin',
            'telephone' => $faker->phoneNumber,
            'extension' => $faker->numberBetween(1000, 2000),
            'mobile_number' => $faker->phoneNumber,
            'email' => 'admin@apex.dev',
            'password' => 'password',
            'email_signature' => 'Best Regards'.PHP_EOL,
            'is_admin' => 1,
            'is_active' => 1
        ]);

        // test staff
        User::create([
            'name' => $faker->name,
            'title' => 'Staff',
            'telephone' => $faker->phoneNumber,
            'extension' => $faker->numberBetween(1000, 2000),
            'mobile_number' => $faker->phoneNumber,
            'email' => 'staff@apex.dev',
            'password' => 'password',
            'email_signature' => 'Best Regards'.PHP_EOL,
            'is_admin' => 0,
            'is_active' => 1
        ]);

        foreach(range(1, 5) as $i) {
            User::create([
                'name' => $faker->name,
                'title' => 'Staff',
                'telephone' => $faker->phoneNumber,
                'extension' => $faker->numberBetween(1000, 2000),
                'mobile_number' => $faker->phoneNumber,
                'email' => $faker->unique()->safeEmail,
                'password' => 'password',
                'email_signature' => 'Best Regards'.PHP_EOL,
                'is_admin' => 0,
                'is_active' => 1
            ]);
        }
    }
}
