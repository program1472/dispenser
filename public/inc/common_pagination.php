<?php
/**
 * 공용 페이징 시스템 (Common Pagination System)
 *
 * 사용법:
 * $paginationConfig = [
 *     'table' => 'table_name',
 *     'where' => 'deleted_at IS NULL',
 *     'join' => 'LEFT JOIN other ON ...',  // 선택
 *     'orderBy' => 'created_at DESC',      // 선택
 *     'rowsPerPage' => 50,                 // 선택 (기본 50)
 *     'targetId' => '#data-tbody',         // 선택
 *     'atValue' => encryptValue('10')      // 선택
 * ];
 * require_once INCLUDES_ROOT . '/common_pagination.php';
 * // 결과: $pagination 변수에 HTML 저장됨
 */

if (!isset($paginationConfig) || !is_array($paginationConfig)) {
    throw new Exception('$paginationConfig 배열이 필요합니다.');
}

// 필수 파라미터 확인
if (!isset($paginationConfig['table'])) {
    throw new Exception('$paginationConfig["table"]이 필요합니다.');
}

if (!isset($paginationConfig['where'])) {
    throw new Exception('$paginationConfig["where"]이 필요합니다.');
}

// 기본값 설정
$table_name = $paginationConfig['table'];
$searchString = $paginationConfig['where'];
$asTableName = $paginationConfig['join'] ?? '';
$orderBy = $paginationConfig['orderBy'] ?? 'created_at DESC';
$rowsPage = $paginationConfig['rowsPerPage'] ?? 25;
$targetId = $paginationConfig['targetId'] ?? '#data-tbody';
$atValue = $paginationConfig['atValue'] ?? encryptValue('10');

// POST에서 페이지 번호 가져오기
$p = $_POST['p'] ?? 1;
$curPage = $rowsPage * ($p - 1);

// 전체 레코드 수 조회
if ($asTableName) {
    // JOIN이 있는 경우: 테이블 이름 앞에 별칭 붙이기
    $countSql = "SELECT COUNT(*) as total FROM {$table_name} {$asTableName} WHERE {$searchString}";
} else {
    $countSql = "SELECT COUNT(*) as total FROM {$table_name} WHERE {$searchString}";
}

$countResult = mysqli_query($con, $countSql);
$totalRows = 0;
if ($countResult && $row = mysqli_fetch_assoc($countResult)) {
    $totalRows = $row['total'];
}

// 디버깅용 로그 (개발 중에만 사용)
if (isset($_POST['_debug_pagination'])) {
    error_log("Pagination Debug - Count SQL: " . $countSql);
    error_log("Pagination Debug - Total Rows: " . $totalRows);
    error_log("Pagination Debug - Current Page: " . $p);
}

// 페이징 HTML 생성
$pagination = '';
if ($totalRows > 0) {
    $totalPages = ceil($totalRows / $rowsPage);
    $currentPage = $p;

    // 페이지 범위 계산 (현재 페이지 ±5)
    $startPage = max(1, $currentPage - 5);
    $endPage = min($totalPages, $currentPage + 5);

    $pagination .= '<div class="pagination" data-at="' . $atValue . '">';

    // 이전 페이지
    if ($currentPage > 1) {
        $prevPage = $currentPage - 1;
        $pagination .= '<a href="#" data-p="' . $prevPage . '" data-id="' . htmlspecialchars($targetId) . '">&laquo; 이전</a>';
    }

    // 첫 페이지
    if ($startPage > 1) {
        $pagination .= '<a href="#" data-p="1" data-id="' . htmlspecialchars($targetId) . '">1</a>';
        if ($startPage > 2) {
            $pagination .= '<span>...</span>';
        }
    }

    // 페이지 번호들
    for ($i = $startPage; $i <= $endPage; $i++) {
        if ($i == $currentPage) {
            $pagination .= '<a href="#" class="active" data-p="' . $i . '" data-id="' . htmlspecialchars($targetId) . '">' . $i . '</a>';
        } else {
            $pagination .= '<a href="#" data-p="' . $i . '" data-id="' . htmlspecialchars($targetId) . '">' . $i . '</a>';
        }
    }

    // 마지막 페이지
    if ($endPage < $totalPages) {
        if ($endPage < $totalPages - 1) {
            $pagination .= '<span>...</span>';
        }
        $pagination .= '<a href="#" data-p="' . $totalPages . '" data-id="' . htmlspecialchars($targetId) . '">' . $totalPages . '</a>';
    }

    // 다음 페이지
    if ($currentPage < $totalPages) {
        $nextPage = $currentPage + 1;
        $pagination .= '<a href="#" data-p="' . $nextPage . '" data-id="' . htmlspecialchars($targetId) . '">다음 &raquo;</a>';
    }

    $pagination .= '</div>';

    // 페이지 정보 추가
    $pagination .= '<div class="pagination-info">전체 ' . number_format($totalRows) . '개 / ' . $currentPage . ' / ' . $totalPages . ' 페이지</div>';
}
?>
