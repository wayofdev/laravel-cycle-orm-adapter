<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Cache\Entities;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

#[Entity(table: 'cache_locks')]
class CacheLock
{
    #[Column(type: 'string', primary: true)]
    public string $key;

    #[Column(type: 'string')]
    public string $owner;

    #[Column(type: 'integer')]
    public int $expiration;
}
