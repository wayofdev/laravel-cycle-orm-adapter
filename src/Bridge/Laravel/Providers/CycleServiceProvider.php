<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Providers;

use Cycle\Database\Config\DatabaseConfig;
use Illuminate\Support\ServiceProvider;

final class CycleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../../../config/cycle.php',
            'cycle'
        );
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../../../config/cycle.php' => config_path('cycle.php'),
            ]);

            $this->registerConsoleCommands();
        }
    }

    private function registerConsoleCommands(): void
    {
        $this->commands([
        ]);
    }

    private function registerDatabaseConfig(): void
    {
        $this->app->singleton(DatabaseConfig::class, static function (): DatabaseConfig {
            return new DatabaseConfig(
                // sjuda podatj nastrojki iz konfiga !!!
            );
        });
    }
}
