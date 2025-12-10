<?php
header("Content-Type: application/json");

require_once "../../config.php";
require_once "../../db.php";
require_once "../../security.php";

// Connect DB
$conn = db_connect();

// Validate API client
$auth = validateClient();
if (!$auth['status']) {
    echo json_encode($auth);
    exit;
}

$sccode = $auth['sccode'];   // <-- sccode available
$student_id = $_POST['student_id'] ?? '';

if (!$student_id) {
    echo json_encode([
        'status' => false,
        'msg' => 'student_id is required'
    ]);
    exit;
}

// Prepare SQL
$sql = "SELECT stnameeng
        FROM students 
        WHERE sccode=? AND stid=? 
        LIMIT 1";

// Prepare & execute statement
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ss", $sccode, $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);

if (!$student) {
    echo json_encode([
        "status" => false,
        "msg" => "Student not found for this institute" . $student_id,
        "sccode" => $sccode
    ]);
    exit;
}

$currentMonth = date('m');
$y = date('y');  // অথবা session year অনুযায়ী পাস করুন
$like_year = "%$y%";

// Prepare SQL for dues sum
$sql_dues = "SELECT SUM(dues) AS total_due 
             FROM stfinance 
             WHERE sccode=? 
               AND stid=? 
               AND month <= ? 
               AND sessionyear LIKE ? 
               AND dues > 0";

$stmt_dues = mysqli_prepare($conn, $sql_dues);

mysqli_stmt_bind_param($stmt_dues, "ssss", $sccode, $student_id, $currentMonth, $like_year);
mysqli_stmt_execute($stmt_dues);
$result_dues = mysqli_stmt_get_result($stmt_dues);
$dues_row = mysqli_fetch_assoc($result_dues);

$total_due = $dues_row['total_due'] ?? 0;




// Return response
echo json_encode([
    "status"      => true,
    "sccode"      => $sccode,
    "student_id"  => $student_id,
    "name"        => $student['stnameeng'],
    "due_amount"  => $total_due,
    "currency"    => "BDT"
]);
