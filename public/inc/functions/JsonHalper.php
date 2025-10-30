<?php
	if (!defined('DISPENSER')) exit; // 개별 페이지 접근 불가

	/**
	 * 주어진 값이 JSON 텍스트 형식인지 검사합니다.
	 *
	 * @param mixed $string 검사할 값
	 * @return bool JSON 형식이면 true, 아니면 false
	 */
	function isJsonString($string): bool {
		if (!is_string($string)) {
			return false;
		}
		json_decode($string);
		return (json_last_error() === JSON_ERROR_NONE && is_array($decoded));
	}


	/**
	 * 다차원 배열에서 값이 true인 경로를 문자열로 반환하는 함수입니다.
	 * 
	 * 각 경로는 키를 ' > ' 구분자로 연결한 문자열 형태이며,
	 * 배열 내에서 최하위 요소가 true인 경우에만 해당 경로가 결과에 포함됩니다.
	 * 
	 * @param array $array true 값을 포함하는 경로를 찾을 다차원 배열입니다.
	 * @param string $prefix 현재까지 누적된 키 경로 문자열입니다. 재귀 호출 시 내부에서 사용됩니다.
	 * 
	 * @return string[] true 값을 가진 최하위 요소까지의 경로 문자열 배열을 반환합니다.
	 * 
	 * @remarks
	 * - 입력값이 배열이 아닌 경우 빈 배열을 반환합니다.
	 * - 값이 true인 경우만 경로에 포함하며, 값이 빈 문자열("") 등 다른 값인 경우 제외됩니다.
	 * - 각 경로는 상위 키부터 하위 키까지 ' > '로 연결된 형태입니다.
	 * 
	 * @example
	 * ```php
	 * $data = [
	 *   "A" => [
	 *     "B" => true,
	 *     "C" => false,
	 *     "D" => [
	 *       "E" => true,
	 *       "F" => ""
	 *     ]
	 *   ],
	 *   "G" => true
	 * ];
	 * $paths = getTruePaths($data);
	 * // 결과: ["A > B", "A > D > E", "G"]
	 * ```
	 */
	function getTruePaths($array, $prefix = '') {
		// 배열이 아니면 빈 배열 반환
		if (!is_array($array)) return [];

		$result = [];
		foreach ($array as $k => $v) {
			// 현재까지의 경로 문자열 생성
			$current = ($prefix === '' ? $k : $prefix.' > '.$k);

			if (is_array($v)) {
				// 값이 배열이면 재귀 호출로 하위 경로 탐색 후 결과 병합
				$result = array_merge($result, getTruePaths($v, $current));
			} else {
				// 최하위 요소가 true인 경우에만 경로에 추가
				if ($v === true) $result[] = $current;
			}
		}

		return $result;
	}


	/**
	 * <summary>
	 * Finalizes the global response payload by removing any keys that are not explicitly allowed,
	 * unless the application is running in debug mode.
	 * </summary>
	 * <remarks>
	 * This function relies on the global <c>$response</c> array. When <c>IS_DEBUG</c> is false,
	 * it will filter <c>$response</c> so that only the keys
	 * <see langword="item"/>, <see langword="items"/>, <see langword="html"/>,
	 * <see langword="result"/>, <see langword="pagination"/>, and <see langword="dev"/>
	 * remain. This helps ensure that no unintended data is exposed in production.
	 * </remarks>
	 * <returns>
	 * <see langword="void"/>. The filtered array is assigned directly back to the global <c>$response</c>.
	 * </returns>
	 */
	function Finish(): void {
		// Bring the global response array into local scope
		global $response, $_SESSION;
		$response['SESSION'] = $_SESSION;
		header('Content-Type: application/json; charset=utf-8');
		// Only apply filtering when not in debug mode
		if (!IS_DEBUG) {
			// Define the list of keys that are safe to require in production
			$allowed = ['item', 'items', 'html', 'result', 'pagination', 'dev', 'message', 'events', 'totalCount', 'table_array', 'error', 'approval', 'M1', 'PL', 'PR', 'T2', 'T3', 'T4', 'T5', 'T6', 'img', 'ip', 'mb_id', 'tags'];
			// Filter $response to keep only the allowed keys:
			// 1. array_flip($allowed) creates an associative array ['item'=>0, 'items'=>1, ...]
			// 2. array_intersect_key retains only entries in $response whose keys exist in that flipped array
			$response = array_filter($response, function($v, $k) use($allowed) {return in_array($k, $allowed, true) || preg_match('/^[A-Za-z]\d{2}$/', $k); }, ARRAY_FILTER_USE_BOTH);
		}
		$converted = json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
		$converted = replaceDomain($converted);
		echo $converted;
		exit();
	}


	/**
	 * 값에 따라 true, false 또는 원본 값을 반환하는 함수
	 *
	 * @param mixed $value 검사할 값
	 * @return bool|mixed true, false, 또는 원본 값
	 */
	function parseBoolOrValue($value) {
		$trueValues = [1, true, 'true', '1'];
		$falseValues = [0, false, 'false', '0'];

		if (in_array($value, $trueValues, true)) {
			return true;
		}
		if (in_array($value, $falseValues, true)) {
			return false;
		}
		return $value;
	}

    /// <summary>
    /// 지정된 키가 주어진 배열에서 유효한지 여부를 확인합니다.
    /// </summary>
    /// <param name="$key">
    /// 검사할 대상 키 값입니다.
    /// </param>
    /// <param name="$hadder_info">
    /// 키와 관련된 정보를 담고 있는 연관 배열입니다.  
    /// 예: [ 'menu1' => ['enabled' => true], 'menu2' => ['enabled' => false] ].
    /// </param>
    /// <param name="$mode">
    /// 검사 모드:
    /// <list type="bullet">
    /// <item>
    /// <term>"exists"</term>
    /// <description>키가 배열에 존재하는지만 확인합니다. (기본값)</description>
    /// </item>
    /// <item>
    /// <term>"enabled"</term>
    /// <description>키가 존재하고, 해당 키의 'enabled' 속성이 비어 있지 않은 경우만 true를 반환합니다.</description>
    /// </item>
    /// </list>
    /// </param>
    /// <returns>
    /// 지정된 조건을 만족하면 true, 그렇지 않으면 false를 반환합니다.
    /// </returns>
    /// <remarks>
    /// - mode가 "enabled"일 경우, 단순 존재 여부를 넘어 실제 사용 가능한 상태인지까지 확인합니다. 
    /// - "exists" 모드에서는 단순히 배열에 키가 존재하는지만 검사합니다.  
    /// - 예상치 못한 mode 값이 들어오면 기본적으로 "exists" 모드로 처리됩니다.
    /// </remarks>
    function isAllowedKey(string $key, array $hadder_info, string $mode = 'exists'): bool {
        if ($mode === 'enabled') {
            // 키가 존재하고, 'enabled' 값이 비어있지 않은 경우만 허용
            return isset($hadder_info[$key]) && !empty($hadder_info[$key]['enabled']);
        }
        // 기본: 키 존재 여부만 확인
        return isset($hadder_info[$key]);
    }


?>