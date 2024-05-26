<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Telescope;

use Psr\Log\LoggerInterface;
use Stringable;
use WayOfDev\Cycle\Bridge\Telescope\Events\Database\QueryExecuted;

class TelescopeLogger implements LoggerInterface
{
    private readonly LoggerInterface $parentLogger;

    public function __construct($parentLogger)
    {
        $this->parentLogger = $parentLogger;
    }

    public function log($level, $message, array $context = []): void
    {
        $this->parentLogger->log($level, $message, $context);

        if ($level === 'info' && isset($context['elapsed'])) {
            event(
                new QueryExecuted(
                    sql: $message,
                    bindings: $context['parameters'] ?? [],
                    time: $context['elapsed'],
                    driver: $context['driver'] ?? null
                )
            );
        }
    }

    public function info(Stringable|string $message, array $context = []): void
    {
        $this->log('info', $message, $context);
    }

    public function error(Stringable|string $message, array $context = []): void
    {
        $this->log('error', $message, $context);
    }

    public function alert(Stringable|string $message, array $context = []): void
    {
        $this->log('alert', $message, $context);
    }

    public function emergency(Stringable|string $message, array $context = []): void
    {
        $this->log('emergency', $message, $context);
    }

    public function critical(Stringable|string $message, array $context = []): void
    {
        $this->log('emergency', $message, $context);
    }

    public function warning(Stringable|string $message, array $context = []): void
    {
        $this->log('warning', $message, $context);
    }

    public function notice(Stringable|string $message, array $context = []): void
    {
        $this->log('notice', $message, $context);
    }

    public function debug(Stringable|string $message, array $context = []): void
    {
        $this->log('debug', $message, $context);
    }
}
