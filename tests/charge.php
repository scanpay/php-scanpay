<?php

declare(strict_types=1);

/*
    Docs: https://docs.scanpay.dev/subscriptions/charge-subscriber
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

$idemkey = $options['headers']['Idempotency-Key'];
$orderid = $data['orderid'];
try {
    echo "[Charge] 1st charge with orderid: #$orderid and idempotency-key: $idemkey\n";
    $scanpay->charge($subscriberid, $data, $options);
    echo "[Charge] 2nd charge with orderid: #$orderid and idempotency-key: $idemkey (idempotency test)\n";
    $scanpay->charge($subscriberid, $data, $options);
    echo "[Charge] 3rd charge with orderid: #$orderid and idempotency-key: $idemkey (idempotency test)\n";
    $scanpay->charge($subscriberid, $data, $options);
} catch (Scanpay\IdempotentResponseException $e) {
    echo('Received idempotent error response: ' . $e->getMessage() . "\n");
    die('You can generate a new idempotency key and try again later');
} catch (\Exception $e) {
    echo "Received error which is not idempotent: '" . $e->getMessage() . "\n";
    echo "<< Save idempotency key to database >>\n";
    die('Done for now. Retry later with same idempotency key ' . $idempotencyKey);
}

echo "[Seq] Searching for orderid #$orderid in seq feed...\n";
$seq = 1200;
while (1) {
    $res = $scanpay->seq($seq, ['hostname' => 'api.scanpay.dev']);
    if (count($res['changes']) === 0) {
        echo "[Seq] Sync is completed.";
        break; // done
    }
    foreach ($res['changes'] as $change) {
        if (isset($change['orderid']) && $change['orderid'] === $data['orderid']) {
            if ($change['totals']['authorized'] !== $change['totals']['captured']) {
                echo "      Found #$orderid in seq feed. Charge ID is #{$change['id']}\n";
                echo "      Capturing charge with #{$change['id']} ...\n";
                $capt = $scanpay->capture($change['id'], [
                    'total' => $change['totals']['authorized'],
                    'index' => 0,
                ], ['hostname' => 'api.scanpay.dev']);
            } else {
                echo "      Charge #{$change['id']} was successfully captured. \n";
            }
        }
    }
    $seq = $res['seq'];
}
echo "New seq is: $seq\n";
