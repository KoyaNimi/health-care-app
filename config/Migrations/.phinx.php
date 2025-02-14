<?php
return
    [
        'environments' => [
            'default_migration_table' => 'phinxlog',
            'default_environment' => 'development',
            'test' => [
                'adapter' => 'mysql',
                'host' => 'localhost',
                'name' => 'health_care_test',
                'user' => 'root',
                'pass' => 'pass',
                'port' => '3306',
                'charset' => 'utf8mb4',
            ],
        ],
    ];
