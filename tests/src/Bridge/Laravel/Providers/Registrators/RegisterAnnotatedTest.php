<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Laravel\Providers\Registrators;

use Cycle\Annotated\Embeddings;
use Cycle\Annotated\Entities;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\TableInheritance;
use Cycle\Schema\GeneratorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spiral\Attributes\ReaderInterface;
use WayOfDev\Tests\TestCase;

class RegisterAnnotatedTest extends TestCase
{
    /**
     * @test
     */
    public function it_binds_reader_interface(): void
    {
        try {
            $this::assertInstanceOf(ReaderInterface::class, $this->app->get(ReaderInterface::class));
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_binds_embeddings(): void
    {
        try {
            $this::assertInstanceOf(GeneratorInterface::class, $this->app->get(Embeddings::class));
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_binds_entities(): void
    {
        try {
            $this::assertInstanceOf(GeneratorInterface::class, $this->app->get(Entities::class));
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_binds_merge_columns(): void
    {
        try {
            $this::assertInstanceOf(GeneratorInterface::class, $this->app->get(MergeColumns::class));
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_binds_table_inheritance(): void
    {
        try {
            $this::assertInstanceOf(GeneratorInterface::class, $this->app->get(TableInheritance::class));
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_binds_merge_indexes(): void
    {
        try {
            $this::assertInstanceOf(GeneratorInterface::class, $this->app->get(MergeIndexes::class));
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }
    }
}
