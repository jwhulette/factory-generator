<?php

declare(strict_types=1);

namespace Jwhulette\FactoryGenerator\Tests;

use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Jwhulette\FactoryGenerator\FactoryGeneratorServiceProvider;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        app()
            ->setBasePath(realpath(__DIR__ . '/..'))
            ->useDatabasePath(__DIR__);

        $this->loadMigrationsFrom(database_path('migrations'));

        $this->artisan('migrate');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $file = database_path('factories/GeneratorFactory.php');

        File::delete($file);
    }

    protected function getPackageProviders($app)
    {
        return [
            FactoryGeneratorServiceProvider::class
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
