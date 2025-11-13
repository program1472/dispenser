<?php
/**
 * HQ 부자재 제품 관리 - AJAX 처리
 */

$action = '';
foreach ($_POST as $key => $value) {
    if (strpos(decryptValue($key), 'action') !== false) {
        $action = $value;
        break;
    }
}

try {
    switch ($action) {
        case 'filter_parts':
            // 필터링된 부자재 목록 조회
            $categoryId = '';
            $warrantyType = '';
            $status = '';
            $keyword = '';

            foreach ($_POST as $key => $value) {
                $decrypted = decryptValue($key);
                if (strpos($decrypted, 'category_id') !== false) {
                    $categoryId = $value ? intval($value) : '';
                } elseif (strpos($decrypted, 'warranty_type') !== false) {
                    $warrantyType = mysqli_real_escape_string($con, $value);
                } elseif (strpos($decrypted, 'status') !== false) {
                    $status = $value !== '' ? intval($value) : '';
                } elseif (strpos($decrypted, 'keyword') !== false) {
                    $keyword = mysqli_real_escape_string($con, $value);
                }
            }

            // WHERE 조건 구성
            $searchString = "p.deleted_at IS NULL";
            if ($categoryId) {
                $searchString .= " AND p.category_id = {$categoryId}";
            }
            if ($warrantyType) {
                $searchString .= " AND p.warranty_type = '{$warrantyType}'";
            }
            if ($status !== '') {
                $searchString .= " AND p.is_active = {$status}";
            }
            if ($keyword) {
                $searchString .= " AND p.part_name LIKE '%{$keyword}%'";
            }

            // 페이징 설정
            $paginationConfig = [
                'table' => 'parts p',
                'where' => $searchString,
                'join' => 'LEFT JOIN categories c ON p.category_id = c.category_id LEFT JOIN devices d ON p.compatible_device_id = d.device_id AND d.deleted_at IS NULL',
                'orderBy' => 'p.created_at DESC, p.part_id DESC',
                'rowsPerPage' => $defaultRowsPage,
                'targetId' => '#partTableBody',
                'atValue' => encryptValue('10')
            ];

            // 페이징 처리
            $rowsPage = $paginationConfig['rowsPerPage'];
            $p = $_POST['p'] ?? 1;
            $curPage = $rowsPage * ($p - 1);

            $sql = "SELECT
                      p.*,
                      c.category_name,
                      d.model_name as compatible_device_name,
                      creator.name as creator_name,
                      updater.name as updater_name
                    FROM parts p
                    LEFT JOIN categories c ON p.category_id = c.category_id
                    LEFT JOIN devices d ON p.compatible_device_id = d.device_id AND d.deleted_at IS NULL
                    LEFT JOIN users creator ON p.created_by = creator.user_id AND creator.deleted_at IS NULL
                    LEFT JOIN users updater ON p.updated_by = updater.user_id AND updater.deleted_at IS NULL
                    WHERE {$searchString}
                    ORDER BY p.created_at DESC, p.part_id DESC
                    LIMIT {$curPage}, {$rowsPage}";

            $response['item']['sql'] = $sql;
            $result = mysqli_query($con, $sql);

            // 페이징 HTML 생성
            require INC_ROOT . '/common_pagination.php';
            $response['pagination'] = $pagination ?? '';

            $partsData = [];
            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $partsData[] = $row;
                }
            }

            $statusLabels = ['1' => '활성', '0' => '비활성'];
            $statusBadges = ['1' => 'badge-status-active', '0' => 'badge-status-inactive'];
            $warrantyTypeLabels = ['FREE' => '무상', 'PAID' => '유상'];
            $warrantyBadges = ['FREE' => 'badge-status-active', 'PAID' => 'badge-status-warning'];

            // HTML 생성
            $html = '';
            if (empty($partsData)) {
                $html = '<tr><td colspan="11" class="table-empty-state">조회된 부자재가 없습니다.</td></tr>';
            } else {
                foreach ($partsData as $part) {
                    $statusKey = $part['is_active'] ?? '1';
                    $warrantyType = $part['warranty_type'] ?? 'FREE';
                    $html .= '<tr data-part-id="' . htmlspecialchars($part['part_id']) . '"
                                  data-category="' . htmlspecialchars($part['category_id'] ?? '') . '"
                                  data-warranty="' . htmlspecialchars($warrantyType) . '"
                                  data-status="' . $statusKey . '">
                              <td><strong>' . htmlspecialchars($part['part_id']) . '</strong></td>
                              <td>' . htmlspecialchars($part['part_name'] ?? '') . '</td>
                              <td>' . htmlspecialchars($part['part_number'] ?? '-') . '</td>
                              <td>' . htmlspecialchars($part['category_name'] ?? '-') . '</td>
                              <td>' . htmlspecialchars($part['compatible_device_name'] ?? '공용') . '</td>
                              <td><strong>₩' . number_format($part['price'] ?? 0) . '</strong></td>
                              <td>' . number_format($part['stock_quantity'] ?? 0) . '</td>
                              <td>
                                <span class="badge ' . $warrantyBadges[$warrantyType] . '">
                                  ' . $warrantyTypeLabels[$warrantyType] . '
                                </span>
                              </td>
                              <td>
                                <span class="badge ' . $statusBadges[$statusKey] . '">
                                  ' . $statusLabels[$statusKey] . '
                                </span>
                              </td>
                              <td>' . date('Y-m-d', strtotime($part['created_at'] ?? 'now')) . '</td>
                              <td>
                                <button class="btn-sm" onclick="editPart(' . $part['part_id'] . ')">수정</button>
                              </td>
                            </tr>';
                }
            }

            $response['result'] = true;
            $response['html'] = $html;
            $response['item']['count'] = count($partsData);
            break;

        case 'get_part':
            $partId = null;
            foreach ($_POST as $key => $value) {
                if (strpos(decryptValue($key), 'part_id') !== false) {
                    $partId = intval($value);
                    break;
                }
            }

            $sql = "SELECT * FROM parts WHERE part_id = {$partId} AND deleted_at IS NULL";
            $response['item']['sql'] = $sql;
            $result = mysqli_query($con, $sql);

            if ($result && $row = mysqli_fetch_assoc($result)) {
                $response['result'] = true;
                $response['item']['part'] = $row;
            } else {
                throw new Exception('부자재 정보를 찾을 수 없습니다.');
            }
            break;

        case 'save_part':
            $partId = null;
            $partName = '';
            $partNumber = '';
            $categoryId = null;
            $compatibleDeviceId = null;
            $price = 0;
            $stockQuantity = 0;
            $warrantyType = 'FREE';
            $description = '';
            $isActive = 1;

            foreach ($_POST as $key => $value) {
                $decrypted = decryptValue($key);

                if (strpos($decrypted, 'part_id') !== false && $value) {
                    $partId = intval($value);
                } elseif (strpos($decrypted, 'part_name') !== false) {
                    $partName = mysqli_real_escape_string($con, $value);
                } elseif (strpos($decrypted, 'part_number') !== false) {
                    $partNumber = mysqli_real_escape_string($con, $value);
                } elseif (strpos($decrypted, 'category_id') !== false) {
                    $categoryId = $value ? intval($value) : null;
                } elseif (strpos($decrypted, 'compatible_device_id') !== false) {
                    $compatibleDeviceId = $value ? intval($value) : null;
                } elseif (strpos($decrypted, 'price') !== false) {
                    $price = floatval($value);
                } elseif (strpos($decrypted, 'stock_quantity') !== false) {
                    $stockQuantity = intval($value);
                } elseif (strpos($decrypted, 'warranty_type') !== false) {
                    $warrantyType = mysqli_real_escape_string($con, $value);
                } elseif (strpos($decrypted, 'description') !== false) {
                    $description = mysqli_real_escape_string($con, $value);
                } elseif (strpos($decrypted, 'is_active') !== false) {
                    $isActive = intval($value);
                }
            }

            if (empty($partName)) {
                throw new Exception('부품명을 입력해주세요.');
            }

            $categoryIdSql = $categoryId ? $categoryId : 'NULL';
            $compatibleDeviceIdSql = $compatibleDeviceId ? $compatibleDeviceId : 'NULL';

            if ($partId) {
                $sql = "UPDATE parts SET
                            part_name = '{$partName}',
                            part_number = '{$partNumber}',
                            category_id = {$categoryIdSql},
                            compatible_device_id = {$compatibleDeviceIdSql},
                            price = {$price},
                            stock_quantity = {$stockQuantity},
                            warranty_type = '{$warrantyType}',
                            description = '{$description}',
                            is_active = {$isActive},
                            updated_by = {$_SESSION['user_id']},
                            updated_at = NOW()
                        WHERE part_id = {$partId}";
                $msg = '부자재가 수정되었습니다.';
            } else {
                $sql = "INSERT INTO parts (
                            part_name, part_number, category_id, compatible_device_id, price, stock_quantity,
                            warranty_type, description, is_active, created_by, created_at
                        ) VALUES (
                            '{$partName}', '{$partNumber}', {$categoryIdSql}, {$compatibleDeviceIdSql}, {$price}, {$stockQuantity},
                            '{$warrantyType}', '{$description}', {$isActive}, {$_SESSION['user_id']}, NOW()
                        )";
                $msg = '부자재가 등록되었습니다.';
            }

            $response['item']['sql'] = $sql;

            if (mysqli_query($con, $sql)) {
                $response['result'] = true;
                $response['msg'] = $msg;
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
