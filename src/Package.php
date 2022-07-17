<?php

declare(strict_types=1);

namespace WayOfDev\Laravel\Package;

final class Package
{
    private string $name;

    public static function fromName(string $name): self
    {
        return new self($name);
    }

    public function name(): string
    {
        return $this->name;
    }

    private function __construct(string $name)
    {
        $this->name = $name;
    }
}
