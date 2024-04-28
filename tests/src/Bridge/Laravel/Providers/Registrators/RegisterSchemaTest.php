<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Laravel\Providers\Registrators;

use Cycle\ORM\Mapper\Mapper;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select\Repository;
use Cycle\ORM\Select\Source;
use Cycle\Schema\Registry;
use Illuminate\Contracts\Container\BindingResolutionException;
use PHPUnit\Framework\Attributes\Test;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use WayOfDev\Cycle\Contracts\CacheManager as CacheManagerContract;
use WayOfDev\Cycle\Contracts\GeneratorLoader;
use WayOfDev\Cycle\Schema\Cache\Manager as CacheManager;
use WayOfDev\Cycle\Schema\Generators\GeneratorQueue;
use WayOfDev\Tests\TestCase;

class RegisterSchemaTest extends TestCase
{
    #[Test]
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
     * @throws BindingResolutionException
     */
    #[Test]
    public function it_registers_generator_loader_as_singleton(): void
    {
        try {
            $class1 = $this->app->get(GeneratorLoader::class);
            $class2 = $this->app->get(GeneratorLoader::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }

        $this::assertSame($class1, $class2);

        $this::assertInstanceOf(GeneratorLoader::class, $class1);
        $this::assertInstanceOf(GeneratorQueue::class, $class1);

        $this::assertCount(14, $class1->get());
    }

    #[Test]
    public function it_registers_schema_interface(): void
    {
        try {
            $class1 = $this->app->get(SchemaInterface::class);
            $class2 = $this->app->get(SchemaInterface::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }

        $this::assertInstanceOf(SchemaInterface::class, $class1);
        $this::assertInstanceOf(Schema::class, $class1);

        $this::assertNotSame($class1, $class2);

        $schema = $class1->toArray();

        $this::assertArrayHasKey('user', $schema);
        $this::assertArrayHasKey('role', $schema);
    }

    #[Test]
    public function it_registers_schema_registry_with_default_config(): void
    {
        try {
            $defaults = $this->app->get(Registry::class)->getDefaults();

            $this::assertSame(Mapper::class, $defaults[SchemaInterface::MAPPER]);
            $this::assertSame(Repository::class, $defaults[SchemaInterface::REPOSITORY]);
            $this::assertSame(Source::class, $defaults[SchemaInterface::SOURCE]);
            $this::assertNull($defaults[SchemaInterface::SCOPE]);
            $this::assertNull($defaults[SchemaInterface::TYPECAST_HANDLER]);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }
    }

    #[Test]
    public function it_registers_schema_registry_with_custom_config(): void
    {
        try {
            config()->set('cycle.schema.defaults', [
                SchemaInterface::TYPECAST_HANDLER => ['foo', 'bar'],
            ]);

            $defaults = $this->app->get(Registry::class)->getDefaults();

            $this::assertSame(Mapper::class, $defaults[SchemaInterface::MAPPER]);
            $this::assertSame(Repository::class, $defaults[SchemaInterface::REPOSITORY]);
            $this::assertSame(Source::class, $defaults[SchemaInterface::SOURCE]);
            $this::assertNull($defaults[SchemaInterface::SCOPE]);
            $this::assertSame(['foo', 'bar'], $defaults[SchemaInterface::TYPECAST_HANDLER]);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }
    }
}
