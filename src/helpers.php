<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Factories\Factory as EloquentFactory;
use WayOfDev\Cycle\Bridge\Laravel\Factories\Factory;

function factory(string $entityClass, ?int $count = null): EloquentFactory
{
    $factory = Factory::factoryForEntity($entityClass);

    if (null !== $count) {
        return $factory->count($count);
    }

    return $factory;
}
