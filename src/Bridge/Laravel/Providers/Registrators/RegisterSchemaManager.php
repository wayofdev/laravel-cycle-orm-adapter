<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Providers\Registrators;

use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Registry;
use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Container\Container;
use WayOfDev\Cycle\Contracts\CacheManager as CacheManagerContract;
use WayOfDev\Cycle\Contracts\Config\Repository as Config;
use WayOfDev\Cycle\Contracts\GeneratorLoader;
use WayOfDev\Cycle\Contracts\SchemaCompiler;
use WayOfDev\Cycle\Contracts\SchemaManager as SchemaManagerContract;
use WayOfDev\Cycle\Schema\Cache\Manager as CacheManager;
use WayOfDev\Cycle\Schema\Compiler;
use WayOfDev\Cycle\Schema\Generators\GeneratorsFactory;
use WayOfDev\Cycle\Schema\Manager as SchemaManager;

final class RegisterSchemaManager
{
    public function __invoke(Container $app): void
    {
        $app->singleton(CacheManagerContract::class, static function (Container $app): CacheManagerContract {
            return new CacheManager(
                config: $app[Config::class],
                cacheFactory: $app[CacheFactory::class]
            );
        });

        $app->singleton(SchemaCompiler::class, static function (Container $app): SchemaCompiler {
            /** @var Registry $registry */
            $registry = $app->make(Registry::class);

            return new Compiler($registry);
        });

        $app->singleton(SchemaInterface::class, static function (Container $app): SchemaInterface {
            return $app[SchemaManagerContract::class]->create();
        });

        $app->singleton(GeneratorsFactory::class, static function (Container $app): GeneratorsFactory {
            return new GeneratorsFactory(
                app: $app,
                config: $app[Config::class]
            );
        });
        $app->bind(GeneratorLoader::class, GeneratorsFactory::class);

        $app->singleton(SchemaManagerContract::class, static function (Container $app): SchemaManagerContract {
            return new SchemaManager(
                $app[GeneratorsFactory::class],
                $app[SchemaCompiler::class],
                $app[Config::class],
                $app[CacheManagerContract::class]
            );
        });
    }
}
