<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Laravel\Console\Commands\ORM;

use Cycle\ORM\SchemaInterface;
use PHPUnit\Framework\Attributes\Test;
use WayOfDev\App\Entities\User;
use WayOfDev\Tests\TestCase;

class UpdateCommandTest extends TestCase
{
    #[Test]
    public function it_runs_handle(): void
    {
        $this->artisanCall('cycle:orm');

        /** @var SchemaInterface $schema */
        $schema = $this->app->make(SchemaInterface::class);

        $this::assertTrue($schema->defines('user'));

        $this::assertSame(
            User::class,
            $schema->define('user', SchemaInterface::ENTITY)
        );
    }
}
