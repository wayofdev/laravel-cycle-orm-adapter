<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Console\Commands\ORM;

use Cycle\Migrations\State;
use Cycle\Schema\Compiler as CycleSchemaCompiler;
use Cycle\Schema\Generator\Migrations\GenerateMigrations;
use Cycle\Schema\Generator\Migrations\Strategy\GeneratorStrategyInterface;
use Cycle\Schema\Generator\Migrations\Strategy\MultipleFilesStrategy;
use Cycle\Schema\Generator\PrintChanges;
use Cycle\Schema\Registry;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Command\Command;
use WayOfDev\Cycle\Bridge\Laravel\Console\Commands\Migrations\AbstractCommand;
use WayOfDev\Cycle\Bridge\Laravel\Console\Commands\Migrations\MigrateCommand as DatabaseMigrateCommand;
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
                           {--r|run : Automatically run generated migration }
                           {--s|split : Split generated migration into multiple files }';

    protected $description = 'Generate ORM schema migrations.';

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(
        GeneratorLoader $generators,
        Registry $registry,
        CacheManagerContract $cache
    ): int {
        if (! $this->migrator->isConfigured()) {
            $this->migrator->configure();
        }

        foreach ($this->migrator->getMigrations() as $migration) {
            if ($migration->getState()->getStatus() !== State::STATUS_EXECUTED) {
                if ($this->isForced() || ($this->isInteractive() && $this->output->confirm('Outstanding migrations found. Do you want to run `cycle:migrate` now?'))) {
                    $this->call(DatabaseMigrateCommand::class, ['--force' => true]);
                } else {
                    $this->warn('Outstanding migrations found, run `cycle:migrate` first.');

                    return self::FAILURE;
                }
            }
        }

        $this->comment('Detecting schema changes...');

        $diff = new PrintChanges($this->output);
        $queue = $generators->add(GeneratorLoader::GROUP_RENDER, $diff);

        $schemaCompiler = Compiler::compile($registry, $queue);
        $schemaCompiler->toMemory($cache);

        if ($diff->hasChanges()) {
            if ($this->option('split')) {
                $this->info('Splitting generated migration into multiple files.');
                $this->laravel->singleton(GeneratorStrategyInterface::class, MultipleFilesStrategy::class);
            }

            $migrations = $this->laravel->get(GenerateMigrations::class);

            // Creates migration files in database/migrations directory.
            (new CycleSchemaCompiler())->compile($registry, [$migrations]);

            if ($this->option('run')) {
                $this->call(DatabaseMigrateCommand::class, ['--force' => true]);
            }
        }

        return Command::SUCCESS;
    }
}
