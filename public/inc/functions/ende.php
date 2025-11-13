<?php
	if (!defined('DISPENSER')) exit; // 개별 페이지 접근 불가

	require_once "EncryptDecrypt.php";
	$encryptDecrypt = new EncryptDecrypt();

	/**
	 * <summary>
	 * 주어진 값을 암호화하여 반환합니다.
	 * 암호화 결과의 패딩 '=' 문자는 제거합니다.
	 * </summary>
	 * <param name="value">암호화할 문자열</param>
	 * <returns>암호화된 문자열 (패딩 제거)</returns>
	 */
	function encryptValue($value) {
		global $encryptDecrypt; // 외부 암호화 인스턴스 참조
		$enKey = $encryptDecrypt->encrypt($value);
		return rtrim($enKey, '=');
	}


	/**
	 * <summary>
	 * 암호화된 값을 복호화하여 원본 문자열을 반환합니다.
	 * </summary>
	 * <param name="value">복호화할 암호화 문자열</param>
	 * <returns>복호화된 원본 문자열</returns>
	 */
	function decryptValue($value) {
		global $encryptDecrypt;
		return $encryptDecrypt->decrypt($value);
	}


	/**
	 * <summary>
	 * 배열을 재귀적으로 순회하면서, 키와 값을 복호화합니다.
	 * 값이 JSON 문자열이면 배열로 디코딩 후 재귀적으로 복호화하며,
	 * 복호화 실패 시 원본 값을 유지합니다.
	 * 또한, 키가 'items'이고 값이 빈 문자열인 경우 해당 항목을 무시합니다.
	 * 복호화된 각 값은 도메인 복원 함수에 전달됩니다.
	 * </summary>
	 * <param name="data">복호화할 배열 데이터</param>
	 * <returns>복호화 및 도메인 복원이 완료된 배열</returns>
	 */
	function decryptArrayRecursive(array $data): array {
		$out = [];
		foreach ($data as $key => $value) {
			// 키 복호화 시도, 실패 시 원본 키 유지
			$decryptedKey = decryptValue($key);
			$newKey       = $decryptedKey === false ? $key : $decryptedKey;

			if (is_string($value)) {
				// JSON 문자열인지 검사
				$decoded = json_decode($value, true);
				if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
					// JSON 배열이면 재귀 호출하여 복호화
					$newValue = decryptArrayRecursive($decoded);
				} else {
					// JSON 아니면 문자열 복호화 시도
					$decryptedValue = decryptValue($value);
					$newValue       = $decryptedValue === false ? $value : $decryptedValue;
				}
			} elseif (is_array($value)) {
				// 배열이면 재귀 호출
				$newValue = decryptArrayRecursive($value);
			} else {
				// 그 외는 그대로 유지
				$newValue = $value;
			}

			// 'items' 키에 빈 문자열 값이면 건너뜀
			if ($newKey === 'items' && $value === '') {
				continue;
			}

			// 복호화된 값에 도메인 복원 적용
			$out[$newKey] = restoreDomain($newValue);
		}
		if (isset($out['name']) && isset($out['value'])) {
			$value = $out['value'];
			if ($value === true || $value === 'true') $value = 1;
			elseif ($value === false || $value === 'false') $value = 0;
			$out[$out['name']] = $value;
			unset($out['name'], $out['value']);
		}
		return $out;
	}


	/**
	 * 주어진 평문을 전역 $secretKey를 사용하여 AES-128-CBC 방식으로 암호화합니다.
	 * 암호화 결과는 초기화 벡터(IV)를 앞에 붙인 후 Base64로 인코딩된 문자열로 반환됩니다.
	 * 
	 * @param string $plainText 암호화할 평문 문자열
	 * 
	 * @global string $secretKey 암호화에 사용할 비밀 키. 반드시 유효한 키가 설정되어 있어야 합니다.
	 * 
	 * @return string|null 암호화된 데이터를 Base64 인코딩한 문자열을 반환합니다.
	 *                     $secretKey 또는 $plainText가 없거나 유효하지 않을 경우 null을 반환합니다.
	 * 
	 * @throws null 별도의 예외 처리는 하지 않으며, openssl_encrypt가 실패하면 false를 반환할 수 있음에 유의해야 합니다.
	 */
	function encryptWithSecretKey(string $plainText): string {
		global $secretKey, $iv;
		$cipherText = openssl_encrypt(
			$plainText,
			'AES-128-CBC',
			$secretKey,
			OPENSSL_RAW_DATA,
			$iv
		);
		return base64_encode($cipherText);
	}

	/**
	 * 주어진 암호화된 데이터를 전역 $secretKey를 사용하여 AES-128-CBC 방식으로 복호화합니다.
	 * 
	 * @param string $encryptedData Base64로 인코딩된 암호화된 문자열.
	 *                              해당 문자열은 초기화 벡터(IV)와 암호문이 결합된 형태여야 합니다.
	 * 
	 * @global string $secretKey 복호화에 사용할 비밀 키. 반드시 유효한 키가 설정되어 있어야 합니다.
	 * 
	 * @return string|null 복호화된 원본 문자열을 반환합니다.
	 *                     $secretKey 또는 $encryptedData가 없거나 유효하지 않을 경우 null을 반환합니다.
	 * 
	 * @throws null 별도의 예외 처리는 하지 않으며, openssl_decrypt가 실패하면 false를 반환할 수 있음에 유의해야 합니다.
	 */
	function decryptWithSecretKey(string $encryptedData): string {
		global $secretKey, $iv;
		$raw = base64_decode($encryptedData);
		return openssl_decrypt(
			$raw,
			'AES-128-CBC',
			$secretKey,
			OPENSSL_RAW_DATA,
			$iv
		);
	}


	/**
	 * MD5 해시를 ASCII 인코딩된 입력 문자열에 대해 계산하여 16진수 문자열로 반환합니다.
	 * 
	 * 이 함수는 VB.NET에서 ASCII 인코딩을 사용한 해시 처리 방식을 PHP로 구현한 것으로,
	 * 입력 문자열을 ASCII 인코딩으로 간주하여 MD5 해시를 바이트 배열(raw output)로 계산한 뒤,
	 * 각 바이트를 2자리 소문자 16진수 문자열로 변환하여 최종 해시 문자열을 생성합니다.
	 * 
	 * @param string $text ASCII 인코딩으로 해시를 생성할 입력 문자열입니다.
	 * 
	 * @return string 입력 문자열의 MD5 해시를 32자리 소문자 16진수 문자열로 반환합니다.
	 * 
	 * @remarks
	 * - PHP 문자열은 바이너리 안전이므로 별도의 인코딩 변환 없이 그대로 해시 계산에 사용합니다.
	 * - md5 함수의 두 번째 인자를 true로 설정하여 바이트 배열(raw output)로 반환받고,
	 *   이를 16진수 문자열로 수동 변환하는 과정을 거칩니다.
	 * 
	 * @example
	 * ```php
	 * $hash = md5HashAscii("Hello");
	 * echo $hash;  // 8b1a9953c4611296a827abf8c47804d7
	 * ```
	 */
	function md5HashAscii(string $text): string {
		// MD5 해시 계산 (raw_output=true로 바이트 배열 반환)
		$hashBytes = md5($text, true);

		$result = '';
		// 바이트 배열을 순회하며 각 바이트를 2자리 소문자 16진수 문자열로 변환
		for ($i = 0; $i < strlen($hashBytes); $i++) {
			// ord 함수로 바이트 값 얻기
			$result .= sprintf('%02x', ord($hashBytes[$i]));
		}

		return $result;
	}


	/**
	 * <summary>
	 * 기존 키 목록에 중복되지 않는 고유 키를 생성합니다.
	 * </summary>
	 * <param name="existing">참조로 전달된 기존 키 목록 배열</param>
	 * <returns>생성된 고유 키 문자열</returns>
	 */
	function generateUniqueKey(&$existing) {
		do {
			$alpha = chr(rand(97, 122));            // a ~ z 중 랜덤 알파벳
			$num   = str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT); // 00 ~ 99 중 랜덤 숫자, 두 자리로 패딩
			$key   = $alpha.$num;                 // 예: 'a05', 'z99'
		} while (in_array($key, $existing));        // 중복 검사

		$existing[] = $key;                          // 생성된 키를 기존 목록에 추가
		return $key;
	}



?>