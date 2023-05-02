<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Laravel\Providers\Registrators;

use Cycle\Migrations\Migrator;
use Cycle\Migrations\RepositoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use WayOfDev\Tests\TestCase;

class RegisterMigrationsTest extends TestCase
{
    /**
     * @test
     */
    public function it_registers_repository_interface_as_singleton(): void
    {
        try {
            $class1 = $this->app->get(RepositoryInterface::class);
            $class2 = $this->app->get(RepositoryInterface::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }

        $this::assertInstanceOf(RepositoryInterface::class, $class1);

        $this::assertSame($class1, $class2);
    }

    /**
     * @test
     */
    public function it_registers_migrator_as_singleton(): void
    {
        try {
            $class1 = $this->app->get(Migrator::class);
            $class2 = $this->app->get(Migrator::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }

        $this::assertInstanceOf(Migrator::class, $class1);

        $this::assertSame($class1, $class2);
    }
}
