<?php

declare(strict_types=1);

namespace WayOfDev\App\Entities;

use Cycle\Database\DatabaseInterface;
use Stringable;

final class UserId implements Stringable
{
    private readonly string $id;

    private function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function create(string $userId): self
    {
        return new self($userId);
    }

    public static function fromString(string $aggregateRootId): static
    {
        return new self($aggregateRootId);
    }

    public static function castValue(string $value, DatabaseInterface $db): self
    {
        return self::fromString($value);
    }

    public function toString(): string
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
