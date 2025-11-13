<?php
if (!defined('DISPENSER')) exit; // 개별 페이지 접근 불가

/**
 * 에러 정보를 생성하거나 바로 출력합니다.
 *
 * @param int  $code     HTTP 상태 코드
 * @param bool $isReturn true면 반환, false면 즉시 출력
 * @return array|string  반환 모드일 때는 배열, 즉시 출력 모드일 땐 JSON 문자열
 */
function sendError(int $code, bool $isReturn = false) {
    // HTTP 상태 코드별 기본 메시지 매핑 (영문 메시지, 한글 주석)
    $messages = [
        // 1xx Informational – 요청이 수신되어 처리 중인 상태
        100 => 'Continue',                // 계속 진행
        101 => 'Switching Protocols',     // 프로토콜 전환
        102 => 'Processing',              // 처리 중
        103 => 'Early Hints',             // 초기 힌트

        // 2xx Success – 요청이 성공적으로 처리된 상태
        200 => 'OK',                      // 요청 성공
        201 => 'Created',                 // 리소스 생성됨
        202 => 'Accepted',                // 요청 수락됨
        203 => 'Non-Authoritative Information', // 권한 없는 정보
        204 => 'No Content',              // 콘텐츠 없음
        205 => 'Reset Content',           // 콘텐츠 재설정
        206 => 'Partial Content',         // 부분 콘텐츠
        207 => 'Multi-Status',            // 다중 상태
        208 => 'Already Reported',         // 이미 보고됨
        226 => 'IM Used',                 // IM 사용됨

        // 3xx Redirection – 다른 URL로 리다이렉션 처리 필요
        300 => 'Multiple Choices',        // 다중 선택
        301 => 'Moved Permanently',       // 영구 이동
        302 => 'Found',                   // 임시 이동
        303 => 'See Other',               // 다른 위치 참조
        304 => 'Not Modified',            // 수정되지 않음
        305 => 'Use Proxy',               // 프록시 사용
        306 => 'Switch Proxy',            // 프록시 전환 (미사용)
        307 => 'Temporary Redirect',      // 임시 리다이렉션
        308 => 'Permanent Redirect',      // 영구 리다이렉션

        // 4xx Client Error – 클라이언트 잘못된 요청 오류
        400 => 'Bad Request',             // 잘못된 요청
        401 => 'Unauthorized',            // 인증 필요
        402 => 'Payment Required',        // 결제 필요
        403 => 'Forbidden',               // 접근 금지
        404 => 'Not Found',               // 리소스 없음
        405 => 'Method Not Allowed',      // 허용되지 않은 메서드
        406 => 'Not Acceptable',          // 허용 불가
        407 => 'Proxy Authentication Required', // 프록시 인증 필요
        408 => 'Request Timeout',         // 요청 시간 초과
        409 => 'Conflict',                // 충돌
        410 => 'Gone',                    // 사라짐
        411 => 'Length Required',         // 길이 필요
        412 => 'Precondition Failed',     // 사전 조건 실패
        413 => 'Payload Too Large',       // 요청 본문이 너무 큼
        414 => 'URI Too Long',            // URI가 너무 김
        415 => 'Unsupported Media Type',  // 지원되지 않는 미디어 타입
        416 => 'Range Not Satisfiable',   // 범위 요청 불가
        417 => 'Expectation Failed',      // 기대 실패
        418 => "I'm a teapot",            // 나는 찻주전자
        421 => 'Misdirected Request',     // 잘못된 요청 대상
        422 => 'Unprocessable Entity',    // 처리할 수 없는 엔티티
        423 => 'Locked',                  // 잠김
        424 => 'Failed Dependency',       // 의존성 실패
        425 => 'Too Early',               // 너무 이른 요청
        426 => 'Upgrade Required',        // 업그레이드 필요
        428 => 'Precondition Required',   // 사전 조건 필요
        429 => 'Too Many Requests',       // 요청 과다
        431 => 'Request Header Fields Too Large', // 헤더 필드 너무 김
        451 => 'Unavailable For Legal Reasons',   // 법적 사유로 사용 불가

        // 5xx Server Error – 서버 내부 오류
        500 => 'Internal Server Error',   // 내부 서버 오류
        501 => 'Not Implemented',         // 구현되지 않음
        502 => 'Bad Gateway',             // 잘못된 게이트웨이
        503 => 'Service Unavailable',     // 서비스 이용 불가
        504 => 'Gateway Timeout',         // 게이트웨이 시간 초과
        505 => 'HTTP Version Not Supported', // HTTP 버전 지원 안함
        506 => 'Variant Also Negotiates', // 변형도 협상함
        507 => 'Insufficient Storage',    // 저장 공간 부족
        508 => 'Loop Detected',           // 무한 루프 감지
        510 => 'Not Extended',            // 확장되지 않음
        511 => 'Network Authentication Required', // 네트워크 인증 필요
    ];

    // 요청된 코드에 맞는 메시지, 없으면 기본 메시지
    $msg = $messages[$code] ?? 'Unknown Error';

    // JSON 응답 데이터 구성
    $response = [
        'error' => [
            'msg'  => $msg,
            'code' => $code,
        ],
    ];

	 // JSON 출력 및 종료
    if ($isReturn) {
		return $response['error'];
	} else {
		http_response_code($code);
		header("HTTP/1.1 {$code} {$msg}");
		header('Content-Type: application/json; charset=utf-8');
		$json = json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		echo $json;
		exit;
	}
}

/**
 * MySQL 에러 메시지를 사용자 친화적인 한글 메시지로 변환합니다.
 *
 * @param string $errorMsg 원본 MySQL 에러 메시지
 * @return string 변환된 사용자 친화적 메시지
 */
function getFriendlyErrorMessage(string $errorMsg): string {
	if (strpos($errorMsg, 'Duplicate entry') !== false && strpos($errorMsg, 'email') !== false) {
		return '이미 사용 중인 이메일입니다. 다른 이메일을 사용해주세요.';
	} elseif (strpos($errorMsg, 'Duplicate entry') !== false) {
		return '중복된 데이터가 있습니다. 다른 값을 입력해주세요.';
	} elseif (strpos($errorMsg, 'Cannot delete or update a parent row') !== false) {
		return '다른 데이터에서 사용 중이므로 삭제할 수 없습니다.';
	} elseif (strpos($errorMsg, 'Data too long') !== false) {
		return '입력한 데이터가 너무 깁니다. 더 짧게 입력해주세요.';
	}

	return $errorMsg;
}

// 사용 예:
// sendError(404); // Not Found – 리소스 없음
// sendError(418); // I'm a teapot – 나는 찻주전자
// $friendlyMsg = getFriendlyErrorMessage($e->getMessage());
?>