<?php

require_once __DIR__ . "/db.php";

function validateClient()
{

    global $conn;

    db_connect(); // ensure db connection

    $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
    $apiSecret = $_SERVER['HTTP_X_API_SECRET'] ?? '';
    $clientIP = $_SERVER['REMOTE_ADDR'];

    if (!$apiKey || !$apiSecret) {
        return ['status' => false, 'msg' => 'Missing API Credentials'];
    }

    // Query updated to match your table: api
    $stmt = db_prepare_and_execute(
        "SELECT sccode, api_secret, allowed_id, status 
         FROM api 
         WHERE api_key=? AND status='active' LIMIT 1",
        "s",
        [$apiKey]
    );

    if (!$stmt) {
        return ['status' => false, 'msg' => 'DB Error'];
    }

    $row = db_stmt_fetch_one_assoc($stmt);

    if (!$row) {
        return ['status' => false, 'msg' => 'Invalid API Key'];
    }

    // Validate secret
    if ($row['api_secret'] !== $apiSecret) {
        return ['status' => false, 'msg' => 'Secret mismatched'];
    }

    // If allowed_id is not NULL, validate IP
    if (!empty($row['allowed_id']) && $row['allowed_id'] !== $clientIP) {
        return ['status' => false, 'msg' => 'IP not allowed'];
    }

    // SUCCESS — return sccode as well
    return [
        'status' => true,
        'sccode' => $row['sccode']
    ];
}