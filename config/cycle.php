<?php

declare(strict_types=1);

use Cycle\Annotated;
use Cycle\Database\Config;
use Cycle\Database\Driver;
use Cycle\ORM\Collection;
use Cycle\ORM\SchemaInterface;
use Cycle\Schema;
use WayOfDev\Cycle\Contracts\GeneratorLoader;

return [
    /*
     * Where ClassLocator should search for entities and embeddings.
     * Important: By default, Laravel application skeleton has its Model classes in the app/Models folder.
     * With Cycle you'll need to create a dedicated folder for your Entities and point your config/cycle.php
     * paths array to it. If you don't, Cycle will scan your whole app/ folder for files,
     * which will have a huge impact on performance!
     */
    'tokenizer' => [
        /*
         * Where should class locator scan for entities?
         */
        'directories' => [
            app_path(),
        ],

        /*
         * Directories, to exclude from Entity search
         */
        'exclude' => [
            'Console',
            'Exceptions',
            'Http',
            'Providers',
        ],

        /*
         * Scopes to use when searching for entities
         */
        'scopes' => [],

        /*
         * Should class locator scan for entities in debug mode?
         */
        'debug' => env('APP_DEBUG', false),

        /*
         * Should class locator cache the results?
         */
        'cache' => [
            'enabled' => env('CYCLE_TOKENIZER_CACHE_TARGETS', true),
            'directory' => storage_path('framework/cache/cycle/tokenizer'),
        ],

        /*
         * What kind of classes should be loaded?
         */
        'load' => [
            'classes' => env('CYCLE_TOKENIZER_LOAD_CLASSES', true),
            'enums' => env('CYCLE_TOKENIZER_LOAD_ENUMS', false),
            'interfaces' => env('CYCLE_TOKENIZER_LOAD_INTERFACES', false),
        ],
    ],

    'attributes' => [
        'cache' => [
            'enabled' => env('CYCLE_ATTRIBUTES_CACHE', true),
            'store' => env('CYCLE_ATTRIBUTES_CACHE_DRIVER', 'file'),
        ],
    ],

    'database' => [
        /*
         * Default database connection
         */
        'default' => env('DB_DEFAULT_CONNECTION', 'default'),

        /*
         * The Cycle/Database module provides support to manage multiple databases
         * in one application, use read/write connections and logically separate
         * multiple databases within one connection using prefixes.
         *
         * To register a new database simply add a new one into
         * "databases" section below.
         */
        'databases' => [
            'default' => [
                'driver' => env('DB_CONNECTION', 'memory'),
            ],
        ],

        /*
         * Configuring connections, see:
         * https://cycle-orm.dev/docs/database-connect/2.x/en
         *
         * Each database instance must have an associated connection object.
         * Connections used to provide low-level functionality and wrap different
         * database drivers. To register a new connection you have to specify
         * the driver class and its connection options.
         */
        'drivers' => [
            /*
             * Setup sqlite database in-memory for testing purposes
             */
            'memory' => new Config\SQLiteDriverConfig(
                connection: new Config\SQLite\MemoryConnectionConfig(),
                queryCache: true
            ),

            'sqlite' => new Config\SQLiteDriverConfig(
                connection: new Config\SQLite\FileConnectionConfig(
                    database: database_path(env('DB_DATABASE', 'database')) . '.sqlite'
                ),
                driver: Driver\SQLite\SQLiteDriver::class,
                reconnect: true,
                timezone: 'UTC',
                queryCache: true,
                readonlySchema: false,
                readonly: false,
                options: [
                    'logInterpolatedQueries' => env('DB_USE_TELESCOPE_LOGGER', false),
                    'logQueryParameters' => env('DB_USE_TELESCOPE_LOGGER', false),
                ],
            ),

            'pgsql' => new Config\PostgresDriverConfig(
                connection: new Config\Postgres\TcpConnectionConfig(
                    database: env('DB_DATABASE', 'default'),
                    host: env('DB_HOST', '127.0.0.1'),
                    port: env('DB_PORT', 5432),
                    user: env('DB_USERNAME', 'cycle'),
                    password: env('DB_PASSWORD', '')
                ),
                schema: Config\PostgresDriverConfig::DEFAULT_SCHEMA,
                driver: Driver\Postgres\PostgresDriver::class,
                reconnect: true,
                timezone: 'UTC',
                queryCache: true,
                readonlySchema: false,
                readonly: false,
                options: [
                    'logInterpolatedQueries' => env('DB_USE_TELESCOPE_LOGGER', false),
                    'logQueryParameters' => env('DB_USE_TELESCOPE_LOGGER', false),
                ],
            ),

            'mysql' => new Config\MySQLDriverConfig(
                connection: new Config\MySQL\TcpConnectionConfig(
                    database: env('DB_DATABASE', 'default'),
                    host: env('DB_HOST', '127.0.0.1'),
                    port: env('DB_PORT', 3306),
                    user: env('DB_USERNAME', 'cycle'),
                    password: env('DB_PASSWORD', '')
                ),
                driver: Driver\MySQL\MySQLDriver::class,
                reconnect: true,
                timezone: 'UTC',
                queryCache: true,
                readonlySchema: false,
                readonly: false,
                options: [
                    'logInterpolatedQueries' => env('DB_USE_TELESCOPE_LOGGER', false),
                    'logQueryParameters' => env('DB_USE_TELESCOPE_LOGGER', false),
                ],
            ),

            'sqlserver' => new Config\SQLServerDriverConfig(
                connection: new Config\SQLServer\TcpConnectionConfig(
                    database: env('DB_DATABASE', 'tempdb'),
                    host: env('DB_HOST', '127.0.0.1'),
                    port: env('DB_PORT', 1433),
                    trustServerCertificate: true,
                    user: env('DB_USERNAME', 'SA'),
                    password: env('DB_PASSWORD', '')
                ),
                driver: Driver\SQLServer\SQLServerDriver::class,
                reconnect: true,
                timezone: 'UTC',
                queryCache: true,
                readonlySchema: false,
                readonly: false,
                options: [
                    'logInterpolatedQueries' => env('DB_USE_TELESCOPE_LOGGER', false),
                    'logQueryParameters' => env('DB_USE_TELESCOPE_LOGGER', false),
                ],
            ),
        ],

        /*
         * Default logger configuration
         *
         * Use any of channels configured in your logging.php file
         */
        'logger' => [
            /*
             * If enabled, allows laravel/telescope to catch all queries
             */
            'use_telescope' => env('DB_USE_TELESCOPE_LOGGER', false),

            /*
             * Default logger channel to use with all databases
             * Set to null to disable logging
             * Use any of channels configured in your logging.php file
             */
            'default' => env('DB_DEFAULT_LOGGER', null),

            /*
             * Logger channels for specific drivers
             * Set to null to disable logging
             * Use any of channels configured in your logging.php file
             */
            'drivers' => [
                'sqlite' => env('DB_DEFAULT_LOGGER', null),
                'pgsql' => env('DB_DEFAULT_LOGGER', null),
                'mysql' => env('DB_DEFAULT_LOGGER', null),
                'sqlserver' => env('DB_DEFAULT_LOGGER', null),
            ],
        ],
    ],

    'schema' => [
        /*
         * true (Default) - Schema will be stored in a cache after compilation.
         * It won't be changed after entity modification. Use `php app.php cycle` to update schema.
         *
         * false - Schema won't be stored in a cache after compilation.
         * It will be automatically changed after entity modification. (Development mode)
         */
        'cache' => [
            'enabled' => env('CYCLE_SCHEMA_CACHE', true),
            'store' => env('CYCLE_SCHEMA_CACHE_DRIVER', 'file'),
        ],

        /*
         * The CycleORM provides the ability to manage default settings for
         * every schema with not defined segments
         */
        'defaults' => [
            SchemaInterface::MAPPER => Cycle\ORM\Mapper\Mapper::class,
            SchemaInterface::REPOSITORY => Cycle\ORM\Select\Repository::class,
            SchemaInterface::SCOPE => null,
            SchemaInterface::TYPECAST_HANDLER => null,
        ],

        'collections' => [
            'default' => env('DB_DEFAULT_COLLECTION', 'illuminate'),
            'factories' => [
                'array' => Collection\ArrayCollectionFactory::class,
                'illuminate' => Collection\IlluminateCollectionFactory::class,
                'doctrine' => Collection\DoctrineCollectionFactory::class,
            ],
        ],

        /*
         * Schema generators (Optional)
         * null (default) - Will be used schema generators defined in bootloaders
         */
        'generators' => [
            GeneratorLoader::GROUP_INDEX => [
                // Register embeddable entities
                Annotated\Embeddings::class,
                // Register annotated entities
                Annotated\Entities::class,
                // Register STI/JTI
                Annotated\TableInheritance::class,
                // Add @Table column declarations
                Annotated\MergeColumns::class,
            ],
            GeneratorLoader::GROUP_RENDER => [
                // Re-declared table schemas (remove columns)
                Schema\Generator\ResetTables::class,
                // Generate entity relations
                Schema\Generator\GenerateRelations::class,
                // Generate changes from schema modifiers
                Schema\Generator\GenerateModifiers::class,
                // Make sure all entity schemas are correct
                Schema\Generator\ValidateEntities::class,
                // Declare table schemas
                Schema\Generator\RenderTables::class,
                // Declare relation keys and indexes
                Schema\Generator\RenderRelations::class,
                // Render all schema modifiers
                Schema\Generator\RenderModifiers::class,
                // Add @Table column declarations
                Annotated\MergeIndexes::class,
                // Add foreign keys
                Schema\Generator\ForeignKeys::class,
            ],
            GeneratorLoader::GROUP_POSTPROCESS => [
                // Typecast non string columns
                Schema\Generator\GenerateTypecast::class,
            ],
        ],
    ],

    'migrations' => [
        'directory' => database_path('migrations/cycle'),
        'strategy' => Schema\Generator\Migrations\Strategy\SingleFileStrategy::class,
        'nameGenerator' => Schema\Generator\Migrations\NameBasedOnChangesGenerator::class,
        'table' => env('DB_MIGRATIONS_TABLE', 'cycle_migrations'),
        'safe' => env('APP_ENV') !== 'production',
    ],

    /*
     * Enable schema cache warmup
     */
    'warmup' => env('CYCLE_SCHEMA_WARMUP', false),

    /*
     * Custom relation types for entities
     */
    'customRelations' => [
        // \Cycle\ORM\Relation::EMBEDDED => [
        //     \Cycle\ORM\Config\RelationConfig::LOADER => \Cycle\ORM\Select\Loader\EmbeddedLoader::class,
        //     \Cycle\ORM\Config\RelationConfig::RELATION => \Cycle\ORM\Relation\Embedded::class,
        // ],
    ],

    /*
     * Enables support of SoftDelete and other features from cycle/entity-behavior package
     * @see https://github.com/cycle/entity-behavior
     */
    'entityBehavior' => [
        'register' => env('CYCLE_REGISTER_ENTITY_BEHAVIOUR', true),
    ],

    'integrations' => [
        /*
         * Enables migration generation for Laravel Queues
         */
        'queue' => [
            'enabled' => env('CYCLE_ADAPTER_QUEUE_INTEGRATION', true),
        ],
        /*
         * Enables migration generation for Laravel Sessions
         */
        'session' => [
            'enabled' => env('CYCLE_ADAPTER_SESSION_INTEGRATION', true),
        ],
        /*
         * Enables migration generation for Laravel Cache
         */
        'cache' => [
            'enabled' => env('CYCLE_ADAPTER_CACHE_INTEGRATION', true),
        ],
    ],
];
