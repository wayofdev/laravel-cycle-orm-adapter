<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Telescope\Entities;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\ForeignKey;
use Cycle\Annotated\Annotation\Table\Index;

#[Index(columns: ['entry_uuid', 'tag'], unique: true)]
#[Index(columns: ['tag'])]
#[ForeignKey(target: TelescopeEntry::class, innerKey: 'entry_uuid', outerKey: 'uuid', action: 'CASCADE')]
#[Entity(table: 'telescope_entries_tags')]
class TelescopeEntryTag
{
    #[Column(type: 'primary')]
    public int $id;

    #[Column(type: 'uuid')]
    public string $entryUuid;

    #[Column(type: 'string')]
    public string $tag;
}
