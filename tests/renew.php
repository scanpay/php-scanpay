<?php

/*
    Docs: https://docs.scanpay.dk/synchronization
    support@scanpay.dk || irc.scanpay.dev:6697 #support
*/
require dirname(__FILE__)  . '/../lib/Scanpay.php';

$apikey = '1153:YHZIUGQw6NkCIYa3mG6CWcgShnl13xuI7ODFUYuMy0j790Q6ThwBEjxfWFXwJZ0W';
$scanpay = new Scanpay\Scanpay($apikey);

$options = [
    'hostname' => 'api.scanpay.dev',
    'headers' => [
        'X-Cardholder-IP' => '192.168.1.1',
    ]
];

$subscriberid = 5;

$data = [
    'successurl' => 'https://docs.test.scanpay.dk/subscriptions/renew-subscriber',
    'language'   => 'da',
    'lifetime'   => '1h',
];

try {
    print_r($renewURL = $scanpay->renew($subscriberid, $data, $options) . "\n");
} catch (Exception $e) {
    die('Caught Scanpay client exception: ' . $e->getMessage() . "\n");
}
