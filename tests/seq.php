<?php

/*
    Docs: https://docs.scanpay.dev/synchronization
    support@scanpay.dk || irc.scanpay.dev:6697 #support
*/
require dirname(__FILE__)  . '/../lib/Scanpay.php';

$apikey = '1153:YHZIUGQw6NkCIYa3mG6CWcgShnl13xuI7ODFUYuMy0j790Q6ThwBEjxfWFXwJZ0W';
$scanpay = new Scanpay\Scanpay($apikey);

$options = [
    'hostname' => 'api.scanpay.dev',
];

$localSeq = 270;

try {
    $obj = $scanpay->seq($localSeq, $options);
} catch (Exception $e) {
    die('Caught Scanpay client exception: ' . $e->getMessage() . "\n");
}

foreach ($obj['changes'] as $change) {
    print_r($change);
}
print_r('New local seq after applying all changes: ' . $obj['seq'] . "\n");
