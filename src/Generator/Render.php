<?php

declare(strict_types=1);

namespace Jwhulette\FactoryGenerator\Generator;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Render
{
    protected string $stub;

    protected string $tableName;

    protected Connection $connection;

    protected Definition $definition;

    protected AbstractSchemaManager $schemaManager;

    public function __construct(Definition $definition)
    {
        $this->definition = $definition;
    }

    public function renderStub(Model $modelInstance, string $modelName, string $namespacedModel): string
    {
        $this->stub = $this->getFactoryStub();

        $this->setSchema($modelInstance);

        $this->replacePlaceholders($modelName, $namespacedModel);

        return $this->definition->create($this->tableName, $this->schemaManager, $this->stub);
    }

    public function setSchema(Model $model): void
    {
        $this->connection = $model->getConnection();

        $this->tableName = $this->connection->getTablePrefix().$model->getTable();

        $this->schemaManager = $this->connection->getDoctrineSchemaManager();

        $platform = $this->connection->getDoctrineConnection()->getDatabasePlatform();

        $this->registerCustomMappings($platform);
    }

    protected function registerCustomMappings(AbstractPlatform $platform): void
    {
        /** @var array $customTypes */
        $customTypes = Config::get("factory-generator.custom_db_types.{$platform->getName()}", []);

        foreach ($customTypes as $typeName => $doctrineTypeName) {
            $platform->registerDoctrineTypeMapping($typeName, $doctrineTypeName);
        }
    }

    public function replaceFactoryNamespace(string $path): void
    {
        $this->stub = Str::replace('{{ factoryNamespace }}', $path, $this->stub);
    }

    public function replaceModelNamespace(string $namespacedModel): void
    {
        $this->stub = Str::replace('{{ namespacedModel }}', $namespacedModel, $this->stub);
    }

    public function replaceModelName(string $model): void
    {
        $this->stub = Str::replace('{{ model }}', $model, $this->stub);
    }

    public function replacePlaceholders(string $model, string $namespacedModel): void
    {
        $this->replaceModelName($model);

        $this->replaceModelNamespace($namespacedModel);

        $this->replaceFactoryNamespace('Database\Factories');
    }

    public function getFactoryStub(): string
    {
        $stub = \base_path('src/stubs/factory.stub');

        return File::get($stub);
    }
}
