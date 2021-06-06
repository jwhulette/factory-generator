<?php

declare(strict_types=1);

namespace Jwhulette\FactoryGenerator\Tests\Unit;

use Illuminate\Support\Facades\File;
use Spatie\Snapshots\MatchesSnapshots;
use Jwhulette\FactoryGenerator\Tests\TestCase;
use Jwhulette\FactoryGenerator\Tests\Models\Generator;
use Jwhulette\FactoryGenerator\Exceptions\FactoryGeneratorException;

class FactoryGeneratorCommandTest extends TestCase
{
    use MatchesSnapshots;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateNewFactory()
    {
        $model = 'tests/Models/Generator';

        $file = database_path('factories/GeneratorFactory.php');

        File::delete($file);

        $this->artisan('factory:generate', ['model' => $model])
            ->assertExitCode(0);

        $this->assertMatchesFileSnapshot($file);

        $generator = \resolve(Generator::class);

        $factory = $generator::factory()->create();

        $this->assertInstanceOf(Generator::class, $factory);
    }

    public function testErrorWhenFactoryExists()
    {
        $this->expectException(FactoryGeneratorException::class);

        $model = 'tests/Models/Generator';

        $this->artisan('factory:generate', ['model' => $model]);

        $this->artisan('factory:generate', ['model' => $model])
            ->assertExitCode(1);
    }

    public function testOverrideOptionwhenFactoryExists()
    {
        $model = 'tests/Models/Generator';

        $this->artisan('factory:generate', ['model' => $model]);

        $this->artisan('factory:generate', ['model' => $model, '--overwrite' => true])
            ->assertExitCode(0);

        $generator = \resolve(Generator::class);

        $factory = $generator::factory()->create();

        $this->assertInstanceOf(Generator::class, $factory);
    }

    public function testCreateFactoryOptionLowerCase()
    {
        File::cleanDirectory(database_path('factories'));

        $model = 'tests/Models/Generator';

        $file = database_path('factories/GeneratorFactory.php');

        config()->set('factory-generator.lower_case_column', true);

        $this->artisan('factory:generate', ['model' => $model])
            ->assertExitCode(0);

        $this->assertMatchesFileSnapshot($file);

        $generator = \resolve(Generator::class);

        $factory = $generator::factory()->create();

        $this->assertInstanceOf(Generator::class, $factory);
    }

    public function testCreateFactoryOptionSetNullDefault()
    {
        File::cleanDirectory(database_path('factories'));

        $model = 'tests/Models/Generator';

        $file = database_path('factories/GeneratorFactory.php');

        config()->set('factory-generator.definition.set_null_default', true);

        $this->artisan('factory:generate', ['model' => $model])
            ->assertExitCode(0);

        $this->assertMatchesFileSnapshot($file);

        $generator = \resolve(Generator::class);

        $factory = $generator::factory()->create();

        $this->assertInstanceOf(Generator::class, $factory);
    }

    public function testCreateFactoryOptionSetDate()
    {
        File::cleanDirectory(database_path('factories'));

        $model = 'tests/Models/Generator';

        $file = database_path('factories/GeneratorFactory.php');

        config()->set('factory-generator.definition.set_date_now', true);

        $this->artisan('factory:generate', ['model' => $model])
            ->assertExitCode(0);

        $this->assertMatchesFileSnapshot($file);

        $generator = \resolve(Generator::class);

        $factory = $generator::factory()->create();

        $this->assertInstanceOf(Generator::class, $factory);
    }

    public function testCreateFactoryOptionAddColumnHint()
    {
        File::cleanDirectory(database_path('factories'));

        $model = 'tests/Models/Generator';

        $file = database_path('factories/GeneratorFactory.php');

        config()->set('factory-generator.add_column_hint', true);

        $this->artisan('factory:generate', ['model' => $model])
            ->assertExitCode(0);

        $this->assertMatchesFileSnapshot($file);

        $generator = \resolve(Generator::class);

        $factory = $generator::factory()->create();

        $this->assertInstanceOf(Generator::class, $factory);
    }
}
