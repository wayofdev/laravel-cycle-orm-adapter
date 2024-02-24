<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Providers\Registrators;

use Cycle\Database\DatabaseProviderInterface;
use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Defaults;
use Cycle\Schema\Registry;
use Illuminate\Container\Container;
use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Foundation\Application;
use WayOfDev\Cycle\Contracts\CacheManager as CacheManagerContract;
use WayOfDev\Cycle\Contracts\GeneratorLoader;
use WayOfDev\Cycle\Schema\Cache\Manager as CacheManager;
use WayOfDev\Cycle\Schema\Compiler;
use WayOfDev\Cycle\Schema\Config\SchemaConfig;
use WayOfDev\Cycle\Schema\Generators\GeneratorQueue;

/**
 * @see https://github.com/spiral/cycle-bridge/blob/2.0/src/Bootloader/SchemaBootloader.php
 */
final class RegisterSchema
{
    public function __invoke(Application $app): void
    {
        $app->singleton(CacheManagerContract::class, static function (Application $app): CacheManagerContract {
            return new CacheManager(
                config: $app->get(SchemaConfig::class),
                cacheFactory: $app->get(CacheFactory::class)
            );
        });

        $app->singleton(GeneratorLoader::class, static function (Application $app): GeneratorLoader {
            return new GeneratorQueue(
                closure: fn () => Container::getInstance(),
                config: $app->get(SchemaConfig::class),
            );
        });

        $app->bind(Registry::class, static function (Application $app): Registry {
            $defaults = new Defaults();

            /** @var SchemaConfig $config */
            $config = $app->get(SchemaConfig::class);

            $defaults->merge($config->defaults());

            return new Registry($app->get(DatabaseProviderInterface::class), $defaults);
        });

        $app->bind(SchemaInterface::class, static function (Application $app): SchemaInterface {
            /** @var SchemaConfig $config */
            $config = $app->get(SchemaConfig::class);

            /** @var CacheManagerContract $cache */
            $cache = $app->get(CacheManagerContract::class);

            $schemaCompiler = Compiler::fromMemory(
                cache: $cache
            );

            if ($schemaCompiler->isEmpty() || ! $config->cacheSchema()) {
                $schemaCompiler = Compiler::compile(
                    registry: $app->get(Registry::class),
                    queue: $app->get(GeneratorLoader::class),
                );

                $schemaCompiler->toMemory($cache);
            }

            return $schemaCompiler->toSchema();
        });
    }
}
