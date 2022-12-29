<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Jwhulette\FactoryGenerator\Tests\Models\Generator;

class FactoryGeneratorCommandTest__it_create_a_factory_with_lower_case_option__1 extends Factory
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
