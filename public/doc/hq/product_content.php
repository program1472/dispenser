<?php
/**
 * HQ 콘텐츠 제품 관리
 * 인쇄물 콘텐츠 관리
 */

// $con 변수는 common.php에서 이미 연결됨

// POST 핸들러 처리
if (!empty($_POST) && (isset($_POST['action']) || isset($_POST['p']))) {
    header('Content-Type: application/json; charset=utf-8');

    $action = $_POST['action'] ?? 'filter_contents';

    switch ($action) {
        case 'filter_contents':
            $categoryId = $_POST['category_id'] ?? '';
            $templateType = $_POST['template_type'] ?? '';
            $ownerType = $_POST['owner_type'] ?? '';
            $status = $_POST['status'] ?? '';
            $keyword = $_POST['keyword'] ?? '';

            $sql = "SELECT
                      c.*,
                      cat.category_name,
                      creator.name as creator_name,
                      updater.name as updater_name,
                      CASE
                        WHEN c.owner_type = 'CUSTOMER' THEN cust.company_name
                        WHEN c.owner_type = 'LUCID' THEN '루시드'
                        ELSE '본사'
                      END as owner_name
                    FROM contents c
                    LEFT JOIN categories cat ON c.category_id = cat.category_id
                    LEFT JOIN users creator ON c.created_by = creator.user_id
                    LEFT JOIN users updater ON c.updated_by = updater.user_id
                    LEFT JOIN customers cust ON c.owner_type = 'CUSTOMER' AND c.owner_id = cust.customer_id
                    WHERE c.deleted_at IS NULL";

            if ($categoryId) {
                $categoryIdEsc = mysqli_real_escape_string($con, $categoryId);
                $sql .= " AND c.category_id = '{$categoryIdEsc}'";
            }

            if ($templateType) {
                $templateTypeEsc = mysqli_real_escape_string($con, $templateType);
                $sql .= " AND c.template_type = '{$templateTypeEsc}'";
            }

            if ($ownerType) {
                $ownerTypeEsc = mysqli_real_escape_string($con, $ownerType);
                $sql .= " AND c.owner_type = '{$ownerTypeEsc}'";
            }

            if ($status !== '') {
                $statusEsc = mysqli_real_escape_string($con, $status);
                $sql .= " AND c.is_active = '{$statusEsc}'";
            }

            if ($keyword) {
                $keywordEsc = mysqli_real_escape_string($con, $keyword);
                $sql .= " AND c.content_title LIKE '%{$keywordEsc}%'";
            }

            // 페이징 설정
            $searchString = "c.deleted_at IS NULL";
            if ($categoryId) $searchString .= " AND c.category_id = '{$categoryIdEsc}'";
            if ($templateType) $searchString .= " AND c.template_type = '{$templateTypeEsc}'";
            if ($ownerType) $searchString .= " AND c.owner_type = '{$ownerTypeEsc}'";
            if ($status !== '') $searchString .= " AND c.is_active = '{$statusEsc}'";
            if ($keyword) $searchString .= " AND c.content_title LIKE '%{$keywordEsc}%'";

            $paginationConfig = [
                'table' => 'contents c',
                'where' => $searchString,
                'join' => 'LEFT JOIN categories cat ON c.category_id = cat.category_id',
                'orderBy' => 'c.created_at DESC',
                'rowsPerPage' => $defaultRowsPage,
                'targetId' => '#contentTableBody',
                'atValue' => encryptValue('10')
            ];

            $rowsPage = $paginationConfig['rowsPerPage'];
            $p = $_POST['p'] ?? 1;
            $curPage = $rowsPage * ($p - 1);

            $sql .= " ORDER BY c.created_at DESC, c.content_id DESC LIMIT {$curPage}, {$rowsPage}";
            $result = mysqli_query($con, $sql);

            // 페이징 HTML 생성
            require INC_ROOT . '/common_pagination.php';
            $response['pagination'] = $pagination ?? '';

            $html = '';
            $count = 0;
            if ($result && mysqli_num_rows($result) > 0) {
                $templateTypeLabels = ['BASIC' => '기본', 'SEASONAL' => '시즌', 'PROMOTIONAL' => '프로모션', 'CUSTOM' => '맞춤'];

                while ($row = mysqli_fetch_assoc($result)) {
                    $count++;
                    $statusLabel = $row['is_active'] == 1 ? '활성' : '비활성';
                    $statusClass = $row['is_active'] == 1 ? 'badge-status-active' : 'badge-status-inactive';
                    $templateLabel = $templateTypeLabels[$row['template_type']] ?? $row['template_type'];

                    $imageHtml = '';
                    if (!empty($row['thumbnail_url'])) {
                        $imageHtml = '<img src="' . htmlspecialchars($row['thumbnail_url']) . '" alt="' . htmlspecialchars($row['content_title']) . '">';
                    } elseif (!empty($row['image_url'])) {
                        $imageHtml = '<img src="' . htmlspecialchars($row['image_url']) . '" alt="' . htmlspecialchars($row['content_title']) . '">';
                    } else {
                        $imageHtml = '<img src="/dispenser/public/images/no-image.png" alt="No Image">';
                    }

                    $html .= '<tr>';
                    $html .= '<td>' . htmlspecialchars($row['content_id']) . '</td>';
                    $html .= '<td>' . $imageHtml . '</td>';
                    $html .= '<td><strong>' . htmlspecialchars($row['content_title']) . '</strong></td>';
                    $html .= '<td>' . htmlspecialchars($row['category_name'] ?? '-') . '</td>';
                    $html .= '<td>' . htmlspecialchars($templateLabel) . '</td>';
                    $html .= '<td>' . htmlspecialchars($row['size'] ?? '-') . '</td>';
                    $html .= '<td>' . htmlspecialchars($row['owner_name'] ?? '-') . '</td>';
                    $html .= '<td>' . (($row['is_free'] ?? 1) == 1 ? '무료' : '유료') . '</td>';
                    $html .= '<td>' . htmlspecialchars($row['view_count'] ?? 0) . '</td>';
                    $html .= '<td><span class="badge ' . $statusClass . '">' . $statusLabel . '</span></td>';
                    $html .= '<td>' . date('Y-m-d', strtotime($row['created_at'])) . '</td>';
                    $html .= '<td>';
                    $html .= '<button class="btn-sm" onclick="editContent(' . $row['content_id'] . ')">수정</button>';
                    $html .= '</td>';
                    $html .= '</tr>';
                }
            } else {
                $html = '<tr><td colspan="12" class="table-empty-state">조회된 콘텐츠가 없습니다.</td></tr>';
            }

            $response['result'] = true;
            $response['html'] = $html;
            $response['item'] = ['count' => $count];
            Finish();

        case 'get_content':
            $contentId = $_POST['content_id'] ?? '';
            if (empty($contentId)) {
                $response['result'] = false;
                $response['error'] = ['msg' => '콘텐츠 ID가 필요합니다.', 'code' => 400];
                Finish();
            }

            $contentIdEsc = mysqli_real_escape_string($con, $contentId);
            $sql = "SELECT * FROM contents WHERE content_id = {$contentIdEsc} AND deleted_at IS NULL";
            $result = mysqli_query($con, $sql);

            if ($result && $row = mysqli_fetch_assoc($result)) {
                $response['result'] = true;
                $response['item'] = ['content' => $row];
            } else {
                $response['result'] = false;
                $response['error'] = ['msg' => '콘텐츠를 찾을 수 없습니다.', 'code' => 404];
            }
            Finish();

        case 'save_content':
            $contentId = $_POST['content_id'] ?? '';
            $contentTitle = $_POST['content_title'] ?? '';
            $categoryId = $_POST['category_id'] ?? '';
            $description = $_POST['description'] ?? '';
            $templateType = $_POST['template_type'] ?? '';
            $ownerType = $_POST['owner_type'] ?? '';
            $isActive = $_POST['is_active'] ?? '1';

            if (empty($contentTitle)) {
                $response['result'] = false;
                $response['error'] = ['msg' => '콘텐츠 제목은 필수입니다.', 'code' => 400];
                Finish();
            }

            $contentTitleEsc = mysqli_real_escape_string($con, $contentTitle);
            $categoryIdEsc = $categoryId ? mysqli_real_escape_string($con, $categoryId) : 'NULL';
            $descriptionEsc = mysqli_real_escape_string($con, $description);
            $templateTypeEsc = mysqli_real_escape_string($con, $templateType);
            $ownerTypeEsc = mysqli_real_escape_string($con, $ownerType);
            $isActiveEsc = mysqli_real_escape_string($con, $isActive);

            if ($contentId) {
                // Update
                $contentIdEsc = mysqli_real_escape_string($con, $contentId);
                $sql = "UPDATE contents SET
                        content_title = '{$contentTitleEsc}',
                        category_id = " . ($categoryId ? "'{$categoryIdEsc}'" : "NULL") . ",
                        description = '{$descriptionEsc}',
                        template_type = '{$templateTypeEsc}',
                        owner_type = '{$ownerTypeEsc}',
                        is_active = '{$isActiveEsc}',
                        updated_at = NOW(),
                        updated_by = {$mb_no}
                        WHERE content_id = {$contentIdEsc}";
            } else {
                // Insert
                $sql = "INSERT INTO contents (content_title, category_id, description, template_type, owner_type, is_active, created_at, created_by)
                        VALUES ('{$contentTitleEsc}', " . ($categoryId ? "'{$categoryIdEsc}'" : "NULL") . ", '{$descriptionEsc}', '{$templateTypeEsc}', '{$ownerTypeEsc}', '{$isActiveEsc}', NOW(), {$mb_no})";
            }

            if (mysqli_query($con, $sql)) {
                $response['result'] = true;
                $response['msg'] = $contentId ? '콘텐츠가 수정되었습니다.' : '콘텐츠가 등록되었습니다.';
            } else {
                $response['result'] = false;
                $response['error'] = ['msg' => '저장 실패: ' . mysqli_error($con), 'code' => 500];
            }
            Finish();

        default:
            $response['result'] = false;
            $response['error'] = ['msg' => 'Invalid action', 'code' => 400];
            Finish();
    }
}

// 모든 콘텐츠 데이터 조회
$sql = "SELECT
          c.*,
          cat.category_name,
          creator.name as creator_name,
          updater.name as updater_name,
          CASE
            WHEN c.owner_type = 'CUSTOMER' THEN cust.company_name
            WHEN c.owner_type = 'LUCID' THEN '루시드'
            ELSE '본사'
          END as owner_name
        FROM contents c
        LEFT JOIN categories cat ON c.category_id = cat.category_id
        LEFT JOIN users creator ON c.created_by = creator.user_id
        LEFT JOIN users updater ON c.updated_by = updater.user_id
        LEFT JOIN customers cust ON c.owner_type = 'CUSTOMER' AND c.owner_id = cust.customer_id
        WHERE c.deleted_at IS NULL
        ORDER BY c.created_at DESC, c.content_id DESC";
$result = mysqli_query($con, $sql);

// SQL 로깅
$response['item']['sql'] = $sql;

// 데이터 가져오기
$contentsData = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $contentsData[] = $row;
    }
}

$statusLabels = [
    '1' => '활성',
    '0' => '비활성'
];

$statusBadges = [
    '1' => 'badge-status-active',
    '0' => 'badge-status-inactive'
];

$templateTypeLabels = [
    'BASIC' => '기본',
    'SEASONAL' => '시즌',
    'PROMOTIONAL' => '프로모션',
    'CUSTOM' => '맞춤'
];

$ownerTypeLabels = [
    'COMPANY' => '본사',
    'CUSTOMER' => '고객사',
    'LUCID' => '루시드'
];
?>

<section class="card">
  <div class="card-hd card-hd-wrap">
    <div class="card-hd-content">
      <div class="card-hd-title-area">
        <div class="card-ttl">콘텐츠 관리</div>
        <div class="card-sub">인쇄물 콘텐츠 정보 관리</div>
      </div>
      <div class="filter-toolbar">
        <div class="filter-group">
          <label>카테고리</label>
          <select id="categoryFilter" class="form-control filter-select" onchange="applyContentFilters()">
            <option value="">전체</option>
            <?php
            // 콘텐츠 유형 카테고리 ID 동적 조회
            $contentTypeId = null;
            $typeResult = mysqli_query($con, "SELECT category_id FROM categories WHERE category_name = '콘텐츠 유형' AND (parent_id = 0 OR parent_id IS NULL)");
            if ($typeResult && $typeRow = mysqli_fetch_assoc($typeResult)) {
              $contentTypeId = $typeRow['category_id'];
            }

            // 콘텐츠 유형의 하위 카테고리 조회 (계절별, 테마별, 프로모션)
            if ($contentTypeId) {
              $subTypeResult = mysqli_query($con, "SELECT category_id FROM categories WHERE parent_id = {$contentTypeId} AND is_active = 1");
              $subTypeIds = [];
              while ($subType = mysqli_fetch_assoc($subTypeResult)) {
                $subTypeIds[] = $subType['category_id'];
              }

              // 2단계 하위 카테고리 조회 (실제 콘텐츠 카테고리들)
              if (!empty($subTypeIds)) {
                $categorySql = "SELECT category_id, category_name, parent_id
                               FROM categories
                               WHERE parent_id IN (" . implode(',', $subTypeIds) . ")
                               AND is_active = 1
                               ORDER BY parent_id, display_order, category_name";
                $categoryResult = mysqli_query($con, $categorySql);
                if ($categoryResult) {
                  while ($cat = mysqli_fetch_assoc($categoryResult)) {
                    echo '<option value="' . htmlspecialchars($cat['category_id']) . '">' . htmlspecialchars($cat['category_name']) . '</option>';
                  }
                }
              }
            }
            ?>
          </select>
        </div>
        <div class="filter-group">
          <label>템플릿</label>
          <select id="templateFilter" class="form-control filter-select" onchange="applyContentFilters()">
            <option value="">전체</option>
            <option value="BASIC">기본</option>
            <option value="SEASONAL">시즌</option>
            <option value="PROMOTIONAL">프로모션</option>
            <option value="CUSTOM">맞춤</option>
          </select>
        </div>
        <div class="filter-group">
          <label>소유자</label>
          <select id="ownerTypeFilter" class="form-control filter-select" onchange="applyContentFilters()">
            <option value="">전체</option>
            <option value="COMPANY">본사</option>
            <option value="CUSTOMER">고객사</option>
            <option value="LUCID">루시드</option>
          </select>
        </div>
        <div class="filter-group">
          <label>상태</label>
          <select id="statusFilter" class="form-control filter-select" onchange="applyContentFilters()">
            <option value="">전체</option>
            <option value="1">활성</option>
            <option value="0">비활성</option>
          </select>
        </div>
        <div class="filter-group">
          <label>검색</label>
          <input type="text" id="searchKeyword" class="form-control filter-input" placeholder="제목 검색" onkeypress="if(event.key==='Enter') applyContentFilters()">
        </div>
        <button class="btn primary" onclick="applyContentFilters()">조회</button>
        <button class="btn primary" onclick="openAddContentModal()">콘텐츠 추가</button>
      </div>
    </div>
    <div class="row">
      <button class="btn" onclick="exportContentsToCsv()">CSV 내보내기</button>
    </div>
  </div>

  <div class="card-bd">
    <div class="table-wrap">
      <table class="tbl-list" id="tblContents">
        <thead>
          <tr>
            <th>ID</th>
            <th>이미지</th>
            <th>제목</th>
            <th>카테고리</th>
            <th>템플릿</th>
            <th>사이즈</th>
            <th>소유자</th>
            <th>무료여부</th>
            <th>조회수</th>
            <th>상태</th>
            <th>등록일</th>
            <th>관리</th>
          </tr>
        </thead>
        <tbody id="contentTableBody">
        </tbody>
      </table>
    </div>

    <!-- 페이징 영역 -->
    <div class="paging" data-id="#contentTableBody"></div>
  </div>
</section>

<!-- 콘텐츠 추가/수정 모달 -->
<div id="modalContent" class="modal">
  <div class="modal-content modal-lg">
    <div class="modal-header">
      <h3 id="contentFormTitle">콘텐츠 추가</h3>
      <button class="modal-close" onclick="closeContentModal()">&times;</button>
    </div>
    <div class="modal-body">
      <form id="contentForm">
        <input type="hidden" id="contentId" name="content_id">

        <div class="grid-2">
          <div class="form-group">
            <label>제목 *</label>
            <input type="text" id="contentTitle" name="content_title" class="form-control" required>
          </div>

          <div class="form-group">
            <label>카테고리 *</label>
            <select id="categoryId" name="category_id" class="form-control" required>
              <option value="">선택</option>
              <?php
              // 콘텐츠 유형 카테고리 ID 동적 조회
              $contentTypeId = null;
              $typeResult = mysqli_query($con, "SELECT category_id FROM categories WHERE category_name = '콘텐츠 유형' AND (parent_id = 0 OR parent_id IS NULL)");
              if ($typeResult && $typeRow = mysqli_fetch_assoc($typeResult)) {
                $contentTypeId = $typeRow['category_id'];
              }

              // 콘텐츠 유형의 하위 카테고리 조회 (계절별, 테마별, 프로모션)
              if ($contentTypeId) {
                $subTypeResult = mysqli_query($con, "SELECT category_id FROM categories WHERE parent_id = {$contentTypeId} AND is_active = 1");
                $subTypeIds = [];
                while ($subType = mysqli_fetch_assoc($subTypeResult)) {
                  $subTypeIds[] = $subType['category_id'];
                }

                // 2단계 하위 카테고리 조회 (실제 콘텐츠 카테고리들)
                if (!empty($subTypeIds)) {
                  $categorySql = "SELECT category_id, category_name, parent_id
                                 FROM categories
                                 WHERE parent_id IN (" . implode(',', $subTypeIds) . ")
                                 AND is_active = 1
                                 ORDER BY parent_id, display_order, category_name";
                  $categoryResult = mysqli_query($con, $categorySql);
                  if ($categoryResult) {
                    while ($cat = mysqli_fetch_assoc($categoryResult)) {
                      echo '<option value="' . htmlspecialchars($cat['category_id']) . '">' . htmlspecialchars($cat['category_name']) . '</option>';
                    }
                  }
                }
              }
              ?>
            </select>
          </div>

          <div class="form-group">
            <label>템플릿 타입 *</label>
            <select id="templateType" name="template_type" class="form-control" required>
              <option value="BASIC">기본</option>
              <option value="SEASONAL">시즌</option>
              <option value="PROMOTIONAL">프로모션</option>
              <option value="CUSTOM">맞춤</option>
            </select>
          </div>

          <div class="form-group">
            <label>사이즈</label>
            <input type="text" id="size" name="size" class="form-control" placeholder="예: A4, A5">
          </div>

          <div class="form-group">
            <label>소유자 타입 *</label>
            <select id="ownerType" name="owner_type" class="form-control" required>
              <option value="COMPANY">본사</option>
              <option value="CUSTOMER">고객사</option>
              <option value="LUCID">루시드</option>
            </select>
          </div>

          <div class="form-group">
            <label>무료 여부 *</label>
            <select id="isFree" name="is_free" class="form-control" required>
              <option value="1">무료</option>
              <option value="0">유료</option>
            </select>
          </div>

          <div class="form-group">
            <label>이미지 URL</label>
            <input type="text" id="imageUrl" name="image_url" class="form-control" placeholder="https://...">
          </div>

          <div class="form-group">
            <label>썸네일 URL</label>
            <input type="text" id="thumbnailUrl" name="thumbnail_url" class="form-control" placeholder="https://...">
          </div>

          <div class="form-group">
            <label>파일 URL</label>
            <input type="text" id="fileUrl" name="file_url" class="form-control" placeholder="https://...">
          </div>

          <div class="form-group">
            <label>상태 *</label>
            <select id="isActive" name="is_active" class="form-control" required>
              <option value="1">활성</option>
              <option value="0">비활성</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label>설명</label>
          <textarea id="description" name="description" class="form-control" rows="3" placeholder="콘텐츠 설명"></textarea>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn" onclick="closeContentModal()">취소</button>
      <button class="btn primary" onclick="saveContent()">저장</button>
    </div>
  </div>
</div>

<script>
// 페이지 이름 (AJAX 호출용)
window.pageName = '<?= $pageName ?>';

// 콘텐츠 추가
window.openAddContentModal = function() {
  document.getElementById('contentFormTitle').textContent = '콘텐츠 추가';
  document.getElementById('contentForm').reset();
  document.getElementById('contentId').value = '';
  document.getElementById('templateType').value = 'BASIC';
  document.getElementById('ownerType').value = 'COMPANY';
  document.getElementById('isFree').value = '1';
  document.getElementById('isActive').value = '1';
  document.getElementById('modalContent').style.display = 'flex';
}

// 콘텐츠 수정
window.editContent = function(contentId) {
  document.getElementById('contentFormTitle').textContent = '콘텐츠 수정';
  document.getElementById('contentId').value = contentId;

  const data = {};
  data['<?= encryptValue('action') ?>'] = 'get_content';
  data['<?= encryptValue('content_id') ?>'] = contentId;

  updateAjaxContent(data, function(response) {
    if (response.result && response.item.content) {
      const c = response.item.content;
      document.getElementById('contentTitle').value = c.content_title || '';
      document.getElementById('categoryId').value = c.category_id || '';
      document.getElementById('templateType').value = c.template_type || 'BASIC';
      document.getElementById('size').value = c.size || '';
      document.getElementById('ownerType').value = c.owner_type || 'COMPANY';
      document.getElementById('isFree').value = c.is_free || '1';
      document.getElementById('imageUrl').value = c.image_url || '';
      document.getElementById('thumbnailUrl').value = c.thumbnail_url || '';
      document.getElementById('fileUrl').value = c.file_url || '';
      document.getElementById('description').value = c.description || '';
      document.getElementById('isActive').value = c.is_active || '1';
      document.getElementById('modalContent').style.display = 'flex';
    } else {
      alert('콘텐츠 정보를 불러올 수 없습니다.');
    }
  }, false);
}

// 모달 닫기
window.closeContentModal = function() {
  document.getElementById('modalContent').style.display = 'none';
}

// 저장
window.saveContent = function() {
  const form = document.getElementById('contentForm');
  if (!form.checkValidity()) {
    alert('필수 항목을 입력해주세요.');
    return;
  }

  const contentId = document.getElementById('contentId').value;
  const data = {};
  data['<?= encryptValue('action') ?>'] = 'save_content';
  if (contentId) data['<?= encryptValue('content_id') ?>'] = contentId;
  data['<?= encryptValue('content_title') ?>'] = document.getElementById('contentTitle').value;
  data['<?= encryptValue('category_id') ?>'] = document.getElementById('categoryId').value;
  data['<?= encryptValue('template_type') ?>'] = document.getElementById('templateType').value;
  data['<?= encryptValue('size') ?>'] = document.getElementById('size').value;
  data['<?= encryptValue('owner_type') ?>'] = document.getElementById('ownerType').value;
  data['<?= encryptValue('is_free') ?>'] = document.getElementById('isFree').value;
  data['<?= encryptValue('image_url') ?>'] = document.getElementById('imageUrl').value;
  data['<?= encryptValue('thumbnail_url') ?>'] = document.getElementById('thumbnailUrl').value;
  data['<?= encryptValue('file_url') ?>'] = document.getElementById('fileUrl').value;
  data['<?= encryptValue('description') ?>'] = document.getElementById('description').value;
  data['<?= encryptValue('is_active') ?>'] = document.getElementById('isActive').value;

  updateAjaxContent(data, function(response) {
    if (response.result) {
      alert(response.msg || '저장되었습니다.');
      closeContentModal();
      const firstTab = document.querySelector('#sec-product-mgmt .tab-btn-inline.active');
      if (firstTab && typeof loadProductTab === 'function') {
        loadProductTab(firstTab, firstTab.getAttribute('data-token'));
      } else {
        location.reload();
      }
    } else {
      alert(response.error?.msg || '저장에 실패했습니다.');
    }
  }, false);
}

// 서버 사이드 필터링
window.applyContentFilters = function() {
  const category = document.getElementById('categoryFilter')?.value || '';
  const template = document.getElementById('templateFilter')?.value || '';
  const ownerType = document.getElementById('ownerTypeFilter')?.value || '';
  const status = document.getElementById('statusFilter')?.value || '';
  const keyword = document.getElementById('searchKeyword')?.value || '';

  const data = {};
  data['<?= encryptValue('action') ?>'] = 'filter_contents';
  data['<?= encryptValue('category_id') ?>'] = category;
  data['<?= encryptValue('template_type') ?>'] = template;
  data['<?= encryptValue('owner_type') ?>'] = ownerType;
  data['<?= encryptValue('status') ?>'] = status;
  data['<?= encryptValue('keyword') ?>'] = keyword;

  updateAjaxContent(data, function(response) {
    if (response.result && response.html) {
      document.querySelector('#contentTableBody').innerHTML = response.html;

      // 페이징 업데이트
      if (response.pagination) {
        const pagingContainer = document.querySelector('.paging[data-id="#contentTableBody"]');
        if (pagingContainer) {
          pagingContainer.innerHTML = response.pagination;
        }
      }

      console.log(`필터링 결과: ${response.item.count}개 콘텐츠 표시`);
    } else {
      alert('조회에 실패했습니다.');
    }
  }, false);
}

// CSV 내보내기
window.exportContentsToCsv = function() {
  const table = document.getElementById('tblContents');
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
  link.download = `HQ_콘텐츠관리_${dateStr}.csv`;
  link.click();
}

// ESC 키로 모달 닫기
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeContentModal();
  }
});

// 페이지 로드 시 자동 조회
applyContentFilters();
</script>
