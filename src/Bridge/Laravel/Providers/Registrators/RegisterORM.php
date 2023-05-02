<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Providers\Registrators;

use Cycle\Database\DatabaseProviderInterface;
use Cycle\ORM\Config\RelationConfig;
use Cycle\ORM\EntityManager;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\Factory;
use Cycle\ORM\FactoryInterface;
use Cycle\ORM\ORM;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\SchemaInterface;
use Illuminate\Container\Container;
use WayOfDev\Cycle\Schema\Config\SchemaConfig;

/**
 * @see https://github.com/spiral/cycle-bridge/blob/2.0/src/Bootloader/CycleOrmBootloader.php
 */
final class RegisterORM
{
    public function __invoke(Container $app): void
    {
        $app->singleton(FactoryInterface::class, static function (Container $app): FactoryInterface {
            /** @var SchemaConfig $config */
            $config = $app->get(SchemaConfig::class);
            $factoryFQCN = $config->defaultCollectionFQCN();
            $factory = $app->make($factoryFQCN);

            return new Factory(
                dbal: $app->get(DatabaseProviderInterface::class),
                config: $app->get(RelationConfig::class),
                defaultCollectionFactory: $factory
            );
        });

        $app->singleton(ORMInterface::class, function (Container $app): ORMInterface {
            return new ORM(
                factory: $app->get(FactoryInterface::class),
                schema: $app->get(SchemaInterface::class)
            );
        });

        $app->singleton(EntityManagerInterface::class, function (Container $app): EntityManagerInterface {
            return new EntityManager(
                $app->get(ORMInterface::class)
            );
        });
    }
}
