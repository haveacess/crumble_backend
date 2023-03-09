<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarketsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('markets')->insert([
            ['id' => 1, 'name' => 'Steam'],
            ['id' => 2, 'name' => 'Bitskins'],
            ['id' => 3, 'name' => 'Waxpeer']
        ]);
    }
}
