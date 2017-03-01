# Scanpay PHP Client

You can sign up for a Scanpay account at [scanpay.dk/opret](https://scanpay.dk/opret).

## Requirements

PHP version >= 5.3.3 with php-curl enabled.

## Composer

This package is published at [Packagist](https://packagist.org/packages/scanpay/scanpay). You can install the client via [Composer](http://getcomposer.org/):

```bash
composer require scanpay/scanpay
```

## Manual install

Download the [latest release](https://github.com/scanpaydk/php-scanpay/releases) and include in into your project:

```php
require('lib/Scanpay.php');
```

## Getting Started

To create a [payment link](https://docs.scanpay.dk/payment-link) all you need to do is:

```php
$scanpay = new Scanpay\Scanpay(' API-key ');

$order = [
    'items' => [
        [
            'name'     => 'Pink Floyd: The Dark Side Of The Moon',
            'quantity' => 2,
            'price'    => '99.99 DKK',
        ]
    ],
];

print_r ($newURL = $scanpay->newURL($order));
```