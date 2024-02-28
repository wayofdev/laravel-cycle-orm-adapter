<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Laravel\Providers\Registrators;

use Cycle\ORM\Entity\Behavior\EventDrivenCommandGenerator;
use Cycle\ORM\Factory;
use Cycle\ORM\FactoryInterface;
use Cycle\ORM\ORM;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Transaction\CommandGenerator;
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

    /**
     * @test
     */
    public function it_registers_entity_behavior_by_default(): void
    {
        try {
            $class = $this->app->get(ORMInterface::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }

        $this::assertInstanceOf(EventDrivenCommandGenerator::class, $class->getCommandGenerator());
    }

    /**
     * @test
     */
    public function it_disables_entity_behavior_by_default(): void
    {
        config()->set('cycle.entityBehavior.register', false);

        try {
            /** @var ORM $class */
            $class = $this->app->get(ORMInterface::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }

        $this::assertInstanceOf(CommandGenerator::class, $class->getCommandGenerator());
    }
}
