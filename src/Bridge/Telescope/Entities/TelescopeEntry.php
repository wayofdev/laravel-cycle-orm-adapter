<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Telescope\Entities;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table\Index;
use DateTimeImmutable;

#[Index(columns: ['uuid'], unique: true, name: 'telescope_entries_uuid_unique')]
#[Index(columns: ['batch_id'], name: 'telescope_entries_batch_id_index')]
#[Index(columns: ['family_hash'], name: 'telescope_entries_family_hash_index')]
#[Index(columns: ['created_at'], name: 'telescope_entries_created_at_index')]
#[Index(columns: ['type', 'should_display_on_index'], name: 'telescope_entries_type_should_display_on_index_index')]
#[Entity(table: 'telescope_entries')]
class TelescopeEntry
{
    #[Column(type: 'primary')]
    public int $sequence;

    #[Column(type: 'uuid')]
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
