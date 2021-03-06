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
        $this->call(CategriesTableSeeder::class);
        $this->call(OrdersTableSeeder::class);
        $this->call(CupomTableSeeder::class);
        $this->call(OAuthClientSeeder::class);
    }
}