<?php
/**
 * HQ 출고관리 > 출고히스토리
 * 출고 및 배송 이력 조회
 */

// 필터 파라미터 (POST는 이미 _ajax_.php에서 복호화됨)
$searchKeyword = isset($_POST['search']) ? $_POST['search'] : (isset($_GET['search']) ? $_GET['search'] : '');
$statusFilter = isset($_POST['status']) ? $_POST['status'] : (isset($_GET['status']) ? $_GET['status'] : '');
$vendorFilter = isset($_POST['vendor']) ? $_POST['vendor'] : (isset($_GET['vendor']) ? $_GET['vendor'] : '');
$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : (isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01'));
$endDate = isset($_POST['end_date']) ? $_POST['end_date'] : (isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t'));

// 출고 히스토리 목록 조회
$sql = "
SELECT
    sh.*,
    c.company_name as customer_name,
    v.company_name as vendor_name,
    wo.work_order_id,
    creator.name as created_by_name
FROM shipping_history sh
INNER JOIN customers c ON sh.customer_id = c.customer_id
INNER JOIN work_orders wo ON sh.work_order_id = wo.work_order_id
LEFT JOIN vendors v ON wo.vendor_id = v.vendor_id AND v.deleted_at IS NULL
LEFT JOIN users creator ON sh.created_by = creator.user_id AND creator.deleted_at IS NULL
WHERE sh.shipped_date BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'
";

if ($searchKeyword) {
    $sql .= " AND (wo.work_order_id LIKE '%" . mysqli_real_escape_string($con, $searchKeyword) . "%'
               OR c.company_name LIKE '%" . mysqli_real_escape_string($con, $searchKeyword) . "%'
               OR sh.tracking_number LIKE '%" . mysqli_real_escape_string($con, $searchKeyword) . "%')";
}

if ($statusFilter) {
    $sql .= " AND sh.status = '" . mysqli_real_escape_string($con, $statusFilter) . "'";
}

if ($vendorFilter) {
    $sql .= " AND wo.vendor_id = '" . mysqli_real_escape_string($con, $vendorFilter) . "'";
}

$sql .= " ORDER BY sh.shipped_date DESC, sh.created_at DESC LIMIT 100";

$result = mysqli_query($con, $sql);

$shippingHistory = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $shippingHistory[] = $row;
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
    'SHIPPED' => '출고',
    'IN_TRANSIT' => '배송중',
    'DELIVERED' => '배송완료',
    'RETURNED' => '반품',
    'FAILED' => '배송실패'
];

$statusBadges = [
    'SHIPPED' => 'badge-in-progress',
    'IN_TRANSIT' => 'badge-warning',
    'DELIVERED' => 'badge-success',
    'RETURNED' => 'badge-danger',
    'FAILED' => 'badge-danger'
];

// HTML 버퍼링 시작
ob_start();
?>

<div class="wrap">
  <section id="sec-shipping-history" class="card">
    <!-- 필터 -->
    <div class="card-hd">
      <div class="card-ttl">출고 히스토리</div>
      <span class="card-sub">출고 및 배송 이력 조회</span>
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
          <option value="SHIPPED" <?php echo $statusFilter === 'SHIPPED' ? 'selected' : ''; ?>>출고</option>
          <option value="IN_TRANSIT" <?php echo $statusFilter === 'IN_TRANSIT' ? 'selected' : ''; ?>>배송중</option>
          <option value="DELIVERED" <?php echo $statusFilter === 'DELIVERED' ? 'selected' : ''; ?>>배송완료</option>
          <option value="RETURNED" <?php echo $statusFilter === 'RETURNED' ? 'selected' : ''; ?>>반품</option>
          <option value="FAILED" <?php echo $statusFilter === 'FAILED' ? 'selected' : ''; ?>>배송실패</option>
        </select>

        <input type="date" id="startDate" class="input" value="<?php echo htmlspecialchars($startDate); ?>">
        <input type="date" id="endDate" class="input" value="<?php echo htmlspecialchars($endDate); ?>">
        <input type="text" id="searchKeyword" class="input" placeholder="지시서ID/고객명/송장번호" value="<?php echo htmlspecialchars($searchKeyword); ?>">

        <button id="btnFilter" class="btn primary">조회</button>
        <button id="btnExportCsv" class="btn">CSV 내보내기</button>
      </div>
    </div>

    <!-- 출고 히스토리 목록 테이블 -->
    <div class="card-bd">
      <div class="table-wrap">
        <table class="table" id="tblShippingHistory">
          <thead>
            <tr>
              <th>작업지시서ID</th>
              <th>고객명</th>
              <th>담당벤더</th>
              <th>출고일시</th>
              <th>배송완료일시</th>
              <th>배송상태</th>
              <th>기기</th>
              <th>향</th>
              <th>콘텐츠</th>
              <th>택배사</th>
              <th>송장번호</th>
              <th>배송비</th>
              <th>관리</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($shippingHistory)): ?>
            <tr>
              <td colspan="13" class="table-text-center">출고 이력이 없습니다.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($shippingHistory as $sh): ?>
            <tr>
              <td><strong><?php echo htmlspecialchars($sh['work_order_id']); ?></strong></td>
              <td><?php echo htmlspecialchars($sh['customer_name']); ?></td>
              <td><?php echo htmlspecialchars($sh['vendor_name'] ?? '-'); ?></td>
              <td><?php echo date('Y-m-d H:i', strtotime($sh['shipped_date'])); ?></td>
              <td><?php echo $sh['delivered_date'] ? date('Y-m-d H:i', strtotime($sh['delivered_date'])) : '-'; ?></td>
              <td>
                <span class="badge <?php echo $statusBadges[$sh['status']] ?? 'badge-secondary'; ?>">
                  <?php echo $statusLabels[$sh['status']] ?? $sh['status']; ?>
                </span>
              </td>
              <td><?php echo number_format($sh['total_devices']); ?></td>
              <td><?php echo number_format($sh['total_scents']); ?></td>
              <td><?php echo number_format($sh['total_contents']); ?></td>
              <td><?php echo htmlspecialchars($sh['carrier'] ?? '-'); ?></td>
              <td><?php echo htmlspecialchars($sh['tracking_number'] ?? '-'); ?></td>
              <td>₩<?php echo number_format($sh['shipping_cost']); ?></td>
              <td>
                <button class="btn-sm btn-edit" onclick="viewShipping('<?php echo $sh['shipping_id']; ?>')">상세</button>
                <button class="btn-sm btn-edit" onclick="trackShipping('<?php echo htmlspecialchars($sh['tracking_number']); ?>', '<?php echo htmlspecialchars($sh['carrier']); ?>')">배송조회</button>
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
window.pageName = '<?= encryptValue(date('Y-m-d') . '/shipping_history') ?>';

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
  const table = document.getElementById('tblShippingHistory');
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
  link.download = 'HQ_출고히스토리_' + new Date().toISOString().slice(0,10) + '.csv';
  link.click();
});

// 출고 상세 보기
function viewShipping(shippingId) {
  alert('출고 상세 보기: ' + shippingId + '\n개발 중입니다.');
}

// 배송 조회
function trackShipping(trackingNumber, carrier) {
  if (!trackingNumber || trackingNumber === '-') {
    alert('송장번호가 등록되지 않았습니다.');
    return;
  }

  // 택배사별 조회 URL (예시)
  let trackingUrl = '';
  switch(carrier) {
    case 'CJ대한통운':
      trackingUrl = 'https://www.cjlogistics.com/ko/tool/parcel/tracking?gnbInvcNo=' + trackingNumber;
      break;
    case '한진택배':
      trackingUrl = 'https://www.hanjin.com/kor/CMS/DeliveryMgr/WaybillResult.do?mCode=MN038&schLang=KR&wblnumText2=' + trackingNumber;
      break;
    case '롯데택배':
      trackingUrl = 'https://www.lotteglogis.com/home/reservation/tracking/index?InvNo=' + trackingNumber;
      break;
    default:
      alert('배송 조회: ' + carrier + '\n송장번호: ' + trackingNumber);
      return;
  }

  window.open(trackingUrl, '_blank');
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
