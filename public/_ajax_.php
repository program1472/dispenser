<?php

	header('Content-Type: application/json');
	require_once "inc/common.php";
	$response["data"]['HTTP_REFERER'] = $_SERVER['HTTP_REFERER'] ?? '';
	if (is_null($mb_id) || strlen($mb_id) === 0){
		if (!empty($_POST) && isset($_POST['type']) && $_POST['type'] === 'curl'){
			unset($_POST['type']);
		} else {
			require ERP_ROOT.'/logout.php';
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
	
	$response['data']["pg"]['sg']['og'] = $segments;

	$pageName = end($segments);

	// 복호화 처리
	$lastSegment = decryptValue($pageName);

	// 복호화된 경로 분리
	$segments = explode('/', $lastSegment);
	$response['data']["pg"]['sg']['nw'] = $segments;
	$today = date('Y-m-d');
	
	if (!is_array($segments) || count($segments) < 2 || empty($segments[0]) || !($segments[0] === $today || $segments[0] === 'iframe')) {
		echo json_encode($segments , JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		//require ERP_ROOT.'/logout.php';
		exit();
	}
	if (count($segments) === 3 && $segments[1] === "image"){
		require 'inc/images.php';
		exit;
	}

	//if ($_SERVER['REQUEST_METHOD'] === 'GET'){
	if (!empty($_GET)){
		$response['data']['GET']['OG'] = $_GET;	
		clearBase64Values($response['data']['GET']['OG']);
		$_GET = decryptArrayRecursive($_GET);
		$response['data']['GET']['NW'] = $_GET;
		clearBase64Values($response['data']['GET']['NW']);
	}
	//if ($_SERVER['REQUEST_METHOD'] === 'POST'){
	if (!empty($_POST)){
		$response['data']['POST']['OG'] = $_POST;
		clearBase64Values($response['data']['POST']['OG']);
		$_POST = decryptArrayRecursive($_POST);
		$response['data']['POST']['NW'] = $_POST;
		clearBase64Values($response['data']['POST']['NW']);
	}
	
	$menuName = end($segments);			// type 구분자 추출 (예: "검색"인지 "수정"인지 등)
	$response['data']['pageName'] = $pageName;
	$response['data']['menuName'] = $menuName;
	
	// 매뉴명을 새로 암호화
	$pageName = encryptValue($segments[0]."/$menuName");

	// require 대상 파일
	$targetFile = DOC_ROOT."/doc/{$roleName}/{$menuName}.php";
	$response['data']['path'] = $targetFile;
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
