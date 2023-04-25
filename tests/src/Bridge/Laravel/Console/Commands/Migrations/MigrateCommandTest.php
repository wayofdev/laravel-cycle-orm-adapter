<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Laravel\Console\Commands\Migrations;

use Cycle\Database\Database;
use Cycle\Database\DatabaseInterface;
use Illuminate\Support\Facades\Artisan;
use WayOfDev\Tests\TestCase;

class MigrateCommandTest extends TestCase
{
    /**
     * @test
     */
    public function it_runs_handle(): void
    {
        /** @var Database $database */
        $database = $this->app->make(DatabaseInterface::class);

        $this::assertSame([], $database->getTables());

        Artisan::call('cycle:migrate:init');
        Artisan::call('cycle:orm:migrate', ['--force' => true]);

        $this::assertCount(1, $database->getTables());

        // @todo Add Entities and uncomment this code
        // Artisan::call('cycle:migrate', ['--force' => true]);
        // $this::assertCount(4, $database->getTables());
    }
}
