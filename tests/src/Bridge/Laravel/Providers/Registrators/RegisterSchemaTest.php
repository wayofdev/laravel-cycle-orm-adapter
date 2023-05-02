<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Laravel\Providers\Registrators;

use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use WayOfDev\Cycle\Contracts\CacheManager as CacheManagerContract;
use WayOfDev\Cycle\Contracts\GeneratorLoader;
use WayOfDev\Cycle\Schema\Cache\Manager as CacheManager;
use WayOfDev\Cycle\Schema\Generators\GeneratorQueue;
use WayOfDev\Tests\TestCase;

class RegisterSchemaTest extends TestCase
{
    /**
     * @test
     */
    public function it_registers_cache_manager_as_singleton(): void
    {
        try {
            $class1 = $this->app->get(CacheManagerContract::class);
            $class2 = $this->app->get(CacheManagerContract::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }

        $this::assertInstanceOf(CacheManagerContract::class, $class1);
        $this::assertInstanceOf(CacheManager::class, $class1);

        $this::assertSame($class1, $class2);
    }

    /**
     * @test
     *
     * @throws BindingResolutionException
     */
    public function it_registers_generator_loader_as_singleton(): void
    {
        try {
            $class1 = $this->app->get(GeneratorLoader::class);
            $class2 = $this->app->get(GeneratorLoader::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }

        $this::assertInstanceOf(GeneratorLoader::class, $class1);
        $this::assertInstanceOf(GeneratorQueue::class, $class1);

        $this::assertCount(13, $class1->get());
    }

    /**
     * @test
     */
    public function it_registers_schema_interface(): void
    {
        try {
            $class1 = $this->app->get(SchemaInterface::class);
            // $class2 = $this->app->get(SchemaInterface::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }

        $this::assertInstanceOf(SchemaInterface::class, $class1);
        $this::assertInstanceOf(Schema::class, $class1);

        // $this::assertSame($class1, $class2);

        $schema = $class1->toArray();

        $this::assertArrayHasKey('user', $schema);
        $this::assertArrayHasKey('role', $schema);
    }
}
