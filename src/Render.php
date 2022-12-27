<?php

declare(strict_types=1);

namespace Jwhulette\FactoryGenerator;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Render
{
    protected const DATETIME_FIELDS = ['date', 'datetimetz', 'datetime'];

    protected const NUMERIC_FIELDS = ['integer', 'bigint', 'smallint'];

    public function renderStub(Model $modelInstance, string $modelName, string $namespacedModel): string
    {
        $stub = $this->getFactoryStub();

        $stub = $this->replacePlaceholders($modelName, $namespacedModel, $stub);

        return $this->createDefinition($modelInstance, $stub);
    }

    protected function registerCustomMappings(AbstractSchemaManager $schema): void
    {
        $databasePlatform = $schema->getDatabasePlatform();

        $platformName = $databasePlatform->getName();

        /** @var array $customTypes */
        $customTypes = Config::get("factory-generator.custom_db_types.{$platformName}", []);

        foreach ($customTypes as $typeName => $doctrineTypeName) {
            $databasePlatform->registerDoctrineTypeMapping($typeName, $doctrineTypeName);
        }
    }

     /**
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return array<\Doctrine\DBAL\Schema\Column>
     */
    public function getColumns(Model $model): array
    {
        $table = $model->getConnection()->getTablePrefix().$model->getTable();

        $schema = $model->getConnection()->getDoctrineSchemaManager();

        $this->registerCustomMappings($schema);

        $database = null;

        if (strpos($table, '.')) {
            [$database, $table] = explode('.', $table);
        }

        try {
            return $schema->listTableColumns($table, $database);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function formatColumnName(string $columnName): string
    {
        if (Config::get('factory-generator.lower_case_column', \false)) {
            return Str::of($columnName)->lower()->__toString();
        }

        return $columnName;
    }

    public function getDefinition(Column $column, array $definitionConfigs): string|int
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

    protected function getNumericHint(Column $column, string $columnType): string
    {
        if (Str::contains($columnType, ['decimal', 'float']) === \false) {
            return '';
        }

        $columnPrecision = $column->getPrecision();
        $columnScale = $column->getScale();

        return '|Precision: '.$columnPrecision.' | Scale: '.$columnScale;
    }

    protected function getColumnLength(Column $column, string $columnType): string
    {
        if (Str::contains($columnType, ['string', 'binary']) === \false) {
            return '';
        }

        $length = $column->getLength() ?? 'NA';

        return ' | Length: '.$length;
    }

    protected function getColumnDefault(Column $column): string
    {
        $columnDefault = $column->getDefault();
        if (\is_null($columnDefault) === \false) {
            return ' | Default: '.$columnDefault;
        }

        return '';
    }

    protected function addDefinitionHint(Column $column): string
    {
        if (Config::get('factory-generator.add_column_hint', \false) === true) {
            $columnType = $column->getType()->getName();
            $columnHint = ' // Type: '.Str::title($columnType);

            $columnNullable = Str::title($column->getNotNull() ? 'true' : 'false');
            $columnHint .= ' | Nullable: '.$columnNullable;

            $columnHint .= $this->getColumnLength($column, $columnType);
            $columnHint .= $this->getColumnDefault($column);
            $columnHint .= $this->getNumericHint($column, $columnType);

            return $columnHint;
        }

        return '';
    }

    public function makeDefinition(Model $model): string
    {
        /** @var array $skipColumns */
        $skipColumns = Config::get('factory-generator.skip_columns', \false);

        /** @var array $definitionConfigs */
        $definitionConfigs = Config::get('factory-generator.definition');

        $columns = $this->getColumns($model);

        $formatPadding = $this->getFormatPadding($columns);

        $definition = '';

        /** @var \Doctrine\DBAL\Schema\Column $column */
        foreach ($columns as $column) {
            $name = $column->getName();

            /* Skip any columns listed in the the skip columns configuration array */
            if (\in_array($name, $skipColumns)) {
                continue;
            }

            $columnName = $this->formatColumnName($name);
            $columnDefinition = $this->getDefinition($column, $definitionConfigs);
            $columnHint = $this->addDefinitionHint($column);

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

    public function replaceDefinition(string $stub, string $definition): string
    {
        return Str::replace('{{ definition }}', $definition, $stub);
    }

        /**
     * @param array<\Doctrine\DBAL\Schema\Column> $columns
     *
     * @return int
     */
    protected function getFormatPadding(array $columns): int
    {
        uksort($columns, fn ($a, $b) => strlen($a) - strlen($b));

        $items = collect($columns);

        /** @var \Doctrine\DBAL\Schema\Column $last */
        $last = $items->last();

        return \strlen($last->getName());
    }

    public function createDefinition(Model $modelInstance, string $stub): string
    {
        $definition = $this->makeDefinition($modelInstance);

        return $this->replaceDefinition($stub, $definition);
    }

    public function replaceFactoryNamespace(string $stub, string $path): string
    {
        return Str::replace('{{ factoryNamespace }}', $path, $stub);
    }

    public function replaceModelNamespace(string $namespacedModel, string $stub): string
    {
        return Str::replace('{{ namespacedModel }}', $namespacedModel, $stub);
    }

    public function replaceModelName(string $stub, string $model): string
    {
        return Str::replace('{{ model }}', $model, $stub);
    }

    public function replacePlaceholders(string $model, string $namespacedModel, string $sub): string
    {
        $replace = $this->replaceModelName($sub, $model);

        $replace = $this->replaceModelNamespace($namespacedModel, $replace);

        return $this->replaceFactoryNamespace($replace, 'Database\Factories');
    }

    public function getFactoryStub(): string
    {
        $stub = __DIR__.'/stubs/factory.stub';

        return File::get($stub);
    }
}
