<?php

declare(strict_types=1);

namespace WayOfDev\Laravel\Package\Facades;

use Illuminate\Support\Facades\Facade;

class Package extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'package';
    }
}
