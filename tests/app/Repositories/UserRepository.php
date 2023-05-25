<?php

declare(strict_types=1);

namespace WayOfDev\App\Repositories;

use WayOfDev\App\Entities\User;
use WayOfDev\Cycle\Repository;

class UserRepository extends Repository implements UserRepositoryInterface
{
    public function findByUsername(string $username): ?User
    {
        return $this->findOne(['username' => $username]);
    }
}
