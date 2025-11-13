<?php
/**
 * dispenser/inc/topArea.php
 * - 공통 헤더/메타 + 역할별 메뉴 렌더링
 * - 메뉴 데이터는 common.php -> dispenser/inc/menus.php 에서 제공하는 $menus 사용(새 선언 금지)
 * - 각 <a>:
 *    data-t   = $item['id']
 *    텍스트    = $item['name']
 *    onclick = loadPage( encryptValue("$today/$item['path']") )
 */

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/common.php'; // 여기서 $menus, $today, 상수/함수 로드됨, $roleName도 제공됨

// CSS/메뉴용 role: $roleName 사용 (common.php에서 이미 매핑됨)
// hq_admin, super_admin 등이 모두 'hq'로 매핑됨
$role = $roleName; // 'hq' | 'vendor' | 'customer' | 'lucid'

// 역할별 제목/브랜드 라벨
$titleByRole = [
  'hq'       => '올투그린 디스펜서 — 본사 포털(UI/UX 고도화)',
  'vender'   => '올투그린 디스펜서 — 벤더 전용(UI/UX 고도화)',
  'customer' => '올투그린 디스펜서 — 고객 포털',
  'lucid' => '올투그린 디스펜서 — 루시드 포털',
];

$brandByRole = [
  'hq'       => '올투그린<br>본사 포털',
  'vender'   => '올투그린<br>벤더 포털',
  'customer' => '올투그린<br>고객 포털',
  'lucid' => '올투그린<br>루시드 포털',
];

// 캐시버스터용 안전 mtime
function safe_mtime($path){ return is_file($path) ? filemtime($path) : time(); }

?><!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=Edge, chrome=1" />
    <meta name="author" content="<?= OFFICE_NAME; ?>" />
    <meta name="copyright" content="" />
    <meta name="title" content="" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta property="og:title" content="" />
    <meta property="og:site_name" content="" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="" />
    <meta property="og:image" content="">
    <title><?= htmlspecialchars($titleByRole[$role] ?? OFFICE_NAME, ENT_QUOTES, 'UTF-8'); ?></title>

    <link rel="apple-touch-icon-precomposed" href="<?= IMG_SRC; ?>/favicon.png" />
    <link rel="shortcut icon" href="<?= IMG_SRC; ?>/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="<?= CSS_SRC; ?>/style.css?<?= date('YmdHis', safe_mtime(CSS_ROOT.'/style.css')) ?>" />
    <link rel="stylesheet" type="text/css" href="<?= CSS_SRC; ?>/tem.css?<?= date('YmdHis', safe_mtime(CSS_ROOT.'/tem.css')) ?>" />
    <link rel="stylesheet" type="text/css" href="<?= CSS_SRC; ?>/<?= $role ?>.css?<?= date('YmdHis', safe_mtime(CSS_ROOT.'/'.$role.'.css')) ?>" />
	<link rel="stylesheet" type="text/css" href="<?= CSS_SRC; ?>/header.css?<?= date('YmdHis', safe_mtime(CSS_ROOT.'/header.css')) ?>" />

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <!-- 달력 CSS가 필요하면 주석 해제
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
    -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
    <!-- tooltip -->
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/themes/light.css" />
    <script src="<?= JS_SRC ?>/x.js?<?= date('YmdHis', safe_mtime(JS_ROOT.'/x.js')) ?>"></script>
	<script>
		function onRoleChange(sel){
		  const v = sel.value;
		  if(!v) return;
		  const u = new URL(window.location.href);
		  u.pathname = '/';                 // 필요 없으면 이 줄 삭제
		  u.searchParams.set('role', v.toLowerCase()); // 소문자로 전달
		  window.location.href = u.toString();
		}
	</script>
</head>
<body>
<?php
  // 로그인 체크(필요 시 처리)
  if (empty($_SESSION['mb_id'] ?? null)) {
    // TODO: 비로그인 시 동작(리다이렉트/메시지). 현재는 통과.
  }

  $brand = $brandByRole[$role] ?? OFFICE_NAME;

  echo '<header>';
  echo '  <div class="brand"><img src="'.IMG_SRC.'/favicon.png" alt="올투그린" style="height: 40px;"></div>';

	// 메뉴 렌더링
	echo '  <nav id="tabs">';
	if (!empty($menus[$role]) && is_array($menus[$role])) {
		$first = true;
		foreach ($menus[$role] as $item) {
			$id   = $item['id']   ?? '';
			$name = $item['name'] ?? '';
			$path = $item['path'] ?? '';
			$sub  = $item['submenu'] ?? $item['sub'] ?? [];

			if ($id === '' || $name === '') continue;

			// 서브메뉴가 있는 경우
			if (!empty($sub) && is_array($sub)) {
				$cls = $first ? ' class="active dropdown"' : ' class="dropdown"';
				echo '    <div'.$cls.' data-t="'.htmlspecialchars($id, ENT_QUOTES, 'UTF-8').'">';
				echo '      <a class="dropdown-toggle">'.htmlspecialchars($name, ENT_QUOTES, 'UTF-8').'</a>';
				echo '      <div class="dropdown-menu">';
				foreach ($sub as $subItem) {
					$subId   = $subItem['id']   ?? '';
					$subName = $subItem['name'] ?? '';
					$subPath = $subItem['path'] ?? '';
					if ($subId === '' || $subName === '' || $subPath === '') continue;

					$subEnc = encryptValue($today . '/' . $subPath);
					echo '        <a data-t="'.htmlspecialchars($subId, ENT_QUOTES, 'UTF-8').'" data-token="'.$subEnc.'" onclick="loadPage(this, \''.$subEnc.'\')">'.htmlspecialchars($subName, ENT_QUOTES, 'UTF-8').'</a>';
				}
				echo '      </div>';
				echo '    </div>';
			} else {
				// 일반 메뉴
				if ($path === '') continue;
				$enc  = encryptValue($today . '/' . $path);
				$cls  = $first ? ' class="active"' : '';
				echo '    <a data-t="'.htmlspecialchars($id, ENT_QUOTES, 'UTF-8').'" data-token="'.$enc.'"'.$cls.' onclick="loadPage(this, \''.$enc.'\')">'.htmlspecialchars($name, ENT_QUOTES, 'UTF-8').'</a>';
			}
			$first = false;
		}
	}
	echo '  </nav>';


	// 역할별 우측 부가영역
	if ($role === 'hq') {
		//echo '  <div class="small" style="margin-left:20px">v1.1 (HQ)</div>';
	} elseif ($role === 'vender') {
		echo '  <div class="row" style="margin-left:20px">';
		echo '    <span class="small">벤더ID</span>';
		echo '    <input id="vendorId" class="input" value="'.$_SESSION['user']['vendor_id'].'" style="width:90px">';
		//echo '    <button class="btn" id="resetSeed">더미데이터 재설정</button>';
		//echo '    <span class="small">v1.0 (Vendor)</span>';
		echo '  </div>';
	} elseif ($role === 'customer') {
		//echo '  <div class="small" style="margin-left:20px">v1.0 (Customer)</div>';
	} elseif ($role === 'lucid') {
		//echo '  <div class="small" style="margin-left:20px">v1.2 (Lucid)</div>';
	}
	echo '  <div class="row" style="margin-left:auto">';
	//echo '<span style="margin-left: 10px;">'.$mb_nm.'('.$_SESSION['user']['userid'].')</span>';
	$logout_url = "logout.php";
	echo '<span style="margin-left: 10px;"><button type="button" onclick="window.location.href=\''.$logout_url.'\'">로그아웃</button></span>';
	echo '</div>';
	$roleLower = strtolower((string)($role ?? ''));
	$validRoles = ['hq','vendor','customer','lucid'];
	$placeholderSelected = in_array($roleLower, $validRoles, true) ? '' : ' selected';
	
	$selHQ       = ($roleLower === 'hq') ? ' selected' : '';
	$selVendor   = ($roleLower === 'vendor') ? ' selected' : '';
	$selCustomer = ($roleLower === 'customer') ? ' selected' : '';
	$selLucid    = ($roleLower === 'lucid') ? ' selected' : '';
	echo '  <div class="small" style="margin-left:15px">';
	echo '
	  <label for="role_select" class="label">역할</label>
	  <select id="role_select" name="role" class="input" onchange="onRoleChange(this)">
		<option value="" disabled' . $placeholderSelected . '>선택하세요</option>
		<option value="HQ"' . $selHQ . '>본사</option>
		<option value="VENDOR"' . $selVendor . '>밴더</option>
		<option value="CUSTOMER"' . $selCustomer . '>고객</option>
		<option value="LUCID"' . $selLucid . '>루시드</option>
	  </select>';
	echo '</div>';
	echo '</header>';
?>
