<?php

declare(strict_types=1);

namespace WayOfDev\Laravel\Package;

use Spatie\LaravelPackageTools\Package as SpatiePackage;
use Spatie\LaravelPackageTools\PackageServiceProvider as SpatiePackageProvider;
use WayOfDev\Laravel\Package\Commands\PackageCommand;

final class PackageServiceProvider extends SpatiePackageProvider
{
    public function configurePackage(SpatiePackage $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('package')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_package_table')
            ->hasCommand(PackageCommand::class);
    }
}
