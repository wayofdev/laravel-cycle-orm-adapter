<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Laravel\Console\Commands\ORM;

use Cycle\ORM\Parser\Typecast;
use Cycle\ORM\SchemaInterface;
use WayOfDev\Tests\TestCase;

class RenderCommandTest extends TestCase
{
    /**
     * @test
     */
    public function it_runs_handle(): void
    {
        $this->assertConsoleCommandOutputContainsStrings('cycle:orm:render', ['--no-color' => true], [
            '[user] :: default.users',
            'Entity: WayOfDev\App\Entities\User',
            'Mapper: Cycle\ORM\Mapper\Mapper',
            'Repository: WayOfDev\App\Repositories\UserRepository',
        ]);
    }

    /**
     * @todo
     */
    public function it_runs_handle_with_redefined_schema(): void
    {
        $this->app['config']->set('cycle.schema.defaults', [
            SchemaInterface::MAPPER => 'custom_mapper',
            SchemaInterface::REPOSITORY => 'custom_repository',
            SchemaInterface::SCOPE => 'custom_scope',
            SchemaInterface::TYPECAST_HANDLER => [
                Typecast::class,
                'custom_typecast_handler',
            ],
        ]);

        $this->artisanCall('cycle:orm:render', ['--no-color' => true]);

        $this->assertConsoleCommandOutputContainsStrings('cycle:orm:render', ['--no-color' => true], [
            'Mapper: custom_mapper',
            'Repository: custom_repository',
            'Scope: custom_scope',
            'Typecast: Cycle\ORM\Parser\Typecast',
            'custom_typecast_handler',
        ]);
    }
}
