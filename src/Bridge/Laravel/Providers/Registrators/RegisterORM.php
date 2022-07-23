<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Providers\Registrators;

use Cycle\Database\DatabaseProviderInterface;
use Cycle\ORM\Factory;
use Cycle\ORM\FactoryInterface;
use Cycle\ORM\ORM;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\SchemaInterface;
use Illuminate\Contracts\Config\Repository as IlluminateConfig;
use Illuminate\Contracts\Container\Container;
use WayOfDev\Cycle\Bridge\Laravel\Providers\Registrator;
use WayOfDev\Cycle\Collection\CollectionConfig;

final class RegisterORM
{
    public function __invoke(Container $app): void
    {
        $app->singleton(CollectionConfig::class, static function (Container $app): CollectionConfig {
            /** @var IlluminateConfig $config */
            $config = $app[IlluminateConfig::class];

            return new CollectionConfig($config->get(Registrator::CFG_KEY_COLLECTIONS));
        });

        $app->singleton(FactoryInterface::class, static function (Container $app): FactoryInterface {
            $collectionFactoryClass = $app[CollectionConfig::class]->getDefaultCollectionFactoryClass();

            return new Factory(
                dbal: $app[DatabaseProviderInterface::class],
                defaultCollectionFactory: $app->make($collectionFactoryClass)
            );
        });

        $app->singleton(ORMInterface::class, function (Container $app): ORMInterface {
            return new ORM(
                factory: $app[FactoryInterface::class],
                schema: $app[SchemaInterface::class]
            );
        });
    }
}
