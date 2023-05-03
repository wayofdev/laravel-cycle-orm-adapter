<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Providers\Registrators;

use Cycle\Database\Config\DatabaseConfig;
use Cycle\Migrations\Config\MigrationConfig;
use Cycle\ORM\Config\RelationConfig;
use Illuminate\Contracts\Config\Repository as IlluminateConfig;
use Illuminate\Contracts\Foundation\Application;
use Spiral\Tokenizer\Config\TokenizerConfig;
use WayOfDev\Cycle\Bridge\Laravel\Providers\Registrator;
use WayOfDev\Cycle\Schema\Config\SchemaConfig;

final class RegisterConfigs
{
    public function __invoke(Application $app): void
    {
        $this->registerDatabaseConfig($app);
        $this->registerMigrationConfig($app);
        $this->registerTokenizerConfig($app);
        $this->registerSchemaConfig($app);
        $this->registerRelationConfig($app);
    }

    private function registerDatabaseConfig(Application $app): void
    {
        $app->singleton(DatabaseConfig::class, static function (Application $app): DatabaseConfig {
            /** @var IlluminateConfig $config */
            $config = $app->get(IlluminateConfig::class);

            return new DatabaseConfig(
                config: $config->get(Registrator::CFG_KEY_DATABASE)
            );
        });
    }

    private function registerMigrationConfig(Application $app): void
    {
        $app->singleton(MigrationConfig::class, static function (Application $app): MigrationConfig {
            /** @var IlluminateConfig $config */
            $config = $app->get(IlluminateConfig::class);

            return new MigrationConfig($config->get(Registrator::CFG_KEY_MIGRATIONS));
        });
    }

    private function registerTokenizerConfig(Application $app): void
    {
        $app->singleton(TokenizerConfig::class, static function (Application $app): TokenizerConfig {
            /** @var IlluminateConfig $config */
            $config = $app->get(IlluminateConfig::class);

            return new TokenizerConfig($config->get(Registrator::CFG_KEY_TOKENIZER));
        });
    }

    private function registerSchemaConfig(Application $app): void
    {
        $app->singleton(SchemaConfig::class, static function (Application $app): SchemaConfig {
            /** @var IlluminateConfig $config */
            $config = $app->get(IlluminateConfig::class);

            return new SchemaConfig($config->get(Registrator::CFG_KEY_SCHEMA));
        });
    }

    private function registerRelationConfig(Application $app): void
    {
        $app->singleton(RelationConfig::class, static function (Application $app): RelationConfig {
            /** @var IlluminateConfig $config */
            $config = $app->get(IlluminateConfig::class);
            $relations = RelationConfig::getDefault()->toArray() + $config->get(Registrator::CFG_KEY_RELATIONS);

            return new RelationConfig($relations);
        });
    }
}
