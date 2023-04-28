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
use WayOfDev\Cycle\Contracts\Config\Repository as Config;

final class RegisterORM
{
    public function __invoke(Container $app): void
    {
        $app->singleton(FactoryInterface::class, static function (Container $app): FactoryInterface {
            $config = $app->make(Config::class);

            $collectionFactoryClass = $config->defaultCollectionFactory();

            $relationConfig = new RelationConfig(
                RelationConfig::getDefault()->toArray() + $config->customRelations()
            );

            return new Factory(
                dbal: $app[DatabaseProviderInterface::class],
                config: $relationConfig,
                defaultCollectionFactory: new $collectionFactoryClass()
            );
        });

        $app->singleton(ORMInterface::class, function (Container $app): ORMInterface {
            return new ORM(
                factory: $app[FactoryInterface::class],
                schema: $app[SchemaInterface::class]
            );
        });

        $app->singleton(EntityManagerInterface::class, function (Container $app): EntityManagerInterface {
            return new EntityManager($app[ORMInterface::class]);
        });
    }
}
