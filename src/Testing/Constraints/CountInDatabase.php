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

    public function matches(mixed $other): bool
    {
        /** @var Table $tableInterface */
        $tableInterface = $this->database->table($other);

        $this->actualCount = $tableInterface->count();

        return $this->actualCount === $this->expectedCount;
    }

    public function failureDescription(mixed $other): string
    {
        return sprintf(
            "table [%s] matches expected entries count of %s. Entries found: %s.\n",
            $other,
            $this->expectedCount,
            $this->actualCount
        );
    }

    public function toString(): string
    {
        return (new ReflectionClass($this))->name;
    }
}
