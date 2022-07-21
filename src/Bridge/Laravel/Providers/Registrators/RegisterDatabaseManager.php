<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Providers\Registrators;

use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\DatabaseManager;
use Cycle\Database\DatabaseProviderInterface;
use Illuminate\Contracts\Config\Repository as IlluminateConfig;
use Illuminate\Contracts\Container\Container;
use WayOfDev\Cycle\Bridge\Laravel\Providers\Registrator;

final class RegisterDatabaseManager
{
    public function __invoke(Container $app): void
    {
        $app->singleton(DatabaseConfig::class, static function (Container $app): DatabaseConfig {
            /** @var IlluminateConfig $config */
            $config = $app[IlluminateConfig::class];

            return new DatabaseConfig($config->get(Registrator::CFG_KEY_DATABASE));
        });

        $app->singleton(DatabaseProviderInterface::class, static function (Container $app): DatabaseProviderInterface {
            return new DatabaseManager(
                config: $app[DatabaseConfig::class]
            );
        });

        $app->alias(DatabaseProviderInterface::class, DatabaseManager::class);
    }
}
