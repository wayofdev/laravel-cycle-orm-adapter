<?php

declare(strict_types=1);

namespace WayOfDev\Tests;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Spatie\LaravelRay\RayServiceProvider;
use WayOfDev\Cycle\Bridge\Laravel\Providers\CycleServiceProvider;
use WayOfDev\Cycle\Testing\Concerns\InteractsWithDatabase;
use WayOfDev\Cycle\Testing\RefreshDatabase;

use function array_key_exists;
use function array_merge;
use function json_encode;
use function sprintf;

/**
 * @see https://cycle-orm.dev/docs/advanced-testing/2.x/en
 */
class TestCase extends OrchestraTestCase
{
    use InteractsWithDatabase;
    use RefreshDatabase;

    protected ?string $migrationsPath = null;

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

        $this->migrationsPath = __DIR__ . '/../app/database/migrations/cycle';
        $this->cleanupMigrations($this->migrationsPath . '/*.php');
        $this->refreshDatabase();

        if (app()->environment() === 'testing') {
            config()->set([
                'cycle.tokenizer.directories' => array_merge(
                    config('cycle.tokenizer.directories'),
                    [__DIR__ . '/../app/Entities']
                ),
                'cycle.migrations.directory' => $this->migrationsPath,
            ]);
        }
    }

    protected function tearDown(): void
    {
        $this->cleanupMigrations($this->migrationsPath . '/*.php');
        $this->refreshDatabase();

        parent::tearDown();
    }

    public function artisanCall(string $command, array $parameters = [])
    {
        return $this->app[Kernel::class]->call($command, $parameters);
    }

    protected function assertConsoleCommandOutputContainsStrings(
        string $command,
        array $args = [],
        array|string $strings = []
    ): void {
        $this->artisanCall($command, $args);
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
}
