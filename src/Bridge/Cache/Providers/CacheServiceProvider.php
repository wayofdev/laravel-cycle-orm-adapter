<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Cache\Providers;

use Illuminate\Config\Repository;
use Illuminate\Support\ServiceProvider;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use function array_merge;

class CacheServiceProvider extends ServiceProvider
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function register(): void
    {
        $config = $this->app->get(Repository::class);

        $config->set('cycle.tokenizer.directories', array_merge(
            $config->get('cycle.tokenizer.directories', []),
            [__DIR__ . '/../Entities']
        ));
    }
}
