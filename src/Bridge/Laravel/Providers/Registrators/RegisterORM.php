<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Providers\Registrators;

use Cycle\Database\DatabaseProviderInterface;
use Cycle\ORM\Factory;
use Cycle\ORM\FactoryInterface;
use Cycle\ORM\ORM as CycleORM;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\SchemaInterface;
use Illuminate\Contracts\Container\Container;
use WayOfDev\Cycle\Contracts\SchemaManager as SchemaManagerContract;

final class RegisterORM
{
    public function __invoke(Container $app): void
    {
        $app->singleton(SchemaInterface::class, static function (Container $app): SchemaInterface {
            return $app[SchemaManagerContract::class]->create();
        });

        $app->singleton(FactoryInterface::class, static function (Container $app): FactoryInterface {
            return new Factory(
                dbal: $app[DatabaseProviderInterface::class]
            );
        });

        $app->singleton(ORMInterface::class, function (Container $app): ORMInterface {
            return new CycleORM(
                factory: $app[FactoryInterface::class],
                schema: $app[SchemaInterface::class]
            );
        });
    }
}
