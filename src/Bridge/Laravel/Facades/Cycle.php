<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class Cycle extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'cycle';
    }
}
