<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Laravel\Providers\Registrators;

use Cycle\Database\Config\DatabaseConfig;
use Cycle\Migrations\Config\MigrationConfig;
use Cycle\ORM\Collection\IlluminateCollectionFactory;
use PHPUnit\Framework\Attributes\Test;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use WayOfDev\Cycle\Schema\Config\SchemaConfig;
use WayOfDev\Tests\TestCase;

class RegisterConfigsTest extends TestCase
{
    #[Test]
    public function it_registers_database_config(): void
    {
        try {
            $config = $this->app->get(DatabaseConfig::class);

            $this::assertInstanceOf(
                DatabaseConfig::class,
                $config
            );
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }

        self::assertArrayHasKey('default', $config->toArray());
        self::assertArrayHasKey('databases', $config->toArray());
        self::assertArrayHasKey('drivers', $config->toArray());
    }

    #[Test]
    public function it_registers_migration_config(): void
    {
        try {
            $config = $this->app->get(MigrationConfig::class);

            $this::assertInstanceOf(
                MigrationConfig::class,
                $config
            );
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }

        self::assertArrayHasKey('directory', $config->toArray());
        self::assertArrayHasKey('table', $config->toArray());
        self::assertArrayHasKey('safe', $config->toArray());
    }

    #[Test]
    public function it_registers_tokenizer_config(): void
    {
        try {
            $config = $this->app->get(TokenizerConfig::class);

            $this::assertInstanceOf(
                TokenizerConfig::class,
                $config
            );
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }

        self::assertArrayHasKey('directories', $config->toArray());
        self::assertArrayHasKey('exclude', $config->toArray());
        self::assertArrayHasKey('scopes', $config->toArray());
        self::assertArrayHasKey('debug', $config->toArray());
        self::assertArrayHasKey('cache', $config->toArray());
    }

    #[Test]
    public function it_registers_schema_config(): void
    {
        try {
            $config = $this->app->get(SchemaConfig::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }

        try {
            $generators = config()->get('cycle.schema.generators');
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }

        $this::assertInstanceOf(SchemaConfig::class, $config);
        $this::assertEquals($generators, $config->generators());
        $this::assertEquals(IlluminateCollectionFactory::class, $config->defaultCollectionFQCN());
    }
}
