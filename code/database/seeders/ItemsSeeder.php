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
        DB::table('items')->insert([
            [
                'id_app' => 730, 'id_class' => 5224946682, 'id_instance' => 480085569,
                'market_hash_name' => 'Glock-18 | Night (Field-Tested)'
            ],
            [
                'id_app' => 730, 'id_class' => 5152572261, 'id_instance' => 188530139,
                'market_hash_name' => 'AK-47 | Slate (Field-Tested)'
            ],
            [
                'id_app' => 730, 'id_class' => 5240627398, 'id_instance' => 480085569,
                'market_hash_name' => 'AK-47 | Asiimov (Field-Tested)'
            ],
            [
                'id_app' => 730, 'id_class' => 4113017952, 'id_instance' => 188530139,
                'market_hash_name' => 'AWP | Graphite (Factory New)'
            ]
        ]);
    }
}
