<?php
// 안전 기본 설정
declare(strict_types=1);

// (1) 허용 루트 디렉터리: source.php가 있는 폴더 기준 하위만 열람
$BASE_DIR = realpath(__DIR__) ?: __DIR__;
// (2) 허용 확장자(코드 보기 목적이면 txt, php, js, css 정도만)
$ALLOW_EXT = ['txt','log','md','json','xml','csv','php','js','css','html'];

// 공통: 에러 상세 노출 금지
ini_set('display_errors', '0');

function deny(int $code = 403, string $msg = 'Forbidden'): void {
    http_response_code($code);
    header('Content-Type: text/plain; charset=UTF-8');
    echo $msg;
    exit;
}

$path = $_GET['path'] ?? '';
if ($path === '' || !is_string($path)) {
    deny(400, 'Bad Request: path required');
}

// 스킴/스트림 차단(php://, file://, data:// 등)
if (strpos($path, '://') !== false) {
    deny('스트림/URL은 허용되지 않습니다.');
}

// 1) 스트림 래퍼, 널바이트, 절대경로 차단
if (preg_match('#^(php|data|zlib|phar|glob|expect|compress|zip|bzip2|file)://#i', $path)) {
    deny();
}
if (strpos($path, "\0") !== false) deny();
if (preg_match('#^[a-zA-Z]:\\\\|^/+#', $path)) { // C:\ 또는 / 루트
    deny();
}

// 널바이트 · 제어문자 제거
$path = preg_replace('/[\x00-\x1F]/', '', $path);

// 2) 정규화(realpath) 및 루트 고정
$target = realpath($BASE_DIR . DIRECTORY_SEPARATOR . $path);
if ($target === false) deny(404, 'Not Found');
$baseReal = realpath($BASE_DIR);
if ($baseReal === false || strpos($target, $baseReal) !== 0) {
    deny();
}

// 3) 파일만 허용 + 확장자 화이트리스트
if (!is_file($target) || !is_readable($target)) deny(404, 'Not Found');
$ext = strtolower(pathinfo($target, PATHINFO_EXTENSION));
if (!in_array($ext, $ALLOW_EXT, true)) deny();

// 4) 헤더 (XSS/MIME 오용 방지)
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store, max-age=0');
header('Content-Security-Policy: default-src \'none\'; style-src \'unsafe-inline\';');

// 5) 코드 파일은 안전하게 “보기”만 제공 (실행 아님)
//    PHP는 highlight_file로 소스 하이라이트(실행되지 않음).
//    그 외 텍스트는 그대로 출력.
if ($ext === 'php') {
    header('Content-Type: text/html; charset=UTF-8');
    echo "<!doctype html><meta charset='utf-8'><title>source view</title>";
    echo "<pre>";
    // highlight_file은 파일 내용을 하이라이트된 HTML로 출력(실행하지 않음)
    highlight_file($target);
    echo "</pre>";
    exit;
}

// 텍스트형 파일 처리
$mime = 'text/plain; charset=UTF-8';
if ($ext === 'html') $mime = 'text/html; charset=UTF-8';
elseif ($ext === 'css') $mime = 'text/css; charset=UTF-8';
elseif ($ext === 'js') $mime = 'application/javascript; charset=UTF-8';

header('Content-Type: '.$mime);
readfile($target);
