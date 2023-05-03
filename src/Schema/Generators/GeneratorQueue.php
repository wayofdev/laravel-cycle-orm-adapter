<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Schema\Generators;

use Cycle\Schema\GeneratorInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use WayOfDev\Cycle\Contracts\GeneratorLoader;
use WayOfDev\Cycle\Schema\Config\SchemaConfig;
use Closure;

use function is_a;
use function is_string;

final class GeneratorQueue implements GeneratorLoader
{
    /** @var array<array<GeneratorInterface|class-string<GeneratorInterface>>> */
    private array $generators;

    private Container $app;

    public function __construct(
        Closure $closure,
        private readonly SchemaConfig $config
    ) {
        $this->app = $closure();
        $this->generators = $this->config->generators();
    }

    public function add(string $group, GeneratorInterface|string $generator): GeneratorLoader
    {
        $queue = clone $this;

        $queue->generators[$group][] = $generator;

        return $queue;
    }

    public function remove(string $removableGenerator): GeneratorLoader
    {
        $queue = clone $this;

        foreach ($queue->generators as $groupKey => $groupName) {
            foreach ($groupName as $generatorKey => $generatorDefinition) {
                if (is_a($generatorDefinition, $removableGenerator, true)) {
                    unset($queue->generators[$groupKey][$generatorKey]);
                }
            }
        }

        return $queue;
    }

    /**
     * @throws BindingResolutionException
     */
    public function get(): array
    {
        $result = [];

        foreach ($this->generators as $group) {
            foreach ($group as $generator) {
                $result[] = $this->make($generator);
            }
        }

        return $result;
    }

    public function without(): GeneratorLoader
    {
        $queue = clone $this;

        $queue->generators = [];

        return $queue;
    }

    /**
     * @throws BindingResolutionException
     */
    private function make(GeneratorInterface|string $generator): GeneratorInterface
    {
        if (is_string($generator)) {
            return $this->app->make($generator);
        }

        return $generator;
    }
}
