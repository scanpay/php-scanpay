<?php
/*
    help@scanpay.dk || irc.libera.chat:6697 #scanpay
*/
ini_set('display_errors', 'On');
require dirname(__FILE__)  . '/../lib/Scanpay.php';

$apikey = '1153:YHZIUGQw6NkCIYa3mG6CWcgShnl13xuI7ODFUYuMy0j790Q6ThwBEjxfWFXwJZ0W';
$scanpay = new Scanpay\Scanpay($apikey);


$idempotencyKey = $scanpay->generateIdempotencyKey();
/* == Save the key to your database with your order or charge entry == */

$options = [
    'hostname' => 'api.test.scanpay.dk',
    'headers' => [
        'X-Cardholder-IP' => '192.168.1.1',
        'Idempotency-Key' => $idempotencyKey,
    ],
    'debug' => false,
    'curl' => [
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_FALSESTART => 1,
#       CURLOPT_TCP_FASTOPEN => 1,
    ],
];

$subscriberid = 10;

$charge = [
    'orderid'    => 'charge-1023',
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
    $chargeResponse = $scanpay->charge($subscriberid, $charge, $options);
} catch (Scanpay\IdempotentResponseException $e) {
    echo('Received idempotent error response: ' . $e->getMessage() . "\n");
    die('You can generate a new idempotency key and try again later');
} catch (\Exception $e) {
    echo "Received error which is not idempotent: '" . $e->getMessage() . "\n";
    echo "<< Save idempotency key to database >>\n";
    die('Done for now. Retry later with same idempotency key ' . $idempotencyKey);
}

# Calculate total so we can print it
$tot = 0;
foreach ($charge['items'] as $item) {
    $tot += $item['total'];
}
$tot .= ' ' . explode(' ', $item['total'])[1];
if ($chargeResponse['totals']['authorized'] < $tot) {
    echo "Charge resulted in a partial authorization, charged {$chargeResponse['totals']['authorized']}" .
        " of $tot from subscriber #$subscriberid (Created trn #$chargeResponse[id])\n";
} else {
    echo "Successfully charged $tot" .
        " from subscriber #$subscriberid (Created trn #$chargeResponse[id])\n";
}

?>
