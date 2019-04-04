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
        'Idempotency-Key' => 'asdsdcdccsvasd',
    ],
    'debug' => false,
    'curl' => [
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_FALSESTART => 1,
#       CURLOPT_TCP_FASTOPEN => 1,
    ],
];

$subscriberid = 2;

$charge = [
    'orderid'    => 'charge-1023',
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
    $scanpay->charge($subscriberid, $charge, $options);
} catch (Scanpay\IdemReusableException $e) {
    echo "Received error which allows idempotency key to be reused: '" . $e->getMessage() . "\n";
    echo "<< Save idempotency key to database and trying to charge again later >>\n";
    die('Done for now');
} catch (\Exception $e) {
    die('Caught Scanpay client exception: ' . $e->getMessage() . "\n");
}

# Calculate total so we can print it
$tot = 0;
foreach ($charge['items'] as $item) {
    $tot += $item['total'];
}

echo 'Successfully charged ' . $tot . ' ' . explode(' ', $item['total'])[1] .
    " from subscriber #$subscriberid\n";

?>
