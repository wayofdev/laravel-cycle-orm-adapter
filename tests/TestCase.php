<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use WayOfDev\Cycle\Bridge\Laravel\Providers\CycleServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            static fn (string $modelName) => 'WayOfDev\\Laravel\\Cycle\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
    }

    protected function getPackageProviders($app): array
    {
        return [
            CycleServiceProvider::class,
        ];
    }
}
