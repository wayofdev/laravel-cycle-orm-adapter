<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Laravel\Console\Commands\Database;

use Cycle\Database\Database;
use Cycle\Database\DatabaseInterface;
use Illuminate\Support\Facades\Artisan;
use WayOfDev\Tests\TestCase;

class ListCommandTest extends TestCase
{
    /**
     * @test
     */
    public function it_runs_handle(): void
    {
        /** @var Database $database */
        $database = $this->app->make(DatabaseInterface::class);

        $tableA = $database->table('clients')->getSchema();
        $tableA->primary('primary_id');
        $tableA->string('name');
        $tableA->index(['name'])->setName('custom_index');
        $tableA->integer('b_id');
        $tableA->foreignKey(['b_id'])->references('posts', ['id']);
        $tableA->save();

        $tableB = $database->table('posts')->getSchema();
        $tableB->primary('id');
        $tableB->save();

        $tableC = $database->table('comments')->getSchema();
        $tableC->primary('id');
        $tableC->save();

        $status = Artisan::call('cycle:db:list', ['db' => 'default']);
        $output = Artisan::output();

        $this::assertSame(0, $status);
        $this::assertStringContainsString('SQLite', $output);
        $this::assertStringContainsString('memory', $output);
        $this::assertStringContainsString('clients', $output);
        $this::assertStringContainsString('posts', $output);
        $this::assertStringContainsString('comments', $output);
    }
}
