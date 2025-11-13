<?php
/**
 * HQ 콘텐츠 제품 관리 - AJAX 처리
 * 실제 contents 테이블 구조에 맞춰 작성
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
        case 'filter_contents':
            // 필터링된 콘텐츠 목록 조회 (실제 DB 스키마 사용)
            $categoryId = '';
            $templateType = '';
            $ownerType = '';
            $status = '';
            $keyword = '';

            foreach ($_POST as $key => $value) {
                $decrypted = decryptValue($key);
                if (strpos($decrypted, 'category_id') !== false) {
                    $categoryId = mysqli_real_escape_string($con, $value);
                } elseif (strpos($decrypted, 'template_type') !== false) {
                    $templateType = mysqli_real_escape_string($con, $value);
                } elseif (strpos($decrypted, 'owner_type') !== false) {
                    $ownerType = mysqli_real_escape_string($con, $value);
                } elseif (strpos($decrypted, 'status') !== false) {
                    $status = mysqli_real_escape_string($con, $value);
                } elseif (strpos($decrypted, 'keyword') !== false) {
                    $keyword = mysqli_real_escape_string($con, $value);
                }
            }

            // WHERE 조건 구성
            $searchString = "c.deleted_at IS NULL";
            if ($categoryId) {
                $searchString .= " AND c.category_id = '{$categoryId}'";
            }
            if ($templateType) {
                $searchString .= " AND c.template_type = '{$templateType}'";
            }
            if ($ownerType) {
                $searchString .= " AND c.owner_type = '{$ownerType}'";
            }
            if ($status !== '') {
                $searchString .= " AND c.is_active = '{$status}'";
            }
            if ($keyword) {
                $searchString .= " AND c.content_title LIKE '%{$keyword}%'";
            }

            // 페이징 설정
            $paginationConfig = [
                'table' => 'contents c',
                'where' => $searchString,
                'join' => 'LEFT JOIN categories cat ON c.category_id = cat.category_id LEFT JOIN customers cust ON c.owner_type = \'CUSTOMER\' AND c.owner_id = cust.customer_id',
                'orderBy' => 'c.created_at DESC, c.content_id DESC',
                'rowsPerPage' => $defaultRowsPage,
                'targetId' => '#contentTableBody',
                'atValue' => encryptValue('10')
            ];

            // 페이징 처리
            $rowsPage = $paginationConfig['rowsPerPage'];
            $p = $_POST['p'] ?? 1;
            $curPage = $rowsPage * ($p - 1);

            $sql = "SELECT
                      c.*,
                      cat.category_name,
                      CASE
                        WHEN c.owner_type = 'CUSTOMER' THEN cust.company_name
                        WHEN c.owner_type = 'LUCID' THEN '루시드'
                        ELSE '본사'
                      END as owner_name
                    FROM contents c
                    LEFT JOIN categories cat ON c.category_id = cat.category_id
                    LEFT JOIN customers cust ON c.owner_type = 'CUSTOMER' AND c.owner_id = cust.customer_id
                    WHERE {$searchString}
                    ORDER BY c.created_at DESC, c.content_id DESC
                    LIMIT {$curPage}, {$rowsPage}";

            $response['item']['sql'] = $sql;
            $result = mysqli_query($con, $sql);

            // 페이징 HTML 생성
            require INC_ROOT . '/common_pagination.php';
            $response['pagination'] = $pagination ?? '';

            $contentsData = [];
            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $contentsData[] = $row;
                }
            }

            $statusLabels = ['1' => '활성', '0' => '비활성'];
            $statusBadges = ['1' => 'badge-status-active', '0' => 'badge-status-inactive'];
            $templateTypeLabels = [
                'BASIC' => '기본',
                'SEASONAL' => '시즌',
                'PROMOTIONAL' => '프로모션',
                'CUSTOM' => '맞춤'
            ];

            // HTML 생성
            $html = '';
            if (empty($contentsData)) {
                $html = '<tr><td colspan="11" class="table-empty-state">조회된 콘텐츠가 없습니다.</td></tr>';
            } else {
                foreach ($contentsData as $content) {
                    $statusKey = $content['is_active'] ?? '1';
                    $isFree = $content['is_free'] ?? 1;

                    $html .= '<tr data-content-id="' . htmlspecialchars($content['content_id']) . '"
                                  data-category="' . htmlspecialchars($content['category_id'] ?? '') . '"
                                  data-template="' . htmlspecialchars($content['template_type'] ?? '') . '"
                                  data-owner-type="' . htmlspecialchars($content['owner_type'] ?? '') . '"
                                  data-status="' . $statusKey . '">
                              <td><strong>' . htmlspecialchars($content['content_id']) . '</strong></td>
                              <td>' . htmlspecialchars($content['content_title'] ?? '') . '</td>
                              <td>' . htmlspecialchars($content['category_name'] ?? '-') . '</td>
                              <td><span class="badge badge-status-normal">' . ($templateTypeLabels[$content['template_type']] ?? '-') . '</span></td>
                              <td>' . htmlspecialchars($content['size'] ?? '-') . '</td>
                              <td>' . htmlspecialchars($content['owner_name'] ?? '-') . '</td>
                              <td>' . ($isFree ? '<span class="badge badge-status-active">무료</span>' : '<span class="badge badge-status-inactive">유료</span>') . '</td>
                              <td>' . number_format($content['view_count'] ?? 0) . '</td>
                              <td>
                                <span class="badge ' . $statusBadges[$statusKey] . '">
                                  ' . $statusLabels[$statusKey] . '
                                </span>
                              </td>
                              <td>' . date('Y-m-d', strtotime($content['created_at'] ?? 'now')) . '</td>
                              <td>
                                <button class="btn-sm" onclick="editContent(' . htmlspecialchars($content['content_id']) . ')">수정</button>
                              </td>
                            </tr>';
                }
            }

            $response['result'] = true;
            $response['html'] = $html;
            $response['item']['count'] = count($contentsData);
            break;

        case 'get_content':
            $contentId = null;
            foreach ($_POST as $key => $value) {
                if (strpos(decryptValue($key), 'content_id') !== false) {
                    $contentId = mysqli_real_escape_string($con, $value);
                    break;
                }
            }

            $sql = "SELECT * FROM contents WHERE content_id = '{$contentId}' AND deleted_at IS NULL";
            $response['item']['sql'] = $sql;
            $result = mysqli_query($con, $sql);

            if ($result && $row = mysqli_fetch_assoc($result)) {
                $response['result'] = true;
                $response['item']['content'] = $row;
            } else {
                throw new Exception('콘텐츠 정보를 찾을 수 없습니다.');
            }
            break;

        case 'save_content':
            $contentId = null;
            $contentTitle = '';
            $categoryId = '';
            $templateType = 'BASIC';
            $size = '';
            $ownerType = 'COMPANY';
            $isFree = 1;
            $imageUrl = '';
            $thumbnailUrl = '';
            $fileUrl = '';
            $description = '';
            $isActive = 1;
            $userId = $_SESSION['user_id'] ?? 1;

            foreach ($_POST as $key => $value) {
                $decrypted = decryptValue($key);

                if (strpos($decrypted, 'content_id') !== false && $value) {
                    $contentId = mysqli_real_escape_string($con, $value);
                } elseif (strpos($decrypted, 'content_title') !== false) {
                    $contentTitle = mysqli_real_escape_string($con, $value);
                } elseif (strpos($decrypted, 'category_id') !== false) {
                    $categoryId = mysqli_real_escape_string($con, $value);
                } elseif (strpos($decrypted, 'template_type') !== false) {
                    $templateType = mysqli_real_escape_string($con, $value);
                } elseif (strpos($decrypted, 'size') !== false) {
                    $size = mysqli_real_escape_string($con, $value);
                } elseif (strpos($decrypted, 'owner_type') !== false) {
                    $ownerType = mysqli_real_escape_string($con, $value);
                } elseif (strpos($decrypted, 'is_free') !== false) {
                    $isFree = intval($value);
                } elseif (strpos($decrypted, 'image_url') !== false) {
                    $imageUrl = mysqli_real_escape_string($con, $value);
                } elseif (strpos($decrypted, 'thumbnail_url') !== false) {
                    $thumbnailUrl = mysqli_real_escape_string($con, $value);
                } elseif (strpos($decrypted, 'file_url') !== false) {
                    $fileUrl = mysqli_real_escape_string($con, $value);
                } elseif (strpos($decrypted, 'description') !== false) {
                    $description = mysqli_real_escape_string($con, $value);
                } elseif (strpos($decrypted, 'is_active') !== false) {
                    $isActive = intval($value);
                }
            }

            if (empty($contentTitle)) {
                throw new Exception('제목을 입력해주세요.');
            }

            $categoryIdSql = $categoryId ? "'{$categoryId}'" : 'NULL';
            $sizeSql = $size ? "'{$size}'" : 'NULL';

            if ($contentId) {
                $sql = "UPDATE contents SET
                            content_title = '{$contentTitle}',
                            category_id = {$categoryIdSql},
                            template_type = '{$templateType}',
                            size = {$sizeSql},
                            owner_type = '{$ownerType}',
                            is_free = {$isFree},
                            image_url = '{$imageUrl}',
                            thumbnail_url = '{$thumbnailUrl}',
                            file_url = '{$fileUrl}',
                            description = '{$description}',
                            is_active = {$isActive},
                            updated_at = NOW(),
                            updated_by = {$userId}
                        WHERE content_id = '{$contentId}'";
                $msg = '콘텐츠가 수정되었습니다.';
            } else {
                $sql = "INSERT INTO contents (
                            content_title, category_id, template_type, size, owner_type, is_free,
                            image_url, thumbnail_url, file_url, description, is_active,
                            created_at, updated_at, created_by, updated_by
                        ) VALUES (
                            '{$contentTitle}', {$categoryIdSql}, '{$templateType}', {$sizeSql}, '{$ownerType}', {$isFree},
                            '{$imageUrl}', '{$thumbnailUrl}', '{$fileUrl}', '{$description}', {$isActive},
                            NOW(), NOW(), {$userId}, {$userId}
                        )";
                $msg = '콘텐츠가 등록되었습니다.';
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
