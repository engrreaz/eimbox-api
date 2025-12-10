<?php

require_once __DIR__ . "/config.php";   // FIXED PATH

if (!defined('DB_HOST')) {
    http_response_code(500);
    error_log("DB config missing.");
    die("Database configuration missing.");
}

$conn = null;

function db_connect()
{
    global $conn;

    if ($conn instanceof mysqli) {
        return $conn;
    }

    $conn = mysqli_init();

    if (!@mysqli_real_connect($conn, DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT)) {
        error_log("DB connect error: " . mysqli_connect_error());
        $conn = null;
        return false;
    }

    mysqli_set_charset($conn, DB_CHARSET);
    return $conn;
}

function db_close()
{
    global $conn;
    if ($conn instanceof mysqli) {
        mysqli_close($conn);
        $conn = null;
    }
}

function db_prepare_and_execute($query, $types = '', $params = [])
{
    $conn = db_connect();
    if (!$conn) return false;

    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) return false;

    if ($types !== '' && !empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    mysqli_stmt_execute($stmt);
    return $stmt;
}

function db_stmt_fetch_one_assoc($stmt)
{
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    mysqli_stmt_close($stmt);
    return $row ?: null;
}

function db_stmt_fetch_all_assoc($stmt)
{
    $result = mysqli_stmt_get_result($stmt);
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
    mysqli_stmt_close($stmt);
    return $rows;
}

function db_last_insert_id()
{
    return mysqli_insert_id(db_connect());
}

function db_query($query)
{
    return mysqli_query(db_connect(), $query);
}
