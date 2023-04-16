<?php

declare(strict_types=1);

namespace Jwhulette\FactoryGenerator;

use Jwhulette\FactoryGenerator\Commands\FactoryGeneratorCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FactoryGeneratorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('factory-generator')
            ->hasConfigFile()
            ->hasCommand(FactoryGeneratorCommand::class);
    }
}
