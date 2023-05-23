<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Testing\Constraints;

use Cycle\Database\DatabaseInterface;
use Cycle\Database\DatabaseProviderInterface;
use Cycle\Database\Table;
use PHPUnit\Framework\Constraint\Constraint;
use ReflectionClass;

use function sprintf;

class CountInDatabase extends Constraint
{
    protected DatabaseInterface $database;

    protected int $expectedCount;

    protected int $actualCount;

    public function __construct(DatabaseProviderInterface $database, int $expectedCount)
    {
        $this->expectedCount = $expectedCount;

        $this->database = $database->database();
    }

    public function matches(mixed $table): bool
    {
        /** @var Table $tableInterface */
        $tableInterface = $this->database->table($table);

        $this->actualCount = $tableInterface->count();

        return $this->actualCount === $this->expectedCount;
    }

    public function failureDescription(mixed $table): string
    {
        return sprintf(
            "table [%s] matches expected entries count of %s. Entries found: %s.\n",
            $table,
            $this->expectedCount,
            $this->actualCount
        );
    }

    public function toString(int $options = 0): string
    {
        return (new ReflectionClass($this))->name;
    }
}
