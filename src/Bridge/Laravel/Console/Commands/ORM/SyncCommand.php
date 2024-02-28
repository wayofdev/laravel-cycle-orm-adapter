<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Console\Commands\ORM;

use Cycle\Schema\Generator\PrintChanges;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Registry;
use WayOfDev\Cycle\Bridge\Laravel\Console\Commands\Migrations\AbstractCommand;
use WayOfDev\Cycle\Contracts\CacheManager as CacheManagerContract;
use WayOfDev\Cycle\Contracts\GeneratorLoader;
use WayOfDev\Cycle\Schema\Compiler;

/**
 * See original spiral framework commands.
 *
 * @see https://github.com/spiral/cycle-bridge/blob/master/src/Console/Command/CycleOrm/SyncCommand.php
 */
final class SyncCommand extends AbstractCommand
{
    protected $signature = 'cycle:orm:sync';

    protected $description = 'Sync Cycle ORM schema with database without intermediate migration (risk operation).';

    public function handle(
        GeneratorLoader $generators,
        Registry $registry,
        CacheManagerContract $cache
    ): int {
        if (! $this->verifyEnvironment('This operation is not recommended for production environment.')) {
            return self::FAILURE;
        }

        $diff = new PrintChanges($this->output);
        $queue = $generators
            ->add(GeneratorLoader::GROUP_RENDER, $diff)
            ->add(GeneratorLoader::GROUP_POSTPROCESS, new SyncTables());

        $schemaCompiler = Compiler::compile($registry, $queue);
        $schemaCompiler->toMemory($cache);

        if ($diff->hasChanges()) {
            $this->info('ORM Schema has been synchronized with database.');
        }

        return self::SUCCESS;
    }
}
