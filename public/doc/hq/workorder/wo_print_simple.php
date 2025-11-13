<?php
/**
 * 작업지시서 프린트 뷰 (HTML)
 * Work Order Print View (HTML version - no PDF library required)
 *
 * URL: /doc/hq/workorder/wo_print_simple.php?id=[작업지시서ID]
 * 브라우저 프린트 기능 사용 (Ctrl+P)
 */


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
mysqli_close($con);

// 작업지시서 번호 생성 (예: WO202511070001)
$woNumber = 'WO' . date('Ymd', strtotime($wo['created_at'])) . str_pad($wo['work_order_id'], 4, '0', STR_PAD_LEFT);

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
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>작업지시서 - <?php echo htmlspecialchars($woNumber); ?></title>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                margin: 0;
                padding: 15mm;
            }
            @page {
                margin: 0;
                size: A4;
            }
        }

        @media screen {
            body {
                background-color: #f5f5f5;
                padding: 20px;
            }
            .print-container {
                max-width: 210mm;
                margin: 0 auto;
                background: white;
                padding: 20mm;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
        }

        body {
            font-family: "Malgun Gothic", "맑은 고딕", sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #333;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 28pt;
            font-weight: bold;
            margin: 0 0 8px 0;
            color: #1976d2;
        }

        .header .subtitle {
            font-size: 12pt;
            color: #666;
            letter-spacing: 2px;
        }

        .info-section {
            margin-bottom: 20px;
        }

        .info-section h2 {
            font-size: 13pt;
            font-weight: bold;
            background-color: #e3f2fd;
            padding: 10px 12px;
            margin: 0 0 12px 0;
            border-left: 5px solid #1976d2;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: left;
            padding: 10px;
            border: 1px solid #ddd;
            width: 25%;
        }

        table td {
            padding: 10px;
            border: 1px solid #ddd;
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
            padding: 12px;
            background-color: #fafafa;
            min-height: 80px;
            margin-top: 12px;
            white-space: pre-wrap;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            text-align: center;
            font-size: 9pt;
            color: #999;
        }

        .signature-area {
            margin-top: 40px;
            display: flex;
            justify-content: space-around;
        }

        .signature-box {
            text-align: center;
            padding: 15px;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            width: 180px;
            margin: 30px auto 8px auto;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 11pt;
        }

        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-in_progress { background-color: #cfe2ff; color: #084298; }
        .status-completed { background-color: #d1e7dd; color: #0f5132; }
        .status-cancelled { background-color: #f8d7da; color: #842029; }

        .print-btn-area {
            text-align: center;
            margin-bottom: 20px;
        }

        .btn-print {
            background-color: #1976d2;
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-print:hover {
            background-color: #1565c0;
        }

        .btn-close {
            background-color: #666;
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 10px;
        }

        .btn-close:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
<div class="print-btn-area no-print">
    <button class="btn-print" onclick="window.print()">🖨️ 프린트</button>
    <button class="btn-close" onclick="window.close()">닫기</button>
</div>

<div class="print-container">
    <div class="header">
        <h1>작업 지시서</h1>
        <div class="subtitle">WORK ORDER</div>
    </div>

    <div class="info-section">
        <table>
            <tr>
                <th>작업지시서 번호</th>
                <td><strong style="font-size:12pt"><?php echo htmlspecialchars($woNumber); ?></strong></td>
                <th>발행일자</th>
                <td><?php echo date('Y-m-d', strtotime($wo['created_at'])); ?></td>
            </tr>
            <tr>
                <th>상태</th>
                <td>
                    <span class="status-badge status-<?php echo strtolower($wo['status']); ?>">
                        <?php echo htmlspecialchars($statusKr); ?>
                    </span>
                </td>
                <th>배송 예정일</th>
                <td><?php echo $wo['delivery_date'] ? date('Y-m-d', strtotime($wo['delivery_date'])) : '-'; ?></td>
            </tr>
        </table>
    </div>

    <div class="info-section">
        <h2>고객 정보</h2>
        <table>
            <tr>
                <th>고객명 (회사명)</th>
                <td colspan="3"><strong style="font-size:12pt"><?php echo htmlspecialchars($wo['customer_name']); ?></strong></td>
            </tr>
            <tr>
                <th>고객 ID</th>
                <td><?php echo htmlspecialchars($wo['customer_id']); ?></td>
                <th>담당 벤더</th>
                <td><?php echo htmlspecialchars($wo['vendor_name'] ?? '-'); ?></td>
            </tr>
            <tr>
                <th>연락처</th>
                <td><?php echo htmlspecialchars($wo['customer_phone'] ?? '-'); ?></td>
                <th>이메일</th>
                <td><?php echo htmlspecialchars($wo['customer_email'] ?? '-'); ?></td>
            </tr>
            <tr>
                <th>배송 주소</th>
                <td colspan="3"><?php echo nl2br(htmlspecialchars($wo['delivery_address'])); ?></td>
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
                    <td><?php echo htmlspecialchars($itemTypeKr); ?></td>
                    <td><strong><?php echo htmlspecialchars($wo['item_name']); ?></strong></td>
                    <td><strong style="font-size:12pt"><?php echo htmlspecialchars($wo['quantity']); ?></strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="info-section">
        <h2>특이사항 / 비고</h2>
        <div class="notes-box"><?php echo htmlspecialchars($wo['notes'] ?: '없음'); ?></div>
    </div>

    <div class="info-section">
        <table>
            <tr>
                <th>요청자</th>
                <td><?php echo htmlspecialchars($wo['requester_name'] ?? '-'); ?></td>
                <th>요청일시</th>
                <td><?php echo date('Y-m-d H:i', strtotime($wo['created_at'])); ?></td>
            </tr>
        </table>
    </div>

    <div class="signature-area">
        <div class="signature-box">
            <div><strong>작성자</strong></div>
            <div class="signature-line"></div>
            <div>(서명)</div>
        </div>
        <div class="signature-box">
            <div><strong>확인자</strong></div>
            <div class="signature-line"></div>
            <div>(서명)</div>
        </div>
    </div>

    <div class="footer">
        <p>본 작업지시서는 시스템에서 자동으로 생성되었습니다.</p>
        <p>문의: 본사 운영팀 | 발행일시: <?php echo date('Y-m-d H:i:s'); ?></p>
        <p style="margin-top:10px; font-size:8pt">© Dispenser System. All rights reserved.</p>
    </div>
</div>

<script>
// 페이지 로드 시 자동 프린트 (선택사항)
// window.addEventListener('load', function() {
//     setTimeout(function() {
//         window.print();
//     }, 500);
// });
</script>
</body>
</html>
