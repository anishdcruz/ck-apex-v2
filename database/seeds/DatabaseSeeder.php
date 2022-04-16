<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(ClientsTableSeeder::class);
        $this->call(VendorsTableSeeder::class);
        $this->call(ProductsTableSeeder::class);
        $this->call(QuotationsTableSeeder::class);
        $this->call(SalesOrdersTableSeeder::class);
        $this->call(AdvancePaymentsTableSeeder::class);
        $this->call(InvoicesTableSeeder::class);
        $this->call(ClientPaymentsTableSeeder::class);
        $this->call(ExpensesTableSeeder::class);
        $this->call(PurchaseOrdersTableSeeder::class);
        $this->call(BillsTableSeeder::class);
        $this->call(VendorPaymentsTableSeeder::class);
    }
}
