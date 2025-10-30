<?php
	if (!defined('DISPENSER')) exit; // 개별 페이지 접근 불가

	require_once ROOT."/dbconfig.php";
	$database_name = IS_DEBUG ? "dispenser" : "dispenser";
	mysqli_select_db($con, $database_name);


	/**
	 * <summary>
	 * MySQLi 쿼리 결과에서 모든 행을 연관 배열로 가져오고,
	 * 각 필드가 JSON 문자열인 경우 연관 배열로 디코딩하여 반환합니다.
	 * </summary>
	 * <param name="result">MySQLi 쿼리 결과 리소스</param>
	 * <returns>모든 행의 배열 (각 행은 연관 배열, JSON 필드는 배열로 변환됨)</returns>
	 */
	function fetchResultArray($result) {
		$data = [];
		if ($result && mysqli_num_rows($result) > 0) {
			while ($row = mysqli_fetch_assoc($result)) {
				foreach ($row as $key => $value) {
					// JSON 형식인지 확인 후 디코딩 (true는 연관 배열로 디코딩)
					$decoded = json_decode($value, true);
					if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
						$row[$key] = $decoded;
					}
				}
				$data[] = $row;
			}
		}
		return $data;
	}


	/**
	 * Executes a SQL query and returns the result.
	 *
	 * - SELECT 쿼리일 경우, 결과를 연관 배열로 반환합니다.
	 * - INSERT 쿼리일 경우, 새로 생성된 AUTO_INCREMENT ID를 반환합니다.
	 * - INSERT ID가 0이고 $uid가 0보다 큰 숫자일 경우, $tb_nm 테이블에서 해당 uid의 데이터를 반환합니다.
	 * - UPDATE, DELETE 등 기타 쿼리 성공 시 true 반환.
	 * - 쿼리 실패 시 false 반환.
	 *
	 * @param string $sql 실행할 SQL 쿼리문
	 * @param int $uid (선택) INSERT ID가 0일 경우 조회할 uid 값, 기본 0
	 * @return mixed SELECT 결과 배열 | INSERT ID (int) | true | false
	 */
	function query($sql, $uid = 0) {
		global $con, $tb_nm;

		$result = mysqli_query($con, $sql);

		if ($result === false) {
			return false;
		}

		if (is_object($result)) {
			$rows = fetchResultArray($result);
			mysqli_free_result($result);
			return $rows;
		}

		$insertId = mysqli_insert_id($con);
		// insertId가 0보다 크면 insertId 사용, 아니면 uid 사용
		$id = 0;
		if ($insertId > 0) {
			$id = intval($insertId);
		} elseif (is_numeric($uid) && $uid > 0) {
			$id = intval($uid);
		}

		if ($id > 0) {
			$id = mysqli_real_escape_string($con, $id);
			$sql2 = "SELECT * FROM `$tb_nm` WHERE `uid` = $id LIMIT 1";
			$res = mysqli_query($con, $sql2);
			if ($res && mysqli_num_rows($res) > 0) {
				$data = mysqli_fetch_assoc($res);
				mysqli_free_result($res);
				return $data;
			}
			return null;
		}

		return true;
	}


	/**
	 * 전역 MySQLi 커넥션을 사용해 문자열을 안전하게 이스케이프합니다.
	 *
	 * @param string $str 이스케이프할 원본 문자열
	 * @return string 이스케이프된 문자열
	 */
	function escapeString(string $str): string {
		global $con;
		return mysqli_real_escape_string($con, $str);
	}

?>