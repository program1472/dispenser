<?php
/**
 * HQ 악세사리 제품 관리 - AJAX 처리
 */

// 액션 추출
$action = '';
foreach ($_POST as $key => $value) {
    $decrypted = decryptValue($key);
    if (strpos($decrypted, 'action') !== false) {
        $action = $value;
        break;
    }
}

try {
    switch ($action) {
        case 'filter_accessories':
            $categoryId = '';
            $status = '';
            $keyword = '';

            foreach ($_POST as $key => $value) {
                $decrypted = decryptValue($key);
                if (strpos($decrypted, 'category_id') !== false) {
                    $categoryId = $value ? intval($value) : '';
                } elseif (strpos($decrypted, 'status') !== false) {
                    $status = $value !== '' ? intval($value) : '';
                } elseif (strpos($decrypted, 'keyword') !== false) {
                    $keyword = mysqli_real_escape_string($con, $value);
                }
            }

            // WHERE 조건 구성
            $searchString = "a.deleted_at IS NULL";
            if ($categoryId) $searchString .= " AND a.category_id = {$categoryId}";
            if ($status !== '') $searchString .= " AND a.is_active = {$status}";
            if ($keyword) $searchString .= " AND a.accessory_name LIKE '%{$keyword}%'";

            // 페이징 설정
            $paginationConfig = [
                'table' => 'accessories a',
                'where' => $searchString,
                'join' => 'LEFT JOIN categories c ON a.category_id = c.category_id LEFT JOIN devices d ON a.device_id = d.device_id AND d.deleted_at IS NULL',
                'orderBy' => 'a.created_at DESC',
                'rowsPerPage' => $defaultRowsPage,
                'targetId' => '#accessoryTableBody',
                'atValue' => encryptValue('10')
            ];

            // 페이징 처리
            $rowsPage = $paginationConfig['rowsPerPage'];
            $p = $_POST['p'] ?? 1;
            $curPage = $rowsPage * ($p - 1);

            $sql = "SELECT a.*, c.category_name, d.model_name as device_name
                    FROM accessories a
                    LEFT JOIN categories c ON a.category_id = c.category_id
                    LEFT JOIN devices d ON a.device_id = d.device_id AND d.deleted_at IS NULL
                    WHERE {$searchString}
                    ORDER BY a.created_at DESC
                    LIMIT {$curPage}, {$rowsPage}";

            $response['item']['sql'] = $sql;
            $result = mysqli_query($con, $sql);

            // 페이징 HTML 생성
            require INC_ROOT . '/common_pagination.php';
            $response['pagination'] = $pagination ?? '';

            $statusLabels = ['1' => '활성', '0' => '비활성'];
            $statusBadges = ['1' => 'badge-status-active', '0' => 'badge-status-inactive'];

            $html = '';
            $count = 0;
            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $count++;
                    $statusKey = $row['is_active'] ?? '1';
                    $html .= '<tr data-accessory-id="' . $row['accessory_id'] . '" data-category="' . $row['category_id'] . '" data-status="' . $statusKey . '">
                              <td><strong>' . $row['accessory_id'] . '</strong></td>
                              <td>' . htmlspecialchars($row['accessory_name']) . '</td>
                              <td>' . htmlspecialchars($row['category_name'] ?? '-') . '</td>
                              <td>' . htmlspecialchars($row['device_name'] ?? '공용') . '</td>
                              <td><strong>₩' . number_format($row['price']) . '</strong></td>
                              <td>' . number_format($row['stock_quantity']) . '</td>
                              <td><span class="badge ' . $statusBadges[$statusKey] . '">' . $statusLabels[$statusKey] . '</span></td>
                              <td>' . date('Y-m-d', strtotime($row['created_at'])) . '</td>
                              <td><button class="btn-sm" onclick="editAccessory(' . $row['accessory_id'] . ')">수정</button></td>
                            </tr>';
                }
            }

            if ($count == 0) {
                $html = '<tr><td colspan="9" class="table-empty-state">조회된 악세사리가 없습니다.</td></tr>';
            }

            $response['result'] = true;
            $response['html'] = $html;
            $response['item']['count'] = $count;
            break;

        case 'get_accessory':
            $accessoryId = null;
            foreach ($_POST as $key => $value) {
                if (strpos(decryptValue($key), 'accessory_id') !== false) {
                    $accessoryId = intval($value);
                    break;
                }
            }

            $sql = "SELECT * FROM accessories WHERE accessory_id = {$accessoryId} AND deleted_at IS NULL";
            $response['item']['sql'] = $sql;
            $result = mysqli_query($con, $sql);

            if ($result && $row = mysqli_fetch_assoc($result)) {
                $response['result'] = true;
                $response['item']['accessory'] = $row;
            } else {
                throw new Exception('악세사리 정보를 찾을 수 없습니다.');
            }
            break;

        case 'save_accessory':
            $accessoryId = null;
            $accessoryName = '';
            $categoryId = null;
            $deviceId = null;
            $price = 0;
            $stockQuantity = 0;
            $imageUrl = '';
            $description = '';
            $isActive = 1;
            $userId = $_SESSION['user_id'] ?? 1;

            foreach ($_POST as $key => $value) {
                $decrypted = decryptValue($key);

                if (strpos($decrypted, 'accessory_id') !== false && $value) {
                    $accessoryId = intval($value);
                } elseif (strpos($decrypted, 'accessory_name') !== false) {
                    $accessoryName = mysqli_real_escape_string($con, $value);
                } elseif (strpos($decrypted, 'category_id') !== false) {
                    $categoryId = $value ? intval($value) : null;
                } elseif (strpos($decrypted, 'device_id') !== false) {
                    $deviceId = $value ? intval($value) : null;
                } elseif (strpos($decrypted, 'price') !== false) {
                    $price = floatval($value);
                } elseif (strpos($decrypted, 'stock_quantity') !== false) {
                    $stockQuantity = intval($value);
                } elseif (strpos($decrypted, 'image_url') !== false) {
                    $imageUrl = mysqli_real_escape_string($con, $value);
                } elseif (strpos($decrypted, 'description') !== false) {
                    $description = mysqli_real_escape_string($con, $value);
                } elseif (strpos($decrypted, 'is_active') !== false) {
                    $isActive = intval($value);
                }
            }

            if (empty($accessoryName)) {
                throw new Exception('악세사리명을 입력해주세요.');
            }

            $categoryIdSql = $categoryId ? $categoryId : 'NULL';
            $deviceIdSql = $deviceId ? $deviceId : 'NULL';

            if ($accessoryId) {
                $sql = "UPDATE accessories SET
                            accessory_name = '{$accessoryName}',
                            category_id = {$categoryIdSql},
                            device_id = {$deviceIdSql},
                            price = {$price},
                            stock_quantity = {$stockQuantity},
                            image_url = '{$imageUrl}',
                            description = '{$description}',
                            is_active = {$isActive},
                            updated_by = {$userId},
                            updated_at = NOW()
                        WHERE accessory_id = {$accessoryId}";
                $msg = '악세사리가 수정되었습니다.';
            } else {
                $sql = "INSERT INTO accessories (
                            accessory_name, category_id, device_id, price, stock_quantity,
                            image_url, description, is_active, created_by, created_at
                        ) VALUES (
                            '{$accessoryName}', {$categoryIdSql}, {$deviceIdSql}, {$price}, {$stockQuantity},
                            '{$imageUrl}', '{$description}', {$isActive}, {$userId}, NOW()
                        )";
                $msg = '악세사리가 등록되었습니다.';
            }

            $response['item']['sql'] = $sql;

            if (mysqli_query($con, $sql)) {
                $response['result'] = true;
                $response['msg'] = $msg;
                if (!$accessoryId) {
                    $response['item']['accessory_id'] = mysqli_insert_id($con);
                }
            } else {
                throw new Exception('저장에 실패했습니다: ' . mysqli_error($con));
            }
            break;

        default:
            throw new Exception('알 수 없는 액션입니다.');
    }
} catch (Exception $e) {
    $response['result'] = false;

    // 공통 함수로 에러 메시지 변환
    $errorMsg = getFriendlyErrorMessage($e->getMessage());

    $response['error'] = ['msg' => $errorMsg];
}

Finish();
