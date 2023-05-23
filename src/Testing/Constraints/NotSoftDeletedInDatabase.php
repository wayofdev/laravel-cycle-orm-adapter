<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Testing\Constraints;

use Cycle\Database\DatabaseInterface;
use Cycle\Database\DatabaseProviderInterface;
use PHPUnit\Framework\Constraint\Constraint;

use function json_encode;
use function sprintf;

class NotSoftDeletedInDatabase extends Constraint
{
    protected int $show = 3;

    protected DatabaseInterface $database;

    protected array $data;

    protected string $deletedAtColumn;

    public function __construct(DatabaseProviderInterface $database, array $data, string $deletedAtColumn)
    {
        $this->database = $database->database();
        $this->data = $data;
        $this->deletedAtColumn = $deletedAtColumn;
    }

    public function matches($table): bool
    {
        return $this->database->table($table)
            ->where($this->data)
            ->whereNull($this->deletedAtColumn)
            ->count() > 0;
    }

    public function failureDescription($table): string
    {
        return sprintf(
            "any existing row in the table [%s] matches the attributes %s.\n\n%s",
            $table,
            $this->toString(),
            $this->getAdditionalInfo($table)
        );
    }

    public function toString(): string
    {
        return json_encode($this->data);
    }

    protected function getAdditionalInfo($table)
    {
        $query = $this->database->table($table);

        $results = $query->limit($this->show)->get();

        if ($results->isEmpty()) {
            return 'The table is empty';
        }

        $description = 'Found: ' . json_encode($results, JSON_PRETTY_PRINT);

        if ($query->count() > $this->show) {
            $description .= sprintf(' and %s others', $query->count() - $this->show);
        }

        return $description;
    }
}
