<?php

namespace Scanpay;

class IdempotentResponseException extends \Exception
{
}

class Scanpay
{
    protected $ch;
    protected $headers;
    protected $apikey;
    protected $idemstatus;
    protected $opts;

    public function __construct($apikey = '', $opts = [])
    {
        if (!function_exists('curl_init')) {
            die("ERROR: Please enable php-curl\n");
        }
        $this->ch = curl_init(); // reuse handle
        $this->headers = [
            'authorization' => 'Authorization: Basic ' . base64_encode($apikey),
            'x-sdk' => 'X-SDK: PHP-1.5.2/' . PHP_VERSION,
            'content-type' => 'Content-Type: application/json',
        ];
        $this->apikey = $apikey;
        $this->opts = $opts;
        if (isset($opts['headers'])) {
            foreach ($opts['headers'] as $key => &$val) {
                $this->headers[strtolower($key)] = $key . ': ' . $val;
            }
        }
    }

    protected function headerCallback($curl, $hdr)
    {
        $header = explode(':', $hdr, 2);
        // Skip invalid headers
        if (count($header) < 2) return strlen($hdr);

        if (strtolower(trim($header[0])) === 'idempotency-status') {
            $this->idemstatus = strtoupper(trim($header[1]));
        }
        return strlen($hdr);
    }

    protected function request($path, $opts = [], $data = null)
    {
        $this->idemstatus = null;
        $headers = $this->headers;
        if (isset($opts['headers'])) {
            foreach ($opts['headers'] as $key => &$val) {
                $headers[strtolower($key)] = $key . ': ' . $val;
            }
        }
        $opts = array_merge($this->opts, $opts);
        $curlopts = [
            CURLOPT_URL => 'https://' . ($opts['hostname'] ?? 'api.scanpay.dk') . $path,
            CURLOPT_HTTPHEADER => array_values($headers),
            CURLOPT_CUSTOMREQUEST => ($data === null) ? 'GET' : 'POST',
            CURLOPT_VERBOSE => $opts['debug'] ?? 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => 20,
            CURLOPT_TIMEOUT => 120,
        ];
        if ($data !== null) {
            $curlopts[CURLOPT_POSTFIELDS] = json_encode($data, JSON_UNESCAPED_SLASHES);
            if ($curlopts[CURLOPT_POSTFIELDS] === false) {
                throw new \Exception('Failed to JSON encode request to Scanpay: ' . json_last_error_msg());
            }
        }
        if (isset($headers['idempotency-key'])) {
            $curlopts[CURLOPT_HEADERFUNCTION] = [$this, 'headerCallback'];
        }

        if (isset($opts['curl'])) {
            foreach ($opts['curl'] as $key => &$val) {
                $curlopts[$key] = $val;
            }
        }
        curl_setopt_array($this->ch, $curlopts);

        $result = curl_exec($this->ch);
        if (!$result) {
            throw new \Exception(curl_strerror(curl_errno($this->ch)));
        }

        $statusCode = curl_getinfo($this->ch, CURLINFO_RESPONSE_CODE);
        if ($statusCode !== 200) {
            throw new \Exception(('Scanpay returned "' . explode("\n", $result)[0] . '"'));
        }

        // Validate Idempotency-Status
        if (isset($headers['idempotency-key']) && $this->idemstatus !== 'OK') {
            throw new \Exception("Server failed to provide idempotency. Scanpay returned $statusCode - " . explode("\n", $result)[0]);
        }

        // Decode the json response (@: suppress warnings)
        if (!is_array($resobj = @json_decode($result, true))) {
            throw new IdempotentResponseException('Invalid JSON response from server');
        }
        return $resobj;
    }

    // newURL: Create a new payment link
    public function newURL($data, $opts = [])
    {
        $o = $this->request('/v1/new', $opts, $data);
        if (isset($o['url']) && filter_var($o['url'], FILTER_VALIDATE_URL)) {
            return $o['url'];
        }
        throw new \Exception('Invalid response from server');
    }

    // seq: Get array of changes since the reqested seqnum
    public function seq($seqnum, $opts = [])
    {
        if (!is_numeric($seqnum)) {
            throw new \Exception('seq argument must be an integer');
        }
        $o = $this->request('/v1/seq/' . $seqnum, $opts);

        if (
            isset($o['seq']) && is_int($o['seq']) &&
            isset($o['changes']) && is_array($o['changes'])
        ) {
            return $o;
        }
        throw new \Exception('Invalid response from server');
    }

    // handlePing: Convert data to JSON and validate integrity
    public function handlePing($opts = [])
    {
        ignore_user_abort(true);

        if (isset($opts['signature'])) {
            $signature = $opts['signature'];
        } elseif (isset($_SERVER['HTTP_X_SIGNATURE'])) {
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

        if (
            isset($obj['seq']) && is_int($obj['seq']) &&
            isset($obj['shopid']) && is_int($obj['shopid'])
        ) {
            return $obj;
        }
        throw new \Exception('missing fields in Scanpay response');
    }

    public function capture($trnid, $data, $opts = [])
    {
        return $this->request("/v1/transactions/$trnid/capture", $opts, $data);
    }

    public function generateIdempotencyKey()
    {
        return rtrim(base64_encode(random_bytes(32)), '=');
    }

    public function charge($subid, $data, $opts = [])
    {
        $o = $this->request("/v1/subscribers/$subid/charge", $opts, $data);
        if (
            isset($o['type']) && $o['type'] === 'charge' &&
            isset($o['id']) && is_int($o['id']) &&
            isset($o['totals']) && isset($o['totals']['authorized'])
        ) {
            return $o;
        }
        throw new \Exception('Invalid response from server');
    }

    public function renew($subid, $data, $opts = [])
    {
        $o = $this->request("/v1/subscribers/$subid/renew", $opts, $data);
        if (isset($o['url']) && filter_var($o['url'], FILTER_VALIDATE_URL)) {
            return $o['url'];
        }
        throw new \Exception('Invalid response from server');
    }
}
