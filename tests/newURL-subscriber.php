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
    'curl' => [
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_FALSESTART => 1,
#       CURLOPT_TCP_FASTOPEN => 1,
    ],
];

$order = [
    'language'   => 'da',
    'successurl' => 'https://docs.scanpay.dk/payment-link',
    'subscriber'    => [
        'ref' => 'sub13991',
    ],
    'billing'  => [
        'name'    => 'John Doe',
        'company' => 'The Shop A/S',
        'email'   => 'john@doe.com',
        'phone'   => '+4512345678',
        'address' => ['Langgade 23, 2. th'],
        'city'    => 'Havneby',
        'zip'     => '1234',
        'state'   => '',
        'country' => 'DK',
        'vatin'   => '35413308',
        'gln'     => '7495563456235',
    ],
    'shipping' => [
        'name'    => 'Jan Dåh',
        'company' => 'The Choppa A/S',
        'email'   => 'jan@doh.com',
        'phone'   => '+4587654321',
        'address' => ['Langgade 23, 1. th', 'C/O The Choppa'],
        'city'    => 'Haveby',
        'zip'     => '1235',
        'state'   => '',
        'country' => 'DK',
    ],
];

try {
    print_r($newURL = $scanpay->newURL($order, $options) . "\n");
} catch (Exception $e) {
    die($e->getMessage() . "\n");
}

?>
