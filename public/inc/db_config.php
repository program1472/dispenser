<?php
/**
 * 데이터베이스 연결 설정 및 유틸리티 함수
 *
 * UTF-8 인코딩 처리를 표준화하여 한글 데이터 깨짐 방지
 * 모든 DB 작업은 이 파일의 함수를 사용할 것
 */

// DB 연결 설정
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'program1472');
define('DB_PASS', '$gPfls1129');
define('DB_NAME', 'dispenser');

// 원격 서버
define('DB_HOST_REMOTE', '220.70.12.230');
define('DB_PORT_REMOTE', 3306);

/**
 * DB 연결 생성 (UTF-8 인코딩 자동 설정)
 *
 * @param bool $remote 원격 서버 연결 여부
 * @return mysqli|false 연결 객체 또는 false
 */
function getDBConnection($remote = false) {
    $host = $remote ? DB_HOST_REMOTE : DB_HOST;
    $port = $remote ? DB_PORT_REMOTE : 3306;

    // 연결 생성
    $con = mysqli_connect($host, DB_USER, DB_PASS, DB_NAME, $port);

    if (!$con) {
        error_log("DB 연결 실패 (" . ($remote ? "원격" : "로컬") . "): " . mysqli_connect_error());
        return false;
    }

    // UTF-8 인코딩 설정 (필수)
    // 이 설정이 없으면 한글 데이터가 깨짐
    mysqli_set_charset($con, "utf8mb4");
    mysqli_query($con, "SET NAMES utf8mb4");
    mysqli_query($con, "SET CHARACTER_SET_CLIENT=utf8mb4");
    mysqli_query($con, "SET CHARACTER_SET_RESULTS=utf8mb4");
    mysqli_query($con, "SET CHARACTER_SET_CONNECTION=utf8mb4");

    return $con;
}

/**
 * DB 연결 종료
 *
 * @param mysqli $con 연결 객체
 */
function closeDBConnection($con) {
    if ($con && $con instanceof mysqli) {
        mysqli_close($con);
    }
}

/**
 * SQL 쿼리 실행 및 에러 로깅
 *
 * @param mysqli $con 연결 객체
 * @param string $sql SQL 쿼리
 * @return mysqli_result|bool 결과 또는 false
 */
function executeQuery($con, $sql) {
    $result = mysqli_query($con, $sql);

    if (!$result) {
        error_log("SQL 실행 실패: " . mysqli_error($con));
        error_log("SQL: " . $sql);
    }

    return $result;
}

/**
 * UTF-8 안전한 문자열 이스케이프
 *
 * @param mysqli $con 연결 객체
 * @param string $value 이스케이프할 값
 * @return string 이스케이프된 값
 */
function escapeString($con, $value) {
    return mysqli_real_escape_string($con, $value);
}

/**
 * 안전한 INSERT 쿼리 실행
 *
 * @param mysqli $con 연결 객체
 * @param string $table 테이블명
 * @param array $data ['컬럼명' => '값'] 배열
 * @return bool 성공 여부
 */
function safeInsert($con, $table, $data) {
    $columns = array_keys($data);
    $values = array_values($data);

    // 모든 값을 이스케이프
    $escapedValues = array_map(function($value) use ($con) {
        if ($value === null) return 'NULL';
        return "'" . mysqli_real_escape_string($con, $value) . "'";
    }, $values);

    $sql = "INSERT INTO " . $table . " (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $escapedValues) . ")";

    return executeQuery($con, $sql) !== false;
}

/**
 * 안전한 UPDATE 쿼리 실행
 *
 * @param mysqli $con 연결 객체
 * @param string $table 테이블명
 * @param array $data ['컬럼명' => '값'] 배열
 * @param string $where WHERE 조건
 * @return bool 성공 여부
 */
function safeUpdate($con, $table, $data, $where) {
    $setParts = [];

    foreach ($data as $column => $value) {
        if ($value === null) {
            $setParts[] = "$column = NULL";
        } else {
            $escapedValue = mysqli_real_escape_string($con, $value);
            $setParts[] = "$column = '$escapedValue'";
        }
    }

    $sql = "UPDATE " . $table . " SET " . implode(", ", $setParts) . " WHERE " . $where;

    return executeQuery($con, $sql) !== false;
}

/**
 * UTF-8 검증: 데이터가 올바른 UTF-8인지 확인
 *
 * @param mysqli $con 연결 객체
 * @param string $table 테이블명
 * @param string $column 컬럼명
 * @param int $id 레코드 ID
 * @return array ['is_valid' => bool, 'hex' => string, 'value' => string]
 */
function validateUTF8($con, $table, $column, $id) {
    $sql = "SELECT $column, HEX($column) as hex_value FROM $table WHERE {$table}_id = $id";
    $result = executeQuery($con, $sql);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        $hex = $row['hex_value'];
        $value = $row[$column];

        // UTF-8 한글은 EC, ED, EA, EB 등으로 시작
        $isValid = preg_match('/^(EC|ED|EA|EB|E[0-9A-F])/', $hex) > 0;

        return [
            'is_valid' => $isValid,
            'hex' => $hex,
            'value' => $value
        ];
    }

    return ['is_valid' => false, 'hex' => '', 'value' => ''];
}

/**
 * HTML 출력용 안전한 문자열 변환
 *
 * @param string $value 변환할 값
 * @return string HTML 안전한 문자열
 */
function safeHTML($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

// 에러 리포팅 설정 (개발 환경)
if (!defined('PRODUCTION')) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/php_errors.log');
}
?>
