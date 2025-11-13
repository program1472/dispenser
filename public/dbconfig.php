<?php

// SITE NAME
if ( !defined('SITE_NAME') )
{
    define('SITE_NAME', "[alltogreen] Make It Short!");
}

// URL LOCATION (Don't forget "/" at the end !)
if ( !defined('BASE_URL') )
{
    define('BASE_URL', "http://atg-cas.iptime.org/");
}

// DATABASE CONFIGURATION
if ( !defined('HOST_NAME') )
{
    define('HOST_NAME', "127.0.0.1:3306");
}

if ( !defined('DB_NAME') )
{
    define('DB_NAME', "dispenser");
}

if ( !defined('USER_NAME') )
{
    define('USER_NAME', "program1472");
}

if ( !defined('USER_PASSWORD') )
{
    define('USER_PASSWORD', '$gPfls1129');
}

	$con = @mysqli_connect('localhost', USER_NAME, USER_PASSWORD, DB_NAME);
	// 연결 확인
	if (!$con) {
		die("MySQL 연결 실패: " . mysqli_connect_error());
	}

	// UTF-8 설정 (한글 깨짐 방지)
	mysqli_set_charset($con, 'utf8mb4');
	mysqli_query($con, "SET NAMES utf8mb4");
	$db_name = "jeju";
	// mysqli_select_db($con, "jeju");
	
	//DB데이터 레코드 총 개수
	function getDbRows($con, $table, $where){	
		$sql = 'select count(*) from '.$table.($where?' where '.getSqlFilter($where):'');
		if ($result = mysqli_query($con, $sql)) {
		  if ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			return $row[0];
		  }
		}
	}

	//SQL필터링
	function getSqlFilter($sql){
		return $sql;
	}

?>