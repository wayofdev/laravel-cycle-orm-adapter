<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Schema\Config;

use Spiral\Core\InjectableConfig;

use function array_merge;

class SchemaConfig extends InjectableConfig
{
    public const CONFIG = 'schema';

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

    public function generators(): array
    {
        return $this->config['generators'];
    }

    public function defaultCollectionFQCN(): string
    {
        $default = $this->config['collections']['default'];
        $factories = $this->config['collections']['factories'];

        return $factories[$default];
    }
}
