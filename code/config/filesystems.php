<?php

return [
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('.'),
            'throw' => true,
        ],
        'local_raw_query' => [
            'driver' => 'local',
            'root' => app_path('models/Raw'),
            'throw' => true
        ],
        'local_temp_migrations' => [
            'driver' => 'local',
            'root' => base_path('database/migrations/temp'),
            'throw' => true
        ]
    ],

];
