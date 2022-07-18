<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Providers;

use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\DatabaseManager;
use Cycle\Database\DatabaseProviderInterface;
use Illuminate\Support\ServiceProvider;
use WayOfDev\Cycle\Config;
use WayOfDev\Cycle\Contracts\Config\Repository as ConfigRepository;
use WayOfDev\Cycle\Contracts\EntityManager;
use WayOfDev\Cycle\Entity\Manager;

final class CycleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../../../config/cycle.php' => config_path('cycle.php'),
            ]);

            $this->registerConsoleCommands();
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../../../config/cycle.php',
            'cycle'
        );

        $this->registerAdapterConfig();
        $this->registerDatabaseConfig();
        $this->registerDatabaseManager();
        $this->registerEntityManager();
    }

    private function registerConsoleCommands(): void
    {
        $this->commands([
            // ...
        ]);
    }

    private function registerAdapterConfig(): void
    {
        $this->app->singleton(ConfigRepository::class, static function (): ConfigRepository {
            return Config::fromArray(
                config('cycle')
            );
        });
    }

    private function registerDatabaseConfig(): void
    {
        $this->app->singleton(DatabaseConfig::class, static function (): DatabaseConfig {
            return new DatabaseConfig(
                config('cycle.database')
            );
        });
    }

    private function registerDatabaseManager(): void
    {
        $this->app->singleton(DatabaseProviderInterface::class, function ($app): DatabaseProviderInterface {
            return new DatabaseManager(
                $app[DatabaseConfig::class]
            );
        });

        $this->app->alias(DatabaseProviderInterface::class, DatabaseManager::class);
    }

    private function registerEntityManager(): void
    {
        $this->app->singleton(EntityManager::class, function ($app): EntityManager {
            return $app[Manager::class];
        });
    }
}
