<?php

ini_set('display_errors', 'On');

$apikey = '';
$scanpay = new ScanpayClient($apikey);

$order = [
    'orderid'    => 'a766409',
    'language'   => 'da',
    'successurl' => 'https://insertyoursuccesspage.dk',
    'items'    => [
        [
            'name'     => 'Pink Floyd: The Dark Side Of The Moon',
            'quantity' => 2,
            'price'    => '99.99 DKK',
            'sku'      => 'fadf23',
        ], [
            'name'     => '巨人宏偉的帽子',
            'quantity' => 2,
            'price'    => '420 DKK',
            'sku'      => '124',
        ],
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

$options = [
    'headers' => [ // Array of headers
        'X-Cardholder-IP: ' . $_SERVER['REMOTE_ADDR'],
    ],
    'auth'  =>  $apikey, // Overwrite the api-key (Optional)
];

echo $paymenturl = $scanpay->new($order, $options);
echo $abc = $scanpay->seq(0);


?>

