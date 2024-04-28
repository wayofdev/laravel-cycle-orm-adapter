<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Laravel\Console\Commands\Database;

use Cycle\Database\Database;
use Cycle\Database\DatabaseInterface;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;
use WayOfDev\Tests\TestCase;

class TableCommandTest extends TestCase
{
    #[Test]
    public function it_runs_handle(): void
    {
        /** @var Database $database */
        $database = $this->app->make(DatabaseInterface::class);

        $tableA = $database->table('sample1')->getSchema();
        $tableA->primary('primary_id');
        $tableA->string('some_string');
        $tableA->index(['some_string'])->setName('custom_index_1');
        $tableA->save();

        $tableB = $database->table('sample')->getSchema();
        $tableB->primary('primary_id');
        $tableB->integer('primary1_id');
        $tableB->foreignKey(['primary1_id'])->references('sample1', ['primary_id']);
        $tableB->integer('some_int');
        $tableB->index(['some_int'])->setName('custom_index');
        $tableB->save();

        $status = $this->artisanCall('cycle:db:table', ['--database' => 'default', 'table' => 'sample']);
        $output = Artisan::output();

        $this::assertSame(0, $status);
        $this::assertStringContainsString('primary_id', $output);
        $this::assertStringContainsString('some_int', $output);
        $this::assertStringContainsString('custom_index', $output);
        $this::assertStringContainsString('sample1', $output);
    }
}
