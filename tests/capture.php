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
    'debug' => false,
    'curl' => [
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_FALSESTART => 1,
    ],
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
echo "Successfully captured $total from trasaction $trnid\n"
?>
