<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Collection;

use Cycle\ORM\Collection\CollectionFactoryInterface;
use Cycle\ORM\Collection\IlluminateCollectionFactory;
use Spiral\Core\InjectableConfig;

final class CollectionConfig extends InjectableConfig
{
    public function getDefaultCollectionName(): string
    {
        return $this->config['collections']['default'] ?? 'illuminate';
    }

    /**
     * @return class-string<CollectionFactoryInterface>
     */
    public function getDefaultCollectionFactoryClass(): string
    {
        if (! isset($this->config['collections'])) {
            return IlluminateCollectionFactory::class;
        }

        return $this->config['collections']['factories'][$this->getDefaultCollectionName()];
    }
}
