<?php

declare(strict_types=1);

namespace Jwhulette\FactoryGenerator;

use Illuminate\Support\Str;
use Doctrine\DBAL\Schema\Column;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use Composer\Autoload\ClassMapGenerator;
use Jwhulette\FactoryGenerator\Exceptions\FactoryGeneratorException;

class FactoryGenerator
{
    protected const DATETIME_FIELDS = ['date', 'datetimetz', 'datetime'];
    protected const NUMERIC_FIELDS = ['integer', 'bigint', 'smallint'];

    /**
     * @param string $model
     *
     * @return void
     */
    public function generateFactory(string $model): void
    {
        $classMap = $this->generateClassMap($model);

        $namespacedModel = \key($classMap);

        $modelInstance = $this->makeModel($classMap);

        $modelName = $this->makeModelName($modelInstance);

        $factory = $this->getFactoryFilePath($modelName);

        if (Config::get('factory-generator.overwrite') === false && File::exists($factory)) {
            throw new FactoryGeneratorException($modelName . 'Factory already exists');
        }

        $stub = $this->renderStub($modelInstance, $modelName, $namespacedModel);

        $this->writeFactory($modelName, $stub);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $modelInstance
     * @param string $modelName
     * @param string $namespacedModel
     *
     * @return string
     */
    public function renderStub(Model $modelInstance, string $modelName, string $namespacedModel): string
    {
        $stub = $this->getFactoryStub();

        $stub = $this->replacePlaceholders($modelName, $namespacedModel, $stub);

        return $this->createDefinition($modelInstance, $stub);
    }

    /**
     * @param string $model
     * @param string $stub
     */
    public function writeFactory(string $model, string $stub): void
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
    public function createDefinition(Model $modelInstance, string $stub): string
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
    public function replacePlaceholders(string $model, string $namespacedModel, string $sub): string
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
    public function replaceDefinition(string $stub, string $definition): string
    {
        return Str::replace('{{ definition }}', $definition, $stub);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return string
     */
    public function makeDefinition(Model $model): string
    {
        $skipColums = Config::get('factory-generator.skip_columns', \false);

        $definitionConfigs = Config::get('factory-generator.definition');

        $columns = $this->getColumns($model);

        $formatPadding = $this->getFormatPadding($columns);

        $definition = '';

        /** @var \Doctrine\DBAL\Schema\Column $column */
        foreach ($columns as $column) {
            $name = $column->getName();

            /* Skip any columns listed in the the skip columns configuration array */
            if (\in_array($name, $skipColums)) {
                continue;
            }

            $columnName = $this->formatColumnName($name);
            $columnDefinition = $this->getDefinition($column, $definitionConfigs);
            $columnHint = $this->addDefinitionHint($column, $definitionConfigs);

            $definition .= '        ';
            $definition .= \sprintf(
                "    %s => %s,%s\n",
                str_pad("'$columnName'", $formatPadding + 2, ' '),
                $columnDefinition,
                $columnHint
            );
        }

        return Str::of($definition)->trim()->__toString();
    }

    /**
     * @param array $columns
     *
     * @return int
     */
    protected function getFormatPadding(array $columns): int
    {
        uksort($columns, function ($a, $b) {
            return strlen($a) - strlen($b);
        });

        $items = collect($columns);

        $last = $items->last();

        return \strlen($last->getName());
    }

    /**
     * @param \Doctrine\DBAL\Schema\Column $column
     * @param array $definitionConfigs
     *
     * @return string
     */
    protected function addDefinitionHint(Column $column, array $definitionConfigs): string
    {
        if (Config::get('factory-generator.add_column_hint', \false) === true) {
            $columnType = $column->getType()->getName();
            $columnHint = '// Type: ' . Str::title($columnType);

            $columnNullable = Str::title($column->getNotNull() ? 'true' : 'false');
            $columnHint .= ' | Nullable: ' . $columnNullable;

            $columnHint .= $this->getColumnLength($column, $columnType);
            $columnHint .= $this->getColumnDefault($column);
            $columnHint .= $this->getNumericHint($column, $columnType);

            return $columnHint;
        }

        return '';
    }

    /**
     * @param \Doctrine\DBAL\Schema\Column $column
     *
     * @return string
     */
    protected function getColumnDefault(Column $column): string
    {
        $columnDefault = $column->getDefault();
        if (\is_null($columnDefault) === \false) {
            return ' | Default: ' . $columnDefault;
        }

        return '';
    }

    /**
     * @param \Doctrine\DBAL\Schema\Column $column
     * @param string $columnType
     *
     * @return string
     */
    protected function getColumnLength(Column $column, string $columnType): string
    {
        if (Str::contains($columnType, ['string', 'binary']) === \false) {
            return '';
        }

        $length = $column->getLength() ?? 'NA';

        return ' | Length: ' . $length;
    }

    /**
     * @param \Doctrine\DBAL\Schema\Column $column
     * @param string $columnType
     *
     * @return string
     */
    protected function getNumericHint(Column $column, string $columnType): string
    {
        if (Str::contains($columnType, ['decimal', 'float']) === \false) {
            return '';
        }

        $columnPrecision = $column->getPrecision();
        $columnScale = $column->getScale();

        return '|Precision: ' . $columnPrecision . '|Scale: ' . $columnScale;
    }

    /**
     * @param \Doctrine\DBAL\Schema\Column  $column
     *
     * @return string|int
     */
    public function getDefinition(Column $column, array $definitionConfigs): string | int
    {
        if ($definitionConfigs['set_null_default'] === true && $column->getNotNull() === false) {
            return 'null';
        }

        $columnType = $column->getType()->getName();

        if ($definitionConfigs['set_date_now'] === true && in_array($columnType, self::DATETIME_FIELDS)) {
            return '\now()';
        }

        if ($definitionConfigs['set_numeric_zero'] === true && in_array($columnType, self::NUMERIC_FIELDS)) {
            return 0;
        }

        return "''";
    }

    /**
     * @param string $columnName
     *
     * @return string
     */
    public function formatColumnName(string $columnName): string
    {
        if (Config::get('factory-generator.lower_case_column', \false)) {
            return Str::of($columnName)->lower()->__toString();
        }

        return $columnName;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getFactoryFilePath(string $name): string
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
    public function replaceModelNamespace(string $namespacedModel, string $stub): string
    {
        return Str::replace('{{ namespacedModel }}', $namespacedModel, $stub);
    }

    /**
     * @param string $stub
     * @param string $path
     *
     * @return string
     */
    public function replaceFactoryNamespace(string $stub, string $path): string
    {
        return Str::replace('{{ factoryNamespace }}', $path, $stub);
    }

    /**
     * @param string $stub
     * @param string $model
     *
     * @return string
     */
    public function replaceModelName(string $stub, string $model): string
    {
        return Str::replace('{{ model }}', $model, $stub);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return string
     */
    public function makeModelName(Model $model): string
    {
        $table = $model->getTable();

        return Str::of($table)->afterLast('.')->camel()->ucfirst()->singular()->__toString();
    }

    /**
     * @param array $classMap
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function makeModel(array $classMap): Model
    {
        return  \resolve(\key($classMap));
    }

    /**
     * @return string
     */
    public function getFactoryStub(): string
    {
        $stub = __DIR__ . '/stubs/factory.stub';

        return File::get($stub);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return array<\Doctrine\DBAL\Schema\Column>
     */
    public function getColumns(Model $model): array
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
    public function generateClassMap(string $model): array
    {
        $path = dirname(base_path($model));

        return ClassMapGenerator::createMap($path);
    }
}
