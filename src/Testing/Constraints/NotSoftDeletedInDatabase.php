<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Testing\Constraints;

use Cycle\Database\DatabaseInterface;
use Cycle\Database\DatabaseProviderInterface;
use Cycle\Database\Query\SelectQuery;
use PHPUnit\Framework\Constraint\Constraint;
use Throwable;

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
        $this->data = $data;

        $this->database = $database->database();

        $this->deletedAtColumn = $deletedAtColumn;
    }

    public function matches(mixed $other): bool
    {
        /** @var SelectQuery $tableInterface */
        $tableInterface = $this->database->table($other);

        try {
            $count = $tableInterface->where($this->data)
                ->andWhere($this->deletedAtColumn, '=', null)
                ->count();

            return $count > 0;
        } catch (Throwable $e) {
            return false;
        }
    }

    public function failureDescription($other): string
    {
        return sprintf(
            'any existing row in the table [%s] matches the attributes %s.\n',
            $other,
            $this->toString()
        );
    }

    public function toString(): string
    {
        return json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
