<?php
	if (!defined('DISPENSER')) exit; // 개별 페이지 접근 불가

    /**
     * 지정된 키($targetKey)에 해당하는 메뉴 아이템의 ID 끝 3자리(Suffix)를 반환하는 함수
     *
     * 이 함수는 다단계로 중첩된 메뉴 배열($menus)을 순회하여,
     * 주어진 키($targetKey)와 일치하는 항목을 찾아내고 해당 항목의 ID 마지막 3자리를 추출합니다.
     *
     * @param array $menus      전체 메뉴 배열
     *                          - 각 메뉴는 'items' 배열을 포함할 수 있음
     *                          - 'items' 배열 내의 요소는 다시 'items' 배열을 가질 수 있음 (2단계 중첩 구조)
     * @param string $targetKey 찾고자 하는 대상 아이템의 key 값
     *
     * @return string|null      대상 아이템을 찾았을 경우 ID 끝 3자리 문자열을 반환,
     *                          찾지 못한 경우 null 반환
     *
     * @example
     * $menus = [
     *     [
     *         'items' => [
     *             [
     *                 'id' => 'menu001',
     *                 'key' => 'home'
     *             ],
     *             [
     *                 'id' => 'menu002',
     *                 'key' => 'settings',
     *                 'items' => [
     *                     [
     *                         'id' => 'sub003',
     *                         'key' => 'profile'
     *                     ]
     *                 ]
     *             ]
     *         ]
     *     ]
     * ];
     *
     * // 'profile'의 id는 'sub003' 이므로 끝 3자리 '003' 반환
     * $result = findIdSuffixByKey($menus, 'profile');
     *
     * // 결과: "003"
     *
     * @note
     * - 최대 2단계 중첩된 'items'만 탐색합니다.
     * - 탐색 과정에서 일치하는 key를 찾으면 즉시 반환합니다.
     * - 일치하는 key가 전혀 없으면 null을 반환합니다.
     */
	function findIdSuffixByKey(array $menus, string $targetKey, int $len = 3): ?string
	{
		foreach ($menus as $node) {
			// 현재 노드가 키를 갖고 있고 일치하면 즉시 반환
			if (isset($node['key']) && $node['key'] === $targetKey && isset($node['id'])) {
				// id 끝의 숫자에서 마지막 $len자리 추출
				if (preg_match('/(\d+)$/', (string)$node['id'], $m)) {
					$digits = $m[1];
					return substr($digits, -$len);
				}
				return null;
			}

			// 하위가 있으면 재귀 탐색
			if (!empty($node['items']) && is_array($node['items'])) {
				$found = findIdSuffixByKey($node['items'], $targetKey, $len);
				if ($found !== null) {
					return $found;
				}
			}
		}

		return null;
	}


    /**
     * 특정 사원의 메뉴 권한 배열을 반환하는 함수
     *
     * 이 함수는 메뉴 구조($menus)에서 대상 키($targetKey)에 해당하는 메뉴 항목을 찾아
     * 그 ID의 마지막 3자리(Suffix)를 추출한 후, 전역 데이터($GLOBAL_DATA)에서
     * 해당 사원($employeeNo)의 권한 배열을 가져옵니다.  
     * 개인 권한이 없을 경우 기본 권한(default)을 반환하며, 둘 다 없으면 빈 배열을 반환합니다.
     *
     * @param array  $GLOBAL_DATA 권한 정보가 담긴 전역 데이터 배열
     *                            - 구조 예시:
     *                              $GLOBAL_DATA['approval'][사원번호][ID Suffix] = 권한배열
     *                              $GLOBAL_DATA['approval']['default'][ID Suffix] = 기본권한배열
     * @param string $employeeNo  권한을 조회할 대상 사원의 번호
     * @param array  $menus       전체 메뉴 배열 (findIdSuffixByKey() 함수 탐색에 사용)
     * @param string $targetKey   권한을 확인할 대상 메뉴의 key 값
     *
     * @return array|null         - 사원별 권한 배열을 반환
     *                            - 해당 메뉴가 존재하지 않으면 null 반환
     *                            - 사원별/기본 권한 모두 없으면 빈 배열 반환
     *
     * @example
     * $GLOBAL_DATA = [
     *     'approval' => [
     *         'E001' => [
     *             '001' => ['read' => true, 'write' => false]
     *         ],
     *         'default' => [
     *             '001' => ['read' => true, 'write' => true]
     *         ]
     *     ]
     * ];
     *
     * $menus = [
     *     [
     *         'items' => [
     *             ['id' => 'menu001', 'key' => 'dashboard']
     *         ]
     *     ]
     * ];
     *
     * // 'dashboard' 메뉴 ID는 menu001 → suffix "001"
     * // E001 사원의 개인권한이 존재하므로 해당 배열 반환
     * $result = getPermissionArray($GLOBAL_DATA, 'E001', $menus, 'dashboard');
     * // 결과: ['read' => true, 'write' => false]
     *
     * @note
     * - findIdSuffixByKey() 함수에 의존합니다. (ID 마지막 3자리 추출)
     * - 개인 권한 우선 → 없으면 default 권한 → 그것도 없으면 빈 배열 반환.
     * - 메뉴에서 대상 key를 찾지 못한 경우 null 반환.
     */
    function getPermissionArray($GLOBAL_DATA, $employeeNo, $menus, $targetKey) {
        // 1) 메뉴에서 ID의 마지막 3자리 추출
        $idSuffix = findIdSuffixByKey($menus, $targetKey);
        if ($idSuffix === null) return null;

        // 2) $GLOBAL_DATA 에서 해당 사원번호 / 해당 3자리 권한 찾기
        if (isset($GLOBAL_DATA['approval'][$employeeNo][$idSuffix])) {
            return $GLOBAL_DATA['approval'][$employeeNo][$idSuffix];
        }

        // 3) 개인권한 없으면 default 반환
        if (isset($GLOBAL_DATA['approval']['default'][$idSuffix])) {
            return $GLOBAL_DATA['approval']['default'][$idSuffix];
        }

        // 4) 둘 다 없으면 빈 배열
        return [];
    }


    /// <summary>
    /// 특정 직원이 주어진 메뉴 키와 작업(Action)에 대해 권한을 가지고 있는지 확인합니다.
    /// </summary>
    /// <param name="$employeeNo">
    /// 직원 번호. 권한을 확인할 대상 직원의 고유 번호입니다.
    /// </param>
    /// <param name="$targetKey">
    /// 권한을 확인할 메뉴의 고유 키 값입니다. 
    /// 이 값은 메뉴 배열($menus)에서 ID 접미어(suffix)를 찾는 데 사용됩니다.
    /// </param>
    /// <param name="$action">
    /// 확인할 작업(Action) 이름입니다. 예: "read", "write", "approve" 등.
    /// </param>
    /// <returns>
    /// 직원이 해당 작업에 대한 권한을 가지고 있으면 true, 
    /// 그렇지 않으면 false를 반환합니다.
    /// </returns>
    /// <remarks>
    /// 권한 확인 순서:
    /// 1. 메뉴 키($targetKey)에 해당하는 ID 접미어를 검색합니다. (없으면 false 반환)
    /// 2. 직원 개인에게 부여된 권한($GLOBAL_DATA['approval'][$employeeNo])이 있으면 우선 확인합니다.
    /// 3. 개인 권한이 없을 경우, 시스템에 설정된 기본 권한($GLOBAL_DATA['approval']['default'])을 확인합니다.
    /// 4. 권한 정보가 전혀 없을 경우 false를 반환합니다.
    /// 
    /// 예외 상황:
    /// - $targetKey가 메뉴 배열에 존재하지 않거나 ID 접미어를 찾지 못한 경우: false 반환.
    /// - $GLOBAL_DATA 구조가 예상과 다를 경우 PHP Notice 또는 Warning 발생 가능.
    /// </remarks>
    function hasPermission($employeeNo, $targetKey, $action) {
        global $GLOBAL_DATA, $menus;

        // 메뉴에서 ID의 마지막 3자리 찾기
        $idSuffix = findIdSuffixByKey($menus, $targetKey);
        if ($idSuffix === null) return false;

        // 직원 개별 권한 먼저 확인
        if (isset($GLOBAL_DATA['approval'][$employeeNo][$idSuffix][$action])) {
            return $GLOBAL_DATA['approval'][$employeeNo][$idSuffix][$action] === "1";
        }

        // default 권한 확인
        if (isset($GLOBAL_DATA['approval']['default'][$idSuffix][$action])) {
            return $GLOBAL_DATA['approval']['default'][$idSuffix][$action] === "1";
        }

        // 권한 없으면 false
        return false;
    }

	function permissionAttr($action, $temName = '') {
		global $mb_no, $menuName;

		// temName 우선, 없으면 전역 menuName 사용
		$target = $temName ?: $menuName;

		if (hasPermission($mb_no, $target, $action)) {
			return ""; // 권한 있으면 버튼 활성
		}
		return "disabled"; // 권한 없으면 비활성
	}

?>