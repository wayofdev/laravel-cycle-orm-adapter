<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Laravel\Console\Commands\Migrations;

use Cycle\Database\Database;
use Cycle\Database\DatabaseInterface;
use Illuminate\Support\Facades\Artisan;
use WayOfDev\Tests\TestCase;

class StatusCommandTest extends TestCase
{
    /**
     * @test
     */
    public function it_runs_handle(): void
    {
        /** @var Database $database */
        $database = $this->app->make(DatabaseInterface::class);
        $this::assertCount(0, $database->getTables());

        $this::assertConsoleCommandOutputContainsStrings('cycle:migrate:status', [], 'No migrations');

        Artisan::call('cycle:migrate:init');

        Artisan::call('cycle:migrate:status');
        $output = Artisan::output();
        $this::assertStringContainsString('No migrations', $output);

        Artisan::call('cycle:orm:migrate');
        $this::assertCount(1, $database->getTables());

        Artisan::call('cycle:migrate:status');
        $output = Artisan::output();
        $this::assertStringContainsString('not executed yet', $output);

        Artisan::call('cycle:migrate', ['--force' => true]);
        $this::assertCount(3, $database->getTables());

        Artisan::call('cycle:migrate:status');
        $output2 = Artisan::output();
        $this::assertNotSame($output, $output2);
    }
}
