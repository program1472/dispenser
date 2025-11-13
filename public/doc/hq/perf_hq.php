<?php
/**
 * HQ 실적관리 > 본사 직접 매출 실적
 * 본사 직접 계약 고객 매출 분석
 */

// 필터 파라미터 (POST는 이미 _ajax_.php에서 복호화됨)
$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : (isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01'));
$endDate = isset($_POST['end_date']) ? $_POST['end_date'] : (isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t'));

// SQL Injection 방지
$startDate = mysqli_real_escape_string($con, $startDate);
$endDate = mysqli_real_escape_string($con, $endDate);

// 본사 직접 고객 (vendor_id가 NULL인 고객) 매출 조회
$hqCustomersSql = "
SELECT
    c.customer_id,
    c.company_name,
    c.ceo_name,
    c.created_at as contract_date,
    COUNT(DISTINCT cs.site_id) as site_count,
    COUNT(DISTINCT s.subscription_id) as subscription_count,
    COALESCE(SUM(s.monthly_fee), 0) as subscription_fee,
    COALESCE(SUM(s.monthly_fee), 0) as total_revenue
FROM customers c
LEFT JOIN customer_sites cs ON c.customer_id = cs.customer_id AND cs.deleted_at IS NULL
LEFT JOIN subscriptions s ON c.customer_id = s.customer_id
    AND s.deleted_at IS NULL
    AND s.status = 'ACTIVE'
    AND s.start_date <= '{$endDate}'
    AND s.end_date >= '{$startDate}'
WHERE c.is_active = 1
    AND c.deleted_at IS NULL
    AND (c.vendor_id IS NULL OR c.vendor_id = '')
    AND c.created_at BETWEEN '{$startDate}' AND '{$endDate}'
GROUP BY c.customer_id, c.company_name, c.ceo_name, c.created_at
ORDER BY total_revenue DESC
";

$hqCustomersResult = mysqli_query($con, $hqCustomersSql);
$hqCustomersData = [];
$totalRevenue = 0;
$totalCustomers = 0;
$totalSites = 0;
$totalSubscriptions = 0;

// 디버깅: 쿼리 실행 확인
if (!$hqCustomersResult) {
    $response['data']['search']['error'] = mysqli_error($con);
}

if ($hqCustomersResult) {
    while ($row = mysqli_fetch_assoc($hqCustomersResult)) {
        $hqCustomersData[] = $row;
        $totalRevenue += $row['total_revenue'];
        $totalCustomers++;
        $totalSites += $row['site_count'];
        $totalSubscriptions += $row['subscription_count'];
    }
}

// 월별 매출 추이 (최근 6개월)
$monthlyRevenueSql = "
SELECT
    DATE_FORMAT(c.created_at, '%Y-%m') as month,
    COUNT(DISTINCT c.customer_id) as customer_count,
    COALESCE(SUM(s.monthly_fee), 0) as total_revenue
FROM customers c
LEFT JOIN subscriptions s ON c.customer_id = s.customer_id
    AND s.deleted_at IS NULL
    AND s.status = 'ACTIVE'
WHERE c.is_active = 1
    AND c.deleted_at IS NULL
    AND (c.vendor_id IS NULL OR c.vendor_id = '')
    AND c.created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
GROUP BY DATE_FORMAT(c.created_at, '%Y-%m')
ORDER BY month DESC
LIMIT 6
";

$monthlyRevenueResult = mysqli_query($con, $monthlyRevenueSql);
$monthlyRevenueData = [];
if ($monthlyRevenueResult) {
    while ($row = mysqli_fetch_assoc($monthlyRevenueResult)) {
        $monthlyRevenueData[] = $row;
    }
}
$monthlyRevenueData = array_reverse($monthlyRevenueData); // 오래된 순서로 정렬

// TOP 5 고객
$topCustomers = array_slice($hqCustomersData, 0, 5);

// SQL 로그 및 KPI 데이터 추가
$response['data']['search']['sql'] = [
    'hq_customers' => $hqCustomersSql,
    'monthly_revenue' => $monthlyRevenueSql,
    'subscription_status' => '' // 아래에서 정의됨
];
$response['data']['search']['filters'] = [
    'start_date' => $startDate,
    'end_date' => $endDate
];
$response['data']['search']['kpi'] = [
    'total_revenue' => $totalRevenue,
    'total_customers' => $totalCustomers,
    'total_sites' => $totalSites,
    'total_subscriptions' => $totalSubscriptions,
    'avg_revenue_per_customer' => $totalCustomers > 0 ? round($totalRevenue / $totalCustomers) : 0
];
$response['data']['search']['debug'] = [
    'row_count' => count($hqCustomersData),
    'total_revenue' => $totalRevenue,
    'total_customers' => $totalCustomers
];

// 구독 상태별 분석 (본사 고객만)
$subscriptionStatusSql = "
SELECT
    s.status,
    COUNT(*) as count,
    COALESCE(SUM(s.monthly_fee), 0) as total_revenue
FROM subscriptions s
INNER JOIN customers c ON s.customer_id = c.customer_id
WHERE s.deleted_at IS NULL
    AND c.is_active = 1
    AND c.deleted_at IS NULL
    AND (c.vendor_id IS NULL OR c.vendor_id = '')
    AND s.start_date <= '{$endDate}'
    AND s.end_date >= '{$startDate}'
GROUP BY s.status
";

$subscriptionStatusResult = mysqli_query($con, $subscriptionStatusSql);
$subscriptionStatusData = [];
if ($subscriptionStatusResult) {
    while ($row = mysqli_fetch_assoc($subscriptionStatusResult)) {
        $subscriptionStatusData[] = $row;
    }
}

// subscription_status SQL 업데이트
$response['data']['search']['sql']['subscription_status'] = $subscriptionStatusSql;

// HTML 출력 버퍼링 시작
ob_start();
?>

<section class="card">
  <div class="card-hd card-hd-wrap">
    <div class="card-hd-content">
      <div class="card-hd-title-area">
        <div class="card-ttl">본사 직접 매출 실적</div>
        <div class="card-sub">본사 직접 계약 고객 매출 분석</div>
      </div>
      <div class="filter-toolbar">
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
        <div class="kpi-label">총 사업장수</div>
        <div class="kpi-value"><?php echo number_format($totalSites); ?></div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">평균 매출/고객</div>
        <div class="kpi-value">₩<?php echo $totalCustomers > 0 ? number_format(round($totalRevenue / $totalCustomers)) : 0; ?></div>
      </div>
    </div>
  </div>

  <!-- 월별 매출 추이 -->
  <div class="card-bd-padding section-divider">
    <h3 class="section-title">월별 매출 추이 (최근 6개월)</h3>
    <div class="table-scroll">
      <table class="data-table" id="tblMonthlyRevenue">
        <thead>
          <tr>
            <th>월</th>
            <th>신규 고객수</th>
            <th>총 매출</th>
            <th>전월 대비</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($monthlyRevenueData)): ?>
          <tr>
            <td colspan="4" class="table-text-center text-muted">데이터가 없습니다.</td>
          </tr>
          <?php else: ?>
          <?php
          $prevRevenue = 0;
          foreach ($monthlyRevenueData as $index => $month):
            $changePercent = 0;
            $changeClass = '';
            if ($index > 0 && $prevRevenue > 0) {
              $changePercent = round((($month['total_revenue'] - $prevRevenue) / $prevRevenue) * 100, 1);
              $changeClass = $changePercent > 0 ? 'badge-status-active' : ($changePercent < 0 ? 'badge-status-expired' : 'badge-default');
            }
          ?>
          <tr>
            <td><strong><?php echo htmlspecialchars($month['month']); ?></strong></td>
            <td><?php echo number_format($month['customer_count']); ?></td>
            <td><strong>₩<?php echo number_format($month['total_revenue']); ?></strong></td>
            <td>
              <?php if ($index > 0 && $prevRevenue > 0): ?>
              <span class="badge <?php echo $changeClass; ?>">
                <?php echo $changePercent > 0 ? '+' : ''; ?><?php echo $changePercent; ?>%
              </span>
              <?php else: ?>
              -
              <?php endif; ?>
            </td>
          </tr>
          <?php
            $prevRevenue = $month['total_revenue'];
          endforeach;
          ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- TOP 5 고객 -->
  <div class="card-bd-padding section-divider">
    <h3 class="section-title">매출 TOP 5 고객</h3>
    <div class="table-scroll">
      <table class="data-table" id="tblTopCustomers">
        <thead>
          <tr>
            <th>순위</th>
            <th>고객ID</th>
            <th>고객명</th>
            <th>대표자명</th>
            <th>계약일</th>
            <th>사업장수</th>
            <th>구독수</th>
            <th>총 매출</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($topCustomers)): ?>
          <tr>
            <td colspan="8" class="table-text-center text-muted">데이터가 없습니다.</td>
          </tr>
          <?php else: ?>
          <?php foreach ($topCustomers as $index => $customer): ?>
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
            <td><?php echo htmlspecialchars($customer['customer_id']); ?></td>
            <td><strong><?php echo htmlspecialchars($customer['company_name']); ?></strong></td>
            <td><?php echo htmlspecialchars($customer['ceo_name'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars(substr($customer['contract_date'], 0, 10)); ?></td>
            <td><?php echo number_format($customer['site_count']); ?></td>
            <td><?php echo number_format($customer['subscription_count']); ?></td>
            <td><strong>₩<?php echo number_format($customer['total_revenue']); ?></strong></td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- 전체 고객 상세 -->
  <div class="card-bd-padding section-divider">
    <h3 class="section-title">본사 직접 고객 상세</h3>
    <div class="table-scroll">
      <table class="data-table" id="tblHqCustomers">
        <thead>
          <tr>
            <th>고객ID</th>
            <th>고객명</th>
            <th>대표자명</th>
            <th>계약일</th>
            <th>사업장수</th>
            <th>구독수</th>
            <th>구독료</th>
            <th>총 매출</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($hqCustomersData)): ?>
          <tr>
            <td colspan="8" class="table-text-center text-muted">해당 기간에 본사 직접 고객이 없습니다.</td>
          </tr>
          <?php else: ?>
          <?php foreach ($hqCustomersData as $customer): ?>
          <tr>
            <td><?php echo htmlspecialchars($customer['customer_id']); ?></td>
            <td><strong><?php echo htmlspecialchars($customer['company_name']); ?></strong></td>
            <td><?php echo htmlspecialchars($customer['ceo_name'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars(substr($customer['contract_date'], 0, 10)); ?></td>
            <td><?php echo number_format($customer['site_count']); ?></td>
            <td><?php echo number_format($customer['subscription_count']); ?></td>
            <td>₩<?php echo number_format($customer['subscription_fee']); ?></td>
            <td><strong>₩<?php echo number_format($customer['total_revenue']); ?></strong></td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
        <tfoot>
          <tr class="total-row">
            <td colspan="4"><strong>합계</strong></td>
            <td><?php echo number_format($totalSites); ?></td>
            <td><?php echo number_format($totalSubscriptions); ?></td>
            <td>-</td>
            <td><strong>₩<?php echo number_format($totalRevenue); ?></strong></td>
          </tr>
        </tfoot>
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
          $totalSubscriptionCount = array_sum(array_column($subscriptionStatusData, 'count'));

          if (empty($subscriptionStatusData)):
          ?>
          <tr>
            <td colspan="4" class="table-text-center text-muted">데이터가 없습니다.</td>
          </tr>
          <?php else: ?>
          <?php foreach ($subscriptionStatusData as $status):
            $percentage = $totalSubscriptionCount > 0 ? round(($status['count'] / $totalSubscriptionCount) * 100, 1) : 0;
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
window.pageName = '<?= encryptValue($today . '/perf_hq') ?>';

// 필터 적용
document.getElementById('btnApplyFilter')?.addEventListener('click', function() {
  const startDate = document.getElementById('startDate').value;
  const endDate = document.getElementById('endDate').value;

  const data = {};
  if (startDate) data['<?= encryptValue('start_date') ?>'] = startDate;
  if (endDate) data['<?= encryptValue('end_date') ?>'] = endDate;

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
  const tables = [
    { id: 'tblMonthlyRevenue', name: '월별매출추이' },
    { id: 'tblTopCustomers', name: 'TOP5고객' },
    { id: 'tblHqCustomers', name: '본사고객상세' },
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
  link.download = `HQ_본사매출_${dateStr}.csv`;
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
