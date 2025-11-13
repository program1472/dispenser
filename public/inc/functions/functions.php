<?php

	/**
	 * <summary>
	 * 숫자 문자열을 동적으로 포맷팅하여 소수점 이하 자릿수를 유지하며 천 단위 구분기호(콤마)를 추가합니다.
	 * 소수점이 없으면 정수 형식으로 콤마만 추가됩니다.
	 * </summary>
	 * <param name="number">포맷팅할 숫자(문자열 또는 숫자)</param>
	 * <returns>콤마가 포함된 형식화된 숫자 문자열</returns>
	 */
	function format_price_dynamic($value) {
		if ($value === null || $value === '') return '';

		// 문자열로 정규화(천단위 콤마 제거)
		$raw = trim((string)$value);
		$clean = str_replace(',', '', $raw);

		// 숫자(부호/소수점)만 허용
		if (!preg_match('/^([+-]?)(\d+)(?:\.(\d+))?$/', $clean, $m)) return '';

		$sign = $m[1] ?? '';
		$int  = $m[2] ?? '0';
		$dec  = $m[3] ?? '';

		// 정수부 앞의 0 제거(모두 0이면 '0' 유지)
		$int = ltrim($int, '0');
		if ($int === '') $int = '0';

		// 정수부 천단위 구분
		$int_fmt = preg_replace('/\B(?=(\d{3})+(?!\d))/', ',', $int);

		// 소수부가 없거나 '000...'(전부 0)이면 소수점 생략
		if ($dec === '' || preg_match('/^0+$/', $dec)) {
			return $sign . $int_fmt;
		}

		// 소수부 존재하고 0이 아닌 숫자가 포함되어 있으면 그대로 표시
		return $sign . $int_fmt . '.' . $dec;
	}


	/**
	 * <summary>
	 * 숫자를 한글 금액 표기법으로 변환합니다.
	 * 예: 12345 → "일만이천삼백사십오"
	 * </summary>
	 * <param name="number">변환할 숫자 (문자열 또는 정수)</param>
	 * <returns>한글로 변환된 숫자 문자열</returns>
	 */
	function numberToKorean($number) {
		$hanA = ["", "일", "이", "삼", "사", "오", "육", "칠", "팔", "구", "십"];
		$danA = ["", "십", "백", "천", "만", "십", "백", "천", "억", "십", "백", "천", "조"];

		// 쉼표 제거
		$number = str_replace(',', '', $number);
		// 문자열을 뒤집고 배열로 변환
		$arr = str_split(strrev((string)$number));
		$txt = '';

		for ($i = 0; $i < count($arr); $i++) {
			if ($arr[$i] != 0) {
				$txt = $hanA[$arr[$i]].$danA[$i].$txt;
			}
		}

		return $txt;
	}


	/**
	 * <summary>
	 * 문자열 내 특정 도메인 및 플레이스홀더 "《HOST》"를 상대경로 "../../"로 치환합니다.
	 * 주로 절대 URL을 상대 경로로 변환할 때 사용합니다.
	 * </summary>
	 * <param name="value">치환 대상 문자열</param>
	 * <returns>상대경로로 치환된 문자열</returns>
	 */
	function replaceDomain($value) {
		/*$value = str_replace("http://atg-cas.iptime.org/", "../../", $value);
		$value = str_replace("http://atg-cas.iptime.org", "../..", $value);
		$value = str_replace("http://220.70.12.230/", "../../", $value);
		$value = str_replace("http://220.70.12.230", "../..", $value);
		$value = str_replace("http://oilpick.co.kr/", "../../", $value);
		$value = str_replace("http://oilpick.co.kr", "../..", $value);*/
		$value = str_replace("《HOST》", "../..", $value);
		return $value;
	}

	/**
	 * <summary>
	 * 문자열 내 특정 도메인 및 경로들을 플레이스홀더 "《HOST》"로 치환하여
	 * 호스트 정보의 일관성을 유지하도록 합니다.
	 * 상대경로 "../../"도 동일하게 치환합니다.
	 * </summary>
	 * <param name="value">치환 대상 문자열</param>
	 * <returns>호스트 플레이스홀더로 치환된 문자열</returns>
	 */
	function restoreDomain($value) {
		/*$value = str_replace("http://atg-cas.iptime.org/", "《HOST》/", $value);
		$value = str_replace("http://atg-cas.iptime.org", "《HOST》", $value);
		$value = str_replace("http://220.70.12.230/", "《HOST》/", $value);
		$value = str_replace("http://220.70.12.230", "《HOST》", $value);
		$value = str_replace("http://oilpick.co.kr/", "《HOST》/", $value);
		$value = str_replace("http://oilpick.co.kr", "《HOST》", $value);*/
		$value = str_replace("../../", "《HOST》/", $value);
		return $value;
	}


	// 랜덤 5자리 영문+숫자 생성
	// 첫글자: 영문(소문자+대문자)
	$first = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 1);
	// 나머지 4자리: 영문+숫자
	$rest = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4);
	// 합치기
	$rand = $first.$rest;

	require_once INC_ROOT."/menus.php";


	/**
	 * 배열 안에서 값이 data:…;base64, 로 시작하는 항목만 빈 문자열로 치환
	 *
	 * @param mixed &$data 배열 또는 스칼라 값
	 */
	function clearBase64Values(&$data) {
		if (is_array($data)) {
			// 배열이면 각 요소를 재귀 호출
			foreach ($data as &$value) {
				clearBase64Values($value);
			}
			unset($value);  // foreach 레퍼런스 해제
		}
		elseif (is_string($data)) {
			// 값이 Base64 데이터 URL 패턴일 때만 '' 로 치환
			if (preg_match('#^data:[^;]+;base64,#i', $data)) {
				$data = 'data:*/*;base64,...';
			}
		}
	}

	/**
	 * 경고 메시지를 띄운 뒤 창을 닫습니다.
	 */
	function alertAndClose($msg = '')
	{
		// <br> 태그만 허용
		$msg = $msg
			? strip_tags($msg, '<br>')
			: '올바른 방법으로 이용해 주십시오.';

		// PHP 종료 태그를 열어서 바로 HTML/JS 출력
		?>
		<script type="text/javascript">
			alert("<?= addslashes($msg) ?>");
			// 팝업창이 아닌 경우에도 강제 닫기 시도
			window.opener = null;
			window.open('', '_self');
			window.close();
		</script>
		<?php
		exit;
	}


	// ——————————————————————————
	// 단위 변환 함수
	// ——————————————————————————
	/**
	 * mm 단위를 픽셀(px)로 변환
	 *
	 * @param float $mm  밀리미터
	 * @param int   $dpi DPI
	 * @return int 픽셀 값
	 */
	function mm2px(float $mm, int $dpi = 300): int {
		return (int) round($mm * $dpi / 25.4);
	}

	/**
	 * pt 단위를 픽셀(px)로 변환
	 *
	 * @param int $pt  포인트
	 * @param int $dpi DPI
	 * @return float 픽셀 값
	 */
	function pt2px(int $pt, int $dpi): int {
		return (int) round($pt * $dpi / 72);
	}


	// LOG 함수 재정의: "LOG:메시지\n" 형태로 echo + flush
	if (!function_exists('AjaxLOG')){
		/**
		 * 스트리밍용 LOG: 한 줄씩 내보내고 즉시 flush
		 */
		function AjaxLOG(string $msg, int $level = 0, int $exit = 0) {
			echo "LOG: {$msg}\n";
			@ob_flush();
			@flush();
			if ($exit) {exit;}
		}
	}

	require_once INC_ROOT."/ini.php";

	if (!isset($_SESSION['secretKeyBase64'])) {
		$_SESSION['secretKeyBase64'] = base64_encode(random_bytes(16));
	}
	$secretKey       = base64_decode($_SESSION['secretKeyBase64']);
	$secretKeyBase64 = $_SESSION['secretKeyBase64'];
	// 2) IV는 여기선 고정 16바이트 0 으로 사용 (실서비스에선 매번 무작위 IV 추천)
	$iv = str_repeat("\0", openssl_cipher_iv_length('AES-128-CBC'));

	$pageName = "";
	$response = [
		'SESSION'     => $_SESSION,
		'menus'       => $menus,
		'data'        => [
			'database_name' => $database_name,
		],
		'html'        => null,
		'item'        => null,
		'items'       => null,
		'result'      => false,
		'error'       => ['msg' => '', 'code' =>0],
		'events'      => null,
		'totalCount'  => 0,
		'approval'    => null,
		'pagination'  => null,
		'table_array' => null,
	];

	require_once "famousSaying.php";


?>