<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Schema\Config;

use Spiral\Core\InjectableConfig;

use function array_merge;

class SchemaConfig extends InjectableConfig
{
    public const CONFIG = 'schema';

    /**
     * Create a new schema configuration instance.
     */
    public function __construct(array $config = [])
    {
        parent::__construct(array_merge([
            'cache' => [
                'enabled' => false,
                'store' => 'file',
            ],
            'defaults' => [],
            'collections' => [
                'default' => 'array',
                'factories' => [],
            ],
            'generators' => [],
        ], $config));
    }

    /**
     * Get the list of generators.
     */
    public function generators(): array
    {
        return $this->config['generators'];
    }

    /**
     * Get the default collection FQCN.
     */
    public function defaultCollectionFQCN(): string
    {
        $default = $this->config['collections']['default'];
        $factories = $this->config['collections']['factories'];

        return $factories[$default];
    }

    /**
     * Check if schema caching is enabled.
     */
    public function cacheSchema(): bool
    {
        return $this->config['cache']['enabled'];
    }

    /**
     * Get the default configuration values.
     */
    public function defaults()
    {
        return $this->config['defaults'];
    }
}
