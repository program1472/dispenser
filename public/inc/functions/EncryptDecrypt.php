<?php
if (!defined('DISPENSER')) exit; // 개별 페이지 접근 불가

/*
class EncryptDecrypt {

    // 비밀 키 (사용자가 지정, 중요)
    private $secretKey = 'your_secret_key'; 
    private $ivLength; // IV의 길이
    private $cipherMethod = 'aes-256-cbc'; // 암호화 알고리즘

    public function __construct() {
        // IV 길이는 알고리즘에 따라 다르므로 이를 계산
        $this->ivLength = openssl_cipher_iv_length($this->cipherMethod);
    }

    // 문자열을 암호화하는 함수
    public function encrypt($data) {
        // 랜덤한 IV를 생성
        $iv = openssl_random_pseudo_bytes($this->ivLength);

        // 데이터 암호화
        $encrypted = openssl_encrypt($data, $this->cipherMethod, $this->secretKey, 0, $iv);

        // 암호화된 데이터와 IV를 반환 (IV는 복호화 시 필요하므로 함께 저장)
        return base64_encode($encrypted . '::' . base64_encode($iv));
    }

    // 암호화된 문자열을 복호화하는 함수
    public function decrypt($data) {
        // base64로 인코딩된 값을 디코딩
        list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);

        // IV 디코딩
        $iv = base64_decode($iv);

        // 복호화
        return openssl_decrypt($encrypted_data, $this->cipherMethod, $this->secretKey, 0, $iv);
    }
}
*/

class EncryptDecrypt {
	private $secretKey = 'your_secret_key';
	private $ivLength;
	private $cipherMethod = 'aes-256-cbc';

	public function __construct() {
		$this->ivLength = openssl_cipher_iv_length($this->cipherMethod);
	}

	public function encrypt($data) {
		$iv = openssl_random_pseudo_bytes($this->ivLength);
		$encrypted = openssl_encrypt($data, $this->cipherMethod, $this->secretKey, 0, $iv);
		return base64_encode($encrypted . '::' . base64_encode($iv));
	}

	public function decrypt($data) {
		if (is_array($data)) {
			// 배열이면 각 요소에 대해 재귀적으로 처리
			return array_map([$this, 'decrypt'], $data);
		}

		$decoded = base64_decode($data);
		if (strpos($decoded, '::') === false) return $data;

		list($encrypted_data, $iv_encoded) = explode('::', $decoded, 2);
		$iv = base64_decode($iv_encoded);

		if (strlen($iv) !== $this->ivLength) return $data;

		return openssl_decrypt($encrypted_data, $this->cipherMethod, $this->secretKey, 0, $iv);
	}
}

?>
