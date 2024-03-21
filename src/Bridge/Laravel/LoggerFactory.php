<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel;

use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\Driver\DriverInterface;
use Cycle\Database\LoggerFactoryInterface;
use Cycle\Database\NamedInterface;
use Illuminate\Log\LogManager;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use WayOfDev\Cycle\Bridge\Telescope\TelescopeLogger;
use WayOfDev\Cycle\Support\Arr;

final class LoggerFactory implements LoggerFactoryInterface
{
    private array $config;

    public function __construct(
        private readonly LogManager $manager,
        DatabaseConfig $databaseConfig
    ) {
        $this->config = $databaseConfig->toArray()['logger'] ?? [];
    }

    public function getLogger(?DriverInterface $driver = null): LoggerInterface
    {
        if (! isset($this->config['default'])) {
            return new NullLogger();
        }

        $channel = $this->config['default'];

        if ($driver instanceof NamedInterface) {
            $driverName = $driver->getName();

            if (isset($this->config['drivers'][$driverName])) {
                $channel = $this->config['drivers'][$driverName];
            }
        }

        $logger = $this->manager->channel($channel);

        if (Arr::get($this->config, 'use_telescope') === true) {
            return new TelescopeLogger($logger);
        }

        return $logger;
    }
}
