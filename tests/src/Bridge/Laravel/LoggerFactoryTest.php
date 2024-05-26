<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Laravel;

use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\Driver\DriverInterface;
use Cycle\Database\LoggerFactoryInterface;
use Cycle\Database\NamedInterface;
use Illuminate\Log\Logger;
use Illuminate\Log\LogManager;
use Illuminate\Support\Facades\Event;
use Mockery as m;
use PHPUnit\Framework\Attributes\Test;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\NullLogger;
use WayOfDev\Cycle\Bridge\Laravel\LoggerFactory;
use WayOfDev\Cycle\Bridge\Telescope\Events\Database\QueryExecuted;
use WayOfDev\Cycle\Bridge\Telescope\TelescopeLogger;
use WayOfDev\Tests\TestCase;

use function file_get_contents;

class LoggerFactoryTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
        parent::tearDown();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Test]
    public function it_should_return_custom_logger_from_factory(): void
    {
        $this->app['config']->set('cycle.database.logger.use_telescope', false);

        $mockDriver = m::mock(DriverInterface::class, NamedInterface::class);
        $mockDriver->shouldReceive('getName')->andReturn('custom');

        $loggerFactory = new LoggerFactory(
            $this->app->get(LogManager::class),
            new DatabaseConfig(config('cycle.database'))
        );

        // @phpstan-ignore-next-line
        $logger = $loggerFactory->getLogger($mockDriver);
        $logger->info('Test log entry');

        $this::assertInstanceOf(Logger::class, $logger);

        $logContent = file_get_contents(storage_path('logs/logger-factory-test.log'));
        $this::assertStringContainsString('Test log entry', $logContent);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Test]
    public function it_should_fire_query_executed_event_when_query_is_logged(): void
    {
        $this->app['config']->set('cycle.database.logger.use_telescope', true);

        Event::fake([QueryExecuted::class]);

        $factory = $this->app->get(LoggerFactoryInterface::class);
        $logger = $factory->getLogger();

        $this::assertInstanceOf(TelescopeLogger::class, $logger);

        // Simulate logging a query
        $logger->info('SELECT * FROM users', ['elapsed' => 50]);

        // Assert that the QueryExecuted event was dispatched
        Event::assertDispatched(QueryExecuted::class, function ($event) {
            return $event->sql === 'SELECT * FROM users';
        });
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Test]
    public function it_should_return_null_logger_from_factory(): void
    {
        config()->set('cycle.database.logger', [
            'default' => null,
            'drivers' => [
                'sqlite' => null,
                'pgsql' => null,
                'mysql' => null,
                'sqlserver' => null,
            ],
        ]);

        $loggerFactory = new LoggerFactory(
            $this->app->get(LogManager::class),
            new DatabaseConfig(config('database'))
        );
        $logger = $loggerFactory->getLogger();

        $this::assertInstanceOf(NullLogger::class, $logger);
    }

    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        // Add custom logging channel configuration
        $app['config']->set('logging.channels.custom_channel', [
            'driver' => 'single',
            'path' => storage_path('logs/logger-factory-test.log'),
            'level' => 'debug',
        ]);

        // Set the database logger configuration
        $app['config']->set('cycle.database.logger', [
            'use_telescope' => true,
            'default' => 'stack',
            'drivers' => [
                'sqlite' => 'stack',
                'pgsql' => 'stack',
                'mysql' => 'stack',
                'sqlserver' => 'stack',
                'custom' => 'custom_channel',
            ],
        ]);
    }
}
