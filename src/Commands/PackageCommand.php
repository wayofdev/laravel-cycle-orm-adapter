<?php

declare(strict_types=1);

namespace WayOfDev\Laravel\Package\Commands;

use Illuminate\Console\Command;

class PackageCommand extends Command
{
    public $signature = 'package';

    public $description = 'Package command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
