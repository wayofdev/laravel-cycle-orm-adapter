<?php

declare(strict_types=1);

namespace WayOfDev\Cycle;

use Cycle\ORM\Select;
use Cycle\ORM\Select\Repository as CycleRepository;

/**
 * Repository provides ability to load entities and construct queries.
 *
 * @template TEntity of object
 * @extends CycleRepository<TEntity>
 */
class Repository extends CycleRepository
{
    public function __construct(Select $select)
    {
        $this->select = $select;

        parent::__construct($select);
    }
}
