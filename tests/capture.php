<?php

/*
    Docs: https://docs.scanpay.dev/
    support@scanpay.dk || irc.scanpay.dev:6697 #support
*/
require dirname(__FILE__)  . '/../lib/Scanpay.php';

$apikey = '1153:YHZIUGQw6NkCIYa3mG6CWcgShnl13xuI7ODFUYuMy0j790Q6ThwBEjxfWFXwJZ0W';
$scanpay = new Scanpay\Scanpay($apikey);

$options = [
    'hostname' => 'api.scanpay.dev',
];

$trnid = 431;
$total = '1 DKK';

$data = [
    'total' => $total,
    'index' => 0,
];

try {
    $chargeResponse = $scanpay->capture($trnid, $data, $options);
} catch (\Exception $e) {
    die("Capture failed: '" . $e->getMessage() . "\n");
}
echo "Successfully captured $total from trasaction $trnid\n";
