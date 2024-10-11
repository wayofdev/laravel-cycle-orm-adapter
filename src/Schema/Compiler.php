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

    public function __construct(
        private readonly mixed $schema,
    ) {
    }

    public static function compile(Registry $registry, GeneratorLoader $queue): self
    {
        return new self((new CycleSchemaCompiler())->compile($registry, $queue->get()));
    }

    public static function fromMemory(CacheManager $cache): self
    {
        return new self($cache->get());
    }

    public function isEmpty(): bool
    {
        return $this->schema === null || $this->schema === [] || $this->schema === self::EMPTY_SCHEMA;
    }

    public function toSchema(): SchemaInterface
    {
        return new Schema($this->isWriteableSchema() ? $this->schema : []);
    }

    public function toMemory(CacheManager $cache): void
    {
        $cache->set($this->isEmpty() ? self::EMPTY_SCHEMA : $this->schema);
    }

    private function isWriteableSchema(): bool
    {
        return is_array($this->schema);
    }
}
