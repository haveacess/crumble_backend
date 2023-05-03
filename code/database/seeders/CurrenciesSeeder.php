<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrenciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * based on https://partner.steamgames.com/doc/store/pricing/currencies
     */
    public function run(): void
    {
        DB::table('currencies')->insert([
            ['id' => 1, 'name' => 'USD', 'rate' => 1, 'suffix' => 'USD'],
            ['id' => 3, 'name' => 'EUR', 'rate' => null, 'suffix' => '€'],
            ['id' => 5, 'name' => 'RUB', 'rate' => null, 'suffix' => 'pуб.'],
            ['id' => 17, 'name' => 'TRY', 'rate' => null, 'suffix' => 'TL'],
        ]);
    }
}
