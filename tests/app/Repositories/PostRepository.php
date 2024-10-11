<?php

declare(strict_types=1);

namespace WayOfDev\App\Repositories;

use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\Select;
use WayOfDev\Cycle\Repository;

class PostRepository extends Repository implements PostRepositoryInterface
{
    public function __construct(
        // @phpstan-ignore-next-line
        protected Select $select,
        protected EntityManagerInterface $entityManager,
    ) {
        parent::__construct($select, $entityManager);
    }

    public function persist(object $entity, bool $cascade = true): void
    {
        $this->entityManager->persist(
            $entity,
            $cascade
        );

        $this->entityManager->run();
    }
}
