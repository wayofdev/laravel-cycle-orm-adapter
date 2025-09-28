<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Laravel\Providers\Registrators;

use Cycle\ORM\Entity\Behavior\EventDrivenCommandGenerator;
use Cycle\ORM\Factory;
use Cycle\ORM\FactoryInterface;
use Cycle\ORM\ORM;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Transaction\CommandGenerator;
use Cycle\ORM\Transaction\CommandGeneratorInterface;
use PHPUnit\Framework\Attributes\Test;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use WayOfDev\Tests\TestCase;

use function spl_object_hash;

class RegisterORMTest extends TestCase
{
    #[Test]
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

    #[Test]
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
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    #[Test]
    public function it_registers_entity_behavior_by_default(): void
    {
        try {
            $class = $this->app->get(ORMInterface::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }

        $this::assertInstanceOf(EventDrivenCommandGenerator::class, $class->getCommandGenerator());

        $this::assertInstanceOf(
            EventDrivenCommandGenerator::class,
            $this->app->get(CommandGeneratorInterface::class)
        );
    }

    #[Test]
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

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Test]
    public function it_returns_same_instance_for_concrete_class_and_interface(): void
    {
        $ormInterface = $this->app->get(ORMInterface::class);
        $ormConcrete = $this->app->get(ORM::class);

        $this::assertSame(
            spl_object_hash($ormInterface),
            spl_object_hash($ormConcrete),
            'ORM::class and ORMInterface::class should return the same instance to prevent entity manager conflicts'
        );

        $this::assertSame($ormInterface, $ormConcrete);
    }
}
