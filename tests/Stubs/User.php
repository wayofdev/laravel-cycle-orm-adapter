<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Tests\Stubs;

class User
{
    public function __construct(public string $name = '')
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
