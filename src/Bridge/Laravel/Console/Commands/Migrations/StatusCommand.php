<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Console\Commands\Migrations;

use Cycle\Migrations\State;

use function count;

/**
 * See original spiral framework commands.
 *
 * @see https://github.com/spiral/cycle-bridge/blob/master/src/Console/Command/Migrate/StatusCommand.php
 */
final class StatusCommand extends AbstractCommand
{
    private const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    private const PENDING = '<fg=red>not executed yet</fg=red>';

    protected $signature = 'cycle:migrate:status';

    protected $description = 'Get list of all available migrations and their statuses.';

    public function handle(): int
    {
        if (! $this->migrator->isConfigured()) {
            $this->migrator->configure();
        }

        if (count($this->migrator->getMigrations()) === 0) {
            $this->comment('No migrations were found.');

            return self::SUCCESS;
        }

        $rows = [];

        foreach ($this->migrator->getMigrations() as $migration) {
            $state = $migration->getState();

            $rows[] = [
                $state->getName(),
                $state->getTimeCreated()->format('Y-m-d H:i:s'),
                $state->getStatus() === State::STATUS_PENDING
                    ? self::PENDING
                    : '<info>' . $state->getTimeExecuted()?->format(self::DATE_TIME_FORMAT) . '</info>',
            ];
        }

        $this->table(['Migration', 'Created at', 'Executed at'], $rows);

        return self::SUCCESS;
    }
}
