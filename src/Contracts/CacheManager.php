<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Contracts;

interface CacheManager
{
    public function get(): ?array;

    public function set(string|array $schema): bool;

    public function flush(): bool;

    public function isCached(): bool;
}
