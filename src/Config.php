<?php

declare(strict_types=1);

namespace WayOfDev\Cycle;

use Cycle\ORM\Collection\CollectionFactoryInterface;
use Cycle\ORM\Collection\IlluminateCollectionFactory;
use WayOfDev\Cycle\Contracts\Config\Repository;
use WayOfDev\Cycle\Exceptions\MissingRequiredAttributes;
use WayOfDev\Cycle\Support\Arr;

use function array_diff;
use function array_keys;
use function implode;

final class Config implements Repository
{
    private const REQUIRED_FIELDS = [
        'tokenizer',
        'database',
        'schema',
        'migrations',
        'warmup',
        'customRelations',
    ];

    public static function fromArray(array $config): self
    {
        $missingAttributes = array_diff(array_keys($config), self::REQUIRED_FIELDS);

        if ([] !== $missingAttributes) {
            throw MissingRequiredAttributes::fromArray(
                implode(',', $missingAttributes)
            );
        }

        return new self(
            $config['tokenizer'],
            $config['database'],
            $config['schema'],
            $config['migrations'],
            $config['warmup'],
            $config['customRelations']
        );
    }

    public function tokenizer(): array
    {
        return $this->tokenizer;
    }

    public function database(): array
    {
        return $this->database;
    }

    public function schema(): array
    {
        return $this->schema;
    }

    public function schemaGenerators(): ?array
    {
        return Arr::get($this->schema, 'generators');
    }

    public function schemaDefaults(): array
    {
        return Arr::get($this->schema, 'defaults', []);
    }

    public function schemaCache(): bool
    {
        return (bool) Arr::get($this->schema, 'cache', true);
    }

    public function manuallyDefinedSchema(): array
    {
        return Arr::get($this->schema, 'map', []);
    }

    public function migrationsDirectory(): string
    {
        return Arr::get($this->migrations, 'directory');
    }

    public function migrationsTable(): string
    {
        return Arr::get($this->migrations, 'table');
    }

    public function safeToMigrate(): bool
    {
        return Arr::get($this->migrations, 'safe');
    }

    public function warmup(): bool
    {
        return $this->warmup;
    }

    public function customRelations(): array
    {
        return $this->customRelations;
    }

    public function defaultCollectionName(): string
    {
        return Arr::get($this->schema, 'collections.default', 'illuminate');
    }

    /**
     * @return class-string<CollectionFactoryInterface>
     */
    public function defaultCollectionFactory(): string
    {
        return Arr::get(
            $this->schema,
            "collections.factories.{$this->defaultCollectionName()}",
            IlluminateCollectionFactory::class
        );
    }

    public function collectionFactories(): array
    {
        return Arr::get($this->schema, 'collections.factories', []);
    }

    private function __construct(
        private readonly array $tokenizer,
        private readonly array $database,
        private readonly array $schema,
        private readonly array $migrations,
        private readonly bool $warmup,
        private readonly array $customRelations
    ) {
    }
}
