# Scanpay PHP Client

PHP client library for the Scanpay API. You can always e-mail us at [help@scanpay.dk](mailto:help@scanpay.dk) or chat with us on `irc.scanpay.dk:6697` or `#scanpay` at Freenode ([webchat](https://webchat.freenode.net?randomnick=1&channels=scanpay&prompt=1))

## Installation

You need PHP version >= 5.6 with php-curl enabled. The package is published at [Packagist](https://packagist.org/packages/scanpay/scanpay). You can install the client via [Composer](http://getcomposer.org/):

```bash
composer require scanpay/scanpay
```
And initiate it in your project with:

```php
$scanpay = new Scanpay\Scanpay(' API-key ');
```

### Manual installation

If you do not wish to use Composer, you can download the [latest release](https://github.com/scanpaydk/php-scanpay/releases) and include in into your project:

```php
require('lib/Scanpay.php');
$scanpay = new Scanpay\Scanpay(' API-key ');
```

## Methods

Please note that all methods accept an optional per-request `options` object. You can read more about this [here](#options).

#### newURL(Object, Object)

Create a payment link by passing the order details ([spec](https://docs.scanpay.dk/payment-link#request-fields)) through `newURL`:

```php
$order = [
    'items' => [
        [
            'name'     => 'Pink Floyd: The Dark Side Of The Moon',
            'total'    => '199.99 DKK'
        ]
    ]
];
print_r ($URL = $scanpay->newURL($order, $options)); // returns String
```

#### seq(Int, Object)

Get an array with a number of changes since the supplied sequence number:

```php
$localSeq = 921;
$obj = $scanpay->seq($localSeq, $options);
print_r (obj.changes);
print_r ('New local seq after applying all changes: ' . obj.seq);
```

#### handlePing(Object)

Securely and efficiently validate pings. This method accepts an optional object with the following arguments:

* `signature`, ie. a string with the X-Signature header (String)
* `body`, ie. the HTTP message body (String).
* `debug` default is false. (Boolean)

```php
print_r ($json = $scanpay->handlePing());
print_r ($json.seq);
```

## Options

All methods, except `handlePing`, accept an optional per-request `options` object. You can use this to:

* Set the API key for this request ([example](tests/seq.php#L16))
* Set HTTP headers, e.g. the highly recommended `X-Cardholder-IP` ([example](tests/newURL.php#L14))
* Change the hostname to use our test environment `api.test.scanpay.dk` ([example](tests/newURL.php#L12))
* Enable debugging mode ([example](tests/newURL.php#L16))
* Override cURL options with [`CURLOPT_*`](http://php.net/manual/en/function.curl-setopt.php) parameters ([example](tests/seq.php#L19-L23)).

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
