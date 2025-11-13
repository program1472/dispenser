<?php
if (!defined('DISPENSER')) exit; // 개별 페이지 접근 불가

/**
 * <summary>
 * PHP 스크립트 실행 시간을 무제한으로 설정합니다.
 * 기본적으로 PHP는 실행 시간이 제한되어 있으나, 0으로 설정 시 제한이 해제됩니다.
 * </summary>
 */
set_time_limit(0);

/**
 * <summary>
 * 쿠키를 저장하고 읽어올 파일의 절대 경로입니다.
 * __DIR__ 상수로 현재 스크립트 위치를 기준으로 cookie.txt 파일을 지정합니다.
 * </summary>
 */
$cookieFile = __DIR__ . '/cookie.txt'; // 쿠키 저장할 파일 경로

/**
 * <summary>
 * HTTP 요청 시 사용되는 쿠키 문자열을 저장하는 전역 변수입니다.
 * 주로 curlWithCookie 함수에서 요청 헤더에 포함되어 서버와 상태를 유지합니다.
 * </summary>
 */
$cookie = "";

/**
 * <summary>
 * 지정한 쿠키 파일이 존재하면 삭제하고, 새로 빈 파일을 생성합니다.
 * </summary>
 * <param name="cookieFile">쿠키 파일 경로</param>
 * <returns>파일 삭제 및 생성 성공 시 true, 실패 시 false</returns>
 */
function resetCookieFile(string $cookieFile): bool
{
    // 쿠키 파일이 존재하는 경우 삭제 시도
    if (file_exists($cookieFile)) {
        if (!unlink($cookieFile)) {
            // 삭제 실패하면 false 반환
            return false;
        }
    }

    // 빈 파일을 생성하거나 수정 시간 갱신 (성공 시 true 반환)
    return touch($cookieFile);
}

/**
 * <summary>
 * 문자열 내 특수문자를 이스케이프 처리하여 SQL 인젝션 등을 방지합니다.
 * 실제 환경에서는 DB 연결 객체의 escape 메서드를 사용하는 것이 안전합니다.
 * </summary>
 * <param name="val">이스케이프할 문자열</param>
 * <returns>이스케이프 처리된 문자열</returns>
 */
function esc($val) {
    // PHP 내장 함수 addslashes로 특수문자 앞에 백슬래시 추가
    return addslashes($val);
}


/**
 * <summary>
 * 문자열이 특정 문자열(needle)로 시작하는지 여부를 반환합니다.
 * PHP 8 이전 버전에서 str_starts_with 함수가 없을 때 직접 구현한 함수입니다.
 * </summary>
 * <param name="haystack">검색 대상 문자열</param>
 * <param name="needle">시작하는지 확인할 문자열</param>
 * <returns>haystack이 needle로 시작하면 true, 아니면 false</returns>
 */
if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle) {
        // haystack의 시작부터 needle 길이만큼 자른 문자열이 needle과 같은지 비교
        return substr($haystack, 0, strlen($needle)) === $needle;
    }
}

/**
 * <summary>
 * 문자열이 특정 문자열(needle)로 끝나는지 여부를 반환합니다.
 * PHP 8 이전 버전에서 str_ends_with 함수가 없을 때 직접 구현한 함수입니다.
 * </summary>
 * <param name="haystack">검색 대상 문자열</param>
 * <param name="needle">끝에 있는지 확인할 문자열</param>
 * <returns>haystack이 needle로 끝나면 true, 아니면 false</returns>
 */
if (!function_exists('str_ends_with')) {
    function str_ends_with($haystack, $needle) {
        // haystack의 끝에서 needle 길이만큼 자른 문자열이 needle과 같은지 비교
        return substr($haystack, -strlen($needle)) === $needle;
    }
}

/**
 * <summary>
 * 지정된 URL에 대해 쿠키 파일을 사용하여 cURL 요청을 수행합니다.
 * HTTP 헤더를 기본값과 추가 헤더를 병합하여 전송하며, 
 * JSON 응답이면 파싱하여 배열로 반환하고, 그렇지 않으면 HTML 원본을 반환합니다.
 * </summary>
 * <param name="url">요청할 URL</param>
 * <param name="cookieFile">쿠키를 저장하고 읽어올 파일 경로 (기본값: 'cookie.txt')</param>
 * <param name="headersExtra">추가로 포함할 HTTP 헤더 배열</param>
 * <returns>오류 정보와 결과 데이터를 포함하는 배열</returns>
 */
function curlWithCookie($url, $cookieFile = 'cookie.txt', $headersExtra = [], $resetCookie = false): array
{
    global $cookie;

    // 쿠키 파일 초기화 (비워두거나 재설정)
    if ($resetCookie) resetCookieFile($cookieFile);

    // 기본 반환 구조 초기화
    $response = [
        'error' => ['code' => 0, 'msg' => ''],
        'result' => ['type' => 'non', 'data' => null]
    ];

    // URL에서 호스트 파싱
    $parsedUrl = parse_url($url);
    $host = $parsedUrl['host'] ?? '';

    // User-Agent 문자열 정의
    $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36';

    // 기본 HTTP 헤더 설정
    $headers = [
        "Host: {$host}",
        "User-Agent: {$userAgent}",
        "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
        "Accept-Language: ko-KR,ko;q=0.8,en-US;q=0.5,en;q=0.3",
        "Accept-Encoding: gzip, deflate, br, zstd",
        "Connection: keep-alive",
        "Upgrade-Insecure-Requests: 1",
        "Sec-Fetch-Dest: document",
        "Sec-Fetch-Mode: navigate",
        "Sec-Fetch-Site: same-origin",
        "Sec-Fetch-User: ?1",
        "Priority: u=0, i",
        "TE: trailers"
    ];

    // 글로벌 쿠키 변수에 값이 있으면 헤더에 포함
    if (!empty($cookie)) $headers[] = 'Cookie: ' . $cookie;

    // 사용자 지정 추가 헤더 병합
    $headers = array_merge($headers, $headersExtra);

    // cURL 초기화 및 옵션 설정
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,     // 개발 시 SSL 검증 해제, 운영환경에서는 true 권장
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0, // HTTP/2 프로토콜 사용
        CURLOPT_ENCODING => '',               // 인코딩 자동 해제(gzip, br 등)
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_COOKIEFILE => $cookieFile,   // 쿠키 읽기
        CURLOPT_COOKIEJAR => $cookieFile,    // 쿠키 저장
        CURLOPT_TIMEOUT => 20,                // 최대 실행 시간 20초
        CURLOPT_CONNECTTIMEOUT => 10,        // 연결 대기 시간 10초
        CURLOPT_USERAGENT => $userAgent,     // User-Agent 지정
    ]);

    // cURL 실행
    $result = curl_exec($ch);

    // 오류 발생 시 오류 코드 및 메시지 반환
    if (curl_errno($ch)) {
        $response['error']['code'] = curl_errno($ch);
        $response['error']['msg'] = curl_error($ch);
        curl_close($ch);
        return $response;
    }

    curl_close($ch);

    // 응답 문자열 앞뒤 공백 제거
    $trimmed = trim($result);

    // JSON 여부 판단: { } 혹은 [ ] 로 감싸져 있으면 JSON으로 판단
    $isJson = (str_starts_with($trimmed, '{') && str_ends_with($trimmed, '}')) ||
              (str_starts_with($trimmed, '[') && str_ends_with($trimmed, ']'));

    if ($isJson) {
        $decoded = json_decode($trimmed, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $response['result']['type'] = 'json';
            $response['result']['data'] = $decoded;
            return $response;
        }
    }

    // JSON이 아닌 경우 HTML 응답으로 처리
    $response['result']['type'] = 'html';
    $response['result']['data'] = $result;
    return $response;
}

?>