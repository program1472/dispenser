<?php
if (!function_exists('netHttpWebRequest')) {
    function netHttpWebRequest(&$url, $postData = null, $method = "GET")
    {
        global $cookieFile;

        // 쿠키 파일 없으면 임시 생성
        if (empty($cookieFile)) {
            $cookieFile = sys_get_temp_dir() . '/curl_cookie_' . getmypid() . '.txt';
        }

        $ch = curl_init();

        // 공통 옵션
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2TLS);
		curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
		curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST,
		  'ECDHE-ECDSA-AES128-GCM-SHA256:'
		.'ECDHE-RSA-AES128-GCM-SHA256:'
		.'ECDHE-ECDSA-AES256-GCM-SHA384:'
		.'ECDHE-RSA-AES256-GCM-SHA384:'
		.'ECDHE-ECDSA-CHACHA20-POLY1305:'
		.'ECDHE-RSA-CHACHA20-POLY1305');
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36");
        curl_setopt($ch, CURLOPT_ENCODING, ""); // gzip/deflate/br 허용
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

		$cookieDir  = dirname($cookieFile);

		// 디렉터리 보장(755)
		if (!is_dir($cookieDir)) {
			mkdir($cookieDir, 0777, true);
		}

		// 파일 없으면 만들 때 umask 영향 제거 후 생성
		if (!file_exists($cookieFile)) {
			$old = umask(0);          // 새 파일 기본권한 최대치로
			touch($cookieFile);       // 생성
			umask($old);              // 복원
		}
		// 원하는 퍼미션으로 고정
		chmod($cookieFile, 0777);

        curl_setopt($ch, CURLOPT_COOKIEJAR,  $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);

        // 보낼 헤더(한 번에 세팅)
		$headers[] = 'Sec-CH-UA: "Chromium";v="137", "Not:A-Brand";v="99"';
		$headers[] = 'Sec-CH-UA-Platform: "Windows"';
		$headers[] = 'Sec-CH-UA-Mobile: ?0';
		$headers[] = 'Sec-Fetch-Dest: document';
		$headers[] = 'Sec-Fetch-Mode: navigate';
		$headers[] = 'Sec-Fetch-Site: same-site';
		$headers[] = 'Sec-Fetch-User: ?1';
		$headers[] = 'Origin: https://supplier.coupang.com';
		$headers[] = 'Referer: https://supplier.coupang.com/';

        // 요청 메서드/바디 처리
        $method = strtoupper($method ?: 'GET');

        if ($method === 'GET') {
            // GET은 바디 전송 금지. 파라미터가 있으면 쿼리스트링으로만 붙임
            if (!is_null($postData)) {
                if (is_array($postData)) {
                    $qs = http_build_query($postData, '', '&', PHP_QUERY_RFC3986);
                } else {
                    $qs = trim((string)$postData);
                }
                if (!empty($qs)) {
                    $url .= (strpos($url, '?') !== false ? '&' : '?') . $qs;
                    curl_setopt($ch, CURLOPT_URL, $url);
                }
            }
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        } else {
            // 비-GET: 바디 전송
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if (is_array($postData)) {
                $payload = http_build_query($postData, '', '&', PHP_QUERY_RFC3986);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                $headers[] = "Content-Type: application/x-www-form-urlencoded";
            } else {
                $payload = (string)$postData;
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                if (strlen($payload) && $payload[0] === '{') {
                    $headers[] = "Content-Type: application/json";
                } else {
                    $headers[] = "Content-Type: application/x-www-form-urlencoded";
                }
            }
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // 응답 헤더 수집(중복 허용)
        $responseHeaders = [];
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function($curl, $headerLine) use (&$responseHeaders) {
            $len = strlen($headerLine);
            if ($len <= 2) return $len;
            if (strpos($headerLine, ":") === false) return $len;
            list($name, $value) = explode(":", $headerLine, 2);
            $name  = strtolower(trim($name));
            $value = trim($value);
            if ($name === '') return $len;
            if (!isset($responseHeaders[$name])) {
                $responseHeaders[$name] = $value;
            } else {
                if (!is_array($responseHeaders[$name])) {
                    $responseHeaders[$name] = [$responseHeaders[$name]];
                }
                $responseHeaders[$name][] = $value;
            }
            return $len;
        });

        // 보낸 요청 헤더 캡처
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        $result       = curl_exec($ch);
        $curlErr      = curl_errno($ch);
        $curlErrMsg   = curl_error($ch);
        $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $sentHeaders  = curl_getinfo($ch, CURLINFO_HEADER_OUT);
        curl_close($ch);

		clearstatcache(true, $cookieFile);
		@chmod($cookieFile, 0777);

        // 공통 응답 구조
        $response = [
            'error'           => ['code' => 0, 'msg' => ''],
            'result'          => ['type' => 'non', 'data' => $result, 'url' => $effectiveUrl],
            'headers'         => $sentHeaders,     // 실제 전송된 요청 헤더(raw)
            'responseHeaders' => $responseHeaders, // 응답 헤더
        ];

        // (선택) 요청/응답 로그 저장
        if (defined('INC_ROOT') && function_exists('strToSaveFile')) {
            try {
                $ts = (new DateTime('now', new DateTimeZone('Asia/Seoul')))->format('YmdHisv');
                $logFile = INC_ROOT . '/tem/' . $ts . '.html';
                strToSaveFile($logFile, $response);
            } catch (\Throwable $e) { /* ignore */ }
        }

        if ($curlErr) {
            $response['error']['code'] = $curlErr;
            $response['error']['msg']  = $curlErrMsg;
            return $response;
        }

        // 파일 응답 감지
        $cdRaw = $responseHeaders['content-disposition'] ?? "";
        $ctRaw = $responseHeaders['content-type'] ?? "";
        $cd = is_array($cdRaw) ? end($cdRaw) : $cdRaw;
        $ct = is_array($ctRaw) ? end($ctRaw) : $ctRaw;

        if (is_string($cd) && stripos($cd, 'attachment') !== false) {
            // 파일명 추출
            $filename = null;
            if (preg_match('/filename\*=UTF-8\'\'([^;]+)/i', $cd, $m) ||
                preg_match('/filename=\"?([^\";]+)\"?/i', $cd, $m)) {
                $filename = urldecode($m[1]);
            }
            if (!$filename) {
                $filename = basename(parse_url($effectiveUrl, PHP_URL_PATH) ?: '');
                if ($filename === '') $filename = 'download.bin';
            }

            // 임시파일 저장
            $tmp = tempnam(sys_get_temp_dir(), 'netFile_');
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if ($ext) {
                @rename($tmp, $tmp . '.' . $ext);
                $tmp .= '.' . $ext;
            }
            file_put_contents($tmp, $result);

            $response['result']['type'] = 'file';
            $response['result']['data'] = [
                'path'     => $tmp,
                'filename' => $filename,
                'mime'     => $ct ?: 'application/octet-stream',
            ];
            return $response;
        }

        // JSON 감지
        $trimmed = is_string($result) ? trim($result) : '';
        $looksJson =
            (strlen($trimmed) > 1 && $trimmed[0] === '{' && substr($trimmed, -1) === '}') ||
            (strlen($trimmed) > 1 && $trimmed[0] === '[' && substr($trimmed, -1) === ']');

        if ($looksJson) {
            $decoded = json_decode($trimmed, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $response['result']['type'] = 'json';
                $response['result']['data'] = $decoded;
                return $response;
            }
        }

        // 그 외(HTML 등)
        $response['result']['type'] = 'html';
        $response['result']['data'] = $result;
        return $response;

    }
}
?>
