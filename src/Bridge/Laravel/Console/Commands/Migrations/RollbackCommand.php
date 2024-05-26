<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Console\Commands\Migrations;

use Throwable;

use function sprintf;

/**
 * See original spiral framework commands.
 *
 * @see https://github.com/spiral/cycle-bridge/blob/master/src/Console/Command/Migrate/RollbackCommand.php
 */
final class RollbackCommand extends AbstractCommand
{
    protected $signature = 'cycle:migrate:rollback
                           {--a|all : Rollback all executed migrations. }';

    protected $description = 'Rollback one (default) or multiple migrations.';

    public function handle(): int
    {
        if (! $this->verifyEnvironment()) {
            return self::FAILURE;
        }

        if (! $this->migrator->isConfigured()) {
            $this->migrator->configure();
        }

        $found = false;
        $count = ! $this->option('all') ? 1 : PHP_INT_MAX;
        try {
            while ($count > 0 && null !== ($migration = $this->migrator->rollback())) {
                $found = true;
                --$count;
                $this->line(
                    sprintf('Migration <comment>%s</comment> was successfully rolled back.', $migration->getState()->getName()),
                    'info'
                );
            }
        } catch (Throwable $e) {
            $this->error('Failed to rollback migration(s): ' . $e->getMessage());
        }

        if (! $found) {
            $this->warn('No outstanding migrations were found.');
        }

        return self::SUCCESS;
    }
}
