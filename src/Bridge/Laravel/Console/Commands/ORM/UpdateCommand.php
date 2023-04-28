<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Console\Commands\ORM;

use Cycle\Schema\Registry;
use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use WayOfDev\Cycle\Bridge\Laravel\Providers\Registrators\RegisterSchema;
use WayOfDev\Cycle\Contracts\CacheManager as CacheManagerContract;
use WayOfDev\Cycle\Contracts\Config\Repository as Config;
use WayOfDev\Cycle\Schema\Compiler;

/**
 * See original spiral framework commands.
 *
 * @see https://github.com/spiral/cycle-bridge/blob/master/src/Console/Command/CycleOrm/UpdateCommand.php
 */
final class UpdateCommand extends Command
{
    protected $signature = 'cycle:orm';

    protected $description = 'Update (init) cycle schema from database and annotated classes';

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
        $this->info('Updating ORM schema...');

        Compiler::compile(
            $registry,
            $bootloader->getGenerators($app, $config)
        )->toMemory($cache);

        $this->info('Done');

        return self::SUCCESS;
    }
}
