<?php
// HQ 고객 관리

// POST 핸들러 처리
if (!empty($_POST)) {
    header('Content-Type: application/json; charset=utf-8');

    $action = $_POST['action'] ?? '';

    // SQL Injection 방지를 위한 이스케이프 함수
    function escapeInput($con, $value) {
        return mysqli_real_escape_string($con, trim($value));
    }

    // Customer ID 생성 함수 (CYYYYMMDDNNNN 패턴)
    function generateCustomerId($con) {
        $today = date('Ymd');
        $prefix = 'C' . $today;

        // 오늘 날짜로 생성된 마지막 ID 조회
        $sql = "SELECT customer_id FROM customers WHERE customer_id LIKE '{$prefix}%' ORDER BY customer_id DESC LIMIT 1";
        $result = mysqli_query($con, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $lastId = $row['customer_id'];
            $sequence = intval(substr($lastId, -4)) + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    // 고객 추가
    if ($action === 'add_customer') {
        $customerName = escapeInput($con, $_POST['customer_name'] ?? '');
        $vendorId = escapeInput($con, $_POST['vendor_id'] ?? '');
        $phone = escapeInput($con, $_POST['customer_phone'] ?? '');
        $email = escapeInput($con, $_POST['customer_email'] ?? '');
        $address = escapeInput($con, $_POST['customer_address'] ?? '');
        $contractStatus = escapeInput($con, $_POST['contract_status'] ?? 'ACTIVE');

        // 필수 필드 검증
        if (empty($customerName) || empty($vendorId) || empty($phone)) {
            $response['error']['msg'] = '필수 항목을 입력해주세요.';
            $response['error']['code'] = 400;
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 벤더 존재 확인
        $vendorCheck = mysqli_query($con, "SELECT vendor_id FROM vendors WHERE vendor_id = '{$vendorId}' AND is_active = 1 AND deleted_at IS NULL");
        if (!$vendorCheck || mysqli_num_rows($vendorCheck) === 0) {
            $response['error']['msg'] = '존재하지 않는 벤더입니다.';
            $response['error']['code'] = 404;
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Customer ID 생성
        $customerId = generateCustomerId($con);

        // Customer 생성
        $sql = "INSERT INTO customers (customer_id, vendor_id, company_name, email, phone, is_active, created_at, updated_at)
                VALUES ('{$customerId}', '{$vendorId}', '{$customerName}', '{$email}', '{$phone}', 1, NOW(), NOW())";

        if (mysqli_query($con, $sql)) {
            // 초기 구독 생성 (1년)
            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d', strtotime('+1 year'));

            $subSql = "INSERT INTO subscriptions (customer_id, status, start_date, end_date, created_at, updated_at)
                       VALUES ('{$customerId}', '{$contractStatus}', '{$startDate}', '{$endDate}', NOW(), NOW())";
            mysqli_query($con, $subSql);

            $response['result'] = true;
            $response['item'] = [
                'customer_id' => $customerId,
                'company_name' => $customerName,
                'vendor_id' => $vendorId,
                'phone' => $phone,
                'email' => $email
            ];
        } else {
            $response['error']['msg'] = '고객 등록 중 오류가 발생했습니다: ' . mysqli_error($con);
            $response['error']['code'] = 500;
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 고객 수정
    if ($action === 'update_customer') {
        $customerId = escapeInput($con, $_POST['customer_id'] ?? '');
        $customerName = escapeInput($con, $_POST['customer_name'] ?? '');
        $vendorId = escapeInput($con, $_POST['vendor_id'] ?? '');
        $phone = escapeInput($con, $_POST['customer_phone'] ?? '');
        $email = escapeInput($con, $_POST['customer_email'] ?? '');
        $address = escapeInput($con, $_POST['customer_address'] ?? '');
        $contractStatus = escapeInput($con, $_POST['contract_status'] ?? 'ACTIVE');

        // 필수 필드 검증
        if (empty($customerId) || empty($customerName) || empty($vendorId) || empty($phone)) {
            $response['error']['msg'] = '필수 항목을 입력해주세요.';
            $response['error']['code'] = 400;
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Customer 업데이트
        $sql = "UPDATE customers
                SET company_name = '{$customerName}',
                    vendor_id = '{$vendorId}',
                    phone = '{$phone}',
                    email = '{$email}',
                    updated_at = NOW()
                WHERE customer_id = '{$customerId}'";

        if (mysqli_query($con, $sql)) {
            // 구독 상태 업데이트
            $subSql = "UPDATE subscriptions SET status = '{$contractStatus}', updated_at = NOW() WHERE customer_id = '{$customerId}'";
            mysqli_query($con, $subSql);

            $response['result'] = true;
            $response['item'] = [
                'customer_id' => $customerId,
                'company_name' => $customerName
            ];
        } else {
            $response['error']['msg'] = '고객 수정 중 오류가 발생했습니다: ' . mysqli_error($con);
            $response['error']['code'] = 500;
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 일괄 벤더 변경
    if ($action === 'bulk_vendor_change') {
        $customerIds = $_POST['customer_ids'] ?? [];
        $newVendorId = escapeInput($con, $_POST['new_vendor_id'] ?? '');

        if (empty($customerIds) || empty($newVendorId)) {
            $response['error']['msg'] = '필수 항목을 입력해주세요.';
            $response['error']['code'] = 400;
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }

        $placeholders = implode(',', array_fill(0, count($customerIds), '?'));
        $stmt = mysqli_prepare($con, "UPDATE customers SET vendor_id = ?, updated_at = NOW() WHERE customer_id IN ($placeholders)");

        if ($stmt) {
            $types = str_repeat('s', count($customerIds) + 1);
            $params = array_merge([$newVendorId], $customerIds);
            mysqli_stmt_bind_param($stmt, $types, ...$params);

            if (mysqli_stmt_execute($stmt)) {
                $response['result'] = true;
                $response['item'] = ['affected_rows' => mysqli_stmt_affected_rows($stmt)];
            } else {
                $response['error']['msg'] = '벤더 변경 중 오류가 발생했습니다.';
                $response['error']['code'] = 500;
            }

            mysqli_stmt_close($stmt);
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 알 수 없는 액션
    $response['error']['msg'] = '지원하지 않는 요청입니다.';
    $response['error']['code'] = 400;
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// 필터 파라미터 (POST 방식으로 변경)
$filterStatus = isset($_POST[encryptValue('status')]) ? $_POST[encryptValue('status')] : (isset($_GET['status']) ? $_GET['status'] : '');
$searchKeyword = isset($_POST[encryptValue('search')]) ? $_POST[encryptValue('search')] : (isset($_GET['search']) ? $_GET['search'] : '');

// 고객 관리 데이터 조회
$sql = "
SELECT
    c.customer_id,
    c.company_name,
    c.vendor_id,
    c.phone,
    c.email,
    c.is_active,
    v.company_name as vendor_name,
    s.status as subscription_status,
    s.start_date as subscription_start_date,
    s.end_date as subscription_end_date,
    COUNT(DISTINCT sit.site_id) as site_count,
    COUNT(DISTINCT d.device_id) as device_count
FROM customers c
LEFT JOIN vendors v ON c.vendor_id = v.vendor_id AND v.deleted_at IS NULL
LEFT JOIN subscriptions s ON c.customer_id = s.customer_id AND s.deleted_at IS NULL
LEFT JOIN sites sit ON c.customer_id = sit.customer_id AND sit.is_active = 1 AND sit.deleted_at IS NULL
LEFT JOIN device_groups dg ON sit.site_id = dg.site_id AND dg.deleted_at IS NULL
LEFT JOIN devices d ON dg.group_id = d.group_id AND d.deleted_at IS NULL
WHERE c.is_active = 1 AND c.deleted_at IS NULL
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
$vendorSql = "SELECT vendor_id, company_name as name FROM vendors WHERE is_active = 1 AND deleted_at IS NULL ORDER BY company_name";
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
?>

<div class="wrap">
  <section id="sec-customer-mgmt" class="card section-card-first">
    <div class="card-hd card-hd-wrap">
      <div class="card-hd-content">
        <div class="card-hd-title-area">
          <div class="card-ttl">고객 관리</div>
          <div class="card-sub">고객정보·계약·기기·담당벤더 관리</div>
        </div>
        <div class="row filter-row">
          <select id="filterStatus" class="form-control input-w-150">
            <option value="">전체 상태</option>
            <option value="ACTIVE" <?php echo $filterStatus === 'ACTIVE' ? 'selected' : ''; ?>>ACTIVE</option>
            <option value="WARNING" <?php echo $filterStatus === 'WARNING' ? 'selected' : ''; ?>>WARNING</option>
            <option value="GRACE" <?php echo $filterStatus === 'GRACE' ? 'selected' : ''; ?>>GRACE</option>
            <option value="TERMINATED" <?php echo $filterStatus === 'TERMINATED' ? 'selected' : ''; ?>>TERMINATED</option>
          </select>
          <input type="text" id="searchCustomer" class="form-control input-w-200" placeholder="고객명 검색" value="<?php echo htmlspecialchars($searchKeyword); ?>">
          <button id="btnFilter" class="btn">조회</button>
          <button id="btnAddCustomer" class="btn primary">고객 추가</button>
          <button id="btnExportCsv" class="btn">CSV 내보내기</button>
        </div>
      </div>
    </div>
    <div class="card-bd">
      <div class="table-wrap">
        <table class="table" id="tblCustomerMgmt">
          <thead>
            <tr>
              <th><input type="checkbox" id="chkAll"></th>
              <th>고객ID</th>
              <th>고객명</th>
              <th>담당벤더</th>
              <th>사업장수</th>
              <th>기기수</th>
              <th>계약상태</th>
              <th>구독시작일</th>
              <th>만료예정일</th>
              <th>연락처</th>
              <th>관리</th>
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
              <td><input type="checkbox" value="<?php echo htmlspecialchars($customer['customer_id']); ?>"></td>
              <td><?php echo htmlspecialchars($customer['customer_id']); ?></td>
              <td><?php echo htmlspecialchars($customer['company_name']); ?></td>
              <td><?php echo htmlspecialchars($customer['vendor_id']); ?></td>
              <td><?php echo htmlspecialchars($customer['site_count']); ?></td>
              <td><?php echo htmlspecialchars($customer['device_count']); ?></td>
              <td>
                <span class="badge <?php echo getStatusBadgeClass($customer['subscription_status'] ?? 'TERMINATED'); ?>">
                  <?php echo htmlspecialchars($customer['subscription_status'] ?? 'NONE'); ?>
                </span>
              </td>
              <td><?php echo htmlspecialchars($customer['subscription_start_date'] ?? '-'); ?></td>
              <td><?php echo htmlspecialchars($customer['subscription_end_date'] ?? '-'); ?></td>
              <td><?php echo htmlspecialchars($customer['phone'] ?? '-'); ?></td>
              <td>
                <button class="btn-sm btn-edit"
                  data-customer-id="<?php echo htmlspecialchars($customer['customer_id']); ?>"
                  data-company-name="<?php echo htmlspecialchars($customer['company_name']); ?>"
                  data-vendor-id="<?php echo htmlspecialchars($customer['vendor_id']); ?>"
                  data-phone="<?php echo htmlspecialchars($customer['phone'] ?? ''); ?>"
                  data-email="<?php echo htmlspecialchars($customer['email'] ?? ''); ?>"
                  data-status="<?php echo htmlspecialchars($customer['subscription_status'] ?? 'ACTIVE'); ?>">수정</button>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <div class="row" style="margin-top:12px">
        <button id="btnBulkVendorChange" class="btn">선택 고객 벤더 변경</button>
        <button id="btnBulkStatusChange" class="btn">선택 고객 상태 변경</button>
      </div>
    </div>
  </section>
</div>

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
          <label>고객명 *</label>
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
          <input type="text" id="customerAddress" name="customer_address" class="form-control">
        </div>
        <div class="form-group">
          <label>계약상태</label>
          <select id="contractStatus" name="contract_status" class="form-control">
            <option value="ACTIVE">ACTIVE</option>
            <option value="WARNING">WARNING</option>
            <option value="GRACE">GRACE</option>
            <option value="TERMINATED">TERMINATED</option>
          </select>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn modal-close">취소</button>
      <button id="btnSaveCustomer" class="btn primary">저장</button>
    </div>
  </div>
</div>

<!-- 벤더 변경 모달 -->
<div id="modalVendorChange" class="modal" style="display:none">
  <div class="modal-content" style="max-width:500px">
    <div class="modal-header">
      <h3>담당벤더 변경</h3>
      <button class="modal-close">&times;</button>
    </div>
    <div class="modal-body">
      <p><strong id="selectedCount">0</strong>명의 고객 담당벤더를 변경합니다.</p>
      <div class="form-group">
        <label>변경할 벤더 *</label>
        <select id="newVendorId" class="form-control" required>
          <option value="">선택하세요</option>
          <?php foreach ($vendors as $vendor): ?>
          <option value="<?php echo htmlspecialchars($vendor['vendor_id']); ?>">
            <?php echo htmlspecialchars($vendor['name']); ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn modal-close">취소</button>
      <button id="btnConfirmVendorChange" class="btn primary">변경</button>
    </div>
  </div>
</div>

<script>
// 페이지 이름 (AJAX 호출용)
window.pageName = '<?= $pageName ?>';

(function() {
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
$(document).on('keypress', '#searchCustomer', function(e) {
  if (e.key === 'Enter') {
    $('#btnFilter').click();
  }
});

// 상태 변경 시 자동 조회
$(document).on('change', '#filterStatus', function() {
  $('#btnFilter').click();
});

// CSV 내보내기
$(document).on('click', '#btnExportCsv', function() {
  const table = document.getElementById('tblCustomerMgmt');
  const rows = Array.from(table.querySelectorAll('thead tr, tbody tr'));

  const csv = rows.map(row => {
    const cells = Array.from(row.querySelectorAll('th, td'));
    return cells.map(cell => {
      if (cell.querySelector('input[type="checkbox"]')) return '';
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

// 전체 선택
$(document).on('change', '#chkAll', function() {
  const checkboxes = document.querySelectorAll('#tblCustomerMgmt tbody input[type="checkbox"]');
  checkboxes.forEach(cb => cb.checked = this.checked);
});

// 고객 추가
$(document).on('click', '#btnAddCustomer', function() {
  document.getElementById('formTitle').textContent = '고객 추가';
  document.getElementById('customerForm').reset();
  document.getElementById('customerId').value = '';
  document.getElementById('modalCustomerForm').style.display = 'flex';
});

// 고객 수정
$(document).on('click', '.btn-edit', function() {
  document.getElementById('formTitle').textContent = '고객 수정';
  document.getElementById('customerId').value = this.getAttribute('data-customer-id');
  document.getElementById('customerName').value = this.getAttribute('data-company-name');
  document.getElementById('vendorId').value = this.getAttribute('data-vendor-id');
  document.getElementById('customerPhone').value = this.getAttribute('data-phone');
  document.getElementById('customerEmail').value = this.getAttribute('data-email');
  document.getElementById('contractStatus').value = this.getAttribute('data-status');

  document.getElementById('modalCustomerForm').style.display = 'flex';
});

// 고객 저장
$(document).on('click', '#btnSaveCustomer', function() {
  const form = document.getElementById('customerForm');
  if (!form.checkValidity()) {
    alert('필수 항목을 입력해주세요.');
    return;
  }

  const customerId = document.getElementById('customerId').value;
  const action = customerId ? 'update_customer' : 'add_customer';

  const formData = new FormData();
  formData.append('action', action);
  if (customerId) {
    formData.append('customer_id', customerId);
  }
  formData.append('customer_name', document.getElementById('customerName').value);
  formData.append('vendor_id', document.getElementById('vendorId').value);
  formData.append('customer_phone', document.getElementById('customerPhone').value);
  formData.append('customer_email', document.getElementById('customerEmail').value);
  formData.append('customer_address', document.getElementById('customerAddress').value);
  formData.append('contract_status', document.getElementById('contractStatus').value);

  // updateAjaxContent 사용
  const data = {};
  data['<?= encryptValue('action') ?>'] = action;
  if (customerId) data['<?= encryptValue('customer_id') ?>'] = customerId;
  data['<?= encryptValue('customer_name') ?>'] = document.getElementById('customerName').value;
  data['<?= encryptValue('vendor_id') ?>'] = document.getElementById('vendorId').value;
  data['<?= encryptValue('customer_phone') ?>'] = document.getElementById('customerPhone').value;
  data['<?= encryptValue('customer_email') ?>'] = document.getElementById('customerEmail').value;
  data['<?= encryptValue('customer_address') ?>'] = document.getElementById('customerAddress').value;
  data['<?= encryptValue('contract_status') ?>'] = document.getElementById('contractStatus').value;

  updateAjaxContent(data, function(response) {
    if (response.result) {
      alert(`고객 ${customerId ? '수정' : '등록'}이 완료되었습니다.`);
      document.getElementById('modalCustomerForm').style.display = 'none';
      location.reload();
    } else {
      alert(response.error?.msg || '오류가 발생했습니다.');
    }
  });
});

// 벤더 일괄 변경
$(document).on('click', '#btnBulkVendorChange', function() {
  const checked = document.querySelectorAll('#tblCustomerMgmt tbody input[type="checkbox"]:checked');
  if (checked.length === 0) {
    alert('변경할 고객을 선택해주세요.');
    return;
  }

  document.getElementById('selectedCount').textContent = checked.length;
  document.getElementById('modalVendorChange').style.display = 'flex';
});

// 벤더 변경 확인
$(document).on('click', '#btnConfirmVendorChange', function() {
  const newVendor = document.getElementById('newVendorId').value;
  if (!newVendor) {
    alert('변경할 벤더를 선택해주세요.');
    return;
  }

  const checked = document.querySelectorAll('#tblCustomerMgmt tbody input[type="checkbox"]:checked');
  const customerIds = Array.from(checked).map(cb => cb.value);

  const data = {};
  data['<?= encryptValue('action') ?>'] = 'bulk_vendor_change';
  data['<?= encryptValue('new_vendor_id') ?>'] = newVendor;
  customerIds.forEach((id, index) => {
    data['<?= encryptValue('customer_ids') ?>[' + index + ']'] = id;
  });

  updateAjaxContent(data, function(response) {
    if (response.result) {
      alert('벤더 변경이 완료되었습니다.');
      document.getElementById('modalVendorChange').style.display = 'none';
      location.reload();
    } else {
      alert(response.error?.msg || '오류가 발생했습니다.');
    }
  });
});

// 모달 닫기
$(document).on('click', '.modal-close', function() {
  $(this).closest('.modal').css('display', 'none');
});

// ESC 키로 모달 닫기
$(document).on('keydown', function(e) {
  if (e.key === 'Escape') {
    $('.modal').css('display', 'none');
  }
});

})(); // IIFE 종료
</script>

