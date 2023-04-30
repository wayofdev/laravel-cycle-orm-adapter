<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Schema;

use Cycle\Schema\Compiler as CycleSchemaCompiler;
use Cycle\Schema\Registry;
use WayOfDev\Cycle\Contracts\GeneratorLoader;
use WayOfDev\Cycle\Contracts\SchemaCompiler;

final class Compiler implements SchemaCompiler
{
    private CycleSchemaCompiler $compiler;

    public function __construct(private readonly Registry $registry)
    {
        $this->compiler = new CycleSchemaCompiler();
    }

    public function compile(GeneratorLoader $queue): array
    {
        return $this->compiler->compile($this->registry, $queue->get());
    }
}
