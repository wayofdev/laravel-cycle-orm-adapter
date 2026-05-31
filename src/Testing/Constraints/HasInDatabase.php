<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Testing\Constraints;

use Cycle\Database\DatabaseInterface;
use Cycle\Database\DatabaseProviderInterface;
use Override;
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

    /**
     * Create a new constraint instance.
     */
    public function __construct(DatabaseProviderInterface $database, array $data)
    {
        $this->data = $data;

        $this->database = $database->database();
    }

    /**
     * Check if the constraint is satisfied.
     */
    public function matches(mixed $other): bool
    {
        try {
            $count = $this->database
                ->select()
                ->from((string) $other)
                ->where($this->data)
                ->count();

            return $count > 0;
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * Returns the description of the failure.
     */
    #[Override]
    public function failureDescription(mixed $other): string
    {
        return sprintf(
            'a row in the table [%s] matches the attributes %s.',
            $other,
            $this->toString(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    /**
     * Get a string representation of the object.
     */
    #[Override]
    public function toString(mixed $options = null): string
    {
        if (is_int($options)) {
            $options |= JSON_THROW_ON_ERROR;
        } else {
            $options = JSON_THROW_ON_ERROR;
        }

        return json_encode($this->data, $options);
    }
}
