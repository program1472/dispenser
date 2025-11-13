<?php
/**
 * HQ 고객관리 > 영업사원
 * 영업사원별 고객 현황
 */

// Ajax 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['action'])) {
    $action = $_POST['action'] ?? '';

    try {
        switch ($action) {
            case 'add_sales_rep':
                $name = trim($_POST['name'] ?? '');
                $vendorId = trim($_POST['vendor_id'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $password = trim($_POST['password'] ?? '');
                $phone = trim($_POST['phone'] ?? '');
                $notes = trim($_POST['notes'] ?? '');

                // 필수 항목 체크
                if (empty($name) || empty($vendorId) || empty($email) || empty($password)) {
                    $response['error'] = ['msg' => '필수 항목을 입력해주세요.', 'code' => 400];
                    Finish();
                }

                // 비밀번호 길이 체크
                if (strlen($password) < 8) {
                    $response['error'] = ['msg' => '비밀번호는 최소 8자 이상이어야 합니다.', 'code' => 400];
                    Finish();
                }

                // 이메일 중복 체크
                $emailEsc = escapeString($email);
                $checkSql = "SELECT `user_id` FROM `users` WHERE `email` = '{$emailEsc}' AND `is_active` = 1";
                $existing = query($checkSql);
                if (!empty($existing)) {
                    $response['error'] = ['msg' => '이미 사용 중인 이메일입니다.', 'code' => 400];
                    Finish();
                }

                // 비밀번호 해싱
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // 사용자 추가
                $nameEsc = escapeString($name);
                $vendorIdEsc = escapeString($vendorId);
                $hashedPasswordEsc = escapeString($hashedPassword);
                $phoneEsc = escapeString($phone);
                $notesEsc = escapeString($notes);

                $sql = "INSERT INTO `users` (`name`, `email`, `password`, `phone`, `vendor_id`, `role`, `notes`, `is_active`, `created_at`)
                        VALUES ('{$nameEsc}', '{$emailEsc}', '{$hashedPasswordEsc}', '{$phoneEsc}', '{$vendorIdEsc}', 'vendor', '{$notesEsc}', 1, NOW())";

                $result = query($sql);

                if ($result) {
                    $response['result'] = true;
                    $response['msg'] = '영업사원이 등록되었습니다.';
                } else {
                    $response['error'] = ['msg' => '영업사원 등록에 실패했습니다.', 'code' => 500];
                }
                Finish();
                break;

            default:
                $response['error'] = sendError(400, true);
                Finish();
        }
    } catch (Exception $e) {
        // 공통 함수로 에러 메시지 변환
        $errorMsg = getFriendlyErrorMessage($e->getMessage());

        $response['error'] = ['msg' => $errorMsg, 'code' => $e->getCode() ?: 500];
        Finish();
    }
}

// 필터 파라미터 (POST 방식으로 변경)
$filterSales = isset($_POST[encryptValue('sales')]) ? $_POST[encryptValue('sales')] : (isset($_GET['sales']) ? $_GET['sales'] : '');
$searchKeyword = isset($_POST[encryptValue('search')]) ? $_POST[encryptValue('search')] : (isset($_GET['search']) ? $_GET['search'] : '');

// 영업사원별 고객 통계 조회 (users 테이블 사용)
$sql = "
SELECT
    u.user_id,
    u.name as sales_name,
    u.email as sales_email,
    u.phone as sales_phone,
    v.vendor_id,
    v.company_name as vendor_name,
    COUNT(DISTINCT c.customer_id) as customer_count,
    COUNT(DISTINCT CASE WHEN s.status = 'ACTIVE' THEN c.customer_id END) as active_count,
    COUNT(DISTINCT sit.site_id) as site_count,
    COUNT(DISTINCT d.device_id) as device_count,
    COALESCE(SUM(CASE WHEN s.status = 'ACTIVE' THEN 50000 ELSE 0 END), 0) as monthly_revenue
FROM users u
LEFT JOIN vendors v ON u.vendor_id = v.vendor_id AND v.deleted_at IS NULL
LEFT JOIN customers c ON v.vendor_id = c.vendor_id AND c.is_active = 1 AND c.deleted_at IS NULL
LEFT JOIN subscriptions s ON c.customer_id = s.customer_id
LEFT JOIN sites sit ON c.customer_id = sit.customer_id AND sit.is_active = 1
LEFT JOIN device_groups dg ON sit.site_id = dg.site_id
LEFT JOIN devices d ON dg.group_id = d.group_id
WHERE u.is_active = 1
    AND u.vendor_id IS NOT NULL
";

if ($filterSales) {
    $sql .= " AND u.user_id = '" . mysqli_real_escape_string($con, $filterSales) . "'";
}

if ($searchKeyword) {
    $sql .= " AND u.name LIKE '%" . mysqli_real_escape_string($con, $searchKeyword) . "%'";
}

$sql .= " GROUP BY u.user_id, u.name, u.email, u.phone, v.vendor_id, v.company_name
          HAVING customer_count > 0
          ORDER BY customer_count DESC
          LIMIT 100";

$result = mysqli_query($con, $sql);

$salesReps = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $salesReps[] = $row;
    }
}

// 영업사원 목록 (필터용) - users 테이블에서 vendor_id가 있는 사용자
$salesListSql = "
    SELECT u.user_id, u.name, v.company_name as vendor_name
    FROM users u
    LEFT JOIN vendors v ON u.vendor_id = v.vendor_id AND v.deleted_at IS NULL
    WHERE u.is_active = 1 AND u.deleted_at IS NULL AND u.vendor_id IS NOT NULL
    ORDER BY u.name
";
$salesListResult = mysqli_query($con, $salesListSql);
$salesList = [];
if ($salesListResult) {
    while ($row = mysqli_fetch_assoc($salesListResult)) {
        $salesList[] = $row;
    }
}
?>
<section class="card">
  <div class="card-hd">
    <div style="display: flex; flex-direction: column; gap: 20px; flex: 1;">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="card-ttl">영업사원별 고객 현황</div>
        <div class="card-sub">영업사원별 고객 수 및 매출 현황</div>
      </div>
      <div class="row">
        <div class="form-group" style="margin-bottom: 0; display: flex; align-items: center; gap: 8px;">
          <label style="white-space: nowrap; margin-bottom: 0;">영업사원</label>
          <select id="filterSales" class="form-control" style="max-width:250px">
            <option value="">전체 영업사원</option>
            <?php foreach ($salesList as $s): ?>
            <option value="<?php echo htmlspecialchars($s['user_id']); ?>" <?php echo $filterSales === $s['user_id'] ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($s['name']); ?> (<?php echo htmlspecialchars($s['vendor_name'] ?? '-'); ?>)
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group" style="margin-bottom: 0; display: flex; align-items: center; gap: 8px;">
          <label style="white-space: nowrap; margin-bottom: 0;">검색</label>
          <input type="text" id="searchKeyword" class="form-control" placeholder="영업사원명 검색" style="max-width:200px" value="<?php echo htmlspecialchars($searchKeyword); ?>">
        </div>
        <button id="btnFilter" class="btn primary" style="align-self: flex-end;">조회</button>
        <button id="btnAddSales" class="btn primary" style="align-self: flex-end;">영업사원 추가</button>
      </div>
    </div>
    <div class="row">
      <button id="btnExportCsv" class="btn">CSV 내보내기</button>
    </div>
  </div>

  <div class="card-bd">
    <div class="table-wrap">
  <table class="table" id="tblSalesCustomers">
    <thead>
      <tr>
        <th>영업사원 ID</th>
        <th>영업사원명</th>
        <th>소속 벤더</th>
        <th>연락처</th>
        <th>총 고객 수</th>
        <th>활성 고객</th>
        <th>사이트 수</th>
        <th>기기 수</th>
        <th>월 매출</th>
        <th>상세</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($salesReps)): ?>
      <tr>
        <td colspan="10" style="text-align:center; padding:40px;">조회된 데이터가 없습니다.</td>
      </tr>
      <?php else: ?>
      <?php foreach ($salesReps as $sales): ?>
      <tr>
        <td><?php echo htmlspecialchars($sales['user_id']); ?></td>
        <td><strong><?php echo htmlspecialchars($sales['sales_name']); ?></strong></td>
        <td><?php echo htmlspecialchars($sales['vendor_name'] ?? '-'); ?></td>
        <td><?php echo htmlspecialchars($sales['sales_phone'] ?? '-'); ?></td>
        <td><?php echo number_format($sales['customer_count']); ?></td>
        <td><?php echo number_format($sales['active_count']); ?></td>
        <td><?php echo number_format($sales['site_count']); ?></td>
        <td><?php echo number_format($sales['device_count']); ?></td>
        <td>₩<?php echo number_format($sales['monthly_revenue']); ?></td>
        <td>
          <button class="btn-sm" onclick="viewSalesDetail('<?php echo htmlspecialchars($sales['user_id']); ?>')">보기</button>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
    </div>
  </div>
</section>

<!-- 영업사원 추가 모달 -->
<div id="salesModal" class="modal">
  <div class="modal-content" style="max-width: 600px;">
    <div class="modal-header">
      <h3>영업사원 추가</h3>
      <span class="modal-close" onclick="closeSalesModal()">&times;</span>
    </div>
    <div class="modal-body">
      <form id="frmSales">
        <div class="form-group">
          <label for="salesName">영업사원명 <span style="color: var(--warn);">*</span></label>
          <input type="text" id="salesName" name="name" class="form-control" required>
        </div>

        <div class="form-group">
          <label for="salesVendorId">소속 벤더 <span style="color: var(--warn);">*</span></label>
          <select id="salesVendorId" name="vendor_id" class="form-control" required>
            <option value="">벤더 선택</option>
            <?php
            $vendorSql = "SELECT vendor_id, name FROM vendors WHERE is_active = 1 ORDER BY name";
            $vendorResult = mysqli_query($con, $vendorSql);
            while ($v = mysqli_fetch_assoc($vendorResult)) {
              echo '<option value="' . htmlspecialchars($v['vendor_id']) . '">' . htmlspecialchars($v['name']) . '</option>';
            }
            ?>
          </select>
        </div>

        <div class="form-group">
          <label for="salesEmail">이메일 (로그인 ID) <span style="color: var(--warn);">*</span></label>
          <input type="email" id="salesEmail" name="email" class="form-control" required>
        </div>

        <div class="form-group">
          <label for="salesPassword">비밀번호 <span style="color: var(--warn);">*</span></label>
          <input type="password" id="salesPassword" name="password" class="form-control" required minlength="8">
          <small style="color: var(--muted);">최소 8자 이상</small>
        </div>

        <div class="form-group">
          <label for="salesPhone">연락처</label>
          <input type="tel" id="salesPhone" name="phone" class="form-control" placeholder="010-0000-0000">
        </div>

        <div class="form-group">
          <label for="salesNotes">비고</label>
          <textarea id="salesNotes" name="notes" class="form-control" rows="3"></textarea>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn" onclick="closeSalesModal()">취소</button>
      <button type="button" class="btn primary" onclick="saveSales()">저장</button>
    </div>
  </div>
</div>

<script>
// 필터 조회 - .off().on() 패턴 사용
$('#btnFilter').off('click').on('click', function() {
  const sales = document.getElementById('filterSales').value;
  const search = document.getElementById('searchKeyword').value;

  // 암호화된 POST 데이터 생성
  const data = {};
  if (sales) data['<?= encryptValue('sales') ?>'] = sales;
  if (search) data['<?= encryptValue('search') ?>'] = search;

  // updateAjaxContent로 페이지 다시 로드
  updateAjaxContent(data, function(response) {
    if (response.result === 'ok' && response.html) {
      const contentArea = document.querySelector('#sec-customer-sales').parentElement;
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

// 영업사원 추가 버튼 - .off().on() 패턴 사용
$('#btnAddSales').off('click').on('click', function() {
  openSalesModal();
});

// CSV 내보내기 - .off().on() 패턴 사용
$('#btnExportCsv').off('click').on('click', function() {
  const table = document.getElementById('tblSalesCustomers');
  const rows = Array.from(table.querySelectorAll('thead tr, tbody tr'));

  const csv = rows.map(row => {
    const cells = Array.from(row.querySelectorAll('th, td'));
    return cells.slice(0, -1).map(cell => {
      return '"' + cell.textContent.trim().replace(/"/g, '""') + '"';
    }).join(',');
  }).join('\n');

  const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = 'HQ_영업사원별고객_' + new Date().toISOString().slice(0,10) + '.csv';
  link.click();
});

// 모달 열기
function openSalesModal() {
  const modal = document.getElementById('salesModal');
  document.getElementById('frmSales').reset();
  modal.classList.add('show');
}

// 모달 닫기
function closeSalesModal() {
  document.getElementById('salesModal').classList.remove('show');
}

// 영업사원 저장
function saveSales() {
  const form = document.getElementById('frmSales');
  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  const data = {
    action: 'add_sales_rep',
    name: document.getElementById('salesName').value,
    vendor_id: document.getElementById('salesVendorId').value,
    email: document.getElementById('salesEmail').value,
    password: document.getElementById('salesPassword').value,
    phone: document.getElementById('salesPhone').value,
    notes: document.getElementById('salesNotes').value
  };

  updateAjaxContent(data, function(response) {
    if (response.result) {
      toast(response.msg || '영업사원이 등록되었습니다.');
      closeSalesModal();
      // 페이지 리로드
      location.reload();
    } else {
      alert(response.error?.msg || '저장에 실패했습니다.');
    }
  }, false);
}

// Toast 알림 함수
function toast(msg) {
  const div = document.createElement('div');
  div.className = 'toast';
  div.textContent = msg;
  div.style.cssText = 'position:fixed; top:20px; right:20px; background:var(--accent); color:#fff; padding:12px 16px; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.15); z-index:10000;';
  document.body.appendChild(div);
  setTimeout(() => div.remove(), 1800);
}

function viewSalesDetail(salesRepId) {
  alert('영업사원 상세 페이지 (개발 예정): ' + salesRepId);
}

// 모달 외부 클릭 시 닫기
window.onclick = function(event) {
  const modal = document.getElementById('salesModal');
  if (event.target === modal) {
    closeSalesModal();
  }
};
</script>

