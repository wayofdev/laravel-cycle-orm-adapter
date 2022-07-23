<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Console\Commands\ORM;

use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Registry;
use Illuminate\Console\Command;
use WayOfDev\Cycle\Console\Commands\ORM\Generators\ShowChanges;

use function array_merge;

// @TODO SHOULD BE REFACTORED use Spiral\Cycle\Schema\Compiler;

/**
 * See original spiral framework commands.
 *
 * @see https://github.com/spiral/cycle-bridge/blob/master/src/Console/Command/CycleOrm/SyncCommand.php
 */
final class SyncCommand extends Command
{
    protected $name = 'cycle:orm:sync';

    protected $description = 'Sync Cycle ORM schema with database without intermediate migration (risk operation).';

    public function handle(
        SchemaBootloader $bootloader, // @TODO SHOULD BE REFACTORED
        CycleConfig $config,
        Registry $registry,
        MemoryInterface $memory
    ): int {
        $show = new ShowChanges($this->output);

        // @TODO SHOULD BE REFACTORED
        $schemaCompiler = Compiler::compile(
            $registry,
            array_merge($bootloader->getGenerators($config), [$show, new SyncTables()])
        );
        $schemaCompiler->toMemory($memory);
        // @TODO SHOULD BE REFACTORED

        if ($show->hasChanges()) {
            $this->info('ORM Schema has been synchronized');
        }

        return self::SUCCESS;
    }
}
