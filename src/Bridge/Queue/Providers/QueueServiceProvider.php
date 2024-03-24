<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Queue\Providers;

use Illuminate\Config\Repository;
use Illuminate\Support\ServiceProvider;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use function array_merge;

class QueueServiceProvider extends ServiceProvider
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function register(): void
    {
        /** @var Repository $config */
        $config = $this->app->get(Repository::class);

        $config->set('cycle.tokenizer.directories', array_merge(
            $config->get('cycle.tokenizer.directories', []),
            [__DIR__ . '/../Entities']
        ));
    }
}
