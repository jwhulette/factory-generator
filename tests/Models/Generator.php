<?php

namespace Jwhulette\FactoryGenerator\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Database\Factories\GeneratorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Generator extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'generator';

    /**
     * The connection assiciated with the model.
     *
     * @var string
     */
    protected $connection = 'test';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected static function newFactory()
    {
        return GeneratorFactory::new();
    }
}
