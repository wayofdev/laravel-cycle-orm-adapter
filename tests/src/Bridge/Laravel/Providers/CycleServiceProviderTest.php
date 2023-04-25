<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Laravel\Providers;

use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\DatabaseInterface;
use Cycle\Database\DatabaseManager;
use Cycle\Database\DatabaseProviderInterface;
use Cycle\Migrations\Config\MigrationConfig;
use Cycle\Migrations\Migrator;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\ORM;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spiral\Tokenizer\ClassesInterface;
use Spiral\Tokenizer\ClassLocator;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;
use WayOfDev\Cycle\Contracts\Config\Repository as ConfigRepository;
use WayOfDev\Tests\TestCase;

class CycleServiceProviderTest extends TestCase
{
    /**
     * @test
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function it_gets_adapter_config_instance_from_container(): void
    {
        $config = $this->app->get(ConfigRepository::class);

        self::assertInstanceOf(ConfigRepository::class, $config);
        self::assertEquals(app_path(), $config->tokenizer()['directories'][0]);
    }

    /**
     * @test
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function it_gets_database_config_from_container(): void
    {
        /** @var DatabaseConfig $config */
        $config = $this->app->get(DatabaseConfig::class);

        self::assertArrayHasKey('default', $config->toArray());
        self::assertArrayHasKey('databases', $config->toArray());
        self::assertArrayHasKey('drivers', $config->toArray());
        self::assertArrayHasKey('drivers', $config->toArray());
    }

    /**
     * @test
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function it_gets_database_manager_instance_from_container(): void
    {
        $manager1 = $this->app->get(DatabaseProviderInterface::class);
        self::assertInstanceOf(DatabaseProviderInterface::class, $manager1);

        $manager2 = $this->app->get(DatabaseManager::class);
        self::assertInstanceOf(DatabaseManager::class, $manager2);

        $database = $this->app->get(DatabaseInterface::class);
        self::assertInstanceOf(DatabaseInterface::class, $database);

        self::assertEquals($manager1, $manager2);
        self::assertEquals($manager2->database(), $database);
    }

    /**
     * @test
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function it_gets_entity_manager_instance_from_container(): void
    {
        $manager = $this->app->get(EntityManagerInterface::class);
        self::assertInstanceOf(EntityManagerInterface::class, $manager);
    }

    /**
     * @test
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function it_gets_database_schema_from_container(): void
    {
        $this::assertInstanceOf(SchemaInterface::class, $this->app->get(SchemaInterface::class));
        $this::assertInstanceOf(Schema::class, $this->app->get(SchemaInterface::class));
    }

    /**
     * @test
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function it_gets_orm_from_container(): void
    {
        $this::assertInstanceOf(ORMInterface::class, $this->app->get(ORMInterface::class));
        $this::assertInstanceOf(ORM::class, $this->app->get(ORMInterface::class));
    }

    /**
     * @test
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function it_gets_migrator_instance_from_container(): void
    {
        /** @var MigrationConfig $config */
        $config = $this->app->get(MigrationConfig::class);

        self::assertEquals(config('cycle.migrations'), $config->toArray());

        $migrator = $this->app->get(Migrator::class);
        self::assertInstanceOf(Migrator::class, $migrator);
    }

    /**
     * @test
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function it_gets_tokenizer_from_container(): void
    {
        $this::assertInstanceOf(TokenizerConfig::class, $this->app->get(TokenizerConfig::class));

        $tokenizer = $this->app->get(Tokenizer::class);
        self::assertInstanceOf(Tokenizer::class, $tokenizer);
    }

    /**
     * @test
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function it_gets_class_locator_instance_from_container(): void
    {
        $classLocator = $this->app->get(ClassesInterface::class);
        self::assertInstanceOf(ClassesInterface::class, $classLocator);

        $classLocator = $this->app->get(ClassLocator::class);
        self::assertInstanceOf(ClassesInterface::class, $classLocator);
    }
}
