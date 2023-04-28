<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Schema\Cache;

use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Psr\SimpleCache\InvalidArgumentException;
use WayOfDev\Cycle\Contracts\CacheManager;
use WayOfDev\Cycle\Contracts\Config\Repository as Config;
use WayOfDev\Cycle\Support\Arr;

final class Manager implements CacheManager
{
    private const SCHEMA_CACHE_KEY = 'cycle.orm.schema';

    public function __construct(
        private readonly Config $config,
        private readonly CacheFactory $cacheFactory
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function get(): ?array
    {
        return $this->cacheStore()->get(self::SCHEMA_CACHE_KEY);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function set(string|array $schema): bool
    {
        return $this->cacheStore()->set(self::SCHEMA_CACHE_KEY, $schema);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function flush(): bool
    {
        return $this->cacheStore()->delete(self::SCHEMA_CACHE_KEY);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function isCached(): bool
    {
        return $this->cacheStore()->has(self::SCHEMA_CACHE_KEY);
    }

    private function cacheStore(): CacheRepository
    {
        return $this->cacheFactory->store(
            Arr::get($this->config->schema(), 'cache.storage')
        );
    }
}
