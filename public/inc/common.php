<?php

	declare(strict_types=1);

	if (!defined('DISPENSER')) define('DISPENSER', true);
	date_default_timezone_set('Asia/Seoul');

	define('IS_DEBUG', true);

	// 세션 초기화
	require_once __DIR__ . '/ini.php';

	// 동적 HOST (고정 IP 대신 권장)
	$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
	$host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
	define('HOST', $scheme . '://' . $host);

	// 루트 경로
	// public 디렉토리가 웹 루트
	$DOCROOT = dirname(__DIR__); // public의 부모 디렉토리
	define('ROOT', $DOCROOT . '/public');

	define('SRC', '/dispenser');                 // 웹 경로
	define('DOC_ROOT', ROOT);        // 물리 경로 (public)
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

	// TCPDF는 composer로 설치됨 (vendor/autoload.php를 통해 로드)
	// require_once ROOT."/doc/TCPDF/tcpdf.php";
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

	$mb_no = isset($_SESSION['user']["user_id"]) ? (int)$_SESSION['user']["user_id"] : null;
	$mb_id = isset($_SESSION['user']["userid"]) ? $_SESSION['user']["userid"] : null;
	$mb_nm = isset($_SESSION['user']["name"]) ? $_SESSION['user']["name"] : null;
	$mb_role = isset($_SESSION['user']["role_code"]) ? $_SESSION['user']["role_code"] : '';
	$roleName = isset($_SESSION['role']) ? $_SESSION['role'] : '';
	$response['mb_id'] = $mb_id;
	$response['PATH'] = [
		'ROOT' => ROOT,
		'SRC' => SRC,
		'DOC_ROOT' => DOC_ROOT,
		'INC_ROOT' => INC_ROOT,
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
	];
	$menuName = '';
	$today = date('Y-m-d');

?>