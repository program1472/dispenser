<?php
/**
 * HQ 향카트리지 제품 관리 - AJAX 처리
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
        case 'filter_scents':
            $categoryId = '';
            $allergenFree = '';
            $ecoFriendly = '';
            $status = '';
            $keyword = '';

            foreach ($_POST as $key => $value) {
                $decrypted = decryptValue($key);
                if (strpos($decrypted, 'category_id') !== false) {
                    $categoryId = $value ? intval($value) : '';
                } elseif (strpos($decrypted, 'is_allergen_free') !== false) {
                    $allergenFree = $value !== '' ? intval($value) : '';
                } elseif (strpos($decrypted, 'is_eco_friendly') !== false) {
                    $ecoFriendly = $value !== '' ? intval($value) : '';
                } elseif (strpos($decrypted, 'status') !== false) {
                    $status = $value !== '' ? intval($value) : '';
                } elseif (strpos($decrypted, 'keyword') !== false) {
                    $keyword = mysqli_real_escape_string($con, $value);
                }
            }

            // WHERE 조건 구성
            $searchString = "s.deleted_at IS NULL";
            if ($categoryId) $searchString .= " AND s.category_id = {$categoryId}";
            if ($allergenFree !== '') $searchString .= " AND s.is_allergen_free = {$allergenFree}";
            if ($ecoFriendly !== '') $searchString .= " AND s.is_eco_friendly = {$ecoFriendly}";
            if ($status !== '') $searchString .= " AND s.is_active = {$status}";
            if ($keyword) $searchString .= " AND s.scent_name LIKE '%{$keyword}%'";

            // 페이징 설정
            $paginationConfig = [
                'table' => 'scents s',
                'where' => $searchString,
                'join' => 'LEFT JOIN categories c ON s.category_id = c.category_id',
                'orderBy' => 's.created_at DESC',
                'rowsPerPage' => $defaultRowsPage,
                'targetId' => '#scentTableBody',
                'atValue' => encryptValue('10')
            ];

            // 페이징 처리
            $rowsPage = $paginationConfig['rowsPerPage'];
            $p = $_POST['p'] ?? 1;
            $curPage = $rowsPage * ($p - 1);

            $sql = "SELECT s.*, c.category_name
                    FROM scents s
                    LEFT JOIN categories c ON s.category_id = c.category_id
                    WHERE {$searchString}
                    ORDER BY s.created_at DESC
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
                    $isAllergenFree = $row['is_allergen_free'] ?? 0;
                    $isEcoFriendly = $row['is_eco_friendly'] ?? 0;

                    $features = '';
                    if ($isAllergenFree) {
                        $features .= '<span class="badge badge-status-active">알러지프리</span> ';
                    }
                    if ($isEcoFriendly) {
                        $features .= '<span class="badge badge-status-normal">친환경</span>';
                    }

                    $html .= '<tr data-scent-id="' . $row['scent_id'] . '" data-category="' . $row['category_id'] . '" data-allergen="' . $isAllergenFree . '" data-eco="' . $isEcoFriendly . '" data-status="' . $statusKey . '">
                              <td><strong>' . $row['scent_id'] . '</strong></td>
                              <td>' . htmlspecialchars($row['scent_name']) . '</td>
                              <td>' . htmlspecialchars($row['scent_family'] ?? '-') . '</td>
                              <td>' . htmlspecialchars($row['category_name'] ?? '-') . '</td>
                              <td>' . ($row['capacity_ml'] ?? 0) . 'ml</td>
                              <td><strong>₩' . number_format($row['price']) . '</strong></td>
                              <td>' . number_format($row['stock_quantity']) . '</td>
                              <td>' . $features . '</td>
                              <td><span class="badge ' . $statusBadges[$statusKey] . '">' . $statusLabels[$statusKey] . '</span></td>
                              <td>' . number_format($row['view_count'] ?? 0) . '</td>
                              <td>' . date('Y-m-d', strtotime($row['created_at'])) . '</td>
                              <td><button class="btn-sm" onclick="editScent(' . $row['scent_id'] . ')">수정</button></td>
                            </tr>';
                }
            }

            if ($count == 0) {
                $html = '<tr><td colspan="12" class="table-empty-state">조회된 향카트리지가 없습니다.</td></tr>';
            }

            $response['result'] = true;
            $response['html'] = $html;
            $response['item']['count'] = $count;
            break;

        case 'get_scent':
            $scentId = null;
            foreach ($_POST as $key => $value) {
                if (strpos(decryptValue($key), 'scent_id') !== false) {
                    $scentId = intval($value);
                    break;
                }
            }

            $sql = "SELECT * FROM scents WHERE scent_id = {$scentId} AND deleted_at IS NULL";
            $response['item']['sql'] = $sql;
            $result = mysqli_query($con, $sql);

            if ($result && $row = mysqli_fetch_assoc($result)) {
                $response['result'] = true;
                $response['item']['scent'] = $row;
            } else {
                throw new Exception('향카트리지 정보를 찾을 수 없습니다.');
            }
            break;

        case 'save_scent':
            $scentId = null;
            $scentName = '';
            $scentFamily = '';
            $categoryId = null;
            $capacityMl = 0;
            $price = 0;
            $stockQuantity = 0;
            $imageUrl = '';
            $description = '';
            $ingredients = '';
            $isAllergenFree = 0;
            $isEcoFriendly = 0;
            $isActive = 1;
            $userId = $_SESSION['user_id'] ?? 1;

            foreach ($_POST as $key => $value) {
                $decrypted = decryptValue($key);

                if (strpos($decrypted, 'scent_id') !== false && $value) {
                    $scentId = intval($value);
                } elseif (strpos($decrypted, 'scent_name') !== false) {
                    $scentName = mysqli_real_escape_string($con, $value);
                } elseif (strpos($decrypted, 'scent_family') !== false) {
                    $scentFamily = mysqli_real_escape_string($con, $value);
                } elseif (strpos($decrypted, 'category_id') !== false) {
                    $categoryId = $value ? intval($value) : null;
                } elseif (strpos($decrypted, 'capacity_ml') !== false) {
                    $capacityMl = intval($value);
                } elseif (strpos($decrypted, 'price') !== false) {
                    $price = floatval($value);
                } elseif (strpos($decrypted, 'stock_quantity') !== false) {
                    $stockQuantity = intval($value);
                } elseif (strpos($decrypted, 'image_url') !== false) {
                    $imageUrl = mysqli_real_escape_string($con, $value);
                } elseif (strpos($decrypted, 'description') !== false) {
                    $description = mysqli_real_escape_string($con, $value);
                } elseif (strpos($decrypted, 'ingredients') !== false) {
                    $ingredients = mysqli_real_escape_string($con, $value);
                } elseif (strpos($decrypted, 'is_allergen_free') !== false) {
                    $isAllergenFree = intval($value);
                } elseif (strpos($decrypted, 'is_eco_friendly') !== false) {
                    $isEcoFriendly = intval($value);
                } elseif (strpos($decrypted, 'is_active') !== false) {
                    $isActive = intval($value);
                }
            }

            if (empty($scentName)) {
                throw new Exception('향 이름을 입력해주세요.');
            }

            $categoryIdSql = $categoryId ? $categoryId : 'NULL';

            if ($scentId) {
                $sql = "UPDATE scents SET
                            scent_name = '{$scentName}',
                            scent_family = '{$scentFamily}',
                            category_id = {$categoryIdSql},
                            capacity_ml = {$capacityMl},
                            price = {$price},
                            stock_quantity = {$stockQuantity},
                            image_url = '{$imageUrl}',
                            description = '{$description}',
                            ingredients = '{$ingredients}',
                            is_allergen_free = {$isAllergenFree},
                            is_eco_friendly = {$isEcoFriendly},
                            is_active = {$isActive},
                            updated_by = {$userId},
                            updated_at = NOW()
                        WHERE scent_id = {$scentId}";
                $msg = '향카트리지가 수정되었습니다.';
            } else {
                $sql = "INSERT INTO scents (
                            scent_name, scent_family, category_id, capacity_ml, price, stock_quantity,
                            image_url, description, ingredients, is_allergen_free, is_eco_friendly,
                            is_active, created_by, created_at
                        ) VALUES (
                            '{$scentName}', '{$scentFamily}', {$categoryIdSql}, {$capacityMl}, {$price}, {$stockQuantity},
                            '{$imageUrl}', '{$description}', '{$ingredients}', {$isAllergenFree}, {$isEcoFriendly},
                            {$isActive}, {$userId}, NOW()
                        )";
                $msg = '향카트리지가 등록되었습니다.';
            }

            $response['item']['sql'] = $sql;

            if (mysqli_query($con, $sql)) {
                $response['result'] = true;
                $response['msg'] = $msg;
                if (!$scentId) {
                    $response['item']['scent_id'] = mysqli_insert_id($con);
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
