<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Providers\Registrators;

use Cycle\Database\DatabaseProviderInterface;
use Cycle\Migrations\Config\MigrationConfig;
use Cycle\Migrations\FileRepository;
use Cycle\Migrations\Migrator;
use Cycle\Migrations\RepositoryInterface;
use Illuminate\Container\Container;

/**
 * @see https://github.com/spiral/cycle-bridge/blob/2.0/src/Bootloader/MigrationsBootloader.php
 */
final class RegisterMigrations
{
    public function __invoke(Container $app): void
    {
        $app->singleton(RepositoryInterface::class, static function (Container $app): RepositoryInterface {
            $config = $app->get(MigrationConfig::class);

            return new FileRepository(
                config: $config
            );
        });

        $app->singleton(Migrator::class, static function (Container $app): Migrator {
            return new Migrator(
                config: $app->get(MigrationConfig::class),
                dbal: $app->get(DatabaseProviderInterface::class),
                repository: $app->get(RepositoryInterface::class)
            );
        });
    }
}
