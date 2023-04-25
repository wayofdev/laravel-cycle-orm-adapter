<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Schema;

use Cycle\Schema\Compiler as CycleCompiler;
use Cycle\Schema\Registry;
use WayOfDev\Cycle\Contracts\SchemaCompiler;

class Compiler implements SchemaCompiler
{
    private CycleCompiler $compiler;

    public function __construct(
        private readonly Registry $registry
    ) {
        $this->compiler = new CycleCompiler();
    }

    public function compile(array $generators): array
    {
        return $this->compiler->compile($this->registry, $generators);
    }
}
