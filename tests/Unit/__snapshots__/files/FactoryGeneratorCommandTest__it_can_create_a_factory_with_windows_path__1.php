<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Jwhulette\FactoryGenerator\Tests\Models\Generator;

class FactoryGeneratorCommandTest__it_can_create_a_factory_with_windows_path__1 extends Factory
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
