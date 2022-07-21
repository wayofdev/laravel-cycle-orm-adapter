<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Providers\Registrators;

use Illuminate\Contracts\Container\Container;
use WayOfDev\Cycle\Contracts\EntityManager as EntityManagerContract;
use WayOfDev\Cycle\Entity\Manager;

final class RegisterEntityManager
{
    public function __invoke(Container $app): void
    {
        $app->singleton(EntityManagerContract::class, static function (Container $app): EntityManagerContract {
            return $app[Manager::class];
        });
    }
}
