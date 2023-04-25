<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Contracts;

use Cycle\ORM\Schema;

interface SchemaManager
{
    public function create(): Schema;

    public function flush(): void;

    public function schema(array $generators = []): array;
}
