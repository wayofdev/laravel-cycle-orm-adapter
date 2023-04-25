<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Providers\Registrators;

use Cycle\ORM\EntityManager;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\ORMInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Application;

final class RegisterEntityManager
{
    public function __invoke(Container $app): void
    {
        $app->singleton(EntityManagerInterface::class, static function (Application $app): EntityManagerInterface {
            /** @var ORMInterface $orm */
            $orm = $app->get(ORMInterface::class);
            $orm->getHeap()->clean();

            return new EntityManager($orm);
        });
    }
}
