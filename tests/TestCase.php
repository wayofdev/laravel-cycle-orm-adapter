<?php

declare(strict_types=1);

namespace WayOfDev\Laravel\Package\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use WayOfDev\Laravel\Package\PackageServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            static fn (string $modelName) => 'WayOfDev\\Laravel\\Package\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_package_table.php.stub';
        $migration->up();
        */
    }

    protected function getPackageProviders($app): array
    {
        return [
            PackageServiceProvider::class,
        ];
    }
}
