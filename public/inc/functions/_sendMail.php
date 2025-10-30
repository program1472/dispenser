<?php
if (!defined('DISPENSER')) exit; // 개별 페이지 접근 불가

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// PHPMailer 로딩
require ROOT.'/doc/PHPMailer/src/Exception.php';
require ROOT.'/doc/PHPMailer/src/PHPMailer.php';
require ROOT.'/doc/PHPMailer/src/SMTP.php';

$response = ['result' => false, 'message' => "", 'log' => []];

// POST 값 수신
$title   = $_POST['title'] ?? '';
$body    = $_POST['body'] ?? '';
$name    = $_POST['name'] ?? '사랑하는 고객님';
$address = $_POST['address'] ?? 'samatg@aserpacific.com';
$filelist = isset($_POST['filelist']) ? explode(',', $_POST['filelist']) : [];
$response['sendData']['title'] = $title;
$response['sendData']['body'] = $body;
$response['sendData']['name'] = $name;
$response['sendData']['address'] = $address;
$response['sendData']['filelist'] = $filelist;

// 메일 기본 정보
$mail_from     = getSetting('SMTP', 'username', 'samatg@aserpacific.com');
$mail_password = getSetting('SMTP', 'password', '!!golf2015@@');
$mail_smtp     = getSetting('SMTP', 'host', 'smtp.cafe24.com');
$mail_port     = getSetting('SMTP', 'port', '587');
$mail_secure     = getSetting('SMTP', 'secure', '');
$response['info']['mail_from'] = $mail_from;
$response['info']['mail_password'] = $mail_password;
$response['info']['mail_smtp'] = $mail_smtp;
$response['info']['mail_port'] = $mail_port;

    // 보안 프로토콜 보정
if ($mail_secure === '' && $mail_port === 465) {
	$mail_secure = 'ssl';
}
if ($mail_secure !== 'ssl' || $mail_secure !== 'tls') {
	$mail_secure = false;
}
$mail = new PHPMailer(true);
try {
    // SMTP 설정
    $mail->isSMTP();
    $response['log'][] = "SMTP 모드 설정 완료";

    $mail->Host       = $mail_smtp;
    $response['log'][] = "SMTP 호스트 설정: {$mail_smtp}";

    $mail->SMTPAuth   = true;
    $response['log'][] = "SMTP 인증 활성화";

    $mail->Username   = $mail_from;
    $response['log'][] = "SMTP 사용자명 설정: {$mail_from}";

    $mail->Password   = $mail_password;
    $response['log'][] = "SMTP 비밀번호 설정";

    $mail->Port       = $mail_port;
    $response['log'][] = "SMTP 포트 설정: {$mail_port}";

    $mail->SMTPSecure = $mail_secure;
    $response['log'][] = "SMTPSecure 설정: {$mail_secure}";

    $mail->CharSet = 'UTF-8';
    $response['log'][] = "문자셋 UTF-8 설정";

    // 발신자 / 수신자
	$mail_from = getSetting('SMTP', 'from_email', 'samatg@aserpacific.com');
    $mail->setFrom($mail_from, getSetting('SMTP', 'from_name', '올투그린'));
    $response['log'][] = "발신자 설정: 올투그린 <{$mail_from}>";

    $toAddress = ($address === $mail_from)
        ? "시스템 <{$address}>"
        : "{$name} <{$address}>";

	if (IS_DEBUG) $address = getSetting('SMTP', 'reply_to', 'program1472@naver.com');

    $mail->addAddress($address, $name);
    $response['log'][] = "수신자 설정: {$toAddress}";

    //$mail->addCC('samatg@aserpacific.com');

    // 제목/내용
    $mail->isHTML(true);
    $response['log'][] = "HTML 메일 설정";

    $mail->Subject = $title;
    $response['log'][] = "메일 제목 설정: {$title}";

    $mail->Body    = $body;
    $response['log'][] = "메일 본문 설정 완료";

    // 첨부파일
    foreach ($filelist as $file) {
        $file = trim($file);
        if ($file && file_exists($file)) {
            $mail->addAttachment($file);
            $response['log'][] = "첨부파일 추가: {$file}";
        } else {
            $response['log'][] = "첨부파일 누락 또는 존재하지 않음: {$file}";
        }
    }

    // 전송
    $mail->send();
    $response['result'] = true;
    $response['message'] = '메일 전송 성공';
    $response['log'][] = "메일 전송 완료";
} catch (Exception $e) {
    $response['result'] = false;
    $response['message'] = $mail->ErrorInfo;
    $response['log'][] = "메일 전송 실패: ".$mail->ErrorInfo;
}

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

?>