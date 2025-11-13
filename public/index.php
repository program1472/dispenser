<?php

	require_once "common.php";	
	if (!$mb_id){
		$current_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		if (!isset($_POST['email']) || !isset($_POST['password'])) unset($_POST);
		require "login.php";
		exit();
	}

	if (!empty($_GET)){
		$response['data']['GET']['OG'] = $_GET;	
		clearBase64Values($response['data']['GET']['OG']);
		$_GET = decryptArrayRecursive($_GET);
		$response['data']['GET']['NW'] = $_GET;
		clearBase64Values($response['data']['GET']['NW']);
	}

	if (!empty($_POST)){
		$response['data']['POST']['OG'] = $_POST;
		clearBase64Values($response['data']['POST']['OG']);
		$_POST = decryptArrayRecursive($_POST);
		$response['data']['POST']['NW'] = $_POST;
		clearBase64Values($response['data']['POST']['NW']);
	}

	if (!empty($_GET)) {
		// GET 값 정리
		$postFields = '';
		foreach ($_GET as $key => $value) {
			$key = htmlspecialchars($key, ENT_QUOTES, 'UTF-8');
			$key = encryptValue($key);
			if (is_array($value)) {
				// 배열인 경우 각 요소마다 hidden input 생성
				foreach ($value as $subValue) {
					$subValue = htmlspecialchars($subValue, ENT_QUOTES, 'UTF-8');
					$postFields .= "<input type='hidden' name='{$key}[]' value='{$subValue}'>".PHP_EOL;
				}
			} else {
				// 문자열인 경우 그대로 처리
				//$value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
				$value = encryptValue($value);
				$postFields .= "<input type='hidden' name='{$key}' value='{$value}'>".PHP_EOL;
			}
		}
		?>
		<!DOCTYPE html>
		<html lang="ko">
		<head>
			<meta charset="UTF-8">
			<title>POST 전송 중...</title>
		</head>
		<body>
			<form id="postForm" method="POST" action="/">
				<?= $postFields ?>
			</form>
			<script>
				document.getElementById('postForm').submit();
			</script>
		</body>
		</html>
		<?php
		exit;
	}

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		if (isset($_POST['menuName'])) {
			$menuName = $_POST['menuName'];
			$menuName = decryptValue($menuName);
			$_POST['menuName'] = $menuName;
			$response['data']['POST']['NW'] = $_POST;
			$segments= explode('/', $menuName);
			if (!is_array($segments) || count($segments) < 2 || empty($segments[0]) || $segments[0] !== $today) {
				require 'logout.php';
				$response['result'] = false;
				$response['error'] = sendError(401, true);
				$response['error']['msg'] = '로그인이 필요합니다.';
				Finish();
			}

			$menuName = end($segments);
			$pageName = encryptValue("$today/$menuName");
			
			$filePath = DOC_ROOT."/doc/{$roleName}/{$menuName}.php";
			$response['item']['path'] = $filePath;
			if (file_exists($filePath)) {
				$response['item']['data'] = $_POST;
				unset($_POST);

				// 출력 버퍼링 시작 - require한 페이지의 HTML을 캡처
				ob_start();
				require $filePath;
				$html = ob_get_clean();

				// JSON 응답으로 통일
				$response['result'] = 'ok';
				$response['html'] = $html;
				Finish();
			} else {
				http_response_code(404);
				$response['result'] = 'error';
				$response['msg'] = '해당 페이지는 존재하지 않으며, 현재 개발 중입니다.';
				$response['error'] = ['msg' => '해당 페이지는 존재하지 않으며, 현재 개발 중입니다.', 'code' => 404];
				Finish();
			}
		} elseif (isset($_POST['role'])){
			$roleName = $_POST['role'] ?? 'customer';
			$_SESSION['role'] = $roleName;
		}
	}
	//$_SESSION['hadder_info'] = [];	
	require "inc/topArea.php";
?>
<div class="content" id="content" name="content">

</div>
<div class="pop"></div>

<div id="imageModal" class="modal" onclick="closeImageModal()" style="display: none;">
	<span class="modal-close" onclick="closeImageModal()">×</span>
	<img class="modal-content" id="modalImage">
</div>

<?php
	$bottomPath = DOC_ROOT . "/doc/{$roleName}/bottomArea.php";
	if (is_file($bottomPath)) require $bottomPath;
	require JS_ROOT."/js.php";
	$type = $_POST['type'] ?? null;
	if (!empty($type) && $type === 'page'){
		$name = $_POST['name'] ?? null;
		if (!empty($name)){
			$segments= explode('/', $name);
			$menuName = end($segments);
			$pageName = encryptValue("$today/$menuName");
			$_POST['menuName'] = $pageName;
			$conditions = [];
			foreach ($_POST as $key => $value) {
				switch ($key) {
					case 'at':
					case 'type':
					case 'name':
					case 'menuName':
						break;
					default:
						$escapedKey = mysqli_real_escape_string($con, $key);
						$escapedVal = mysqli_real_escape_string($con, $value);
						$escapedKey = encryptValue($escapedKey);
						$escapedVal = encryptValue($escapedVal);
						$conditions[] = "$escapedKey=$escapedVal";
						break;
				}
			}
			if(count($conditions) > 0){
				$_POST['menuName'] .= "?".implode('&', $conditions);
			}
		}
	}

	// 암호화 키/평문 둘 다 지원
	$k = encryptValue('menuName');
	if (!empty($_POST) && (isset($_POST['menuName']) || isset($_POST[$k]))) {
		$menuName = $_POST['menuName'] ?? $_POST[$k];
		// el 없이 토큰만 넘겨도 동작하도록 JS에서 처리하므로 이 형태 OK
		echo "<script>loadPage('".addslashes($menuName)."');</script>";
	} else {
		$defaultToken = encryptValue("$today/dashboard");
		echo "<script>loadPage('".$defaultToken."');</script>";
	}

	require "inc/bottomArea.php";

?>