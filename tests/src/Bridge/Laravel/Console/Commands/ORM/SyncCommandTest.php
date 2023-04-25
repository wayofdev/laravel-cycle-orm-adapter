<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Laravel\Console\Commands\ORM;

use Cycle\ORM\EntityManager;
use Illuminate\Support\Facades\Artisan;
use Throwable;
use WayOfDev\App\Entities\User;
use WayOfDev\Tests\TestCase;

class SyncCommandTest extends TestCase
{
    /**
     * @test
     *
     * @throws Throwable
     */
    public function it_runs_handle(): void
    {
        Artisan::call('cycle:orm:sync');
        $output = Artisan::output();

        $this::assertStringContainsString('default.users', $output);

        $u = new User('Antony');
        $this->app->make(EntityManager::class)->persist($u)->run();

        $this::assertSame(1, $u->id);
    }

    /**
     * @test
     *
     * @throws Throwable
     */
    public function it_runs_handle_in_debug_mode(): void
    {
        $this->assertConsoleCommandOutputContainsStrings('cycle:orm:sync', ['-vvv'], [
            'default.users',
            'create table',
            'add column',
            'add index',
        ]);

        $u = new User('Antony');
        $this->app->make(EntityManager::class)->persist($u)->run();

        $this::assertSame(1, $u->id);
    }
}
