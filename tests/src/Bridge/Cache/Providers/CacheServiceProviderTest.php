<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Cache\Providers;

use PHPUnit\Framework\Attributes\Test;
use WayOfDev\Cycle\Bridge\Cache\Providers\CacheServiceProvider;
use WayOfDev\Tests\TestCase;

class CacheServiceProviderTest extends TestCase
{
    #[Test]
    public function it_skips_entities_registration_by_default(): void
    {
        $this->artisanCall('cache:clear');
        $this->refreshApplication();

        $this::assertFalse($this->app->providerIsLoaded(CacheServiceProvider::class));

        $this->assertConsoleCommandOutputDoesNotContainStrings('cycle:orm:render', ['--no-color' => true], [
            '[cache] :: default.cache',
            '[cacheLock] :: default.cache_locks',
            'Entity: WayOfDev\Cycle\Bridge\Cache\Entities\Cache',
            'Entity: WayOfDev\Cycle\Bridge\Cache\Entities\CacheLock',
        ]);
    }

    #[Test]
    public function it_generates_migration_when_enabled(): void
    {
        $this->app->register(CacheServiceProvider::class);

        $this::assertTrue($this->app->providerIsLoaded(CacheServiceProvider::class));
        $this->assertConsoleCommandOutputContainsStrings('cycle:orm:render', ['--no-color' => true], [
            '[cache] :: default.cache',
            '[cacheLock] :: default.cache_locks',
            'Entity: WayOfDev\Cycle\Bridge\Cache\Entities\Cache',
            'Entity: WayOfDev\Cycle\Bridge\Cache\Entities\CacheLock',
        ]);
    }
}
