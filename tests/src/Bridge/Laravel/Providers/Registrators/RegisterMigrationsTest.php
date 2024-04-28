<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Laravel\Providers\Registrators;

use Cycle\Migrations\Config\MigrationConfig;
use Cycle\Migrations\Migrator;
use Cycle\Migrations\RepositoryInterface;
use Cycle\Schema\Generator\Migrations\NameBasedOnChangesGenerator;
use Cycle\Schema\Generator\Migrations\NameGeneratorInterface;
use Cycle\Schema\Generator\Migrations\Strategy\GeneratorStrategyInterface;
use Cycle\Schema\Generator\Migrations\Strategy\MultipleFilesStrategy;
use Cycle\Schema\Generator\Migrations\Strategy\SingleFileStrategy;
use PHPUnit\Framework\Attributes\Test;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use WayOfDev\Tests\TestCase;

class RegisterMigrationsTest extends TestCase
{
    #[Test]
    public function it_registers_repository_interface_as_singleton(): void
    {
        try {
            $class1 = $this->app->get(RepositoryInterface::class);
            $class2 = $this->app->get(RepositoryInterface::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }

        $this::assertInstanceOf(RepositoryInterface::class, $class1);

        $this::assertSame($class1, $class2);
    }

    #[Test]
    public function it_registers_migrator_as_singleton(): void
    {
        try {
            $class1 = $this->app->get(Migrator::class);
            $class2 = $this->app->get(Migrator::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }

        $this::assertInstanceOf(Migrator::class, $class1);

        $this::assertSame($class1, $class2);
    }

    #[Test]
    public function it_registers_name_generator_interface_as_expected(): void
    {
        $this->app->instance(MigrationConfig::class, new MigrationConfig([
            'nameGenerator' => NameBasedOnChangesGenerator::class,
        ]));

        try {
            $nameGenerator = $this->app->get(NameGeneratorInterface::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }

        $this::assertInstanceOf(NameBasedOnChangesGenerator::class, $nameGenerator);
    }

    #[Test]
    public function it_registers_generator_strategy_interface_as_expected(): void
    {
        $this->app->instance(MigrationConfig::class, new MigrationConfig([
            'strategy' => SingleFileStrategy::class,
        ]));

        try {
            $strategy = $this->app->get(GeneratorStrategyInterface::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }

        $this::assertInstanceOf(SingleFileStrategy::class, $strategy);
    }

    #[Test]
    public function it_registers_generator_strategy_with_multiple_files_strategy_in_config(): void
    {
        config()->set('cycle.migrations.strategy', MultipleFilesStrategy::class);

        try {
            $strategy = $this->app->get(GeneratorStrategyInterface::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this::fail($e->getMessage());
        }

        $this::assertInstanceOf(MultipleFilesStrategy::class, $strategy);
    }
}
