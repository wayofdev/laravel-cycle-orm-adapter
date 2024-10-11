<?php

declare(strict_types=1);

namespace WayOfDev\Cycle;

use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\Select;
use Cycle\ORM\Select\Repository as CycleRepository;
use Throwable;

/**
 * Repository provides ability to load entities and construct queries.
 *
 * @template TEntity of object
 */
class Repository extends CycleRepository
{
    /**
     * Create repository linked to one specific selector.
     *
     * @param Select<TEntity> $select
     */
    public function __construct(
        // @phpstan-ignore-next-line
        protected Select $select,
        protected EntityManagerInterface $entityManager,
    ) {
        parent::__construct($select);
    }

    /**
     * @throws Throwable
     */
    public function persist(object $entity, bool $cascade = true): void
    {
        $this->entityManager->persist(
            $entity,
            $cascade
        );

        $this->entityManager->run();
    }
}
