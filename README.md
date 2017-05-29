#  Trilateration

[![Latest Version](https://img.shields.io/packagist/v/tuupola/trilateration.svg?style=flat-square)](https://packagist.org/packages/tuupola/trilateration)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/tuupola/trilateration/master.svg?style=flat-square)](https://travis-ci.org/tuupola/trilateration)
[![HHVM Status](https://img.shields.io/hhvm/tuupola/trilateration.svg?style=flat-square)](http://hhvm.h4cc.de/package/tuupola/trilateration)
[![Coverage](http://img.shields.io/codecov/c/github/tuupola/trilateration.svg?style=flat-square)](https://codecov.io/github/tuupola/trilateration)

PHP implementation of [Trilateration](https://en.wikipedia.org/wiki/Trilateration) algorithm. See [Wifi Trilateration With Three or More Points](https://appelsiini.net/2017/trilateration-with-n-points/) for walkthrough.

## Install

Install the library using [Composer](https://getcomposer.org/).

``` bash
$ composer require tuupola/trilateration
```
## Usage

Here be dragons.

## Testing

You can run tests either manually or automatically on every code change. Automatic tests require [entr](http://entrproject.org/) to work.

``` bash
$ composer test
```
``` bash
$ brew install entr
$ composer watch
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email tuupola@appelsiini.net instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
