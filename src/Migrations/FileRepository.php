<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Migrations;

use Cycle\Migrations\RepositoryInterface;

/**
 * Stores migrations as files.
 * Check as an example:.
 *
 * @see https://github.com/cycle/migrations/blob/3.x/src/FileRepository.php
 */
final class FileRepository implements RepositoryInterface
{
    public function getMigrations(): array
    {
        return [];
    }

    public function registerMigration(string $name, string $class, string $body = null): string
    {
        return '';
    }
}
