<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel;

use Cycle\Database\Driver\DriverInterface;
use Cycle\Database\LoggerFactoryInterface;
use Illuminate\Log\Logger;
use Psr\Log\LoggerInterface;

final class LoggerFactory implements LoggerFactoryInterface
{
    public function __construct(private readonly Logger $logger)
    {
    }

    public function getLogger(DriverInterface $driver = null): LoggerInterface
    {
        return $this->logger->getLogger();
    }
}
