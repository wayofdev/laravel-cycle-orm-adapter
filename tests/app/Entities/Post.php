<?php

declare(strict_types=1);

namespace WayOfDev\App\Entities;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\SoftDelete;
use DateTimeImmutable;
use WayOfDev\App\Repositories\PostRepository;

#[Entity(repository: PostRepository::class)]
#[SoftDelete(column: 'deleted_at')]
class Post
{
    #[Column(type: 'primary')]
    public int $id;

    #[Column(type: 'string')]
    private readonly string $title;

    #[Column(type: 'text')]
    private readonly string $description;

    #[Column(type: 'datetime', nullable: true)]
    private ?DateTimeImmutable $deletedAt = null;

    public function __construct(
        string $title,
        string $description,
    ) {
        $this->title = $title;
        $this->description = $description;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function deletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function softDelete(DateTimeImmutable $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }
}
