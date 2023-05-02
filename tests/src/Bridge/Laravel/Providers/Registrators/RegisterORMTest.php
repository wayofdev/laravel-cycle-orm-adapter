<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Laravel\Providers\Registrators;

use Cycle\ORM\Factory;
use Cycle\ORM\FactoryInterface;
use Cycle\ORM\ORM;
use Cycle\ORM\ORMInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use WayOfDev\Tests\TestCase;

class RegisterORMTest extends TestCase
{
    /**
     * @test
     */
    public function it_registers_factory_interface_as_singleton(): void
    {
        try {
            $class = $this->app->get(FactoryInterface::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }

        $this::assertInstanceOf(FactoryInterface::class, $class);
        $this::assertInstanceOf(Factory::class, $class);
    }

    /**
     * @test
     */
    public function it_registers_orm_as_singleton(): void
    {
        try {
            $class1 = $this->app->get(ORMInterface::class);
            $class2 = $this->app->get(ORMInterface::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }

        $this::assertInstanceOf(ORMInterface::class, $class1);
        $this::assertInstanceOf(ORM::class, $class1);

        $this::assertSame($class1, $class2);
    }
}
