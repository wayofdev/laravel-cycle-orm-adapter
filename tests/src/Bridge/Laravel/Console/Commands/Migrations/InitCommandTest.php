<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Laravel\Console\Commands\Migrations;

use Cycle\Database\Database;
use Cycle\Database\DatabaseInterface;
use PHPUnit\Framework\Attributes\Test;
use WayOfDev\Tests\TestCase;

class InitCommandTest extends TestCase
{
    #[Test]
    public function it_runs_handle(): void
    {
        /** @var Database $database */
        $database = $this->app->make(DatabaseInterface::class);

        $this::assertCount(0, $database->getTables());

        $status = $this->artisanCall('cycle:migrate:init');

        $this::assertSame(0, $status, 'Command exit status should be 0');
        $this::assertCount(1, $database->getTables());
        $this::assertStringContainsString('cycle_migrations', $database->getTables()[0]->getName());
    }
}
