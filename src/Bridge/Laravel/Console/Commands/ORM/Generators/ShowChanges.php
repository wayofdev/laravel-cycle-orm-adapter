<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Console\Commands\ORM\Generators;

use Cycle\Database\Schema\AbstractTable;
use Cycle\Database\Schema\ComparatorInterface;
use Cycle\Schema\GeneratorInterface;
use Cycle\Schema\Registry;
use Symfony\Component\Console\Output\OutputInterface;

use function count;
use function implode;
use function sprintf;

/**
 * @see https://github.com/spiral/cycle-bridge/blob/master/src/Console/Command/CycleOrm/Generator/ShowChanges.php
 */
final class ShowChanges implements GeneratorInterface
{
    public array $changes = [];
    private OutputInterface $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function run(Registry $registry): Registry
    {
        $this->output->writeln('<info>Detecting schema changes:</info>');
        $this->changes = [];

        foreach ($registry->getIterator() as $e) {
            if ($registry->hasTable($e)) {
                $table = $registry->getTableSchema($e);

                dump($table->getComparator());

                if ($table->getComparator()->hasChanges()) {
                    $key = $registry->getDatabase($e) . ':' . $registry->getTable($e);
                    $this->changes[$key] = [
                        'database' => $registry->getDatabase($e),
                        'table' => $registry->getTable($e),
                        'schema' => $table,
                    ];
                }
            }
        }

        if ([] === $this->changes) {
            $this->output->writeln('<fg=yellow>No database changes have been detected</fg=yellow>');

            return $registry;
        }

        foreach ($this->changes as $change) {
            $this->output->writeLn(sprintf('• <fg=cyan>%s.%s</fg=cyan>', $change['database'], $change['table']));
            $this->describeChanges($change['schema']);
        }

        return $registry;
    }

    public function hasChanges(): bool
    {
        return [] !== $this->changes;
    }

    private function describeChanges(AbstractTable $table): void
    {
        if (! $this->output->isVerbose()) {
            $this->output->writeln(
                sprintf(
                    ': <fg=green>%s</fg=green> change(s) detected',
                    $this->numChanges($table)
                )
            );

            return;
        }
        $this->output->write("\n");

        if (! $table->exists()) {
            $this->output->writeln('    - create table');
        }

        if ($table->getStatus() === AbstractTable::STATUS_DECLARED_DROPPED) {
            $this->output->writeln('    - drop table');

            return;
        }

        $comparator = $table->getComparator();

        $this->describeColumns($comparator);
        $this->describeIndexes($comparator);
        $this->describeFKs($comparator);
    }

    private function describeColumns(ComparatorInterface $comparator): void
    {
        foreach ($comparator->addedColumns() as $column) {
            $this->output->writeln("    - add column <fg=yellow>{$column->getName()}</fg=yellow>");
        }

        foreach ($comparator->droppedColumns() as $column) {
            $this->output->writeln("    - drop column <fg=yellow>{$column->getName()}</fg=yellow>");
        }

        foreach ($comparator->alteredColumns() as $column) {
            $column = $column[0];
            $this->output->writeln("    - alter column <fg=yellow>{$column->getName()}</fg=yellow>");
        }
    }

    private function describeIndexes(ComparatorInterface $comparator): void
    {
        foreach ($comparator->addedIndexes() as $index) {
            $index = implode(', ', $index->getColumns());
            $this->output->writeln("    - add index on <fg=yellow>[{$index}]</fg=yellow>");
        }

        foreach ($comparator->droppedIndexes() as $index) {
            $index = implode(', ', $index->getColumns());
            $this->output->writeln("    - drop index on <fg=yellow>[{$index}]</fg=yellow>");
        }

        foreach ($comparator->alteredIndexes() as $index) {
            $index = $index[0];
            $index = implode(', ', $index->getColumns());
            $this->output->writeln("    - alter index on <fg=yellow>[{$index}]</fg=yellow>");
        }
    }

    private function describeFKs(ComparatorInterface $comparator): void
    {
        foreach ($comparator->addedForeignKeys() as $fk) {
            $fkColumns = implode(', ', $fk->getColumns());
            $this->output->writeln("    - add foreign key on <fg=yellow>{$fkColumns}</fg=yellow>");
        }

        foreach ($comparator->droppedForeignKeys() as $fk) {
            $fkColumns = implode(', ', $fk->getColumns());
            $this->output->writeln("    - drop foreign key <fg=yellow>{$fkColumns}</fg=yellow>");
        }

        foreach ($comparator->alteredForeignKeys() as $fk) {
            $fk = $fk[0];
            $fkColumns = implode(', ', $fk->getColumns());
            $this->output->writeln("    - alter foreign key <fg=yellow>{$fkColumns}</fg=yellow>");
        }
    }

    private function numChanges(AbstractTable $table): int
    {
        $comparator = $table->getComparator();

        return count($comparator->addedColumns())
            + count($comparator->droppedColumns())
            + count($comparator->alteredColumns())
            + count($comparator->addedIndexes())
            + count($comparator->droppedIndexes())
            + count($comparator->alteredIndexes())
            + count($comparator->addedForeignKeys())
            + count($comparator->droppedForeignKeys())
            + count($comparator->alteredForeignKeys());
    }
}
