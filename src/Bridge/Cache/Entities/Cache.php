<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Cache\Entities;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

#[Entity(table: 'cache')]
class Cache
{
    #[Column(type: 'string', primary: true)]
    public string $key;

    #[Column(type: 'longText')]
    public string $text;

    #[Column(type: 'integer')]
    public int $expiration;
}
