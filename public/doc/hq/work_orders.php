<?php
/**
 * HQ 출고관리 > 작업지시서
 * 작업지시서 생성 및 관리
 */

// 필터 파라미터 (POST는 이미 _ajax_.php에서 복호화됨)
$searchKeyword = isset($_POST['search']) ? $_POST['search'] : (isset($_GET['search']) ? $_GET['search'] : '');
$statusFilter = isset($_POST['status']) ? $_POST['status'] : (isset($_GET['status']) ? $_GET['status'] : '');
$vendorFilter = isset($_POST['vendor']) ? $_POST['vendor'] : (isset($_GET['vendor']) ? $_GET['vendor'] : '');
$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : (isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01'));
$endDate = isset($_POST['end_date']) ? $_POST['end_date'] : (isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t'));

// 작업지시서 목록 조회
$sql = "
SELECT
    wo.*,
    c.company_name as customer_name,
    v.company_name as vendor_name,
    u.name as sales_rep_name,
    creator.name as created_by_name
FROM work_orders wo
INNER JOIN customers c ON wo.customer_id = c.customer_id
LEFT JOIN vendors v ON wo.vendor_id = v.vendor_id AND v.deleted_at IS NULL
LEFT JOIN users u ON wo.sales_rep_id = u.user_id AND u.deleted_at IS NULL
LEFT JOIN users creator ON wo.created_by = creator.user_id AND creator.deleted_at IS NULL
WHERE wo.order_date BETWEEN '{$startDate}' AND '{$endDate}'
";

if ($searchKeyword) {
    $sql .= " AND (wo.work_order_id LIKE '%" . mysqli_real_escape_string($con, $searchKeyword) . "%'
               OR c.company_name LIKE '%" . mysqli_real_escape_string($con, $searchKeyword) . "%'
               OR wo.tracking_number LIKE '%" . mysqli_real_escape_string($con, $searchKeyword) . "%')";
}

if ($statusFilter) {
    $sql .= " AND wo.status = '" . mysqli_real_escape_string($con, $statusFilter) . "'";
}

if ($vendorFilter) {
    $sql .= " AND wo.vendor_id = '" . mysqli_real_escape_string($con, $vendorFilter) . "'";
}

$sql .= " ORDER BY wo.order_date DESC, wo.created_at DESC LIMIT 100";

$result = mysqli_query($con, $sql);

$workOrders = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $workOrders[] = $row;
    }
}

// 벤더 목록 (필터용)
$vendorListSql = "SELECT vendor_id, company_name as name FROM vendors WHERE is_active = 1 AND deleted_at IS NULL ORDER BY company_name";
$vendorListResult = mysqli_query($con, $vendorListSql);
$vendors = [];
if ($vendorListResult) {
    while ($row = mysqli_fetch_assoc($vendorListResult)) {
        $vendors[] = $row;
    }
}

// 상태 레이블 매핑
$statusLabels = [
    'PENDING' => '대기',
    'PREPARING' => '준비중',
    'READY' => '준비완료',
    'SHIPPED' => '출고',
    'DELIVERED' => '배송완료',
    'CANCELLED' => '취소'
];

$statusBadges = [
    'PENDING' => 'badge-secondary',
    'PREPARING' => 'badge-warning',
    'READY' => 'badge-due',
    'SHIPPED' => 'badge-in-progress',
    'DELIVERED' => 'badge-success',
    'CANCELLED' => 'badge-danger'
];

// HTML 버퍼링 시작
ob_start();
?>

<div class="wrap">
  <section id="sec-work-orders" class="card">
    <!-- 필터 -->
    <div class="card-hd">
      <div class="card-ttl">작업지시서 관리</div>
      <span class="card-sub">출고 작업지시서 생성 및 관리</span>
    </div>

    <div class="card-bd">
      <div class="row">
        <select id="vendorFilter" class="input">
          <option value="">전체 벤더</option>
          <?php foreach ($vendors as $vendor): ?>
          <option value="<?php echo htmlspecialchars($vendor['vendor_id']); ?>"
                  <?php echo $vendorFilter === $vendor['vendor_id'] ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($vendor['name']); ?>
          </option>
          <?php endforeach; ?>
        </select>

        <select id="statusFilter" class="input">
          <option value="">전체 상태</option>
          <option value="PENDING" <?php echo $statusFilter === 'PENDING' ? 'selected' : ''; ?>>대기</option>
          <option value="PREPARING" <?php echo $statusFilter === 'PREPARING' ? 'selected' : ''; ?>>준비중</option>
          <option value="READY" <?php echo $statusFilter === 'READY' ? 'selected' : ''; ?>>준비완료</option>
          <option value="SHIPPED" <?php echo $statusFilter === 'SHIPPED' ? 'selected' : ''; ?>>출고</option>
          <option value="DELIVERED" <?php echo $statusFilter === 'DELIVERED' ? 'selected' : ''; ?>>배송완료</option>
          <option value="CANCELLED" <?php echo $statusFilter === 'CANCELLED' ? 'selected' : ''; ?>>취소</option>
        </select>

        <input type="date" id="startDate" class="input" value="<?php echo htmlspecialchars($startDate); ?>">
        <input type="date" id="endDate" class="input" value="<?php echo htmlspecialchars($endDate); ?>">
        <input type="text" id="searchKeyword" class="input" placeholder="지시서ID/고객명/송장번호" value="<?php echo htmlspecialchars($searchKeyword); ?>">

        <button id="btnFilter" class="btn primary">조회</button>
        <button id="btnAddWorkOrder" class="btn primary">신규 작업지시서</button>
        <button id="btnExportCsv" class="btn">CSV 내보내기</button>
      </div>
    </div>

    <!-- 작업지시서 목록 테이블 -->
    <div class="card-bd">
      <div class="table-wrap">
        <table class="table" id="tblWorkOrders">
          <thead>
            <tr>
              <th>지시서ID</th>
              <th>고객명</th>
              <th>담당벤더</th>
              <th>담당영업</th>
              <th>지시일</th>
              <th>출고예정일</th>
              <th>상태</th>
              <th>기기</th>
              <th>향</th>
              <th>콘텐츠</th>
              <th>송장번호</th>
              <th>관리</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($workOrders)): ?>
            <tr>
              <td colspan="12" class="table-text-center">등록된 작업지시서가 없습니다.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($workOrders as $wo): ?>
            <tr>
              <td><strong><?php echo htmlspecialchars($wo['work_order_id']); ?></strong></td>
              <td><?php echo htmlspecialchars($wo['customer_name']); ?></td>
              <td><?php echo htmlspecialchars($wo['vendor_name'] ?? '-'); ?></td>
              <td><?php echo htmlspecialchars($wo['sales_rep_name'] ?? '-'); ?></td>
              <td><?php echo date('Y-m-d', strtotime($wo['order_date'])); ?></td>
              <td><?php echo $wo['requested_ship_date'] ? date('Y-m-d', strtotime($wo['requested_ship_date'])) : '-'; ?></td>
              <td>
                <span class="badge <?php echo $statusBadges[$wo['status']] ?? 'badge-secondary'; ?>">
                  <?php echo $statusLabels[$wo['status']] ?? $wo['status']; ?>
                </span>
              </td>
              <td><?php echo number_format($wo['total_devices']); ?></td>
              <td><?php echo number_format($wo['total_scents']); ?></td>
              <td><?php echo number_format($wo['total_contents']); ?></td>
              <td><?php echo htmlspecialchars($wo['tracking_number'] ?? '-'); ?></td>
              <td>
                <button class="btn-sm btn-edit" onclick="viewWorkOrder('<?php echo $wo['work_order_id']; ?>')">상세</button>
                <?php if ($wo['status'] === 'PENDING' || $wo['status'] === 'PREPARING'): ?>
                <button class="btn-sm btn-edit" onclick="editWorkOrder('<?php echo $wo['work_order_id']; ?>')">수정</button>
                <?php endif; ?>
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
// 페이지 이름 설정
window.pageName = '<?= encryptValue(date('Y-m-d') . '/work_orders') ?>';

// 필터 조회
document.getElementById('btnFilter')?.addEventListener('click', function() {
  const vendorId = document.getElementById('vendorFilter').value;
  const status = document.getElementById('statusFilter').value;
  const startDate = document.getElementById('startDate').value;
  const endDate = document.getElementById('endDate').value;
  const search = document.getElementById('searchKeyword').value;

  // 암호화된 POST 데이터 생성
  const data = {};
  if (vendorId) data['<?= encryptValue('vendor') ?>'] = vendorId;
  if (status) data['<?= encryptValue('status') ?>'] = status;
  if (startDate) data['<?= encryptValue('start_date') ?>'] = startDate;
  if (endDate) data['<?= encryptValue('end_date') ?>'] = endDate;
  if (search) data['<?= encryptValue('search') ?>'] = search;

  // updateAjaxContent로 탭 내용만 업데이트
  updateAjaxContent(data, function(response) {
    if (response.result === 'ok' && response.html) {
      const contentArea = document.querySelector('#shipping-tab-content');
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

// CSV 내보내기
document.getElementById('btnExportCsv')?.addEventListener('click', () => {
  const table = document.getElementById('tblWorkOrders');
  const rows = Array.from(table.querySelectorAll('thead tr, tbody tr'));

  const csv = rows.map(row => {
    const cells = Array.from(row.querySelectorAll('th, td'));
    return cells.slice(0, -1).map(cell => {
      const badge = cell.querySelector('.badge');
      if (badge) return '"' + badge.textContent.trim() + '"';
      return '"' + cell.textContent.trim().replace(/"/g, '""') + '"';
    }).join(',');
  }).join('\n');

  const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = 'HQ_작업지시서_' + new Date().toISOString().slice(0,10) + '.csv';
  link.click();
});

// 신규 작업지시서
document.getElementById('btnAddWorkOrder')?.addEventListener('click', () => {
  alert('작업지시서 생성 기능은 개발 중입니다.');
});

// 작업지시서 상세 보기
function viewWorkOrder(workOrderId) {
  alert('작업지시서 상세 보기: ' + workOrderId + '\n개발 중입니다.');
}

// 작업지시서 수정
function editWorkOrder(workOrderId) {
  alert('작업지시서 수정: ' + workOrderId + '\n개발 중입니다.');
}

// 검색어 엔터키 처리
document.getElementById('searchKeyword')?.addEventListener('keypress', function(e) {
  if (e.key === 'Enter') {
    document.getElementById('btnFilter').click();
  }
});
</script>

<?php
// HTML 버퍼 캡처 및 응답 생성
$response['html'] = ob_get_clean();
$response['result'] = 'ok';
Finish();
?>
