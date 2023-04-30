<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Providers\Registrators;

use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Registry;
use Illuminate\Container\Container;
use Illuminate\Contracts\Cache\Factory as CacheFactory;
use WayOfDev\Cycle\Contracts\CacheManager as CacheManagerContract;
use WayOfDev\Cycle\Contracts\GeneratorLoader;
use WayOfDev\Cycle\Contracts\SchemaCompiler;
use WayOfDev\Cycle\Schema\Cache\Manager as CacheManager;
use WayOfDev\Cycle\Schema\Compiler;
use WayOfDev\Cycle\Schema\Config\SchemaConfig;
use WayOfDev\Cycle\Schema\Factory;
use WayOfDev\Cycle\Schema\Generators\GeneratorQueue;

/**
 * @see https://github.com/spiral/cycle-bridge/blob/2.0/src/Bootloader/SchemaBootloader.php
 */
final class RegisterSchema
{
    public function __invoke(Container $app): void
    {
        $app->singleton(CacheManagerContract::class, static function (Container $app): CacheManagerContract {
            return new CacheManager(
                config: $app->get(SchemaConfig::class),
                cacheFactory: $app->get(CacheFactory::class)
            );
        });

        $app->singleton(GeneratorLoader::class, static function (Container $app): GeneratorLoader {
            return new GeneratorQueue(
                app: $app,
                config: $app->get(SchemaConfig::class),
            );
        });

        $app->bind(SchemaCompiler::class, static function (Container $app): SchemaCompiler {
            /** @var Registry $registry */
            $registry = $app->make(Registry::class);

            return new Compiler($registry);
        });

        $app->singleton(SchemaInterface::class, static function (Container $app): SchemaInterface {
            /** @var Factory $factory */
            $factory = $app->make(Factory::class);

            return $factory->create();
        });
    }
}
