<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Console\Commands\ORM;

use Cycle\Migrations\State;
use Cycle\Schema\Compiler as CycleSchemaCompiler;
use Cycle\Schema\Generator\Migrations\GenerateMigrations;
use Cycle\Schema\Registry;
use Symfony\Component\Console\Command\Command;
use WayOfDev\Cycle\Bridge\Laravel\Console\Commands\Migrations\AbstractCommand;
use WayOfDev\Cycle\Bridge\Laravel\Console\Commands\Migrations\MigrateCommand as DatabaseMigrateCommand;
use WayOfDev\Cycle\Bridge\Laravel\Console\Commands\ORM\Generators\ShowChanges;
use WayOfDev\Cycle\Contracts\CacheManager as CacheManagerContract;
use WayOfDev\Cycle\Contracts\GeneratorLoader;
use WayOfDev\Cycle\Schema\Compiler;

/**
 * See original spiral framework commands.
 *
 * @see https://github.com/spiral/cycle-bridge/blob/master/src/Console/Command/Migrate/MigrateCommand.php
 */
final class MigrateCommand extends AbstractCommand
{
    protected $signature = 'cycle:orm:migrate
                           {--r|run : Automatically run generated migration }';

    protected $description = 'Generate ORM schema migrations.';

    public function handle(
        GeneratorLoader $generators,
        Registry $registry,
        GenerateMigrations $migrations,
        CacheManagerContract $cache
    ): int {
        if (! $this->migrator->isConfigured()) {
            $this->migrator->configure();
        }

        foreach ($this->migrator->getMigrations() as $migration) {
            if ($migration->getState()->getStatus() !== State::STATUS_EXECUTED) {
                $this->warn('Outstanding migrations found, run `cycle:orm:migrate` first!');

                return self::FAILURE;
            }
        }

        $diff = new ShowChanges($this->output);
        $queue = $generators->add(GeneratorLoader::GROUP_RENDER, $diff);

        $schemaCompiler = Compiler::compile($registry, $queue);
        $schemaCompiler->toMemory($cache);

        if ($diff->hasChanges()) {
            if ($this->option('run')) {
                $this->call(DatabaseMigrateCommand::class, ['--force' => true]);
            }
        }

        return Command::SUCCESS;
    }
}
