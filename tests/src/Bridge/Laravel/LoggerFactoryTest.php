<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Laravel;

use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\Driver\DriverInterface;
use Cycle\Database\NamedInterface;
use Illuminate\Log\LogManager;
use Mockery as m;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\NullLogger;
use WayOfDev\Cycle\Bridge\Laravel\LoggerFactory;
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
     * @test
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function it_should_return_custom_logger_from_factory(): void
    {
        $mockDriver = m::mock(DriverInterface::class, NamedInterface::class);
        $mockDriver->shouldReceive('getName')->andReturn('custom');

        $loggerFactory = new LoggerFactory(
            $this->app->get(LogManager::class),
            new DatabaseConfig(config('cycle.database'))
        );

        // @phpstan-ignore-next-line
        $logger = $loggerFactory->getLogger($mockDriver);
        $logger->info('Test log entry');

        $logContent = file_get_contents(storage_path('logs/logger-factory-test.log'));
        $this::assertStringContainsString('Test log entry', $logContent);
    }

    /**
     * @test
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
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
