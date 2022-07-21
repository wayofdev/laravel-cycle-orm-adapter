<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Providers\Registrators;

use Cycle\Database\DatabaseProviderInterface;
use Cycle\Migrations\Config\MigrationConfig;
use Cycle\Migrations\FileRepository;
use Cycle\Migrations\Migrator;
use Cycle\Migrations\RepositoryInterface;
use Illuminate\Contracts\Config\Repository as IlluminateConfig;
use Illuminate\Contracts\Container\Container;
use WayOfDev\Cycle\Bridge\Laravel\Providers\Registrator;

final class RegisterMigrator
{
    public function __invoke(Container $app): void
    {
        $app->singleton(MigrationConfig::class, static function (Container $app): MigrationConfig {
            /** @var IlluminateConfig $config */
            $config = $app[IlluminateConfig::class];

            return new MigrationConfig($config->get(Registrator::CFG_KEY_MIGRATIONS));
        });

        $app->singleton(RepositoryInterface::class, static function (Container $app): RepositoryInterface {
            $config = $app[MigrationConfig::class];

            return new FileRepository(
                config: $config
            );
        });

        $app->singleton(Migrator::class, static function (Container $app): Migrator {
            return new Migrator(
                config: $app[MigrationConfig::class],
                dbal: $app[DatabaseProviderInterface::class],
                repository: $app[RepositoryInterface::class]
            );
        });
    }
}
