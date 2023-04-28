<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Providers\Registrators;

use Cycle\Annotated;
use Illuminate\Container\Container;
use Spiral\Attributes\Factory;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\ClassesInterface;

final class RegisterAnnotated
{
    public function __invoke(Container $app): void
    {
        $app->bind(ReaderInterface::class, function () {
            return (new Factory())->create();
        });

        $app->bind(Annotated\Embeddings::class, function ($app) {
            return new Annotated\Embeddings(
                $app[ClassesInterface::class],
                $app[ReaderInterface::class]
            );
        });

        $app->bind(Annotated\Entities::class, function ($app) {
            return new Annotated\Entities(
                $app[ClassesInterface::class],
                $app[ReaderInterface::class]
            );
        });

        $app->bind(Annotated\MergeColumns::class, function ($app) {
            return new Annotated\MergeColumns(
                $app[ReaderInterface::class]
            );
        });

        $app->bind(Annotated\TableInheritance::class, function ($app) {
            return new Annotated\TableInheritance(
                $app[ReaderInterface::class]
            );
        });

        $app->bind(Annotated\MergeIndexes::class, function ($app) {
            return new Annotated\MergeIndexes(
                $app[ReaderInterface::class]
            );
        });
    }
}
