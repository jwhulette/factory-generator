<?php

namespace Jwhulette\FactoryGenerator\Commands;

use Illuminate\Console\Command;

class FactoryGeneratorCommand extends Command
{
    public $signature = 'factory-generator';

    public $description = 'Generate a factory by suppling the path to your model';

    public function handle()
    {
        $this->comment('All done');
    }
}
