<?php

namespace Database\Factories;

use Illuminate\Support\Str;
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
            'CONFIRMED'     => '',
            'name'          => null,
            'amount'        => '',
            'amountDouble'  => '',
            'amountFloat'   => '',
            'sunrise'       => '',
        ];
    }
}
