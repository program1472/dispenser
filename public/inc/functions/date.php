<?php
	if (!defined('DISPENSER')) exit; // 개별 페이지 접근 불가

	/**
	 * <summary>
	 * 현재 마이크로초 단위 시간값에 기반한 32비트 CRC 해시 코드를 생성합니다.
	 * </summary>
	 * <returns>32비트 정수 해시 코드</returns>
	 */
	function getNowTicksHashCode() {
		$ticks = microtime(true) * 10000000;
		return crc32((string)$ticks); // 32비트 int 해시 반환
	}

	/**
	 * <summary>
	 * 주어진 문자열에서 날짜(YYYY-MM-DD) 부분만 추출하여 반환합니다.
	 * 입력값이 비어있거나 날짜 형식이 아니면 빈 문자열을 반환합니다.
	 * </summary>
	 * <param name="datetime">날짜 및 시간 정보가 포함된 문자열</param>
	 * <returns>YYYY-MM-DD 형식의 날짜 문자열 또는 빈 문자열</returns>
	 */
	function getDateOnly($datetime) {
		if (empty($datetime)) return ''; // 값이 비어있을 때

		// 이미 YYYY-MM-DD 형식이면 그대로 반환
		if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $datetime)) {
			return $datetime;
		}

		// 공백 또는 'T' 구분자로 앞부분(날짜)만 추출
		$date = preg_split('/[ T]/', $datetime)[0];

		// 추출한 부분이 YYYY-MM-DD 형식인지 확인 후 반환, 아니면 빈 문자열
		if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
			return $date;
		} else {
			return '';
		}
	}

	/**
	 * 날짜 형식 검사 함수
	 *
	 * 지정한 포맷($format)에 맞는 날짜 문자열인지 확인합니다.
	 *
	 * @param string $date 검사할 날짜 문자열
	 * @param string $format 날짜 포맷 (기본값: 'Y-m-d')
	 * @return bool 포맷에 맞는 올바른 날짜면 true, 아니면 false
	 */
	function is_date($date, $format = 'Y-m-d') {
		$d = DateTime::createFromFormat($format, $date);
		return $d && $d->format($format) === $date;
	}

?>