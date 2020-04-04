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
        $this->call('CustomersSeeder');
        $this->call('UsersSeeder');
        $this->call('ShopsSeeder');
        $this->call('CountriesSeeder');
        $this->call('ProvidersSeeder');
        $this->call('ChannelsSeeder');
    }
}
