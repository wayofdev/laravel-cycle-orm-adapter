<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Testing\Constraints;

use Cycle\Database\DatabaseInterface;
use Cycle\Database\DatabaseProviderInterface;
use Cycle\Database\Query\SelectQuery;
use PHPUnit\Framework\Constraint\Constraint;

use function json_encode;
use function sprintf;

class SoftDeletedInDatabase extends Constraint
{
    protected int $show = 3;

    protected DatabaseInterface $database;

    protected array $data;

    protected string $deletedAtColumn;

    public function __construct(DatabaseProviderInterface $database, array $data, string $deletedAtColumn)
    {
        $this->data = $data;
        $this->database = $database->database();
        $this->deletedAtColumn = $deletedAtColumn;
    }

    public function matches($table): bool
    {
        /** @var SelectQuery $query */
        $query = $this->database->table($table);

        return $query
            ->where($this->data)
            ->where($this->deletedAtColumn, 'IS NOT', null)
            ->count() > 0;
    }

    public function failureDescription($table): string
    {
        return sprintf(
            "any soft deleted row in the table [%s] matches the attributes %s.\n\n%s",
            $table,
            $this->toString(),
            $this->getAdditionalInfo($table)
        );
    }

    public function toString(): string
    {
        return json_encode($this->data);
    }

    protected function getAdditionalInfo($table): string
    {
        /** @var SelectQuery $query */
        $query = $this->database->table($table);

        $results = $query->limit($this->show)->fetchAll();

        if ([] === $results) {
            return 'The table is empty';
        }

        $description = 'Found: ' . json_encode($results, JSON_PRETTY_PRINT);

        if ($query->count() > $this->show) {
            $description .= sprintf(' and %s others', $query->count() - $this->show);
        }

        return $description;
    }
}
