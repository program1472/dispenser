<?php
/**
 * HQ 실적관리 > 영업사원별 실적
 * 영업사원별 매출 및 인센티브 현황
 */

// AJAX 페이지네이션 요청 처리 (POST는 이미 _ajax_.php에서 복호화됨)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['p'])) {
    $startDate = isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-01');
    $endDate = isset($_POST['end_date']) ? $_POST['end_date'] : date('Y-m-t');
    $salesRepFilter = isset($_POST['sales_rep_id']) ? $_POST['sales_rep_id'] : '';

    $startDate = mysqli_real_escape_string($con, $startDate);
    $endDate = mysqli_real_escape_string($con, $endDate);
    $salesRepFilter = mysqli_real_escape_string($con, $salesRepFilter);

    // WHERE 조건
    $whereCondition = "u.deleted_at IS NULL AND r.role_name = 'SALES_REP'";
    if ($salesRepFilter) {
        $whereCondition .= " AND u.user_id = '{$salesRepFilter}'";
    }

    // 영업사원별 매출 조회
    $salesRepSql = "
    SELECT
        u.user_id,
        u.name as sales_rep_name,
        u.email,
        u.phone,
        r.role_name,
        COUNT(DISTINCT aa.customer_id) as total_customers,
        COUNT(DISTINCT CASE WHEN c.created_at BETWEEN '{$startDate}' AND '{$endDate}' THEN c.customer_id END) as new_customers,
        COUNT(DISTINCT s.subscription_id) as total_subscriptions,
        COALESCE(SUM(s.monthly_fee), 0) as subscription_revenue,
        COALESCE(SUM(s.monthly_fee), 0) as total_revenue,
        ROUND(COALESCE(SUM(s.monthly_fee), 0) * 0.05, 0) as incentive_5,
        ROUND(COALESCE(SUM(s.monthly_fee), 0) * 0.10, 0) as incentive_10
    FROM users u
    INNER JOIN roles r ON u.role_id = r.role_id
    LEFT JOIN account_assignments aa ON u.user_id = aa.sales_user_id AND aa.is_active = 1
    LEFT JOIN customers c ON aa.customer_id = c.customer_id AND c.is_active = 1
    LEFT JOIN subscriptions s ON c.customer_id = s.customer_id AND s.deleted_at IS NULL AND s.status = 'ACTIVE'
    WHERE {$whereCondition}
    GROUP BY u.user_id, u.name, u.email, u.phone, r.role_name
    HAVING total_revenue > 0
    ORDER BY total_revenue DESC";

    // Pagination config
    $paginationConfig = [
        'table' => 'users u',
        'where' => $whereCondition,
        'join' => 'INNER JOIN roles r ON u.role_id = r.role_id
                   LEFT JOIN account_assignments aa ON u.user_id = aa.sales_user_id AND aa.is_active = 1
                   LEFT JOIN customers c ON aa.customer_id = c.customer_id AND c.is_active = 1
                   LEFT JOIN subscriptions s ON c.customer_id = s.customer_id AND s.deleted_at IS NULL AND s.status = \'ACTIVE\'',
        'orderBy' => 'total_revenue DESC',
        'rowsPerPage' => $defaultRowsPage,
        'targetId' => '#tblSalesRepDetailBody',
        'atValue' => encryptValue('10')
    ];

    $rowsPage = $paginationConfig['rowsPerPage'];
    $p = $_POST['p'] ?? 1;
    $curPage = $rowsPage * ($p - 1);

    $salesRepSql .= " LIMIT {$curPage}, {$rowsPage}";

    $result = mysqli_query($con, $salesRepSql);

    // Pagination 생성
    require INC_ROOT . '/common_pagination.php';
    $response['pagination'] = $pagination ?? '';

    // tbody HTML 생성
    $html = '';
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($row['user_id']) . '</td>';
            $html .= '<td><strong>' . htmlspecialchars($row['sales_rep_name']) . '</strong></td>';
            $html .= '<td>' . htmlspecialchars($row['phone'] ?? '-') . '</td>';
            $html .= '<td>' . htmlspecialchars($row['email'] ?? '-') . '</td>';
            $html .= '<td>' . number_format($row['total_customers']) . '</td>';
            $html .= '<td>';
            if ($row['new_customers'] > 0) {
                $html .= '<span class="badge badge-status-active">+' . number_format($row['new_customers']) . '</span>';
            } else {
                $html .= '-';
            }
            $html .= '</td>';
            $html .= '<td>' . number_format($row['total_subscriptions']) . '</td>';
            $html .= '<td>₩' . number_format($row['subscription_revenue']) . '</td>';
            $html .= '<td><strong>₩' . number_format($row['total_revenue']) . '</strong></td>';
            $html .= '<td class="text-ok"><strong>₩' . number_format($row['incentive_5']) . '</strong></td>';
            $html .= '<td class="text-ok">₩' . number_format($row['incentive_10']) . '</td>';
            $html .= '</tr>';
        }
    } else {
        $html = '<tr><td colspan="11" class="table-text-center text-muted">해당 기간에 실적 데이터가 없습니다.</td></tr>';
    }

    $response['result'] = true;
    $response['html'] = $html;
    Finish();
}

// 필터 파라미터 (POST는 이미 _ajax_.php에서 복호화됨)
$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : (isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01'));
$endDate = isset($_POST['end_date']) ? $_POST['end_date'] : (isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t'));
$salesRepFilter = isset($_POST['sales_rep_id']) ? $_POST['sales_rep_id'] : (isset($_GET['sales_rep_id']) ? $_GET['sales_rep_id'] : '');

// SQL Injection 방지
$startDate = mysqli_real_escape_string($con, $startDate);
$endDate = mysqli_real_escape_string($con, $endDate);
$salesRepFilter = mysqli_real_escape_string($con, $salesRepFilter);

// 영업사원 목록 (필터용) - roles 테이블에서 SALES_REP 역할 확인
$salesRepListSql = "
SELECT u.user_id, u.name, r.role_name
FROM users u
INNER JOIN roles r ON u.role_id = r.role_id
WHERE u.deleted_at IS NULL
  AND r.role_name = 'SALES_REP'
ORDER BY u.name
";
$salesRepListResult = mysqli_query($con, $salesRepListSql);
$salesReps = [];
if ($salesRepListResult) {
    while ($row = mysqli_fetch_assoc($salesRepListResult)) {
        $salesReps[] = $row;
    }
}

// 영업사원별 매출 조회
$salesRepSql = "
SELECT
    u.user_id,
    u.name as sales_rep_name,
    u.email,
    u.phone,
    r.role_name,
    COUNT(DISTINCT aa.customer_id) as total_customers,
    COUNT(DISTINCT CASE WHEN c.created_at BETWEEN '{$startDate}' AND '{$endDate}' THEN c.customer_id END) as new_customers,
    COUNT(DISTINCT s.subscription_id) as total_subscriptions,
    COALESCE(SUM(s.monthly_fee), 0) as subscription_revenue,
    COALESCE(SUM(s.monthly_fee), 0) as total_revenue,
    ROUND(COALESCE(SUM(s.monthly_fee), 0) * 0.05, 0) as incentive_5,
    ROUND(COALESCE(SUM(s.monthly_fee), 0) * 0.10, 0) as incentive_10
FROM users u
INNER JOIN roles r ON u.role_id = r.role_id
LEFT JOIN account_assignments aa ON u.user_id = aa.sales_user_id AND aa.is_active = 1
LEFT JOIN customers c ON aa.customer_id = c.customer_id AND c.is_active = 1
LEFT JOIN subscriptions s ON c.customer_id = s.customer_id AND s.deleted_at IS NULL AND s.status = 'ACTIVE'
WHERE u.deleted_at IS NULL
  AND r.role_name = 'SALES_REP'
" . ($salesRepFilter ? " AND u.user_id = '{$salesRepFilter}'" : "") . "
GROUP BY u.user_id, u.name, u.email, u.phone, r.role_name
HAVING total_revenue > 0
ORDER BY total_revenue DESC
";

// SQL 로그 추가
$response['data']['search']['sql'] = $salesRepSql;

$salesRepResult = mysqli_query($con, $salesRepSql);
$salesRepData = [];
$totalRevenue = 0;
$totalIncentive = 0;
$totalNewCustomers = 0;

if ($salesRepResult) {
    while ($row = mysqli_fetch_assoc($salesRepResult)) {
        $salesRepData[] = $row;
        $totalRevenue += $row['total_revenue'];
        $totalIncentive += $row['incentive_5'];
        $totalNewCustomers += $row['new_customers'];
    }
}

// TOP 5 영업사원
$topSalesReps = array_slice($salesRepData, 0, 5);

// 신규 고객 확보 TOP 5
$newCustomerTopSql = "
SELECT
    u.user_id,
    u.name as sales_rep_name,
    r.role_name,
    COUNT(DISTINCT c.customer_id) as new_customer_count
FROM users u
INNER JOIN roles r ON u.role_id = r.role_id
LEFT JOIN account_assignments aa ON u.user_id = aa.sales_user_id AND aa.is_active = 1
LEFT JOIN customers c ON aa.customer_id = c.customer_id
    AND c.created_at BETWEEN '{$startDate}' AND '{$endDate}'
    AND c.is_active = 1
WHERE u.deleted_at IS NULL
  AND r.role_name = 'SALES_REP'
GROUP BY u.user_id, u.name, r.role_name
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

// HTML 출력 버퍼링 시작
ob_start();
?>

<section class="card">
  <div class="card-hd card-hd-wrap">
    <div class="card-hd-content">
      <div class="card-hd-title-area">
        <div class="card-ttl">영업사원별 매출 실적</div>
        <div class="card-sub">영업사원별 실적 분석 및 인센티브 관리</div>
      </div>
      <div class="filter-toolbar">
        <div class="filter-group">
          <label>영업사원</label>
          <select id="salesRepFilter" name="sales_rep_id" class="form-control input-w-200">
            <option value="">전체 영업사원</option>
            <?php foreach ($salesReps as $rep): ?>
            <option value="<?php echo htmlspecialchars($rep['user_id']); ?>"
                    <?php echo $salesRepFilter === $rep['user_id'] ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($rep['name']); ?>
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
        <div class="kpi-label">신규 고객수</div>
        <div class="kpi-value"><?php echo number_format($totalNewCustomers); ?></div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">활성 영업사원</div>
        <div class="kpi-value"><?php echo count($salesRepData); ?></div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">총 인센티브 (5%)</div>
        <div class="kpi-value ok">₩<?php echo number_format($totalIncentive); ?></div>
      </div>
    </div>
  </div>

  <!-- 그리드: TOP 5 & 신규고객 TOP 5 -->
  <div class="grid-2 card-bd-padding section-divider">
    <!-- TOP 5 매출 -->
    <div>
      <h3 class="section-title">매출 TOP 5</h3>
      <div class="table-scroll">
        <table class="data-table" id="tblTopSales">
          <thead>
            <tr>
              <th>순위</th>
              <th>영업사원</th>
              <th>총 매출</th>
              <th>인센티브 (5%)</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($topSalesReps)): ?>
            <tr>
              <td colspan="4" class="table-text-center text-muted">데이터가 없습니다.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($topSalesReps as $index => $rep): ?>
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
              <td><strong><?php echo htmlspecialchars($rep['sales_rep_name']); ?></strong></td>
              <td><strong>₩<?php echo number_format($rep['total_revenue']); ?></strong></td>
              <td class="text-ok"><strong>₩<?php echo number_format($rep['incentive_5']); ?></strong></td>
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
              <th>영업사원</th>
              <th>신규 고객수</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($newCustomerTopData)): ?>
            <tr>
              <td colspan="3" class="table-text-center text-muted">데이터가 없습니다.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($newCustomerTopData as $index => $rep): ?>
            <tr>
              <td><strong><?php echo $index + 1; ?></strong></td>
              <td><strong><?php echo htmlspecialchars($rep['sales_rep_name']); ?></strong></td>
              <td>
                <span class="badge badge-status-active">+<?php echo number_format($rep['new_customer_count']); ?></span>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- 전체 영업사원 상세 실적 -->
  <div class="card-bd-padding">
    <h3 class="section-title">영업사원별 상세 실적</h3>
    <div class="table-scroll">
      <table class="data-table" id="tblSalesRepDetail">
        <thead>
          <tr>
            <th>사원ID</th>
            <th>사원명</th>
            <th>연락처</th>
            <th>이메일</th>
            <th>총 고객수</th>
            <th>신규 고객수</th>
            <th>구독수</th>
            <th>구독료 매출</th>
            <th>총 매출</th>
            <th>인센티브 5%</th>
            <th>인센티브 10%</th>
          </tr>
        </thead>
        <tbody id="tblSalesRepDetailBody">
          <?php if (empty($salesRepData)): ?>
          <tr>
            <td colspan="11" class="table-text-center text-muted">해당 기간에 실적 데이터가 없습니다.</td>
          </tr>
          <?php else: ?>
          <?php foreach ($salesRepData as $rep): ?>
          <tr>
            <td><?php echo htmlspecialchars($rep['user_id']); ?></td>
            <td><strong><?php echo htmlspecialchars($rep['sales_rep_name']); ?></strong></td>
            <td><?php echo htmlspecialchars($rep['phone'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars($rep['email'] ?? '-'); ?></td>
            <td><?php echo number_format($rep['total_customers']); ?></td>
            <td>
              <?php if ($rep['new_customers'] > 0): ?>
              <span class="badge badge-status-active">+<?php echo number_format($rep['new_customers']); ?></span>
              <?php else: ?>
              -
              <?php endif; ?>
            </td>
            <td><?php echo number_format($rep['total_subscriptions']); ?></td>
            <td>₩<?php echo number_format($rep['subscription_revenue']); ?></td>
            <td><strong>₩<?php echo number_format($rep['total_revenue']); ?></strong></td>
            <td class="text-ok"><strong>₩<?php echo number_format($rep['incentive_5']); ?></strong></td>
            <td class="text-ok">₩<?php echo number_format($rep['incentive_10']); ?></td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
        <tfoot>
          <tr class="total-row">
            <td colspan="4"><strong>합계</strong></td>
            <td>-</td>
            <td><strong><?php echo number_format($totalNewCustomers); ?></strong></td>
            <td>-</td>
            <td>-</td>
            <td><strong>₩<?php echo number_format($totalRevenue); ?></strong></td>
            <td class="text-ok"><strong>₩<?php echo number_format($totalIncentive); ?></strong></td>
            <td>-</td>
          </tr>
        </tfoot>
      </table>
    </div>

    <!-- 페이징 영역 -->
    <div class="paging" data-id="#tblSalesRepDetailBody"></div>
  </div>
</section>

<script>
// 페이지 이름 설정
window.pageName = '<?= encryptValue(date('Y-m-d') . '/perf_sales') ?>';

// 필터 적용
document.getElementById('btnApplyFilter')?.addEventListener('click', function() {
  const startDate = document.getElementById('startDate').value;
  const endDate = document.getElementById('endDate').value;
  const salesRepId = document.getElementById('salesRepFilter').value;

  // 암호화된 POST 데이터 생성
  const data = {};
  if (startDate) data['<?= encryptValue('start_date') ?>'] = startDate;
  if (endDate) data['<?= encryptValue('end_date') ?>'] = endDate;
  if (salesRepId) data['<?= encryptValue('sales_rep_id') ?>'] = salesRepId;

  // updateAjaxContent로 탭 내용만 업데이트
  updateAjaxContent(data, function(response) {
    if (response.result === 'ok' && response.html) {
      const contentArea = document.querySelector('#perf-tab-content');
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
document.getElementById('btnExportCsv')?.addEventListener('click', function() {
  const table = document.getElementById('tblSalesRepDetail');
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
  link.download = `HQ_영업사원별실적_${dateStr}.csv`;
  link.click();
});

// 리포트 출력
document.getElementById('btnPrintReport')?.addEventListener('click', function() {
  window.print();
});

// 날짜 프리셋 함수
function setDate(preset) {
  const startDateInput = document.getElementById('startDate');
  const endDateInput = document.getElementById('endDate');
  const today = new Date();

  let startDate, endDate;

  switch(preset) {
    case 'today':
      startDate = endDate = today;
      break;
    case 'thisWeek':
      const firstDayOfWeek = new Date(today.setDate(today.getDate() - today.getDay()));
      startDate = firstDayOfWeek;
      endDate = new Date();
      break;
    case 'prevWeek':
      const lastWeek = new Date(today.setDate(today.getDate() - 7));
      const firstDayOfLastWeek = new Date(lastWeek.setDate(lastWeek.getDate() - lastWeek.getDay()));
      const lastDayOfLastWeek = new Date(firstDayOfLastWeek);
      lastDayOfLastWeek.setDate(firstDayOfLastWeek.getDate() + 6);
      startDate = firstDayOfLastWeek;
      endDate = lastDayOfLastWeek;
      break;
    case 'thisMonth':
      startDate = new Date(today.getFullYear(), today.getMonth(), 1);
      endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
      break;
    case 'prevMonth':
      startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
      endDate = new Date(today.getFullYear(), today.getMonth(), 0);
      break;
    case '30days':
      startDate = new Date(today.setDate(today.getDate() - 30));
      endDate = new Date();
      break;
    default:
      return;
  }

  startDateInput.value = startDate.toISOString().split('T')[0];
  endDateInput.value = endDate.toISOString().split('T')[0];
}
</script>

<?php
// HTML 버퍼 캡처 및 응답 생성
$response['html'] = ob_get_clean();
$response['result'] = 'ok';
Finish();
?>
