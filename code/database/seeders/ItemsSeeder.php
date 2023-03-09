<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csgoItems = Arr::map([
            'AK-47 | Slate (Field-Tested)',
            'AWP | Graphite (Factory New)',
            'AK-47 | Asiimov (Field-Tested)',
            'AK-47 | Ice Coaled (Field-Tested)'
        ], function ($marketHashName) {
            return ['id_app' => 730, 'market_hash_name' => $marketHashName];
        });

        DB::table('items')->insert($csgoItems);
    }
}
