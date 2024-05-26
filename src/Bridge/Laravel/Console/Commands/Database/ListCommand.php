<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Console\Commands\Database;

use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\Database;
use Cycle\Database\DatabaseProviderInterface;
use Cycle\Database\Driver\Driver;
use Exception;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Throwable;

use function array_keys;
use function array_merge;
use function count;
use function end;
use function number_format;

/**
 * See original spiral framework commands.
 *
 * @see https://github.com/spiral/cycle-bridge/blob/master/src/Console/Command/Database/ListCommand.php
 */
final class ListCommand extends Command
{
    private const HEADERS = [
        'Name (ID):',
        'Database:',
        'Driver:',
        'Prefix:',
        'Status:',
        'Tables:',
        'Count Records:',
    ];

    protected $signature = 'cycle:db:list
                           {db? : Database name }';

    protected $description = 'Get list of available databases, their tables and records count';

    public function handle(DatabaseConfig $config, DatabaseProviderInterface $dbal): int
    {
        /** @var string|null $databaseArgumentValue */
        $databaseArgumentValue = $this->argument('db');

        /** @var array<string> $databases */
        $databases = ($databaseArgumentValue !== null)
            ? [$databaseArgumentValue]
            : array_keys($config->getDatabases());

        if (count($databases) === 0) {
            $this->line('No databases found.', 'fg=red');

            return self::SUCCESS;
        }

        $grid = (new Table($this->output))->setHeaders(self::HEADERS);

        foreach ($databases as $database) {
            /** @var Database $db */
            $db = $dbal->database($database);

            /** @var Driver $driver */
            $driver = $db->getDriver();
            $prefix = ($db->getPrefix() !== '') ? $db->getPrefix() : '<comment>---</comment>';

            $header = [
                $db->getName(),
                $driver->getSource(),
                $driver->getType(),
                $prefix,
            ];

            try {
                $driver->connect();
            } catch (Exception $exception) {
                $this->renderException($grid, $header, $exception);

                if ($db->getName() != end($db)) {
                    $grid->addRow(new TableSeparator());
                }

                continue;
            }

            $header[] = '<info>connected</info>';
            $this->renderTables($grid, $header, $db);
            if ($db->getName() != end($databases)) {
                $grid->addRow(new TableSeparator());
            }
        }

        $grid->render();

        return self::SUCCESS;
    }

    private function renderException(Table $grid, array $header, Throwable $exception): void
    {
        $grid->addRow(
            array_merge(
                $header,
                [
                    "<fg=red>{$exception->getMessage()}</fg=red>",
                    '<comment>---</comment>',
                    '<comment>---</comment>',
                ]
            )
        );
    }

    private function renderTables(Table $grid, array $header, Database $database): void
    {
        foreach ($database->getTables() as $table) {
            $grid->addRow(
                array_merge(
                    $header,
                    [$table->getName(), number_format($table->count())]
                )
            );
            $header = ['', '', '', '', ''];
        }

        if (isset($header[1])) {
            $grid->addRow(array_merge($header, ['no tables', 'no records']));
        }
    }
}
