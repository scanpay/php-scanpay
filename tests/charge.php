<?php

/*
    Docs: https://docs.scanpay.dev/subscriptions/
    support@scanpay.dk || irc.scanpay.dev:6697 #support
*/
require dirname(__FILE__)  . '/../lib/Scanpay.php';

$apikey = '1153:YHZIUGQw6NkCIYa3mG6CWcgShnl13xuI7ODFUYuMy0j790Q6ThwBEjxfWFXwJZ0W';
$subscriberid = 68;
$scanpay = new Scanpay\Scanpay($apikey);

$data = [
    'orderid'    => uniqid(),
    'autocapture'   => false,
    'items'    => [
        [
            'name'     => 'Pink Floyd: The Dark Side Of The Moon',
            'quantity' => 2,
            'total'    => '199.99 DKK',
            'sku'      => 'fadf23',
        ]
    ],
];
$options = [
    'hostname' => 'api.scanpay.dev',
    //'debug' => 1,
    'headers' => [
        'Idempotency-Key' => $scanpay->generateIdempotencyKey(),
    ],
];

try {
    echo "* 1st charge with orderid #{$data['orderid']} and idempotency-key: {$options['headers']['Idempotency-Key']}\n";
    $scanpay->charge($subscriberid, $data, $options);
    echo "* 2nd charge with orderid #{$data['orderid']} and idempotency-key: {$options['headers']['Idempotency-Key']}\n";
    $scanpay->charge($subscriberid, $data, $options);
    echo "* 3rd charge with orderid #{$data['orderid']} and idempotency-key: {$options['headers']['Idempotency-Key']}\n";
    $scanpay->charge($subscriberid, $data, $options);
} catch (Scanpay\IdempotentResponseException $e) {
    echo('Received idempotent error response: ' . $e->getMessage() . "\n");
    die('You can generate a new idempotency key and try again later');
} catch (\Exception $e) {
    echo "Received error which is not idempotent: '" . $e->getMessage() . "\n";
    echo "<< Save idempotency key to database >>\n";
    die('Done for now. Retry later with same idempotency key ' . $idempotencyKey);
}

$seq = 1200; // $db['seq'];
while (1) {
    $res = $scanpay->seq($seq, ['hostname' => 'api.scanpay.dev']);
    if (count($res['changes']) === 0) {
        break; // done
    }
    foreach ($res['changes'] as $change) {
        if (isset($change['orderid']) && $change['orderid'] === $data['orderid']) {
            echo "\nFound 1 charge with orderid #{$data['orderid']} in seq feed.\n";
        }
    }
    $seq = $res['seq'];
}
echo "New seq is: $seq\n"

?>
