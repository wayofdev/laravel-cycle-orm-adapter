<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Tests\Bridge\Laravel\Providers;

use WayOfDev\Cycle\Contracts\Config\Repository as ConfigRepository;
use WayOfDev\Cycle\Tests\TestCase;

class CycleServiceProviderTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_get_adapter_config_instance_from_container(): void
    {
        $config = $this->app->make(ConfigRepository::class);

        self::assertInstanceOf(ConfigRepository::class, $config);
        self::assertEquals(app_path(), $config->directories()[0]);
    }
}
