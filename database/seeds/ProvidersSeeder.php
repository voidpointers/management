<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvidersSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('logistics_providers')->truncate();

        $data = [
            ['title' => '云途', 'en' => 'yuntu', 'code' => 'YT', 'sort' => 1]
        ];

        DB::table('logistics_providers')->insert($data);
    }
}
