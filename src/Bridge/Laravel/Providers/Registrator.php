<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Providers;

interface Registrator
{
    public const CFG_KEY = 'cycle';

    public const CFG_KEY_DATABASE = 'cycle.database';

    public const CFG_KEY_TOKENIZER = 'cycle.tokenizer';

    public const CFG_KEY_ATTRIBUTES = 'cycle.attributes';

    public const CFG_KEY_MIGRATIONS = 'cycle.migrations';

    public const CFG_KEY_SCHEMA = 'cycle.schema';

    public const CFG_KEY_WARMUP = 'cycle.warmup';

    public const CFG_KEY_RELATIONS = 'cycle.customRelations';
}
