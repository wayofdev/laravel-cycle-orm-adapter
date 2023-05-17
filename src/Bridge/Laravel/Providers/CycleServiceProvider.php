<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Providers;

use Illuminate\Support\ServiceProvider;
use WayOfDev\Cycle\Bridge\Laravel\Console\Commands\Database;
use WayOfDev\Cycle\Bridge\Laravel\Console\Commands\Migrations;
use WayOfDev\Cycle\Bridge\Laravel\Console\Commands\ORM;

final class CycleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../../../config/cycle.php' => config_path('cycle.php'),
            ], 'config');

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
            Registrators\RegisterConfigs::class,
            Registrators\RegisterClassesInterface::class,
            Registrators\RegisterAnnotated::class,
            Registrators\RegisterORM::class,
            Registrators\RegisterDatabase::class,
            Registrators\RegisterMigrations::class,
            Registrators\RegisterSchema::class,
        ];

        foreach ($registrators as $registrator) {
            (new $registrator())($this->app);
        }
    }

    private function registerConsoleCommands(): void
    {
        $this->commands([
            Database\ListCommand::class,
            Database\TableCommand::class,
            Migrations\InitCommand::class,
            Migrations\MigrateCommand::class,
            Migrations\ReplayCommand::class,
            Migrations\RollbackCommand::class,
            Migrations\StatusCommand::class,
            ORM\MigrateCommand::class,
            ORM\RenderCommand::class,
            ORM\SyncCommand::class,
            ORM\UpdateCommand::class,
        ]);
    }
}
