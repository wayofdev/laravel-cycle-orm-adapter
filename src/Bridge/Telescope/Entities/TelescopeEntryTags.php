<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Telescope\Entities;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\ForeignKey;
use Cycle\Annotated\Annotation\Relation\HasMany;
use Cycle\Annotated\Annotation\Table\Index;
use Illuminate\Support\Collection;

#[Index(columns: ['entry_uuid', 'tag'])]
#[Index(columns: ['tag'])]
#[ForeignKey(target: TelescopeEntry::class, innerKey: 'entry_uuid', outerKey: 'uuid', action: 'CASCADE')]
#[Entity(table: 'telescope_entry_tags')]
class TelescopeEntryTags
{
    #[Column(type: 'uuid', primary: true)]
    public string $entryUuid;

    #[Column(type: 'string')]
    public string $tag;
}
