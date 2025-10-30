<?php
/**
 * Database Configuration File
 * MySQL/MariaDB 연결 설정
 */

if (!defined('DISPENSER')) exit; // 직접 접근 차단

// 데이터베이스 연결 정보
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'dispenser');
define('DB_CHARSET', 'utf8mb4');

// MySQLi 연결 생성
$con = mysqli_connect(DB_HOST, DB_USER, DB_PASS);

if (!$con) {
    die(json_encode([
        'result' => 'error',
        'error' => [
            'code' => 500,
            'msg' => 'Database connection failed: ' . mysqli_connect_error()
        ]
    ], JSON_UNESCAPED_UNICODE));
}

// 문자셋 설정
mysqli_set_charset($con, DB_CHARSET);

// 데이터베이스 선택 (없으면 생성)
$dbSelected = mysqli_select_db($con, DB_NAME);

if (!$dbSelected) {
    // 데이터베이스가 없으면 생성 시도
    $createDB = "CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "`
                 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";

    if (mysqli_query($con, $createDB)) {
        mysqli_select_db($con, DB_NAME);
    } else {
        die(json_encode([
            'result' => 'error',
            'error' => [
                'code' => 500,
                'msg' => 'Database creation failed: ' . mysqli_error($con)
            ]
        ], JSON_UNESCAPED_UNICODE));
    }
}

// 타임존 설정
mysqli_query($con, "SET time_zone = '+09:00'");

?>
