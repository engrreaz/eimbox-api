<?php
// call_request.php
// Usage: php call_request.php

$apiUrl = "https/api.eimbox.com/pay/request/"; // <-- বদলান
$apiKey = "SFSFS89SDF";          // <-- আপনার API key
$apiSecret = "SFSAFSD98798";     // <-- আপনার API secret

$postData = [
    'student_id' => '1031871666'
];

// init
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-API-KEY: {$apiKey}",
    "X-API-SECRET: {$apiSecret}",
    "Accept: application/json"
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    fwrite(STDERR, "cURL error: " . curl_error($ch) . PHP_EOL);
    $ch = null;
    exit(1);
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$ch = null;

// try decode
$json = json_decode($response, true);

echo "HTTP: {$httpCode}\n";
if (json_last_error() === JSON_ERROR_NONE) {
    echo "Response:\n";
    print_r($json);
} else {
    echo "Raw response:\n";
    echo $response . "\n";
}