<?php
/**
 * HQ 고객관리 > 벤더 관리
 * 벤더 정보 CRUD (새 DB 구조: users + vendors JOIN)
 */

// AJAX 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['action']) || isset($_POST['p']))) {
    header('Content-Type: application/json; charset=utf-8');

    $action = $_POST['action'] ?? 'filter_vendors';

    switch ($action) {
        case 'get_vendor':
            $vendorId = isset($_POST['vendor_id']) ? intval($_POST['vendor_id']) : 0;

            if (!$vendorId) {
                $response['result'] = false;
                $response['error'] = ['msg' => '벤더 ID가 필요합니다.', 'code' => 400];
                Finish();
            }

            // JOIN users table to get email and login info
            $sql = "SELECT v.*,
                           v.ceo_name as representative,
                           v.account_number as bank_account_number,
                           v.account_holder as bank_account_holder,
                           u.email, u.name as user_name, u.phone as user_phone, u.is_active as user_active
                    FROM vendors v
                    LEFT JOIN users u ON v.user_id = u.user_id
                    WHERE v.vendor_id = {$vendorId} AND v.deleted_at IS NULL";
            $response['item']['sql'] = $sql;
            $result = mysqli_query($con, $sql);

            if ($result && $row = mysqli_fetch_assoc($result)) {
                $response['result'] = true;
                $response['item'] = $row;
            } else {
                $response['result'] = false;
                $response['error'] = ['msg' => '벤더를 찾을 수 없습니다.', 'code' => 404];
            }
            Finish();

        case 'add_vendor':
            // Get form data
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? trim($_POST['password']) : '';
            $userName = isset($_POST['user_name']) ? trim($_POST['user_name']) : '';
            $userPhone = isset($_POST['user_phone']) ? trim($_POST['user_phone']) : '';

            $companyName = isset($_POST['company_name']) ? trim($_POST['company_name']) : '';
            $businessNumber = isset($_POST['business_number']) ? trim($_POST['business_number']) : '';
            $representative = isset($_POST['representative']) ? trim($_POST['representative']) : '';
            $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
            $address = isset($_POST['address']) ? trim($_POST['address']) : '';
            $contactPerson = isset($_POST['contact_person']) ? trim($_POST['contact_person']) : '';
            $contactPhone = isset($_POST['contact_phone']) ? trim($_POST['contact_phone']) : '';
            $contactEmail = isset($_POST['contact_email']) ? trim($_POST['contact_email']) : '';
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

            if (empty($companyName)) {
                $response['result'] = false;
                $response['error'] = ['msg' => '회사명은 필수 항목입니다.', 'code' => 400];
                Finish();
            }

            // Check email duplicate
            $emailEsc = mysqli_real_escape_string($con, $email);
            $checkSql = "SELECT user_id FROM users WHERE email = '{$emailEsc}'";
            $checkResult = mysqli_query($con, $checkSql);
            if ($checkResult && mysqli_num_rows($checkResult) > 0) {
                $response['result'] = false;
                $response['error'] = ['msg' => '이미 사용 중인 이메일입니다.', 'code' => 400];
                Finish();
            }

            // Get VENDOR role_id
            $roleResult = mysqli_query($con, "SELECT role_id FROM roles WHERE role_name = 'VENDOR' LIMIT 1");
            if (!$roleResult || mysqli_num_rows($roleResult) === 0) {
                $response['result'] = false;
                $response['error'] = ['msg' => 'VENDOR 역할을 찾을 수 없습니다.', 'code' => 500];
                Finish();
            }
            $roleRow = mysqli_fetch_assoc($roleResult);
            $roleId = (int)$roleRow['role_id'];

            // Start transaction
            mysqli_begin_transaction($con);

            try {
                // 1. Create user first
                $passwordHash = password_hash($password, PASSWORD_BCRYPT);
                $userNameEsc = mysqli_real_escape_string($con, $userName ?: $companyName);
                $userPhoneEsc = mysqli_real_escape_string($con, $userPhone);

                $userSql = "INSERT INTO users (email, password_hash, name, phone, role_id, is_active, created_at)
                            VALUES ('{$emailEsc}', '{$passwordHash}', '{$userNameEsc}', " .
                            ($userPhone ? "'{$userPhoneEsc}'" : "NULL") . ", {$roleId}, 1, NOW())";

                if (!mysqli_query($con, $userSql)) {
                    throw new Exception('사용자 생성 실패: ' . mysqli_error($con));
                }

                $userId = mysqli_insert_id($con);

                // 2. Create vendor with user_id FK
                $companyNameEsc = mysqli_real_escape_string($con, $companyName);
                $businessNumberEsc = mysqli_real_escape_string($con, $businessNumber);
                $ceoNameEsc = mysqli_real_escape_string($con, $representative); // representative -> ceo_name
                $addressEsc = mysqli_real_escape_string($con, $address);
                $phoneEsc = mysqli_real_escape_string($con, $phone);
                $contactPersonEsc = mysqli_real_escape_string($con, $contactPerson);
                $contactPhoneEsc = mysqli_real_escape_string($con, $contactPhone);
                $contactEmailEsc = mysqli_real_escape_string($con, $contactEmail);
                $bankNameEsc = mysqli_real_escape_string($con, $bankName);
                $accountNumberEsc = mysqli_real_escape_string($con, $bankAccountNumber); // bank_account_number -> account_number
                $accountHolderEsc = mysqli_real_escape_string($con, $bankAccountHolder); // bank_account_holder -> account_holder
                $taxIdNumberEsc = mysqli_real_escape_string($con, $taxIdNumber);
                $notesEsc = mysqli_real_escape_string($con, $notes);

                $vendorSql = "INSERT INTO vendors (
                                user_id, company_name, business_number, ceo_name, address,
                                phone, contact_person, contact_phone, contact_email,
                                bank_name, account_number, account_holder, tax_id_number,
                                notes, commission_rate, incentive_rate, created_at
                            ) VALUES (
                                {$userId},
                                '{$companyNameEsc}',
                                " . ($businessNumber ? "'{$businessNumberEsc}'" : "NULL") . ",
                                " . ($representative ? "'{$ceoNameEsc}'" : "NULL") . ",
                                " . ($address ? "'{$addressEsc}'" : "NULL") . ",
                                " . ($phone ? "'{$phoneEsc}'" : "NULL") . ",
                                " . ($contactPerson ? "'{$contactPersonEsc}'" : "NULL") . ",
                                " . ($contactPhone ? "'{$contactPhoneEsc}'" : "NULL") . ",
                                " . ($contactEmail ? "'{$contactEmailEsc}'" : "NULL") . ",
                                " . ($bankName ? "'{$bankNameEsc}'" : "NULL") . ",
                                " . ($bankAccountNumber ? "'{$accountNumberEsc}'" : "NULL") . ",
                                " . ($bankAccountHolder ? "'{$accountHolderEsc}'" : "NULL") . ",
                                " . ($taxIdNumber ? "'{$taxIdNumberEsc}'" : "NULL") . ",
                                " . ($notes ? "'{$notesEsc}'" : "NULL") . ",
                                40.00, 5.00, NOW()
                            )";
                $response['item']['vendorSql'] = $vendorSql;
                if (!mysqli_query($con, $vendorSql)) {
                    throw new Exception('벤더 생성 실패: ' . mysqli_error($con));
                }

                $vendorId = mysqli_insert_id($con);

                // Commit transaction
                mysqli_commit($con);

                $response['result'] = true;
                $response['msg'] = '벤더가 등록되었습니다.';
                $response['item'] = ['vendor_id' => $vendorId, 'user_id' => $userId];

            } catch (Exception $e) {
                mysqli_rollback($con);
                $response['result'] = false;

                // 공통 함수로 에러 메시지 변환
                $errorMsg = getFriendlyErrorMessage($e->getMessage());

                $response['error'] = ['msg' => $errorMsg, 'code' => 500];
            }

            Finish();

        case 'update_vendor':
            // Get form data
            $vendorId = isset($_POST['vendor_id']) ? intval($_POST['vendor_id']) : 0;
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? trim($_POST['password']) : '';
            $userName = isset($_POST['user_name']) ? trim($_POST['user_name']) : '';
            $userPhone = isset($_POST['user_phone']) ? trim($_POST['user_phone']) : '';
            $isActive = isset($_POST['is_active']) ? intval($_POST['is_active']) : 0;

            $companyName = isset($_POST['company_name']) ? trim($_POST['company_name']) : '';
            $businessNumber = isset($_POST['business_number']) ? trim($_POST['business_number']) : '';
            $representative = isset($_POST['representative']) ? trim($_POST['representative']) : '';
            $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
            $address = isset($_POST['address']) ? trim($_POST['address']) : '';
            $contactPerson = isset($_POST['contact_person']) ? trim($_POST['contact_person']) : '';
            $contactPhone = isset($_POST['contact_phone']) ? trim($_POST['contact_phone']) : '';
            $contactEmail = isset($_POST['contact_email']) ? trim($_POST['contact_email']) : '';
            $bankName = isset($_POST['bank_name']) ? trim($_POST['bank_name']) : '';
            $bankAccountNumber = isset($_POST['bank_account_number']) ? trim($_POST['bank_account_number']) : '';
            $bankAccountHolder = isset($_POST['bank_account_holder']) ? trim($_POST['bank_account_holder']) : '';
            $taxIdNumber = isset($_POST['tax_id_number']) ? trim($_POST['tax_id_number']) : '';
            $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';

            // Validate required fields
            if (!$vendorId || empty($email) || empty($companyName)) {
                $response['result'] = false;
                $response['error'] = ['msg' => '필수 항목을 입력해주세요.', 'code' => 400];
                Finish();
            }

            // Get vendor's user_id
            $vendorCheck = mysqli_query($con, "SELECT user_id FROM vendors WHERE vendor_id = {$vendorId}");
            if (!$vendorCheck || mysqli_num_rows($vendorCheck) === 0) {
                $response['result'] = false;
                $response['error'] = ['msg' => '벤더를 찾을 수 없습니다.', 'code' => 404];
                Finish();
            }
            $vendorData = mysqli_fetch_assoc($vendorCheck);
            $userId = (int)$vendorData['user_id'];

            // Check email duplicate (except current user)
            $emailEsc = mysqli_real_escape_string($con, $email);
            $checkSql = "SELECT user_id FROM users WHERE email = '{$emailEsc}' AND user_id != {$userId}";
            $checkResult = mysqli_query($con, $checkSql);
            if ($checkResult && mysqli_num_rows($checkResult) > 0) {
                $response['result'] = false;
                $response['error'] = ['msg' => '이미 사용 중인 이메일입니다.', 'code' => 400];
                Finish();
            }

            // Start transaction
            mysqli_begin_transaction($con);

            try {
                // 1. Update users table
                $userNameEsc = mysqli_real_escape_string($con, $userName ?: $companyName);
                $userPhoneEsc = mysqli_real_escape_string($con, $userPhone);

                $userSql = "UPDATE users SET
                            email = '{$emailEsc}',
                            name = '{$userNameEsc}',
                            phone = " . ($userPhone ? "'{$userPhoneEsc}'" : "NULL") . ",
                            is_active = {$isActive},
                            updated_at = NOW()";

                // Update password if provided
                if ($password) {
                    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
                    $userSql .= ", password_hash = '{$passwordHash}'";
                }

                $userSql .= " WHERE user_id = {$userId}";
                $response['item']['sql'] = $userSql;
                if (!mysqli_query($con, $userSql)) {
                    throw new Exception('사용자 정보 수정 실패: ' . mysqli_error($con));
                }

                // 2. Update vendors table
                $companyNameEsc = mysqli_real_escape_string($con, $companyName);
                $businessNumberEsc = mysqli_real_escape_string($con, $businessNumber);
                $ceoNameEsc = mysqli_real_escape_string($con, $representative); // representative -> ceo_name
                $addressEsc = mysqli_real_escape_string($con, $address);
                $phoneEsc = mysqli_real_escape_string($con, $phone);
                $contactPersonEsc = mysqli_real_escape_string($con, $contactPerson);
                $contactPhoneEsc = mysqli_real_escape_string($con, $contactPhone);
                $contactEmailEsc = mysqli_real_escape_string($con, $contactEmail);
                $bankNameEsc = mysqli_real_escape_string($con, $bankName);
                $accountNumberEsc = mysqli_real_escape_string($con, $bankAccountNumber); // bank_account_number -> account_number
                $accountHolderEsc = mysqli_real_escape_string($con, $bankAccountHolder); // bank_account_holder -> account_holder
                $taxIdNumberEsc = mysqli_real_escape_string($con, $taxIdNumber);
                $notesEsc = mysqli_real_escape_string($con, $notes);

                $vendorSql = "UPDATE vendors SET
                                company_name = '{$companyNameEsc}',
                                business_number = " . ($businessNumber ? "'{$businessNumberEsc}'" : "NULL") . ",
                                ceo_name = " . ($representative ? "'{$ceoNameEsc}'" : "NULL") . ",
                                address = " . ($address ? "'{$addressEsc}'" : "NULL") . ",
                                phone = " . ($phone ? "'{$phoneEsc}'" : "NULL") . ",
                                contact_person = " . ($contactPerson ? "'{$contactPersonEsc}'" : "NULL") . ",
                                contact_phone = " . ($contactPhone ? "'{$contactPhoneEsc}'" : "NULL") . ",
                                contact_email = " . ($contactEmail ? "'{$contactEmailEsc}'" : "NULL") . ",
                                bank_name = " . ($bankName ? "'{$bankNameEsc}'" : "NULL") . ",
                                account_number = " . ($bankAccountNumber ? "'{$accountNumberEsc}'" : "NULL") . ",
                                account_holder = " . ($bankAccountHolder ? "'{$accountHolderEsc}'" : "NULL") . ",
                                tax_id_number = " . ($taxIdNumber ? "'{$taxIdNumberEsc}'" : "NULL") . ",
                                notes = " . ($notes ? "'{$notesEsc}'" : "NULL") . ",
                                updated_at = NOW()
                            WHERE vendor_id = {$vendorId}";
                $response['item']['vendorSql'] = $vendorSql;
                if (!mysqli_query($con, $vendorSql)) {
                    throw new Exception('벤더 정보 수정 실패: ' . mysqli_error($con));
                }

                // Commit transaction
                mysqli_commit($con);

                $response['result'] = true;
                $response['msg'] = '벤더 정보가 수정되었습니다.';

            } catch (Exception $e) {
                mysqli_rollback($con);
                $response['result'] = false;

                // 공통 함수로 에러 메시지 변환
                $errorMsg = getFriendlyErrorMessage($e->getMessage());

                $response['error'] = ['msg' => $errorMsg, 'code' => 500];
            }

            Finish();

        case 'delete_vendor':
            $vendorId = isset($_POST['vendor_id']) ? intval($_POST['vendor_id']) : 0;

            if (!$vendorId) {
                $response['result'] = false;
                $response['error'] = ['msg' => '벤더 ID가 필요합니다.', 'code' => 400];
                Finish();
            }

            // Soft delete - set deleted_at timestamp
            $sql = "UPDATE vendors SET deleted_at = NOW(), updated_at = NOW() WHERE vendor_id = {$vendorId}";

            if (mysqli_query($con, $sql)) {
                $response['result'] = true;
                $response['msg'] = '벤더가 삭제되었습니다.';
            } else {
                $response['result'] = false;
                $response['error'] = ['msg' => '삭제에 실패했습니다: ' . mysqli_error($con), 'code' => 500];
            }
            Finish();

        case 'filter_vendors':
            // 필터 파라미터 (_ajax_.php에서 이미 복호화됨)
            $searchKeyword = isset($_POST['search']) ? $_POST['search'] : '';
            $statusFilter = isset($_POST['status']) ? $_POST['status'] : '';

            // WHERE 조건 구성
            $searchString = "v.deleted_at IS NULL";

            if ($searchKeyword) {
                $searchEsc = mysqli_real_escape_string($con, $searchKeyword);
                $searchString .= " AND (v.company_name LIKE '%{$searchEsc}%'
                           OR v.ceo_name LIKE '%{$searchEsc}%'
                           OR v.business_number LIKE '%{$searchEsc}%'
                           OR u.email LIKE '%{$searchEsc}%'
                           OR u.name LIKE '%{$searchEsc}%')";
            }

            if ($statusFilter !== '') {
                $searchString .= " AND u.is_active = " . intval($statusFilter);
            }

            // Build SELECT query
            $sql = "SELECT
                v.*,
                v.ceo_name as representative,
                u.email, u.name as user_name, u.phone, u.is_active as user_active,
                COUNT(DISTINCT aa.customer_id) as customer_count,
                COUNT(DISTINCT CASE WHEN aa.is_active = 1 THEN aa.customer_id END) as active_customer_count
            FROM vendors v
            LEFT JOIN users u ON v.user_id = u.user_id
            LEFT JOIN account_assignments aa ON v.vendor_id = aa.vendor_id
            WHERE {$searchString}
            GROUP BY v.vendor_id";

            // Pagination: Calculate manually for GROUP BY queries
            $rowsPage = $defaultRowsPage;
            $p = $_POST['p'] ?? 1;
            $curPage = $rowsPage * ($p - 1);

            $sql .= " ORDER BY v.created_at DESC LIMIT {$curPage}, {$rowsPage}";

            // Debugging SQL
            $response['data']['search']['sql'] = $sql;

            $result = mysqli_query($con, $sql);

            // Generate pagination HTML manually for GROUP BY
            $countSql = "SELECT COUNT(DISTINCT v.vendor_id) as total
                         FROM vendors v
                         LEFT JOIN users u ON v.user_id = u.user_id
                         WHERE {$searchString}";
            $countResult = mysqli_query($con, $countSql);
            $totalRows = 0;
            if ($countResult && $row = mysqli_fetch_assoc($countResult)) {
                $totalRows = $row['total'];
            }

            $pagination = '';
            if ($totalRows > 0) {
                $totalPages = ceil($totalRows / $rowsPage);
                $currentPage = $p;
                $startPage = max(1, $currentPage - 5);
                $endPage = min($totalPages, $currentPage + 5);
                $atValue = encryptValue('10');
                $targetId = '#vendorTableBody';

                $pagination .= '<div class="pagination" data-at="' . $atValue . '">';

                if ($currentPage > 1) {
                    $prevPage = $currentPage - 1;
                    $pagination .= '<a href="#" data-p="' . $prevPage . '" data-id="' . htmlspecialchars($targetId) . '">&laquo; 이전</a>';
                }

                if ($startPage > 1) {
                    $pagination .= '<a href="#" data-p="1" data-id="' . htmlspecialchars($targetId) . '">1</a>';
                    if ($startPage > 2) {
                        $pagination .= '<span>...</span>';
                    }
                }

                for ($i = $startPage; $i <= $endPage; $i++) {
                    if ($i == $currentPage) {
                        $pagination .= '<a href="#" class="active" data-p="' . $i . '" data-id="' . htmlspecialchars($targetId) . '">' . $i . '</a>';
                    } else {
                        $pagination .= '<a href="#" data-p="' . $i . '" data-id="' . htmlspecialchars($targetId) . '">' . $i . '</a>';
                    }
                }

                if ($endPage < $totalPages) {
                    if ($endPage < $totalPages - 1) {
                        $pagination .= '<span>...</span>';
                    }
                    $pagination .= '<a href="#" data-p="' . $totalPages . '" data-id="' . htmlspecialchars($targetId) . '">' . $totalPages . '</a>';
                }

                if ($currentPage < $totalPages) {
                    $nextPage = $currentPage + 1;
                    $pagination .= '<a href="#" data-p="' . $nextPage . '" data-id="' . htmlspecialchars($targetId) . '">다음 &raquo;</a>';
                }

                $pagination .= '</div>';
                $pagination .= '<div class="pagination-info">전체 ' . number_format($totalRows) . '개 / ' . $currentPage . ' / ' . $totalPages . ' 페이지</div>';
            }

            $response['pagination'] = $pagination;

            $html = '';
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $html .= '<tr>';
                    $html .= '<td>' . htmlspecialchars($row['vendor_id']) . '</td>';
                    $html .= '<td><strong>' . htmlspecialchars($row['company_name']) . '</strong></td>';
                    $html .= '<td>' . htmlspecialchars($row['representative'] ?? '-') . '</td>';
                    $html .= '<td>' . htmlspecialchars($row['email'] ?? '-') . '</td>';
                    $html .= '<td>' . htmlspecialchars($row['phone'] ?? '-') . '</td>';
                    $html .= '<td>' . number_format($row['active_customer_count']) . ' / ' . number_format($row['customer_count']) . '</td>';
                    $html .= '<td>';
                    if ($row['user_active']) {
                        $html .= '<span class="badge badge-success">활성</span>';
                    } else {
                        $html .= '<span class="badge badge-secondary">비활성</span>';
                    }
                    $html .= '</td>';
                    $html .= '<td>' . date('Y-m-d', strtotime($row['created_at'])) . '</td>';
                    $html .= '<td>';
                    $html .= '<button class="btn-sm btn-edit" onclick="editVendor(' . $row['vendor_id'] . ')">수정</button> ';
                    $html .= '<button class="btn-sm btn-delete" onclick="deleteVendor(' . $row['vendor_id'] . ', \'' . htmlspecialchars($row['company_name'], ENT_QUOTES) . '\')">삭제</button>';
                    $html .= '</td>';
                    $html .= '</tr>';
                }
            } else {
                $html = '<tr><td colspan="9" class="table-empty-state">조회된 데이터가 없습니다.</td></tr>';
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
$searchKeyword = isset($_POST['search']) ? $_POST['search'] : (isset($_GET['search']) ? $_GET['search'] : '');
$statusFilter = isset($_POST['status']) ? $_POST['status'] : (isset($_GET['status']) ? $_GET['status'] : '');

// 벤더 목록 조회 (users JOIN)
$sql = "
SELECT
    v.*,
    v.ceo_name as representative,
    u.email, u.name as user_name, u.phone, u.is_active as user_active,
    COUNT(DISTINCT aa.customer_id) as customer_count,
    COUNT(DISTINCT CASE WHEN aa.is_active = 1 THEN aa.customer_id END) as active_customer_count
FROM vendors v
LEFT JOIN users u ON v.user_id = u.user_id
LEFT JOIN account_assignments aa ON v.vendor_id = aa.vendor_id
WHERE v.deleted_at IS NULL
";

if ($searchKeyword) {
    $searchEsc = mysqli_real_escape_string($con, $searchKeyword);
    $sql .= " AND (v.company_name LIKE '%{$searchEsc}%'
               OR v.ceo_name LIKE '%{$searchEsc}%'
               OR v.business_number LIKE '%{$searchEsc}%'
               OR u.email LIKE '%{$searchEsc}%'
               OR u.name LIKE '%{$searchEsc}%')";
}

if ($statusFilter !== '') {
    $sql .= " AND u.is_active = " . intval($statusFilter);
}

$sql .= " GROUP BY v.vendor_id
          ORDER BY v.created_at DESC";
$response['item']['sql'] = $sql;
$result = mysqli_query($con, $sql);

$vendors = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $vendors[] = $row;
    }
}
?>

<section class="card">
  <!-- 필터 -->
  <div class="card-hd card-hd-wrap">
    <div class="card-hd-content">
      <div class="card-hd-title-area">
        <div class="card-ttl">벤더 관리</div>
        <div class="card-sub">벤더 정보 등록·수정·조회</div>
      </div>
      <div class="filter-toolbar">
        <div class="filter-group">
          <label>검색</label>
          <input type="text" id="searchKeyword" name="search" class="form-control filter-input" placeholder="회사명/대표자/이메일"
                 value="<?php echo htmlspecialchars($searchKeyword); ?>" onkeypress="if(event.key==='Enter') filterVendors()">
        </div>
        <div class="filter-group">
          <label>상태</label>
          <select id="statusFilter" name="status" class="form-control filter-select">
            <option value="">전체</option>
            <option value="1" <?php echo $statusFilter === '1' ? 'selected' : ''; ?>>활성</option>
            <option value="0" <?php echo $statusFilter === '0' ? 'selected' : ''; ?>>비활성</option>
          </select>
        </div>
        <button id="btnFilter" class="btn primary" onclick="filterVendors()">조회</button>
        <button id="btnResetFilter" class="btn" onclick="resetVendorFilter()">초기화</button>
        <button id="btnAddVendor" class="btn primary" onclick="openAddVendorModal()">신규 벤더 등록</button>
      </div>
    </div>
    <div class="row">
      <button id="btnExportCsv" class="btn" onclick="exportVendorsToCsv()">CSV 내보내기</button>
    </div>
  </div>

  <!-- 벤더 목록 테이블 -->
  <div class="card-bd card-bd-padding">
    <div class="table-wrap">
      <table class="tbl-list" id="tblVendors">
        <thead>
          <tr>
            <th>벤더ID</th>
            <th>회사명</th>
            <th>대표자</th>
            <th>이메일</th>
            <th>연락처</th>
            <th>고객수</th>
            <th>상태</th>
            <th>등록일</th>
            <th>관리</th>
          </tr>
        </thead>
        <tbody id="vendorTableBody">
        </tbody>
      </table>
    </div>

    <!-- 페이징 영역 -->
    <div class="paging" data-id="#vendorTableBody"></div>
  </div>
</section>

<!-- 벤더 등록/수정 모달 -->
<div id="vendorModal" class="modal">
  <div class="modal-content modal-content-lg">
    <div class="modal-header">
      <h3 id="modalTitle">신규 벤더 등록</h3>
      <button class="modal-close" onclick="closeVendorModal()">&times;</button>
    </div>
    <div class="modal-body">
      <form id="vendorForm">
        <input type="hidden" id="vendorId" name="vendor_id">
        <input type="hidden" id="formMode" value="add">

        <div class="grid-2">
          <!-- 로그인 정보 -->
          <div>
            <h4 class="section-header">로그인 정보</h4>

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
              <label>담당자명</label>
              <input type="text" id="userName" name="user_name" class="form-control">
              <small class="text-muted">미입력 시 회사명 사용</small>
            </div>

            <div class="form-group">
              <label>담당자 연락처</label>
              <input type="tel" id="userPhone" name="user_phone" class="form-control">
            </div>

            <div class="form-group">
              <label>
                <input type="checkbox" id="isActive" name="is_active" checked>
                활성 상태
              </label>
              <small class="text-muted">비활성 시 로그인 불가</small>
            </div>
          </div>

          <!-- 회사 정보 -->
          <div>
            <h4 class="section-header">회사 정보</h4>

            <div class="form-group">
              <label class="required">회사명</label>
              <input type="text" id="companyName" name="company_name" class="form-control" required>
            </div>

            <div class="form-group">
              <label>사업자번호</label>
              <input type="text" id="businessNumber" name="business_number" class="form-control" placeholder="000-00-00000">
            </div>

            <div class="form-group">
              <label>대표자명</label>
              <input type="text" id="representative" name="representative" class="form-control">
            </div>

            <div class="form-group">
              <label>전화번호</label>
              <input type="tel" id="phone" name="phone" class="form-control" placeholder="02-000-0000">
            </div>

            <div class="form-group">
              <label>주소</label>
              <textarea id="address" name="address" class="form-control" rows="2"></textarea>
            </div>
          </div>
        </div>

        <!-- 담당자 정보 -->
        <h4 class="section-header-spaced">현장 담당자 정보 (선택)</h4>
        <div class="grid-3">
          <div class="form-group">
            <label>담당자명</label>
            <input type="text" id="contactPerson" name="contact_person" class="form-control">
          </div>
          <div class="form-group">
            <label>담당자 연락처</label>
            <input type="tel" id="contactPhone" name="contact_phone" class="form-control">
          </div>
          <div class="form-group">
            <label>담당자 이메일</label>
            <input type="email" id="contactEmail" name="contact_email" class="form-control">
          </div>
        </div>

        <!-- 은행 정보 -->
        <h4 class="section-header-spaced">은행 정보 (수수료 지급용)</h4>
        <div class="grid-2">
          <div class="form-group">
            <label>은행명</label>
            <input type="text" id="bankName" name="bank_name" class="form-control" placeholder="예: 신한은행">
          </div>
          <div class="form-group">
            <label>계좌번호</label>
            <input type="text" id="bankAccountNumber" name="bank_account_number" class="form-control">
          </div>
          <div class="form-group">
            <label>예금주</label>
            <input type="text" id="bankAccountHolder" name="bank_account_holder" class="form-control">
          </div>
          <div class="form-group">
            <label>주민/사업자번호 (국세청 신고용)</label>
            <input type="text" id="taxIdNumber" name="tax_id_number" class="form-control" placeholder="000000-0000000">
          </div>
        </div>

        <div class="form-group">
          <label>메모</label>
          <textarea id="notes" name="notes" class="form-control" rows="3" placeholder="기타 특이사항이나 메모"></textarea>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn" onclick="closeVendorModal()">취소</button>
      <button class="btn primary" onclick="saveVendor()">저장</button>
    </div>
  </div>
</div>



<script>
// 페이지 이름 (AJAX 호출용)
window.pageName = '<?= $pageName ?>';

// 필터 적용
window.filterVendors = function() {
  const searchKeyword = document.getElementById('searchKeyword').value || '';
  const statusFilter = document.getElementById('statusFilter').value || '';

  const data = {};
  data['<?= encryptValue('action') ?>'] = 'filter_vendors';
  data['<?= encryptValue('search') ?>'] = searchKeyword;
  data['<?= encryptValue('status') ?>'] = statusFilter;

  updateAjaxContent(data, function(response) {
    if (response.result && response.html) {
      const tbody = document.querySelector('#vendorTableBody');
      if (tbody) {
        tbody.innerHTML = response.html;

        // 테이블 리렌더링 후 스타일 재적용
        const table = document.getElementById('tblVendors');
        if (table) {
          // 강제 리플로우
          table.offsetHeight;
          // 테이블 display 재설정 (레이아웃 리셋)
          const originalDisplay = table.style.display;
          table.style.display = 'none';
          table.offsetHeight; // 리플로우 트리거
          table.style.display = originalDisplay || '';
        }
      }

      // Update pagination
      if (response.pagination) {
        const pagingContainer = document.querySelector('.paging[data-id="#vendorTableBody"]');
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
window.resetVendorFilter = function() {
  document.getElementById('searchKeyword').value = '';
  document.getElementById('statusFilter').value = '';
  filterVendors();
};

// 신규 벤더 등록 모달 열기
window.openAddVendorModal = function() {
  document.getElementById('modalTitle').textContent = '신규 벤더 등록';
  document.getElementById('formMode').value = 'add';
  document.getElementById('vendorForm').reset();
  document.getElementById('vendorId').value = '';
  document.getElementById('isActive').checked = true;
  document.getElementById('password').required = true;
  document.getElementById('passwordHint').style.display = 'none';
  document.getElementById('vendorModal').style.display = 'flex';
};

// 벤더 수정
window.editVendor = function(vendorId) {
  const data = {};
  data['<?= encryptValue('action') ?>'] = 'get_vendor';
  data['<?= encryptValue('vendor_id') ?>'] = vendorId;

  updateAjaxContent(data, function(response) {
    if (response.result && response.item) {
      const vendor = response.item;

      document.getElementById('modalTitle').textContent = '벤더 정보 수정';
      document.getElementById('formMode').value = 'edit';
      document.getElementById('vendorId').value = vendor.vendor_id;

      // User info
      document.getElementById('email').value = vendor.email || '';
      document.getElementById('password').value = '';
      document.getElementById('password').required = false;
      document.getElementById('passwordHint').style.display = 'block';
      document.getElementById('userName').value = vendor.user_name || '';
      document.getElementById('userPhone').value = vendor.user_phone || '';
      document.getElementById('isActive').checked = vendor.user_active == 1;

      // Vendor info
      document.getElementById('companyName').value = vendor.company_name || '';
      document.getElementById('businessNumber').value = vendor.business_number || '';
      document.getElementById('representative').value = vendor.representative || '';
      document.getElementById('phone').value = vendor.phone || '';
      document.getElementById('address').value = vendor.address || '';
      document.getElementById('contactPerson').value = vendor.contact_person || '';
      document.getElementById('contactPhone').value = vendor.contact_phone || '';
      document.getElementById('contactEmail').value = vendor.contact_email || '';
      document.getElementById('bankName').value = vendor.bank_name || '';
      document.getElementById('bankAccountNumber').value = vendor.bank_account_number || '';
      document.getElementById('bankAccountHolder').value = vendor.bank_account_holder || '';
      document.getElementById('taxIdNumber').value = vendor.tax_id_number || '';
      document.getElementById('notes').value = vendor.notes || '';

      document.getElementById('vendorModal').style.display = 'flex';
    } else {
      alert('벤더 정보를 불러올 수 없습니다.');
    }
  }, false);
}

// 벤더 삭제
window.deleteVendor = function(vendorId, companyName) {
  if (!confirm('정말로 "' + companyName + '" 벤더를 삭제하시겠습니까?')) {
    return;
  }

  const data = {};
  data['<?= encryptValue('action') ?>'] = 'delete_vendor';
  data['<?= encryptValue('vendor_id') ?>'] = vendorId;

  updateAjaxContent(data, function(response) {
    if (response.result) {
      alert('벤더가 삭제되었습니다.');
      filterVendors();
    } else {
      alert('삭제 실패: ' + (response.error?.msg || '알 수 없는 오류'));
    }
  }, false);
}

// 모달 닫기
window.closeVendorModal = function() {
  document.getElementById('vendorModal').style.display = 'none';
  document.getElementById('vendorForm').reset();
}

// 벤더 저장
window.saveVendor = function() {
  const form = document.getElementById('vendorForm');

  // 필수 입력 검증
  if (!document.getElementById('email').value.trim()) {
    alert('이메일은 필수 입력 항목입니다.');
    document.getElementById('email').focus();
    return;
  }

  if (!document.getElementById('companyName').value.trim()) {
    alert('회사명은 필수 입력 항목입니다.');
    document.getElementById('companyName').focus();
    return;
  }

  const mode = document.getElementById('formMode').value;

  if (mode === 'add' && !document.getElementById('password').value.trim()) {
    alert('비밀번호는 필수 입력 항목입니다.');
    document.getElementById('password').focus();
    return;
  }

  const formData = new FormData(form);
  const data = {};

  data['<?= encryptValue('action') ?>'] = mode === 'add' ? 'add_vendor' : 'update_vendor';

  // Pre-encrypted field names mapping
  const fieldMap = {
    'vendor_id': '<?= encryptValue('vendor_id') ?>',
    'email': '<?= encryptValue('email') ?>',
    'password': '<?= encryptValue('password') ?>',
    'user_name': '<?= encryptValue('user_name') ?>',
    'user_phone': '<?= encryptValue('user_phone') ?>',
    'company_name': '<?= encryptValue('company_name') ?>',
    'business_number': '<?= encryptValue('business_number') ?>',
    'representative': '<?= encryptValue('representative') ?>',
    'phone': '<?= encryptValue('phone') ?>',
    'address': '<?= encryptValue('address') ?>',
    'contact_person': '<?= encryptValue('contact_person') ?>',
    'contact_phone': '<?= encryptValue('contact_phone') ?>',
    'contact_email': '<?= encryptValue('contact_email') ?>',
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
      alert(mode === 'add' ? '벤더가 등록되었습니다.' : '벤더 정보가 수정되었습니다.');
      closeVendorModal();
      filterVendors();
    } else {
      alert('저장 실패: ' + (response.error?.msg || '알 수 없는 오류'));
    }
  }, false);
}

// CSV 내보내기
window.exportVendorsToCsv = function() {
  const table = document.getElementById('tblVendors');
  const rows = Array.from(table.querySelectorAll('tr'));

  const csv = '\uFEFF' + rows.map(row => {
    const cells = Array.from(row.querySelectorAll('th, td'));
    // 마지막 "관리" 열 제외
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
  link.download = '벤더목록_' + dateStr + '.csv';
  link.click();
};

// 모달 외부 클릭 처리
document.getElementById('vendorModal').onclick = function(e) {
  if (e.target === this) {
    closeVendorModal();
  }
};

// ESC 키로 모달 닫기
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape' && document.getElementById('vendorModal').style.display === 'flex') {
    closeVendorModal();
  }
});

// 페이지 로드 시 자동 조회
filterVendors();
</script>
