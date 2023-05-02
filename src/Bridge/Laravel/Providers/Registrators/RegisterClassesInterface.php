<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Providers\Registrators;

use Illuminate\Container\Container;
use Spiral\Tokenizer\ClassesInterface;
use Spiral\Tokenizer\ClassLocator;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\InvocationLocator;
use Spiral\Tokenizer\InvocationsInterface;
use Spiral\Tokenizer\ScopedClassesInterface;
use Spiral\Tokenizer\ScopedClassLocator;
use Spiral\Tokenizer\Tokenizer;

/**
 * @see https://github.com/spiral/tokenizer/blob/master/src/Bootloader/TokenizerBootloader.php
 */
final class RegisterClassesInterface
{
    public function __invoke(Container $app): void
    {
        $app->singleton(Tokenizer::class, static function (Container $app): Tokenizer {
            return new Tokenizer($app[TokenizerConfig::class]);
        });

        $app->bind(ScopedClassesInterface::class, static function ($app): ScopedClassesInterface {
            /** @var Tokenizer $tokenizer */
            $tokenizer = $app->get(Tokenizer::class);

            return new ScopedClassLocator($tokenizer);
        });

        $app->bind(ClassesInterface::class, static function ($app): ClassesInterface {
            /** @var Tokenizer $tokenizer */
            $tokenizer = $app->get(Tokenizer::class);

            return $tokenizer->classLocator();
        });

        $app->bind(InvocationsInterface::class, static function ($app): InvocationLocator {
            /** @var Tokenizer $tokenizer */
            $tokenizer = $app->get(Tokenizer::class);

            return $tokenizer->invocationLocator();
        });

        $app->alias(ClassesInterface::class, ClassLocator::class);
    }
}
