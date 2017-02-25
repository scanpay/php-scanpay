<?php
ini_set('display_errors', 'On');
require 'vendor/autoload.php';

$apikey = ' API-key ';
$scanpay = new Scanpay\Scanpay($apikey);

$options = [
    'auth'  =>  $apikey, // Overwrite the api-key (Optional)
];

try {
    print_r ($seq = $scanpay->seq(0));
} catch (Exception $e) {
    die('Caught Scanpay client exception: ' . $e->getMessage() . "\n");
}

?>
