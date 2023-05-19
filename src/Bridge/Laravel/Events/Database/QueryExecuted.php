<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Events\Database;

use Cycle\Database\Driver\DriverInterface;

/**
 * @see https://github.com/laravel/framework/blob/10.x/src/Illuminate/Database/Connection.php
 * @see https://github.com/laravel/framework/blob/10.x/src/Illuminate/Database/Events/QueryExecuted.php
 */
final class QueryExecuted
{
    public string $connectionName;

    public function __construct(public string $sql, public array $bindings, public ?float $time, public DriverInterface $driver)
    {
        // $this->connectionName = $driver->getName();
    }
}
