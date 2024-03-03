<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Laravel\Console\Commands\Migrations;

use Cycle\Database\Database;
use Cycle\Database\DatabaseInterface;
use WayOfDev\Tests\TestCase;

class MigrateCommandTest extends TestCase
{
    /**
     * @test
     */
    public function it_migrates_in_safe_environment(): void
    {
        /** @var Database $database */
        $database = $this->app->make(DatabaseInterface::class);

        $this::assertSame([], $database->getTables());

        $this->artisanCall('cycle:migrate:init');
        $this->artisanCall('cycle:orm:migrate');

        // @phpstan-ignore-next-line
        $this::assertCount(1, $database->getTables());
    }

    /**
     * @test
     */
    public function it_migrates_in_production_with_force_option(): void
    {
        // Set the application environment to production
        config()->set('cycle.migrations.safe', false);

        /** @var Database $database */
        $database = $this->app->make(DatabaseInterface::class);

        $this::assertSame([], $database->getTables());

        $this->artisanCall('cycle:migrate:init');
        $this->artisanCall('cycle:orm:migrate', ['--force' => true]);

        // @phpstan-ignore-next-line
        $this::assertCount(1, $database->getTables());

        $this->artisanCall('cycle:migrate', ['--force' => true]);
        $this::assertCount(4, $database->getTables());
    }

    /**
     * @test
     */
    public function it_warns_about_unsafe_migrations(): void
    {
        // Set the application environment to production
        config()->set('cycle.migrations.safe', false);

        $this->artisanCall('cycle:migrate:init');

        // Simulate running the command and answering 'no' to the confirmation question
        $this->artisan('cycle:migrate')
            ->expectsOutput('Confirmation is required to run migrations!')
            ->expectsQuestion('Would you like to continue?', false)
            ->expectsOutput('Cancelling operation...')
            ->assertFailed();
    }
}
