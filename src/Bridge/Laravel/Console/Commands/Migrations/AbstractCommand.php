<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Console\Commands\Migrations;

use Cycle\Migrations\Config\MigrationConfig;
use Cycle\Migrations\Migrator;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

use function array_merge;
use function call_user_func_array;

abstract class AbstractCommand extends Command
{
    protected const OPTIONS = [];

    private const DEFAULT_CONFIRMATION = 'Confirmation is required to run migrations!';

    public function __construct(protected Migrator $migrator, protected MigrationConfig $config)
    {
        parent::__construct();
    }

    protected function verifyConfigured(): bool
    {
        if (! $this->migrator->isConfigured()) {
            $this->warn(
                "Migrations are not configured yet, run '<info>migrate:init</info>' first."
            );

            return false;
        }

        return true;
    }

    protected function verifyEnvironment(?string $confirmationQuestion = null): bool
    {
        $confirmationQuestion = $confirmationQuestion ?? self::DEFAULT_CONFIRMATION;

        if ($this->isForced() || $this->config->isSafe()) {
            return true;
        }

        $this->error($confirmationQuestion);

        if (! $this->askConfirmation()) {
            $this->comment('Cancelling operation...');

            return false;
        }

        return true;
    }

    protected function askConfirmation(): bool
    {
        if ($this->confirm('Would you like to continue?')) {
            return true;
        }

        return false;
    }

    protected function defineOptions(): array
    {
        return array_merge(
            static::OPTIONS,
            [
                ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production'],
                ['no-interaction', 'n', InputOption::VALUE_NONE, 'Do not ask any interactive question'],
            ]
        );
    }

    /**
     * Check if the operation is being forced.
     */
    protected function isForced(): bool
    {
        return $this->option('force');
    }

    /**
     * Check if the command is running in interactive mode.
     */
    protected function isInteractive(): bool
    {
        return ! $this->option('no-interaction');
    }

    /**
     * Configure the command options.
     */
    protected function configure(): void
    {
        foreach ($this->defineOptions() as $option) {
            call_user_func_array([$this, 'addOption'], $option);
        }
    }
}
