<?php
ini_set('display_errors', 'On');
require dirname(__FILE__)  . '/../lib/Scanpay.php';

$apikey = ' API-key ';
$scanpay = new Scanpay\Scanpay($apikey);

$options = [
    'auth'  =>  $apikey, // Overwrite the api-key (Optional)
];

try {
    print_r($ping = $scanpay->handlePing());
} catch(\Exception $e) {
    print_r('exception: ', $e->getMessage());
}

?>
