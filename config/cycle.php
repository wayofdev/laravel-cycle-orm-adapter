<?php

declare(strict_types=1);

use Cycle\Database\Config;

return [
    /*
     * Where ClassLocator should search for entities and embeddings.
     * Important: By default, Laravel's application skeleton has its Model classes in the app/Models folder.
     * With Cycle you'll need to create a dedicated folder for your Entities and point your config/cycle.php
     * paths array to it. If you don't, Cycle will scan your whole app/ folder for files,
     * which will have a huge impact on performance!
     */
    'directories' => [
        app_path(),
    ],

    'databases' => [
        'default' => 'default',

        'databases' => [
            'default' => [
                'connection' => 'sqlite',
            ],
        ],

        /*
         * Configuring connections, see:
         * https://cycle-orm.dev/docs/database-connect/2.x/en
         */
        'connections' => [
            /*
             * Setup sqlite database in-memory for testing purposes
             */
            'sqlite' => new Config\SQLiteDriverConfig(
                connection: new Config\SQLite\MemoryConnectionConfig(),
                queryCache: true
            ),

            'pgsql' => new Config\PostgresDriverConfig(
                connection: new Config\Postgres\TcpConnectionConfig(
                    database: env('DB_NAME', 'wod'),
                    host: env('DB_HOST', '127.0.0.1'),
                    port: (int) env('DB_PORT', 5432),
                    user: env('DB_USER', 'wod'),
                    password: env('DB_PASSWORD')
                ),
                schema: Config\PostgresDriverConfig::DEFAULT_SCHEMA,
                reconnect: true,
                timezone: 'UTC',
                queryCache: true
            ),

            'mysql' => new Config\MySQLDriverConfig(
                connection: new Config\MySQL\TcpConnectionConfig(
                    database: env('DB_NAME', 'wod'),
                    host: env('DB_HOST', '127.0.0.1'),
                    port: (int) env('DB_PORT', 3306),
                    user: env('DB_USER', 'wod'),
                    password: env('DB_PASSWORD')
                ),
                queryCache: true,
            ),

            'sqlserver' => new Config\SQLServerDriverConfig(
                connection: new Config\SQLServer\TcpConnectionConfig(
                    database: env('DB_NAME', 'wod'),
                    host: env('DB_HOST', '127.0.0.1'),
                    port: (int) env('DB_PORT', 1433),
                    user: env('DB_USER', 'wod'),
                    password: env('DB_PASSWORD')
                ),
                queryCache: true,
            ),
        ],
    ],

    'schema' => [
        // ...
    ],
];
