<?php

declare(strict_types=1);

namespace Jwhulette\FactoryGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Jwhulette\FactoryGenerator\Generator\FactoryGenerator;

class FactoryGeneratorCommand extends Command
{
    public $signature = 'factory:generate {model} {--overwrite : Overwrite an existing model}';

    public $description = 'Generate a factory by supplying the path to your model';

    public function handle(): int
    {
        /** @var string $model */
        $model = $this->argument('model');

        if ($this->option('overwrite') === true) {
            Config::set('factory-generator.overwrite', true);
        }

        try {
            $modelName = (new FactoryGenerator())->generateFactory($model);

            $this->info($modelName.'Factory created!');

            return 0;
        } catch (\Throwable $th) {
            $this->alert(' The following error occurred. ');
            $this->warn($th->getMessage());
            $this->newLine();
            $this->info($this->errorHints($th->getMessage()));
            $this->newLine();

            return 1;
        }
    }

    protected function errorHints(string $errorMessage): string
    {
        if (Str::contains($errorMessage, 'Unknown database type')) {
            return 'Set a custom type in the custom_db_types array, in the factory-generator configuration';
        }

        return '';
    }
}
