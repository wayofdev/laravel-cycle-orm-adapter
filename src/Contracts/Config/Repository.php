<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Contracts\Config;

interface Repository
{
    public function tokenizer(): array;

    public function databases(): array;

    public function schema(): array;

    public function migrationsDirectory(): string;

    public function migrationsTable(): string;

    public function relations(): array;
}
