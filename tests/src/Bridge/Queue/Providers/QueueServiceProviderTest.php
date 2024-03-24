<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Queue\Providers;

use PHPUnit\Framework\Attributes\Test;
use WayOfDev\Cycle\Bridge\Queue\Providers\QueueServiceProvider;
use WayOfDev\Tests\TestCase;

class QueueServiceProviderTest extends TestCase
{
    #[Test]
    public function it_skips_entities_registration_by_default(): void
    {
        $this->refreshApplication();

        $this::assertFalse($this->app->providerIsLoaded(QueueServiceProvider::class));

        $this->assertConsoleCommandOutputDoesNotContainStrings('cycle:orm:render', ['--no-color' => true], [
            '[job] :: default.jobs',
            '[failedJob] :: default.failed_jobs',
            '[jobBatch] :: default.job_batches',
            'Entity: WayOfDev\Cycle\Bridge\Queue\Entities\Job',
            'Entity: WayOfDev\Cycle\Bridge\Queue\Entities\JobBatch',
            'Entity: WayOfDev\Cycle\Bridge\Queue\Entities\FailedJob',
        ]);
    }

    #[Test]
    public function it_generates_migration_when_enabled(): void
    {
        $this->app->register(QueueServiceProvider::class);

        $this::assertTrue($this->app->providerIsLoaded(QueueServiceProvider::class));
        $this->assertConsoleCommandOutputContainsStrings('cycle:orm:render', ['--no-color' => true], [
            '[job] :: default.jobs',
            '[failedJob] :: default.failed_jobs',
            '[jobBatch] :: default.job_batches',
            'Entity: WayOfDev\Cycle\Bridge\Queue\Entities\Job',
            'Entity: WayOfDev\Cycle\Bridge\Queue\Entities\JobBatch',
            'Entity: WayOfDev\Cycle\Bridge\Queue\Entities\FailedJob',
        ]);
    }
}
