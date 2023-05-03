<?php

declare(strict_types=1);

use WayOfDev\Cycle\Bridge\Laravel\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Factory as EloquentFactory;

function factory(string $entityClass, ?int $count = null): EloquentFactory
{
    $factory = Factory::factoryForEntity($entityClass);

    if (null !== $count) {
        return $factory->count($count);
    }

    return $factory;
}
