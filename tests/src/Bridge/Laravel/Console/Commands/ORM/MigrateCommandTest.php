<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Laravel\Console\Commands\ORM;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use WayOfDev\Tests\TestCase;

use function file_put_contents;

class MigrateCommandTest extends TestCase
{
    public const USER_MIGRATION = [
        'default.users',
        'create table',
        'add column id',
        'add column user_id',
        'add column name',
        'add index on [user_id]',
        'add foreign key on user_id',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('cycle:migrate:init', ['-vvv' => true]);

        File::delete(__DIR__ . '/../../../../../../app/Entities/Tag.php');
    }

    /**
     * @test
     */
    public function it_runs_migrate(): void
    {
        $this->assertConsoleCommandOutputContainsStrings('cycle:orm:migrate', [], self::USER_MIGRATION);
        $this->assertConsoleCommandOutputContainsStrings('cycle:orm:migrate', [], 'Outstanding migrations found');
    }

    /**
     * @test
     */
    public function it_runs_migrate_with_no_changes(): void
    {
        Artisan::call('cycle:orm:migrate');
        Artisan::call('cycle:migrate', ['--force' => true]);
        Artisan::call('cycle:orm:migrate');

        $this->assertConsoleCommandOutputContainsStrings('cycle:orm:migrate', [], 'No database changes');
    }

    /**
     * @test
     */
    public function it_creates_migration_when_entity_appeared(): void
    {
        $this->assertConsoleCommandOutputContainsStrings('cycle:orm:migrate', ['-r' => true], self::USER_MIGRATION);

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

        $this->assertConsoleCommandOutputContainsStrings('cycle:orm:migrate', ['-r' => true], [
            'default.tags',
            'create table',
            'add column id',
        ]);

        File::delete($entity);
    }
}
