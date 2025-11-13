<?php
/**
 * 작업지시서 PDF 출력
 * Work Order Print with mPDF
 *
 * URL: /doc/hq/workorder/wo_print.php?id=[작업지시서ID]
 * PDF 저장 경로: /files/workorder/YY/[작업지시서번호].pdf
 */


require_once dirname(__DIR__, 3) . '/utility/autoload.php';

use Mpdf\Mpdf;

// 작업지시서 ID 확인
$workOrderId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($workOrderId <= 0) {
    die('작업지시서 ID가 필요합니다.');
}

// 작업지시서 데이터 조회
$sql = "
SELECT
    wo.work_order_id,
    wo.customer_id,
    wo.item_type,
    wo.item_name,
    wo.quantity,
    wo.delivery_address,
    wo.delivery_date,
    wo.status,
    wo.notes,
    wo.requested_by,
    wo.created_at,
    c.company_name as customer_name,
    c.phone as customer_phone,
    c.email as customer_email,
    v.name as vendor_name,
    u.name as requester_name
FROM work_orders wo
LEFT JOIN customers c ON wo.customer_id = c.customer_id
LEFT JOIN vendors v ON c.vendor_id = v.vendor_id
LEFT JOIN users u ON wo.requested_by = u.user_id
WHERE wo.work_order_id = ?
";

$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, 'i', $workOrderId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$wo = mysqli_fetch_assoc($result);

if (!$wo) {
    die('작업지시서를 찾을 수 없습니다.');
}

mysqli_stmt_close($stmt);

// 작업지시서 번호 생성 (예: WO202511070001)
$woNumber = 'WO' . date('Ymd', strtotime($wo['created_at'])) . str_pad($wo['work_order_id'], 4, '0', STR_PAD_LEFT);

// PDF 저장 경로 설정
$year = date('y', strtotime($wo['created_at'])); // 년도 뒤 2자리
$pdfDir = dirname(__DIR__, 4) . '/files/workorder/' . $year;
$pdfFileName = $woNumber . '.pdf';
$pdfPath = $pdfDir . '/' . $pdfFileName;

// 디렉토리 생성
if (!is_dir($pdfDir)) {
    mkdir($pdfDir, 0755, true);
}

// 상태 한글 변환
$statusMap = [
    'PENDING' => '대기',
    'IN_PROGRESS' => '진행중',
    'COMPLETED' => '완료',
    'CANCELLED' => '취소'
];
$statusKr = $statusMap[$wo['status']] ?? $wo['status'];

// 항목 타입 한글 변환
$itemTypeMap = [
    'DEVICE' => '기기',
    'SCENT' => '향 카트리지',
    'CONTENT' => '콘텐츠 프린팅',
    'INSTALLATION' => '설치',
    'MAINTENANCE' => '유지보수',
    'OTHER' => '기타'
];
$itemTypeKr = $itemTypeMap[$wo['item_type']] ?? $wo['item_type'];

// HTML 템플릿 생성
$html = '
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>작업지시서 - ' . htmlspecialchars($woNumber) . '</title>
    <style>
        @page {
            margin: 15mm;
        }
        body {
            font-family: "Malgun Gothic", "맑은 고딕", sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 24pt;
            font-weight: bold;
            margin: 0 0 5px 0;
            color: #1976d2;
        }
        .header .subtitle {
            font-size: 11pt;
            color: #666;
        }
        .info-section {
            margin-bottom: 15px;
        }
        .info-section h2 {
            font-size: 12pt;
            font-weight: bold;
            background-color: #e3f2fd;
            padding: 8px 10px;
            margin: 0 0 10px 0;
            border-left: 4px solid #1976d2;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
            width: 25%;
        }
        table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .item-table {
            margin-top: 10px;
        }
        .item-table th {
            background-color: #e3f2fd;
            text-align: center;
        }
        .item-table td {
            text-align: center;
        }
        .notes-box {
            border: 1px solid #ddd;
            padding: 10px;
            background-color: #fafafa;
            min-height: 60px;
            margin-top: 10px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #ddd;
            text-align: center;
            font-size: 9pt;
            color: #999;
        }
        .signature-area {
            margin-top: 30px;
            display: table;
            width: 100%;
        }
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 10px;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            width: 150px;
            margin: 20px auto 5px auto;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 10pt;
        }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-progress { background-color: #cfe2ff; color: #084298; }
        .status-completed { background-color: #d1e7dd; color: #0f5132; }
        .status-cancelled { background-color: #f8d7da; color: #842029; }
    </style>
</head>
<body>
    <div class="header">
        <h1>작업 지시서</h1>
        <div class="subtitle">WORK ORDER</div>
    </div>

    <div class="info-section">
        <table>
            <tr>
                <th>작업지시서 번호</th>
                <td><strong>' . htmlspecialchars($woNumber) . '</strong></td>
                <th>발행일자</th>
                <td>' . date('Y-m-d', strtotime($wo['created_at'])) . '</td>
            </tr>
            <tr>
                <th>상태</th>
                <td>
                    <span class="status-badge status-' . strtolower($wo['status']) . '">' . htmlspecialchars($statusKr) . '</span>
                </td>
                <th>배송 예정일</th>
                <td>' . ($wo['delivery_date'] ? date('Y-m-d', strtotime($wo['delivery_date'])) : '-') . '</td>
            </tr>
        </table>
    </div>

    <div class="info-section">
        <h2>고객 정보</h2>
        <table>
            <tr>
                <th>고객명 (회사명)</th>
                <td colspan="3"><strong>' . htmlspecialchars($wo['customer_name']) . '</strong></td>
            </tr>
            <tr>
                <th>고객 ID</th>
                <td>' . htmlspecialchars($wo['customer_id']) . '</td>
                <th>담당 벤더</th>
                <td>' . htmlspecialchars($wo['vendor_name'] ?? '-') . '</td>
            </tr>
            <tr>
                <th>연락처</th>
                <td>' . htmlspecialchars($wo['customer_phone'] ?? '-') . '</td>
                <th>이메일</th>
                <td>' . htmlspecialchars($wo['customer_email'] ?? '-') . '</td>
            </tr>
            <tr>
                <th>배송 주소</th>
                <td colspan="3">' . nl2br(htmlspecialchars($wo['delivery_address'])) . '</td>
            </tr>
        </table>
    </div>

    <div class="info-section">
        <h2>작업 내용</h2>
        <table class="item-table">
            <thead>
                <tr>
                    <th>항목 구분</th>
                    <th>품목명</th>
                    <th>수량</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>' . htmlspecialchars($itemTypeKr) . '</td>
                    <td>' . htmlspecialchars($wo['item_name']) . '</td>
                    <td><strong>' . htmlspecialchars($wo['quantity']) . '</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="info-section">
        <h2>특이사항 / 비고</h2>
        <div class="notes-box">
            ' . nl2br(htmlspecialchars($wo['notes'] ?: '없음')) . '
        </div>
    </div>

    <div class="info-section">
        <table>
            <tr>
                <th>요청자</th>
                <td>' . htmlspecialchars($wo['requester_name'] ?? '-') . '</td>
                <th>요청일시</th>
                <td>' . date('Y-m-d H:i', strtotime($wo['created_at'])) . '</td>
            </tr>
        </table>
    </div>

    <div class="signature-area">
        <div class="signature-box">
            <div>작성자</div>
            <div class="signature-line"></div>
            <div>(서명)</div>
        </div>
        <div class="signature-box">
            <div>확인자</div>
            <div class="signature-line"></div>
            <div>(서명)</div>
        </div>
    </div>

    <div class="footer">
        <p>본 작업지시서는 자동으로 생성되었습니다.</p>
        <p>문의: 본사 운영팀 | 발행일시: ' . date('Y-m-d H:i:s') . '</p>
    </div>
</body>
</html>
';

// mPDF 설정
$mpdf = new Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'margin_left' => 15,
    'margin_right' => 15,
    'margin_top' => 15,
    'margin_bottom' => 15,
    'margin_header' => 0,
    'margin_footer' => 0,
    'default_font' => 'dejavusans'
]);

// PDF 메타데이터
$mpdf->SetTitle('작업지시서 - ' . $woNumber);
$mpdf->SetAuthor('Dispenser System');
$mpdf->SetCreator('mPDF');

// HTML을 PDF로 변환
$mpdf->WriteHTML($html);

// PDF 파일 저장
$mpdf->Output($pdfPath, 'F');

// 작업지시서 테이블에 PDF 경로 저장
$updateSql = "UPDATE work_orders SET pdf_path = ? WHERE work_order_id = ?";
$updateStmt = mysqli_prepare($con, $updateSql);
$relativePath = 'files/workorder/' . $year . '/' . $pdfFileName;
mysqli_stmt_bind_param($updateStmt, 'si', $relativePath, $workOrderId);
mysqli_stmt_execute($updateStmt);
mysqli_stmt_close($updateStmt);

// 브라우저에서 PDF 미리보기
$mpdf->Output($woNumber . '.pdf', 'I'); // I = Inline (브라우저에서 보기)

mysqli_close($con);
?>
