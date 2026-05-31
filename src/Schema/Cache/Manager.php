<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Schema\Cache;

use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Psr\SimpleCache\InvalidArgumentException;
use WayOfDev\Cycle\Contracts\CacheManager;
use WayOfDev\Cycle\Schema\Config\SchemaConfig;
use WayOfDev\Cycle\Support\Arr;

final class Manager implements CacheManager
{
    private const SCHEMA_CACHE_KEY = 'cycle.orm.schema';

    /**
     * Create a new cache manager instance.
     */
    public function __construct(
        private readonly SchemaConfig $config,
        private readonly CacheFactory $cacheFactory,
    ) {
    }

    /**
     * Get the cached schema.
     *
     * @throws InvalidArgumentException
     */
    public function get(): mixed
    {
        return $this->cacheStore()->get(self::SCHEMA_CACHE_KEY);
    }

    /**
     * Cache the provided schema.
     *
     * @throws InvalidArgumentException
     */
    public function set(string|array $schema): bool
    {
        return $this->cacheStore()->set(self::SCHEMA_CACHE_KEY, $schema);
    }

    /**
     * Flush the cached schema.
     *
     * @throws InvalidArgumentException
     */
    public function flush(): bool
    {
        return $this->cacheStore()->delete(self::SCHEMA_CACHE_KEY);
    }

    /**
     * Check if the schema is cached.
     *
     * @throws InvalidArgumentException
     */
    public function isCached(): bool
    {
        return $this->cacheStore()->has(self::SCHEMA_CACHE_KEY);
    }

    /**
     * Get the cache repository instance.
     */
    private function cacheStore(): CacheRepository
    {
        $store = Arr::get(
            $this->config->toArray(),
            'cache.store',
        );

        return $this->cacheFactory->store($store);
    }
}
