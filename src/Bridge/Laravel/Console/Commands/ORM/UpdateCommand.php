<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Console\Commands\ORM;

use Illuminate\Console\Command;
use WayOfDev\Cycle\Contracts\GeneratorLoader;
use WayOfDev\Cycle\Contracts\SchemaCompiler;

/**
 * See original spiral framework commands.
 *
 * @see https://github.com/spiral/cycle-bridge/blob/master/src/Console/Command/CycleOrm/UpdateCommand.php
 */
final class UpdateCommand extends Command
{
    protected $signature = 'cycle:orm';

    protected $description = 'Update (init) cycle schema from database and annotated classes';

    public function handle(SchemaCompiler $schemaCompiler, GeneratorLoader $generators): int
    {
        $this->info('Updating ORM schema...');

        $schemaCompiler->compile($generators->get());

        $this->info('Done');

        return self::SUCCESS;
    }
}
