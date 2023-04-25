<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Console\Commands\Migrations;

use Cycle\Migrations\Migrator;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

use function array_merge;
use function call_user_func_array;

abstract class AbstractCommand extends Command
{
    protected const OPTIONS = [];

    private const DEFAULT_CONFIRMATION = 'Confirmation is required to run migrations!';

    public function __construct(protected Migrator $migrator)
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

    protected function verifyEnvironment(string $confirmationQuestion = null): bool
    {
        $confirmationQuestion = $confirmationQuestion ?? self::DEFAULT_CONFIRMATION;

        if ($this->option('force')) {
            return true;
        }

        $this->line($confirmationQuestion, 'fg=red');

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
                ['force', 's', InputOption::VALUE_NONE, 'Skip safe environment check'],
            ]
        );
    }

    protected function configure(): void
    {
        foreach ($this->defineOptions() as $option) {
            call_user_func_array([$this, 'addOption'], $option);
        }
    }
}
