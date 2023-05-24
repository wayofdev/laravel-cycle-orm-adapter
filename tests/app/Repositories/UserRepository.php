<?php

declare(strict_types=1);

namespace WayOfDev\App\Repositories;

use Cycle\ORM\EntityManager;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Select;
use Cycle\ORM\Select\Repository;
use Throwable;
use WayOfDev\App\Entities\User;

class UserRepository extends Repository
{
    private EntityManager $entityManager;

    public function __construct(Select $select, ORMInterface $orm)
    {
        parent::__construct($select);
        $this->entityManager = new EntityManager($orm);
    }

    /**
     * @throws Throwable
     */
    public function persist(User $user, bool $cascade = true): void
    {
        $this->entityManager->persist(
            $user,
            $cascade
        );

        $this->entityManager->run();
    }
}
