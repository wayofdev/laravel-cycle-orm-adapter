<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Providers\Registrators;

use Illuminate\Contracts\Config\Repository as IlluminateConfig;
use Illuminate\Contracts\Container\Container;
use Spiral\Tokenizer\ClassesInterface;
use Spiral\Tokenizer\ClassLocator;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;
use WayOfDev\Cycle\Bridge\Laravel\Providers\Registrator;

final class RegisterClassLocator
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

        $app->singleton(ClassLocator::class, static function (Container $app): ClassesInterface {
            return $app[Tokenizer::class];
        });

        $app->alias(ClassesInterface::class, ClassLocator::class);
    }
}
