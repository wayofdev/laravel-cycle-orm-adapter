<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Console\Commands\Migrations;

/**
 * See original spiral framework commands.
 *
 * @see https://github.com/spiral/cycle-bridge/blob/master/src/Console/Command/Migrate/InitCommand.php
 */
final class InitCommand extends AbstractCommand
{
    protected $name = 'cycle:migrate:init';

    protected $description = 'Create the cycle orm migrations table.';

    public function handle(): int
    {
        if (! $this->migrator->isConfigured()) {
            $this->migrator->configure();
            $this->info('Cycle migrations table was successfully configured.');
        } else {
            $this->info('Cycle migrations table already configured.');
        }

        return self::SUCCESS;
    }
}
