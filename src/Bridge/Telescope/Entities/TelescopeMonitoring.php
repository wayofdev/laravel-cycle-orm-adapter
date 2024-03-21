<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Telescope\Entities;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

#[Entity(table: 'telescope_monitoring')]
class TelescopeMonitoring
{
    #[Column(type: 'string', primary: true)]
    public string $tag;
}
