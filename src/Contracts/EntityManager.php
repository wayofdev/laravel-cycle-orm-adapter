<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Contracts;

interface EntityManager
{
    public function persist(object $entity, bool $cascade = true): self;

    public function delete(object $entity, bool $cascade = true): self;
}
