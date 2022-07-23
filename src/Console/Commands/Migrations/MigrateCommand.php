<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Console\Commands\Migrations;

use function sprintf;

/**
 * See original spiral framework commands.
 *
 * @see https://github.com/spiral/cycle-bridge/blob/master/src/Console/Command/Migrate/MigrateCommand.php
 */
final class MigrateCommand extends AbstractCommand
{
    protected $name = 'cycle:migrate
                       {--o|one : Execute only one (first) migration. }
                       {--seed : Indicates if the seed task should be re-run }';

    protected $description = 'Perform one or all outstanding migrations.';

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

        while (0 < $count && ($migration = $this->migrator->run())) {
            $found = true;
            --$count;

            $this->writeln(
                sprintf(
                    '<info>Migration <comment>%s</comment> was successfully executed.</info>',
                    $migration->getState()->getName()
                )
            );
        }

        if (! $found) {
            $this->writeln('<fg=red>No outstanding migrations were found.</fg=red>');
        }

        if ($this->option('seed')) {
            $this->call('db:seed', ['--force' => $this->option('force')]);
        }

        return self::SUCCESS;
    }
}
