<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Console\Commands\ORM;

use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Renderer\OutputSchemaRenderer;
use Cycle\Schema\Renderer\SchemaToArrayConverter;
use Symfony\Component\Console\Output\OutputInterface;
use WayOfDev\Cycle\Console\Commands\Migrations\AbstractCommand;

/**
 * See original spiral framework commands.
 *
 * @see https://github.com/spiral/cycle-bridge/blob/master/src/Console/Command/Migrate/StatusCommand.php
 */
final class RenderCommand extends AbstractCommand
{
    protected $name = 'cycle:orm:render
                       {--nc|no-color : Display output without colors. }';

    protected $description = 'Render available CycleORM schemas.';

    public function handle(
        OutputInterface $output,
        SchemaInterface $schema,
        SchemaToArrayConverter $converter
    ): int {
        $format = $this->option('no-color') ?
            OutputSchemaRenderer::FORMAT_PLAIN_TEXT :
            OutputSchemaRenderer::FORMAT_CONSOLE_COLOR;

        $renderer = new OutputSchemaRenderer($format);

        $output->writeln(
            $renderer->render(
                $converter->convert($schema)
            )
        );

        return self::SUCCESS;
    }
}
