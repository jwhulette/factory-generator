<?php

declare(strict_types=1);

namespace Jwhulette\FactoryGenerator\Generator;

use Composer\ClassMapGenerator\ClassMapGenerator;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Jwhulette\FactoryGenerator\Exceptions\FactoryGeneratorException;
use Jwhulette\FactoryGenerator\Generator\Render;

class FactoryGenerator extends Command
{
    public function generateFactory(string $model): string
    {
        $classMap = $this->generateClassMap($model);

        /** @var string $namespacedModel */
        $namespacedModel = \key($classMap);

        $modelInstance = $this->makeModel($classMap);

        $modelName = $this->makeModelName($modelInstance);

        $factory = $this->getFactoryFilePath($modelName);

        if (Config::get('factory-generator.overwrite') === false && File::exists($factory)) {
            throw new FactoryGeneratorException($modelName.'Factory already exists');
        }

        $stub = \resolve(Render::class)->renderStub($modelInstance, $modelName, $namespacedModel);

        $this->writeFactory($modelName, $stub);

        return $modelName;
    }

    public function writeFactory(string $model, string $stub): void
    {
        $path = $this->getFactoryFilePath($model);

        File::put($path, $stub);
    }

    public function getFactoryFilePath(string $name): string
    {
        $factoryDirectory = database_path('factories');

        File::ensureDirectoryExists($factoryDirectory);

        return $factoryDirectory.'/'.$name.'Factory.php';
    }

    public function makeModelName(Model $model): string
    {
        $table = $model->getTable();

        return Str::of($table)->afterLast('.')->camel()->ucfirst()->singular()->toString();
    }

    public function makeModel(array $classMap): Model
    {
        return  \resolve((string) \key($classMap));
    }

    public function generateClassMap(string $model): array
    {
        $model = Str::of($model)->replace('\\', '/')
            ->remove('.php')
            ->toString();

        $path = dirname(\base_path($model));

        $classMap = collect(ClassMapGenerator::createMap($path));

        return $classMap->filter(function ($item, $key) use ($model) {
            if (Str::contains($item, $model)) {
                return $item;
            }
        })->toArray();
    }
}
