# Generate Laravel 8+ database factories

<!-- [![Latest Version on Packagist](https://img.shields.io/packagist/v/jwhulette/factory-generator.svg?style=flat-square)](https://packagist.org/packages/jwhulette/factory-generator) -->
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/jwhulette/factory-generator/run-tests?label=tests)](https://github.com/jwhulette/factory-generator/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/jwhulette/factory-generator/Check%20&%20fix%20styling?label=code%20style)](https://github.com/jwhulette/factory-generator/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
<!-- [![Total Downloads](https://img.shields.io/packagist/dt/jwhulette/factory-generator.svg?style=flat-square)](https://packagist.org/packages/jwhulette/factory-generator) -->

---

<img src=".github/images/Factory Generator.png" width="80%" style="display: block;margin-left: auto;margin-right: auto;" />

---

## Installation

You can install the package via composer:

```bash
composer require jwhulette/factory-generator
```
## Usage

```php
$factory-generator = new Jwhulette\FactoryGenerator();
echo $factory-generator->echoPhrase('Hello, Spatie!');
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
