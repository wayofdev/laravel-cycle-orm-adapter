<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Schema;

use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Compiler as CycleSchemaCompiler;
use Cycle\Schema\Registry;
use WayOfDev\Cycle\Contracts\CacheManager;
use WayOfDev\Cycle\Contracts\GeneratorLoader;

use function is_array;

final class Compiler
{
    private const EMPTY_SCHEMA = ':empty:';

    /**
     * Create a new compiler instance.
     */
    public function __construct(
        private readonly mixed $schema,
    ) {
    }

    /**
     * Compile the schema using the provided registry and generator queue.
     */
    public static function compile(Registry $registry, GeneratorLoader $queue): self
    {
        return new self((new CycleSchemaCompiler())->compile($registry, $queue->get()));
    }

    /**
     * Load the schema from cache.
     */
    public static function fromMemory(CacheManager $cache): self
    {
        return new self($cache->get());
    }

    /**
     * Check if the schema is empty.
     */
    public function isEmpty(): bool
    {
        return $this->schema === null || $this->schema === [] || $this->schema === self::EMPTY_SCHEMA;
    }

    /**
     * Convert the compiled schema to a SchemaInterface instance.
     */
    public function toSchema(): SchemaInterface
    {
        return new Schema($this->isWriteableSchema() ? $this->schema : []);
    }

    /**
     * Save the compiled schema to cache.
     */
    public function toMemory(CacheManager $cache): void
    {
        $cache->set($this->isEmpty() ? self::EMPTY_SCHEMA : $this->schema);
    }

    /**
     * Check if the schema can be written to cache.
     */
    private function isWriteableSchema(): bool
    {
        return is_array($this->schema);
    }
}
