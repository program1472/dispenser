<?php
/**
 * HQ 영업사원 관리
 * 영업사원 정보 및 커미션 계좌 관리 (새 DB 구조: users with role SALES_REP)
 */

// AJAX 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['action']) || isset($_POST['p']))) {
    header('Content-Type: application/json; charset=utf-8');

    $action = $_POST['action'] ?? 'filter_sales_reps';

    switch ($action) {
        case 'get_sales_rep':
            $userId = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

            if (!$userId) {
                $response['result'] = false;
                $response['error'] = ['msg' => '사용자 ID가 필요합니다.', 'code' => 400];
                Finish();
            }

            // JOIN roles table to verify SALES_REP role
            $sql = "SELECT u.*, r.role_name,
                           GROUP_CONCAT(DISTINCT aa.vendor_id) as vendor_ids,
                           GROUP_CONCAT(DISTINCT v.company_name SEPARATOR ', ') as vendor_name
                    FROM users u
                    LEFT JOIN roles r ON u.role_id = r.role_id
                    LEFT JOIN account_assignments aa ON u.user_id = aa.sales_user_id AND aa.is_active = 1
                    LEFT JOIN vendors v ON aa.vendor_id = v.vendor_id AND v.deleted_at IS NULL
                    WHERE u.user_id = {$userId} AND u.deleted_at IS NULL
                    GROUP BY u.user_id";
            $response['item']['sql'] = $sql;
            $result = mysqli_query($con, $sql);

            if ($result && $row = mysqli_fetch_assoc($result)) {
                $response['result'] = true;
                $response['item'] = $row;
            } else {
                $response['result'] = false;
                $response['error'] = ['msg' => '영업사원을 찾을 수 없습니다.', 'code' => 404];
            }
            Finish();

        case 'add_sales_rep':
            // Get form data
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? trim($_POST['password']) : '';
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
            $vendorId = isset($_POST['vendor_id']) ? intval($_POST['vendor_id']) : 0;

            $department = isset($_POST['department']) ? trim($_POST['department']) : '';
            $position = isset($_POST['position']) ? trim($_POST['position']) : '';
            $address = isset($_POST['address']) ? trim($_POST['address']) : '';
            $bankName = isset($_POST['bank_name']) ? trim($_POST['bank_name']) : '';
            $bankAccountNumber = isset($_POST['bank_account_number']) ? trim($_POST['bank_account_number']) : '';
            $bankAccountHolder = isset($_POST['bank_account_holder']) ? trim($_POST['bank_account_holder']) : '';
            $taxIdNumber = isset($_POST['tax_id_number']) ? trim($_POST['tax_id_number']) : '';
            $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';

            // Validate required fields
            if (empty($email)) {
                $response['result'] = false;
                $response['error'] = ['msg' => '이메일은 필수 항목입니다.', 'code' => 400];
                Finish();
            }

            if (empty($password)) {
                $response['result'] = false;
                $response['error'] = ['msg' => '비밀번호는 필수 항목입니다.', 'code' => 400];
                Finish();
            }

            if (empty($name)) {
                $response['result'] = false;
                $response['error'] = ['msg' => '이름은 필수 항목입니다.', 'code' => 400];
                Finish();
            }

            // Check email duplicate
            $emailEsc = mysqli_real_escape_string($con, $email);
            $checkSql = "SELECT user_id FROM users WHERE email = '{$emailEsc}' AND deleted_at IS NULL";
            $response['item']['checkSql'] = $checkSql;
            $checkResult = mysqli_query($con, $checkSql);
            if ($checkResult && mysqli_num_rows($checkResult) > 0) {
                $response['result'] = false;
                $response['error'] = ['msg' => '이미 사용 중인 이메일입니다.', 'code' => 400];
                Finish();
            }

            // Get SALES_REP role_id
            $roleSql = "SELECT role_id FROM roles WHERE role_name = 'SALES_REP' LIMIT 1";
            $response['item']['roleSql'] = $roleSql;
            $roleResult = mysqli_query($con, $roleSql);
            if (!$roleResult || mysqli_num_rows($roleResult) === 0) {
                $response['result'] = false;
                $response['error'] = ['msg' => 'SALES_REP 역할을 찾을 수 없습니다.', 'code' => 500];
                Finish();
            }
            $roleRow = mysqli_fetch_assoc($roleResult);
            $roleId = (int)$roleRow['role_id'];

            // Start transaction
            mysqli_begin_transaction($con);

            try {
                // 1. Create user
                $passwordHash = password_hash($password, PASSWORD_BCRYPT);
                $nameEsc = mysqli_real_escape_string($con, $name);
                $phoneEsc = mysqli_real_escape_string($con, $phone);
                $departmentEsc = mysqli_real_escape_string($con, $department);
                $positionEsc = mysqli_real_escape_string($con, $position);
                $addressEsc = mysqli_real_escape_string($con, $address);
                $bankNameEsc = mysqli_real_escape_string($con, $bankName);
                $bankAccountNumberEsc = mysqli_real_escape_string($con, $bankAccountNumber);
                $bankAccountHolderEsc = mysqli_real_escape_string($con, $bankAccountHolder);
                $taxIdNumberEsc = mysqli_real_escape_string($con, $taxIdNumber);
                $notesEsc = mysqli_real_escape_string($con, $notes);

                $userSql = "INSERT INTO users (
                                email, password_hash, name, phone, role_id,
                                department, position, address,
                                bank_name, bank_account_number, bank_account_holder, tax_id_number,
                                notes, is_active, created_at
                            ) VALUES (
                                '{$emailEsc}',
                                '{$passwordHash}',
                                '{$nameEsc}',
                                " . ($phone ? "'{$phoneEsc}'" : "NULL") . ",
                                {$roleId},
                                " . ($department ? "'{$departmentEsc}'" : "NULL") . ",
                                " . ($position ? "'{$positionEsc}'" : "NULL") . ",
                                " . ($address ? "'{$addressEsc}'" : "NULL") . ",
                                " . ($bankName ? "'{$bankNameEsc}'" : "NULL") . ",
                                " . ($bankAccountNumber ? "'{$bankAccountNumberEsc}'" : "NULL") . ",
                                " . ($bankAccountHolder ? "'{$bankAccountHolderEsc}'" : "NULL") . ",
                                " . ($taxIdNumber ? "'{$taxIdNumberEsc}'" : "NULL") . ",
                                " . ($notes ? "'{$notesEsc}'" : "NULL") . ",
                                1, NOW()
                            )";
                $response['item']['userSql'] = $userSql;

                if (!mysqli_query($con, $userSql)) {
                    throw new Exception('사용자 생성 실패: ' . mysqli_error($con));
                }

                $userId = mysqli_insert_id($con);

                // 2. Create account assignment if vendor specified (customer_id NULL for sales rep-vendor assignment)
                if ($vendorId > 0) {
                    $assignSql = "INSERT INTO account_assignments (customer_id, vendor_id, sales_user_id, assigned_date, is_active, created_at)
                                  VALUES (NULL, {$vendorId}, {$userId}, NOW(), 1, NOW())";
                    $response['item']['assignSql'] = $assignSql;

                    if (!mysqli_query($con, $assignSql)) {
                        throw new Exception('벤더 배정 실패: ' . mysqli_error($con));
                    }
                }

                // Commit transaction
                mysqli_commit($con);

                $response['result'] = true;
                $response['msg'] = '영업사원이 등록되었습니다.';
                $response['item'] = ['user_id' => $userId];

            } catch (Exception $e) {
                mysqli_rollback($con);
                $response['result'] = false;

                // 공통 함수로 에러 메시지 변환
                $errorMsg = getFriendlyErrorMessage($e->getMessage());

                $response['error'] = ['msg' => $errorMsg, 'code' => 500];
            }

            Finish();

        case 'update_sales_rep':
            $userId = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? trim($_POST['password']) : '';
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
            $vendorId = isset($_POST['vendor_id']) ? intval($_POST['vendor_id']) : 0;
            $isActive = isset($_POST['is_active']) ? intval($_POST['is_active']) : 0;

            $department = isset($_POST['department']) ? trim($_POST['department']) : '';
            $position = isset($_POST['position']) ? trim($_POST['position']) : '';
            $address = isset($_POST['address']) ? trim($_POST['address']) : '';
            $bankName = isset($_POST['bank_name']) ? trim($_POST['bank_name']) : '';
            $bankAccountNumber = isset($_POST['bank_account_number']) ? trim($_POST['bank_account_number']) : '';
            $bankAccountHolder = isset($_POST['bank_account_holder']) ? trim($_POST['bank_account_holder']) : '';
            $taxIdNumber = isset($_POST['tax_id_number']) ? trim($_POST['tax_id_number']) : '';
            $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';

            // Validate required fields
            if (!$userId || empty($email) || empty($name)) {
                $response['result'] = false;
                $response['error'] = ['msg' => '필수 항목을 입력해주세요.', 'code' => 400];
                Finish();
            }

            // Check user exists
            $checkSql = "SELECT user_id FROM users WHERE user_id = {$userId} AND deleted_at IS NULL";
            $response['item']['checkSql'] = $checkSql;
            $checkResult = mysqli_query($con, $checkSql);
            if (!$checkResult || mysqli_num_rows($checkResult) === 0) {
                $response['result'] = false;
                $response['error'] = ['msg' => '영업사원을 찾을 수 없습니다.', 'code' => 404];
                Finish();
            }

            // Check email duplicate (except current user)
            $emailEsc = mysqli_real_escape_string($con, $email);
            $checkEmailSql = "SELECT user_id FROM users WHERE email = '{$emailEsc}' AND user_id != {$userId} AND deleted_at IS NULL";
            $response['item']['checkEmailSql'] = $checkEmailSql;
            $checkEmailResult = mysqli_query($con, $checkEmailSql);
            if ($checkEmailResult && mysqli_num_rows($checkEmailResult) > 0) {
                $response['result'] = false;
                $response['error'] = ['msg' => '이미 사용 중인 이메일입니다.', 'code' => 400];
                Finish();
            }

            // Start transaction
            mysqli_begin_transaction($con);

            try {
                // 1. Update users table
                $nameEsc = mysqli_real_escape_string($con, $name);
                $phoneEsc = mysqli_real_escape_string($con, $phone);
                $departmentEsc = mysqli_real_escape_string($con, $department);
                $positionEsc = mysqli_real_escape_string($con, $position);
                $addressEsc = mysqli_real_escape_string($con, $address);
                $bankNameEsc = mysqli_real_escape_string($con, $bankName);
                $bankAccountNumberEsc = mysqli_real_escape_string($con, $bankAccountNumber);
                $bankAccountHolderEsc = mysqli_real_escape_string($con, $bankAccountHolder);
                $taxIdNumberEsc = mysqli_real_escape_string($con, $taxIdNumber);
                $notesEsc = mysqli_real_escape_string($con, $notes);

                $userSql = "UPDATE users SET
                            email = '{$emailEsc}',
                            name = '{$nameEsc}',
                            phone = " . ($phone ? "'{$phoneEsc}'" : "NULL") . ",
                            department = " . ($department ? "'{$departmentEsc}'" : "NULL") . ",
                            position = " . ($position ? "'{$positionEsc}'" : "NULL") . ",
                            address = " . ($address ? "'{$addressEsc}'" : "NULL") . ",
                            bank_name = " . ($bankName ? "'{$bankNameEsc}'" : "NULL") . ",
                            bank_account_number = " . ($bankAccountNumber ? "'{$bankAccountNumberEsc}'" : "NULL") . ",
                            bank_account_holder = " . ($bankAccountHolder ? "'{$bankAccountHolderEsc}'" : "NULL") . ",
                            tax_id_number = " . ($taxIdNumber ? "'{$taxIdNumberEsc}'" : "NULL") . ",
                            notes = " . ($notes ? "'{$notesEsc}'" : "NULL") . ",
                            is_active = {$isActive},
                            updated_at = NOW()";

                // Update password if provided
                if ($password) {
                    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
                    $userSql .= ", password_hash = '{$passwordHash}'";
                }

                $userSql .= " WHERE user_id = {$userId}";

                if (!mysqli_query($con, $userSql)) {
                    throw new Exception('사용자 정보 수정 실패: ' . mysqli_error($con));
                }

                // 2. Update account assignment for sales rep-vendor relationship (customer_id NULL)
                // First, deactivate all existing sales rep-vendor assignments (where customer_id IS NULL)
                $deactivateSql = "UPDATE account_assignments
                                  SET is_active = 0, updated_at = NOW()
                                  WHERE sales_user_id = {$userId} AND customer_id IS NULL";
                $response['item']['deactivateSql'] = $deactivateSql;
                mysqli_query($con, $deactivateSql);

                // Then create or activate assignment for the specified vendor
                if ($vendorId > 0) {
                    $checkAssignSql = "SELECT assignment_id FROM account_assignments
                                       WHERE vendor_id = {$vendorId} AND sales_user_id = {$userId} AND customer_id IS NULL";
                    $response['item']['checkAssignSql'] = $checkAssignSql;
                    $checkAssignResult = mysqli_query($con, $checkAssignSql);

                    if ($checkAssignResult && mysqli_num_rows($checkAssignResult) > 0) {
                        // Update existing assignment
                        $assignRow = mysqli_fetch_assoc($checkAssignResult);
                        $assignmentId = $assignRow['assignment_id'];
                        $updateAssignSql = "UPDATE account_assignments
                                            SET is_active = 1, updated_at = NOW()
                                            WHERE assignment_id = {$assignmentId}";
                        $response['item']['updateAssignSql'] = $updateAssignSql;
                        mysqli_query($con, $updateAssignSql);
                    } else {
                        // Create new assignment
                        $insertAssignSql = "INSERT INTO account_assignments (customer_id, vendor_id, sales_user_id, assigned_date, is_active, created_at)
                                            VALUES (NULL, {$vendorId}, {$userId}, NOW(), 1, NOW())";
                        $response['item']['insertAssignSql'] = $insertAssignSql;
                        mysqli_query($con, $insertAssignSql);
                    }
                }

                // Commit transaction
                mysqli_commit($con);

                $response['result'] = true;
                $response['msg'] = '영업사원 정보가 수정되었습니다.';

            } catch (Exception $e) {
                mysqli_rollback($con);
                $response['result'] = false;

                // 공통 함수로 에러 메시지 변환
                $errorMsg = getFriendlyErrorMessage($e->getMessage());

                $response['error'] = ['msg' => $errorMsg, 'code' => 500];
            }

            Finish();

        case 'delete_sales_rep':
            $userId = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

            if (!$userId) {
                $response['result'] = false;
                $response['error'] = ['msg' => '사용자 ID가 필요합니다.', 'code' => 400];
                Finish();
            }

            // Soft delete - set deleted_at timestamp
            $sql = "UPDATE users SET deleted_at = NOW(), updated_at = NOW() WHERE user_id = {$userId}";
            $response['item']['sql'] = $sql;

            if (mysqli_query($con, $sql)) {
                $response['result'] = true;
                $response['msg'] = '영업사원이 삭제되었습니다.';
            } else {
                $response['result'] = false;
                $response['error'] = ['msg' => '삭제에 실패했습니다: ' . mysqli_error($con), 'code' => 500];
            }
            Finish();

        case 'filter_sales_reps':
            // 필터 파라미터 (_ajax_.php에서 이미 복호화됨)
            $searchKeyword = isset($_POST['search']) ? trim($_POST['search']) : '';
            $filterVendor = isset($_POST['vendor']) ? intval($_POST['vendor']) : 0;

            // 검색 키워드 공백 분리 처리
            $searchKeywords = [];
            if ($searchKeyword) {
                $searchKeywords = array_filter(array_map('trim', explode(' ', $searchKeyword)));
            }

            // WHERE 조건 구성
            $searchString = "u.deleted_at IS NULL AND r.role_name = 'SALES_REP'";

            // 검색 키워드 처리 (공백 분리 AND 검색)
            if (!empty($searchKeywords)) {
                foreach ($searchKeywords as $keyword) {
                    $keywordEsc = mysqli_real_escape_string($con, $keyword);
                    $searchString .= " AND (CONVERT(u.name USING utf8mb4) LIKE _utf8mb4'%{$keywordEsc}%' COLLATE utf8mb4_unicode_ci
                               OR CONVERT(u.email USING utf8mb4) LIKE _utf8mb4'%{$keywordEsc}%' COLLATE utf8mb4_unicode_ci
                               OR CONVERT(u.phone USING utf8mb4) LIKE _utf8mb4'%{$keywordEsc}%' COLLATE utf8mb4_unicode_ci)";
                }
            }

            // Build SELECT query
            $sql = "SELECT
                        u.user_id, u.email, u.name, u.phone,
                        u.department, u.position, u.is_active,
                        GROUP_CONCAT(DISTINCT v.company_name SEPARATOR ', ') as vendor_name,
                        u.bank_name, u.bank_account_number,
                        u.created_at
                    FROM users u
                    LEFT JOIN roles r ON u.role_id = r.role_id
                    LEFT JOIN account_assignments aa ON u.user_id = aa.sales_user_id AND aa.is_active = 1
                    LEFT JOIN vendors v ON aa.vendor_id = v.vendor_id AND v.deleted_at IS NULL
                    WHERE {$searchString}
                    GROUP BY u.user_id";

            if ($filterVendor > 0) {
                $sql .= " HAVING FIND_IN_SET({$filterVendor}, GROUP_CONCAT(DISTINCT aa.vendor_id))";
            }

            // Pagination config (for vendors, we use a simpler count without GROUP BY complexity)
            $countSearchString = $searchString;
            if ($filterVendor > 0) {
                $countSearchString .= " AND EXISTS (SELECT 1 FROM account_assignments aa2 WHERE aa2.sales_user_id = u.user_id AND aa2.vendor_id = {$filterVendor} AND aa2.is_active = 1)";
            }

            $paginationConfig = [
                'table' => 'users u',
                'where' => $countSearchString,
                'join' => 'LEFT JOIN roles r ON u.role_id = r.role_id',
                'orderBy' => 'u.created_at DESC',
                'rowsPerPage' => $defaultRowsPage,
                'targetId' => '#salesRepTableBody',
                'atValue' => encryptValue('10')
            ];

            $rowsPage = $paginationConfig['rowsPerPage'];
            $p = $_POST['p'] ?? 1;
            $curPage = $rowsPage * ($p - 1);

            $sql .= " ORDER BY u.created_at DESC LIMIT {$curPage}, {$rowsPage}";

            // Debugging SQL
            $response['data']['search']['sql'] = $sql;

            $result = mysqli_query($con, $sql);

            // Generate pagination HTML
            require INC_ROOT . '/common_pagination.php';
            $response['pagination'] = $pagination ?? '';

            $html = '';
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $html .= '<tr>';
                    $html .= '<td><strong>' . htmlspecialchars($row['name']) . '</strong></td>';
                    $html .= '<td>' . htmlspecialchars($row['email'] ?? '-') . '</td>';
                    $html .= '<td>' . htmlspecialchars($row['vendor_name'] ?? '-') . '</td>';
                    $html .= '<td>' . htmlspecialchars($row['department'] ?? '-') . '</td>';
                    $html .= '<td>' . htmlspecialchars($row['position'] ?? '-') . '</td>';
                    $html .= '<td>' . htmlspecialchars($row['phone'] ?? '-') . '</td>';
                    $html .= '<td>' . htmlspecialchars($row['bank_name'] ?? '-') . '</td>';
                    $html .= '<td>' . htmlspecialchars($row['bank_account_number'] ?? '-') . '</td>';
                    $html .= '<td>';
                    if ($row['is_active']) {
                        $html .= '<span class="badge badge-success">활성</span>';
                    } else {
                        $html .= '<span class="badge badge-secondary">비활성</span>';
                    }
                    $html .= '</td>';
                    $html .= '<td>' . date('Y-m-d', strtotime($row['created_at'])) . '</td>';
                    $html .= '<td>';
                    $html .= '<button class="btn-sm btn-edit" onclick="editSalesRep(' . $row['user_id'] . ')">수정</button> ';
                    $html .= '<button class="btn-sm btn-delete" onclick="deleteSalesRep(' . $row['user_id'] . ', \'' . htmlspecialchars($row['name'], ENT_QUOTES) . '\')">삭제</button>';
                    $html .= '</td>';
                    $html .= '</tr>';
                }
            } else {
                $html = '<tr><td colspan="11" class="table-empty-state">조회된 데이터가 없습니다.</td></tr>';
            }

            $response['result'] = true;
            $response['html'] = $html;
            Finish();

        default:
            $response['result'] = false;
            $response['error'] = ['msg' => 'Invalid action', 'code' => 400];
            Finish();
    }
}

// 필터 파라미터 (GET 또는 POST에서 받음)
$searchKeyword = isset($_POST['search']) ? trim($_POST['search']) : (isset($_GET['search']) ? trim($_GET['search']) : '');
$filterVendor = isset($_POST['vendor']) ? intval($_POST['vendor']) : 0;

// 검색 키워드 공백 분리 처리
$searchKeywords = [];
if ($searchKeyword) {
    $searchKeywords = array_filter(array_map('trim', explode(' ', $searchKeyword)));
}


// 벤더 목록 (필터용)
$vendorListSql = "SELECT vendor_id, company_name FROM vendors WHERE deleted_at IS NULL ORDER BY company_name";
$response['item']['vendorListSql'] = $vendorListSql;
$vendorListResult = mysqli_query($con, $vendorListSql);
$vendorList = [];
while ($row = mysqli_fetch_assoc($vendorListResult)) {
    $vendorList[] = $row;
}
?>


<section class="card">
    <!-- 필터 -->
    <div class="card-hd card-hd-wrap">
      <div class="card-hd-content">
        <div class="card-hd-title-area">
          <div class="card-ttl">영업사원 관리</div>
          <div class="card-sub">영업사원 정보 및 커미션 계좌 관리</div>
        </div>
        <div class="filter-toolbar">
          <div class="filter-group">
            <label>검색</label>
            <input type="text" id="searchKeyword" name="search" class="form-control filter-input" placeholder="이름/이메일/전화번호"
                   value="<?php echo htmlspecialchars($searchKeyword); ?>" onkeypress="if(event.key==='Enter') filterSalesReps()">
          </div>
          <div class="filter-group">
            <label>벤더</label>
            <select id="filterVendor" name="vendor" class="form-control filter-select">
              <option value="">전체 벤더</option>
              <?php foreach ($vendorList as $v): ?>
              <option value="<?php echo $v['vendor_id']; ?>" <?php echo $filterVendor === $v['vendor_id'] ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($v['company_name']); ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
          <button id="btnFilter" class="btn primary" onclick="filterSalesReps()">조회</button>
          <button id="btnResetFilter" class="btn" onclick="resetFilter()">초기화</button>
          <button id="btnAddSalesRep" class="btn primary" onclick="openAddSalesRepModal()">영업사원 추가</button>
        </div>
      </div>
      <div class="row">
        <button id="btnExportCsv" class="btn" onclick="exportSalesRepsToCsv()">CSV 내보내기</button>
      </div>
    </div>

    <div class="card-bd card-bd-padding">
      <div class="table-wrap">
        <table class="tbl-list" id="tblSalesReps">
          <thead>
            <tr>
              <th>이름</th>
              <th>이메일</th>
              <th>소속 벤더</th>
              <th>부서</th>
              <th>직급</th>
              <th>전화번호</th>
              <th>은행</th>
              <th>계좌번호</th>
              <th>상태</th>
              <th>등록일</th>
              <th>관리</th>
            </tr>
          </thead>
          <tbody id="salesRepTableBody">
          </tbody>
        </table>
      </div>

      <!-- 페이징 영역 -->
      <div class="paging" data-id="#salesRepTableBody"></div>
    </div>
</section>

<!-- 영업사원 추가/수정 모달 -->
<div id="salesRepModal" class="modal">
  <div class="modal-content modal-content-lg">
    <div class="modal-header">
      <h3 id="modalTitle">영업사원 추가</h3>
      <button class="modal-close" onclick="closeSalesRepModal()">&times;</button>
    </div>
    <div class="modal-body">
      <form id="frmSalesRep">
        <input type="hidden" id="userId" name="user_id">
        <input type="hidden" id="modalMode" value="add">

        <div class="grid-2">
          <!-- 왼쪽 컬럼: 기본 정보 -->
          <div>
            <h4 class="section-header">기본 정보</h4>

            <div class="form-group">
              <label class="required">이메일 (로그인 ID)</label>
              <input type="email" id="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
              <label class="required">비밀번호</label>
              <input type="password" id="password" name="password" class="form-control" required>
              <small class="text-muted" id="passwordHint">수정 시 변경할 경우만 입력</small>
            </div>

            <div class="form-group">
              <label class="required">이름</label>
              <input type="text" id="name" name="name" class="form-control" required>
            </div>

            <div class="form-group">
              <label>전화번호</label>
              <input type="tel" id="phone" name="phone" class="form-control" placeholder="010-0000-0000">
            </div>

            <div class="form-group">
              <label>소속 벤더</label>
              <select id="vendorId" name="vendor_id" class="form-control">
                <option value="">벤더 선택</option>
                <?php foreach ($vendorList as $v): ?>
                <option value="<?php echo $v['vendor_id']; ?>">
                  <?php echo htmlspecialchars($v['company_name']); ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label>부서</label>
              <input type="text" id="department" name="department" class="form-control" placeholder="예: 영업팀">
            </div>

            <div class="form-group">
              <label>직급</label>
              <input type="text" id="position" name="position" class="form-control" placeholder="예: 대리">
            </div>

            <div class="form-group">
              <label>주소</label>
              <textarea id="address" name="address" class="form-control" rows="2"></textarea>
            </div>

            <div class="form-group">
              <label>
                <input type="checkbox" id="isActive" name="is_active" checked>
                활성 상태
              </label>
              <small class="text-muted">비활성 시 로그인 불가</small>
            </div>
          </div>

          <!-- 오른쪽 컬럼: 커미션 정보 -->
          <div>
            <h4 class="section-header">커미션 정보</h4>

            <div class="form-group">
              <label>은행명</label>
              <input type="text" id="bankName" name="bank_name" class="form-control" placeholder="예: 신한은행">
            </div>

            <div class="form-group">
              <label>계좌번호</label>
              <input type="text" id="bankAccountNumber" name="bank_account_number" class="form-control" placeholder="숫자만 입력">
            </div>

            <div class="form-group">
              <label>예금주</label>
              <input type="text" id="bankAccountHolder" name="bank_account_holder" class="form-control">
            </div>

            <div class="form-group">
              <label>주민등록번호/사업자번호</label>
              <input type="text" id="taxIdNumber" name="tax_id_number" class="form-control" placeholder="국세청 신고용">
              <small class="text-muted">커미션 국세청 신고에 사용됩니다</small>
            </div>

            <div class="form-group">
              <label>비고</label>
              <textarea id="notes" name="notes" class="form-control" rows="8" placeholder="기타 특이사항이나 메모"></textarea>
            </div>
          </div>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn" onclick="closeSalesRepModal()">취소</button>
      <button type="button" class="btn primary" onclick="saveSalesRep()">저장</button>
    </div>
  </div>
</div>



<script>
// 페이지 이름 (AJAX 호출용)
window.pageName = '<?= $pageName ?>';

// 필터 조회
window.filterSalesReps = function() {
  const vendor = document.getElementById('filterVendor').value || '';
  const search = document.getElementById('searchKeyword').value || '';

  const data = {};
  data['<?= encryptValue('action') ?>'] = 'filter_sales_reps';
  data['<?= encryptValue('vendor') ?>'] = vendor;
  data['<?= encryptValue('search') ?>'] = search;

  updateAjaxContent(data, function(response) {
    if (response.result && response.html) {
      const tbody = document.querySelector('#salesRepTableBody');
      if (tbody) {
        tbody.innerHTML = response.html;
      }

      // Update pagination
      if (response.pagination) {
        const pagingContainer = document.querySelector('.paging[data-id="#salesRepTableBody"]');
        if (pagingContainer) {
          pagingContainer.innerHTML = response.pagination;
        }
      }
    } else {
      alert(response.error?.msg || '조회에 실패했습니다.');
    }
  });
};

// 필터 초기화
window.resetFilter = function() {
  document.getElementById('searchKeyword').value = '';
  document.getElementById('filterVendor').value = '';
  filterSalesReps();
};

// CSV 내보내기
window.exportSalesRepsToCsv = function() {
  const table = document.getElementById('tblSalesReps');
  const rows = Array.from(table.querySelectorAll('tr'));

  const csv = '\uFEFF' + rows.map(row => {
    const cells = Array.from(row.querySelectorAll('th, td'));
    return cells.slice(0, -1).map(cell => {
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
  link.download = '영업사원목록_' + dateStr + '.csv';
  link.click();
};

// 영업사원 추가 모달 열기
window.openAddSalesRepModal = function() {
  document.getElementById('modalTitle').textContent = '영업사원 추가';
  document.getElementById('modalMode').value = 'add';
  document.getElementById('frmSalesRep').reset();
  document.getElementById('userId').value = '';
  document.getElementById('isActive').checked = true;
  document.getElementById('password').required = true;
  document.getElementById('passwordHint').style.display = 'none';
  document.getElementById('salesRepModal').style.display = 'flex';
};

// 모달 닫기
window.closeSalesRepModal = function() {
  document.getElementById('salesRepModal').style.display = 'none';
};

// 영업사원 수정
window.editSalesRep = function(userId) {
  const data = {};
  data['<?= encryptValue('action') ?>'] = 'get_sales_rep';
  data['<?= encryptValue('user_id') ?>'] = userId;

  updateAjaxContent(data, function(response) {
    if (response.result && response.item) {
      const rep = response.item;

      document.getElementById('modalTitle').textContent = '영업사원 수정';
      document.getElementById('modalMode').value = 'edit';
      document.getElementById('userId').value = rep.user_id;

      document.getElementById('email').value = rep.email || '';
      document.getElementById('password').value = '';
      document.getElementById('password').required = false;
      document.getElementById('passwordHint').style.display = 'block';
      document.getElementById('name').value = rep.name || '';
      document.getElementById('phone').value = rep.phone || '';
      document.getElementById('vendorId').value = rep.vendor_id || '';
      document.getElementById('department').value = rep.department || '';
      document.getElementById('position').value = rep.position || '';
      document.getElementById('address').value = rep.address || '';
      document.getElementById('bankName').value = rep.bank_name || '';
      document.getElementById('bankAccountNumber').value = rep.bank_account_number || '';
      document.getElementById('bankAccountHolder').value = rep.bank_account_holder || '';
      document.getElementById('taxIdNumber').value = rep.tax_id_number || '';
      document.getElementById('notes').value = rep.notes || '';
      document.getElementById('isActive').checked = rep.is_active == 1;

      document.getElementById('salesRepModal').style.display = 'flex';
    } else {
      alert(response.error?.msg || '영업사원 정보를 불러올 수 없습니다.');
    }
  }, false);
}

// 영업사원 저장
window.saveSalesRep = function() {
  const form = document.getElementById('frmSalesRep');

  // 필수 입력 검증
  if (!document.getElementById('email').value.trim()) {
    alert('이메일은 필수 입력 항목입니다.');
    document.getElementById('email').focus();
    return;
  }

  if (!document.getElementById('name').value.trim()) {
    alert('이름은 필수 입력 항목입니다.');
    document.getElementById('name').focus();
    return;
  }

  const mode = document.getElementById('modalMode').value;

  if (mode === 'add' && !document.getElementById('password').value.trim()) {
    alert('비밀번호는 필수 입력 항목입니다.');
    document.getElementById('password').focus();
    return;
  }

  const formData = new FormData(form);
  const data = {};

  data['<?= encryptValue('action') ?>'] = mode === 'add' ? 'add_sales_rep' : 'update_sales_rep';

  const fieldMap = {
    'user_id': '<?= encryptValue('user_id') ?>',
    'email': '<?= encryptValue('email') ?>',
    'password': '<?= encryptValue('password') ?>',
    'name': '<?= encryptValue('name') ?>',
    'phone': '<?= encryptValue('phone') ?>',
    'vendor_id': '<?= encryptValue('vendor_id') ?>',
    'department': '<?= encryptValue('department') ?>',
    'position': '<?= encryptValue('position') ?>',
    'address': '<?= encryptValue('address') ?>',
    'bank_name': '<?= encryptValue('bank_name') ?>',
    'bank_account_number': '<?= encryptValue('bank_account_number') ?>',
    'bank_account_holder': '<?= encryptValue('bank_account_holder') ?>',
    'tax_id_number': '<?= encryptValue('tax_id_number') ?>',
    'notes': '<?= encryptValue('notes') ?>',
    'is_active': '<?= encryptValue('is_active') ?>'
  };

  for (let [key, value] of formData.entries()) {
    if (fieldMap[key]) {
      if (key === 'is_active') {
        data[fieldMap[key]] = document.getElementById('isActive').checked ? '1' : '0';
      } else {
        data[fieldMap[key]] = value;
      }
    }
  }

  updateAjaxContent(data, function(response) {
    if (response.result) {
      alert(mode === 'add' ? '영업사원이 등록되었습니다.' : '영업사원 정보가 수정되었습니다.');
      closeSalesRepModal();
      filterSalesReps();
    } else {
      alert('저장 실패: ' + (response.error?.msg || '알 수 없는 오류'));
    }
  }, false);
}

// 영업사원 삭제
window.deleteSalesRep = function(userId, name) {
  if (!confirm('정말로 영업사원 "' + name + '"을(를) 삭제하시겠습니까?')) {
    return;
  }

  const data = {};
  data['<?= encryptValue('action') ?>'] = 'delete_sales_rep';
  data['<?= encryptValue('user_id') ?>'] = userId;

  updateAjaxContent(data, function(response) {
    if (response.result) {
      alert('영업사원이 삭제되었습니다.');
      filterSalesReps();
    } else {
      alert('삭제 실패: ' + (response.error?.msg || '알 수 없는 오류'));
    }
  }, false);
}

// 모달 외부 클릭 처리
document.getElementById('salesRepModal').onclick = function(e) {
  if (e.target === this) {
    closeSalesRepModal();
  }
};

// ESC 키로 모달 닫기
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape' && document.getElementById('salesRepModal').style.display === 'flex') {
    closeSalesRepModal();
  }
});

filterSalesReps();
</script>
