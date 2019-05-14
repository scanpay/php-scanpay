<?php
/*
    help@scanpay.dk || irc.scanpay.dk:6697 || scanpay.dk/slack
*/
ini_set('display_errors', 'On');
require dirname(__FILE__)  . '/../lib/Scanpay.php';

$apikey = '1153:YHZIUGQw6NkCIYa3mG6CWcgShnl13xuI7ODFUYuMy0j790Q6ThwBEjxfWFXwJZ0W';
$scanpay = new Scanpay\Scanpay($apikey);

$options = [
    'hostname' => 'api.test.scanpay.dk',
    'headers' => [
        'X-Cardholder-IP' => '192.168.1.1',
    ],
    'debug' => false,
    'curl' => [
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_FALSESTART => 1,
#       CURLOPT_TCP_FASTOPEN => 1,
    ],
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

?>
