<?php
/**
 * HQ 기기 제품 관리 - AJAX 처리
 * 기기 추가/수정/삭제/조회 처리
 */

// POST 데이터에서 액션 추출
$action = '';
foreach ($_POST as $key => $value) {
    $decrypted = decryptValue($key);
    if (strpos($decrypted, 'action') !== false) {
        $action = $value;
        break;
    }
}

$response['item']['action'] = $action;

try {
    switch ($action) {
        case 'filter_devices':
            // 필터링된 기기 목록 조회
            $manufacturer = '';
            $categoryId = '';
            $status = '';
            $keyword = '';

            foreach ($_POST as $key => $value) {
                $decrypted = decryptValue($key);
                if (strpos($decrypted, 'manufacturer') !== false) {
                    $manufacturer = mysqli_real_escape_string($con, $value);
                } elseif (strpos($decrypted, 'category_id') !== false) {
                    $categoryId = $value ? intval($value) : '';
                } elseif (strpos($decrypted, 'status') !== false) {
                    $status = $value !== '' ? intval($value) : '';
                } elseif (strpos($decrypted, 'keyword') !== false) {
                    $keyword = mysqli_real_escape_string($con, $value);
                }
            }

            // WHERE 조건 구성
            $searchString = "d.deleted_at IS NULL";
            if ($manufacturer) {
                $searchString .= " AND d.manufacturer = '{$manufacturer}'";
            }
            if ($categoryId) {
                $searchString .= " AND d.category_id = {$categoryId}";
            }
            if ($status !== '') {
                $searchString .= " AND d.is_active = {$status}";
            }
            if ($keyword) {
                $searchString .= " AND d.model_name LIKE '%{$keyword}%'";
            }

            // 페이징 설정
            $paginationConfig = [
                'table' => 'devices d',
                'where' => $searchString,
                'join' => 'LEFT JOIN categories c ON d.category_id = c.category_id',
                'orderBy' => 'd.created_at DESC, d.device_id DESC',
                'rowsPerPage' => $defaultRowsPage,
                'targetId' => '#deviceTableBody',
                'atValue' => encryptValue('10')
            ];

            // 페이징 처리
            $rowsPage = $paginationConfig['rowsPerPage'];
            $p = $_POST['p'] ?? 1;
            $curPage = $rowsPage * ($p - 1);

            $sql = "SELECT
                      d.*,
                      c.category_name,
                      creator.name as creator_name,
                      updater.name as updater_name
                    FROM devices d
                    LEFT JOIN categories c ON d.category_id = c.category_id
                    LEFT JOIN users creator ON d.created_by = creator.user_id AND creator.deleted_at IS NULL
                    LEFT JOIN users updater ON d.updated_by = updater.user_id AND updater.deleted_at IS NULL
                    WHERE {$searchString}
                    ORDER BY d.created_at DESC, d.device_id DESC
                    LIMIT {$curPage}, {$rowsPage}";

            $response['item']['sql'] = $sql;
            $result = mysqli_query($con, $sql);

            // 페이징 HTML 생성
            require INC_ROOT . '/common_pagination.php';
            $response['pagination'] = $pagination ?? '';

            $devicesData = [];
            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $devicesData[] = $row;
                }
            }

            $statusLabels = ['1' => '활성', '0' => '비활성'];
            $statusBadges = ['1' => 'badge-status-active', '0' => 'badge-status-inactive'];

            // HTML 생성
            $html = '';
            if (empty($devicesData)) {
                $html = '<tr><td colspan="8" class="table-empty-state">조회된 기기가 없습니다.</td></tr>';
            } else {
                foreach ($devicesData as $device) {
                    $statusKey = $device['is_active'] ?? '1';
                    $html .= '<tr data-device-id="' . htmlspecialchars($device['device_id']) . '"
                                  data-manufacturer="' . htmlspecialchars($device['manufacturer'] ?? '') . '"
                                  data-category="' . htmlspecialchars($device['category_id'] ?? '') . '"
                                  data-status="' . $statusKey . '">
                              <td><strong>' . htmlspecialchars($device['device_id']) . '</strong></td>
                              <td>' . htmlspecialchars($device['model_name'] ?? '') . '</td>
                              <td>' . htmlspecialchars($device['manufacturer'] ?? '') . '</td>
                              <td>' . htmlspecialchars($device['category_name'] ?? '-') . '</td>
                              <td><small>' . htmlspecialchars(mb_substr($device['specifications'] ?? '', 0, 50)) . '</small></td>
                              <td>
                                <span class="badge ' . $statusBadges[$statusKey] . '">
                                  ' . $statusLabels[$statusKey] . '
                                </span>
                              </td>
                              <td>' . date('Y-m-d', strtotime($device['created_at'] ?? 'now')) . '</td>
                              <td>
                                <button class="btn-sm" onclick="editDevice(' . $device['device_id'] . ')">수정</button>
                              </td>
                            </tr>';
                }
            }

            $response['result'] = true;
            $response['html'] = $html;
            $response['item']['count'] = count($devicesData);
            break;

        case 'get_device':
            // 기기 정보 조회
            $deviceId = null;
            foreach ($_POST as $key => $value) {
                $decrypted = decryptValue($key);
                if (strpos($decrypted, 'device_id') !== false) {
                    $deviceId = intval($value);
                    break;
                }
            }

            if (!$deviceId) {
                throw new Exception('기기 ID가 필요합니다.');
            }

            $sql = "SELECT * FROM devices WHERE device_id = {$deviceId} AND deleted_at IS NULL";
            $response['item']['sql'] = $sql;
            $result = mysqli_query($con, $sql);

            if ($result && $row = mysqli_fetch_assoc($result)) {
                $response['result'] = true;
                $response['item']['device'] = $row;
            } else {
                throw new Exception('기기 정보를 찾을 수 없습니다.');
            }
            break;

        case 'save_device':
            // 기기 추가 또는 수정
            $deviceId = null;
            $modelName = '';
            $manufacturer = '';
            $categoryId = null;
            $specifications = '';
            $imageUrl = '';
            $manualUrl = '';
            $isActive = 1;

            // POST 데이터 파싱
            foreach ($_POST as $key => $value) {
                $decrypted = decryptValue($key);

                if (strpos($decrypted, 'device_id') !== false && $value) {
                    $deviceId = intval($value);
                } elseif (strpos($decrypted, 'model_name') !== false) {
                    $modelName = mysqli_real_escape_string($con, $value);
                } elseif (strpos($decrypted, 'manufacturer') !== false) {
                    $manufacturer = mysqli_real_escape_string($con, $value);
                } elseif (strpos($decrypted, 'category_id') !== false) {
                    $categoryId = intval($value);
                } elseif (strpos($decrypted, 'specifications') !== false) {
                    $specifications = mysqli_real_escape_string($con, $value);
                } elseif (strpos($decrypted, 'image_url') !== false) {
                    $imageUrl = mysqli_real_escape_string($con, $value);
                } elseif (strpos($decrypted, 'manual_url') !== false) {
                    $manualUrl = mysqli_real_escape_string($con, $value);
                } elseif (strpos($decrypted, 'is_active') !== false) {
                    $isActive = intval($value);
                }
            }

            // 필수 필드 검증
            if (empty($modelName) || empty($manufacturer) || !$categoryId) {
                throw new Exception('필수 항목을 입력해주세요.');
            }

            if ($deviceId) {
                // 수정
                $sql = "UPDATE devices SET
                            model_name = '{$modelName}',
                            manufacturer = '{$manufacturer}',
                            category_id = {$categoryId},
                            specifications = '{$specifications}',
                            image_url = '{$imageUrl}',
                            manual_url = '{$manualUrl}',
                            is_active = {$isActive},
                            updated_by = {$_SESSION['user_id']},
                            updated_at = NOW()
                        WHERE device_id = {$deviceId}";

                $response['item']['sql'] = $sql;

                if (mysqli_query($con, $sql)) {
                    $response['result'] = true;
                    $response['msg'] = '기기가 수정되었습니다.';
                } else {
                    throw new Exception('기기 수정에 실패했습니다: ' . mysqli_error($con));
                }
            } else {
                // 추가
                $sql = "INSERT INTO devices (
                            model_name,
                            manufacturer,
                            category_id,
                            specifications,
                            image_url,
                            manual_url,
                            is_active,
                            created_by,
                            created_at
                        ) VALUES (
                            '{$modelName}',
                            '{$manufacturer}',
                            {$categoryId},
                            '{$specifications}',
                            '{$imageUrl}',
                            '{$manualUrl}',
                            {$isActive},
                            {$_SESSION['user_id']},
                            NOW()
                        )";

                $response['item']['sql'] = $sql;

                if (mysqli_query($con, $sql)) {
                    $response['result'] = true;
                    $response['msg'] = '기기가 등록되었습니다.';
                    $response['item']['device_id'] = mysqli_insert_id($con);
                } else {
                    throw new Exception('기기 등록에 실패했습니다: ' . mysqli_error($con));
                }
            }
            break;

        case 'delete_device':
            // 기기 삭제 (soft delete)
            $deviceId = null;
            foreach ($_POST as $key => $value) {
                $decrypted = decryptValue($key);
                if (strpos($decrypted, 'device_id') !== false) {
                    $deviceId = intval($value);
                    break;
                }
            }

            if (!$deviceId) {
                throw new Exception('기기 ID가 필요합니다.');
            }

            $sql = "UPDATE devices SET
                        deleted_at = NOW(),
                        updated_by = {$_SESSION['user_id']},
                        updated_at = NOW()
                    WHERE device_id = {$deviceId}";

            $response['item']['sql'] = $sql;

            if (mysqli_query($con, $sql)) {
                $response['result'] = true;
                $response['msg'] = '기기가 삭제되었습니다.';
            } else {
                throw new Exception('기기 삭제에 실패했습니다: ' . mysqli_error($con));
            }
            break;

        default:
            throw new Exception('알 수 없는 액션입니다: ' . $action);
    }
} catch (Exception $e) {
    $response['result'] = false;

    // 공통 함수로 에러 메시지 변환
    $errorMsg = getFriendlyErrorMessage($e->getMessage());

    $response['error'] = ['msg' => $errorMsg];
}

Finish();
