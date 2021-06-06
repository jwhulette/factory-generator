<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Jwhulette\FactoryGenerator\Tests\Models\Generator;

class GeneratorFactory extends Factory
{
    protected $model = Generator::class;

    public function definition()
    {
        return [
            'first_name'    => '', 
            'last_name'     => '', 
            'date_of_birth' => \now(), 
            'photo'         => '', 
            'CONFIRMED'     => '', 
            'name'          => '', 
            'amount'        => '', 
            'amountDouble'  => '', 
            'amountFloat'   => '', 
            'sunrise'       => '',
        ];
    }
}
