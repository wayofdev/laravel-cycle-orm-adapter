<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Telescope\Events\Database;

/**
 * @see https://github.com/laravel/framework/blob/11.x/src/Illuminate/Database/Connection.php
 * @see https://github.com/laravel/framework/blob/11.x/src/Illuminate/Database/Events/QueryExecuted.php
 */
final class QueryExecuted
{
    public function __construct(public string $sql, public array $bindings, public ?float $time, public ?string $driver = null)
    {
        $this->time = $time * 1000;
        $this->driver = null !== $driver ? 'CycleORM/' . $driver : 'CycleORM';
    }
}
