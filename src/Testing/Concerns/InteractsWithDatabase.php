<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Testing\Concerns;

use Cycle\Database\DatabaseProviderInterface;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\LogicalNot as ReverseConstraint;
use WayOfDev\Cycle\Support\Arr;
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
     * @param string|object $table
     * @param string|null $connection
     *
     * @return $this
     */
    protected function assertDatabaseHas($table, array $data = [], $connection = null): static
    {
        $this->assertThat(
            $table,
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
    protected function assertDatabaseMissing($table, array $data = [], $connection = null): static
    {
        $constraint = new ReverseConstraint(
            new HasInDatabase(app(DatabaseProviderInterface::class), $data)
        );

        $this->assertThat($table, $constraint);

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
            $table,
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
            $table,
            new CountInDatabase(app(DatabaseProviderInterface::class), 0)
        );

        return $this;
    }

    protected function cleanupMigrations(string $pathGlob): void
    {
        $files = File::glob($pathGlob);
        foreach ($files as $file) {
            File::delete($file);
        }
    }

    protected function assertSoftDeleted($table, array $data = [], $connection = null, $deletedAtColumn = 'deleted_at'): self
    {
        $this->assertThat(
            $table,
            new SoftDeletedInDatabase(
                app(DatabaseProviderInterface::class),
                $data,
                $deletedAtColumn,
            )
        );

        return $this;
    }

    protected function assertNotSoftDeleted($table, array $data = [], $connection = null, $deletedAtColumn = 'deleted_at'): self
    {
        $this->assertThat(
            $table,
            new NotSoftDeletedInDatabase(
                app(DatabaseProviderInterface::class),
                $data,
                $deletedAtColumn,
            )
        );

        return $this;
    }
}
