<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Tests\Bridge\Laravel\Providers;

use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\DatabaseManager;
use Cycle\Database\DatabaseProviderInterface;
use Spiral\Tokenizer\ClassesInterface;
use Spiral\Tokenizer\ClassLocator;
use Spiral\Tokenizer\Tokenizer;
use WayOfDev\Cycle\Contracts\Config\Repository as ConfigRepository;
use WayOfDev\Cycle\Contracts\EntityManager;
use WayOfDev\Cycle\Tests\TestCase;

class CycleServiceProviderTest extends TestCase
{
    /**
     * @test
     */
    public function it_gets_adapter_config_instance_from_container(): void
    {
        $config = $this->app->make(ConfigRepository::class);

        self::assertInstanceOf(ConfigRepository::class, $config);
        self::assertEquals(app_path(), $config->tokenizer()['directories'][0]);
    }

    /**
     * @test
     */
    public function it_gets_database_config_from_container(): void
    {
        /** @var DatabaseConfig $config */
        $config = $this->app->make(DatabaseConfig::class);

        self::assertEquals(config('cycle.database'), $config->toArray());
    }

    /**
     * @test
     */
    public function it_gets_database_manager_instance_from_container(): void
    {
        $manager = $this->app->make(DatabaseProviderInterface::class);
        self::assertInstanceOf(DatabaseProviderInterface::class, $manager);

        $manager = $this->app->make(DatabaseManager::class);
        self::assertInstanceOf(DatabaseManager::class, $manager);
    }

    public function it_gets_entity_manager_instance_from_container(): void
    {
        $manager = $this->app->make(EntityManager::class);
        self::assertInstanceOf(EntityManager::class, $manager);
    }

    public function it_gets_database_schema_from_container(): void
    {
        // ...
    }

    public function it_gets_orm_from_container(): void
    {
        // ...
    }

    /**
     * @test
     */
    public function it_gets_tokenizer_from_container(): void
    {
        $tokenizer = $this->app->make(Tokenizer::class);
        self::assertInstanceOf(Tokenizer::class, $tokenizer);
    }

    public function it_gets_class_locator_instance_from_container(): void
    {
        $classLocator = $this->app->make(ClassesInterface::class);
        self::assertInstanceOf(ClassesInterface::class, $classLocator);

        $classLocator = $this->app->make(ClassLocator::class);
        self::assertInstanceOf(ClassesInterface::class, $classLocator);
    }
}
