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
            'first_name'    => '',// Type: String | Nullable: True | Length: NA
            'last_name'     => '',// Type: String | Nullable: True | Length: NA
            'date_of_birth' => '',// Type: Date | Nullable: True
            'photo'         => '',// Type: Blob | Nullable: True
            'CONFIRMED'     => '',// Type: Boolean | Nullable: True
            'name'          => '',// Type: String | Nullable: False | Length: NA
            'amount'        => '',// Type: Decimal | Nullable: True|Precision: 10 | Scale: 0
            'amountDouble'  => '',// Type: Float | Nullable: True|Precision: 10 | Scale: 0
            'amountFloat'   => '',// Type: Float | Nullable: True|Precision: 10 | Scale: 0
            'sunrise'       => '',// Type: Time | Nullable: True
        ];
    }
}
