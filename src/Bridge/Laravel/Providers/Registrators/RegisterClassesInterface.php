<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Providers\Registrators;

use Illuminate\Container\Container;
use Illuminate\Contracts\Config\Repository as IlluminateConfig;
use Spiral\Tokenizer\ClassesInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\InvocationLocator;
use Spiral\Tokenizer\InvocationsInterface;
use Spiral\Tokenizer\ScopedClassesInterface;
use Spiral\Tokenizer\ScopedClassLocator;
use Spiral\Tokenizer\Tokenizer;
use WayOfDev\Cycle\Bridge\Laravel\Providers\Registrator;

final class RegisterClassesInterface
{
    public function __invoke(Container $app): void
    {
        $app->singleton(TokenizerConfig::class, static function (Container $app): TokenizerConfig {
            /** @var IlluminateConfig $config */
            $config = $app[IlluminateConfig::class];

            return new TokenizerConfig($config->get(Registrator::CFG_KEY_TOKENIZER));
        });

        $app->singleton(Tokenizer::class, static function (Container $app): Tokenizer {
            return new Tokenizer($app[TokenizerConfig::class]);
        });

        $app->singleton(ScopedClassesInterface::class, static function ($app): ScopedClassesInterface {
            return new ScopedClassLocator($app[TokenizerConfig::class]);
        });

        $app->singleton(ClassesInterface::class, static function ($app): ClassesInterface {
            return (new Tokenizer($app[TokenizerConfig::class]))->classLocator();
        });

        $app->singleton(InvocationsInterface::class, static function ($app): InvocationLocator {
            return new InvocationLocator($app[TokenizerConfig::class]);
        });
    }
}
