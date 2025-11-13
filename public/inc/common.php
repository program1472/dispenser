<?php

	declare(strict_types=1);

	if (!defined('DISPENSER')) define('DISPENSER', true);
	date_default_timezone_set('Asia/Seoul');

	define('IS_DEBUG', true);

	// 동적 HOST (고정 IP 대신 권장)
	$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
	$host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
	define('HOST', $scheme . '://' . $host);

	// 루트 경로
	$DOCROOT = $_SERVER['DOCUMENT_ROOT'];	//rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/\\');
	define('ROOT', $DOCROOT);

	// SRC 동적 설정
	// __DIR__과 DOCUMENT_ROOT 비교하여 웹 경로 계산
	$currentDir = str_replace('\\', '/', __DIR__);  // inc 디렉토리
	$publicDir = dirname($currentDir);  // public 디렉토리
	$docRoot = str_replace('\\', '/', $DOCROOT);

	// DOCUMENT_ROOT와 public 디렉토리 경로 비교
	if ($publicDir === $docRoot) {
		// 로컬: public이 DOCUMENT_ROOT와 같음
		define('SRC', '');
	} else {
		// 서버: DOCUMENT_ROOT 이후 경로를 SRC로 설정
		$webPath = str_replace($docRoot, '', $publicDir);
		// 마지막 /public 제거
		$webPath = preg_replace('#/public$#', '', $webPath);
		define('SRC', $webPath);
	}
	define('DOC_ROOT', ROOT . SRC);        // 물리 경로
	define('INC_ROOT', DOC_ROOT . '/inc');
	define('FUNCTIONS_ROOT', INC_ROOT . '/functions');
	define('JS_ROOT', DOC_ROOT . '/js');
	define('CSS_ROOT', DOC_ROOT . '/css');
	define('IMG_ROOT', DOC_ROOT . '/image');
	define("FILES_ROOT", DOC_ROOT.'/files');

	define('INC_SRC', SRC . '/inc');
	define('DOC_SRC', SRC . '/doc');
	define('FUNCTIONS_SRC', INC_SRC . '/functions');
	define('JS_SRC', SRC . '/js');
	define('CSS_SRC', SRC . '/css');
	define('IMG_SRC', SRC . '/image');
	define("FILES_SRC", SRC.'/files');

	define('OFFICE_NAME', '(주)올투그린파트너스');

	//require_once ROOT."/doc/TCPDF/tcpdf.php";
	require_once FUNCTIONS_ROOT.'/file.php';
	require_once FUNCTIONS_ROOT.'/date.php';
	require_once FUNCTIONS_ROOT."/curl.php";
	require_once FUNCTIONS_ROOT.'/ende.php';
	require_once FUNCTIONS_ROOT."/jinsu.php";
	require_once FUNCTIONS_ROOT."/error.php";
	require_once FUNCTIONS_ROOT.'/MySQLi.php';
	require_once FUNCTIONS_ROOT."/settings.php";
	require_once FUNCTIONS_ROOT.'/outputFile.php';
	require_once FUNCTIONS_ROOT.'/SENDMAIL.php';
	require_once FUNCTIONS_ROOT.'/JsonHalper.php';
	require_once FUNCTIONS_ROOT.'/permission.php';

	$GLOBAL_DATA_PATH = INC_ROOT.'/'.(IS_DEBUG?'dev_':'').'data.json';
	if (file_exists($GLOBAL_DATA_PATH)) {
		$jsonContent = file_get_contents($GLOBAL_DATA_PATH);
		$GLOBAL_DATA  = json_decode($jsonContent, true);
	} else {
		$GLOBAL_DATA  = [];
		file_put_contents($GLOBAL_DATA_PATH, json_encode($GLOBAL_DATA , JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
	}
	
	require_once FUNCTIONS_ROOT."/functions.php";
	require_once INC_ROOT."/menus.php";

	$response = [
		'SESSION'     => $_SESSION??[],
		'menus'       => $menus??[],
		'data'        => [
			'database_name' => $database_name,
			'PATH' => [
				'ROOT' => ROOT,
				'SRC' => SRC,
				'DOC_ROOT' => DOC_ROOT,
				'INC_ROOT' => INC_ROOT,
				'__DIR__' => __DIR__,
				'FUNCTIONS_ROOT' => FUNCTIONS_ROOT,
				'JS_ROOT' => JS_ROOT,
				'CSS_ROOT' => CSS_ROOT,
				'IMG_ROOT' => IMG_ROOT,
				'INC_SRC' => INC_SRC,
				'DOC_SRC' => DOC_SRC,
				'FUNCTIONS_SRC' => FUNCTIONS_SRC,
				'JS_SRC' => JS_SRC,
				'CSS_SRC' => CSS_SRC,
				'IMG_SRC' => IMG_SRC,
				'OFFICE_NAME' => OFFICE_NAME,
				'HOST' => HOST,
			],
		],
		'html'        => null,
		'item'        => null,
		'items'       => null,
		'result'      => false,
		'error'       => ['msg' => '', 'code' =>0],
		'events'      => null,
		'totalCount'  => 0,
		'approval'    => null,
		'pagination'  => null,
		'table_array' => null,
	];

	// 새 스키마: user_id만 사용 (userid 필드 제거됨)
	$mb_no = isset($_SESSION['user']["user_id"]) ? (int)$_SESSION['user']["user_id"] : null;
	$mb_id = $mb_no; // 호환성: mb_id는 이제 user_id와 동일
	$mb_nm = isset($_SESSION['user']["name"]) ? $_SESSION['user']["name"] : null;
	$mb_role = isset($_SESSION['user']["role_code"]) ? $_SESSION['user']["role_code"] : '';

	// role_code → directory 매핑 (super_admin, hq_admin → hq)
	$sessionRole = isset($_SESSION['role']) ? $_SESSION['role'] : '';
	$roleToDir = [
		'super_admin' => 'hq',
		'hq_admin'    => 'hq',
		'hq'          => 'hq',
		'vendor'      => 'vendor',
		'vender'      => 'vendor',
		'sales_rep'   => 'vendor',
		'customer'    => 'customer',
		'lucid'       => 'lucid',
	];
	$roleName = $roleToDir[$sessionRole] ?? 'customer';

	$response['mb_id'] = $mb_id;

	$menuName = '';
	$today = date('Y-m-d');
	$defaultRowsPage = 25;
?>