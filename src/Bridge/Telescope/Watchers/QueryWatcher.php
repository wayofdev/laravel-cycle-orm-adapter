<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Telescope\Watchers;

use Cycle\Database\Query\Interpolator;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\Watchers\FetchesStackTrace;
use Laravel\Telescope\Watchers\Watcher;
use WayOfDev\Cycle\Bridge\Telescope\Events\Database\QueryExecuted;

use function md5;
use function number_format;

final class QueryWatcher extends Watcher
{
    use FetchesStackTrace;

    public function register($app): void
    {
        $app['events']->listen(QueryExecuted::class, [$this, 'recordQuery']);
    }

    public function recordQuery(QueryExecuted $event): void
    {
        if (! Telescope::isRecording()) {
            return;
        }

        $time = $event->time;
        $caller = $this->getCallerFromStackTrace();

        if ($caller !== null) {
            Telescope::recordQuery(IncomingEntry::make([
                'connection' => $event->driver,
                'bindings' => $event->bindings,
                'sql' => Interpolator::interpolate($event->sql, $event->bindings),
                'time' => number_format($time, 2, '.', ''),
                'slow' => isset($this->options['slow']) && $time >= $this->options['slow'],
                'file' => $caller['file'],
                'line' => $caller['line'],
                'hash' => $this->familyHash($event),
            ])->tags($this->tags($event)));
        }
    }

    public function familyHash(QueryExecuted $event): string
    {
        return md5($event->sql);
    }

    protected function tags(QueryExecuted $event): array
    {
        return isset($this->options['slow']) && $event->time >= $this->options['slow'] ? ['slow'] : [];
    }
}
