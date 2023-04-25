<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class Cycle extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'cycle';
    }
}
