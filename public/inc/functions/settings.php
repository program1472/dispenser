<?php
if (!defined('DISPENSER')) exit; // 개별 페이지 접근 불가

// settings.json 파일 위치 (앱 루트 등, 웹에서 노출되지 않는 곳으로 두세요)
define('SETTINGS_FILE', __DIR__.'/'.(IS_DEBUG?'dev_':'').'settings.json');

/**
 * 설정 저장
 *
 * @param string      $section  섹션 이름 (VB의 AppName)
 * @param string|null $key      키 이름 (VB의 KeyName). null이면 섹션 전체를 $value로 저장
 * @param mixed|null  $value    저장할 값
 * @return bool                 성공 여부
 */
function saveSetting(string $section, ?string $key = null, $value = null): bool
{
    // 기존 설정 읽기
    $all = [];
    if (file_exists(SETTINGS_FILE)) {
        $json = file_get_contents(SETTINGS_FILE);
        $all  = json_decode($json, true) ?: [];
    }

    if ($key === null) {
        // 섹션 전체를 $value로 저장 (배열/스칼라 모두 허용)
        $all[$section] = $value;
    } else {
        // 섹션/키에 값 할당 (섹션을 배열로 보장)
        if (!isset($all[$section]) || !is_array($all[$section])) {
            $all[$section] = [];
        }
        $all[$section][$key] = $value;
    }

    // 다시 JSON으로 덮어쓰기
    $newJson = json_encode($all, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    return file_put_contents(SETTINGS_FILE, $newJson) !== false;
}

/**
 * 설정 읽기
 *
 * @param string      $section  섹션 이름
 * @param string|null $key      키 이름. null이면 섹션 전체를 반환
 * @param mixed|null  $default  기본값
 * @return mixed                저장된 값 또는 $default
 */
function getSetting(string $section, ?string $key = null, $default = null)
{
    static $cache = null;
    if ($cache === null) {
        if (file_exists(SETTINGS_FILE)) {
            $cache = json_decode(file_get_contents(SETTINGS_FILE), true) ?: [];
        } else {
            $cache = [];
        }
    }

    if ($key === null) {
        // 섹션 전체 반환
        return array_key_exists($section, $cache) ? $cache[$section] : $default;
    }

    // 특정 키 반환
    return isset($cache[$section]) && is_array($cache[$section]) && array_key_exists($key, $cache[$section])
        ? $cache[$section][$key]
        : $default;
}

/**
 * 설정 삭제
 *
 * @param string      $section
 * @param string|null $key   null이면 섹션 전체 삭제
 * @return bool
 */
function deleteSetting(string $section, ?string $key = null): bool
{
    if (!file_exists(SETTINGS_FILE)) {
        return false;
    }
    $all = json_decode(file_get_contents(SETTINGS_FILE), true) ?: [];

    if (!array_key_exists($section, $all)) {
        return false;
    }

    if ($key === null) {
        // 섹션 전체 삭제
        unset($all[$section]);
    } else {
        // 키만 삭제
        if (is_array($all[$section]) && array_key_exists($key, $all[$section])) {
            unset($all[$section][$key]);
            if (empty($all[$section])) {
                unset($all[$section]);
            }
        } else {
            // 삭제할 키 없음
            return false;
        }
    }

    return file_put_contents(SETTINGS_FILE, json_encode($all, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)) !== false;
}
?>
