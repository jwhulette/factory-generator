# Generate Laravel 8+ database factories

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.0-8892BF.svg?style=flat-square)](https://php.net/)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/jwhulette/factory-generator.svg?style=flat-square)](https://packagist.org/packages/jwhulette/factory-generator)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/jwhulette/factory-generator/run-tests?label=tests)](https://github.com/jwhulette/factory-generator/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/jwhulette/factory-generator/Check%20&%20fix%20styling?label=code%20style)](https://github.com/jwhulette/factory-generator/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/jwhulette/factory-generator.svg?style=flat-square)](https://packagist.org/packages/jwhulette/factory-generator)

---

<img src=".github/images/Factory Generator.png" width="100%" style="display: block;margin-left: auto;margin-right: auto;" />

---

The package allows you to generate Laravel model factories automatically from your models. 

It uses the model's database connection to retrieve the column information so the package can be used with mulitple databases.

---

## Installation

You can install the package via composer:

```bash
composer require jwhulette/factory-generator
```
You can publish the config file with:
```bash
php artisan vendor:publish --provider="Jwhulette\FactoryGenerator\FactoryGeneratorServiceProvider.php" --tag="factory-generator-config"
```

This is the contents of the published config file:

```php
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
    |--------------------------------------------------------------------------
    */
    'skip_columns' => ['id'],

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
];
```
## Usage

```php
php artisan factory:generate app/Models/User
```

To overwrite a single factory
```php
php artisan factory:generate app/Models/User --overwrite
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Wes Hulette](https://github.com/jwhulette)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
