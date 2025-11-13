<?php
/**
 * HQ 고객 목록 관리
 * 고객 정보 CRUD (결제방식 포함)
 */

// Ajax 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!empty($_POST['action']) || isset($_POST['p']))) {
    $action = $_POST['action'] ?? 'filter_customers';

    try {
        switch ($action) {
            case 'get_customer':
                $customerId = trim($_POST['customer_id'] ?? '');
                if (empty($customerId)) {
                    $response['result'] = false;
                    $response['error'] = sendError(400, true);
                    Finish();
                }

                $customerIdEsc = escapeString($customerId);
                $sql = "SELECT c.customer_id, c.company_name as name, cu.email, cu.phone, c.address,
                               c.vendor_id, v.company_name as vendor_name,
                               aa.sales_user_id as sales_rep_id, su.name as sales_rep_name,
                               c.payment_method, c.bank_name as cms_bank_name, c.account_number as cms_account_number,
                               '' as cms_account_holder, c.ceo_name as contact_person, cu.phone as contact_phone,
                               cu.email as contact_email, '' as notes, c.created_at
                        FROM `customers` c
                        LEFT JOIN `users` cu ON c.user_id = cu.user_id
                        LEFT JOIN `vendors` v ON c.vendor_id = v.vendor_id
                        LEFT JOIN `account_assignments` aa ON c.customer_id = aa.customer_id AND aa.is_active = 1
                        LEFT JOIN `users` su ON aa.sales_user_id = su.user_id
                        WHERE c.customer_id = '{$customerIdEsc}' AND c.is_active = 1";
                $response['item']['sql'] = $sql;

                $customer = query($sql);

                if (!empty($customer)) {
                    $response['result'] = true;
                    $response['item'] = $customer[0];
                } else {
                    $response['result'] = false;
                    $response['error'] = ['msg' => '고객을 찾을 수 없습니다.', 'code' => 404];
                }
                Finish();
                break;

            case 'add_customer':
                $name = trim($_POST['name'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $phone = trim($_POST['phone'] ?? '');
                $address = trim($_POST['address'] ?? '');
                $vendorId = trim($_POST['vendor_id'] ?? '');
                $salesRepId = trim($_POST['sales_rep_id'] ?? '');
                $paymentMethod = trim($_POST['payment_method'] ?? '');
                $cmsBankName = trim($_POST['cms_bank_name'] ?? '');
                $cmsAccountNumber = trim($_POST['cms_account_number'] ?? '');
                $cmsAccountHolder = trim($_POST['cms_account_holder'] ?? '');
                $contactPerson = trim($_POST['contact_person'] ?? '');
                $contactPhone = trim($_POST['contact_phone'] ?? '');
                $contactEmail = trim($_POST['contact_email'] ?? '');
                $notes = trim($_POST['notes'] ?? '');

                // 필수 항목 체크
                if (empty($name) || empty($paymentMethod)) {
                    $response['result'] = false;
                    $response['error'] = ['msg' => '필수 항목을 입력해주세요.', 'code' => 400];
                    Finish();
                }

                // CMS 결제 시 은행 정보 필수 확인
                if ($paymentMethod === 'CMS' && (empty($cmsBankName) || empty($cmsAccountNumber) || empty($cmsAccountHolder))) {
                    $response['result'] = false;
                    $response['error'] = ['msg' => 'CMS 결제 시 은행 정보는 필수입니다.', 'code' => 400];
                    Finish();
                }

                // Generate customer_id (CYYYYMMDDNNNN 형식)
                $today = date('Ymd');
                $prefix = 'C' . $today;

                // 오늘 날짜의 마지막 customer_id 조회
                $lastIdSql = "SELECT customer_id FROM customers WHERE customer_id LIKE '{$prefix}%' ORDER BY customer_id DESC LIMIT 1";
                $response['item']['lastIdSql'] = $lastIdSql;
                $lastIdResult = mysqli_query($con, $lastIdSql);

                if ($lastIdResult && mysqli_num_rows($lastIdResult) > 0) {
                    $lastRow = mysqli_fetch_assoc($lastIdResult);
                    $lastId = $lastRow['customer_id'];
                    $lastSeq = intval(substr($lastId, -4));
                    $newSeq = str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);
                } else {
                    $newSeq = '0001';
                }

                $customerId = $prefix . $newSeq;

                // Escape all values
                $customerIdEsc = escapeString($customerId);
                $nameEsc = escapeString($name);
                $emailEsc = escapeString($email);
                $phoneEsc = escapeString($phone);
                $addressEsc = escapeString($address);
                $vendorIdEsc = escapeString($vendorId);
                $salesRepIdEsc = escapeString($salesRepId);
                $paymentMethodEsc = escapeString($paymentMethod);
                $cmsBankNameEsc = escapeString($cmsBankName);
                $cmsAccountNumberEsc = escapeString($cmsAccountNumber);
                $cmsAccountHolderEsc = escapeString($cmsAccountHolder);
                $contactPersonEsc = escapeString($contactPerson);
                $contactPhoneEsc = escapeString($contactPhone);
                $contactEmailEsc = escapeString($contactEmail);
                $notesEsc = escapeString($notes);

                // Handle NULL values for optional fields
                $vendorIdValue = $vendorId ? "'{$vendorIdEsc}'" : 'NULL';
                $salesRepIdValue = $salesRepId ? "'{$salesRepIdEsc}'" : 'NULL';
                $emailValue = $email ? "'{$emailEsc}'" : 'NULL';
                $phoneValue = $phone ? "'{$phoneEsc}'" : 'NULL';
                $addressValue = $address ? "'{$addressEsc}'" : 'NULL';
                $cmsBankNameValue = $cmsBankName ? "'{$cmsBankNameEsc}'" : 'NULL';
                $cmsAccountNumberValue = $cmsAccountNumber ? "'{$cmsAccountNumberEsc}'" : 'NULL';
                $cmsAccountHolderValue = $cmsAccountHolder ? "'{$cmsAccountHolderEsc}'" : 'NULL';
                $contactPersonValue = $contactPerson ? "'{$contactPersonEsc}'" : 'NULL';
                $contactPhoneValue = $contactPhone ? "'{$contactPhoneEsc}'" : 'NULL';
                $contactEmailValue = $contactEmail ? "'{$contactEmailEsc}'" : 'NULL';
                $notesValue = $notes ? "'{$notesEsc}'" : 'NULL';

                $sql = "INSERT INTO `customers` (`customer_id`, `name`, `email`, `phone`, `address`, `vendor_id`, `sales_rep_id`,
                        `payment_method`, `cms_bank_name`, `cms_account_number`, `cms_account_holder`,
                        `contact_person`, `contact_phone`, `contact_email`, `notes`, `is_active`, `created_at`)
                        VALUES ('{$customerIdEsc}', '{$nameEsc}', {$emailValue}, {$phoneValue}, {$addressValue}, {$vendorIdValue}, {$salesRepIdValue},
                        '{$paymentMethodEsc}', {$cmsBankNameValue}, {$cmsAccountNumberValue}, {$cmsAccountHolderValue},
                        {$contactPersonValue}, {$contactPhoneValue}, {$contactEmailValue}, {$notesValue}, 1, NOW())";
                $response['item']['sql'] = $sql;

                $result = query($sql);

                if ($result) {
                    $response['result'] = true;
                    $response['msg'] = '고객이 등록되었습니다.';
                    $response['item'] = ['customer_id' => $customerId];
                } else {
                    $response['result'] = false;
                    $response['error'] = ['msg' => '고객 등록에 실패했습니다: ' . mysqli_error($con), 'code' => 500];
                }
                Finish();
                break;

            case 'update_customer':
                $customerId = trim($_POST['customer_id'] ?? '');
                $name = trim($_POST['name'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $phone = trim($_POST['phone'] ?? '');
                $address = trim($_POST['address'] ?? '');
                $vendorId = trim($_POST['vendor_id'] ?? '');
                $salesRepId = trim($_POST['sales_rep_id'] ?? '');
                $paymentMethod = trim($_POST['payment_method'] ?? '');
                $cmsBankName = trim($_POST['cms_bank_name'] ?? '');
                $cmsAccountNumber = trim($_POST['cms_account_number'] ?? '');
                $cmsAccountHolder = trim($_POST['cms_account_holder'] ?? '');
                $contactPerson = trim($_POST['contact_person'] ?? '');
                $contactPhone = trim($_POST['contact_phone'] ?? '');
                $contactEmail = trim($_POST['contact_email'] ?? '');
                $notes = trim($_POST['notes'] ?? '');

                // 필수 항목 체크
                if (empty($customerId) || empty($name) || empty($paymentMethod)) {
                    $response['result'] = false;
                    $response['error'] = ['msg' => '필수 항목을 입력해주세요.', 'code' => 400];
                    Finish();
                }

                // CMS 결제 시 은행 정보 필수 확인
                if ($paymentMethod === 'CMS' && (empty($cmsBankName) || empty($cmsAccountNumber))) {
                    $response['result'] = false;
                    $response['error'] = ['msg' => 'CMS 결제 시 은행 정보는 필수입니다.', 'code' => 400];
                    Finish();
                }

                try {
                    // Start transaction
                    mysqli_begin_transaction($con);

                    // Escape all values
                    $customerIdEsc = escapeString($customerId);
                    $nameEsc = escapeString($name);
                    $emailEsc = escapeString($email);
                    $phoneEsc = escapeString($phone);
                    $addressEsc = escapeString($address);
                    $vendorIdEsc = escapeString($vendorId);
                    $paymentMethodEsc = escapeString($paymentMethod);
                    $cmsBankNameEsc = escapeString($cmsBankName);
                    $cmsAccountNumberEsc = escapeString($cmsAccountNumber);
                    $notesEsc = escapeString($notes);

                    // 1. Get customer's user_id
                    $getUserSql = "SELECT user_id FROM customers WHERE customer_id = '{$customerIdEsc}' AND is_active = 1";
                    $response['item']['getUserSql'] = $getUserSql;
                    $userResult = query($getUserSql);

                    if (empty($userResult)) {
                        throw new Exception('고객을 찾을 수 없습니다.');
                    }

                    $userId = $userResult[0]['user_id'];

                    // 2. Update customers table
                    $vendorIdValue = $vendorId ? "'{$vendorIdEsc}'" : 'NULL';
                    $addressValue = $address ? "'{$addressEsc}'" : 'NULL';
                    $cmsBankNameValue = $cmsBankName ? "'{$cmsBankNameEsc}'" : 'NULL';
                    $cmsAccountNumberValue = $cmsAccountNumber ? "'{$cmsAccountNumberEsc}'" : 'NULL';

                    $updateCustomerSql = "UPDATE customers SET
                            company_name = '{$nameEsc}',
                            address = {$addressValue},
                            vendor_id = {$vendorIdValue},
                            payment_method = '{$paymentMethodEsc}',
                            bank_name = {$cmsBankNameValue},
                            account_number = {$cmsAccountNumberValue},
                            updated_at = NOW()
                            WHERE customer_id = '{$customerIdEsc}' AND is_active = 1";
                    $response['item']['updateCustomerSql'] = $updateCustomerSql;

                    if (!mysqli_query($con, $updateCustomerSql)) {
                        throw new Exception('고객 정보 업데이트 실패: ' . mysqli_error($con));
                    }

                    // 3. Update users table (email, phone, password)
                    $password = trim($_POST['password'] ?? '');
                    $emailValue = $email ? "'{$emailEsc}'" : 'NULL';
                    $phoneValue = $phone ? "'{$phoneEsc}'" : 'NULL';

                    $updateUserSql = "UPDATE users SET
                            email = {$emailValue},
                            phone = {$phoneValue}";

                    // 비밀번호가 입력된 경우에만 업데이트
                    if (!empty($password)) {
                        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                        $updateUserSql .= ", password_hash = '{$passwordHash}'";
                    }

                    $updateUserSql .= ", updated_at = NOW()
                            WHERE user_id = {$userId}";
                    $response['item']['updateUserSql'] = $updateUserSql;

                    if (!mysqli_query($con, $updateUserSql)) {
                        throw new Exception('사용자 정보 업데이트 실패: ' . mysqli_error($con));
                    }

                    // 4. Update account_assignments (sales rep assignment)
                    // First, deactivate all existing assignments for this customer
                    $deactivateAssignSql = "UPDATE account_assignments
                                           SET is_active = 0, updated_at = NOW()
                                           WHERE customer_id = '{$customerIdEsc}'";
                    $response['item']['deactivateAssignSql'] = $deactivateAssignSql;
                    mysqli_query($con, $deactivateAssignSql);

                    // Then create or activate assignment if sales rep specified
                    if (!empty($salesRepId)) {
                        $salesRepIdEsc = escapeString($salesRepId);

                        // Check if assignment exists
                        $checkAssignSql = "SELECT assignment_id FROM account_assignments
                                          WHERE customer_id = '{$customerIdEsc}'
                                          AND sales_user_id = '{$salesRepIdEsc}'
                                          AND vendor_id = {$vendorIdValue}";
                        $response['item']['checkAssignSql'] = $checkAssignSql;
                        $assignResult = query($checkAssignSql);

                        if (!empty($assignResult)) {
                            // Update existing assignment
                            $assignmentId = $assignResult[0]['assignment_id'];
                            $updateAssignSql = "UPDATE account_assignments
                                               SET is_active = 1, updated_at = NOW()
                                               WHERE assignment_id = {$assignmentId}";
                            $response['item']['updateAssignSql'] = $updateAssignSql;
                            mysqli_query($con, $updateAssignSql);
                        } else {
                            // Create new assignment
                            $insertAssignSql = "INSERT INTO account_assignments
                                               (customer_id, vendor_id, sales_user_id, assigned_date, is_active, created_at)
                                               VALUES ('{$customerIdEsc}', {$vendorIdValue}, '{$salesRepIdEsc}', NOW(), 1, NOW())";
                            $response['item']['insertAssignSql'] = $insertAssignSql;
                            mysqli_query($con, $insertAssignSql);
                        }
                    }

                    // Commit transaction
                    mysqli_commit($con);

                    $response['result'] = true;
                    $response['msg'] = '고객 정보가 수정되었습니다.';

                } catch (Exception $e) {
                    mysqli_rollback($con);
                    $response['result'] = false;

                    // 공통 함수로 에러 메시지 변환
                    $errorMsg = getFriendlyErrorMessage($e->getMessage());

                    $response['error'] = ['msg' => $errorMsg, 'code' => 500];
                }
                Finish();
                break;

            case 'delete_customer':
                $customerId = trim($_POST['customer_id'] ?? '');

                if (empty($customerId)) {
                    $response['result'] = false;
                    $response['error'] = sendError(400, true);
                    Finish();
                }

                $customerIdEsc = escapeString($customerId);

                // Soft delete
                $sql = "UPDATE `customers` SET `is_active` = 0, `updated_at` = NOW()
                        WHERE `customer_id` = '{$customerIdEsc}'";
                $response['item']['sql'] = $sql;

                $result = query($sql);

                if ($result) {
                    $response['result'] = true;
                    $response['msg'] = '고객이 삭제되었습니다.';
                } else {
                    $response['result'] = false;
                    $response['error'] = ['msg' => '삭제에 실패했습니다.', 'code' => 500];
                }
                Finish();
                break;

            case 'get_sales_reps_by_vendor':
                $vendorId = trim($_POST['vendor_id'] ?? '');

                if (empty($vendorId)) {
                    $response['result'] = true;
                    $response['items'] = [];
                    Finish();
                }

                $vendorIdEsc = escapeString($vendorId);

                // Get sales reps assigned to this vendor
                $sql = "SELECT DISTINCT u.user_id, u.name, u.email
                        FROM users u
                        LEFT JOIN roles r ON u.role_id = r.role_id
                        LEFT JOIN account_assignments aa ON u.user_id = aa.sales_user_id AND aa.is_active = 1
                        WHERE u.deleted_at IS NULL
                        AND r.role_name = 'SALES_REP'
                        AND (aa.vendor_id = '{$vendorIdEsc}' OR aa.vendor_id IS NULL)
                        ORDER BY u.name";
                $response['item']['sql'] = $sql;

                $result = query($sql);

                $response['result'] = true;
                $response['items'] = $result ?? [];
                Finish();
                break;

            case 'bulk_update_customers':
                // 일괄 변경: 벤더 또는 영업사원
                $customerIds = isset($_POST['customer_ids']) ? $_POST['customer_ids'] : '';
                $vendorId = isset($_POST['vendor_id']) ? trim($_POST['vendor_id']) : '';
                $salesRepId = isset($_POST['sales_rep_id']) ? trim($_POST['sales_rep_id']) : '';

                if (empty($customerIds)) {
                    $response['result'] = false;
                    $response['error'] = ['msg' => '선택된 고객이 없습니다.', 'code' => 400];
                    Finish();
                }

                // 쉼표로 구분된 고객 ID를 배열로 변환
                $customerIdArray = explode(',', $customerIds);
                $customerIdArray = array_map('trim', $customerIdArray);
                $customerIdArray = array_filter($customerIdArray);

                if (empty($customerIdArray)) {
                    $response['result'] = false;
                    $response['error'] = ['msg' => '유효한 고객 ID가 없습니다.', 'code' => 400];
                    Finish();
                }

                // 변경할 필드 확인
                if (empty($vendorId) && empty($salesRepId)) {
                    $response['result'] = false;
                    $response['error'] = ['msg' => '변경할 벤더 또는 영업사원을 선택하세요.', 'code' => 400];
                    Finish();
                }

                try {
                    // Start transaction
                    mysqli_begin_transaction($con);

                    $affectedCount = 0;

                    // 각 고객에 대해 업데이트
                    foreach ($customerIdArray as $customerId) {
                        $customerIdEsc = escapeString($customerId);

                        // 1. Update vendor_id in customers table if specified
                        if (!empty($vendorId)) {
                            $vendorIdEsc = escapeString($vendorId);
                            $updateCustomerSql = "UPDATE customers
                                                 SET vendor_id = '{$vendorIdEsc}', updated_at = NOW()
                                                 WHERE customer_id = '{$customerIdEsc}' AND is_active = 1";
                            $response['item']["updateCustomerSql_{$customerId}"] = $updateCustomerSql;

                            if (mysqli_query($con, $updateCustomerSql)) {
                                if (mysqli_affected_rows($con) > 0) {
                                    $affectedCount++;
                                }
                            }
                        }

                        // 2. Update sales rep assignment if specified
                        if (!empty($salesRepId)) {
                            $salesRepIdEsc = escapeString($salesRepId);

                            // Get vendor_id for this customer
                            $getVendorSql = "SELECT vendor_id FROM customers WHERE customer_id = '{$customerIdEsc}'";
                            $response['item']["getVendorSql_{$customerId}"] = $getVendorSql;
                            $vendorResult = query($getVendorSql);

                            if (!empty($vendorResult)) {
                                $customerVendorId = $vendorResult[0]['vendor_id'];
                                $vendorIdValue = $customerVendorId ? "'{$customerVendorId}'" : 'NULL';

                                // Deactivate existing assignments for this customer
                                $deactivateSql = "UPDATE account_assignments
                                                 SET is_active = 0, updated_at = NOW()
                                                 WHERE customer_id = '{$customerIdEsc}'";
                                $response['item']["deactivateSql_{$customerId}"] = $deactivateSql;
                                mysqli_query($con, $deactivateSql);

                                // Check if assignment exists
                                $checkAssignSql = "SELECT assignment_id FROM account_assignments
                                                  WHERE customer_id = '{$customerIdEsc}'
                                                  AND sales_user_id = '{$salesRepIdEsc}'
                                                  AND vendor_id = {$vendorIdValue}";
                                $response['item']["checkAssignSql_{$customerId}"] = $checkAssignSql;
                                $assignResult = query($checkAssignSql);

                                if (!empty($assignResult)) {
                                    // Update existing assignment
                                    $assignmentId = $assignResult[0]['assignment_id'];
                                    $updateAssignSql = "UPDATE account_assignments
                                                       SET is_active = 1, updated_at = NOW()
                                                       WHERE assignment_id = {$assignmentId}";
                                    $response['item']["updateAssignSql_{$customerId}"] = $updateAssignSql;
                                    mysqli_query($con, $updateAssignSql);
                                } else {
                                    // Create new assignment
                                    $insertAssignSql = "INSERT INTO account_assignments
                                                       (customer_id, vendor_id, sales_user_id, assigned_date, is_active, created_at)
                                                       VALUES ('{$customerIdEsc}', {$vendorIdValue}, '{$salesRepIdEsc}', NOW(), 1, NOW())";
                                    $response['item']["insertAssignSql_{$customerId}"] = $insertAssignSql;
                                    mysqli_query($con, $insertAssignSql);
                                }
                            }
                        }
                    }

                    // Commit transaction
                    mysqli_commit($con);

                    $response['result'] = true;
                    $response['msg'] = count($customerIdArray) . "명의 고객 정보가 변경되었습니다.";

                } catch (Exception $e) {
                    mysqli_rollback($con);
                    $response['result'] = false;
                    $response['error'] = ['msg' => '일괄 변경에 실패했습니다: ' . $e->getMessage(), 'code' => 500];
                }
                Finish();
                break;

            case 'filter_customers':
                // 필터 파라미터 받기 (_ajax_.php에서 이미 복호화됨)
                $searchKeyword = isset($_POST['search']) ? $_POST['search'] : '';
                $filterVendor = isset($_POST['vendor']) ? $_POST['vendor'] : '';
                $filterPaymentMethod = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';

                // WHERE 조건 구성
                $searchString = "c.is_active = 1";

                if ($searchKeyword) {
                    $searchKeywordEsc = escapeString($searchKeyword);
                    $searchString .= " AND (c.company_name LIKE '%{$searchKeywordEsc}%' OR cu.email LIKE '%{$searchKeywordEsc}%' OR cu.phone LIKE '%{$searchKeywordEsc}%' OR su.name LIKE '%{$searchKeywordEsc}%')";
                }

                if ($filterVendor) {
                    $filterVendorEsc = escapeString($filterVendor);
                    $searchString .= " AND c.vendor_id = '{$filterVendorEsc}'";
                }

                if ($filterPaymentMethod) {
                    $filterPaymentMethodEsc = escapeString($filterPaymentMethod);
                    $searchString .= " AND c.payment_method = '{$filterPaymentMethodEsc}'";
                }

                // Build SELECT query
                $sql = "SELECT c.customer_id, c.company_name as name, cu.email, cu.phone, c.address,
                               c.vendor_id, v.company_name as vendor_name,
                               aa.sales_user_id as sales_rep_id, su.name as sales_rep_name,
                               c.payment_method, c.ceo_name as contact_person, cu.phone as contact_phone,
                               c.created_at
                        FROM customers c
                        LEFT JOIN users cu ON c.user_id = cu.user_id
                        LEFT JOIN vendors v ON c.vendor_id = v.vendor_id
                        LEFT JOIN account_assignments aa ON c.customer_id = aa.customer_id AND aa.is_active = 1
                        LEFT JOIN users su ON aa.sales_user_id = su.user_id
                        WHERE {$searchString}";

                // Pagination config
                $paginationConfig = [
                    'table' => 'customers c',
                    'where' => $searchString,
                    'join' => 'LEFT JOIN users cu ON c.user_id = cu.user_id
                               LEFT JOIN vendors v ON c.vendor_id = v.vendor_id
                               LEFT JOIN account_assignments aa ON c.customer_id = aa.customer_id AND aa.is_active = 1
                               LEFT JOIN users su ON aa.sales_user_id = su.user_id',
                    'orderBy' => 'c.created_at DESC',
                    'rowsPerPage' => $defaultRowsPage,
                    'targetId' => '#customerTableBody',
                    'atValue' => encryptValue('10')
                ];

                $rowsPage = $paginationConfig['rowsPerPage'];
                $p = $_POST['p'] ?? 1;
                $curPage = $rowsPage * ($p - 1);

                $sql .= " ORDER BY c.created_at DESC LIMIT {$curPage}, {$rowsPage}";

                // Debugging SQL
                $response['data']['search']['sql'] = $sql;

                $result = mysqli_query($con, $sql);

                // Generate pagination HTML
                require INC_ROOT . '/common_pagination.php';
                $response['pagination'] = $pagination ?? '';

                // tbody HTML 생성
                $html = '';
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $html .= '<tr>';
                        $html .= '<td><input type="checkbox" class="customer-checkbox" value="' . htmlspecialchars($row['customer_id']) . '"></td>';
                        $html .= '<td>' . htmlspecialchars($row['customer_id']) . '</td>';
                        $html .= '<td><strong>' . htmlspecialchars($row['name']) . '</strong></td>';
                        $html .= '<td>' . htmlspecialchars($row['vendor_name'] ?? '-') . '</td>';
                        $html .= '<td>' . htmlspecialchars($row['sales_rep_name'] ?? '-') . '</td>';
                        $html .= '<td>';
                        $paymentLabels = [
                            'CMS' => ['label' => 'CMS', 'class' => 'badge-payment-cms'],
                            'CARD' => ['label' => '카드', 'class' => 'badge-payment-card'],
                            'TRANSFER' => ['label' => '계좌이체', 'class' => 'badge-payment-transfer'],
                            'ONE_TIME' => ['label' => '일시불', 'class' => 'badge-payment-onetime']
                        ];
                        $method = $row['payment_method'] ?? 'ONE_TIME';
                        $payment = $paymentLabels[$method] ?? ['label' => $method, 'class' => 'badge-payment-onetime'];
                        $html .= '<span class="badge ' . $payment['class'] . '">' . $payment['label'] . '</span>';
                        $html .= '</td>';
                        $html .= '<td>' . htmlspecialchars($row['phone'] ?? '-') . '</td>';
                        $html .= '<td>' . htmlspecialchars($row['email'] ?? '-') . '</td>';
                        $html .= '<td>' . date('Y-m-d', strtotime($row['created_at'])) . '</td>';
                        $html .= '<td>';
                        $html .= '<button class="btn-sm" onclick="editCustomer(\'' . htmlspecialchars($row['customer_id']) . '\')">수정</button>';
                        $html .= '<button class="btn-sm btn-danger" onclick="deleteCustomer(\'' . htmlspecialchars($row['customer_id']) . '\', \'' . htmlspecialchars($row['name']) . '\')">삭제</button>';
                        $html .= '</td>';
                        $html .= '</tr>';
                    }
                } else {
                    $html = '<tr><td colspan="10" class="table-empty-state">조회된 고객이 없습니다.</td></tr>';
                }

                $response['result'] = true;
                $response['html'] = $html;
                Finish();

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

// 검색 파라미터
// 필터 파라미터 (GET 또는 POST에서 받음)
$searchKeyword = isset($_POST['search']) ? $_POST['search'] : (isset($_GET['search']) ? $_GET['search'] : '');
$filterVendor = isset($_POST['vendor']) ? $_POST['vendor'] : (isset($_GET['vendor']) ? $_GET['vendor'] : '');
$filterPaymentMethod = isset($_POST['payment_method']) ? $_POST['payment_method'] : (isset($_GET['payment_method']) ? $_GET['payment_method'] : '');

// 고객 목록 조회
$sql = "SELECT c.customer_id, c.company_name as name, cu.email, cu.phone, c.address,
               c.vendor_id, v.company_name as vendor_name,
               aa.sales_user_id as sales_rep_id, su.name as sales_rep_name,
               c.payment_method, c.ceo_name as contact_person, cu.phone as contact_phone,
               c.created_at
        FROM customers c
        LEFT JOIN users cu ON c.user_id = cu.user_id
        LEFT JOIN vendors v ON c.vendor_id = v.vendor_id
        LEFT JOIN account_assignments aa ON c.customer_id = aa.customer_id AND aa.is_active = 1
        LEFT JOIN users su ON aa.sales_user_id = su.user_id
        WHERE c.is_active = 1";

if ($searchKeyword) {
    $searchKeywordEsc = mysqli_real_escape_string($con, $searchKeyword);
    $sql .= " AND (c.company_name LIKE '%{$searchKeywordEsc}%' OR cu.email LIKE '%{$searchKeywordEsc}%' OR cu.phone LIKE '%{$searchKeywordEsc}%' OR su.name LIKE '%{$searchKeywordEsc}%')";
}

if ($filterVendor) {
    $filterVendorEsc = mysqli_real_escape_string($con, $filterVendor);
    $sql .= " AND c.vendor_id = '{$filterVendorEsc}'";
}

if ($filterPaymentMethod) {
    $filterPaymentMethodEsc = mysqli_real_escape_string($con, $filterPaymentMethod);
    $sql .= " AND c.payment_method = '{$filterPaymentMethodEsc}'";
}

$sql .= " ORDER BY c.created_at DESC LIMIT 100";
$response['item']['sql'] = $sql;

$result = mysqli_query($con, $sql);

$customers = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $customers[] = $row;
    }
}

// 벤더 목록 (필터용)
$vendorListSql = "SELECT vendor_id, company_name as name FROM vendors WHERE is_active = 1 ORDER BY company_name";
$response['item']['vendorListSql'] = $vendorListSql;
$vendorListResult = mysqli_query($con, $vendorListSql);
$vendorList = [];
while ($row = mysqli_fetch_assoc($vendorListResult)) {
    $vendorList[] = $row;
}

// 영업사원 목록 (폼용)
$salesRepSql = "SELECT u.user_id, u.name
                FROM users u
                INNER JOIN roles r ON u.role_id = r.role_id
                WHERE u.is_active = 1 AND r.code = 'SALES'
                ORDER BY u.name";
$response['item']['salesRepSql'] = $salesRepSql;
$salesRepResult = mysqli_query($con, $salesRepSql);
$salesRepList = [];
if ($salesRepResult) {
    while ($row = mysqli_fetch_assoc($salesRepResult)) {
        $salesRepList[] = $row;
    }
}
?>


<section class="card">
    <!-- 필터 -->
    <div class="card-hd card-hd-wrap">
      <div class="card-hd-content">
        <div class="card-hd-title-area">
          <div class="card-ttl">고객 목록</div>
          <div class="card-sub">고객 정보 및 결제방식 관리</div>
        </div>
        <div class="filter-toolbar">
          <div class="filter-group">
            <label>벤더</label>
            <select id="filterVendor" name="vendor" class="form-control input-w-180">
              <option value="">전체 벤더</option>
              <?php foreach ($vendorList as $v): ?>
              <option value="<?php echo htmlspecialchars($v['vendor_id']); ?>" <?php echo $filterVendor === $v['vendor_id'] ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($v['name']); ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="filter-group">
            <label>결제방식</label>
            <select id="filterPaymentMethod" name="payment_method" class="form-control filter-select">
              <option value="">전체</option>
              <option value="CMS" <?php echo $filterPaymentMethod === 'CMS' ? 'selected' : ''; ?>>CMS</option>
              <option value="CARD" <?php echo $filterPaymentMethod === 'CARD' ? 'selected' : ''; ?>>카드</option>
              <option value="TRANSFER" <?php echo $filterPaymentMethod === 'TRANSFER' ? 'selected' : ''; ?>>계좌이체</option>
              <option value="ONE_TIME" <?php echo $filterPaymentMethod === 'ONE_TIME' ? 'selected' : ''; ?>>일시불</option>
            </select>
          </div>
          <div class="filter-group">
            <label>검색</label>
            <input type="text" id="searchCustomer" name="search" class="form-control filter-input" placeholder="회사명/담당영업사원/이메일/전화번호" value="<?php echo htmlspecialchars($searchKeyword); ?>" onkeypress="if(event.key==='Enter') filterCustomers()">
          </div>
          <button id="btnFilter" class="btn primary" onclick="filterCustomers()">조회</button>
          <button id="btnAddCustomer" class="btn primary" onclick="openAddCustomerModal()">고객 추가</button>
        </div>
      </div>
      <div class="row">
        <button id="btnExportCsv" class="btn" onclick="exportCustomersToCsv()">CSV 내보내기</button>
        <button id="btnBulkUpdate" class="btn primary" onclick="openBulkUpdateModal()">일괄 변경</button>
      </div>
    </div>

    <div class="card-bd card-bd-padding">
      <div class="table-wrap">
        <table class="tbl-list" id="tblCustomers">
          <thead>
            <tr>
              <th><input type="checkbox" id="checkAll" onclick="toggleAllCheckboxes(this)"></th>
              <th>고객ID</th>
              <th>고객명</th>
              <th>담당벤더</th>
              <th>담당영업사원</th>
              <th>결제방식</th>
              <th>연락처</th>
              <th>이메일</th>
              <th>등록일</th>
              <th>관리</th>
            </tr>
          </thead>
          <tbody id="customerTableBody">
          </tbody>
        </table>
      </div>

      <!-- 페이징 영역 -->
      <div class="paging" data-id="#customerTableBody"></div>
    </div>
</section>


<!-- 고객 추가/수정 모달 -->
<div id="customerModal" class="modal">
  <div class="modal-content" class="modal-content-lg">
    <div class="modal-header">
      <h3 id="modalTitle">고객 추가</h3>
      <span class="close" onclick="closeCustomerModal()">&times;</span>
    </div>
    <div class="modal-body">
      <form id="frmCustomer" onsubmit="return false;">
        <input type="hidden" id="customerId" name="customer_id">
        <input type="hidden" id="modalMode" value="add">

        <div class="modal-form-grid">
          <!-- 왼쪽 컬럼: 기본 정보 -->
          <div>
            <h4 class="section-header">기본 정보</h4>

            <div class="form-group">
              <label for="name">고객명 <span class="required-mark">*</span></label>
              <input type="text" id="name" name="name" class="form-control" required>
            </div>

            <div class="form-group">
              <label for="email">이메일</label>
              <input type="email" id="email" name="email" class="form-control">
            </div>

            <div class="form-group">
              <label for="phone">전화번호</label>
              <input type="tel" id="phone" name="phone" class="form-control" placeholder="010-0000-0000">
            </div>

            <div class="form-group">
              <label for="password">비밀번호</label>
              <input type="password" id="password" name="password" class="form-control" placeholder="변경하려면 입력하세요">
              <small class="help-text">비밀번호를 변경하지 않으려면 비워두세요</small>
            </div>

            <div class="form-group">
              <label for="address">주소</label>
              <textarea id="address" name="address" class="form-control" rows="2"></textarea>
            </div>

            <div class="form-group">
              <label for="vendorId">담당 벤더</label>
              <select id="vendorId" name="vendor_id" class="form-control" onchange="loadSalesRepsByVendor(this.value)">
                <option value="">벤더 선택</option>
                <?php foreach ($vendorList as $v): ?>
                <option value="<?php echo htmlspecialchars($v['vendor_id']); ?>">
                  <?php echo htmlspecialchars($v['name']); ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label for="salesRepId">담당 영업사원</label>
              <select id="salesRepId" name="sales_rep_id" class="form-control">
                <option value="">영업사원 선택</option>
                <?php foreach ($salesRepList as $rep): ?>
                <option value="<?php echo htmlspecialchars($rep['user_id']); ?>">
                  <?php echo htmlspecialchars($rep['name']); ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>

            <h4 class="section-header-spaced">담당자 정보</h4>

            <div class="form-group">
              <label for="contactPerson">담당자명</label>
              <input type="text" id="contactPerson" name="contact_person" class="form-control">
            </div>

            <div class="form-group">
              <label for="contactPhone">담당자 전화번호</label>
              <input type="tel" id="contactPhone" name="contact_phone" class="form-control">
            </div>

            <div class="form-group">
              <label for="contactEmail">담당자 이메일</label>
              <input type="email" id="contactEmail" name="contact_email" class="form-control">
            </div>
          </div>

          <!-- 오른쪽 컬럼: 결제 정보 -->
          <div>
            <h4 class="section-header">결제 정보</h4>

            <div class="form-group">
              <label for="paymentMethod">결제방식 <span class="required-mark">*</span></label>
              <select id="paymentMethod" name="payment_method" class="form-control" required onchange="toggleCmsFields()">
                <option value="">선택하세요</option>
                <option value="CMS">CMS (정기구독)</option>
                <option value="CARD">카드 결제</option>
                <option value="TRANSFER">계좌이체</option>
                <option value="ONE_TIME">일시불</option>
              </select>
              <small class="help-text">CMS 선택 시 은행 정보가 필수입니다</small>
            </div>

            <div id="cmsFieldsGroup" class="cms-fields-group">
              <div class="form-group">
                <label for="cmsBankName">은행명 <span class="required-mark">*</span></label>
                <input type="text" id="cmsBankName" name="cms_bank_name" class="form-control" placeholder="예: 신한은행">
              </div>

              <div class="form-group">
                <label for="cmsAccountNumber">계좌번호 <span class="required-mark">*</span></label>
                <input type="text" id="cmsAccountNumber" name="cms_account_number" class="form-control" placeholder="숫자만 입력">
              </div>

              <div class="form-group">
                <label for="cmsAccountHolder">예금주 <span class="required-mark">*</span></label>
                <input type="text" id="cmsAccountHolder" name="cms_account_holder" class="form-control">
              </div>
            </div>

            <div class="form-group" class="form-group-spaced">
              <label for="notes">비고</label>
              <textarea id="notes" name="notes" class="form-control" rows="6"></textarea>
            </div>
          </div>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn" onclick="closeCustomerModal()">취소</button>
      <button type="button" class="btn primary" onclick="saveCustomer()">저장</button>
    </div>
  </div>
</div>

<!-- 일괄 변경 모달 -->
<div id="bulkUpdateModal" class="modal">
  <div class="modal-content" class="modal-content-md">
    <div class="modal-header">
      <h2>고객 일괄 변경</h2>
      <span class="close" onclick="closeBulkUpdateModal()">&times;</span>
    </div>
    <div class="modal-body">
      <p id="bulkUpdateInfo" class="modal-info">선택된 고객: <strong id="selectedCount">0</strong>명</p>

      <form id="bulkUpdateForm" onsubmit="return false;">
        <div class="form-group">
          <label for="bulkVendor">벤더 변경</label>
          <select id="bulkVendor" name="vendor_id" class="form-control" onchange="loadBulkSalesRepsByVendor(this.value)">
            <option value="">변경 안 함</option>
            <?php foreach ($vendorList as $vendor): ?>
              <option value="<?= htmlspecialchars($vendor['vendor_id']) ?>">
                <?= htmlspecialchars($vendor['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="bulkSalesRep">담당 영업사원 변경</label>
          <select id="bulkSalesRep" name="sales_rep_id" class="form-control">
            <option value="">변경 안 함</option>
            <?php foreach ($salesRepList as $rep): ?>
              <option value="<?= htmlspecialchars($rep['user_id']) ?>">
                <?= htmlspecialchars($rep['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn" onclick="closeBulkUpdateModal()">취소</button>
      <button type="button" class="btn primary" onclick="saveBulkUpdate()">변경</button>
    </div>
  </div>
</div>

<script>
// 페이지 이름 (AJAX 호출용)
window.pageName = '<?= $pageName ?>';

// 필터 조회
window.filterCustomers = function() {
  const vendor = document.getElementById('filterVendor').value || '';
  const paymentMethod = document.getElementById('filterPaymentMethod').value || '';
  const search = document.getElementById('searchCustomer').value || '';

  const data = {};
  data['<?= encryptValue('action') ?>'] = 'filter_customers';
  data['<?= encryptValue('vendor') ?>'] = vendor;
  data['<?= encryptValue('payment_method') ?>'] = paymentMethod;
  data['<?= encryptValue('search') ?>'] = search;

  updateAjaxContent(data, function(response) {
    if (response.result && response.html) {
      const tbody = document.querySelector('#customerTableBody');
      if (tbody) {
        tbody.innerHTML = response.html;

        // 테이블 리렌더링 후 스타일 재적용
        const table = document.querySelector('#tblCustomers');
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
        const pagingContainer = document.querySelector('.paging[data-id="#customerTableBody"]');
        if (pagingContainer) {
          pagingContainer.innerHTML = response.pagination;
        }
      }
    } else {
      alert('조회에 실패했습니다.');
    }
  });
};

// CSV 내보내기
window.exportCustomersToCsv = function() {
  const table = document.getElementById('tblCustomers');
  const rows = Array.from(table.querySelectorAll('thead tr, tbody tr'));

  const csv = rows.map(row => {
    const cells = Array.from(row.querySelectorAll('th, td'));
    return cells.slice(0, -1).map(cell => {
      const badge = cell.querySelector('.badge');
      if (badge) return '"' + badge.textContent.trim() + '"';
      return '"' + cell.textContent.trim().replace(/"/g, '""') + '"';
    }).join(',');
  }).join('\n');

  const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = 'HQ_고객목록_' + new Date().toISOString().slice(0,10) + '.csv';
  link.click();
};

// 고객 추가 모달 열기
window.openAddCustomerModal = function() {
  openCustomerModal('add');
};

// 모달 열기
window.openCustomerModal = function(mode, customerId = null) {
  const modal = document.getElementById('customerModal');
  const modalTitle = document.getElementById('modalTitle');
  const modalMode = document.getElementById('modalMode');

  modalMode.value = mode;
  document.getElementById('frmCustomer').reset();
  document.getElementById('customerId').value = '';
  document.getElementById('cmsFieldsGroup').style.display = 'none';

  if (mode === 'add') {
    modalTitle.textContent = '고객 추가';
  } else {
    modalTitle.textContent = '고객 수정';
    loadCustomerData(customerId);
  }

  modal.style.display = 'block';
}

// 모달 닫기
window.closeCustomerModal = function() {
  document.getElementById('customerModal').style.display = 'none';
}

// CMS 필드 토글
window.toggleCmsFields = function() {
  const paymentMethod = document.getElementById('paymentMethod').value;
  const cmsFieldsGroup = document.getElementById('cmsFieldsGroup');
  const cmsBankName = document.getElementById('cmsBankName');
  const cmsAccountNumber = document.getElementById('cmsAccountNumber');
  const cmsAccountHolder = document.getElementById('cmsAccountHolder');

  if (paymentMethod === 'CMS') {
    cmsFieldsGroup.style.display = 'block';
    cmsBankName.required = true;
    cmsAccountNumber.required = true;
    cmsAccountHolder.required = true;
  } else {
    cmsFieldsGroup.style.display = 'none';
    cmsBankName.required = false;
    cmsAccountNumber.required = false;
    cmsAccountHolder.required = false;
  }
}

// 벤더 선택 시 영업사원 목록 로드
window.loadSalesRepsByVendor = function(vendorId) {
  const salesRepSelect = document.getElementById('salesRepId');

  // 초기화
  salesRepSelect.innerHTML = '<option value="">영업사원 선택</option>';

  if (!vendorId) {
    return;
  }

  const data = {};
  data['<?= encryptValue('action') ?>'] = 'get_sales_reps_by_vendor';
  data['<?= encryptValue('vendor_id') ?>'] = vendorId;

  updateAjaxContent(data, function(response) {
    if (response.result && response.items) {
      response.items.forEach(function(rep) {
        const option = document.createElement('option');
        option.value = rep.user_id;
        option.textContent = rep.name;
        salesRepSelect.appendChild(option);
      });
    }
  }, false);
}

// 고객 수정
window.editCustomer = function(customerId) {
  openCustomerModal('edit', customerId);
}

// 고객 데이터 로드
window.loadCustomerData = function(customerId) {
  const data = {};
  data['<?= encryptValue('action') ?>'] = 'get_customer';
  data['<?= encryptValue('customer_id') ?>'] = customerId;

  updateAjaxContent(data, function(response) {
    if (response.result && response.item) {
      const customer = response.item;
      document.getElementById('customerId').value = customer.customer_id;
      document.getElementById('name').value = customer.name;
      document.getElementById('email').value = customer.email || '';
      document.getElementById('phone').value = customer.phone || '';
      document.getElementById('password').value = ''; // 비밀번호 필드는 항상 비움
      document.getElementById('address').value = customer.address || '';
      document.getElementById('vendorId').value = customer.vendor_id || '';
      document.getElementById('paymentMethod').value = customer.payment_method || '';
      document.getElementById('cmsBankName').value = customer.bank_name || '';
      document.getElementById('cmsAccountNumber').value = customer.account_number || '';
      document.getElementById('cmsAccountHolder').value = customer.cms_account_holder || '';
      document.getElementById('contactPerson').value = customer.contact_person || '';
      document.getElementById('contactPhone').value = customer.contact_phone || '';
      document.getElementById('contactEmail').value = customer.contact_email || '';
      document.getElementById('notes').value = customer.notes || '';

      // 벤더에 따라 영업사원 목록 로드 후 선택
      if (customer.vendor_id) {
        loadSalesRepsByVendor(customer.vendor_id);
        // 영업사원 목록 로드 후 선택 (약간의 지연)
        setTimeout(function() {
          document.getElementById('salesRepId').value = customer.sales_rep_id || '';
        }, 300);
      } else {
        document.getElementById('salesRepId').innerHTML = '<option value="">영업사원 선택</option>';
        document.getElementById('salesRepId').value = '';
      }

      toggleCmsFields();
    } else {
      alert(response.error?.msg || '고객 정보를 불러올 수 없습니다.');
    }
  }, false);
}

// 고객 저장
window.saveCustomer = function() {
  const form = document.getElementById('frmCustomer');

  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  const formData = new FormData(form);
  const data = {};

  const mode = document.getElementById('modalMode').value;
  data['<?= encryptValue('action') ?>'] = mode === 'add' ? 'add_customer' : 'update_customer';

  // Pre-encrypted field names mapping
  const fieldMap = {
    'customer_id': '<?= encryptValue('customer_id') ?>',
    'name': '<?= encryptValue('name') ?>',
    'email': '<?= encryptValue('email') ?>',
    'phone': '<?= encryptValue('phone') ?>',
    'address': '<?= encryptValue('address') ?>',
    'vendor_id': '<?= encryptValue('vendor_id') ?>',
    'sales_rep_id': '<?= encryptValue('sales_rep_id') ?>',
    'payment_method': '<?= encryptValue('payment_method') ?>',
    'cms_bank_name': '<?= encryptValue('cms_bank_name') ?>',
    'cms_account_number': '<?= encryptValue('cms_account_number') ?>',
    'cms_account_holder': '<?= encryptValue('cms_account_holder') ?>',
    'contact_person': '<?= encryptValue('contact_person') ?>',
    'contact_phone': '<?= encryptValue('contact_phone') ?>',
    'contact_email': '<?= encryptValue('contact_email') ?>',
    'notes': '<?= encryptValue('notes') ?>'
  };

  for (let [key, value] of formData.entries()) {
    if (fieldMap[key]) {
      data[fieldMap[key]] = value;
    }
  }

  updateAjaxContent(data, function(response) {
    if (response.result) {
      toast(response.msg || '저장되었습니다.');
      closeCustomerModal();
      // 현재 탭 리로드
      const activeTab = document.querySelector('.tab-btn-inline.active');
      if (activeTab) {
        const token = activeTab.getAttribute('data-token');
        loadCustomerTab(activeTab, token);
      }
    } else {
      alert(response.error?.msg || '저장에 실패했습니다.');
    }
  }, false);
}

// Toast 알림 함수
window.toast = function(msg) {
  const div = document.createElement('div');
  div.className = 'toast';
  div.textContent = msg;
  div.style.cssText = 'position:fixed; top:20px; right:20px; background:var(--accent); color:#fff; padding:12px 16px; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.15); z-index:10000;';
  document.body.appendChild(div);
  setTimeout(() => div.remove(), 1800);
}

// 고객 삭제
window.deleteCustomer = function(customerId, name) {
  if (!confirm(`고객 "${name}"을(를) 삭제하시겠습니까?`)) {
    return;
  }

  const data = {};
  data['<?= encryptValue('action') ?>'] = 'delete_customer';
  data['<?= encryptValue('customer_id') ?>'] = customerId;

  updateAjaxContent(data, function(response) {
    if (response.result) {
      toast(response.msg || '삭제되었습니다.');
      // 현재 탭 리로드
      loadCustomerTab(document.querySelector('.tab-btn-inline.active'), 'customer_list');
    } else {
      alert(response.error?.msg || '삭제에 실패했습니다.');
    }
  }, false);
}

// 전체 체크박스 토글
window.toggleAllCheckboxes = function(checkbox) {
  const checkboxes = document.querySelectorAll('.customer-checkbox');
  checkboxes.forEach(cb => {
    cb.checked = checkbox.checked;
  });
}

// 일괄 변경 모달 열기
window.openBulkUpdateModal = function() {
  const checkedBoxes = document.querySelectorAll('.customer-checkbox:checked');

  if (checkedBoxes.length === 0) {
    alert('변경할 고객을 선택해주세요.');
    return;
  }

  // 선택된 개수 표시
  document.getElementById('selectedCount').textContent = checkedBoxes.length;

  // 폼 초기화
  document.getElementById('bulkUpdateForm').reset();

  // 모달 열기
  document.getElementById('bulkUpdateModal').style.display = 'block';
}

// 일괄 변경 모달 닫기
window.closeBulkUpdateModal = function() {
  document.getElementById('bulkUpdateModal').style.display = 'none';
}

// 일괄 변경 모달 - 벤더 선택 시 영업사원 목록 로드
window.loadBulkSalesRepsByVendor = function(vendorId) {
  const salesRepSelect = document.getElementById('bulkSalesRep');

  // 초기화
  salesRepSelect.innerHTML = '<option value="">변경 안 함</option>';

  if (!vendorId) {
    return;
  }

  const data = {};
  data['<?= encryptValue('action') ?>'] = 'get_sales_reps_by_vendor';
  data['<?= encryptValue('vendor_id') ?>'] = vendorId;

  updateAjaxContent(data, function(response) {
    if (response.result && response.items) {
      response.items.forEach(function(rep) {
        const option = document.createElement('option');
        option.value = rep.user_id;
        option.textContent = rep.name;
        salesRepSelect.appendChild(option);
      });
    }
  }, false);
}

// 일괄 변경 저장
window.saveBulkUpdate = function() {
  const vendorId = document.getElementById('bulkVendor').value;
  const salesRepId = document.getElementById('bulkSalesRep').value;

  // 최소 하나는 선택되어야 함
  if (!vendorId && !salesRepId) {
    alert('벤더 또는 영업사원 중 최소 하나를 선택해주세요.');
    return;
  }

  // 체크된 고객 ID 수집
  const checkedBoxes = document.querySelectorAll('.customer-checkbox:checked');
  const customerIds = Array.from(checkedBoxes).map(cb => cb.value).join(',');

  if (!customerIds) {
    alert('선택된 고객이 없습니다.');
    return;
  }

  const data = {};
  data['<?= encryptValue('action') ?>'] = 'bulk_update_customers';
  data['<?= encryptValue('customer_ids') ?>'] = customerIds;
  if (vendorId) {
    data['<?= encryptValue('vendor_id') ?>'] = vendorId;
  }
  if (salesRepId) {
    data['<?= encryptValue('sales_rep_id') ?>'] = salesRepId;
  }

  updateAjaxContent(data, function(response) {
    if (response.result) {
      toast(response.msg || '변경되었습니다.');
      closeBulkUpdateModal();

      // 체크박스 해제
      document.getElementById('checkAll').checked = false;
      document.querySelectorAll('.customer-checkbox').forEach(cb => cb.checked = false);

      // 목록 새로고침
      filterCustomers();
    } else {
      alert(response.error?.msg || '변경에 실패했습니다.');
    }
  }, false);
}

// 모달 외부 클릭 처리
document.getElementById('customerModal').onclick = function(e) {
  if (e.target === this) {
    closeCustomerModal();
  }
};

document.getElementById('bulkUpdateModal').onclick = function(e) {
  if (e.target === this) {
    closeBulkUpdateModal();
  }
};

// ESC 키로 모달 닫기
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    if (document.getElementById('customerModal').style.display === 'block') {
      closeCustomerModal();
    }
    if (document.getElementById('bulkUpdateModal').style.display === 'block') {
      closeBulkUpdateModal();
    }
  }
});

// 페이지 로드 시 자동 조회
filterCustomers();
</script>
