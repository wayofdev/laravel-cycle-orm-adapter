<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Tests;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use WayOfDev\Cycle\Bridge\Laravel\Providers\CycleServiceProvider;

use function array_key_exists;

abstract class TestCase extends Orchestra
{
    final protected static function faker(string $locale = 'en_US'): Generator
    {
        /** @var array<string, Generator> $fakers */
        static $fakers = [];

        if (! array_key_exists($locale, $fakers)) {
            $faker = FakerFactory::create($locale);

            $fakers[$locale] = $faker;
        }

        return $fakers[$locale];
    }

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
