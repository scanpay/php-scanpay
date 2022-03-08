<?php
/*
    help@scanpay.dk || irc.libera.chat:6697 #scanpay
*/
ini_set('display_errors', 'On');
require dirname(__FILE__)  . '/../lib/Scanpay.php';

$apikey = '1153:YHZIUGQw6NkCIYa3mG6CWcgShnl13xuI7ODFUYuMy0j790Q6ThwBEjxfWFXwJZ0W';
$scanpay = new Scanpay\Scanpay($apikey);
$options = [
    'hostname' => 'api.test.scanpay.dk', // for seq request
];

$localSeq = 22;

try {
    $ping = $scanpay->handlePing();

    if ($ping['seq'] > $localSeq) {
        // Fetch changes with a seq request...
        $seq = $scanpay->seq($localSeq, $options);

        foreach ($seq['changes'] as $change) {
            // Apply some changes (captures, refunds ...)
            // to your database.
            print_r('Applied changes to transaction: #' . $change['id'] . "\n");
        }
        $localSeq = $seq['seq'];
    }
} catch(\Exception $e) {
    print_r('exception: ' . $e->getMessage());
}

?>
