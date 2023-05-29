<?php

declare(strict_types=1);

namespace WayOfDev\App\Entities;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\Annotated\Annotation\Relation\HasMany;
use Illuminate\Support\Collection;
use WayOfDev\App\Repositories\UserRepository;

#[Entity(repository: UserRepository::class)]
class User
{
    #[Column(type: 'primary')]
    public int $id;

    #[Column(type: 'integer', name: 'user_id', nullable: true)]
    public ?int $userId = null;

    #[Column(type: 'string', nullable: true)]
    public ?string $email = null;

    #[Column(type: 'string', nullable: true)]
    public ?string $company = null;

    #[BelongsTo(target: User::class, innerKey: 'userId', nullable: true)]
    public ?User $friend = null;

    #[HasMany(target: User::class, outerKey: 'userId', nullable: true)]
    public null|iterable $friends = [];

    #[HasMany(target: User::class, outerKey: 'userId', nullable: true, collection: 'array')]
    public ?array $friendsAsArray = [];

    #[HasMany(target: Role::class)]
    public ?Collection $roles;

    #[HasMany(target: User::class, outerKey: 'userId', nullable: true, collection: 'illuminate')]
    public ?Collection $friendsAsIlluminateCollection;

    #[Column(type: 'string', nullable: true)]
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;

        $this->roles = new Collection();
        $this->friendsAsIlluminateCollection = new Collection();
    }

    public function addFriend(User $user): void
    {
        $this->friendsAsArray[] = $user;
        $user->friend = $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
