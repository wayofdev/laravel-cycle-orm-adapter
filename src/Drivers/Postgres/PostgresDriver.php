<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Drivers\Postgres;

use Cycle\Database\Driver\Postgres\PostgresDriver as CyclePostgresDriver;
use Cycle\Database\Exception\ReadonlyConnectionException;
use Cycle\Database\StatementInterface;
use WayOfDev\Cycle\Bridge\Laravel\Events\Database\QueryExecuted;

use function microtime;
use function round;

final class PostgresDriver extends CyclePostgresDriver
{
    public function query(string $statement, array $parameters = []): StatementInterface
    {
        $start = microtime(true);

        $statement = $this->statement($statement, $parameters);

        // @todo: move to dependency injection
        event(
            new QueryExecuted(
                $statement->getQueryString(),
                $parameters,
                $this->getElapsedTime($start),
                $this
            )
        );

        return $statement;
    }

    public function execute(string $query, array $parameters = []): int
    {
        if ($this->isReadonly()) {
            throw ReadonlyConnectionException::onWriteStatementExecution();
        }

        $start = microtime(true);

        $statement = $this->statement($query, $parameters)->rowCount();

        // @todo: move to dependency injection
        event(
            new QueryExecuted(
                $query,
                $parameters,
                $this->getElapsedTime($start),
                $this
            )
        );

        return $statement;
    }

    private function getElapsedTime(float $start): float
    {
        return round((microtime(true) - $start) * 1000, 2);
    }
}
