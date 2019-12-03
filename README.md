# BotMan Mobiverse USSD Driver for Laravel

[![Latest Release on GitHub][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

## About

BotMan driver to connect the Mobiverse USSD API with [BotMan](https://github.com/botman/botman)

## Installation

Install Botman for Laravel before installing this driver. See the [Botman documentation](https://botman.io/2.0/welcome).

Require the `gashey/botman-ussd-driver` package in your `composer.json` and update your dependencies:

```sh
$ composer require gashey/botman-ussd-driver
```

Add the Gashey\BotmanUssdDriver\UssdServiceProvider to your `providers` array:

```php
Gashey\BotmanUssdDriver\UssdServiceProvider::class,
```

Add the following listener to your botman routes file:

```php
$botman->hears(config('ussd.cancel_text', 'CANCEL'), function ($bot) {
    $bot->reply('stopped');
})->stopsConversation();
```

## Usage

You can use the [Mobiverse USSD Simulator](https://apps.mobivs.com/USSDSIM/) to test your application. Supply your application url as: http://your-application.com/botman

## Configuration

The defaults are set in `config/ussd.php`. Publish the config using this command:

```sh
$ php artisan vendor:publish --provider="Gashey\BotmanUssdDriver\UssdServiceProvider"
```

```php
return [

    "cancel_text" => "CANCEL",

    "network_mapping" => array('01' => 'MTN', '02' => 'VODAFONE', '03' => 'AIRTEL-TIGO', '04' => 'AIRTEL-TIGO', '05' => 'GLO'),
];
```

## License

Released under the MIT License, see [LICENSE](LICENSE).

[ico-version]: https://img.shields.io/github/release/gashey/laravel-mobiverse-ussd.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/gashey/laravel-mobiverse-ussd.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/gashey/laravel-mobiverse-ussd.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/gashey/laravel-mobiverse-ussd.svg?style=flat-square
[link-packagist]: https://packagist.org/packages/gashey/laravel-mobiverse-ussd
[link-scrutinizer]: https://scrutinizer-ci.com/g/gashey/laravel-mobiverse-ussd/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/gashey/laravel-mobiverse-ussd
[link-downloads]: https://packagist.org/packages/gashey/laravel-mobiverse-ussd
[link-author]: https://github.com/gashey
[link-contributors]: ../../contributors
