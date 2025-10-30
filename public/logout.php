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

require_once "common.php";
// 이동
$alert = (isset($mb_id) && $mb_id !== '') ? "alert('로그아웃 되었습니다.');" : "";
echo "<script>{$alert}window.location.replace('".SRC."');</script>";
$mb_id = $mb_nm = $mb_lv = null;
exit;
