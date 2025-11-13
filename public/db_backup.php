<?php
/**
 * DB 백업/복원 유틸리티
 * schema.sql 파일로 백업 및 복원
 */
require_once __DIR__ . '/inc/common.php';

// HQ 권한 체크
/*if (!isset($_SESSION['portal']) || $_SESSION['portal'] !== 'hq') {
    header('Location: /');
    exit;
}*/

$message = '';
$messageType = '';

// 전역 백업 파일 경로 설정 (한 번만 실행)
// DOCUMENT_ROOT와 __DIR__을 비교하여 동적으로 경로 설정
$publicDir = __DIR__;
$docRoot = $_SERVER['DOCUMENT_ROOT'];

// DOCUMENT_ROOT가 /public으로 끝나는지 확인
if (basename($docRoot) === 'public') {
    // 로컬: DOCUMENT_ROOT = C:/php/dispenser/public
    // schema.sql은 C:/php/dispenser/schema.sql에 위치
    $backupFile = dirname($docRoot) . '/schema.sql';
} else {
    // 서버: DOCUMENT_ROOT = D:/php/public_html/alltogreen
    // schema.sql은 D:\php\public_html\alltogreen\dispenser/schema.sql에 위치
    $backupFile = $publicDir . '/schema.sql';
}

// 백업 처리
if (isset($_POST['action']) && $_POST['action'] === 'backup') {
    try {
        $backupContent = "-- Database Backup\n";
        $backupContent .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";

        // 모든 테이블 목록 조회
        $tables = [];
        $result = mysqli_query($con, "SHOW TABLES");
        while ($row = mysqli_fetch_array($result)) {
            $tables[] = $row[0];
        }

        // 각 테이블에 대해 CREATE TABLE 및 INSERT 문 생성
        foreach ($tables as $table) {
            // DROP TABLE
            $backupContent .= "DROP TABLE IF EXISTS `$table`;\n";

            // CREATE TABLE
            $createTable = mysqli_query($con, "SHOW CREATE TABLE `$table`");
            $createRow = mysqli_fetch_row($createTable);
            $backupContent .= $createRow[1] . ";\n\n";

            // INSERT DATA
            $rows = mysqli_query($con, "SELECT * FROM `$table`");
            $numFields = mysqli_num_fields($rows);

            while ($row = mysqli_fetch_row($rows)) {
                $backupContent .= "INSERT INTO `$table` VALUES(";
                for ($i = 0; $i < $numFields; $i++) {
                    $row[$i] = str_replace("\n", "\\n", addslashes($row[$i]));
                    if (isset($row[$i])) {
                        $backupContent .= '"' . $row[$i] . '"';
                    } else {
                        $backupContent .= 'NULL';
                    }
                    if ($i < ($numFields - 1)) {
                        $backupContent .= ',';
                    }
                }
                $backupContent .= ");\n";
            }
            $backupContent .= "\n";
        }

        // 파일 저장
        if (file_put_contents($backupFile, $backupContent)) {
            $message = '데이터베이스가 성공적으로 백업되었습니다. (' . count($tables) . '개 테이블)';
            $messageType = 'success';
        } else {
            $message = '백업 파일 저장에 실패했습니다.';
            $messageType = 'error';
        }
    } catch (Exception $e) {
        $message = '백업 중 오류 발생: ' . $e->getMessage();
        $messageType = 'error';
    }
}

// 복원 처리
if (isset($_POST['action']) && $_POST['action'] === 'restore') {
    try {
        if (!file_exists($backupFile)) {
            throw new Exception('schema.sql 파일을 찾을 수 없습니다.');
        }

        // SQL 파일 읽기
        $sql = file_get_contents($backupFile);

        if ($sql === false) {
            throw new Exception('schema.sql 파일을 읽을 수 없습니다.');
        }

        // 복원 전 설정: 외래키 체크 비활성화 및 안전 모드 설정
        mysqli_query($con, "SET FOREIGN_KEY_CHECKS=0");
        mysqli_query($con, "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO'");
        mysqli_query($con, "SET AUTOCOMMIT=0");

        // 시작 전 에러 상태 초기화
        while (mysqli_more_results($con)) {
            mysqli_next_result($con);
        }

        // 여러 쿼리 실행
        if (!mysqli_multi_query($con, $sql)) {
            throw new Exception('SQL 실행 실패: ' . mysqli_error($con));
        }

        // 모든 쿼리 결과 처리
        $errorOccurred = false;
        $errorMessage = '';

        do {
            if ($result = mysqli_store_result($con)) {
                mysqli_free_result($result);
            }

            // 심각한 에러만 체크 (테이블 존재, DROP 경고는 무시)
            if (mysqli_errno($con)) {
                $errNo = mysqli_errno($con);
                $errMsg = mysqli_error($con);

                // 무시할 에러 코드
                // 1050: Table already exists (DROP IF EXISTS 후 발생 가능)
                // 1051: Unknown table (DROP IF EXISTS에서 발생 가능)
                // 1091: Can't DROP - check that column/key exists
                $ignorableErrors = [1050, 1051, 1091];

                if (!in_array($errNo, $ignorableErrors)) {
                    $errorOccurred = true;
                    $errorMessage = "MySQL 오류 ({$errNo}): {$errMsg}";
                    break;
                }
            }
        } while (mysqli_more_results($con) && mysqli_next_result($con));

        if ($errorOccurred) {
            // 롤백
            mysqli_query($con, "ROLLBACK");
            mysqli_query($con, "SET FOREIGN_KEY_CHECKS=1");
            throw new Exception($errorMessage);
        }

        // 커밋 및 복원
        mysqli_query($con, "COMMIT");
        mysqli_query($con, "SET FOREIGN_KEY_CHECKS=1");
        mysqli_query($con, "SET AUTOCOMMIT=1");

        $message = '데이터베이스가 성공적으로 복원되었습니다.';
        $messageType = 'success';
    } catch (Exception $e) {
        // 에러 발생 시 설정 복원
        @mysqli_query($con, "ROLLBACK");
        @mysqli_query($con, "SET FOREIGN_KEY_CHECKS=1");
        @mysqli_query($con, "SET AUTOCOMMIT=1");

        $message = '복원 중 오류 발생: ' . $e->getMessage();
        $messageType = 'error';
    }
}

// schema.sql 파일 정보 (전역 $backupFile 사용)
$schemaFile = $backupFile;
$fileExists = file_exists($schemaFile);
$fileSize = $fileExists ? filesize($schemaFile) : 0;
$fileDate = $fileExists ? date('Y-m-d H:i:s', filemtime($schemaFile)) : '-';

// 현재 DB 정보
$tableCount = 0;
$result = mysqli_query($con, "SHOW TABLES");
if ($result) {
    $tableCount = mysqli_num_rows($result);
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DB 백업/복원 - Dispenser HQ</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Noto Sans KR', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            width: 100%;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .content {
            padding: 40px;
        }

        .alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert::before {
            content: '✓';
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
        }

        .alert-success::before {
            background: #10b981;
            color: white;
        }

        .alert-error::before {
            content: '✕';
            background: #ef4444;
            color: white;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .info-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
        }

        .info-card-label {
            font-size: 12px;
            color: #6b7280;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .info-card-value {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
        }

        .info-card-sub {
            font-size: 13px;
            color: #9ca3af;
            margin-top: 4px;
        }

        .action-section {
            background: #f9fafb;
            border: 2px dashed #d1d5db;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 20px;
        }

        .action-title {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .action-desc {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .btn-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .btn::before {
            font-size: 18px;
        }

        .btn-backup::before {
            content: '💾';
        }

        .btn-restore::before {
            content: '🔄';
        }

        .btn-home::before {
            content: '🏠';
        }

        .warning {
            background: #fef3c7;
            border: 1px solid #fbbf24;
            color: #92400e;
            padding: 16px;
            border-radius: 8px;
            font-size: 13px;
            margin-top: 20px;
            line-height: 1.6;
        }

        .warning strong {
            display: block;
            margin-bottom: 4px;
            font-size: 14px;
        }

        @media (max-width: 640px) {
            .content {
                padding: 24px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .btn-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>데이터베이스 백업/복원</h1>
            <p>schema.sql 파일을 이용한 데이터베이스 관리</p>
        </div>

        <div class="content">
            <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>

            <div class="info-grid">
                <div class="info-card">
                    <div class="info-card-label">현재 테이블 수</div>
                    <div class="info-card-value"><?php echo $tableCount; ?>개</div>
                    <div class="info-card-sub">데이터베이스</div>
                </div>

                <div class="info-card">
                    <div class="info-card-label">백업 파일 상태</div>
                    <div class="info-card-value"><?php echo $fileExists ? '존재' : '없음'; ?></div>
                    <div class="info-card-sub"><?php echo $fileExists ? number_format($fileSize / 1024, 2) . ' KB' : '-'; ?></div>
                </div>

                <div class="info-card">
                    <div class="info-card-label">최종 백업 일시</div>
                    <div class="info-card-value" style="font-size: 16px;"><?php echo $fileDate !== '-' ? date('m/d H:i', filemtime($schemaFile)) : '-'; ?></div>
                    <div class="info-card-sub"><?php echo $fileDate !== '-' ? date('Y년', filemtime($schemaFile)) : '백업 없음'; ?></div>
                </div>
            </div>

            <div class="action-section">
                <div class="action-title">💾 데이터베이스 백업</div>
                <div class="action-desc">
                    현재 데이터베이스의 모든 테이블과 데이터를 <?= $backupFile ?> 파일로 백업합니다.
                    기존 schema.sql 파일이 있으면 덮어씌워집니다.
                </div>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="backup">
                    <button type="submit" class="btn btn-success btn-backup" onclick="return confirm('데이터베이스를 백업하시겠습니까?');">
                        백업 실행
                    </button>
                </form>
            </div>

            <div class="action-section">
                <div class="action-title">🔄 데이터베이스 복원</div>
                <div class="action-desc">
                    schema.sql 파일의 내용으로 데이터베이스를 복원합니다.
                    <strong>현재 데이터베이스의 모든 테이블이 삭제되고 백업 파일의 내용으로 대체됩니다.</strong>
                </div>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="restore">
                    <button type="submit" class="btn btn-danger btn-restore"
                            onclick="return confirm('⚠️ 경고!\n\n현재 데이터베이스의 모든 데이터가 삭제되고\nschema.sql 파일의 내용으로 복원됩니다.\n\n정말 복원하시겠습니까?');"
                            <?php echo !$fileExists ? 'disabled' : ''; ?>>
                        <?php echo $fileExists ? '복원 실행' : '백업 파일 없음'; ?>
                    </button>
                </form>

                <?php if (!$fileExists): ?>
                <div class="warning">
                    <strong>⚠️ 백업 파일이 없습니다</strong>
                    복원을 실행하려면 먼저 백업을 실행하거나 schema.sql 파일을 프로젝트 루트에 업로드하세요.
                </div>
                <?php endif; ?>
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <a href="/" class="btn btn-secondary btn-home">
                    홈으로 돌아가기
                </a>
            </div>

            <div class="warning">
                <strong>⚠️ 주의사항</strong>
                이 기능은 관리자 전용입니다. 복원 작업은 되돌릴 수 없으므로 반드시 백업을 먼저 실행한 후 복원하시기 바랍니다.
                대용량 데이터베이스의 경우 작업에 시간이 소요될 수 있습니다.
            </div>
        </div>
    </div>
</body>
</html>
