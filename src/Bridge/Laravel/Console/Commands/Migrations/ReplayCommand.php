<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Console\Commands\Migrations;

/**
 * See original spiral framework commands.
 *
 * @see https://github.com/spiral/cycle-bridge/blob/master/src/Console/Command/Migrate/ReplayCommand.php
 */
final class ReplayCommand extends AbstractCommand
{
    protected $signature = 'cycle:migrate:replay
                           {--a|all : Replay all migrations. }';

    protected $description = 'Replay (down, up) one or multiple migrations.';

    public function handle(): int
    {
        if (! $this->verifyEnvironment()) {
            return self::FAILURE;
        }

        $rollback = ['--force' => $this->option('force')];
        $migrate = ['--force' => $this->option('force')];

        if ($this->option('all')) {
            $rollback['--all'] = true;
        } else {
            $migrate['--one'] = true;
        }

        $this->info('Rolling back executed migration(s)...');
        $this->call(RollbackCommand::class, $rollback);

        $this->info('');

        $this->info('Executing outstanding migration(s)...');
        $this->call(MigrateCommand::class, $migrate);

        return self::SUCCESS;
    }
}
