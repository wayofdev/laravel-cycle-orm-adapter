<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Console\Commands\ORM;

use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Registry;
use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use WayOfDev\Cycle\Bridge\Laravel\Console\Commands\ORM\Generators\ShowChanges;
use WayOfDev\Cycle\Bridge\Laravel\Providers\Registrators\RegisterSchema;
use WayOfDev\Cycle\Contracts\CacheManager as CacheManagerContract;
use WayOfDev\Cycle\Contracts\Config\Repository as Config;
use WayOfDev\Cycle\Schema\Compiler;

use function array_merge;

/**
 * See original spiral framework commands.
 *
 * @see https://github.com/spiral/cycle-bridge/blob/master/src/Console/Command/CycleOrm/SyncCommand.php
 */
final class SyncCommand extends Command
{
    protected $signature = 'cycle:orm:sync';

    protected $description = 'Sync Cycle ORM schema with database without intermediate migration (risk operation).';

    /**
     * @throws BindingResolutionException
     */
    public function handle(
        Container $app,
        RegisterSchema $bootloader,
        Registry $registry,
        Config $config,
        CacheManagerContract $cache
    ): int {
        $diff = new ShowChanges($this->output);

        $schemaCompiler = Compiler::compile(
            $registry,
            array_merge(
                $bootloader->getGenerators($app, $config),
                [$diff, new SyncTables()]
            )
        );
        $schemaCompiler->toMemory($cache);

        if ($diff->hasChanges()) {
            $this->info('ORM Schema has been synchronized!');
        }

        return self::SUCCESS;
    }
}
