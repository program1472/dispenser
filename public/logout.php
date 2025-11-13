<?php
// 로그아웃 시, “특정 키만 해제”와 “전체 초기화”는 용도가 다릅니다.
// 둘 다 동시에 할 필요는 없고, 상황에 맞게 ‘하나만’ 선택하세요.

if (session_status() !== PHP_SESSION_ACTIVE) @session_start();

/* ──────────────────────────────────────────────────────────────
 [선택 A] 필요한 세션 키만 해제 (다른 세션 데이터는 유지하고 싶을 때)
────────────────────────────────────────────────────────────── */
// unset($_SESSION['mb_id'], $_SESSION['mb_nm'], $_SESSION['mb_lv']);

/* ──────────────────────────────────────────────────────────────
 [선택 B] 모든 세션 데이터 제거 (완전한 로그아웃에 일반적으로 권장)
 선택 A를 했다면 선택 B는 하지 마세요(중복/불필요).
────────────────────────────────────────────────────────────── */
$_SESSION = array();

// 세션 쿠키 제거
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}

// 세션 파기
@session_destroy();
@session_write_close();

// 로그인 페이지로 리다이렉트 (common.php 로드하지 않음 - 무한 리다이렉트 방지)
$login_url = '/';  // 또는 'login.php' (상대 경로)

// 이동 (세션이 이미 파기되었으므로 항상 로그아웃 알림 표시)
$alert = "alert('로그아웃 되었습니다.');";
echo "<script>{$alert}window.location.replace('{$login_url}');</script>";
exit;
