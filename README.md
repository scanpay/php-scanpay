# Scanpay PHP client

[![Latest Stable Version](https://img.shields.io/github/v/release/scanpay/php-scanpay?cacheSeconds=600)](https://packagist.org/packages/scanpay/scanpay)
[![License](https://img.shields.io/github/license/scanpay/php-scanpay?cacheSeconds=6000)](https://github.com/scanpay/php-scanpay/blob/master/LICENSE)
[![CodeFactor](https://www.codefactor.io/repository/github/scanpay/php-scanpay/badge)](https://www.codefactor.io/repository/github/scanpay/php-scanpay)

The Scanpay PHP client library provides convenient and simplified access to the Scanpay API from programs written in PHP. The library is developed and maintained by Scanpay in Denmark.

If you have any questions, concerns or ideas, please do not hesitate to e-mail us at [support@scanpay.dk](mailto:support@scanpay.dk). Feel free to join our IRC server `irc.scanpay.dev:6697 #support` or chat with us at [chat.scanpay.dev](https://chat.scanpay.dev).

## Requirements

PHP version >= 7.4 with php-curl (libcurl >= 7.60.0). See [compatibility table](#compatibility-table).

## Installation

The package is published at [Packagist](https://packagist.org/packages/scanpay/scanpay). You can install the library via [Composer](http://getcomposer.org/):

```bash
composer require scanpay/scanpay
```
You can then include it in your project with:

```php
require 'vendor/autoload.php'; // composer autoload
$scanpay = new Scanpay\Scanpay('API key');
```

### Manual installation

If you do not wish to use Composer, you can download the [latest release](https://github.com/scanpay/php-scanpay/releases) and include it in your project:

```php
require 'lib/Scanpay.php';
$scanpay = new Scanpay\Scanpay('API key');
```

## Usage

The API documentation is available [here](https://docs.scanpay.dev/). Most methods accept an optional per-request array with [options](#options), referred to as `$options`.

#### newURL(Array, $options): String

Create a link to our hosted payment window ([docs](https://docs.scanpay.dev/payment-link) \| [example](tests/newURL.php)).

```php
$order = [
    'orderid'    => 'order_184',
    'items' => [
        [
            'name'     => 'Pink Floyd: The Dark Side Of The Moon',
            'total'    => '199.99 DKK'
        ]
    ]
];
$paymentLink = $scanpay->newURL($order, $options);
```

#### seq(Integer, $options): Array

Fetch changes after a specified sequence number ([docs](https://docs.scanpay.dev/synchronization#sequence-request) \| [example](tests/seq.php)).

```php
$localSeq = (int) $yourDB['seq']; // Locally stored sequence number
$arr = $scanpay->seq($localSeq, $options);
foreach ($arr['changes'] as $change) {
    print_r($change); // Apply change in your DB
}
$localSeq = (int) $arr.seq;
```

#### parsePing(String, String): Array

Validate and parse scanpay pings ([docs](https://docs.scanpay.dev/synchronization#ping-service) \| [example](tests/parsePing.php)).

```php
$ping = $scanpay->parsePing(
    file_get_contents('php://input', false, null, 0, 512),
    $_SERVER['HTTP_X_SIGNATURE'] // X-Signature HTTP header
);
```

#### capture(Integer, Array, $options): Array

Capture an authorized amount from a transaction ([docs](https://docs.scanpay.dev/transactions) \| [example](tests/charge.php#L66-L69)).<br>The `index` is the number of actions recorded by your system, and it's a security measure against double captures.

```php
$order = (arr) $yourDB.getOrder('order_184');
$trnID = (int) $order['scanpay']['id'];
$nActs = count($order['scanpay']['acts']); // $change['acts'] from seq()
$data = [
    'total' => $order['amount'], // e.g. '199,99 DKK'
    'index' => $nActs,
];
$scanpay->capture($trnID, $data, $options);
```

#### charge(Integer, Array, $options): Array

Charge a subscriber ([docs](https://docs.scanpay.dev/subscriptions/charge-subscriber) \| [example](tests/charge.php)).

```php
$subscriberid = 2;
$charge = [
    'orderid'    => 'charge_1023',
    'items'    => [
        [
            'name'     => 'Pink Floyd: The Dark Side Of The Moon',
            'total'    => '199.99 DKK',
        ]
    ]
];
$scanpay->charge($subscriberid, $charge, $options);
```

#### renew(Integer, Array, $options): String

Create a link to renew the payment method for a subscriber. ([docs](https://docs.scanpay.dev/subscriptions/renew-subscriber) \| [example](tests/renew.php)).

```php
$subcriptionLink = $scanpay->renew($subscriberid, [], $options);
```

## Options

All methods, except `parsePing`, accept an optional per-request `$options` array. You can use this to:

* Set HTTP headers, e.g. the highly recommended `X-Cardholder-IP` ([example](tests/options.php#L17-L22))
* Override API key ([example](tests/options.php#L19))
* Change the hostname to use our test environment `api.scanpay.dev` ([example](tests/options.php#L14))
* Enable debugging mode ([example](tests/options.php#L25))
* Override cURL options with [`CURLOPT_*`](https://php.net/manual/en/function.curl-setopt.php) parameters ([example](tests/options.php#L28-L31)).

## Compatibility table

| PHP                                       | Version |
| :---------------------------------------- | :-----: |
| ~~CurlHandle class~~ (polyfilled)         | 8.0     |
| Typed class properties                    | 7.4     |
| Type declarations                         | 7.4     |
| Null coalescing operator                  | 7.4     |
| hash_equals                               | 5.6     |
| curl_strerror                             | 5.5     |


## License

Everything in this repository is licensed under the [MIT license](LICENSE).
