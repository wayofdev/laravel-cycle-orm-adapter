<?php

declare(strict_types=1);

namespace WayOfDev\App\Entities;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use WayOfDev\App\Repositories\RoleRepositoryInterface;

#[Entity(repository: RoleRepositoryInterface::class)]
class Role
{
    #[Column(type: 'primary')]
    public int $id;

    #[Column(type: 'string')]
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
