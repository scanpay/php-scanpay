<?php

/*
    Docs: https://docs.scanpay.dk/payment-link
    help@scanpay.dk || irc.libera.chat:6697 #scanpay
*/
require dirname(__FILE__)  . '/../lib/Scanpay.php';

$apikey = '1153:YHZIUGQw6NkCIYa3mG6CWcgShnl13xuI7ODFUYuMy0j790Q6ThwBEjxfWFXwJZ0W';
$scanpay = new Scanpay\Scanpay($apikey);

$options = [
    // Change API endpoint.
    'hostname' => 'api.test.scanpay.dk',

    // Set HTTP HEADERS
    'headers' => [
        // Manually overwrite the API key
        'Authorization' => 'Basic ' . base64_encode($apikey),
        // Client IP address (DoS prevention)
        'X-Cardholder-IP' => '192.168.1.1',
    ],

    // Set cURL to verbose
    'debug' => true,

    // cURL options (https://php.net/manual/en/function.curl-setopt.php)
    'curl' => [
        CURLOPT_SSL_FALSESTART => 1,
        CURLOPT_TCP_FASTOPEN => 1,
    ],
];

$order = [
    'orderid'    => 'a766409',
    'language'   => 'da',
    'successurl' => 'https://docs.scanpay.dk/payment-link',
    'items'    => [
        [
            'name'     => 'Pink Floyd: The Dark Side Of The Moon',
            'quantity' => 2,
            'total'    => '199.99 DKK',
            'sku'      => 'fadf23',
        ], [
            'name'     => '巨人宏偉的帽子',
            'quantity' => 2,
            'total'    => '420 DKK',
            'sku'      => '124',
        ],
    ]
];

try {
    print_r($newURL = $scanpay->newURL($order, $options) . "\n");
} catch (Exception $e) {
    die($e->getMessage() . "\n");
}
