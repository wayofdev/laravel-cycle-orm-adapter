<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Providers\Registrators;

use Cycle\Database\Config\DatabaseConfig;
use Cycle\Migrations\Config\MigrationConfig;
use Cycle\ORM\Config\RelationConfig;
use Illuminate\Contracts\Config\Repository as IlluminateConfig;
use Illuminate\Contracts\Container\Container;
use Spiral\Tokenizer\Config\TokenizerConfig;
use WayOfDev\Cycle\Bridge\Laravel\Providers\Registrator;
use WayOfDev\Cycle\Schema\Config\SchemaConfig;

final class RegisterConfigs
{
    public function __invoke(Container $app): void
    {
        $this->registerDatabaseConfig($app);
        $this->registerMigrationConfig($app);
        $this->registerTokenizerConfig($app);
        $this->registerSchemaConfig($app);
        $this->registerRelationConfig($app);
    }

    private function registerDatabaseConfig(Container $app): void
    {
        $app->singleton(DatabaseConfig::class, static function (Container $app): DatabaseConfig {
            /** @var IlluminateConfig $config */
            $config = $app->get(IlluminateConfig::class);

            return new DatabaseConfig(
                config: $config->get(Registrator::CFG_KEY_DATABASE)
            );
        });
    }

    private function registerMigrationConfig(Container $app): void
    {
        $app->singleton(MigrationConfig::class, static function (Container $app): MigrationConfig {
            /** @var IlluminateConfig $config */
            $config = $app->get(IlluminateConfig::class);

            return new MigrationConfig($config->get(Registrator::CFG_KEY_MIGRATIONS));
        });
    }

    private function registerTokenizerConfig(Container $app): void
    {
        $app->singleton(TokenizerConfig::class, static function (Container $app): TokenizerConfig {
            /** @var IlluminateConfig $config */
            $config = $app->get(IlluminateConfig::class);

            return new TokenizerConfig($config->get(Registrator::CFG_KEY_TOKENIZER));
        });
    }

    private function registerSchemaConfig(Container $app): void
    {
        $app->singleton(SchemaConfig::class, static function (Container $app): SchemaConfig {
            /** @var IlluminateConfig $config */
            $config = $app->get(IlluminateConfig::class);

            return new SchemaConfig($config->get(Registrator::CFG_KEY_SCHEMA));
        });
    }

    private function registerRelationConfig(Container $app): void
    {
        $app->singleton(RelationConfig::class, static function (Container $app): RelationConfig {
            /** @var IlluminateConfig $config */
            $config = $app->get(IlluminateConfig::class);
            $relations = RelationConfig::getDefault()->toArray() + $config->get(Registrator::CFG_KEY_RELATIONS);

            return new RelationConfig($relations);
        });
    }
}
