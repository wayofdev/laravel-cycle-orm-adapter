<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Contracts;

interface SchemaCompiler
{
    public function compile(array $generators): array;
}
