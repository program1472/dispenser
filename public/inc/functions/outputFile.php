<?php
	if (!defined('DISPENSER')) exit; // 개별 페이지 접근 불가
	/**
	 * <summary>
	 * 지정한 파일을 HTTP 다운로드 응답으로 출력합니다.
	 * 파일이 존재하지 않으면 404 상태코드와 메시지를 출력 후 종료합니다.
	 * 한글 파일명 처리 및 캐시 방지 헤더를 포함합니다.
	 * </summary>
	 * <param name="filePath">다운로드할 파일의 전체 경로</param>
	 * <param name="encodedFileName">URL 인코딩된 파일명 (없으면 자동 생성)</param>
	 */
	function outputFile($filePath, $encodedFileName = null) {
		if (!file_exists($filePath)) {
			http_response_code(404);
			exit('파일을 찾을 수 없습니다.');
		}

		// 파일명 추출 (경로 구분자 '/' 기준)
		$fileName = explode('/', $filePath);
		$fileName = end($fileName);

		// MIME 타입 결정 (파일 확장자 기반)
		$mimeType = mime_content_type($filePath);

		// 한글 파일명 인코딩 처리
		if (empty($encodedFileName)) {
			$encodedFileName = rawurlencode($fileName);
			// +를 %20으로 변환 (공백 처리)
			$encodedFileName = str_replace("+", "%20", $encodedFileName);
		}

		// 캐시 방지 헤더 설정
		header('Cache-Control: no-cache, no-store, must-revalidate');
		header('Pragma: no-cache');
		header('Expires: 0');

		// 다운로드용 HTTP 헤더 설정
		header('Content-Type: '.$mimeType);
		header('Content-Disposition: attachment; filename="'.$encodedFileName.'"; filename*=UTF-8\'\''.$encodedFileName);

		// 파일 내용 출력
		readfile($filePath);
		exit;
	}

?>