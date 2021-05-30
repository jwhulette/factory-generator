<?php

declare(strict_types=1);

namespace Jwhulette\FactoryGenerator\Tests\Unit;

use Spatie\Snapshots\MatchesSnapshots;
use Jwhulette\FactoryGenerator\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FactoryGeneratorCommandTest extends TestCase
{
    use RefreshDatabase;
    use MatchesSnapshots;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testPassModel()
    {
        $model = 'tests/Models/Generator';

        $this->artisan('factory-generate', ['model' => $model])
            ->assertExitCode(0);

        $file = database_path('factories/GeneratorFactory.php');

        $this->assertMatchesFileSnapshot($file);
    }
}
