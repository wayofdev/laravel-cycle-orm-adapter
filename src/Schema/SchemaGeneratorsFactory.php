<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Schema;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;

final class SchemaGeneratorsFactory
{
    public function __construct(
        private readonly Container $app,
        private readonly array $generatorClasses
    ) {
    }

    /**
     * @throws BindingResolutionException
     */
    public function get(): array
    {
        $generators = [];

        foreach ($this->generatorClasses as $class) {
            $generators[] = $this->app->make($class);
        }

        return $generators;
    }
}
