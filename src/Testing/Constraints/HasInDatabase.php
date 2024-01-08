<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Testing\Constraints;

use Cycle\Database\DatabaseInterface;
use Cycle\Database\DatabaseProviderInterface;
use Cycle\Database\Query\SelectQuery;
use JsonException;
use PHPUnit\Framework\Constraint\Constraint;
use Throwable;

use function is_int;
use function json_encode;
use function sprintf;

class HasInDatabase extends Constraint
{
    protected int $show = 3;

    protected DatabaseInterface $database;

    protected array $data;

    public function __construct(DatabaseProviderInterface $database, array $data)
    {
        $this->data = $data;

        $this->database = $database->database();
    }

    public function matches(mixed $other): bool
    {
        /** @var SelectQuery $tableInterface */
        $tableInterface = $this->database->table($other);

        try {
            $count = $tableInterface->where($this->data)->count();

            return 0 < $count;
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * @throws JsonException
     */
    public function failureDescription(mixed $other): string
    {
        return sprintf(
            'a row in the table [%s] matches the attributes %s.',
            $other,
            $this->toString(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    /**
     * @throws JsonException
     */
    public function toString($options = null): string
    {
        if (is_int($options)) {
            $options |= JSON_THROW_ON_ERROR;
        } else {
            $options = JSON_THROW_ON_ERROR;
        }

        return json_encode($this->data, $options | $options);
    }
}
