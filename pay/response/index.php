<?php
header("Content-Type: application/json");

require_once "../../config.php";
require_once "../../db.php";
require_once "../../security.php";

$conn = db_connect();

$auth = validateClient();
if (!$auth['status']) {
    echo json_encode($auth);
    exit;
}

// institute code
$sccode = $auth['sccode'];

$student_id = $_POST['student_id'] ?? '';
$amount = $_POST['amount'] ?? '';
$payer_id = $_POST['payer_id'] ?? '';
$trans_id = $_POST['trans_id'] ?? '';
$payment_ref = $_POST['payment_ref'] ?? '';
$gateway_id = $_POST['gateway_id'] ?? '';
$status = $_POST['status'] ?? '';

if (!$student_id || !$amount || !$trans_id) {
    echo json_encode([
        "status" => false,
        "msg" => "Required fields missing"
    ]);
    exit;
}

if ($status !== "SUCCESS") {
    echo json_encode([
        "status" => false,
        "msg" => "Payment not successful"
    ]);
    exit;
}

/*
|--------------------------------------------------------------------------
| IMPORTANT:
| If you had payment history table, query example:
|
| INSERT INTO payments (sccode, stid, amount, gateway_id, trans_id, payment_ref, status, created_at)
| VALUES (?, ?, ?, ?, ?, ?, 'SUCCESS', NOW())
|
|--------------------------------------------------------------------------
| As per your instruction, we will not use database here.
|--------------------------------------------------------------------------
*/

// Just returning ACK
echo json_encode([
    "status" => true,
    "msg" => "Payment recorded successfully",
    "sccode" => $auth['sccode'],
    "student_id" => $student_id,
    "amount" => $amount,
    "payer_id" => $payer_id,
    "trans_id" => $trans_id,
    "payment_ref" => $payment_ref,
    "gateway_id" => $gateway_id
]);
