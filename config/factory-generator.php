<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Overwrite an existing factory
    |--------------------------------------------------------------------------
    */
    'overwrite' => false,

    /*
    |--------------------------------------------------------------------------
    | Set the factory column name to lower case
    |--------------------------------------------------------------------------
    */
    'lower_case_column' => false,

    /*
    |--------------------------------------------------------------------------
    | An array of columns to skip on factory creation
    | Note: Column names are case sensitive
    |--------------------------------------------------------------------------
    */
    'skip_columns' => ['id', 'ID'],

    /*
    |--------------------------------------------------------------------------
    | Add a column hint to the defintion
    | @example 'payment' => '', // Type: Float | Nullable: True | Precision: 8 | Scale: 2
    | @example 'first_name' => '', // Type: String | Nullable: True | Length: 255
    |--------------------------------------------------------------------------
    */
    'add_column_hint' => false,

    /*
    |--------------------------------------------------------------------------
    | Set the defintion based on the column properties
    |--------------------------------------------------------------------------
    */
    'definition' => [
        /*
        |--------------------------------------------------------------------------
        | If the column allows nulls, set the factory column value to null
        | IMPORTANT: This setting will overide all others
        |--------------------------------------------------------------------------
        */
        'set_null_default' => false,

        /*
        |--------------------------------------------------------------------------
        | If the column is a date column, set it to now()
        |--------------------------------------------------------------------------
        */
        'set_date_now' => false,

        /*
        |--------------------------------------------------------------------------
        | If the column is a numeric column, set it to 0
        |--------------------------------------------------------------------------
        */
        'set_numeric_zero' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Support for custom DB types
    |--------------------------------------------------------------------------
    |
    | This setting allow you to map any custom database type (that you may have
    | created using CREATE TYPE statement or imported using database plugin
    | / extension to a Doctrine type.
    |
    | Each key in this array is a name of the Doctrine DBAL Platform. Currently valid names are:
    | 'postgresql', 'db2', 'mysql', 'oracle', 'sqlite', 'mssql'
    |
    | This name is returned by getName() method of the specific Doctrine/DBAL/Platforms/AbstractPlatform descendant
    |
    | The value of the array is an array of type mappings. Key is the name of the custom type,
    | (for example, "jsonb" from Postgres 9.4) and the value is the name of the corresponding Doctrine type (in
    | our case it is 'json_array'. Doctrine types are listed here:
    | https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/types.html#types
    |
    | So to support jsonb in your models when working with Postgres, just add the following entry to the array below:
    |
    | "postgresql" => array(
    |       "jsonb" => "json_array",
    |  ),
    |
    */
    'custom_db_types' => [],
];
