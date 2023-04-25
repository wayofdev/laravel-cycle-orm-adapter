<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Schema\Generators;

use Cycle\Schema\GeneratorInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use WayOfDev\Cycle\Contracts\Config\Repository as Config;
use WayOfDev\Cycle\Contracts\GeneratorLoader;

use function is_a;

final class GeneratorsFactory implements GeneratorLoader
{
    private array $generatorClasses;

    public function __construct(
        private readonly Container $app,
        private readonly Config $config
    ) {
        $this->generatorClasses = $this->config->schemaGenerators();
    }

    /**
     * @throws BindingResolutionException
     */
    public function get(): array
    {
        $instances = [];

        foreach ($this->generatorClasses as $group) {
            foreach ($group as $class) {
                $instances[] = $this->make($class);
            }
        }

        return $instances;
    }

    public function add(string $group, GeneratorInterface|string $generator): GeneratorLoader
    {
        $queue = clone $this;

        $queue->generatorClasses[$group][] = $generator;

        return $queue;
    }

    public function remove(string $removableGenerator): GeneratorLoader
    {
        $queue = clone $this;

        foreach ($queue->generatorClasses as $groupKey => $groupName) {
            foreach ($groupName as $generatorKey => $generatorDefinition) {
                if (is_a($generatorDefinition, $removableGenerator, true)) {
                    unset($queue->generatorClasses[$groupKey][$generatorKey]);
                }
            }
        }

        return $queue;
    }

    public function without(): GeneratorLoader
    {
        $loader = clone $this;

        $loader->generatorClasses = [];

        return $loader;
    }

    /**
     * @throws BindingResolutionException
     */
    private function make(GeneratorInterface|string $class): GeneratorInterface
    {
        return $class instanceof GeneratorInterface ? $class : $this->app->make($class);
    }
}
