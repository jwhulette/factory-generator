<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Jwhulette\FactoryGenerator\Tests\Models\Generator;

class GeneratorFactory extends Factory
{
    protected $model = Generator::class;

    public function definition()
    {
        return [
            'first_name'    => '',
            'last_name'     => '',
            'date_of_birth' => '',
            'photo'         => '',
            'confirmed'     => '',
            'name'          => '',
            'amount'        => '',
            'amountdouble'  => '',
            'amountfloat'   => '',
            'sunrise'       => '',
        ];
    }
}
