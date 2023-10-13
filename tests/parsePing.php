<?php

declare(strict_types=1);

/*
    Docs: https://docs.scanpay.dev/synchronization
    support@scanpay.dk || irc.scanpay.dev:6697 #support
*/

ignore_user_abort(true);
set_time_limit(120);

require 'lib/Scanpay.php';
$apikey = '21185:xcXA4JJPMNHdAovCi2/r/5mBdOowbkBPWkCsfzskcb9PIqSWI1O+SbXElureoptJ';
$scanpay = new Scanpay\Scanpay($apikey);

// Check if 'X-Signature' header is set
if (!isset($_SERVER['HTTP_X_SIGNATURE'])) {
    echo json_encode(['error' => 'invalid signature']);
    die();
}

try {
    $ping = $scanpay->parsePing(
        file_get_contents('php://input', false, null, 0, 512),
        $_SERVER['HTTP_X_SIGNATURE']
    );

    // Handle the ping ...
    $localSeq = 0;
    if ($ping['seq'] >= $localSeq) {
        // Fetch changes with a seq request...
        $seq = $scanpay->seq($localSeq);
        var_dump($seq);
        $localSeq = $seq['seq'];
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
    die();
}

// Send a success respond to the ping server (optional)
echo json_encode(['success' => true]);
