<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Factories;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Spiral\Core\FactoryInterface;

final class SpiralFactory implements FactoryInterface
{
    public function __construct(private readonly Application $app)
    {
    }

    /**
     * @throws BindingResolutionException
     */
    public function make(string $alias, array $parameters = []): mixed
    {
        return $this->app->make($alias, $parameters);
    }
}
