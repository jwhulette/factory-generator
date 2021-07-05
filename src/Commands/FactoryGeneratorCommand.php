<?php

declare(strict_types=1);

namespace Jwhulette\FactoryGenerator\Commands;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Jwhulette\FactoryGenerator\FactoryGenerator;

class FactoryGeneratorCommand extends FactoryGenerator
{
    public $signature = 'factory:generate {model} {--overwrite : Overwrite an existing model}';

    public $description = 'Generate a factory by suppling the path to your model';

    public function handle(): int
    {
        $model = $this->argument('model');

        $model = $this->cleanInput($model);

        if ($this->option('overwrite') === true) {
            Config::set('factory-generator.overwrite', true);
        }

        try {
            $modelName = $this->generateFactory($model);

            $this->info($modelName . 'Factory created!');

            return 0;
        } catch (\Throwable $th) {
            $this->alert(' The following error occurred. ');
            $this->error($th->getMessage());
            $this->newLine();
            $this->info($this->errorHints($th->getMessage()));
            $this->newLine();

            return 1;
        }
    }

    /**
     * @param string $errorMessage
     *
     * @return string
     */
    protected function errorHints(string $errorMessage): string
    {
        if (Str::contains($errorMessage, 'Unknown database type')) {
            return 'Set a custom type in the custom_db_types array, in the factory-generator configration';
        }

        return '';
    }

    /**
     *
     * @param string $model
     *
     * @return string
     */
    protected function cleanInput(string $model): string
    {
        /* Swap path seperators && Remove extension */
        return Str::of($model)->replace('\\', '/')->replace('.php', '')->__toString();
    }
}
