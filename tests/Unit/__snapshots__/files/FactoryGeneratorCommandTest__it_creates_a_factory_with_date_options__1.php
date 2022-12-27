<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Jwhulette\FactoryGenerator\Tests\Models\Generator;

class FactoryGeneratorCommandTest__it_creates_a_factory_with_date_options__1 extends Factory
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
