<?php
/**
 * 작업지시서 목록 + PDF 출력 관리
 * Work Order List with PDF Print
 */


// POST 처리 (PDF 생성 요청)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'generate_pdf') {
        $workOrderId = isset($_POST['work_order_id']) ? intval($_POST['work_order_id']) : 0;

        if ($workOrderId > 0) {
            // PDF 생성 페이지로 리다이렉트 (새 창)
            $pdfUrl = '/doc/hq/workorder/wo_print.php?id=' . $workOrderId;
            header('Content-Type: application/json');
            echo json_encode(['result' => true, 'pdf_url' => $pdfUrl]);
            exit;
        } else {
            header('Content-Type: application/json');
            echo json_encode(['result' => false, 'error' => ['msg' => '작업지시서 ID가 필요합니다.']]);
            exit;
        }
    }
}

// 필터 파라미터 (POST 방식으로 변경)
$filterStatus = isset($_POST[encryptValue('status')]) ? $_POST[encryptValue('status')] : (isset($_GET['status']) ? $_GET['status'] : '');
$filterCustomer = isset($_POST[encryptValue('customer')]) ? $_POST[encryptValue('customer')] : (isset($_GET['customer']) ? $_GET['customer'] : '');
$filterItemType = isset($_POST[encryptValue('item_type')]) ? $_POST[encryptValue('item_type')] : (isset($_GET['item_type']) ? $_GET['item_type'] : '');
$searchKeyword = isset($_POST[encryptValue('search')]) ? $_POST[encryptValue('search')] : (isset($_GET['search']) ? $_GET['search'] : '');

// 작업지시서 목록 조회
$sql = "
SELECT
    wo.work_order_id,
    wo.customer_id,
    wo.item_type,
    wo.item_name,
    wo.quantity,
    wo.delivery_date,
    wo.status,
    wo.pdf_path,
    wo.created_at,
    c.company_name as customer_name,
    v.name as vendor_name
FROM work_orders wo
LEFT JOIN customers c ON wo.customer_id = c.customer_id
LEFT JOIN vendors v ON c.vendor_id = v.vendor_id
WHERE 1=1
";

$params = [];
$types = '';

if ($filterStatus) {
    $sql .= " AND wo.status = ?";
    $params[] = $filterStatus;
    $types .= 's';
}

if ($filterCustomer) {
    $sql .= " AND wo.customer_id = ?";
    $params[] = $filterCustomer;
    $types .= 's';
}

if ($filterItemType) {
    $sql .= " AND wo.item_type = ?";
    $params[] = $filterItemType;
    $types .= 's';
}

if ($searchKeyword) {
    $sql .= " AND (c.company_name LIKE ? OR wo.item_name LIKE ?)";
    $searchParam = "%{$searchKeyword}%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= 'ss';
}

$sql .= " ORDER BY wo.created_at DESC LIMIT 100";

$stmt = mysqli_prepare($con, $sql);

if ($types) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$workOrders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $workOrders[] = $row;
}

mysqli_stmt_close($stmt);

// 상태 배지 클래스
function getStatusBadgeClass($status) {
    $map = [
        'PENDING' => 'badge-warning',
        'IN_PROGRESS' => 'badge-info',
        'COMPLETED' => 'badge-success',
        'CANCELLED' => 'badge-secondary'
    ];
    return $map[$status] ?? 'badge-secondary';
}

// 상태 한글
function getStatusKr($status) {
    $map = [
        'PENDING' => '대기',
        'IN_PROGRESS' => '진행중',
        'COMPLETED' => '완료',
        'CANCELLED' => '취소'
    ];
    return $map[$status] ?? $status;
}

// 항목 타입 한글
function getItemTypeKr($type) {
    $map = [
        'DEVICE' => '기기',
        'SCENT' => '향 카트리지',
        'CONTENT' => '콘텐츠',
        'INSTALLATION' => '설치',
        'MAINTENANCE' => '유지보수',
        'OTHER' => '기타'
    ];
    return $map[$type] ?? $type;
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>작업지시서 관리 - HQ</title>
    <link rel="stylesheet" href="../../../css/common.css">
</head>
<body>
<div class="wrap">
  <section id="sec-workorder-list" class="card">
    <div class="card-hd">
      <div style="display: flex; flex-direction: column; gap: 20px; flex: 1;">
        <div style="display: flex; align-items: center; gap: 12px;">
          <div class="card-ttl">작업지시서 관리</div>
          <div class="card-sub">작업지시서 조회 및 PDF 출력</div>
        </div>
        <div class="row">
          <select id="filterStatus" class="form-control" style="max-width:120px">
            <option value="">전체 상태</option>
            <option value="PENDING" <?php echo $filterStatus === 'PENDING' ? 'selected' : ''; ?>>대기</option>
            <option value="IN_PROGRESS" <?php echo $filterStatus === 'IN_PROGRESS' ? 'selected' : ''; ?>>진행중</option>
            <option value="COMPLETED" <?php echo $filterStatus === 'COMPLETED' ? 'selected' : ''; ?>>완료</option>
            <option value="CANCELLED" <?php echo $filterStatus === 'CANCELLED' ? 'selected' : ''; ?>>취소</option>
          </select>
          <select id="filterItemType" class="form-control" style="max-width:150px">
            <option value="">전체 항목</option>
            <option value="DEVICE" <?php echo $filterItemType === 'DEVICE' ? 'selected' : ''; ?>>기기</option>
            <option value="SCENT" <?php echo $filterItemType === 'SCENT' ? 'selected' : ''; ?>>향 카트리지</option>
            <option value="CONTENT" <?php echo $filterItemType === 'CONTENT' ? 'selected' : ''; ?>>콘텐츠</option>
            <option value="OTHER" <?php echo $filterItemType === 'OTHER' ? 'selected' : ''; ?>>기타</option>
          </select>
          <input type="text" id="searchKeyword" class="form-control" placeholder="고객명/품목 검색" style="max-width:200px" value="<?php echo htmlspecialchars($searchKeyword); ?>">
          <button id="btnFilter" class="btn">조회</button>
        </div>
      </div>
      <div class="row">
        <button id="btnExportCsv" class="btn">CSV 내보내기</button>
      </div>
    </div>
    <div class="card-bd">
      <div class="table-wrap">
        <table class="table" id="tblWorkOrders">
          <thead>
            <tr>
              <th>작업지시서 ID</th>
              <th>고객명</th>
              <th>항목구분</th>
              <th>품목명</th>
              <th>수량</th>
              <th>배송예정일</th>
              <th>상태</th>
              <th>발행일</th>
              <th>PDF</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($workOrders)): ?>
            <tr>
              <td colspan="9" style="text-align:center; padding:40px;">조회된 작업지시서가 없습니다.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($workOrders as $wo): ?>
            <tr>
              <td><?php echo htmlspecialchars($wo['work_order_id']); ?></td>
              <td><strong><?php echo htmlspecialchars($wo['customer_name']); ?></strong></td>
              <td><?php echo htmlspecialchars(getItemTypeKr($wo['item_type'])); ?></td>
              <td><?php echo htmlspecialchars($wo['item_name']); ?></td>
              <td><?php echo htmlspecialchars($wo['quantity']); ?></td>
              <td><?php echo $wo['delivery_date'] ? date('Y-m-d', strtotime($wo['delivery_date'])) : '-'; ?></td>
              <td>
                <span class="badge <?php echo getStatusBadgeClass($wo['status']); ?>">
                  <?php echo htmlspecialchars(getStatusKr($wo['status'])); ?>
                </span>
              </td>
              <td><?php echo date('Y-m-d', strtotime($wo['created_at'])); ?></td>
              <td>
                <?php if ($wo['pdf_path']): ?>
                  <a href="/<?php echo htmlspecialchars($wo['pdf_path']); ?>" target="_blank" class="btn-sm btn-view">보기</a>
                <?php endif; ?>
                <button class="btn-sm btn-print-pdf" data-id="<?php echo $wo['work_order_id']; ?>">출력</button>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

<script>
// 필터 조회
document.getElementById('btnFilter')?.addEventListener('click', function() {
  const status = document.getElementById('filterStatus').value;
  const itemType = document.getElementById('filterItemType').value;
  const search = document.getElementById('searchKeyword').value;

  // 암호화된 POST 데이터 생성
  const data = {};
  if (status) data['<?= encryptValue('status') ?>'] = status;
  if (itemType) data['<?= encryptValue('item_type') ?>'] = itemType;
  if (search) data['<?= encryptValue('search') ?>'] = search;

  // updateAjaxContent로 페이지 다시 로드
  updateAjaxContent(data, function(response) {
    if (response.result === 'ok' && response.html) {
      const contentArea = document.querySelector('#sec-work-order-list').parentElement;
      if (contentArea) {
        contentArea.innerHTML = response.html;
        // 스크립트 재실행
        const scripts = contentArea.querySelectorAll('script');
        scripts.forEach(script => {
          try {
            (new Function(script.textContent))();
          } catch (e) {
            console.error('스크립트 실행 오류:', e);
          }
        });
      }
    }
  }, false);
});

// 엔터키 검색
document.getElementById('searchKeyword')?.addEventListener('keypress', function(e) {
  if (e.key === 'Enter') {
    document.getElementById('btnFilter').click();
  }
});

// 프린트 출력
document.querySelectorAll('.btn-print-pdf').forEach(btn => {
  btn.addEventListener('click', function() {
    const workOrderId = this.getAttribute('data-id');
    const printUrl = '/doc/hq/workorder/wo_print_simple.php?id=' + workOrderId;

    // 새 창으로 프린트 뷰 열기
    window.open(printUrl, '_blank', 'width=1000,height=900,scrollbars=yes');
  });
});

// CSV 내보내기
document.getElementById('btnExportCsv')?.addEventListener('click', () => {
  const table = document.getElementById('tblWorkOrders');
  const rows = Array.from(table.querySelectorAll('thead tr, tbody tr'));

  const csv = rows.map(row => {
    const cells = Array.from(row.querySelectorAll('th, td'));
    return cells.slice(0, -1).map(cell => {
      if (cell.querySelector('button')) return '';
      const badge = cell.querySelector('.badge');
      if (badge) return badge.textContent.trim();
      return '"' + cell.textContent.trim().replace(/"/g, '""') + '"';
    }).filter(Boolean).join(',');
  }).join('\n');

  const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = 'HQ_작업지시서_' + new Date().toISOString().slice(0,10) + '.csv';
  link.click();
});
</script>

<style>
.btn-view {
  background-color: #28a745;
  color: white;
  margin-right: 4px;
}

.btn-view:hover {
  background-color: #218838;
}

.btn-print-pdf {
  background-color: #1976d2;
  color: white;
}

.btn-print-pdf:hover {
  background-color: #1565c0;
}
</style>
</body>
</html>
