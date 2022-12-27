<?php

declare(strict_types=1);

namespace Jwhulette\FactoryGenerator\Tests\Unit;

use Illuminate\Support\Facades\File;
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

    /** @test */
    public function it_can_create_a_new_factory(): void
    {
        $this->artisan('factory:generate', ['model' => $this->model])
            ->assertExitCode(0);

        $this->assertMatchesFileSnapshot($this->file);

        $generator = \resolve(Generator::class);

        $factory = $generator::factory()->create();

        $this->assertInstanceOf(Generator::class, $factory);
    }

    /** @test */
    public function it_throw_an_error_when_a_factory_exits(): void
    {
        $this->artisan('factory:generate', ['model' => $this->model]);

        $this->artisan('factory:generate', ['model' => $this->model])
            ->assertExitCode(1);
    }

    /** @test */
    public function it_overrides_options_when_factory_exits(): void
    {
        $this->artisan('factory:generate', ['model' => $this->model]);

        $this->artisan('factory:generate', ['model' => $this->model, '--overwrite' => true])
            ->assertExitCode(0);

        $generator = \resolve(Generator::class);

        $factory = $generator::factory()->create();

        $this->assertInstanceOf(Generator::class, $factory);
    }

    /** @test */
    public function it_create_a_factory_with_lower_case_option(): void
    {
        config()->set('factory-generator.lower_case_column', true);

        $this->artisan('factory:generate', ['model' => $this->model])
            ->assertExitCode(0);

        $this->assertMatchesFileSnapshot($this->file);

        $generator = \resolve(Generator::class);

        $factory = $generator::factory()->create();

        $this->assertInstanceOf(Generator::class, $factory);
    }

    /** @test */
    public function it_creates_factory_with_null_default_option(): void
    {
        config()->set('factory-generator.definition.set_null_default', true);

        $this->artisan('factory:generate', ['model' => $this->model])
            ->assertExitCode(0);

        $this->assertMatchesFileSnapshot($this->file);

        $generator = \resolve(Generator::class);

        $factory = $generator::factory()->create();

        $this->assertInstanceOf(Generator::class, $factory);
    }

    /** @test */
    public function it_creates_a_factory_with_date_options(): void
    {
        config()->set('factory-generator.definition.set_date_now', true);

        $this->artisan('factory:generate', ['model' => $this->model])
            ->assertExitCode(0);

        $this->assertMatchesFileSnapshot($this->file);

        $generator = \resolve(Generator::class);

        $factory = $generator::factory()->create();

        $this->assertInstanceOf(Generator::class, $factory);
    }

    /** @test */
    public function it_can_create_factory_with_column_hint_option(): void
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
