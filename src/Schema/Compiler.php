<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Schema;

use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Compiler as CycleCompiler;
use Cycle\Schema\Registry;
use WayOfDev\Cycle\Contracts\CacheManager as CacheManagerContract;

use function is_array;

class Compiler
{
    private const EMPTY_SCHEMA = ':empty:';

    public static function compile(Registry $registry, array $generators, array $defaults = []): self
    {
        return new self((new CycleCompiler())->compile($registry, $generators, $defaults));
    }

    public static function fromMemory(CacheManagerContract $cache): self
    {
        return new self($cache->get());
    }

    public function __construct(
        private readonly mixed $schema
    ) {
    }

    public function isEmpty(): bool
    {
        return null === $this->schema || [] === $this->schema || self::EMPTY_SCHEMA === $this->schema;
    }

    public function toSchema(): SchemaInterface
    {
        return new Schema($this->isWriteableSchema() ? $this->schema : []);
    }

    public function toMemory(CacheManagerContract $cache): void
    {
        $cache->set($this->isEmpty() ? self::EMPTY_SCHEMA : $this->schema);
    }

    private function isWriteableSchema(): bool
    {
        return is_array($this->schema);
    }
}
