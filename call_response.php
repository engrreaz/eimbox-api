<?php
// call_response.php
// Usage: php call_response.php

$apiUrl = "https/api.eimbox.com/pay/response/"; // <-- বদলান
$apiKey = "SFSFS89SDF";          // <-- আপনার API key
$apiSecret = "SFSAFSD98798";     // <-- আপনার API secret

$postData = [
    'student_id' => '1031871630',
    'amount' => '521',
    'payer_id' => '01919629672',
    'trans_id' => 'TXN889JU7X',
    'payment_ref' => 'REF99',
    'gateway_id' => 'bkash',
    'status' => 'SUCCESS'
];

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
    curl_close($ch);
    exit(1);
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$json = json_decode($response, true);

echo "HTTP: {$httpCode}\n";
if (json_last_error() === JSON_ERROR_NONE) {
    echo "Response:\n";
    print_r($json);
} else {
    echo "Raw response:\n";
    echo $response . "\n";
}