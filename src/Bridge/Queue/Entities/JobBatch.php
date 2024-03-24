<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Queue\Entities;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

#[Entity(table: 'job_batches')]
class JobBatch
{
    #[Column(type: 'string', primary: true)]
    public string $id;

    #[Column(type: 'string')]
    public string $name;

    #[Column(type: 'integer')]
    public int $totalJobs;

    #[Column(type: 'integer')]
    public int $pendingJobs;

    #[Column(type: 'integer')]
    public int $failedJobs;

    #[Column(type: 'longText')]
    public string $failedJobIds;

    #[Column(type: 'longText', nullable: true)]
    public ?string $options;

    #[Column(type: 'integer', nullable: true)]
    public ?int $cancelledAt;

    #[Column(type: 'integer')]
    public int $createdAt;

    #[Column(type: 'integer', nullable: true)]
    public ?int $finishedAt;
}
