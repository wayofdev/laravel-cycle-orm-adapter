<?php

declare(strict_types=1);

namespace WayOfDev\Tests;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Override;
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

    /**
     * Get a Faker instance.
     */
    final protected static function faker(string $locale = 'en_US'): Generator
    {
        /** @var array<string, Generator> $fakers */
        static $fakers = [];

        if (! array_key_exists($locale, $fakers)) {
            $fakers[$locale] = FakerFactory::create($locale);
        }

        return $fakers[$locale];
    }

    /**
     * Set up the test environment.
     */
    #[Override]
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
                    [__DIR__ . '/../app/Entities'],
                ),
                'cycle.migrations.directory' => $this->migrationsPath,
            ]);
        }
    }

    /**
     * Clean up the test environment.
     */
    #[Override]
    protected function tearDown(): void
    {
        $this->cleanupMigrations($this->migrationsPath . '/*.php');
        $this->refreshDatabase();

        parent::tearDown();
    }

    /**
     * Call an artisan command.
     */
    public function artisanCall(string $command, array $parameters = []): int
    {
        return $this->app[Kernel::class]->call($command, $parameters);
    }

    /**
     * Assert the output of a console command.
     */
    protected function assertConsoleCommandOutput(
        string $command,
        array $args,
        $strings,
        callable $assertionCallback,
    ): void {
        $this->artisanCall($command, $args);
        $output = Artisan::output();

        foreach ((array) $strings as $string) {
            $assertionCallback($string, $output, sprintf(
                'Console command [%s] with args [%s] output assertion failed for string [%s]',
                $command,
                json_encode($args),
                $string
            ));
        }
    }

    /**
     * Assert that the console command output contains the given strings.
     */
    protected function assertConsoleCommandOutputContainsStrings(
        string $command,
        array $args = [],
        $strings = [],
    ): void {
        $this->assertConsoleCommandOutput($command, $args, $strings, function ($string, $output, $message): void {
            $this::assertStringContainsString($string, $output, $message);
        });
    }

    /**
     * Assert that the console command output does not contain the given strings.
     */
    protected function assertConsoleCommandOutputDoesNotContainStrings(
        string $command,
        array $args = [],
        $strings = [],
    ): void {
        $this->assertConsoleCommandOutput($command, $args, $strings, function ($string, $output, $message): void {
            $this::assertStringNotContainsString($string, $output, $message);
        });
    }

    /**
     * Get the package providers for the application.
     */
    #[Override]
    protected function getPackageProviders($app): array
    {
        return [
            CycleServiceProvider::class,
        ];
    }
}
