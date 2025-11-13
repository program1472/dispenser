<?php
/**
 * HQ 실적관리 > 벤더별 매출 실적
 * 벤더별 매출 분석 및 커미션 관리
 */

// 필터 파라미터 (POST는 이미 _ajax_.php에서 복호화됨)
$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : (isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01'));
$endDate = isset($_POST['end_date']) ? $_POST['end_date'] : (isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t'));
$vendorFilter = isset($_POST['vendor_id']) ? $_POST['vendor_id'] : (isset($_GET['vendor_id']) ? $_GET['vendor_id'] : '');

// 원본 값 저장 (selected 체크용)
$vendorFilterDisplay = $vendorFilter;

// SQL Injection 방지
$startDate = mysqli_real_escape_string($con, $startDate);
$endDate = mysqli_real_escape_string($con, $endDate);
$vendorFilter = mysqli_real_escape_string($con, $vendorFilter);

// 벤더별 매출 상세 조회
$vendorSalesSql = "
SELECT
    v.vendor_id,
    v.company_name as vendor_name,
    v.ceo_name as representative,
    v.phone,
    v.email as email,
    COUNT(DISTINCT c.customer_id) as active_customers,
    COUNT(DISTINCT CASE WHEN c.created_at BETWEEN '{$startDate}' AND '{$endDate}' THEN c.customer_id END) as new_customers,
    COUNT(DISTINCT cs.site_id) as total_sites,
    COUNT(DISTINCT s.subscription_id) as total_subscriptions,
    COALESCE(SUM(s.monthly_fee), 0) as subscription_revenue,
    COALESCE(SUM(s.monthly_fee), 0) as total_revenue,
    ROUND(COALESCE(SUM(s.monthly_fee), 0) * 0.40, 0) as commission_40,
    ROUND(COALESCE(SUM(s.monthly_fee), 0) * 0.20, 0) as additional_commission
FROM vendors v
LEFT JOIN customers c ON v.vendor_id = c.vendor_id AND c.is_active = 1 AND c.deleted_at IS NULL
LEFT JOIN customer_sites cs ON c.customer_id = cs.customer_id AND cs.deleted_at IS NULL
LEFT JOIN subscriptions s ON c.customer_id = s.customer_id
    AND s.deleted_at IS NULL
    AND s.status = 'ACTIVE'
    AND s.start_date <= '{$endDate}'
    AND s.end_date >= '{$startDate}'
WHERE v.deleted_at IS NULL
" . ($vendorFilter ? " AND v.vendor_id = '{$vendorFilter}'" : "") . "
GROUP BY v.vendor_id, v.company_name, v.ceo_name, v.phone, v.email
HAVING total_revenue > 0
ORDER BY total_revenue DESC
";

$vendorSalesResult = mysqli_query($con, $vendorSalesSql);
$vendorSalesData = [];
$totalRevenue = 0;
$totalCommission = 0;
$totalAdditionalCommission = 0;
$totalCustomers = 0;

// 디버깅: 쿼리 실행 확인
if (!$vendorSalesResult) {
    $response['data']['search']['error'] = mysqli_error($con);
}

if ($vendorSalesResult) {
    while ($row = mysqli_fetch_assoc($vendorSalesResult)) {
        $vendorSalesData[] = $row;
        $totalRevenue += $row['total_revenue'];
        $totalCommission += $row['commission_40'];
        $totalAdditionalCommission += $row['additional_commission'];
        $totalCustomers += $row['active_customers'];
    }
}

// 디버깅: 결과 데이터 확인
$response['data']['search']['debug'] = [
    'row_count' => count($vendorSalesData),
    'total_revenue' => $totalRevenue,
    'total_customers' => $totalCustomers
];

// 벤더 목록 (필터용)
$vendorListSql = "SELECT vendor_id, company_name FROM vendors WHERE deleted_at IS NULL ORDER BY company_name";
$vendorListResult = mysqli_query($con, $vendorListSql);
$vendors = [];
if ($vendorListResult) {
    while ($row = mysqli_fetch_assoc($vendorListResult)) {
        $vendors[] = $row;
    }
}

// 상위 벤더 TOP 5
$topVendors = array_slice($vendorSalesData, 0, 5);

// 신규 고객 확보 TOP 5
$newCustomerTopSql = "
SELECT
    v.vendor_id,
    v.company_name as vendor_name,
    COUNT(DISTINCT c.customer_id) as new_customer_count
FROM vendors v
LEFT JOIN customers c ON v.vendor_id = c.vendor_id
    AND c.created_at BETWEEN '{$startDate}' AND '{$endDate}'
    AND c.is_active = 1
    AND c.deleted_at IS NULL
WHERE v.deleted_at IS NULL
GROUP BY v.vendor_id, v.company_name
HAVING new_customer_count > 0
ORDER BY new_customer_count DESC
LIMIT 5
";
$newCustomerTopResult = mysqli_query($con, $newCustomerTopSql);
$newCustomerTopData = [];
if ($newCustomerTopResult) {
    while ($row = mysqli_fetch_assoc($newCustomerTopResult)) {
        $newCustomerTopData[] = $row;
    }
}

// SQL 로그 및 KPI 데이터 추가
$response['data']['search']['sql'] = [
    'vendor_sales' => $vendorSalesSql,
    'new_customer_top' => $newCustomerTopSql
];
$response['data']['search']['filters'] = [
    'start_date' => $startDate,
    'end_date' => $endDate,
    'vendor_id' => $vendorFilter,
    'vendor_id_display' => $vendorFilterDisplay
];
$response['data']['search']['kpi'] = [
    'total_revenue' => $totalRevenue,
    'total_customers' => $totalCustomers,
    'total_commission' => $totalCommission,
    'active_vendors' => count($vendorSalesData)
];

// HTML 출력 버퍼링 시작
ob_start();
?>

<section class="card">
  <div class="card-hd card-hd-wrap">
    <div class="card-hd-content">
      <div class="card-hd-title-area">
        <div class="card-ttl">벤더별 매출 실적</div>
        <div class="card-sub">벤더별 매출 분석 및 커미션 관리</div>
      </div>
      <div class="filter-toolbar">
        <div class="filter-group">
          <label>벤더</label>
          <select id="vendorFilter" name="vendor_id" class="form-control input-w-200">
            <option value="">전체 벤더</option>
            <?php foreach ($vendors as $vendor): ?>
            <option value="<?php echo htmlspecialchars($vendor['vendor_id']); ?>"
                    <?php echo $vendorFilterDisplay == $vendor['vendor_id'] ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($vendor['company_name']); ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="filter-group">
          <label>시작일</label>
          <input type="date" id="startDate" name="start_date" class="form-control input-w-160" value="<?php echo htmlspecialchars($startDate); ?>">
        </div>
        <div class="filter-group">
          <label>종료일</label>
          <input type="date" id="endDate" name="end_date" class="form-control input-w-160" value="<?php echo htmlspecialchars($endDate); ?>">
        </div>
        <div class="filter-group">
          <button type="button" class="btn-preset" onclick="setDate('today')">오늘</button>
          <button type="button" class="btn-preset" onclick="setDate('thisWeek')">금주</button>
          <button type="button" class="btn-preset" onclick="setDate('prevWeek')">전주</button>
          <button type="button" class="btn-preset" onclick="setDate('thisMonth')">당월</button>
          <button type="button" class="btn-preset" onclick="setDate('prevMonth')">전월</button>
          <button type="button" class="btn-preset" onclick="setDate('30days')">최근1개월</button>
        </div>
        <button type="button" id="btnApplyFilter" class="btn primary">조회</button>
      </div>
    </div>
    <div class="row">
      <button type="button" id="btnExportCsv" class="btn">CSV 내보내기</button>
      <button id="btnPrintReport" class="btn">리포트 출력</button>
    </div>
  </div>

  <!-- KPI Cards -->
  <div class="card-bd-padding">
    <div class="kpi-grid">
      <div class="kpi-card">
        <div class="kpi-label">총 매출</div>
        <div class="kpi-value">₩<?php echo number_format($totalRevenue); ?></div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">총 고객수</div>
        <div class="kpi-value"><?php echo number_format($totalCustomers); ?></div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">활성 벤더수</div>
        <div class="kpi-value"><?php echo count($vendorSalesData); ?></div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">총 커미션 (40%)</div>
        <div class="kpi-value warn">₩<?php echo number_format($totalCommission); ?></div>
      </div>
    </div>
  </div>

  <!-- 그리드: TOP 5 & 신규고객 TOP 5 -->
  <div class="grid-2 card-bd-padding section-divider">
    <!-- TOP 5 매출 -->
    <div>
      <h3 class="section-title">매출 TOP 5</h3>
      <div class="table-scroll">
        <table class="data-table" id="tblTopVendors">
          <thead>
            <tr>
              <th>순위</th>
              <th>벤더명</th>
              <th>고객수</th>
              <th>총 매출</th>
              <th>커미션 (40%)</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($topVendors)): ?>
            <tr>
              <td colspan="5" class="table-text-center text-muted">해당 기간에 매출 데이터가 없습니다.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($topVendors as $index => $vendor): ?>
            <tr>
              <td>
                <?php if ($index === 0): ?>
                <span class="rank-medal">🥇</span>
                <?php elseif ($index === 1): ?>
                <span class="rank-medal">🥈</span>
                <?php elseif ($index === 2): ?>
                <span class="rank-medal">🥉</span>
                <?php else: ?>
                <strong><?php echo $index + 1; ?></strong>
                <?php endif; ?>
              </td>
              <td><strong><?php echo htmlspecialchars($vendor['vendor_name']); ?></strong></td>
              <td><?php echo number_format($vendor['active_customers']); ?></td>
              <td><strong>₩<?php echo number_format($vendor['total_revenue']); ?></strong></td>
              <td class="text-warn"><strong>₩<?php echo number_format($vendor['commission_40']); ?></strong></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- 신규 고객 확보 TOP 5 -->
    <div>
      <h3 class="section-title">신규 고객 확보 TOP 5</h3>
      <div class="table-scroll">
        <table class="data-table" id="tblNewCustomerTop">
          <thead>
            <tr>
              <th>순위</th>
              <th>벤더명</th>
              <th>신규 고객수</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($newCustomerTopData)): ?>
            <tr>
              <td colspan="3" class="table-text-center text-muted">데이터가 없습니다.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($newCustomerTopData as $index => $vendor): ?>
            <tr>
              <td><strong><?php echo $index + 1; ?></strong></td>
              <td><strong><?php echo htmlspecialchars($vendor['vendor_name']); ?></strong></td>
              <td>
                <span class="badge badge-status-active">+<?php echo number_format($vendor['new_customer_count']); ?></span>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- 전체 벤더 매출 상세 -->
  <div class="card-bd-padding">
    <h3 class="section-title">벤더별 상세 실적</h3>
    <div class="table-scroll">
      <table class="data-table" id="tblVendorDetail">
        <thead>
          <tr>
            <th>벤더ID</th>
            <th>벤더명</th>
            <th>대표자</th>
            <th>연락처</th>
            <th>활성 고객수</th>
            <th>신규 고객수</th>
            <th>사업장수</th>
            <th>구독수</th>
            <th>구독료 매출</th>
            <th>총 매출</th>
            <th>커미션 40%</th>
            <th>추가커미션 20%</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($vendorSalesData)): ?>
          <tr>
            <td colspan="12" class="table-text-center text-muted">해당 기간에 매출 데이터가 없습니다.</td>
          </tr>
          <?php else: ?>
          <?php foreach ($vendorSalesData as $vendor): ?>
          <tr>
            <td><?php echo htmlspecialchars($vendor['vendor_id']); ?></td>
            <td><strong><?php echo htmlspecialchars($vendor['vendor_name']); ?></strong></td>
            <td><?php echo htmlspecialchars($vendor['representative'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars($vendor['phone'] ?? '-'); ?></td>
            <td><?php echo number_format($vendor['active_customers']); ?></td>
            <td>
              <?php if ($vendor['new_customers'] > 0): ?>
              <span class="badge badge-status-active">+<?php echo number_format($vendor['new_customers']); ?></span>
              <?php else: ?>
              -
              <?php endif; ?>
            </td>
            <td><?php echo number_format($vendor['total_sites']); ?></td>
            <td><?php echo number_format($vendor['total_subscriptions']); ?></td>
            <td>₩<?php echo number_format($vendor['subscription_revenue']); ?></td>
            <td><strong>₩<?php echo number_format($vendor['total_revenue']); ?></strong></td>
            <td class="text-warn"><strong>₩<?php echo number_format($vendor['commission_40']); ?></strong></td>
            <td class="text-warn">₩<?php echo number_format($vendor['additional_commission']); ?></td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
        <tfoot>
          <tr class="total-row">
            <td colspan="4"><strong>합계</strong></td>
            <td><strong><?php echo number_format($totalCustomers); ?></strong></td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td><strong>₩<?php echo number_format($totalRevenue); ?></strong></td>
            <td class="text-warn"><strong>₩<?php echo number_format($totalCommission); ?></strong></td>
            <td class="text-warn"><strong>₩<?php echo number_format($totalAdditionalCommission); ?></strong></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</section>

<script>
// 페이지 이름 (AJAX 호출용)
window.pageName = '<?= encryptValue(date('Y-m-d') . '/perf_vendor') ?>';

// 필터 적용
document.getElementById('btnApplyFilter')?.addEventListener('click', function() {
  const startDate = document.getElementById('startDate').value;
  const endDate = document.getElementById('endDate').value;
  const vendorId = document.getElementById('vendorFilter').value;

  const data = {};
  if (startDate) data['<?= encryptValue('start_date') ?>'] = startDate;
  if (endDate) data['<?= encryptValue('end_date') ?>'] = endDate;
  if (vendorId) data['<?= encryptValue('vendor_id') ?>'] = vendorId;

  updateAjaxContent(data, function(response) {
    if (response.result === 'ok' && response.html) {
      const contentArea = document.querySelector('#perf-tab-content');
      if (contentArea) {
        contentArea.innerHTML = response.html;
        contentArea.querySelectorAll('script').forEach(function(oldScript) {
          const newScript = document.createElement('script');
          if (oldScript.src) {
            newScript.src = oldScript.src;
          } else {
            newScript.text = oldScript.text || oldScript.textContent || oldScript.innerHTML;
          }
          oldScript.parentNode.replaceChild(newScript, oldScript);
        });
      }
    }
  }, false);
});

// CSV 내보내기
document.getElementById('btnExportCsv')?.addEventListener('click', function() {
  const table = document.getElementById('tblVendorDetail');
  const rows = Array.from(table.querySelectorAll('tr'));

  const csv = '\uFEFF' + rows.map(row => {
    const cells = Array.from(row.querySelectorAll('th, td'));
    return cells.map(cell => {
      const badge = cell.querySelector('.badge');
      if (badge) return '"' + badge.textContent.trim() + '"';

      const text = cell.textContent.trim().replace(/\s+/g, ' ');
      return '"' + text.replace(/"/g, '""') + '"';
    }).join(',');
  }).join('\n');

  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  const dateStr = new Date().toISOString().slice(0, 10);
  link.download = `HQ_벤더별매출_${dateStr}.csv`;
  link.click();
});

// 리포트 출력
document.getElementById('btnPrintReport')?.addEventListener('click', function() {
  window.print();
});
</script>

<?php
// HTML 버퍼 캡처 및 응답 생성
$response['html'] = ob_get_clean();
$response['result'] = 'ok';
Finish();
?>
