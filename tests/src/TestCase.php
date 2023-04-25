<?php

declare(strict_types=1);

namespace WayOfDev\Tests;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Spatie\LaravelRay\RayServiceProvider;
use WayOfDev\Cycle\Bridge\Laravel\Providers\CycleServiceProvider;

use function array_key_exists;
use function array_merge;
use function json_encode;
use function sprintf;

class TestCase extends OrchestraTestCase
{
    final protected static function faker(string $locale = 'en_US'): Generator
    {
        /** @var array<string, Generator> $fakers */
        static $fakers = [];

        if (! array_key_exists($locale, $fakers)) {
            $fakers[$locale] = FakerFactory::create($locale);
        }

        return $fakers[$locale];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->cleanupMigrations();

        Factory::guessFactoryNamesUsing(
            static fn (string $modelName) => 'WayOfDev\\Laravel\\Cycle\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );

        if (app()->environment() === 'testing') {
            config()->set([
                'cycle.tokenizer.directories' => array_merge(
                    config('cycle.tokenizer.directories'),
                    [__DIR__ . '/../app/Entities']
                ),
            ]);
        }
    }

    protected function tearDown(): void
    {
        $this->cleanupMigrations();

        parent::tearDown();
    }

    protected function assertConsoleCommandOutputContainsStrings(
        string $command,
        array $args = [],
        array|string $strings = [],
        ?int $verbosityLevel = null
    ): void {
        Artisan::call($command, $args);
        $output = Artisan::output();

        foreach ((array) $strings as $string) {
            $this::assertStringContainsString(
                $string,
                $output,
                sprintf(
                    'Console command [%s] with args [%s] does not contain string [%s]',
                    $command,
                    json_encode($args),
                    $string
                )
            );
        }
    }

    protected function getPackageProviders($app): array
    {
        return [
            CycleServiceProvider::class,
            RayServiceProvider::class,
        ];
    }

    protected function cleanupMigrations(): void
    {
        $files = File::glob(database_path('migrations/*.php'));
        foreach ($files as $file) {
            File::delete($file);
        }
    }
}
