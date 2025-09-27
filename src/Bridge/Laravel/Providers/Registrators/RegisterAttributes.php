<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Providers\Registrators;

use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Foundation\Application;
use Spiral\Attributes\AttributeReader;
use Spiral\Attributes\Internal\Instantiator\InstantiatorInterface;
use Spiral\Attributes\Internal\Instantiator\NamedArgumentsInstantiator;
use Spiral\Attributes\Psr16CachedReader;
use Spiral\Attributes\ReaderInterface;

/**
 * @see https://github.com/spiral/framework/blob/master/src/Framework/Bootloader/Attributes/AttributesBootloader.php
 */
final class RegisterAttributes
{
    public function __invoke(Application $app): void
    {
        $app->singleton(InstantiatorInterface::class, function () {
            return new NamedArgumentsInstantiator();
        });

        $app->singleton(ReaderInterface::class, function (Application $app): ReaderInterface {
            $reader = new AttributeReader($app->get(InstantiatorInterface::class));

            /** @var bool $cacheEnabled */
            $cacheEnabled = config('cycle.attributes.cache.enabled', false);

            if ($cacheEnabled) {
                $cacheFactory = $app->get(CacheFactory::class);
                $cacheStore = config('cycle.attributes.cache.store', 'file');
                $reader = new Psr16CachedReader($reader, $cacheFactory->store($cacheStore));
            }

            return $reader;
        });
    }
}
