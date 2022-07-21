<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Providers\Registrators;

use Illuminate\Contracts\Config\Repository as IlluminateConfig;
use Illuminate\Contracts\Container\Container;
use WayOfDev\Cycle\Config;
use WayOfDev\Cycle\Contracts\Config\Repository as ConfigRepository;

final class RegisterAdapterConfig
{
    private const CFG_KEY = 'cycle';

    public function __invoke(Container $app): void
    {
        $app->singleton(ConfigRepository::class, static function (Container $app): ConfigRepository {
            /** @var IlluminateConfig $config */
            $config = $app[IlluminateConfig::class];

            return Config::fromArray($config->get(self::CFG_KEY));
        });
    }
}
