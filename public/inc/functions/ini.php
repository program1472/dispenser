<?php
$lifetime = 28800; // 8시간
$savePath = __DIR__ . '/sessions';

if (!is_dir($savePath)) {
    mkdir($savePath, 0770, true);
}

ini_set('session.save_path', $savePath);
ini_set('session.gc_maxlifetime', (string)$lifetime);
ini_set('session.gc_probability', '1');
ini_set('session.gc_divisor', '100');

if (PHP_VERSION_ID >= 70300) {
    // PHP 7.3 이상: 배열 옵션 지원
    session_set_cookie_params([
        'lifetime' => $lifetime,
        'path'     => '/',
        'secure'   => !empty($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
} else {
    // PHP 7.2 이하: 배열 미지원 → 기본 파라미터만
    session_set_cookie_params(
        $lifetime,
        '/',
        '',
        !empty($_SERVER['HTTPS']),
        true
    );
}

session_start();

/*
좋습니다. 질문 주신 구문은 이거죠:

```php
setcookie(session_name(), session_id(), time() + $lifetime, '/; samesite=Lax', '', !empty($_SERVER['HTTPS']), true);
```

`setcookie()`의 파라미터는 순서대로 이렇게 해석됩니다:

---

### 1. `session_name()`

* 쿠키 이름.
* PHP 세션은 기본적으로 `"PHPSESSID"`라는 이름을 쓰는데, `session_name()`으로 현재 세션 이름을 반환해서 그대로 사용합니다.
* 즉, 발급되는 쿠키의 key 값이 됩니다.

---

### 2. `session_id()`

* 쿠키 값.
* 지금 열린 세션의 ID(예: 무작위 문자열).
* 이게 있어야 서버가 클라이언트 요청을 같은 세션으로 인식합니다.

---

### 3. `time() + $lifetime`

* 쿠키 만료 시간 (Unix 타임스탬프).
* 여기서는 "현재 시각 + 8시간"이니, 브라우저가 그 시간까지 쿠키를 유지합니다.
* 0을 주면 "세션 쿠키"가 돼서 브라우저 닫을 때 지워집니다.

---

### 4. `'/; samesite=Lax'`

* 쿠키의 경로(`path`).
* 기본적으로 `/`를 주면 도메인 전체에서 쿠키가 유효합니다.
* 여기서는 PHP 7.2 호환 때문에 path 뒤에 `; samesite=Lax` 문자열을 붙여서 **브라우저가 SameSite 속성도 같이 해석**하게 꼼수를 쓰고 있습니다.

  * 원래는 PHP 7.3 이상부터 `'samesite' => 'Lax'` 옵션을 배열로 줄 수 있지만, 7.2는 지원하지 않아서 이렇게 흉내내는 겁니다.

---

### 5. `''`

* 쿠키의 도메인(domain).
* 빈 문자열이면 현재 도메인에 한정됩니다.
* 예를 들어 `example.com`에서 빈 값이면 `example.com`과 그 하위 경로에서만 유효.

---

### 6. `!empty($_SERVER['HTTPS'])`

* `secure` 플래그.
* true면 HTTPS 연결에서만 쿠키 전송.
* 여기서는 `$_SERVER['HTTPS']`가 비어 있지 않으면 true가 되도록 했습니다. 즉, HTTPS면 secure 쿠키.

---

### 7. `true`

* `httponly` 플래그.
* true면 자바스크립트(`document.cookie`)에서 접근 불가.
* 보안 강화 목적(세션 탈취 방지).

---

📌 정리

* 이름: 세션 쿠키 이름 (`PHPSESSID`)
* 값: 세션 ID
* 만료: 지금부터 8시간 후
* 경로: `/` (그리고 SameSite=Lax 속성 꼼수 포함)
* 도메인: 현재 도메인
* secure: HTTPS에서만
* httponly: JS 접근 차단

---

여기서 궁금한 건, 당신이 원하는 게 **진짜 고정 8시간 세션**인지, 아니면 **활동 있을 때마다 8시간씩 갱신되는 세션**인지예요.
지금 `setcookie()`를 매 요청마다 쓸지, 로그인 직후에만 쓸지를 그걸로 나누게 됩니다.
*/
// PHP 7.2에서도 samesite 적용하고 싶으면 → 세션 시작 후 강제로 덮어쓰기
if (PHP_VERSION_ID < 70300) {
	// setcookie()는 브라우저에 새로운 만료 시간을 보내서 “세션 쿠키 갱신”을 시킵니다.
	// 이걸 페이지마다 실행하면, 사용자가 클릭할 때마다 만료 시간이 계속 뒤로 밀려서 결과적으로 **“8시간 동안 아무 활동 없을 때만 로그아웃”**으로 바뀝니다.
	// 반대로, 딱 로그인한 순간만 실행하면, 8시간이 지나면 무조건 로그아웃이 됩니다.
    //setcookie(session_name(), session_id(), time() + $lifetime, '/; samesite=Lax', '', !empty($_SERVER['HTTPS']), true);
}
	//setcookie('path', $savePath, time() + $lifetime, '/; samesite=Lax', '', !empty($_SERVER['HTTPS']), true);
?>