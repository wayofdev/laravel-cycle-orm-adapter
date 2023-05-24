<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Testing\Concerns;

use Cycle\Database\DatabaseProviderInterface;
use Exception;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\LogicalNot as ReverseConstraint;
use WayOfDev\Cycle\Testing\Constraints\CountInDatabase;
use WayOfDev\Cycle\Testing\Constraints\HasInDatabase;
use WayOfDev\Cycle\Testing\Constraints\NotSoftDeletedInDatabase;
use WayOfDev\Cycle\Testing\Constraints\SoftDeletedInDatabase;

/**
 * @method void assertThat($value, Constraint $constraint, string $message = '')
 */
trait InteractsWithDatabase
{
    /**
     * @param string|object $table
     * @param string|null $connection
     *
     * @return $this
     */
    protected function assertDatabaseHas($table, array $data, $connection = null): static
    {
        $this->assertThat(
            $this->getTable($table),
            new HasInDatabase(app(DatabaseProviderInterface::class), $data)
        );

        return $this;
    }

    /**
     * @param string|object $table
     * @param string|null $connection
     *
     * @return $this
     */
    protected function assertDatabaseMissing($table, array $data, $connection = null): static
    {
        $constraint = new ReverseConstraint(
            new HasInDatabase(app(DatabaseProviderInterface::class), $data)
        );

        $this->assertThat($this->getTable($table), $constraint);

        return $this;
    }

    /**
     * @param string|object $table
     * @param string|null $connection
     *
     * @return $this
     */
    protected function assertDatabaseCount($table, int $count, $connection = null): static
    {
        $this->assertThat(
            $this->getTable($table),
            new CountInDatabase(app(DatabaseProviderInterface::class), $count)
        );

        return $this;
    }

    /**
     * @param string|object $table
     * @param string|null $connection
     *
     * @return $this
     */
    protected function assertDatabaseEmpty($table, $connection = null): static
    {
        $this->assertThat(
            $this->getTable($table),
            new CountInDatabase(app(DatabaseProviderInterface::class), 0)
        );

        return $this;
    }

    /**
     * @param string|object $table
     * @param string|null $connection
     * @param string|null $deletedAtColumn
     *
     * @throws Exception
     *
     * @return $this
     */
    protected function assertSoftDeleted($table, array $data = [], $connection = null, $deletedAtColumn = 'deleted_at'): static
    {
        if ($this->isSoftDeletableModel($table)) {
            throw new Exception('Eloquent not supported');
        }

        $this->assertThat(
            $this->getTable($table),
            new SoftDeletedInDatabase(app(DatabaseProviderInterface::class), $data, $deletedAtColumn)
        );

        return $this;
    }

    /**
     * @param string|object $table
     * @param string|null $connection
     * @param string|null $deletedAtColumn
     *
     * @throws Exception
     *
     * @return $this
     */
    protected function assertNotSoftDeleted($table, array $data = [], $connection = null, $deletedAtColumn = 'deleted_at'): static
    {
        if ($this->isSoftDeletableModel($table)) {
            throw new Exception('Eloquent not supported');
        }

        $this->assertThat(
            $this->getTable($table),
            new NotSoftDeletedInDatabase(app(DatabaseProviderInterface::class), $data, $deletedAtColumn)
        );

        return $this;
    }

    /**
     * @param string|object $table
     */
    protected function getTable($table): string
    {
        return $table;
    }
}
