<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Testing\Concerns;

use Cycle\Database\DatabaseProviderInterface;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\SchemaInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\LogicalNot as ReverseConstraint;
use Throwable;
use WayOfDev\Cycle\Support\Arr;
use WayOfDev\Cycle\Testing\Constraints\CountInDatabase;
use WayOfDev\Cycle\Testing\Constraints\HasInDatabase;
use WayOfDev\Cycle\Testing\Constraints\NotSoftDeletedInDatabase;
use WayOfDev\Cycle\Testing\Constraints\SoftDeletedInDatabase;

use function is_iterable;
use function is_object;
use function is_string;

/**
 * @method void assertThat($value, Constraint $constraint, string $message = '')
 */
trait InteractsWithDatabase
{
    /**
     * @param array|string $class
     */
    public function seed($class = 'Database\\Seeders\\DatabaseSeeder'): static
    {
        foreach (Arr::wrap($class) as $wrappedClass) {
            $this->artisan('db:seed', ['--class' => $wrappedClass, '--no-interaction' => true]);
        }

        return $this;
    }

    /**
     * @param Model|string|object|iterable<Model|string|object> $table
     * @param array<string, mixed> $data
     * @param string|null $connection
     */
    protected function assertDatabaseHas($table, array $data = [], $connection = null): static
    {
        if (is_iterable($table)) {
            foreach ($table as $item) {
                $this->assertDatabaseHas($item, $data, $connection);
            }

            return $this;
        }

        $this->assertThat(
            $this->normalizeTable($table),
            new HasInDatabase(app(DatabaseProviderInterface::class), $data)
        );

        return $this;
    }

    /**
     * @param Model|string|object|iterable<Model|string|object> $table
     * @param array<string, mixed> $data
     * @param string|null $connection
     */
    protected function assertDatabaseMissing($table, array $data = [], $connection = null): static
    {
        if (is_iterable($table)) {
            foreach ($table as $item) {
                $this->assertDatabaseMissing($item, $data, $connection);
            }

            return $this;
        }

        $constraint = new ReverseConstraint(
            new HasInDatabase(app(DatabaseProviderInterface::class), $data)
        );

        $this->assertThat($this->normalizeTable($table), $constraint);

        return $this;
    }

    /**
     * @param Model|string|object|iterable<Model|string|object> $table
     * @param string|null $connection
     */
    protected function assertDatabaseCount($table, int $count, $connection = null): static
    {
        if (is_iterable($table)) {
            foreach ($table as $item) {
                $this->assertDatabaseCount($item, $count, $connection);
            }

            return $this;
        }

        $this->assertThat(
            $this->normalizeTable($table),
            new CountInDatabase(app(DatabaseProviderInterface::class), $count)
        );

        return $this;
    }

    /**
     * @param Model|string|object|iterable<Model|string|object> $table
     * @param string|null $connection
     */
    protected function assertDatabaseEmpty($table, $connection = null): static
    {
        if (is_iterable($table)) {
            foreach ($table as $item) {
                $this->assertDatabaseEmpty($item, $connection);
            }

            return $this;
        }

        return $this->assertDatabaseCount($table, 0, $connection);
    }

    protected function cleanupMigrations(string $pathGlob): void
    {
        $files = File::glob($pathGlob);
        foreach ($files as $file) {
            File::delete($file);
        }
    }

    /**
     * @param Model|string|object|iterable<Model|string|object> $table
     * @param array<string, mixed> $data
     * @param string|null $connection
     * @param string|null $deletedAtColumn
     */
    protected function assertSoftDeleted($table, array $data = [], $connection = null, $deletedAtColumn = 'deleted_at'): static
    {
        if (is_iterable($table)) {
            foreach ($table as $item) {
                $this->assertSoftDeleted($item, $data, $connection, $deletedAtColumn);
            }

            return $this;
        }

        $this->assertThat(
            $this->normalizeTable($table),
            new SoftDeletedInDatabase(
                app(DatabaseProviderInterface::class),
                $data,
                $deletedAtColumn ?? 'deleted_at',
            )
        );

        return $this;
    }

    /**
     * @param Model|string|object|iterable<Model|string|object> $table
     * @param array<string, mixed> $data
     * @param string|null $connection
     * @param string|null $deletedAtColumn
     */
    protected function assertNotSoftDeleted($table, array $data = [], $connection = null, $deletedAtColumn = 'deleted_at'): static
    {
        if (is_iterable($table)) {
            foreach ($table as $item) {
                $this->assertNotSoftDeleted($item, $data, $connection, $deletedAtColumn);
            }

            return $this;
        }

        $this->assertThat(
            $this->normalizeTable($table),
            new NotSoftDeletedInDatabase(
                app(DatabaseProviderInterface::class),
                $data,
                $deletedAtColumn ?? 'deleted_at',
            )
        );

        return $this;
    }

    /**
     * @param Model|string|object $table
     */
    protected function normalizeTable(mixed $table): string
    {
        if ($table instanceof Model) {
            return $table->getTable();
        }

        if (is_string($table) || is_object($table)) {
            try {
                $orm = app(ORMInterface::class);
                $schema = $orm->getSchema();
                $role = is_object($table) ? $table::class : $table;

                if ($schema->defines($role)) {
                    return (string) $schema->define($role, SchemaInterface::TABLE);
                }
            } catch (Throwable) {
            }
        }

        return (string) $table;
    }
}
