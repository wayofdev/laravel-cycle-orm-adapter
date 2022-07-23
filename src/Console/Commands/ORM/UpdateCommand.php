<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Console\Commands\ORM;

use Illuminate\Console\Command;

/**
 * See original spiral framework commands.
 *
 * @see https://github.com/spiral/cycle-bridge/blob/master/src/Console/Command/CycleOrm/UpdateCommand.php
 */
final class UpdateCommand extends Command
{
    protected $name = 'cycle:orm';

    protected $description = 'Sync Cycle ORM schema with database without intermediate migration (risk operation).';

    public function handle(): int
    {
        $this->write('Updating ORM schema... ');

        SchemaCompiler::compile(
            $registry,
            $bootloader->getGenerators($config)
        )->toMemory($memory);

        $this->writeln('<info>done</info>');

        return self::SUCCESS;
    }
}
