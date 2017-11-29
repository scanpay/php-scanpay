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
];

$localSeq = 22;

try {
    $obj = $scanpay->seq($localSeq, $options);
} catch (Exception $e) {
    die('Caught Scanpay client exception: ' . $e->getMessage() . "\n");
}

foreach ($obj['changes'] as $change) {
    print_r($change);
}
print_r('New local seq after applying all changes: ' . $obj['seq'] . "\n");

?>
