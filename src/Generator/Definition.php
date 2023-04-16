<?php

declare(strict_types=1);

namespace Jwhulette\FactoryGenerator\Generator;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class Definition
{
    protected string $tableName;

    protected AbstractSchemaManager $schemaManager;

    protected const DATETIME_FIELDS = ['date', 'datetimetz', 'datetime'];

    protected const NUMERIC_FIELDS = ['integer', 'bigint', 'smallint'];

    public function create(string $tableName, AbstractSchemaManager $schemaManager, string $stub): string
    {
        $this->tableName = $tableName;

        $this->schemaManager = $schemaManager;

        $definition = $this->makeDefinition();

        return $this->replaceDefinition($stub, $definition);
    }

    public function replaceDefinition(string $stub, string $definition): string
    {
        return Str::replace('{{ definition }}', $definition, $stub);
    }

    public function makeDefinition(): string
    {
        /** @var array $skipColumns */
        $skipColumns = Config::get('factory-generator.skip_columns', \false);

        /** @var array $definitionConfigs */
        $definitionConfigs = Config::get('factory-generator.definition');

        $columns = $this->getColumns();

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

        return Str::of($definition)->trim()->toString();
    }

    /**
     * @return array<\Doctrine\DBAL\Schema\Column>
     */
    public function getColumns(): array
    {
        $database = null;

        if (strpos($this->tableName, '.')) {
            [$database, $this->tableName] = explode('.', $this->tableName);
        }

        try {
            return $this->schemaManager->listTableColumns($this->tableName, $database);
        } catch (\Throwable $th) {
            throw $th;
        }
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

    public function formatColumnName(string $columnName): string
    {
        if (Config::get('factory-generator.lower_case_column', \false)) {
            return Str::lower($columnName);
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
}
