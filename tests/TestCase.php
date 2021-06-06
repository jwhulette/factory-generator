<?php

declare(strict_types=1);

namespace Jwhulette\FactoryGenerator\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Jwhulette\FactoryGenerator\FactoryGeneratorServiceProvider;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        app()
            ->setBasePath(realpath(__DIR__ . '/..'))
            ->useDatabasePath(__DIR__ . '/database');

        $this->loadMigrationsFrom(database_path('migrations'));

        $this->artisan('migrate');

        // Set the factory directory for testing
        $loader = new \Composer\Autoload\ClassLoader();
        $loader->addPsr4('Database\\Factories\\', database_path('factories'));
        $loader->register();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    protected function getPackageProviders($app)
    {
        return [
            FactoryGeneratorServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $config = $app['config'];

        // Setup default database to use sqlite :memory:
        $config->set('database.default', 'test');
        $config->set('database.connections.test', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
}
