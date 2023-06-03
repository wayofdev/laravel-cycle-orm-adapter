<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Testing;

use Cycle\Database\DatabaseProviderInterface;
use Cycle\Database\Driver\HandlerInterface;
use Cycle\Database\Table;

trait RefreshDatabase
{
    protected function refreshDatabase(): void
    {
        $database = app(DatabaseProviderInterface::class)->database('default');

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
    }
}
