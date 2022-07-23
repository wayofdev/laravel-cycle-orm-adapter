<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Console\Commands\ORM;

use WayOfDev\Cycle\Console\Commands\Migrations\AbstractCommand;

/**
 * See original spiral framework commands.
 *
 * @see https://github.com/spiral/cycle-bridge/blob/master/src/Console/Command/Migrate/MigrateCommand.php
 */
final class MigrateCommand extends AbstractCommand
{
    protected $name = 'cycle:orm:migrate
                       {--r|run : Automatically run generated migration. }';

    protected $description = 'Generate ORM schema migrations.';

    public function handle(): int
    {
    }
}
