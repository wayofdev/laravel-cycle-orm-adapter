<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Telescope\Entities;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table\Index;
use DateTimeImmutable;

#[Index(columns: ['batch_id'])]
#[Index(columns: ['family_hash'])]
#[Index(columns: ['created_at'])]
#[Index(columns: ['type', 'should_display_on_index'])]
#[Entity(table: 'telescope_entries')]
class TelescopeEntry
{
    #[Column(type: 'bigInteger', primary: true)]
    public int $sequence;

    #[Column(type: 'uuid', unique: true)]
    public string $uuid;

    #[Column(type: 'uuid')]
    public string $batchId;

    #[Column(type: 'string', nullable: true)]
    public string $familyHash;

    #[Column(type: 'boolean', default: true)]
    public bool $shouldDisplayOnIndex;

    #[Column(type: 'string', size: 20)]
    public string $type;

    #[Column(type: 'longText')]
    public string $content;

    #[Column(type: 'datetime', nullable: true)]
    public ?DateTimeImmutable $createdAt;
}
