<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Providers\Registrators;

use Cycle\Annotated;
use Illuminate\Container\Container;
use Spiral\Attributes\Factory;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\ClassesInterface;

/**
 * @see https://github.com/spiral/cycle-bridge/blob/2.0/src/Bootloader/AnnotatedBootloader.php
 */
final class RegisterAnnotated
{
    public function __invoke(Container $app): void
    {
        $app->bind(ReaderInterface::class, function () {
            return (new Factory())->create();
        });

        $app->bind(Annotated\Embeddings::class, function ($app) {
            return new Annotated\Embeddings(
                $app->get(ClassesInterface::class),
                $app->get(ReaderInterface::class)
            );
        });

        $app->bind(Annotated\Entities::class, function ($app) {
            return new Annotated\Entities(
                $app->get(ClassesInterface::class),
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
