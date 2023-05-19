<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Drivers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use WayOfDev\Cycle\Bridge\Laravel\Events\Database\QueryExecuted;
use WayOfDev\Cycle\Drivers\Postgres\PostgresDriver;
use WayOfDev\Tests\TestCase;

final class PostgresDriverTest extends TestCase
{
    /**
     * @test
     */
    public function it_dispatches_event_on_query(): void
    {
        $databaseConfig = Config::get('cycle.database.drivers.pgsql');
        $driver = PostgresDriver::create($databaseConfig);

        Event::fake([QueryExecuted::class]);

        $driver->query('select * from cycle_migrations');

        Event::assertDispatched(QueryExecuted::class, function ($event) {
            $this::assertSame('select * from cycle_migrations', $event->sql);

            return true;
        });
    }
}
