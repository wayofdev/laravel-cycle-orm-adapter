<?php

declare(strict_types=1);

namespace WayOfDev\Tests;

use Cycle\Database\Config\SQLite\MemoryConnectionConfig;
use Cycle\Database\Config\SQLiteDriverConfig;
use Cycle\ORM\Collection\ArrayCollectionFactory;
use Cycle\ORM\Collection\IlluminateCollectionFactory;
use Cycle\ORM\SchemaInterface;
use WayOfDev\Cycle\Config;
use WayOfDev\Cycle\Contracts\Config\Repository;
use WayOfDev\Cycle\Exceptions\MissingRequiredAttributes;

final class ConfigTest extends TestCase
{
    public static function dataProviderForConfig(): array
    {
        return [
            'success' => [
                [
                    'tokenizer' => [
                        'directories' => [
                            '/foo/bar',
                            '/boo/bar/baz',
                        ],
                        'exclude' => [],
                        'scopes' => [],
                    ],
                    'database' => [
                        'default' => 'default',
                        'aliases' => [],
                        'databases' => [
                            'default' => [
                                'connection' => 'sqlite',
                            ],
                        ],
                        'connections' => [
                            'sqlite' => new SQLiteDriverConfig(
                                connection: new MemoryConnectionConfig(),
                                queryCache: true
                            ),
                        ],
                    ],
                    'schema' => [
                        'cache' => [
                            'storage' => 'file',
                            'enabled' => true,
                        ],
                        'defaults' => [
                            SchemaInterface::MAPPER => null,
                            SchemaInterface::REPOSITORY => null,
                            SchemaInterface::SOURCE => null,
                        ],
                        'collections' => [
                            'default' => 'illuminate',
                            'factories' => [
                                'array' => ArrayCollectionFactory::class,
                                'illuminate' => IlluminateCollectionFactory::class,
                            ],
                        ],
                        'generators' => [],
                    ],
                    'migrations' => [
                        'directory' => '/app/vendor/orchestra/testbench-core/laravel/database/migrations',
                        'table' => 'migrations',
                        'safe' => true,
                    ],
                    'warmup' => true,
                    'customRelations' => [],
                ],
                false,
            ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider dataProviderForConfig
     */
    public function it_creates_config(array $config, bool $throwMissingException): void
    {
        if ($throwMissingException) {
            $this->expectException(MissingRequiredAttributes::class);
        } else {
            $configToTest = Config::fromArray($config);

            self::assertEquals($config['tokenizer'], $configToTest->tokenizer());
            self::assertEquals($config['database'], $configToTest->database());

            self::assertEquals($config['schema'], $configToTest->schema());
            self::assertEquals($config['schema']['cache'], $configToTest->schemaCache());
            self::assertEquals($config['schema']['defaults'], $configToTest->schemaDefaults());
            self::assertEquals($config['schema']['collections']['factories'], $configToTest->collectionFactories());
            self::assertEquals($config['schema']['generators'], $configToTest->schemaGenerators());

            self::assertEquals($config['migrations']['directory'], $configToTest->migrationsDirectory());
            self::assertEquals($config['migrations']['table'], $configToTest->migrationsTable());
            self::assertEquals($config['migrations']['safe'], $configToTest->safeToMigrate());

            self::assertEquals($config['customRelations'], $configToTest->customRelations());
        }
    }

    /**
     * @test
     */
    public function it_gets_default_collection_class_fqdn(): void
    {
        /** @var Config $config */
        $config = app(Repository::class);

        self::assertEquals(IlluminateCollectionFactory::class, $config->defaultCollectionFactory());
    }
}
