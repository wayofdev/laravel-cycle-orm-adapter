<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Schema;

use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use WayOfDev\Cycle\Contracts\CacheManager;
use WayOfDev\Cycle\Contracts\GeneratorLoader;
use WayOfDev\Cycle\Contracts\SchemaCompiler;

final class Factory
{
    public function __construct(
        private readonly CacheManager $cacheManager,
        private readonly SchemaCompiler $compiler,
        private readonly GeneratorLoader $generators
    ) {
    }

    public function create(): SchemaInterface
    {
        if ($this->cacheManager->isCached()) {
            return new Schema($this->cacheManager->get() ?? []);
        }

        $schema = $this->compiler->compile($this->generators);

        return new Schema($schema);
    }
}
