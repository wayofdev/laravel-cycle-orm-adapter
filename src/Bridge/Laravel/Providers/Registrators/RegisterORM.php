<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Providers\Registrators;

use Cycle\Database\DatabaseProviderInterface;
use Cycle\ORM\Factory;
use Cycle\ORM\FactoryInterface;
use Cycle\ORM\ORM;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\SchemaInterface;
use Illuminate\Contracts\Container\Container;
use WayOfDev\Cycle\Contracts\Config\Repository as Config;

final class RegisterORM
{
    public function __invoke(Container $app): void
    {
        $app->singleton(FactoryInterface::class, static function (Container $app): FactoryInterface {
            $collectionFactoryClass = $app->make($app[Config::class]->defaultCollectionFactory());

            return new Factory(
                dbal: $app[DatabaseProviderInterface::class],
                defaultCollectionFactory: $collectionFactoryClass
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
