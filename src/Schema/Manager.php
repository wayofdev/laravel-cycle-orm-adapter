<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Schema;

use Cycle\ORM\Schema;
use WayOfDev\Cycle\Contracts\CacheManager;
use WayOfDev\Cycle\Contracts\Config\Repository as Config;
use WayOfDev\Cycle\Contracts\GeneratorLoader;
use WayOfDev\Cycle\Contracts\SchemaManager;

use function count;

class Manager implements SchemaManager
{
    public function __construct(
        private readonly GeneratorLoader $generatorsFactory,
        private readonly SchemaCompiler $compiler,
        private readonly Config $config,
        private readonly CacheManager $cache
    ) {
    }

    public function create(): Schema
    {
        return new Schema($this->schema());
    }

    public function flush(): void
    {
        $this->cache->flush();
    }

    public function schema(array $generators = []): array
    {
        if ($this->cache->isCached()) {
            return $this->cache->get() ?? [];
        }

        $manuallyDefinedSchema = $this->config->manuallyDefinedSchema();

        if (count($manuallyDefinedSchema) > 0) {
            return $manuallyDefinedSchema;
        }

        return $this->compiler->compile($this->generatorsFactory->get());
    }
}
