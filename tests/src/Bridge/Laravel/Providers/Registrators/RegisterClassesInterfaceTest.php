<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Laravel\Providers\Registrators;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spiral\Tokenizer\ClassesInterface;
use Spiral\Tokenizer\ClassLocator;
use Spiral\Tokenizer\InvocationsInterface;
use Spiral\Tokenizer\ScopedClassesInterface;
use Spiral\Tokenizer\Tokenizer;
use WayOfDev\Tests\TestCase;

class RegisterClassesInterfaceTest extends TestCase
{
    /**
     * @test
     */
    public function it_registers_tokenizer_as_singleton(): void
    {
        try {
            $tokenizer1 = $this->app->get(Tokenizer::class);
            $tokenizer2 = $this->app->get(Tokenizer::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }

        $this::assertInstanceOf(
            Tokenizer::class,
            $tokenizer1
        );

        $this::assertSame($tokenizer1, $tokenizer2);
    }

    /**
     * @test
     */
    public function it_binds_scoped_classes_interface(): void
    {
        try {
            $class = $this->app->get(ScopedClassesInterface::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }

        $this::assertInstanceOf(ScopedClassesInterface::class, $class);
    }

    /**
     * @test
     */
    public function it_binds_classes_interface(): void
    {
        try {
            $class = $this->app->get(ClassesInterface::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }

        $this::assertInstanceOf(ClassesInterface::class, $class);
    }

    /**
     * @test
     */
    public function it_binds_invocations_interface(): void
    {
        try {
            $class = $this->app->get(InvocationsInterface::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }

        $this::assertInstanceOf(InvocationsInterface::class, $class);
    }

    /**
     * @test
     */
    public function it_aliases_classes_interface_to_class_locator(): void
    {
        try {
            $class = $this->app->get(ClassesInterface::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }

        $this::assertInstanceOf(ClassLocator::class, $class);
    }
}
