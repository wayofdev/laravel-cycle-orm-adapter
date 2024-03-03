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
        $this->artisanCall('cycle:orm:sync');
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
        $this->assertConsoleCommandOutputContainsStrings('cycle:orm:sync', ['--verbose' => 3], [
            'default.users',
            'create table',
            'add column',
            'add index',
        ]);

        $u = new User('Antony');
        $this->app->make(EntityManager::class)->persist($u)->run();

        $this::assertSame(1, $u->id);
    }

    /**
     * @test
     */
    public function it_fails_in_production_without_force(): void
    {
        // Set the application environment to production
        config()->set('cycle.migrations.safe', false);

        $warningOutput = 'This operation is not recommended for production environment.';
        $confirmationQuestion = 'Would you like to continue?';

        // Simulate running the command and answering 'no' to the confirmation question
        $this->artisan('cycle:orm:sync')
            ->expectsOutput($warningOutput)
            ->expectsQuestion($confirmationQuestion, false)
            ->expectsOutput('Cancelling operation...')
            ->assertFailed();

        // To test the affirmative path, simulate answering 'yes'
        $this->artisan('cycle:orm:sync')
            ->expectsOutput($warningOutput)
            ->expectsQuestion($confirmationQuestion, 'yes')
            ->assertSuccessful();
    }
}
