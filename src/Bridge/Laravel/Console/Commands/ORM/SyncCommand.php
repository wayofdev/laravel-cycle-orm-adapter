<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Console\Commands\ORM;

use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Registry;
use Illuminate\Console\Command;
use WayOfDev\Cycle\Bridge\Laravel\Console\Commands\ORM\Generators\ShowChanges;
use WayOfDev\Cycle\Contracts\CacheManager as CacheManagerContract;
use WayOfDev\Cycle\Contracts\GeneratorLoader;
use WayOfDev\Cycle\Schema\Compiler;

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
        Registry $registry,
        CacheManagerContract $cache
    ): int {
        $diff = new ShowChanges($this->output);
        $queue = $generators
            ->add(GeneratorLoader::GROUP_RENDER, $diff)
            ->add(GeneratorLoader::GROUP_POSTPROCESS, new SyncTables());

        $schemaCompiler = Compiler::compile($registry, $queue);
        $schemaCompiler->toMemory($cache);

        if ($diff->hasChanges()) {
            $this->info('ORM Schema has been synchronized!');
        }

        return self::SUCCESS;
    }
}
