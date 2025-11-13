<?php
	if (!defined('DISPENSER')) exit; // 개별 페이지 접근 불가

	/**
	 * <summary>
	 * 지정한 디렉토리 내 모든 파일의 전체 경로를 재귀적으로 수집하여 배열로 반환합니다.
	 * </summary>
	 * <param name="dir">검색할 디렉토리 경로</param>
	 * <returns>디렉토리 내 모든 파일의 절대 경로 배열</returns>
	 */
	function getAllFiles($dir) {
		$result = [];
		if (!is_dir($dir)) return $result;

		$items = scandir($dir);
		foreach ($items as $item) {
			if ($item === '.' || $item === '..') continue;

			$path = $dir.DIRECTORY_SEPARATOR.$item;
			if (is_dir($path)) {
				// 하위 디렉토리도 재귀적으로 탐색
				$result = array_merge($result, getAllFiles($path));
			} else {
				// 파일 경로 추가
				$result[] = $path;
			}
		}
		return $result;
	}


	/**
	 * <summary>
	 * 안전하게 파일을 이동합니다. 
	 * 동일한 디스크 볼륨 내에서는 rename을 시도하고,
	 * 실패할 경우(예: 다른 볼륨 간 이동) copy 후 원본 파일을 삭제합니다.
	 * </summary>
	 * <param name="string $src">
	 * 이동할 원본 파일 경로.
	 * </param>
	 * <param name="string $dst">
	 * 이동 대상 파일 경로.
	 * </param>
	 * <returns type="bool">
	 * 파일 이동에 성공하면 <c>true</c>, 실패하면 <c>false</c>를 반환합니다.
	 * </returns>
	 * <remarks>
	 * - rename 함수가 실패하면 copy와 unlink 조합으로 이동을 시도합니다.
	 * - 에러 억제를 위해 @ 연산자를 사용하지만, 실제 사용 시 에러 처리를 권장합니다.
	 * </remarks>
	 */
	function safeMoveFile(string $src, string $dst): bool
	{
		// 같은 볼륨일 경우 rename 시도
		if (@rename($src, $dst)) {
			return true;
		}

		// rename 실패 시 copy 후 원본 삭제 시도
		if (@copy($src, $dst)) {
			@unlink($src);
			return true;
		}

		// 모두 실패 시 false 반환
		return false;
	}


	/**
	 * <summary>
	 * 현재 서버 환경을 기준으로 지정한 디렉토리의 상대 URL 경로를 반환합니다.
	 * 반환 경로는 웹 루트 기준 상대경로이며, 기본값은 현재 디렉토리(__DIR__)입니다.
	 * </summary>
	 * <param name="dir">경로 계산에 사용할 디렉토리 (기본값: 현재 파일 위치)</param>
	 * <returns>웹 루트 기준 상대 URL 경로 문자열 (예: ../../erp/)</returns>
	 */
	function pathUrl($dir = __DIR__)
	{
		// 현재 디렉토리 경로를 실 경로로 변환 후 슬래시 통일
		$dir = str_replace('\\', '/', realpath($dir));

		// 프로토콜 결정 (HTTPS가 설정되어 있으면 https, 아니면 http)
		$protocol = (!empty($_SERVER['HTTPS'])) ? 'https' : 'http';

		// 호스트명 가져오기
		$host = $_SERVER['HTTP_HOST'];

		// 웹 루트 URL 생성 (예: http://example.com)
		$webRoot = $protocol.'://'.$host;

		// 웹 서버 환경에 따라 웹 루트 경로 계산
		if (!empty($_SERVER['CONTEXT_PREFIX'])) {
			$basePath = $_SERVER['CONTEXT_PREFIX'].substr($dir, strlen($_SERVER['CONTEXT_DOCUMENT_ROOT']));
		} else {
			$basePath = substr($dir, strlen($_SERVER['DOCUMENT_ROOT']));
		}

		// basePath에서 앞뒤 슬래시 제거 후 깊이(폴더 개수) 계산
		$rootPath = $basePath;
		$rootDepth = substr_count(trim($rootPath, '/'), '/');

		// 상대경로 생성 (웹 루트로 올라가는 ../ 개수)
		$relativePath = str_repeat('../', $rootDepth);

		// 상대경로 뒤에 erp/ 경로를 붙여 최종 반환
		return $relativePath."erp/";
	}



?>