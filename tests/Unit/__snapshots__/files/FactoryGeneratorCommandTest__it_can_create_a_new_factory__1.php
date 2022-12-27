<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Jwhulette\FactoryGenerator\Tests\Models\Generator;

class FactoryGeneratorCommandTest__it_can_create_a_new_factory__1 extends Factory
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
            'name'          => '',
            'amount'        => '',
            'amountDouble'  => '',
            'amountFloat'   => '',
            'sunrise'       => '',
        ];
    }
}
