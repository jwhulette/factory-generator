<?php

namespace Jwhulette\FactoryGenerator;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Jwhulette\FactoryGenerator\Commands\FactoryGeneratorCommand;

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
            ->hasCommand(FactoryGeneratorCommand::class);
    }
}
