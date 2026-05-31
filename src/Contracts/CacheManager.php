<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Contracts;

interface CacheManager
{
    /**
     * Retrieve the cached schema.
     */
    public function get(): mixed;

    /**
     * Cache the provided schema.
     */
    public function set(string|array $schema): bool;

    /**
     * Flush the cached schema.
     */
    public function flush(): bool;

    /**
     * Check if the schema is cached.
     */
    public function isCached(): bool;
}
