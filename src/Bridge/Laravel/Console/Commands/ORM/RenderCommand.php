<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Console\Commands\ORM;

use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Renderer\OutputSchemaRenderer;
use Cycle\Schema\Renderer\PhpSchemaRenderer;
use Cycle\Schema\Renderer\SchemaToArrayConverter;
use WayOfDev\Cycle\Bridge\Laravel\Console\Commands\Migrations\AbstractCommand;

/**
 * See original spiral framework commands.
 *
 * @see https://github.com/spiral/cycle-bridge/blob/master/src/Console/Command/Migrate/StatusCommand.php
 */
final class RenderCommand extends AbstractCommand
{
    protected $signature = 'cycle:orm:render
                           {--nc|no-color : Display output without colors. }
                           {--p|php : Display output as PHP code. }';

    protected $description = 'Render available CycleORM schemas.';

    public function handle(
        SchemaInterface $schema,
        SchemaToArrayConverter $converter
    ): int {
        $format = $this->option('no-color') ?
            OutputSchemaRenderer::FORMAT_PLAIN_TEXT :
            OutputSchemaRenderer::FORMAT_CONSOLE_COLOR;

        $renderer = $this->option('php')
            ? new PhpSchemaRenderer()
            : new OutputSchemaRenderer($format);

        $this->output->writeln(
            $renderer->render(
                $converter->convert($schema)
            )
        );

        return self::SUCCESS;
    }
}
