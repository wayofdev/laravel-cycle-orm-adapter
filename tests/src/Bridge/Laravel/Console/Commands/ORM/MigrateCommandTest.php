<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Laravel\Console\Commands\ORM;

use Cycle\ORM\SchemaInterface;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use WayOfDev\Cycle\Contracts\CacheManager;
use WayOfDev\Tests\TestCase;

use function file_put_contents;

class MigrateCommandTest extends TestCase
{
    public const USER_MIGRATION = [
        'default.users',
        'create table',
        'add column [id]',
        'add column [user_id]',
        'add column [name]',
        'add index on [user_id]',
        'add foreign key on [user_id]',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisanCall('cycle:migrate:init', ['-vvv' => true]);

        File::delete(__DIR__ . '/../../../../../../app/Entities/Tag.php');
    }

    #[Test]
    public function it_runs_migrate(): void
    {
        $this->assertConsoleCommandOutputContainsStrings('cycle:orm:migrate', ['-n' => true, '-v' => true], self::USER_MIGRATION);

        $this->assertConsoleCommandOutputContainsStrings('cycle:orm:migrate', ['-n' => true], [
            'Outstanding migrations found',
        ]);
    }

    #[Test]
    public function it_runs_migrate_with_no_changes(): void
    {
        $this->artisanCall('cycle:orm:migrate', ['-n' => true]);
        $this->artisanCall('cycle:migrate', ['--force' => true]);
        $this->artisanCall('cycle:orm:migrate', ['-n' => true]);

        $this->assertConsoleCommandOutputContainsStrings('cycle:orm:migrate', [], 'No database changes');
    }

    #[Test]
    public function it_creates_migration_when_entity_appeared(): void
    {
        $this->assertConsoleCommandOutputContainsStrings('cycle:orm:migrate', ['-r' => true, '-v' => true], self::USER_MIGRATION);

        $entity = __DIR__ . '/../../../../../../app/Entities/Tag.php';

        file_put_contents(
            $entity,
            <<<'PHP'
                <?php

                declare(strict_types=1);

                namespace WayOfDev\App\Entities;

                use Cycle\Annotated\Annotation\Column;
                use Cycle\Annotated\Annotation\Entity;

                #[Entity]
                class Tag
                {
                    #[Column(type: 'primary')]
                    public int $id;
                }
                PHP
        );

        $this->assertConsoleCommandOutputContainsStrings('cycle:orm:migrate', ['-r' => true, '-v' => true], [
            'default.tags',
            'create table',
            'add column [id]',
        ]);

        File::delete($entity);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Test]
    public function it_passes_schema_defaults_to_compiler(): void
    {
        config()->set('cycle.schema.defaults', [
            SchemaInterface::TYPECAST_HANDLER => ['foo'],
        ]);

        $this->artisanCall('cycle:orm:migrate', ['-n' => true]);
        $this->artisanCall('cycle:migrate', ['--force' => true]);
        $this->artisanCall('cycle:orm:migrate', ['-n' => true]);

        $cacheManager = $this->app->get(CacheManager::class);

        $this::assertSame(['foo'], $cacheManager->get()['role'][SchemaInterface::TYPECAST_HANDLER]);
    }
}
