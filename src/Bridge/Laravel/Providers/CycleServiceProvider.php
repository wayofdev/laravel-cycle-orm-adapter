<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Providers;

use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\DatabaseManager;
use Cycle\Database\DatabaseProviderInterface;
use Cycle\ORM\SchemaInterface;
use Illuminate\Contracts\Config\Repository as IlluminateConfig;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Spiral\Tokenizer\Tokenizer;
use WayOfDev\Cycle\Config;
use WayOfDev\Cycle\Contracts\Config\Repository as ConfigRepository;
use WayOfDev\Cycle\Contracts\EntityManager;
use WayOfDev\Cycle\Contracts\SchemaManager as SchemaManagerContract;
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
        $this->registerDatabaseSchema();
        $this->registerOrm();
        $this->registerTokenizer();
    }

    private function registerConsoleCommands(): void
    {
        $this->commands([
            // ...
        ]);
    }

    private function registerAdapterConfig(): void
    {
        $this->app->singleton(ConfigRepository::class, static function (Container $app): ConfigRepository {
            /** @var IlluminateConfig $config */
            $config = $app[IlluminateConfig::class];

            return Config::fromArray($config->get('cycle'));
        });
    }

    private function registerDatabaseConfig(): void
    {
        $this->app->singleton(DatabaseConfig::class, static function (Container $app): DatabaseConfig {
            /** @var IlluminateConfig $config */
            $config = $app[IlluminateConfig::class];

            return new DatabaseConfig($config->get('cycle.database'));
        });
    }

    private function registerDatabaseManager(): void
    {
        $this->app->singleton(DatabaseProviderInterface::class, static function (Container $app): DatabaseProviderInterface {
            return new DatabaseManager(
                $app[DatabaseConfig::class]
            );
        });

        $this->app->alias(DatabaseProviderInterface::class, DatabaseManager::class);
    }

    private function registerEntityManager(): void
    {
        $this->app->singleton(EntityManager::class, static function (Container $app): EntityManager {
            return $app[Manager::class];
        });
    }

    private function registerDatabaseSchema(): void
    {
        $this->app->singleton(SchemaInterface::class, static function (Container $app): SchemaInterface {
            return $app[SchemaManagerContract::class]->create();
        });
    }

    private function registerOrm(): void
    {
        // @todo implement...
    }

    private function registerTokenizer(): void
    {
        $this->app->singleton(Tokenizer::class, static function (Container $app): Tokenizer {
            /** @var IlluminateConfig $config */
            $config = $app[IlluminateConfig::class];

            return new Tokenizer($config->get('cycle'));
        });
    }
}
