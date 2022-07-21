<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Tests\Stubs;

use Illuminate\Database\Eloquent\Factories\HasFactory;

final class User
{
    use HasFactory;

    private mixed $id;

    private string $password;

    private string $rememberToken = '';

    private string $name;

    public static function resolveFactoryName(): string
    {
        return UserFactory::class;
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(mixed $id): void
    {
        $this->id = $id;
    }

    public function setPassword(mixed $password): void
    {
        $this->password = $password;
    }

    public function setRememberToken(string $rememberToken): void
    {
        $this->rememberToken = $rememberToken;
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
