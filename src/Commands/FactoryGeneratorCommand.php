<?php

declare(strict_types=1);

namespace Jwhulette\FactoryGenerator\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Composer\Autoload\ClassMapGenerator;

class FactoryGeneratorCommand extends Command
{
    public $signature = 'factory-generate {model}';

    public $description = 'Generate a factory by suppling the path to your model';

    public function handle(): int
    {
        $model = $this->argument('model');

        $this->generateFactory($model);

        return 0;
    }

    /**
     * @param string $model
     */
    protected function generateFactory(string $model)
    {
        $classMap = $this->generateClassMap($model);

        $namespacedModel = \key($classMap);

        $modelInstance = $this->makeModel($classMap);

        $model = $this->makeModelName($modelInstance);

        $stub = $this->getFactoryStub();

        $stub = $this->replacePlaceholders($model, $namespacedModel, $stub);

        $stub = $this->createDefinition($modelInstance, $stub);

        $this->writeFactory($stub, $model);
    }

    /**
     * @param string $stub
     * @param string $model
     */
    protected function writeFactory(string $stub, string $model): void
    {
        $path = $this->getFactoryFilePath($model);

        File::put($path, $stub);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $modelInstance
     * @param string $stub
     *
     * @return string
     */
    protected function createDefinition(Model $modelInstance, string $stub): string
    {
        $definition = $this->makeDefinition($modelInstance);

        return $this->replaceDefinition($stub, $definition);
    }

    /**
     * @param string $model
     * @param string $namespacedModel
     * @param string $sub
     *
     * @return string
     */
    protected function replacePlaceholders(string $model, string $namespacedModel, string $sub): string
    {
        $replace = $this->replaceModelName($sub, $model);

        $replace = $this->replaceModelNamespace($namespacedModel, $replace);

        return $this->replaceFactoryNamespace($replace, 'Database\Factories');
    }

    /**
     * @param string $stub
     * @param string $definition
     *
     * @return string
     */
    protected function replaceDefinition(string $stub, string $definition): string
    {
        return Str::replace('{{ definition }}', $definition, $stub);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return string
     */
    protected function makeDefinition(Model $model): string
    {
        $columns = $this->getColumns($model);

        $definition = '';

        /** @var \Doctrine\DBAL\Schema\Column $column */
        foreach ($columns as $column) {
            $name = $column->getName();

            if ($name === 'id') {
                continue;
            }

            $columnName = $this->formatColumnName($name);
            $definition .= "        ";
            $definition .= "    '$columnName' => '',\n";
        }

        return Str::of($definition)->trim()->rtrim(',')->__toString();
    }

    /**
     * @param string $columnName
     *
     * @return string
     */
    protected function formatColumnName(string $columnName): string
    {
        return Str::of($columnName)->lower()->__toString();
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getFactoryFilePath(string $name): string
    {
        $factoryDirectory = database_path('factories');

        File::ensureDirectoryExists($factoryDirectory);

        return $factoryDirectory . '/' . $name . 'Factory.php';
    }

    /**
     * @param string $namespacedModel
     * @param string $stub
     *
     * @return string
     */
    protected function replaceModelNamespace(string $namespacedModel, string $stub): string
    {
        return Str::replace('{{ namespacedModel }}', $namespacedModel, $stub);
    }

    /**
     * @param string $stub
     * @param string $path
     *
     * @return string
     */
    protected function replaceFactoryNamespace(string $stub, string $path): string
    {
        return Str::replace('{{ factoryNamespace }}', $path, $stub);
    }

    /**
     * @param string $stub
     * @param string $model
     *
     * @return string
     */
    protected function replaceModelName(string $stub, string $model): string
    {
        return Str::replace('{{ model }}', $model, $stub);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return string
     */
    protected function makeModelName(Model $model): string
    {
        $table = $model->getTable();

        return Str::of($table)->afterLast('.')->camel()->ucfirst()->singular()->__toString();
    }

    /**
     * @param array $classMap
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function makeModel(array $classMap): Model
    {
        return  $this->laravel->make(\key($classMap));
    }

    /**
     * @return string
     */
    protected function getFactoryStub(): string
    {
        $stub = __DIR__ . '/../stubs/factory.stub';

        return File::get($stub);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return array<\Doctrine\DBAL\Schema\Column>
     */
    protected function getColumns(Model $model): array
    {
        $table = $model->getConnection()->getTablePrefix() . $model->getTable();

        $schema = $model->getConnection()->getDoctrineSchemaManager();

        $database = null;

        if (strpos($table, '.')) {
            [$database, $table] = explode('.', $table);
        }

        return $schema->listTableColumns($table, $database);
    }

    /**
     * @param string $model
     *
     * @return array
     */
    protected function generateClassMap(string $model): array
    {
        $path = dirname(base_path($model));

        return ClassMapGenerator::createMap($path);
    }
}
