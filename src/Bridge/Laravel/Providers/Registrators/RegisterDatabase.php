<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Providers\Registrators;

use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\DatabaseInterface;
use Cycle\Database\DatabaseManager;
use Cycle\Database\DatabaseProviderInterface;
use Illuminate\Contracts\Foundation\Application;

/**
 * @see https://github.com/spiral/cycle-bridge/blob/2.0/src/Bootloader/DatabaseBootloader.php
 */
final class RegisterDatabase
{
    public function __invoke(Application $app): void
    {
        $app->singleton(DatabaseProviderInterface::class, static function (Application $app): DatabaseProviderInterface {
            return new DatabaseManager(
                config: $app->get(DatabaseConfig::class),
                loggerFactory: null
            );
        });

        $app->bind(DatabaseInterface::class, static function (Application $app): DatabaseInterface {
            return $app->get(DatabaseProviderInterface::class)->database();
        });

        $app->alias(DatabaseProviderInterface::class, DatabaseManager::class);
    }
}
