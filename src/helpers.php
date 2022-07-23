<?php

declare(strict_types=1);

use WayOfDev\Cycle\Factories\Factory;

function factory(string $entityClass, ?int $count = null): Factory
{
    $factory = Factory::factoryForEntity($entityClass);

    if (null !== $count) {
        return $factory->count($count);
    }

    return $factory;
}
