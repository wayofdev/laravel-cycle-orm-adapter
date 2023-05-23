<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Console\Commands\Migrations;

use Cycle\Database\DatabaseProviderInterface;
use Cycle\Database\Driver\HandlerInterface;
use Cycle\Database\Table;

final class FreshSchemaCommand extends AbstractCommand
{
    protected $signature = 'cycle:migrate:fresh';

    protected $description = 'Drop all tables and re-run all cycle orm migrations.';

    public function handle(DatabaseProviderInterface $dbal): int
    {
        if (! $this->verifyEnvironment()) {
            return self::FAILURE;
        }

        $database = $dbal->database('default');

        /** @var Table $table */
        foreach ($database->getTables() as $table) {
            $schema = $table->getSchema();
            foreach ($schema->getForeignKeys() as $foreign) {
                $schema->dropForeignKey($foreign->getColumns());
            }

            $schema->save(HandlerInterface::DROP_FOREIGN_KEYS);
        }

        /** @var Table $table */
        foreach ($database->getTables() as $table) {
            $schema = $table->getSchema();
            $schema->declareDropped();
            $schema->save();
        }

        $this->alert('Wiped database data and tables...');

        return self::SUCCESS;
    }
}
