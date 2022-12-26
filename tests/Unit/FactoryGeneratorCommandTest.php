<?php

declare(strict_types=1);

namespace Jwhulette\FactoryGenerator\Tests\Unit;

use Illuminate\Support\Facades\File;
use Jwhulette\FactoryGenerator\Exceptions\FactoryGeneratorException;
use Jwhulette\FactoryGenerator\Tests\Models\Generator;
use Jwhulette\FactoryGenerator\Tests\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class FactoryGeneratorCommandTest extends TestCase
{
    use MatchesSnapshots;

    public string $model = 'tests/Models/Generator';

    public string $file = '';

    public function setUp(): void
    {
        parent::setUp();

        $this->file = database_path('factories/GeneratorFactory.php');
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->deleteTestFile();
    }

    protected function deleteTestFile(): void
    {
        $file = database_path('factories/GeneratorFactory.php');

        File::delete($file);
    }

    public function testCreateNewFactory(): void
    {
        $this->artisan('factory:generate', ['model' => $this->model])
            ->assertExitCode(0);

        $this->assertMatchesFileSnapshot($this->file);

        $generator = \resolve(Generator::class);

        $factory = $generator::factory()->create();

        $this->assertInstanceOf(Generator::class, $factory);
    }

    public function testErrorWhenFactoryExists(): void
    {
        $this->artisan('factory:generate', ['model' => $this->model]);

        $this->artisan('factory:generate', ['model' => $this->model])
            ->assertExitCode(1);
    }

    public function testOverrideOptionwhenFactoryExists(): void
    {
        $this->artisan('factory:generate', ['model' => $this->model]);

        $this->artisan('factory:generate', ['model' => $this->model, '--overwrite' => true])
            ->assertExitCode(0);

        $generator = \resolve(Generator::class);

        $factory = $generator::factory()->create();

        $this->assertInstanceOf(Generator::class, $factory);
    }

    public function testCreateFactoryOptionLowerCase(): void
    {
        config()->set('factory-generator.lower_case_column', true);

        $this->artisan('factory:generate', ['model' => $this->model])
            ->assertExitCode(0);

        $this->assertMatchesFileSnapshot($this->file);

        $generator = \resolve(Generator::class);

        $factory = $generator::factory()->create();

        $this->assertInstanceOf(Generator::class, $factory);
    }

    public function testCreateFactoryOptionSetNullDefault(): void
    {
        config()->set('factory-generator.definition.set_null_default', true);

        $this->artisan('factory:generate', ['model' => $this->model])
            ->assertExitCode(0);

        $this->assertMatchesFileSnapshot($this->file);

        $generator = \resolve(Generator::class);

        $factory = $generator::factory()->create();

        $this->assertInstanceOf(Generator::class, $factory);
    }

    public function testCreateFactoryOptionSetDate(): void
    {
        config()->set('factory-generator.definition.set_date_now', true);

        $this->artisan('factory:generate', ['model' => $this->model])
            ->assertExitCode(0);

        $this->assertMatchesFileSnapshot($this->file);

        $generator = \resolve(Generator::class);

        $factory = $generator::factory()->create();

        $this->assertInstanceOf(Generator::class, $factory);
    }

    public function testCreateFactoryOptionAddColumnHint(): void
    {
        config()->set('factory-generator.add_column_hint', true);

        $this->artisan('factory:generate', ['model' => $this->model])
            ->assertExitCode(0);

        $this->assertMatchesFileSnapshot($this->file);

        $generator = \resolve(Generator::class);

        $factory = $generator::factory()->create();

        $this->assertInstanceOf(Generator::class, $factory);
    }

    public function testCreateNewFactoryWithWindowsPath(): void
    {
        $model = 'tests\Models\Generator.php';

        $this->artisan('factory:generate', ['model' => $model])
            ->assertExitCode(0);

        $this->assertMatchesFileSnapshot($this->file);

        $generator = \resolve(Generator::class);

        $factory = $generator::factory()->create();

        $this->assertInstanceOf(Generator::class, $factory);
    }
}
