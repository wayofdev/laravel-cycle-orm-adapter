<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Laravel\Providers\Registrators;

use Cycle\Database\DatabaseInterface;
use Cycle\Database\DatabaseManager;
use Cycle\Database\DatabaseProviderInterface;
use PHPUnit\Framework\Attributes\Test;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use WayOfDev\Tests\TestCase;

class RegisterDatabaseTest extends TestCase
{
    #[Test]
    public function it_creates_database_provider_interface_singleton(): void
    {
        try {
            $class1 = $this->app->get(DatabaseProviderInterface::class);
            $class2 = $this->app->get(DatabaseProviderInterface::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }

        $this::assertInstanceOf(DatabaseProviderInterface::class, $class1);
        $this::assertInstanceOf(DatabaseManager::class, $class1);

        $this::assertSame($class1, $class2);
    }

    #[Test]
    public function it_binds_database_interface(): void
    {
        try {
            $class = $this->app->get(DatabaseInterface::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }

        $this::assertInstanceOf(DatabaseInterface::class, $class);
    }

    #[Test]
    public function it_aliases_database_provider_interface_to_database_manager(): void
    {
        try {
            $provider = $this->app->get(DatabaseProviderInterface::class);
            $manager = $this->app->get(DatabaseManager::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }

        $this::assertSame($provider, $manager);
    }
}
