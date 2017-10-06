<?php
namespace Scanpay;

class Scanpay {
    protected $opts;
    protected $ch;
    protected $apikey;

    public function __construct($apikey = '') {
        // Check if libcurl is enabled
        if (!function_exists('curl_init')) { throw new \Exception('Enable php-curl.'); }

        // Public cURL handle (we want to reuse connections)
        $this->ch = curl_init();

        curl_setopt_array($this->ch, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 30, // Timeout after 30s
            CURLOPT_USE_SSL => CURLUSESSL_ALL,
            CURLOPT_SSLVERSION => 6, // TLSv1.2
            CURLOPT_TCP_NODELAY => 1,
        ]);

        $this->opts = [
            'hostname' => 'api.scanpay.dk',
            'headers' => [
                'Authorization: Basic ' . base64_encode($apikey),
                'X-SDK: PHP-1.2.0/'. PHP_VERSION,
                'Content-Type: application/json'
            ]
        ];

        $this->apikey = $apikey;
    }

    protected function request($url, $opts=[], $data=null) {
        $o = array_merge($this->opts, $opts);

        if (isset($opts['headers'])) {
            // array_merge is not deep. So now we need to reassign headers.
            $o['headers'] = array_merge($this->opts['headers'], $opts['headers']);
        }

        if (isset($opts['auth'])) {  // redefine the API key.
            $o['headers'][0] = 'Authorization: Basic ' . base64_encode($opts['auth']);
        }

        if ($data !== null) {
            curl_setopt_array($this->ch, [
                CURLOPT_URL => 'https://' . $o['hostname'] . $url,
                CURLOPT_HTTPHEADER => $o['headers'],
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($data, JSON_UNESCAPED_SLASHES),
            ]);
        } else {
            curl_setopt_array($this->ch, [
                CURLOPT_URL => 'https://' . $o['hostname'] . $url,
                CURLOPT_HTTPHEADER => $o['headers'],
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_POSTFIELDS => 0,
            ]);
        }

        $result = curl_exec($this->ch);
        if (!$result) {
            throw new \Exception(curl_strerror(curl_errno($this->ch)));
        }

        $code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        if ($code !== 200) {
            throw new \Exception(explode("\n", $result)[0]);
        }

        // Decode the json response (@: surpress warnings)
        if (!$resobj = @json_decode($result, true)) {
            throw new \Exception('Invalid response from server');
        }
        return $resobj;
    }

    public function newURL($data, $opts=[]) {
        $o = $this->request('/v1/new', $opts, $data);
        if (isset($o['url']) && filter_var($o['url'], FILTER_VALIDATE_URL)) {
            return $o['url'];
        }
        throw new \Exception('Invalid response from server');
    }

    public function seq($seq, $opts=[]) {
        $o = $this->request('/v1/seq/' . $seq, $opts);
        if (isset($o['seq']) && is_int($o['seq']) && isset($o['changes']) && is_array($o['changes'])) {
            return $o;
        }
        throw new \Exception('Invalid response from server');
    }

    public function maxSeq($opts=[]) {
        $o = $this->request('/v1/seq', $opts);
        if (isset($o['seq']) && is_int($o['seq'])) {
            return $o['seq'];
        }
        throw new \Exception('Invalid response from server');
    }

    public function handlePing($opts=[]) {
        ignore_user_abort(true);
        // TODO: Close connection and save bandwidth
        // Notice that log_error and flush/echo wont work
        // after fastcgi_finish_request has been called.
        // if (function_exists('fastcgi_finish_request')) {
        //    fastcgi_finish_request();
        // }

        if (isset($opts['signature'])) {
            $signature = $opts['signature'];
        } else if (isset($_SERVER['HTTP_X_SIGNATURE'])) {
            $signature = $_SERVER['HTTP_X_SIGNATURE'];
        } else {
            throw new \Exception('missing ping signature');
        }

        $body = isset($opts['body']) ? $opts['body'] : file_get_contents('php://input');
        if ($body === false) {
            throw new \Exception('unable to get ping body');
        }

        $checksum = base64_encode(hash_hmac('sha256', $body, $this->apikey, true));

        if (!hash_equals($checksum, $signature)) {
            throw new \Exception('invalid ping signature');
        }

        $obj = @json_decode($body, true);
        if ($obj === null) {
            throw new \Exception('invalid json from Scanpay server');
        }

        if (isset($obj['seq']) && is_int($obj['seq']) &&
            isset($obj['shopid']) && is_int($obj['shopid'])) {
            return $obj;
        }
        throw new \Exception('missing fields in Scanpay response');
    }
}

?>
