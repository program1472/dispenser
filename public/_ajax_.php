<?php

	header('Content-Type: application/json');
	require_once "inc/common.php";
	$response["data"]['HTTP_REFERER'] = $_SERVER['HTTP_REFERER'] ?? '';
	if (is_null($mb_id) || strlen($mb_id) === 0){
		if (!empty($_POST) && isset($_POST['type']) && $_POST['type'] === 'curl'){
			unset($_POST['type']);
		} else {
			require DOC_ROOT.'/logout.php';
			exit();
		}
	}
	$response["html"] = "";
	$response["totalCount"] = 0;
	$response["ip"] = $_SERVER['REMOTE_ADDR'];
	$response["dev"] = [
		'name' => "변희성",
		'eMail' => "program1472@naver.com",
		'H.P' => "010-5222-2318",
		'homepage' => "http://program1472.com/",
		'blog' => "http://program1472.com/blog",
		'cafe' => "http://program1472.com/cafe"
	];

	$uri = $_SERVER['REQUEST_URI'];
	$path = parse_url($uri, PHP_URL_PATH);
	$segments = explode('/', trim($path, '/'));

	$response['data']["page"]['segments']['encrypt'] = $segments;

	$pageName = end($segments);

	// 쿼리스트링 분리 (암호화된 토큰 뒤에 ?param=value 형태가 붙을 수 있음)
	$queryString = '';
	if (strpos($pageName, '?') !== false) {
		list($pageName, $queryString) = explode('?', $pageName, 2);
		// 쿼리스트링을 $_GET에 추가
		parse_str($queryString, $queryParams);
		$_GET = array_merge($_GET, $queryParams);
	}

	// 복호화 처리
	$lastSegment = decryptValue($pageName);

	// 복호화된 경로 분리
	$segments = explode('/', $lastSegment);
	$response['data']["page"]['segments']['decrypt'] = $segments;
	$today = date('Y-m-d');
	
	if (!is_array($segments) || count($segments) < 2 || empty($segments[0]) || !($segments[0] === $today || $segments[0] === 'iframe')) {
		echo json_encode($segments , JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		//require DOC_ROOT.'/logout.php';
		exit();
	}
	if (count($segments) === 3 && $segments[1] === "image"){
		require 'inc/images.php';
		exit;
	}

	//if ($_SERVER['REQUEST_METHOD'] === 'GET'){
	if (!empty($_GET)){
		$response['data']['GET']['encrypt'] = $_GET;	
		clearBase64Values($response['data']['GET']['encrypt']);
		$_GET = decryptArrayRecursive($_GET);
		$response['data']['GET']['decrypt'] = $_GET;
		clearBase64Values($response['data']['GET']['decrypt']);
	}
	//if ($_SERVER['REQUEST_METHOD'] === 'POST'){
	if (!empty($_POST)){
		$response['data']['POST']['encrypt'] = $_POST;
		clearBase64Values($response['data']['POST']['encrypt']);
		$_POST = decryptArrayRecursive($_POST);
		$response['data']['POST']['decrypt'] = $_POST;
		clearBase64Values($response['data']['POST']['decrypt']);
	}
	
	$menuName = end($segments);			// type 구분자 추출 (예: "검색"인지 "수정"인지 등)
	$response['data']['page']['pageName'] = $pageName;
	$response['data']['page']['menuName'] = $menuName;
	
	// 매뉴명을 새로 암호화
	$pageName = encryptValue($segments[0]."/$menuName");

	// require 대상 파일
	$targetFile = DOC_ROOT."/doc/{$roleName}/{$menuName}.php";
	$response['data']['page']['path'] = $targetFile;
	$response['data']['page']['DOC_ROOT'] = DOC_ROOT;
	$response['data']['page']['SRC'] = SRC;
	$response['data']['page']['roleName'] = $roleName;
	$response['data']['page']['file_exists'] = file_exists($targetFile);
	if (file_exists($targetFile)) {
		if ($menuName === 'dev') $_GET['a'] = 'list';
		require $targetFile;
	} else {
		$response['error'] = sendError(403, true)	;
		$response['error']['msg'] = "올바른 접근이 아닙니다.";
		Finish();
	}

	ini_set('memory_limit', '1G');   // 1GB까지 임시 확장
skip:
	Finish();
?>
