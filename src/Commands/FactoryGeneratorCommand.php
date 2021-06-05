<?php

declare(strict_types=1);

namespace Jwhulette\FactoryGenerator\Commands;

use Illuminate\Console\Command;
use Jwhulette\FactoryGenerator\FactoryGenerator;

class FactoryGeneratorCommand extends Command
{
    public $signature = 'factory:generate {model}';

    public $description = 'Generate a factory by suppling the path to your model';

    public function handle(): int
    {
        $model = $this->argument('model');

        (new FactoryGenerator)->generateFactory($model);

        return 0;
    }
}
