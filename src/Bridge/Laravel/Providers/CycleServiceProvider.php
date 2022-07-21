<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Providers;

use Illuminate\Support\ServiceProvider;

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
            Registrator::CFG_KEY
        );

        $registrators = [
            Registrators\RegisterAdapterConfig::class,
            Registrators\RegisterDatabaseManager::class,
            Registrators\RegisterEntityManager::class,
            Registrators\RegisterORM::class,
            Registrators\RegisterMigrator::class,
            Registrators\RegisterClassLocator::class,
        ];

        foreach ($registrators as $registrator) {
            (new $registrator())($this->app);
        }
    }

    private function registerConsoleCommands(): void
    {
        $this->commands([
            // ...
        ]);
    }
}
