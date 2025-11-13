<?php
// HQ 실적 > 고객

// 필터 파라미터 (POST 방식으로 변경)
$filterMonth = isset($_POST[encryptValue('month')]) ? $_POST[encryptValue('month')] : (isset($_GET['month']) ? $_GET['month'] : date('Y-m'));
$searchKeyword = isset($_POST[encryptValue('search')]) ? $_POST[encryptValue('search')] : (isset($_GET['search']) ? $_GET['search'] : '');

// 고객 실적 데이터 조회
$sql = "
SELECT
    c.customer_id,
    c.company_name,
    c.vendor_id,
    v.name as vendor_name,
    s.status as subscription_status,
    s.start_date as subscription_start_date,
    COALESCE(SUM(CASE WHEN sal.type = 'SUBSCRIPTION' THEN sal.amount ELSE 0 END), 0) as total_subscription,
    COALESCE(SUM(CASE WHEN sal.type = 'CONTENT' THEN sal.amount ELSE 0 END), 0) as total_content,
    COALESCE(SUM(CASE WHEN sal.type = 'SCENT' THEN sal.amount ELSE 0 END), 0) as total_scent,
    COALESCE(SUM(CASE WHEN sal.type = 'PRINTING' THEN sal.amount ELSE 0 END), 0) as total_printing,
    COALESCE(SUM(sal.amount), 0) as total_sales,
    COUNT(DISTINCT sit.site_id) as site_count,
    COUNT(DISTINCT dg.group_id) as device_count
FROM customers c
LEFT JOIN vendors v ON c.vendor_id = v.vendor_id
LEFT JOIN subscriptions s ON c.customer_id = s.customer_id
LEFT JOIN sales sal ON c.customer_id = sal.customer_id
    AND DATE_FORMAT(sal.sale_date, '%Y-%m') = ?
    AND sal.status = 'PAID'
LEFT JOIN sites sit ON c.customer_id = sit.customer_id AND sit.is_active = 1
LEFT JOIN device_groups dg ON sit.site_id = dg.site_id
WHERE c.is_active = 1
";

if ($searchKeyword) {
    $sql .= " AND c.company_name LIKE ?";
}

$sql .= " GROUP BY c.customer_id
ORDER BY total_sales DESC
LIMIT 100";

$stmt = mysqli_prepare($con, $sql);

if ($searchKeyword) {
    $searchParam = "%{$searchKeyword}%";
    mysqli_stmt_bind_param($stmt, 'ss', $filterMonth, $searchParam);
} else {
    mysqli_stmt_bind_param($stmt, 's', $filterMonth);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$customers = [];
$totalStats = [
    'total_sales' => 0,
    'total_subscription' => 0,
    'total_content' => 0,
    'total_scent' => 0,
    'total_printing' => 0
];

while ($row = mysqli_fetch_assoc($result)) {
    $customers[] = $row;
    $totalStats['total_sales'] += $row['total_sales'];
    $totalStats['total_subscription'] += $row['total_subscription'];
    $totalStats['total_content'] += $row['total_content'];
    $totalStats['total_scent'] += $row['total_scent'];
    $totalStats['total_printing'] += $row['total_printing'];
}

mysqli_stmt_close($stmt);

// 상태 배지 클래스 매핑
function getStatusBadgeClass($status) {
    $map = [
        'ACTIVE' => 'badge-success',
        'WARNING' => 'badge-warning',
        'GRACE' => 'badge-danger',
        'TERMINATED' => 'badge-secondary'
    ];
    return $map[$status] ?? 'badge-secondary';
}

// 금액 포맷
function formatCurrency($amount) {
    return '₩' . number_format($amount);
}
?>

<div class="wrap">
  <section id="sec-customer-perf" class="card section-card-first">
    <div class="card-hd card-hd-wrap">
      <div class="card-hd-content">
        <div class="card-hd-title-area">
          <div class="card-ttl">고객 실적 관리</div>
          <div class="card-sub">구매내역·매출·구독 상태 분석</div>
        </div>
        <div class="row filter-row">
          <div class="form-group-inline">
            <label>월</label>
            <input type="month" id="filterMonth" class="form-control input-w-180" value="<?php echo htmlspecialchars($filterMonth); ?>">
          </div>
          <div class="form-group-inline">
            <label>검색</label>
            <input type="text" id="searchCustomer" class="form-control input-w-200" placeholder="고객명 검색" value="<?php echo htmlspecialchars($searchKeyword); ?>">
          </div>
          <button id="btnFilter" class="btn primary btn-align-end">조회</button>
        </div>
      </div>
      <div class="row">
        <button id="btnExportCsv" class="btn">CSV 내보내기</button>
      </div>
    </div>
    <div class="card-bd">
      <div class="table-wrap">
        <table class="table" id="tblCustomerPerf">
          <thead>
            <tr>
              <th>고객ID</th>
              <th>고객명</th>
              <th>구독상태</th>
              <th>구독시작일</th>
              <th>총매출</th>
              <th>구독료</th>
              <th>유료콘텐츠</th>
              <th>유료향</th>
              <th>프린팅</th>
              <th>담당벤더</th>
              <th>상세</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($customers)): ?>
            <tr>
              <td colspan="11" style="text-align:center; padding:40px;">조회된 데이터가 없습니다.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($customers as $customer): ?>
            <tr data-id="<?php echo htmlspecialchars($customer['customer_id']); ?>">
              <td><?php echo htmlspecialchars($customer['customer_id']); ?></td>
              <td><?php echo htmlspecialchars($customer['company_name']); ?></td>
              <td>
                <span class="badge <?php echo getStatusBadgeClass($customer['subscription_status'] ?? 'TERMINATED'); ?>">
                  <?php echo htmlspecialchars($customer['subscription_status'] ?? 'NONE'); ?>
                </span>
              </td>
              <td><?php echo htmlspecialchars($customer['subscription_start_date'] ?? '-'); ?></td>
              <td><?php echo formatCurrency($customer['total_sales']); ?></td>
              <td><?php echo formatCurrency($customer['total_subscription']); ?></td>
              <td><?php echo formatCurrency($customer['total_content']); ?></td>
              <td><?php echo formatCurrency($customer['total_scent']); ?></td>
              <td><?php echo formatCurrency($customer['total_printing']); ?></td>
              <td><?php echo htmlspecialchars($customer['vendor_id']); ?></td>
              <td><button class="btn-sm btn-detail" data-customer-id="<?php echo htmlspecialchars($customer['customer_id']); ?>">상세</button></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
          <tfoot>
            <tr>
              <th colspan="4">합계</th>
              <th><?php echo formatCurrency($totalStats['total_sales']); ?></th>
              <th><?php echo formatCurrency($totalStats['total_subscription']); ?></th>
              <th><?php echo formatCurrency($totalStats['total_content']); ?></th>
              <th><?php echo formatCurrency($totalStats['total_scent']); ?></th>
              <th><?php echo formatCurrency($totalStats['total_printing']); ?></th>
              <th colspan="2"></th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </section>
</div>

<!-- 고객 상세 모달 -->
<div id="modalCustomerDetail" class="modal" style="display:none">
  <div class="modal-content">
    <div class="modal-header">
      <h3>고객 상세 정보</h3>
      <button class="modal-close">&times;</button>
    </div>
    <div class="modal-body" id="modalBodyCustomer">
      <div class="detail-grid">
        <div class="detail-item"><label>고객ID:</label><span id="dtl-customer-id"></span></div>
        <div class="detail-item"><label>고객명:</label><span id="dtl-customer-name"></span></div>
        <div class="detail-item"><label>구독상태:</label><span id="dtl-status"></span></div>
        <div class="detail-item"><label>구독시작일:</label><span id="dtl-start-date"></span></div>
        <div class="detail-item"><label>총매출:</label><span id="dtl-total"></span></div>
        <div class="detail-item"><label>구독료:</label><span id="dtl-subscription"></span></div>
        <div class="detail-item"><label>유료콘텐츠:</label><span id="dtl-content"></span></div>
        <div class="detail-item"><label>유료향:</label><span id="dtl-scent"></span></div>
        <div class="detail-item"><label>프린팅:</label><span id="dtl-printing"></span></div>
        <div class="detail-item"><label>담당벤더:</label><span id="dtl-vendor"></span></div>
      </div>
      <h4 style="margin-top:20px">최근 구매 내역 (5건)</h4>
      <table class="table">
        <thead>
          <tr><th>날짜</th><th>항목</th><th>금액</th><th>상태</th></tr>
        </thead>
        <tbody id="dtl-purchases">
          <tr><td colspan="4" style="text-align:center;">로딩 중...</td></tr>
        </tbody>
      </table>
    </div>
    <div class="modal-footer">
      <button class="btn modal-close">닫기</button>
    </div>
  </div>
</div>

<script>
// 필터 조회
document.getElementById('btnFilter')?.addEventListener('click', function() {
  const month = document.getElementById('filterMonth').value;
  const search = document.getElementById('searchCustomer').value;

  // 암호화된 POST 데이터 생성
  const data = {};
  if (month) data['<?= encryptValue('month') ?>'] = month;
  if (search) data['<?= encryptValue('search') ?>'] = search;

  // updateAjaxContent로 페이지 다시 로드
  updateAjaxContent(data, function(response) {
    if (response.result === 'ok' && response.html) {
      const contentArea = document.querySelector('#sec-customer-perf').parentElement;
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

// 엔터키로 검색
document.getElementById('searchCustomer')?.addEventListener('keypress', function(e) {
  if (e.key === 'Enter') {
    document.getElementById('btnFilter').click();
  }
});

// CSV 내보내기
document.getElementById('btnExportCsv')?.addEventListener('click', () => {
  const table = document.getElementById('tblCustomerPerf');
  const rows = Array.from(table.querySelectorAll('thead tr, tbody tr, tfoot tr'));

  const csv = rows.map(row => {
    const cells = Array.from(row.querySelectorAll('th, td'));
    return cells.map(cell => {
      if (cell.querySelector('button')) return '';
      const badge = cell.querySelector('.badge');
      if (badge) return badge.textContent.trim();
      return '"' + cell.textContent.trim().replace(/"/g, '""') + '"';
    }).filter(Boolean).join(',');
  }).join('\n');

  const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = 'HQ_고객실적_' + new Date().toISOString().slice(0,10) + '.csv';
  link.click();
});

// 상세보기
document.querySelectorAll('.btn-detail').forEach(btn => {
  btn.addEventListener('click', function() {
    const customerId = this.getAttribute('data-customer-id');
    const row = this.closest('tr');
    const cells = row.querySelectorAll('td');

    document.getElementById('dtl-customer-id').textContent = cells[0].textContent;
    document.getElementById('dtl-customer-name').textContent = cells[1].textContent;
    document.getElementById('dtl-status').innerHTML = cells[2].innerHTML;
    document.getElementById('dtl-start-date').textContent = cells[3].textContent;
    document.getElementById('dtl-total').textContent = cells[4].textContent;
    document.getElementById('dtl-subscription').textContent = cells[5].textContent;
    document.getElementById('dtl-content').textContent = cells[6].textContent;
    document.getElementById('dtl-scent').textContent = cells[7].textContent;
    document.getElementById('dtl-printing').textContent = cells[8].textContent;
    document.getElementById('dtl-vendor').textContent = cells[9].textContent;

    // AJAX로 최근 구매 내역 조회
    fetch('_ajax_.php?action=getCustomerPurchases&customer_id=' + customerId + '&limit=5')
      .then(res => res.json())
      .then(data => {
        if (data.success && data.data) {
          const tbody = document.getElementById('dtl-purchases');
          tbody.innerHTML = data.data.map(item => `
            <tr>
              <td>${item.sale_date}</td>
              <td>${item.description || item.type}</td>
              <td>₩${Number(item.amount).toLocaleString()}</td>
              <td><span class="badge badge-${item.status === 'PAID' ? 'success' : 'warning'}">${item.status}</span></td>
            </tr>
          `).join('');
        }
      })
      .catch(err => {
        document.getElementById('dtl-purchases').innerHTML = '<tr><td colspan="4" style="text-align:center;">데이터를 불러올 수 없습니다.</td></tr>';
      });

    document.getElementById('modalCustomerDetail').style.display = 'flex';
  });
});

// 모달 닫기
document.querySelectorAll('.modal-close').forEach(btn => {
  btn.addEventListener('click', () => {
    document.getElementById('modalCustomerDetail').style.display = 'none';
  });
});

// ESC 키로 모달 닫기
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    document.getElementById('modalCustomerDetail').style.display = 'none';
  }
});
</script>

