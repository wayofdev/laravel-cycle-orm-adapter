<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Providers\Registrators;

use Cycle\Annotated;
use Illuminate\Contracts\Foundation\Application;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\ClassesInterface;

/**
 * @see https://github.com/spiral/cycle-bridge/blob/2.x/src/Bootloader/AnnotatedBootloader.php
 */
final class RegisterAnnotated
{
    public function __invoke(Application $app): void
    {
        $app->singleton(Annotated\Locator\EmbeddingLocatorInterface::class, function ($app) {
            return new Annotated\Locator\TokenizerEmbeddingLocator(
                $app->get(ClassesInterface::class),
                $app->get(ReaderInterface::class)
            );
        });

        $app->singleton(Annotated\Locator\EntityLocatorInterface::class, function ($app) {
            return new Annotated\Locator\TokenizerEntityLocator(
                $app->get(ClassesInterface::class),
                $app->get(ReaderInterface::class)
            );
        });

        $app->bind(Annotated\Embeddings::class, function ($app) {
            return new Annotated\Embeddings(
                $app->get(Annotated\Locator\EmbeddingLocatorInterface::class),
                $app->get(ReaderInterface::class)
            );
        });

        $app->bind(Annotated\Entities::class, function ($app) {
            return new Annotated\Entities(
                $app->get(Annotated\Locator\EntityLocatorInterface::class),
                $app->get(ReaderInterface::class)
            );
        });

        $app->bind(Annotated\MergeColumns::class, function ($app) {
            return new Annotated\MergeColumns(
                $app->get(ReaderInterface::class)
            );
        });

        $app->bind(Annotated\TableInheritance::class, function ($app) {
            return new Annotated\TableInheritance(
                $app->get(ReaderInterface::class)
            );
        });

        $app->bind(Annotated\MergeIndexes::class, function ($app) {
            return new Annotated\MergeIndexes(
                $app->get(ReaderInterface::class)
            );
        });
    }
}
