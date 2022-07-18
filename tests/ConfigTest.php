<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Tests;

use Cycle\ORM\SchemaInterface;
use WayOfDev\Cycle\Config;
use WayOfDev\Cycle\Exceptions\MissingRequiredAttributes;

final class ConfigTest extends TestCase
{
    public function dataProviderForConfig(): array
    {
        return [
            'success' => [
                [
                    'directories' => [
                        '/foo/bar',
                        '/boo/bar/baz',
                    ],
                    'databases' => [],
                    'schema' => [
                        'sync' => false,
                        'cache' => [
                            'storage' => 'file',
                            'enabled' => true,
                        ],
                        'defaults' => [
                            SchemaInterface::MAPPER => null,
                            SchemaInterface::REPOSITORY => null,
                            SchemaInterface::SOURCE => null,
                        ],
                    ],
                    'migrations' => [
                        'directory' => '/app/vendor/orchestra/testbench-core/laravel/database/migrations',
                        'table' => 'migrations',
                    ],
                    'relations' => [],
                ],
                false,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider dataProviderForConfig
     */
    public function it_creates_config(array $config, bool $throwMissingException): void
    {
        if ($throwMissingException) {
            $this->expectException(MissingRequiredAttributes::class);
        } else {
            $configToTest = Config::fromArray($config);

            self::assertEquals($config['directories'], $configToTest->directories());
            self::assertEquals($config['migrations']['directory'], $configToTest->migrationsDirectory());
            self::assertEquals($config['migrations']['table'], $configToTest->migrationsTable());
        }
    }
}
