<?php

declare(strict_types=1);

namespace Jwhulette\FactoryGenerator\Tests\Unit;

use Illuminate\Support\Facades\File;
use Spatie\Snapshots\MatchesSnapshots;
use Jwhulette\FactoryGenerator\Tests\TestCase;
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

        $this->artisan('factory-generate', ['model' => $model])
            ->assertExitCode(0);

        $this->assertMatchesFileSnapshot($file);
    }

    public function testErrorWhenFactoryExists()
    {
        $this->expectException(FactoryGeneratorException::class);

        $model = 'tests/Models/Generator';

        $this->artisan('factory-generate', ['model' => $model])
            ->assertExitCode(1);
    }
}
