<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Tests\Stubs;

use WayOfDev\Cycle\Repository;

class UserRepository extends Repository
{
    public function findByUsername(string $username): ?User
    {
        return $this->findOne(['username' => $username]);
    }
}
