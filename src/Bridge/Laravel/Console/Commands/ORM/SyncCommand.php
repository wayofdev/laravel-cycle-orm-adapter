<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Console\Commands\ORM;

use Cycle\Schema\Generator\SyncTables;
use Illuminate\Console\Command;
use WayOfDev\Cycle\Bridge\Laravel\Console\Commands\ORM\Generators\ShowChanges;
use WayOfDev\Cycle\Contracts\GeneratorLoader;
use WayOfDev\Cycle\Contracts\SchemaCompiler;

/**
 * See original spiral framework commands.
 *
 * @see https://github.com/spiral/cycle-bridge/blob/master/src/Console/Command/CycleOrm/SyncCommand.php
 */
final class SyncCommand extends Command
{
    protected $signature = 'cycle:orm:sync';

    protected $description = 'Sync Cycle ORM schema with database without intermediate migration (risk operation).';

    public function handle(
        GeneratorLoader $generators,
        SchemaCompiler $schemaCompiler,
        SyncTables $syncTablesGenerator
    ): int {
        $diff = new ShowChanges($this->output);

        $generators = $generators->add(GeneratorLoader::GROUP_POSTPROCESS, $diff)
            ->add(GeneratorLoader::GROUP_POSTPROCESS, $syncTablesGenerator);

        $schemaCompiler->compile($generators->get());

        if ($diff->hasChanges()) {
            $this->info('ORM Schema has been synchronized!');
        }

        return self::SUCCESS;
    }
}
