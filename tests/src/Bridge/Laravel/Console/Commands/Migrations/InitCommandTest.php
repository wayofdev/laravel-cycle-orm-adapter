<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Laravel\Console\Commands\Migrations;

use Cycle\Database\Database;
use Cycle\Database\DatabaseInterface;
use Illuminate\Support\Facades\Artisan;
use WayOfDev\Tests\TestCase;

class InitCommandTest extends TestCase
{
    /**
     * @test
     */
    public function it_runs_handle(): void
    {
        /** @var Database $database */
        $database = $this->app->make(DatabaseInterface::class);

        $this::assertCount(0, $database->getTables());

        $status = Artisan::call('cycle:migrate:init');

        $this::assertSame(0, $status);
        $this::assertCount(1, $database->getTables());
        $this::assertSame('cycle_migrations', $database->getTables()[0]->getName());
    }
}
