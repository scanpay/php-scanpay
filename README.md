# Scanpay PHP client

The Scanpay PHP client library provides convenient and simplified access to the Scanpay API from programs written in PHP. 
If you have any questions or ideas, don't hesitate to contact us at [support@scanpay.dk](mailto:support@scanpay.dk) or chat with us on [`irc.scanpay.dev:6697`](https://chat.scanpay.dev).

## Requirements

PHP version >= 5.6 with php-curl enabled. See [compatibility table](#compatibility-table).

## Installation

The package is published at [Packagist](https://packagist.org/packages/scanpay/scanpay). You can install the library via [Composer](http://getcomposer.org/):

```bash
composer require scanpay/scanpay
```
You can then include it in your project with:

```php
$scanpay = new Scanpay\Scanpay('API key');
```

### Manual installation

If you do not wish to use Composer, you can download the [latest release](https://github.com/scanpaydk/php-scanpay/releases) and include it in into your project:

```php
require('lib/Scanpay.php');
$scanpay = new Scanpay\Scanpay('API key');
```

## Usage

The API documentation is available [here](https://docs.scanpay.dev/). Most methods accept an optional per-request object with [options](#options), here referred to as `$options`.

#### newURL(Object, options)

Create a link to our hosted payment window ([docs](https://docs.scanpay.dev/payment-link) \| [example](tests/newURL.php)).

```php
$order = [
    'orderid'    => '123',
    'items' => [
        [
            'name'     => 'Pink Floyd: The Dark Side Of The Moon',
            'total'    => '199.99 DKK'
        ]
    ]
];
print_r ($URL = $scanpay->newURL($order, $options)); // returns String
```

#### seq(Integer, options)

Make a sequence request to pull changes from the server ([docs](https://docs.scanpay.dev/synchronization#sequence-request) \| [example](tests/seq.php)).

```php
$localSeq = 921;
$obj = $scanpay->seq($localSeq, $options);
print_r (obj.changes);
print_r ('New local seq after applying all changes: ' . obj.seq);
```

#### handlePing(Object)

Handle and validate synchronization pings ([docs](https://docs.scanpay.dev/synchronization#ping-service) \| [example](tests/handlePing.php)).
```php
print_r ($json = $scanpay->handlePing());
print_r ($json.seq);
```
This method accepts an optional object with the following arguments:

* `signature`, ie. a string with the X-Signature header (String)
* `body`, ie. the HTTP message body (String).
* `debug` default is false. (Boolean)

#### capture(Integer, Object, options)

Capture an amount from a transaction.

```php
$trnid = 2;
$data = [
    'total' => '1 DKK',
    'index' => 0,
};
$scanpay->capture($trnid, $data, $options);
```

#### charge(Integer, Object, options)

Charge a subscriber ([docs](https://docs.scanpay.dev/subscriptions/charge-subscriber) \| [example](tests/charge.php)).

```php
$subscriberid = 2;
$charge = [
    'orderid'    => 'charge-1023',
    'items'    => [
        [
            'name'     => 'Pink Floyd: The Dark Side Of The Moon',
            'total'    => '199.99 DKK',
        ]
    ]
};
$scanpay->charge($subscriberid, $charge, $options);
```

#### renew(Integer, Object, options)

Create a link to renew the payment method for a subscriber. ([docs](https://docs.scanpay.dev/subscriptions/renew-subscriber) \| [example](tests/renew.php)).

```php
print_r ($URL = $scanpay->renew($subscriberid, [], $options)); // returns String
```

## Options

All methods, except `handlePing`, accept an optional per-request `options` object. You can use this to:

* Set HTTP headers, e.g. the highly recommended `X-Cardholder-IP` ([example](tests/options.php#L17-L22))
* Override API key ([example](tests/options.php#L19))
* Change the hostname to use our test environment `api.test.scanpay.dk` ([example](tests/options.php#L14))
* Enable debugging mode ([example](tests/options.php#L25))
* Override cURL options with [`CURLOPT_*`](https://php.net/manual/en/function.curl-setopt.php) parameters ([example](tests/options.php#L28-L31)).

## Compatibility table

| Feature                                   | Version |
| :---------------------------------------- | :-----: |
| hash_equals                               | 5.6     |
| curl_strerror                             | 5.5     |
| Array, short syntax                       | 5.4     |
| Namespaces                                | 5.3.0   |
| json_decode                               | 5.2.0   |
| curl_setopt_array                         | 5.1.3   |
| hash_hmac                                 | 5.1.2   |
| Exception class                           | 5.1.0   |
| Default function parameters               | 5.0.0   |

## License

Everything in this repository is licensed under the [MIT license](LICENSE).
