<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Queue\Entities;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table\Index;
use DateTimeImmutable;

#[Index(columns: ['uuid'], unique: true)]
#[Entity(table: 'failed_jobs')]
class FailedJob
{
    #[Column(type: 'primary')]
    public int $id;

    #[Column(type: 'string')]
    public string $uuid;

    #[Column(type: 'text')]
    public string $connection;

    #[Column(type: 'text')]
    public string $queue;

    #[Column(type: 'longText')]
    public string $payload;

    #[Column(type: 'longText')]
    public string $exception;

    #[Column(type: 'datetime', default: 'CURRENT_TIMESTAMP')]
    public DateTimeImmutable $failedAt;
}
