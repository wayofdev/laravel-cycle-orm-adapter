<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Contracts\Config;

interface Repository
{
    public function tokenizer(): array;

    public function database(): array;

    public function schema(): array;

    public function schemaGenerators(): ?array;

    public function schemaDefaults(): ?array;

    public function schemaCache(); // : array;

    public function manuallyDefinedSchema(): array;

    public function migrationsDirectory(): string;

    public function migrationsTable(): string;

    public function safeToMigrate(): bool;

    public function customRelations(): array;

    public function defaultCollectionName(): string;

    public function defaultCollectionFactory();

    public function collectionFactories(): array;
}
