<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Laravel\Providers;

use Cycle\Database\Config\DatabaseConfig;
use Cycle\ORM\EntityManager;
use Cycle\ORM\EntityManagerInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use PHPUnit\Framework\Attributes\Test;
use WayOfDev\Tests\TestCase;

class CycleServiceProviderTest extends TestCase
{
    /**
     * @throws BindingResolutionException
     */
    #[Test]
    public function it_gets_database_config_from_container(): void
    {
        $config = $this->app->make(DatabaseConfig::class);

        self::assertArrayHasKey('default', $config->toArray());
        self::assertArrayHasKey('databases', $config->toArray());
        self::assertArrayHasKey('drivers', $config->toArray());
    }

    /**
     * @throws BindingResolutionException
     */
    #[Test]
    public function it_gets_entity_manager_instance_from_container(): void
    {
        /** @var EntityManager|null $manager */
        $manager = $this->app->make(EntityManagerInterface::class);
        self::assertInstanceOf(EntityManagerInterface::class, $manager);
    }

    #[Test]
    public function it_registers_configurations_correctly(): void
    {
        $this::assertNotNull(config('cycle'));
    }
}
