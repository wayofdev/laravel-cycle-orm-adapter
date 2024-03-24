<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Session\Providers;

use PHPUnit\Framework\Attributes\Test;
use WayOfDev\Cycle\Bridge\Session\Providers\SessionServiceProvider;
use WayOfDev\Tests\TestCase;

class SessionServiceProviderTest extends TestCase
{
    #[Test]
    public function it_skips_entities_registration_by_default(): void
    {
        $this->refreshApplication();

        $this::assertFalse($this->app->providerIsLoaded(SessionServiceProvider::class));

        $this->assertConsoleCommandOutputDoesNotContainStrings('cycle:orm:render', ['--no-color' => true], [
            '[session] :: default.sessions',
            'Entity: WayOfDev\Cycle\Bridge\Session\Entities\Session',
        ]);
    }

    #[Test]
    public function it_generates_migration_when_enabled(): void
    {
        $this->app->register(SessionServiceProvider::class);

        $this::assertTrue($this->app->providerIsLoaded(SessionServiceProvider::class));
        $this->assertConsoleCommandOutputContainsStrings('cycle:orm:render', ['--no-color' => true], [
            '[session] :: default.sessions',
            'Entity: WayOfDev\Cycle\Bridge\Session\Entities\Session',
        ]);
    }
}
