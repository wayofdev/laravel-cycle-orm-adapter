<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Queue\Entities;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table\Index;

#[Index(columns: ['queue'])]
#[Entity(table: 'jobs')]
class Job
{
    #[Column(type: 'primary')]
    public int $id;

    #[Column(type: 'string')]
    public string $queue;

    #[Column(type: 'longText')]
    public string $payload;

    #[Column(type: 'tinyInteger', unsigned: true)]
    public int $attempts;

    #[Column(type: 'timestamp', nullable: true)]
    public ?int $reservedAt;

    #[Column(type: 'timestamp')]
    public int $availableAt;

    #[Column(type: 'timestamp')]
    public int $createdAt;
}
