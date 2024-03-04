<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Laravel\Providers\Registrators;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spiral\Attributes\AttributeReader;
use Spiral\Attributes\Internal\Instantiator\InstantiatorInterface;
use Spiral\Attributes\Psr16CachedReader;
use Spiral\Attributes\ReaderInterface;
use WayOfDev\Tests\TestCase;

class RegisterAttributesTest extends TestCase
{
    /**
     * @test
     */
    public function it_registers_instantiator_interface_as_singleton(): void
    {
        try {
            $instantiator1 = $this->app->get(InstantiatorInterface::class);
            $instantiator2 = $this->app->get(InstantiatorInterface::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }

        $this::assertInstanceOf(
            InstantiatorInterface::class,
            $instantiator1
        );

        $this::assertSame($instantiator1, $instantiator2);
    }

    /**
     * @test
     */
    public function it_uses_cached_reader(): void
    {
        try {
            $reader = $this->app->get(ReaderInterface::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }

        $this::assertInstanceOf(Psr16CachedReader::class, $reader);
    }

    /**
     * @test
     */
    public function it_uses_attribute_reader_if_cache_disabled(): void
    {
        config()->set('cycle.attributes.cache.enabled', false);

        try {
            $reader = $this->app->get(ReaderInterface::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }

        $this::assertInstanceOf(AttributeReader::class, $reader);
    }
}
