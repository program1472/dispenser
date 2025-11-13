<?php
/**
 * HQ 실적관리 > 전체 매출 실적
 * 전체 매출 분석 (벤더 + 영업사원 + 본사)
 */

// 필터 파라미터 (POST는 이미 _ajax_.php에서 복호화됨)
$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : (isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01'));
$endDate = isset($_POST['end_date']) ? $_POST['end_date'] : (isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t'));
$vendorFilter = isset($_POST['vendor_id']) ? $_POST['vendor_id'] : (isset($_GET['vendor_id']) ? $_GET['vendor_id'] : '');
$categoryFilter = isset($_POST['category']) ? $_POST['category'] : (isset($_GET['category']) ? $_GET['category'] : '');

// 원본 값 저장 (selected 체크용)
$vendorFilterDisplay = $vendorFilter;
$categoryFilterDisplay = $categoryFilter;

// SQL Injection 방지
$startDate = mysqli_real_escape_string($con, $startDate);
$endDate = mysqli_real_escape_string($con, $endDate);
$vendorFilter = mysqli_real_escape_string($con, $vendorFilter);
$categoryFilter = mysqli_real_escape_string($con, $categoryFilter);

// 카테고리 필터에 따른 조인 조건
$categoryJoin = '';
$categoryWhere = '';
if ($categoryFilter === 'content') {
    $categoryJoin = " INNER JOIN subscription_items si ON s.subscription_id = si.subscription_id AND si.item_type = 'CONTENT'
                      INNER JOIN contents ct ON si.item_id_ref = ct.content_id ";
    // INNER JOIN으로 변경했으므로 categoryWhere 불필요
} elseif ($categoryFilter === 'scent') {
    $categoryJoin = " INNER JOIN subscription_items si ON s.subscription_id = si.subscription_id AND si.item_type = 'SCENT'
                      INNER JOIN scents sc ON si.item_id_ref = sc.scent_id ";
    // INNER JOIN으로 변경했으므로 categoryWhere 불필요
}
// subscription 카테고리는 기본 구독료이므로 추가 조인 불필요

// 1. 벤더별 매출 합계 (벤더가 있는 고객의 구독료)
$vendorRevenueSql = "
SELECT
    COUNT(DISTINCT c.customer_id) as customer_count,
    COALESCE(SUM(s.monthly_fee), 0) as subscription_revenue,
    COALESCE(SUM(s.monthly_fee), 0) as total_revenue
FROM vendors v
LEFT JOIN customers c ON v.vendor_id = c.vendor_id AND c.is_active = 1 AND c.deleted_at IS NULL
LEFT JOIN subscriptions s ON c.customer_id = s.customer_id
    AND s.deleted_at IS NULL
    AND s.status = 'ACTIVE'
    AND s.start_date <= '{$endDate}'
    AND s.end_date >= '{$startDate}'
{$categoryJoin}
WHERE v.deleted_at IS NULL
" . ($vendorFilter ? " AND v.vendor_id = '{$vendorFilter}'" : "");

$vendorRevenueResult = mysqli_query($con, $vendorRevenueSql);
$vendorRevenueData = $vendorRevenueResult ? mysqli_fetch_assoc($vendorRevenueResult) : [
    'customer_count' => 0,
    'subscription_revenue' => 0,
    'total_revenue' => 0
];

// 2. 영업사원 매출 합계 (영업사원이 담당하는 고객의 구독료)
$salesRepRevenueSql = "
SELECT
    COUNT(DISTINCT c.customer_id) as customer_count,
    COALESCE(SUM(s.monthly_fee), 0) as subscription_revenue,
    COALESCE(SUM(s.monthly_fee), 0) as total_revenue
FROM users u
INNER JOIN roles r ON u.role_id = r.role_id
LEFT JOIN account_assignments aa ON u.user_id = aa.sales_user_id AND aa.is_active = 1
LEFT JOIN customers c ON aa.customer_id = c.customer_id AND c.is_active = 1 AND c.deleted_at IS NULL
LEFT JOIN subscriptions s ON c.customer_id = s.customer_id
    AND s.deleted_at IS NULL
    AND s.status = 'ACTIVE'
    AND s.start_date <= '{$endDate}'
    AND s.end_date >= '{$startDate}'
{$categoryJoin}
WHERE u.deleted_at IS NULL
    AND r.role_name = 'SALES_REP'
" . ($vendorFilter ? " AND c.vendor_id = '{$vendorFilter}'" : "");

$salesRepRevenueResult = mysqli_query($con, $salesRepRevenueSql);
$salesRepRevenueData = $salesRepRevenueResult ? mysqli_fetch_assoc($salesRepRevenueResult) : [
    'customer_count' => 0,
    'subscription_revenue' => 0,
    'total_revenue' => 0
];

// 3. 본사 직접 매출 합계 (vendor_id가 NULL인 고객)
$hqRevenueSql = "
SELECT
    COUNT(DISTINCT c.customer_id) as customer_count,
    COALESCE(SUM(s.monthly_fee), 0) as subscription_revenue,
    COALESCE(SUM(s.monthly_fee), 0) as total_revenue
FROM customers c
LEFT JOIN subscriptions s ON c.customer_id = s.customer_id
    AND s.deleted_at IS NULL
    AND s.status = 'ACTIVE'
    AND s.start_date <= '{$endDate}'
    AND s.end_date >= '{$startDate}'
{$categoryJoin}
WHERE c.is_active = 1
    AND c.deleted_at IS NULL
    AND (c.vendor_id IS NULL OR c.vendor_id = '')
    AND c.created_at BETWEEN '{$startDate}' AND '{$endDate}'
";

$hqRevenueResult = mysqli_query($con, $hqRevenueSql);
$hqRevenueData = $hqRevenueResult ? mysqli_fetch_assoc($hqRevenueResult) : [
    'customer_count' => 0,
    'subscription_revenue' => 0,
    'total_revenue' => 0
];

// 전체 합계
$totalRevenue = $vendorRevenueData['total_revenue'] + $salesRepRevenueData['total_revenue'] + $hqRevenueData['total_revenue'];
$totalCustomers = $vendorRevenueData['customer_count'] + $salesRepRevenueData['customer_count'] + $hqRevenueData['customer_count'];

// 매출 구성 데이터
$salesData = [
    [
        'category' => '벤더 채널',
        'customer_count' => $vendorRevenueData['customer_count'],
        'revenue' => $vendorRevenueData['subscription_revenue'],
        'percentage' => $totalRevenue > 0 ? round(($vendorRevenueData['total_revenue'] / $totalRevenue) * 100, 1) : 0
    ],
    [
        'category' => '영업사원 채널',
        'customer_count' => $salesRepRevenueData['customer_count'],
        'revenue' => $salesRepRevenueData['subscription_revenue'],
        'percentage' => $totalRevenue > 0 ? round(($salesRepRevenueData['total_revenue'] / $totalRevenue) * 100, 1) : 0
    ],
    [
        'category' => '본사 직접',
        'customer_count' => $hqRevenueData['customer_count'],
        'revenue' => $hqRevenueData['subscription_revenue'],
        'percentage' => $totalRevenue > 0 ? round(($hqRevenueData['total_revenue'] / $totalRevenue) * 100, 1) : 0
    ]
];

// 벤더별 매출 통계 (TOP 10)
$vendorSalesSql = "
SELECT
    v.vendor_id,
    v.company_name as vendor_name,
    COUNT(DISTINCT c.customer_id) as customer_count,
    COUNT(DISTINCT c.customer_id) as new_customers,
    COALESCE(SUM(s.monthly_fee), 0) as subscription_revenue,
    COALESCE(SUM(s.monthly_fee), 0) as total_revenue,
    ROUND(COALESCE(SUM(s.monthly_fee), 0) * 0.15, 0) as commission
FROM vendors v
LEFT JOIN customers c ON v.vendor_id = c.vendor_id
    AND c.is_active = 1
    AND c.deleted_at IS NULL
    AND c.created_at BETWEEN '{$startDate}' AND '{$endDate}'
LEFT JOIN subscriptions s ON c.customer_id = s.customer_id
    AND s.deleted_at IS NULL
    AND s.status = 'ACTIVE'
{$categoryJoin}
WHERE v.deleted_at IS NULL
" . ($vendorFilter ? " AND v.vendor_id = '{$vendorFilter}'" : "") . "
GROUP BY v.vendor_id, v.company_name
HAVING total_revenue > 0
ORDER BY total_revenue DESC
LIMIT 10
";

$vendorSalesResult = mysqli_query($con, $vendorSalesSql);
$vendorSalesData = [];
if ($vendorSalesResult) {
    while ($row = mysqli_fetch_assoc($vendorSalesResult)) {
        $vendorSalesData[] = $row;
    }
}

// 구독 상태 분석
$subscriptionStatusSql = "
SELECT
    s.status,
    COUNT(*) as count,
    COALESCE(SUM(s.monthly_fee), 0) as total_revenue
FROM subscriptions s
LEFT JOIN customers c ON s.customer_id = c.customer_id
{$categoryJoin}
WHERE s.deleted_at IS NULL
    AND s.start_date <= '{$endDate}'
    AND s.end_date >= '{$startDate}'
" . ($vendorFilter ? " AND c.vendor_id = '{$vendorFilter}'" : "") . "
GROUP BY s.status
";

$subscriptionStatusResult = mysqli_query($con, $subscriptionStatusSql);
$subscriptionStatusData = [];
if ($subscriptionStatusResult) {
    while ($row = mysqli_fetch_assoc($subscriptionStatusResult)) {
        $subscriptionStatusData[] = $row;
    }
}

// 벤더 목록 (필터용)
$vendorListSql = "SELECT vendor_id, company_name FROM vendors WHERE deleted_at IS NULL ORDER BY company_name";
$vendorListResult = mysqli_query($con, $vendorListSql);
$vendors = [];
if ($vendorListResult) {
    while ($row = mysqli_fetch_assoc($vendorListResult)) {
        $vendors[] = $row;
    }
}

// 활성 구독 수
$activeSubscriptionsSql = "SELECT COUNT(*) as cnt FROM subscriptions WHERE deleted_at IS NULL AND status = 'ACTIVE'";
$activeSubscriptionsResult = mysqli_query($con, $activeSubscriptionsSql);
$activeSubscriptionsRow = $activeSubscriptionsResult ? mysqli_fetch_assoc($activeSubscriptionsResult) : ['cnt' => 0];
$activeSubscriptions = $activeSubscriptionsRow['cnt'];

// SQL 로그 및 KPI 데이터 추가
$response['data']['search']['sql'] = [
    'vendor_revenue' => $vendorRevenueSql,
    'sales_rep_revenue' => $salesRepRevenueSql,
    'hq_revenue' => $hqRevenueSql,
    'vendor_sales_top10' => $vendorSalesSql,
    'subscription_status' => $subscriptionStatusSql,
    'category_join' => $categoryJoin
];
$response['data']['search']['filters'] = [
    'start_date' => $startDate,
    'end_date' => $endDate,
    'vendor_id' => $vendorFilter,
    'category' => $categoryFilter,
    'vendor_id_display' => $vendorFilterDisplay,
    'category_display' => $categoryFilterDisplay
];
$response['data']['search']['kpi'] = [
    'total_revenue' => $totalRevenue,
    'total_customers' => $totalCustomers,
    'active_subscriptions' => $activeSubscriptions,
    'vendor_revenue_data' => $vendorRevenueData,
    'sales_rep_revenue_data' => $salesRepRevenueData,
    'hq_revenue_data' => $hqRevenueData
];

// HTML 출력 버퍼링 시작
ob_start();
?>

<section class="card">
  <div class="card-hd card-hd-wrap">
    <div class="card-hd-content">
      <div class="card-hd-title-area">
        <div class="card-ttl">전체 매출 실적</div>
        <div class="card-sub">기간별 전체 매출 분석 및 채널별 실적</div>
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
          <label>카테고리</label>
          <select id="categoryFilter" name="category" class="form-control input-w-160">
            <option value="">전체 카테고리</option>
            <option value="subscription" <?php echo $categoryFilterDisplay == 'subscription' ? 'selected' : ''; ?>>구독료</option>
            <option value="content" <?php echo $categoryFilterDisplay == 'content' ? 'selected' : ''; ?>>콘텐츠</option>
            <option value="scent" <?php echo $categoryFilterDisplay == 'scent' ? 'selected' : ''; ?>>향기</option>
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
      <button type="button" id="btnPrintReport" class="btn">리포트 출력</button>
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
        <div class="kpi-label">활성 구독수</div>
        <div class="kpi-value"><?php echo number_format($activeSubscriptions); ?></div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">평균 구독료/고객</div>
        <div class="kpi-value">₩<?php echo $totalCustomers > 0 ? number_format(round($totalRevenue / $totalCustomers)) : 0; ?></div>
      </div>
    </div>
  </div>

  <!-- 채널별 매출 구성 -->
  <div class="card-bd-padding section-divider">
    <h3 class="section-title">채널별 매출 구성</h3>
    <div class="table-scroll">
      <table class="data-table" id="tblChannelSales">
        <thead>
          <tr>
            <th>채널</th>
            <th>고객수</th>
            <th>매출액</th>
            <th>매출 비중</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($salesData) || $totalRevenue == 0): ?>
          <tr>
            <td colspan="4" class="table-text-center text-muted">해당 기간에 매출 데이터가 없습니다.</td>
          </tr>
          <?php else: ?>
          <?php foreach ($salesData as $row): ?>
          <tr>
            <td><strong><?php echo htmlspecialchars($row['category']); ?></strong></td>
            <td><?php echo number_format($row['customer_count']); ?></td>
            <td><strong>₩<?php echo number_format($row['revenue']); ?></strong></td>
            <td>
              <div class="progress-wrapper">
                <div class="progress-bar">
                  <div class="progress-fill" style="width: <?php echo $row['percentage']; ?>%"></div>
                </div>
                <span class="progress-label"><?php echo $row['percentage']; ?>%</span>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
        <tfoot>
          <tr class="total-row">
            <td><strong>합계</strong></td>
            <td><strong><?php echo number_format($totalCustomers); ?></strong></td>
            <td><strong>₩<?php echo number_format($totalRevenue); ?></strong></td>
            <td><strong>100%</strong></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  <!-- 벤더별 매출 TOP 10 -->
  <div class="card-bd-padding section-divider">
    <h3 class="section-title">벤더별 매출 TOP 10</h3>
    <div class="table-scroll">
      <table class="data-table" id="tblVendorSales">
        <thead>
          <tr>
            <th>순위</th>
            <th>벤더명</th>
            <th>고객수</th>
            <th>신규 고객수</th>
            <th>구독료 매출</th>
            <th>총 매출</th>
            <th>커미션 (15%)</th>
            <th>매출 비중</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($vendorSalesData)): ?>
          <tr>
            <td colspan="8" class="table-text-center text-muted">해당 기간에 매출 데이터가 없습니다.</td>
          </tr>
          <?php else: ?>
          <?php foreach ($vendorSalesData as $index => $vendor):
            $percentage = $totalRevenue > 0 ? round(($vendor['total_revenue'] / $totalRevenue) * 100, 1) : 0;
          ?>
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
            <td><?php echo number_format($vendor['customer_count']); ?></td>
            <td>
              <?php if ($vendor['new_customers'] > 0): ?>
              <span class="badge badge-status-active">+<?php echo number_format($vendor['new_customers']); ?></span>
              <?php else: ?>
              -
              <?php endif; ?>
            </td>
            <td>₩<?php echo number_format($vendor['subscription_revenue']); ?></td>
            <td><strong>₩<?php echo number_format($vendor['total_revenue']); ?></strong></td>
            <td class="text-warn">₩<?php echo number_format($vendor['commission']); ?></td>
            <td><?php echo $percentage; ?>%</td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- 구독 상태 분석 -->
  <div class="card-bd-padding">
    <h3 class="section-title">구독 상태 분석</h3>
    <div class="table-scroll">
      <table class="data-table" id="tblSubscriptionStatus">
        <thead>
          <tr>
            <th>구독 상태</th>
            <th>구독 수</th>
            <th>매출액</th>
            <th>비율</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $statusLabels = [
            'ACTIVE' => '활성',
            'PENDING' => '대기',
            'SUSPENDED' => '정지',
            'EXPIRED' => '만료',
            'CANCELLED' => '취소'
          ];
          $statusBadges = [
            'ACTIVE' => 'badge-status-active',
            'PENDING' => 'badge-status-pending',
            'SUSPENDED' => 'badge-status-suspended',
            'EXPIRED' => 'badge-status-expired',
            'CANCELLED' => 'badge-status-cancelled'
          ];
          $totalSubscriptions = array_sum(array_column($subscriptionStatusData, 'count'));

          if (empty($subscriptionStatusData)):
          ?>
          <tr>
            <td colspan="4" class="table-text-center text-muted">데이터가 없습니다.</td>
          </tr>
          <?php else: ?>
          <?php foreach ($subscriptionStatusData as $status):
            $percentage = $totalSubscriptions > 0 ? round(($status['count'] / $totalSubscriptions) * 100, 1) : 0;
            $statusKey = $status['status'];
          ?>
          <tr>
            <td>
              <span class="badge <?php echo $statusBadges[$statusKey] ?? 'badge-default'; ?>">
                <?php echo $statusLabels[$statusKey] ?? $statusKey; ?>
              </span>
            </td>
            <td><?php echo number_format($status['count']); ?></td>
            <td>₩<?php echo number_format($status['total_revenue']); ?></td>
            <td><?php echo $percentage; ?>%</td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<script>
// 페이지 이름 (AJAX 호출용)
window.pageName = '<?= encryptValue($today . '/perf_all') ?>';

// 필터 적용
document.getElementById('btnApplyFilter')?.addEventListener('click', function() {
  const startDate = document.getElementById('startDate').value;
  const endDate = document.getElementById('endDate').value;
  const vendorId = document.getElementById('vendorFilter').value;
  const category = document.getElementById('categoryFilter').value;

  // 암호화된 POST 데이터 생성 (빈 값도 전송)
  const data = {};
  data['<?= encryptValue('start_date') ?>'] = startDate || '';
  data['<?= encryptValue('end_date') ?>'] = endDate || '';
  data['<?= encryptValue('vendor_id') ?>'] = vendorId || '';
  data['<?= encryptValue('category') ?>'] = category || '';

  // updateAjaxContent로 페이지 다시 로드
  updateAjaxContent(data, function(response) {
    if (response.result === 'ok' && response.html) {
      const contentArea = document.querySelector('#perf-tab-content');
      if (contentArea) {
        contentArea.innerHTML = response.html;
        // 스크립트 재실행
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
  const tables = [
    { id: 'tblChannelSales', name: '채널별매출' },
    { id: 'tblVendorSales', name: '벤더별매출' },
    { id: 'tblSubscriptionStatus', name: '구독상태' }
  ];

  let csv = '\uFEFF'; // UTF-8 BOM

  tables.forEach((tableInfo, index) => {
    const table = document.getElementById(tableInfo.id);
    if (!table) return;

    if (index > 0) csv += '\n\n';
    csv += `=== ${tableInfo.name} ===\n`;

    const rows = Array.from(table.querySelectorAll('tr'));
    csv += rows.map(row => {
      const cells = Array.from(row.querySelectorAll('th, td'));
      return cells.map(cell => {
        const badge = cell.querySelector('.badge');
        if (badge) return '"' + badge.textContent.trim() + '"';

        const text = cell.textContent.trim().replace(/\s+/g, ' ');
        return '"' + text.replace(/"/g, '""') + '"';
      }).join(',');
    }).join('\n');
  });

  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  const dateStr = new Date().toISOString().slice(0, 10);
  link.download = `HQ_전체매출실적_${dateStr}.csv`;
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
