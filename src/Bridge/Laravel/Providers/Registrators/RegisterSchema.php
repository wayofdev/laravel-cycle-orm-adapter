<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Providers\Registrators;

use Cycle\Annotated;
use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Generator;
use Cycle\Schema\GeneratorInterface;
use Cycle\Schema\Registry;
use Illuminate\Container\Container;
use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Container\BindingResolutionException;
use WayOfDev\Cycle\Contracts\CacheManager as CacheManagerContract;
use WayOfDev\Cycle\Contracts\Config\Repository as Config;
use WayOfDev\Cycle\Schema\Cache\Manager as CacheManager;
use WayOfDev\Cycle\Schema\Compiler;

use function array_merge;
use function is_array;
use function is_object;

final class RegisterSchema
{
    public const GROUP_INDEX = 'index';
    public const GROUP_RENDER = 'render';
    public const GROUP_POSTPROCESS = 'postprocess';

    /** @var string[][]|GeneratorInterface[][] */
    private array $defaultGenerators;

    public function __invoke(Container $app): void
    {
        $this->defaultGenerators = [
            self::GROUP_INDEX => [
                Annotated\Embeddings::class,
                Annotated\Entities::class,
                Annotated\MergeColumns::class,
            ],
            self::GROUP_RENDER => [
                Generator\ResetTables::class,
                Generator\GenerateRelations::class,
                Generator\GenerateModifiers::class,
                Generator\ValidateEntities::class,
                Generator\RenderTables::class,
                Generator\RenderRelations::class,
                Generator\RenderModifiers::class,
                Annotated\TableInheritance::class,
                Annotated\MergeIndexes::class,
            ],
            self::GROUP_POSTPROCESS => [
                Generator\GenerateTypecast::class,
            ],
        ];

        $app->singleton(CacheManagerContract::class, static function (Container $app): CacheManagerContract {
            return new CacheManager(
                config: $app[Config::class],
                cacheFactory: $app[CacheFactory::class]
            );
        });

        $app->bind(SchemaInterface::class, function (Container $app): SchemaInterface {
            $config = $app->make(Config::class);

            $schemaCompiler = Compiler::fromMemory($app[CacheManagerContract::class]);

            if ($schemaCompiler->isEmpty() || ! $config->schemaCache()) {
                $schemaCompiler = Compiler::compile(
                    $app->get(Registry::class),
                    $this->getGenerators($app, $config),
                    $config->schemaDefaults(),
                );
            }

            return $schemaCompiler->toSchema();
        });
    }

    /**
     * @throws BindingResolutionException
     */
    public function getGenerators(Container $app, Config $config): array
    {
        $generators = $config->schemaGenerators();
        if (is_array($generators)) {
            $generators = array_merge([self::GROUP_INDEX => $generators], $generators);
        } else {
            $generators = $this->defaultGenerators;
        }

        $result = [];
        foreach ($generators as $group) {
            foreach ($group as $generator) {
                if (is_object($generator)) {
                    $result[] = $generator;
                } else {
                    $result[] = $app->make($generator);
                }
            }
        }

        return $result;
    }
}
