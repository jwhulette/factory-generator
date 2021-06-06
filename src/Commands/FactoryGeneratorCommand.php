<?php

declare(strict_types=1);

namespace Jwhulette\FactoryGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Jwhulette\FactoryGenerator\FactoryGenerator;

class FactoryGeneratorCommand extends Command
{
    public $signature = 'factory:generate {model} {--overwrite : Overwrite an existing model}';

    public $description = 'Generate a factory by suppling the path to your model';

    public function handle(): int
    {
        $model = $this->argument('model');

        if ($this->option('overwrite') === true) {
            Config::set('factory-generator.overwrite', true);
        }

        (new FactoryGenerator())->generateFactory($model);

        return 0;
    }
}
