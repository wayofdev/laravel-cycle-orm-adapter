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

        $this->artisanCall('cycle:migrate:init');

        $this->artisanCall('cycle:migrate:status');
        $output = Artisan::output();
        $this::assertStringContainsString('No migrations', $output);

        $this->artisanCall('cycle:orm:migrate');
        $this::assertCount(1, $database->getTables());

        $this->artisanCall('cycle:migrate:status');
        $output = Artisan::output();
        $this::assertStringContainsString('not executed yet', $output);

        $this->artisanCall('cycle:migrate', ['--force' => true]);
        $this::assertCount(3, $database->getTables());

        $this->artisanCall('cycle:migrate:status');
        $output2 = Artisan::output();
        $this::assertNotSame($output, $output2);
    }
}
