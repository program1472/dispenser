<?php
	if (!defined('DISPENSER')) exit; // 개별 페이지 접근 불가

	if (!function_exists('SENDMAIL')) {
		/**
		 * 지정된 제목, 본문으로 메일을 발송합니다.
		 *
		 * @param string $title          메일 제목
		 * @param string $body           메일 본문
		 * @param string $toName         수신자 이름 (기본: 사랑하는 고객님)
		 * @param string $MAIL_TO        수신자 이메일 주소(콤마로 구분된 경우 첫 번째만 사용)
		 * @param string $MAIL_ATTACHMENT 첨부 파일 리스트(콤마 구분된 파일 경로 목록)
		 * @return array                 ['success'=>bool, 기타 응답 데이터]
		 */
		function SENDMAIL(
			string $title,
			string $body,
			string $toName       = '사랑하는 고객님',
			string $MAIL_TO      = 'samatg@aserpacific.com',
			string $MAIL_ATTACHMENT = ''
		): array {
			// 1) 수신자 주소 파싱 (콤마로 구분된 경우 첫 번째만)
			$emails  = explode(',', $MAIL_TO);
			$address = trim($emails[0]);
			$errResponse = null;

			// 2) 첨부 파일 리스트
			$filelist = [];
			if ($MAIL_ATTACHMENT !== '') {
				foreach (explode(',', $MAIL_ATTACHMENT) as $f) {
					$f = trim($f);
					if ($f !== '') {
						$filelist[] = $f;
					}
				}
				if (empty($filelist)) {
					$filelist = null;
				}
			} else {
				$filelist = null;
			}

			// 3) POST 데이터 준비
			$postData = [
				'title'     => $title,
				'body'      => $body,
				'name'      => $toName,
				'address'   => $address,
				'filelist'  => $filelist
			];

			// 4) cURL 요청
			$ch = curl_init();
			curl_setopt_array($ch, [
				CURLOPT_URL            => HOST.FUNCTIONS_ROOT.'/_sendMail.php',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POST           => true,
				CURLOPT_POSTFIELDS     => $postData,
			]);
			$rawResponse = curl_exec($ch);

			if (curl_errno($ch)) {
				$err = curl_error($ch);
				$code = curl_errno($ch);
				curl_close($ch);
				return [
					'success' => false,
					'error'   => $err,
					'code'    => $code
				];
			}
			curl_close($ch);

			// 5) JSON 디코딩 및 반환
			$decoded = json_decode($rawResponse, true);
			if (json_last_error() !== JSON_ERROR_NONE) {
				$errResponse = [
					'success' => false,
					'error'   => 'Invalid JSON response',
					'raw'     => $rawResponse
				];
			}
			$decoded['error'] = $errResponse;
			$decoded['data'] = $postData;
			return $decoded;
		}
	}

?>