<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Providers\Registrators;

use Cycle\Database\DatabaseProviderInterface;
use Cycle\ORM\Config\RelationConfig;
use Cycle\ORM\Entity\Behavior\EventDrivenCommandGenerator;
use Cycle\ORM\EntityManager;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\Factory;
use Cycle\ORM\FactoryInterface;
use Cycle\ORM\ORM;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Transaction\CommandGeneratorInterface;
use Illuminate\Contracts\Foundation\Application;
use WayOfDev\Cycle\Bridge\Laravel\Factories\SpiralFactory;
use WayOfDev\Cycle\Schema\Config\SchemaConfig;

/**
 * @see https://github.com/spiral/cycle-bridge/blob/2.0/src/Bootloader/CycleOrmBootloader.php
 */
final class RegisterORM
{
    public function __invoke(Application $app): void
    {
        $app->singleton(FactoryInterface::class, static function (Application $app): FactoryInterface {
            $config = $app->get(SchemaConfig::class);
            $factoryFQCN = $config->defaultCollectionFQCN();
            $factory = $app->make($factoryFQCN);

            return new Factory(
                dbal: $app->get(DatabaseProviderInterface::class),
                config: $app->get(RelationConfig::class),
                factory: $app->get(SpiralFactory::class),
                defaultCollectionFactory: $factory
            );
        });

        if ($this->shouldLoadEntityBehavior() === true) {
            $app->bind(CommandGeneratorInterface::class, function (Application $app): CommandGeneratorInterface {
                return new EventDrivenCommandGenerator($app->get(SchemaInterface::class), $app);
            });
        }

        $app->singleton(ORMInterface::class, function (Application $app): ORMInterface {
            $commandGenerator = $this->shouldLoadEntityBehavior()
                ? $app->get(CommandGeneratorInterface::class)
                : null;

            return new ORM(
                factory: $app->get(FactoryInterface::class),
                schema: $app->get(SchemaInterface::class),
                commandGenerator: $commandGenerator
            );
        });

        $app->singleton(EntityManagerInterface::class, function (Application $app): EntityManagerInterface {
            return new EntityManager(
                $app->get(ORMInterface::class)
            );
        });

        // Fix #752: Alias ORM::class to ORMInterface::class singleton (@mm-sol)
        $app->alias(ORMInterface::class, ORM::class);
    }

    private function shouldLoadEntityBehavior(): bool
    {
        return config('cycle.entityBehavior.register', true);
    }
}
