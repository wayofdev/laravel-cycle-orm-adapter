<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Providers\Registrators;

use Cycle\Database\DatabaseProviderInterface;
use Cycle\ORM\SchemaInterface;
use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Container\Container;
use WayOfDev\Cycle\Contracts\Config\Repository as ConfigRepository;
use WayOfDev\Cycle\Contracts\SchemaManager as SchemaManagerContract;
use WayOfDev\Cycle\Schema\Manager;
use WayOfDev\Cycle\Schema\SchemaGeneratorsFactory;

final class RegisterSchemaManager
{
    public function __invoke(Container $app): void
    {
        $app->singleton(SchemaInterface::class, static function (Container $app): SchemaInterface {
            return $app[SchemaManagerContract::class]->create();
        });

        $app->singleton(SchemaGeneratorsFactory::class, SchemaGeneratorsFactory::class);
        $app->singleton(SchemaManagerContract::class, function (Container $app): SchemaManagerContract {
            return new Manager(
                $app[DatabaseProviderInterface::class],
                $app[SchemaGeneratorsFactory::class],
                $app[ConfigRepository::class],
                $app[CacheFactory::class]
            );
        });
    }
}
