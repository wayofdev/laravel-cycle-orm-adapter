<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Providers;

use Cycle\ORM\ORMInterface;
use Illuminate\Contracts\Config\Repository as IlluminateConfig;
use Illuminate\Support\ServiceProvider;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use WayOfDev\Cycle\Bridge\Laravel\Console\Commands\Database;
use WayOfDev\Cycle\Bridge\Laravel\Console\Commands\Migrations;
use WayOfDev\Cycle\Bridge\Laravel\Console\Commands\ORM;

final class CycleServiceProvider extends ServiceProvider
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../../../config/cycle.php' => config_path('cycle.php'),
            ], 'config');

            $this->registerConsoleCommands();
        }

        /** @var IlluminateConfig $config */
        $config = $this->app->get(IlluminateConfig::class);
        $warmup = $config->get('cycle.warmup');

        if (true === $warmup) {
            $orm = $this->app->get(ORMInterface::class);
            $orm->prepareServices();
        }
    }

    public function register(): void
    {
        // @phpstan-ignore-next-line
        if (! $this->app->configurationIsCached()) {
            $this->mergeConfigFrom(
                __DIR__ . '/../../../../config/cycle.php',
                Registrator::CFG_KEY
            );
        }

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
            Migrations\FreshSchemaCommand::class,
            ORM\MigrateCommand::class,
            ORM\RenderCommand::class,
            ORM\SyncCommand::class,
            ORM\UpdateCommand::class,
        ]);
    }
}
