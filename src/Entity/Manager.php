<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Entity;

use Cycle\ORM\ORMInterface;
use Cycle\ORM\RepositoryInterface;
use WayOfDev\Cycle\Contracts\EntityManager;

final class Manager implements EntityManager
{
    private ORMInterface $orm;

    public function __construct(ORMInterface $orm)
    {
        $this->orm = $orm;
    }

    public function persist(object $entity, bool $cascade = true): EntityManager
    {
        // ...
    }

    public function delete(object $entity, bool $cascade = true): EntityManager
    {
        // ...
    }

    /**
     * @template TEntity of object
     *
     * @return RepositoryInterface<TEntity>
     */
    private function getRepository(object $entity): RepositoryInterface
    {
        return $this->orm->getRepository($entity);
    }
}
