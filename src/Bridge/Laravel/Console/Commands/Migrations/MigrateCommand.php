<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Console\Commands\Migrations;

use function sprintf;

/**
 * See original spiral framework commands.
 *
 * @see https://github.com/spiral/cycle-bridge/blob/master/src/Console/Command/Migrate/MigrateCommand.php
 */
final class MigrateCommand extends AbstractCommand
{
    protected $signature = 'cycle:migrate
                           {--o|one : Execute only one (first) migration. }
                           {--seed : Indicates if the seed task should be re-run }';

    protected $description = 'Execute one or multiple migrations.';

    public function handle(): int
    {
        if (! $this->verifyEnvironment()) {
            return self::FAILURE;
        }

        if (! $this->migrator->isConfigured()) {
            $this->migrator->configure();
        }

        $found = false;
        $count = $this->option('one') ? 1 : PHP_INT_MAX;

        while (0 < $count && null !== ($migration = $this->migrator->run())) {
            $found = true;
            --$count;

            $this->line(
                sprintf('Migration <comment>%s</comment> was successfully executed.', $migration->getState()->getName()),
                'info'
            );
        }

        if (! $found) {
            $this->warn('No outstanding migrations were found.');

            return self::SUCCESS;
        }

        if ($this->option('seed')) {
            $this->call('db:seed', ['--force' => $this->isForced()]);
        }

        return self::SUCCESS;
    }
}
