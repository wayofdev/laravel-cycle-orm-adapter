<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Telescope\Providers;

use PHPUnit\Framework\Attributes\Test;
use WayOfDev\Cycle\Bridge\Telescope\Providers\TelescopeServiceProvider;
use WayOfDev\Tests\TestCase;

class TelescopeServiceProviderTest extends TestCase
{
    #[Test]
    public function it_skips_entities_registration_by_default(): void
    {
        $this->refreshApplication();

        $this::assertFalse($this->app->providerIsLoaded(TelescopeServiceProvider::class));

        $this->assertConsoleCommandOutputDoesNotContainStrings('cycle:orm:render', ['--no-color' => true], [
            '[telescopeEntry] :: default.telescope_entries',
            '[telescopeEntryTag] :: default.telescope_entries_tags',
            '[telescopeMonitoring] :: default.telescope_monitoring',
            'Entity: WayOfDev\Cycle\Bridge\Telescope\Entities\Telescope',
            'Entity: WayOfDev\Cycle\Bridge\Telescope\Entities\TelescopeEntryTag',
            'Entity: WayOfDev\Cycle\Bridge\Telescope\Entities\TelescopeMonitoring',
        ]);
    }

    #[Test]
    public function it_generates_migration_when_enabled(): void
    {
        $this->app->register(TelescopeServiceProvider::class);

        $this::assertTrue($this->app->providerIsLoaded(TelescopeServiceProvider::class));
        $this->assertConsoleCommandOutputContainsStrings('cycle:orm:render', ['--no-color' => true], [
            '[telescopeEntry] :: default.telescope_entries',
            '[telescopeEntryTag] :: default.telescope_entries_tags',
            '[telescopeMonitoring] :: default.telescope_monitoring',
            'Entity: WayOfDev\Cycle\Bridge\Telescope\Entities\Telescope',
            'Entity: WayOfDev\Cycle\Bridge\Telescope\Entities\TelescopeEntryTag',
            'Entity: WayOfDev\Cycle\Bridge\Telescope\Entities\TelescopeMonitoring',
        ]);
    }
}
