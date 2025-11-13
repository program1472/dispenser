<?php
// HQ 고객관리 > 고객 (통합 버전)
// 고객 정보 입력 + 히스토리 검색 (영업성과, 이용현황, 컴플레인)

// 필터 파라미터 (POST 방식으로 변경)
$filterStatus = isset($_POST[encryptValue('status')]) ? $_POST[encryptValue('status')] : (isset($_GET['status']) ? $_GET['status'] : '');
$searchKeyword = isset($_POST[encryptValue('search')]) ? $_POST[encryptValue('search')] : (isset($_GET['search']) ? $_GET['search'] : '');
$selectedCustomerId = isset($_GET['customer_id']) ? $_GET['customer_id'] : '';

// 고객 목록 조회
$sql = "
SELECT
    c.customer_id,
    c.company_name,
    c.vendor_id,
    c.phone,
    c.email,
    c.created_at,
    v.name as vendor_name,
    s.status as subscription_status,
    s.start_date as subscription_start_date,
    COUNT(DISTINCT sit.site_id) as site_count,
    COUNT(DISTINCT d.device_id) as device_count
FROM customers c
LEFT JOIN vendors v ON c.vendor_id = v.vendor_id
LEFT JOIN subscriptions s ON c.customer_id = s.customer_id
LEFT JOIN sites sit ON c.customer_id = sit.customer_id AND sit.is_active = 1
LEFT JOIN device_groups dg ON sit.site_id = dg.site_id
LEFT JOIN devices d ON dg.group_id = d.group_id
WHERE c.is_active = 1
";

if ($filterStatus) {
    $sql .= " AND s.status = ?";
}

if ($searchKeyword) {
    $sql .= " AND c.company_name LIKE ?";
}

$sql .= " GROUP BY c.customer_id
ORDER BY c.created_at DESC
LIMIT 100";

$stmt = mysqli_prepare($con, $sql);

if ($filterStatus && $searchKeyword) {
    $searchParam = "%{$searchKeyword}%";
    mysqli_stmt_bind_param($stmt, 'ss', $filterStatus, $searchParam);
} elseif ($filterStatus) {
    mysqli_stmt_bind_param($stmt, 's', $filterStatus);
} elseif ($searchKeyword) {
    $searchParam = "%{$searchKeyword}%";
    mysqli_stmt_bind_param($stmt, 's', $searchParam);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$customers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $customers[] = $row;
}

mysqli_stmt_close($stmt);

// 벤더 목록 조회
$vendorSql = "SELECT vendor_id, name FROM vendors WHERE is_active = 1 ORDER BY name";
$vendorResult = mysqli_query($con, $vendorSql);
$vendors = [];
while ($row = mysqli_fetch_assoc($vendorResult)) {
    $vendors[] = $row;
}

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
<section id="sec-customer-mgmt" class="card">
  <div class="card-hd">
    <div style="display: flex; flex-direction: column; gap: 20px; flex: 1;">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="card-ttl">고객 관리</div>
        <div class="card-sub">고객정보·히스토리·영업성과·컴플레인 통합관리</div>
      </div>
      <div class="row">
        <select id="filterStatus" class="form-control" style="max-width:150px">
          <option value="">전체 상태</option>
          <option value="ACTIVE" <?php echo $filterStatus === 'ACTIVE' ? 'selected' : ''; ?>>ACTIVE</option>
          <option value="WARNING" <?php echo $filterStatus === 'WARNING' ? 'selected' : ''; ?>>WARNING</option>
          <option value="GRACE" <?php echo $filterStatus === 'GRACE' ? 'selected' : ''; ?>>GRACE</option>
          <option value="TERMINATED" <?php echo $filterStatus === 'TERMINATED' ? 'selected' : ''; ?>>TERMINATED</option>
        </select>
        <input type="text" id="searchCustomer" class="form-control" placeholder="고객명 검색" style="max-width:200px" value="<?php echo htmlspecialchars($searchKeyword); ?>">
        <button id="btnFilter" class="btn primary" style="align-self: flex-end;">조회</button>
      </div>
    </div>
    <div class="row">
      <button id="btnAddCustomer" class="btn primary">고객 추가</button>
      <button id="btnExportCsv" class="btn">CSV 내보내기</button>
    </div>
  </div>
    <div class="card-bd">
      <div class="table-wrap">
        <table class="table" id="tblCustomerList">
          <thead>
            <tr>
              <th style="width:40px"><input type="checkbox" id="chkAll"></th>
              <th>고객ID</th>
              <th>고객명</th>
              <th>담당벤더</th>
              <th>사이트수</th>
              <th>기기수</th>
              <th>구독상태</th>
              <th>가입일</th>
              <th>연락처</th>
              <th>상세</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($customers)): ?>
            <tr>
              <td colspan="10" style="text-align:center; padding:40px;">조회된 데이터가 없습니다.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($customers as $customer): ?>
            <tr data-id="<?php echo htmlspecialchars($customer['customer_id']); ?>" class="<?php echo $selectedCustomerId === $customer['customer_id'] ? 'selected' : ''; ?>">
              <td><input type="checkbox" value="<?php echo htmlspecialchars($customer['customer_id']); ?>" class="chk-customer"></td>
              <td><?php echo htmlspecialchars($customer['customer_id']); ?></td>
              <td><strong><?php echo htmlspecialchars($customer['company_name']); ?></strong></td>
              <td><?php echo htmlspecialchars($customer['vendor_name'] ?? '-'); ?></td>
              <td><?php echo htmlspecialchars($customer['site_count']); ?></td>
              <td><?php echo htmlspecialchars($customer['device_count']); ?></td>
              <td>
                <span class="badge <?php echo getStatusBadgeClass($customer['subscription_status'] ?? 'TERMINATED'); ?>">
                  <?php echo htmlspecialchars($customer['subscription_status'] ?? 'NONE'); ?>
                </span>
              </td>
              <td><?php echo htmlspecialchars(substr($customer['created_at'], 0, 10)); ?></td>
              <td><?php echo htmlspecialchars($customer['phone'] ?? '-'); ?></td>
              <td>
                <button class="btn-sm btn-view-history" data-customer-id="<?php echo htmlspecialchars($customer['customer_id']); ?>">히스토리</button>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <!-- 고객 히스토리 섹션 -->
  <section id="sec-customer-history" class="card" style="display:none; margin-top:20px;">
    <div class="card-hd">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="card-ttl">고객 히스토리: <span id="historyCustomerName"></span></div>
        <div class="card-sub">영업성과·이용현황·컴플레인·메모 통합조회</div>
      </div>
      <div class="row">
        <button id="btnCloseHistory" class="btn">닫기</button>
      </div>
    </div>
    <div class="card-bd">
      <!-- 탭 네비게이션 -->
      <div class="tab-nav">
        <button class="tab-btn active" data-tab="tab-sales">영업성과</button>
        <button class="tab-btn" data-tab="tab-usage">이용현황</button>
        <button class="tab-btn" data-tab="tab-complaints">컴플레인</button>
        <button class="tab-btn" data-tab="tab-notes">메모</button>
      </div>

      <!-- 탭 컨텐츠: 영업성과 -->
      <div id="tab-sales" class="tab-content active">
        <h4>월별 매출 추이</h4>
        <div class="table-wrap">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>월</th>
                <th>구독료</th>
                <th>유료콘텐츠</th>
                <th>유료향</th>
                <th>프린팅</th>
                <th>합계</th>
              </tr>
            </thead>
            <tbody id="tblSalesHistory">
              <tr><td colspan="6" style="text-align:center;">로딩 중...</td></tr>
            </tbody>
          </table>
        </div>
        <h4 style="margin-top:20px">최근 구매 상세 (10건)</h4>
        <div class="table-wrap">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>날짜</th>
                <th>항목</th>
                <th>금액</th>
                <th>상태</th>
                <th>비고</th>
              </tr>
            </thead>
            <tbody id="tblPurchaseDetail">
              <tr><td colspan="5" style="text-align:center;">로딩 중...</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- 탭 컨텐츠: 이용현황 -->
      <div id="tab-usage" class="tab-content">
        <div class="stat-grid">
          <div class="stat-item">
            <label>총 사이트 수</label>
            <span id="statSiteCount" class="stat-value">-</span>
          </div>
          <div class="stat-item">
            <label>총 기기 수</label>
            <span id="statDeviceCount" class="stat-value">-</span>
          </div>
          <div class="stat-item">
            <label>콘텐츠 다운로드</label>
            <span id="statContentDownload" class="stat-value">-</span>
          </div>
          <div class="stat-item">
            <label>향 배송 횟수</label>
            <span id="statScentDelivery" class="stat-value">-</span>
          </div>
        </div>
        <h4 style="margin-top:20px">기기 목록</h4>
        <div class="table-wrap">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>기기 ID</th>
                <th>시리얼</th>
                <th>사이트</th>
                <th>상태</th>
                <th>마지막 활동</th>
              </tr>
            </thead>
            <tbody id="tblDeviceList">
              <tr><td colspan="5" style="text-align:center;">로딩 중...</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- 탭 컨텐츠: 컴플레인 -->
      <div id="tab-complaints" class="tab-content">
        <div class="row" style="margin-bottom:12px">
          <button id="btnAddComplaint" class="btn primary">컴플레인 추가</button>
        </div>
        <div class="table-wrap">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>날짜</th>
                <th>카테고리</th>
                <th>제목</th>
                <th>상태</th>
                <th>담당자</th>
                <th>상세</th>
              </tr>
            </thead>
            <tbody id="tblComplaintList">
              <tr><td colspan="6" style="text-align:center;">로딩 중...</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- 탭 컨텐츠: 메모 -->
      <div id="tab-notes" class="tab-content">
        <div class="row" style="margin-bottom:12px">
          <button id="btnAddNote" class="btn primary">메모 추가</button>
        </div>
        <div id="notesList">
          <p style="text-align:center; color:#999;">로딩 중...</p>
        </div>
      </div>
    </div>
  </section>

<!-- 고객 추가/수정 모달 -->
<div id="modalCustomerForm" class="modal" style="display:none">
  <div class="modal-content">
    <div class="modal-header">
      <h3 id="formTitle">고객 추가</h3>
      <button class="modal-close">&times;</button>
    </div>
    <div class="modal-body">
      <form id="customerForm">
        <input type="hidden" id="customerId" name="customer_id">
        <div class="form-group">
          <label>고객명 (회사명) *</label>
          <input type="text" id="customerName" name="customer_name" class="form-control" required>
        </div>
        <div class="form-group">
          <label>담당벤더 *</label>
          <select id="vendorId" name="vendor_id" class="form-control" required>
            <option value="">선택하세요</option>
            <?php foreach ($vendors as $vendor): ?>
            <option value="<?php echo htmlspecialchars($vendor['vendor_id']); ?>">
              <?php echo htmlspecialchars($vendor['name']); ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>연락처 *</label>
          <input type="tel" id="customerPhone" name="customer_phone" class="form-control" required>
        </div>
        <div class="form-group">
          <label>이메일</label>
          <input type="email" id="customerEmail" name="customer_email" class="form-control">
        </div>
        <div class="form-group">
          <label>주소</label>
          <textarea id="customerAddress" name="customer_address" class="form-control" rows="2"></textarea>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn modal-close">취소</button>
      <button id="btnSaveCustomer" class="btn primary">저장</button>
    </div>
  </div>
</div>

<!-- 컴플레인 추가 모달 -->
<div id="modalComplaintForm" class="modal" style="display:none">
  <div class="modal-content">
    <div class="modal-header">
      <h3>컴플레인 추가</h3>
      <button class="modal-close">&times;</button>
    </div>
    <div class="modal-body">
      <form id="complaintForm">
        <input type="hidden" id="complaintCustomerId">
        <div class="form-group">
          <label>카테고리 *</label>
          <select id="complaintCategory" class="form-control" required>
            <option value="">선택하세요</option>
            <option value="기기오작동">기기오작동</option>
            <option value="콘텐츠오류">콘텐츠오류</option>
            <option value="배송지연">배송지연</option>
            <option value="결제오류">결제오류</option>
            <option value="기타">기타</option>
          </select>
        </div>
        <div class="form-group">
          <label>제목 *</label>
          <input type="text" id="complaintTitle" class="form-control" required>
        </div>
        <div class="form-group">
          <label>내용 *</label>
          <textarea id="complaintContent" class="form-control" rows="4" required></textarea>
        </div>
        <div class="form-group">
          <label>우선순위</label>
          <select id="complaintPriority" class="form-control">
            <option value="낮음">낮음</option>
            <option value="보통" selected>보통</option>
            <option value="높음">높음</option>
            <option value="긴급">긴급</option>
          </select>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn modal-close">취소</button>
      <button id="btnSaveComplaint" class="btn primary">저장</button>
    </div>
  </div>
</div>

<!-- 메모 추가 모달 -->
<div id="modalNoteForm" class="modal" style="display:none">
  <div class="modal-content">
    <div class="modal-header">
      <h3>메모 추가</h3>
      <button class="modal-close">&times;</button>
    </div>
    <div class="modal-body">
      <form id="noteForm">
        <input type="hidden" id="noteCustomerId">
        <div class="form-group">
          <label>메모 내용 *</label>
          <textarea id="noteContent" class="form-control" rows="5" required placeholder="고객 관련 메모를 입력하세요..."></textarea>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn modal-close">취소</button>
      <button id="btnSaveNote" class="btn primary">저장</button>
    </div>
  </div>
</div>

<script>
// 페이지 이름 (AJAX 호출용)
window.pageName = '<?= $pageName ?>';

(function() {
// 현재 선택된 고객 ID
let currentCustomerId = null;

// 필터 조회
$(document).on('click', '#btnFilter', function() {
  const status = document.getElementById('filterStatus').value;
  const search = document.getElementById('searchCustomer').value;

  // 암호화된 POST 데이터 생성
  const data = {};
  if (status) data['<?= encryptValue('status') ?>'] = status;
  if (search) data['<?= encryptValue('search') ?>'] = search;

  // updateAjaxContent로 페이지 다시 로드
  updateAjaxContent(data, function(response) {
    if (response.result === 'ok' && response.html) {
      const contentArea = document.querySelector('#sec-customer-mgmt').parentElement;
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

// 전체 선택
document.getElementById('chkAll')?.addEventListener('change', function() {
  const checkboxes = document.querySelectorAll('.chk-customer');
  checkboxes.forEach(chk => chk.checked = this.checked);
});

// CSV 내보내기
document.getElementById('btnExportCsv')?.addEventListener('click', () => {
  const table = document.getElementById('tblCustomerList');
  const rows = Array.from(table.querySelectorAll('thead tr, tbody tr'));

  const csv = rows.map(row => {
    const cells = Array.from(row.querySelectorAll('th, td'));
    return cells.slice(1, -1).map(cell => {
      if (cell.querySelector('button')) return '';
      const badge = cell.querySelector('.badge');
      if (badge) return badge.textContent.trim();
      return '"' + cell.textContent.trim().replace(/"/g, '""') + '"';
    }).filter(Boolean).join(',');
  }).join('\n');

  const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = 'HQ_고객관리_' + new Date().toISOString().slice(0,10) + '.csv';
  link.click();
});

// 고객 추가
document.getElementById('btnAddCustomer')?.addEventListener('click', () => {
  document.getElementById('formTitle').textContent = '고객 추가';
  document.getElementById('customerForm').reset();
  document.getElementById('customerId').value = '';
  document.getElementById('modalCustomerForm').style.display = 'flex';
});

// 고객 저장
document.getElementById('btnSaveCustomer')?.addEventListener('click', () => {
  const form = document.getElementById('customerForm');
  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  const formData = new FormData(form);
  formData.append('action', document.getElementById('customerId').value ? 'update_customer' : 'add_customer');

  fetch(window.location.href, {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.result) {
      alert('저장되었습니다.');
      window.location.reload();
    } else {
      alert('오류: ' + (data.error?.msg || '알 수 없는 오류'));
    }
  })
  .catch(err => {
    alert('오류: ' + err.message);
  });
});

// 히스토리 보기
document.querySelectorAll('.btn-view-history').forEach(btn => {
  btn.addEventListener('click', function() {
    currentCustomerId = this.getAttribute('data-customer-id');
    const row = this.closest('tr');
    const customerName = row.querySelector('td:nth-child(3)').textContent;

    // 선택된 행 표시
    document.querySelectorAll('#tblCustomerList tbody tr').forEach(r => r.classList.remove('selected'));
    row.classList.add('selected');

    // 히스토리 섹션 표시
    document.getElementById('historyCustomerName').textContent = customerName;
    document.getElementById('sec-customer-history').style.display = 'block';

    // 히스토리 데이터 로드
    loadCustomerHistory(currentCustomerId);

    // 섹션으로 스크롤
    document.getElementById('sec-customer-history').scrollIntoView({ behavior: 'smooth' });
  });
});

// 히스토리 닫기
document.getElementById('btnCloseHistory')?.addEventListener('click', () => {
  document.getElementById('sec-customer-history').style.display = 'none';
  document.querySelectorAll('#tblCustomerList tbody tr').forEach(r => r.classList.remove('selected'));
  currentCustomerId = null;
});

// 탭 전환
document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    const tabId = this.getAttribute('data-tab');

    // 모든 탭 비활성화
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

    // 선택된 탭 활성화
    this.classList.add('active');
    document.getElementById(tabId).classList.add('active');
  });
});

// 고객 히스토리 데이터 로드
function loadCustomerHistory(customerId) {
  // 영업성과 데이터 로드
  fetch(`_ajax_.php?action=getCustomerSalesHistory&customer_id=${customerId}`)
    .then(res => res.json())
    .then(data => {
      if (data.success && data.data) {
        const tbody = document.getElementById('tblSalesHistory');
        tbody.innerHTML = data.data.map(item => `
          <tr>
            <td>${item.month}</td>
            <td>₩${Number(item.subscription).toLocaleString()}</td>
            <td>₩${Number(item.content).toLocaleString()}</td>
            <td>₩${Number(item.scent).toLocaleString()}</td>
            <td>₩${Number(item.printing).toLocaleString()}</td>
            <td><strong>₩${Number(item.total).toLocaleString()}</strong></td>
          </tr>
        `).join('') || '<tr><td colspan="6" style="text-align:center;">데이터 없음</td></tr>';
      }
    });

  // 구매 상세 로드
  fetch(`_ajax_.php?action=getCustomerPurchases&customer_id=${customerId}&limit=10`)
    .then(res => res.json())
    .then(data => {
      if (data.success && data.data) {
        const tbody = document.getElementById('tblPurchaseDetail');
        tbody.innerHTML = data.data.map(item => `
          <tr>
            <td>${item.sale_date}</td>
            <td>${item.description || item.type}</td>
            <td>₩${Number(item.amount).toLocaleString()}</td>
            <td><span class="badge badge-${item.status === 'PAID' ? 'success' : 'warning'}">${item.status}</span></td>
            <td>${item.notes || '-'}</td>
          </tr>
        `).join('') || '<tr><td colspan="5" style="text-align:center;">데이터 없음</td></tr>';
      }
    });

  // 이용현황 데이터 로드
  fetch(`_ajax_.php?action=getCustomerUsageStats&customer_id=${customerId}`)
    .then(res => res.json())
    .then(data => {
      if (data.success && data.data) {
        document.getElementById('statSiteCount').textContent = data.data.site_count || '0';
        document.getElementById('statDeviceCount').textContent = data.data.device_count || '0';
        document.getElementById('statContentDownload').textContent = data.data.content_downloads || '0';
        document.getElementById('statScentDelivery').textContent = data.data.scent_deliveries || '0';
      }
    });

  // 기기 목록 로드
  fetch(`_ajax_.php?action=getCustomerDevices&customer_id=${customerId}`)
    .then(res => res.json())
    .then(data => {
      if (data.success && data.data) {
        const tbody = document.getElementById('tblDeviceList');
        tbody.innerHTML = data.data.map(item => `
          <tr>
            <td>${item.device_id}</td>
            <td>${item.serial}</td>
            <td>${item.site_name}</td>
            <td><span class="badge badge-${item.is_active ? 'success' : 'secondary'}">${item.is_active ? '활성' : '비활성'}</span></td>
            <td>${item.last_activity || '-'}</td>
          </tr>
        `).join('') || '<tr><td colspan="5" style="text-align:center;">데이터 없음</td></tr>';
      }
    });

  // 컴플레인 목록 로드
  fetch(`_ajax_.php?action=getCustomerComplaints&customer_id=${customerId}`)
    .then(res => res.json())
    .then(data => {
      if (data.success && data.data) {
        const tbody = document.getElementById('tblComplaintList');
        tbody.innerHTML = data.data.map(item => `
          <tr>
            <td>${item.created_at}</td>
            <td>${item.category}</td>
            <td>${item.title}</td>
            <td><span class="badge badge-${item.status === '완료' ? 'success' : 'warning'}">${item.status}</span></td>
            <td>${item.assignee || '-'}</td>
            <td><button class="btn-sm" onclick="viewComplaintDetail(${item.complaint_id})">상세</button></td>
          </tr>
        `).join('') || '<tr><td colspan="6" style="text-align:center;">데이터 없음</td></tr>';
      }
    });

  // 메모 목록 로드
  fetch(`_ajax_.php?action=getCustomerNotes&customer_id=${customerId}`)
    .then(res => res.json())
    .then(data => {
      if (data.success && data.data) {
        const notesList = document.getElementById('notesList');
        notesList.innerHTML = data.data.map(item => `
          <div class="note-item">
            <div class="note-header">
              <span class="note-author">${item.author}</span>
              <span class="note-date">${item.created_at}</span>
            </div>
            <div class="note-content">${item.content}</div>
          </div>
        `).join('') || '<p style="text-align:center; color:#999;">메모가 없습니다.</p>';
      }
    });
}

// 컴플레인 추가
document.getElementById('btnAddComplaint')?.addEventListener('click', () => {
  document.getElementById('complaintCustomerId').value = currentCustomerId;
  document.getElementById('complaintForm').reset();
  document.getElementById('modalComplaintForm').style.display = 'flex';
});

// 컴플레인 저장
document.getElementById('btnSaveComplaint')?.addEventListener('click', () => {
  const form = document.getElementById('complaintForm');
  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  const formData = new FormData();
  formData.append('action', 'add_complaint');
  formData.append('customer_id', document.getElementById('complaintCustomerId').value);
  formData.append('category', document.getElementById('complaintCategory').value);
  formData.append('title', document.getElementById('complaintTitle').value);
  formData.append('content', document.getElementById('complaintContent').value);
  formData.append('priority', document.getElementById('complaintPriority').value);

  fetch(window.location.href, {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.result) {
      alert('컴플레인이 등록되었습니다.');
      document.getElementById('modalComplaintForm').style.display = 'none';
      loadCustomerHistory(currentCustomerId);
    } else {
      alert('오류: ' + (data.error?.msg || '알 수 없는 오류'));
    }
  });
});

// 메모 추가
document.getElementById('btnAddNote')?.addEventListener('click', () => {
  document.getElementById('noteCustomerId').value = currentCustomerId;
  document.getElementById('noteForm').reset();
  document.getElementById('modalNoteForm').style.display = 'flex';
});

// 메모 저장
document.getElementById('btnSaveNote')?.addEventListener('click', () => {
  const content = document.getElementById('noteContent').value.trim();
  if (!content) {
    alert('메모 내용을 입력하세요.');
    return;
  }

  const formData = new FormData();
  formData.append('action', 'add_note');
  formData.append('customer_id', document.getElementById('noteCustomerId').value);
  formData.append('content', content);

  fetch(window.location.href, {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.result) {
      alert('메모가 저장되었습니다.');
      document.getElementById('modalNoteForm').style.display = 'none';
      loadCustomerHistory(currentCustomerId);
    } else {
      alert('오류: ' + (data.error?.msg || '알 수 없는 오류'));
    }
  });
});

// 모달 닫기
document.querySelectorAll('.modal-close').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.modal').forEach(modal => modal.style.display = 'none');
  });
});

// ESC 키로 모달 닫기
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    document.querySelectorAll('.modal').forEach(modal => modal.style.display = 'none');
  }
});
</script>
