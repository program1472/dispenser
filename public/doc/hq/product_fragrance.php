<?php
/**
 * HQ 향카트리지 제품 관리
 * 디스펜서 오일 (향카트리지) 관리
 */

// $con 변수는 common.php에서 이미 연결됨

// POST 핸들러 처리
if (!empty($_POST) && (isset($_POST['action']) || isset($_POST['p']))) {
    header('Content-Type: application/json; charset=utf-8');

    $action = $_POST['action'] ?? 'filter_scents';

    switch ($action) {
        case 'filter_scents':
            $categoryId = $_POST['category_id'] ?? '';
            $allergen = $_POST['allergen_free'] ?? '';
            $eco = $_POST['eco_friendly'] ?? '';
            $status = $_POST['status'] ?? '';
            $keyword = $_POST['keyword'] ?? '';

            $sql = "SELECT
                      s.*,
                      c.category_name,
                      creator.name as creator_name,
                      updater.name as updater_name
                    FROM scents s
                    LEFT JOIN categories c ON s.category_id = c.category_id
                    LEFT JOIN users creator ON s.created_by = creator.user_id AND creator.deleted_at IS NULL
                    LEFT JOIN users updater ON s.updated_by = updater.user_id AND updater.deleted_at IS NULL
                    WHERE s.deleted_at IS NULL";

            if ($categoryId) {
                $categoryIdEsc = mysqli_real_escape_string($con, $categoryId);
                $sql .= " AND s.category_id = '{$categoryIdEsc}'";
            }

            if ($allergen !== '') {
                $allergenEsc = mysqli_real_escape_string($con, $allergen);
                $sql .= " AND s.is_allergen_free = '{$allergenEsc}'";
            }

            if ($eco !== '') {
                $ecoEsc = mysqli_real_escape_string($con, $eco);
                $sql .= " AND s.is_eco_friendly = '{$ecoEsc}'";
            }

            if ($status !== '') {
                $statusEsc = mysqli_real_escape_string($con, $status);
                $sql .= " AND s.is_active = '{$statusEsc}'";
            }

            if ($keyword) {
                $keywordEsc = mysqli_real_escape_string($con, $keyword);
                $sql .= " AND s.scent_name LIKE '%{$keywordEsc}%'";
            }

            // 페이징 설정
            $searchString = "s.deleted_at IS NULL";
            if ($categoryId) $searchString .= " AND s.category_id = '{$categoryIdEsc}'";
            if ($allergen !== '') $searchString .= " AND s.is_allergen_free = '{$allergenEsc}'";
            if ($eco !== '') $searchString .= " AND s.is_eco_friendly = '{$ecoEsc}'";
            if ($status !== '') $searchString .= " AND s.is_active = '{$statusEsc}'";
            if ($keyword) $searchString .= " AND s.scent_name LIKE '%{$keywordEsc}%'";

            $paginationConfig = [
                'table' => 'scents s',
                'where' => $searchString,
                'join' => 'LEFT JOIN categories c ON s.category_id = c.category_id',
                'orderBy' => 's.created_at DESC',
                'rowsPerPage' => $defaultRowsPage,
                'targetId' => '#scentTableBody',
                'atValue' => encryptValue('10')
            ];

            $rowsPage = $paginationConfig['rowsPerPage'];
            $p = $_POST['p'] ?? 1;
            $curPage = $rowsPage * ($p - 1);

            $sql .= " ORDER BY s.created_at DESC, s.scent_id DESC LIMIT {$curPage}, {$rowsPage}";
            $result = mysqli_query($con, $sql);

            // 페이징 HTML 생성
            require INC_ROOT . '/common_pagination.php';
            $response['pagination'] = $pagination ?? '';

            $html = '';
            $count = 0;
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $count++;
                    $statusLabel = $row['is_active'] == 1 ? '활성' : '비활성';
                    $statusClass = $row['is_active'] == 1 ? 'badge-status-active' : 'badge-status-inactive';

                    $imageHtml = '';
                    if (!empty($row['image_url'])) {
                        $imageHtml = '<img src="' . htmlspecialchars($row['image_url']) . '" alt="' . htmlspecialchars($row['scent_name']) . '">';
                    } else {
                        $imageHtml = '<img src="/dispenser/public/images/no-image.png" alt="No Image">';
                    }

                    $html .= '<tr>';
                    $html .= '<td>' . htmlspecialchars($row['scent_id']) . '</td>';
                    $html .= '<td>' . $imageHtml . '</td>';
                    $html .= '<td><strong>' . htmlspecialchars($row['scent_name']) . '</strong></td>';
                    $html .= '<td>' . htmlspecialchars($row['category_name'] ?? '-') . '</td>';
                    $html .= '<td>' . htmlspecialchars($row['capacity_ml'] ?? '-') . 'ml</td>';
                    $html .= '<td>' . number_format($row['price'] ?? 0) . '원</td>';
                    $html .= '<td>' . number_format($row['stock_quantity'] ?? 0) . '</td>';
                    $html .= '<td>' . (($row['is_allergen_free'] ?? 0) ? '알레르기무' : ($row['is_eco_friendly'] ?? 0) ? '친환경' : '-') . '</td>';
                    $html .= '<td><span class="badge ' . $statusClass . '">' . $statusLabel . '</span></td>';
                    $html .= '<td>' . htmlspecialchars($row['view_count'] ?? 0) . '</td>';
                    $html .= '<td>' . date('Y-m-d', strtotime($row['created_at'])) . '</td>';
                    $html .= '<td>';
                    $html .= '<button class="btn-sm" onclick="editScent(' . $row['scent_id'] . ')">수정</button>';
                    $html .= '</td>';
                    $html .= '</tr>';
                }
            } else {
                $html = '<tr><td colspan="12" class="table-empty-state">조회된 향카트리지가 없습니다.</td></tr>';
            }

            $response['result'] = true;
            $response['html'] = $html;
            $response['item'] = ['count' => $count];
            Finish();

        case 'get_scent':
            $scentId = $_POST['scent_id'] ?? '';
            if (empty($scentId)) {
                $response['result'] = false;
                $response['error'] = ['msg' => '향 ID가 필요합니다.', 'code' => 400];
                Finish();
            }

            $scentIdEsc = mysqli_real_escape_string($con, $scentId);
            $sql = "SELECT * FROM scents WHERE scent_id = {$scentIdEsc} AND deleted_at IS NULL";
            $result = mysqli_query($con, $sql);

            if ($result && $row = mysqli_fetch_assoc($result)) {
                $response['result'] = true;
                $response['item'] = ['scent' => $row];
            } else {
                $response['result'] = false;
                $response['error'] = ['msg' => '향을 찾을 수 없습니다.', 'code' => 404];
            }
            Finish();

        case 'save_scent':
            $scentId = $_POST['scent_id'] ?? '';
            $scentName = $_POST['scent_name'] ?? '';
            $categoryId = $_POST['category_id'] ?? '';
            $description = $_POST['description'] ?? '';
            $allergenFree = isset($_POST['allergen_free']) ? 1 : 0;
            $ecoFriendly = isset($_POST['eco_friendly']) ? 1 : 0;
            $isActive = $_POST['is_active'] ?? '1';

            if (empty($scentName)) {
                $response['result'] = false;
                $response['error'] = ['msg' => '향 이름은 필수입니다.', 'code' => 400];
                Finish();
            }

            $scentNameEsc = mysqli_real_escape_string($con, $scentName);
            $categoryIdEsc = $categoryId ? mysqli_real_escape_string($con, $categoryId) : 'NULL';
            $descriptionEsc = mysqli_real_escape_string($con, $description);
            $isActiveEsc = mysqli_real_escape_string($con, $isActive);

            if ($scentId) {
                // Update
                $scentIdEsc = mysqli_real_escape_string($con, $scentId);
                $sql = "UPDATE scents SET
                        scent_name = '{$scentNameEsc}',
                        category_id = " . ($categoryId ? "'{$categoryIdEsc}'" : "NULL") . ",
                        description = '{$descriptionEsc}',
                        allergen_free = {$allergenFree},
                        eco_friendly = {$ecoFriendly},
                        is_active = '{$isActiveEsc}',
                        updated_at = NOW(),
                        updated_by = {$mb_no}
                        WHERE scent_id = {$scentIdEsc}";
            } else {
                // Insert
                $sql = "INSERT INTO scents (scent_name, category_id, description, allergen_free, eco_friendly, is_active, created_at, created_by)
                        VALUES ('{$scentNameEsc}', " . ($categoryId ? "'{$categoryIdEsc}'" : "NULL") . ", '{$descriptionEsc}', {$allergenFree}, {$ecoFriendly}, '{$isActiveEsc}', NOW(), {$mb_no})";
            }

            if (mysqli_query($con, $sql)) {
                $response['result'] = true;
                $response['msg'] = $scentId ? '향카트리지가 수정되었습니다.' : '향카트리지가 등록되었습니다.';
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

// 모든 향카트리지 데이터 조회
$sql = "SELECT
          s.*,
          c.category_name,
          creator.name as creator_name,
          updater.name as updater_name
        FROM scents s
        LEFT JOIN categories c ON s.category_id = c.category_id
        LEFT JOIN users creator ON s.created_by = creator.user_id AND creator.deleted_at IS NULL
        LEFT JOIN users updater ON s.updated_by = updater.user_id AND updater.deleted_at IS NULL
        WHERE s.deleted_at IS NULL
        ORDER BY s.created_at DESC, s.scent_id DESC";
$result = mysqli_query($con, $sql);

// SQL 로깅
$response['item']['sql'] = $sql;

// 데이터 가져오기
$scentsData = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $scentsData[] = $row;
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
?>

<section class="card">
  <div class="card-hd card-hd-wrap">
    <div class="card-hd-content">
      <div class="card-hd-title-area">
        <div class="card-ttl">향카트리지 관리</div>
        <div class="card-sub">디스펜서 오일 (향카트리지) 정보 및 재고 관리</div>
      </div>
      <div class="filter-toolbar">
        <div class="filter-group">
          <label>향 계열</label>
          <select id="categoryFilter" class="form-control filter-select" onchange="applyScentFilters()">
            <option value="">전체</option>
            <?php
            // 동적으로 "향 계열" 카테고리 찾기
            $scentTypeId = null;
            $typeResult = mysqli_query($con, "SELECT category_id FROM categories WHERE category_name = '향 계열' AND (parent_id = 0 OR parent_id IS NULL)");
            if ($typeResult && $typeRow = mysqli_fetch_assoc($typeResult)) {
              $scentTypeId = $typeRow['category_id'];
            }

            if ($scentTypeId) {
              $categorySql = "SELECT category_id, category_name FROM categories WHERE parent_id = {$scentTypeId} AND is_active = 1 ORDER BY display_order";
            } else {
              // Fallback to hardcoded if category not found
              $categorySql = "SELECT category_id, category_name FROM categories WHERE parent_id = 1 AND is_active = 1 ORDER BY display_order";
            }
            $categoryResult = mysqli_query($con, $categorySql);
            if ($categoryResult) {
              while ($cat = mysqli_fetch_assoc($categoryResult)) {
                echo '<option value="' . htmlspecialchars($cat['category_id']) . '">' . htmlspecialchars($cat['category_name']) . '</option>';
              }
            }
            ?>
          </select>
        </div>
        <div class="filter-group">
          <label>알러지 프리</label>
          <select id="allergenFilter" class="form-control filter-select" onchange="applyScentFilters()">
            <option value="">전체</option>
            <option value="1">예</option>
            <option value="0">아니오</option>
          </select>
        </div>
        <div class="filter-group">
          <label>친환경</label>
          <select id="ecoFilter" class="form-control filter-select" onchange="applyScentFilters()">
            <option value="">전체</option>
            <option value="1">예</option>
            <option value="0">아니오</option>
          </select>
        </div>
        <div class="filter-group">
          <label>상태</label>
          <select id="statusFilter" class="form-control filter-select" onchange="applyScentFilters()">
            <option value="">전체</option>
            <option value="1">활성</option>
            <option value="0">비활성</option>
          </select>
        </div>
        <div class="filter-group">
          <label>검색</label>
          <input type="text" id="searchKeyword" class="form-control filter-input" placeholder="향 이름 검색" onkeypress="if(event.key==='Enter') applyScentFilters()">
        </div>
        <button class="btn primary" onclick="applyScentFilters()">조회</button>
        <button class="btn primary" onclick="openAddScentModal()">향카트리지 추가</button>
      </div>
    </div>
    <div class="row">
      <button class="btn" onclick="exportScentsToCsv()">CSV 내보내기</button>
    </div>
  </div>

  <div class="card-bd card-bd-padding">
    <div class="table-wrap">
      <table class="tbl-list" id="tblScents">
        <thead>
          <tr>
            <th>ID</th>
            <th>이미지</th>
            <th>향 이름</th>
            <th>카테고리</th>
            <th>용량</th>
            <th>가격</th>
            <th>재고</th>
            <th>특성</th>
            <th>상태</th>
            <th>조회수</th>
            <th>등록일</th>
            <th>관리</th>
          </tr>
        </thead>
        <tbody id="scentTableBody">
        </tbody>
      </table>
    </div>

    <!-- 페이징 영역 -->
    <div class="paging" data-id="#scentTableBody"></div>
  </div>
</section>

<!-- 향카트리지 추가/수정 모달 -->
<div id="modalScent" class="modal">
  <div class="modal-content modal-lg">
    <div class="modal-header">
      <h3 id="scentFormTitle">향카트리지 추가</h3>
      <button class="modal-close" onclick="closeScentModal()">&times;</button>
    </div>
    <div class="modal-body">
      <form id="scentForm">
        <input type="hidden" id="scentId" name="scent_id">

        <div class="grid-2">
          <div class="form-group">
            <label>향 이름 *</label>
            <input type="text" id="scentName" name="scent_name" class="form-control" required>
          </div>

          <div class="form-group">
            <label>향 계열</label>
            <input type="text" id="scentFamily" name="scent_family" class="form-control" placeholder="예: Woody, Floral">
          </div>

          <div class="form-group">
            <label>카테고리 *</label>
            <select id="categoryId" name="category_id" class="form-control" required>
              <option value="">선택</option>
              <?php
              // 모달용 카테고리 - 위의 동적 lookup 결과 재사용
              if ($scentTypeId) {
                $categorySql = "SELECT category_id, category_name FROM categories WHERE parent_id = {$scentTypeId} AND is_active = 1 ORDER BY display_order";
              } else {
                $categorySql = "SELECT category_id, category_name FROM categories WHERE parent_id = 1 AND is_active = 1 ORDER BY display_order";
              }
              $categoryResult = mysqli_query($con, $categorySql);
              if ($categoryResult) {
                while ($cat = mysqli_fetch_assoc($categoryResult)) {
                  echo '<option value="' . htmlspecialchars($cat['category_id']) . '">' . htmlspecialchars($cat['category_name']) . '</option>';
                }
              }
              ?>
            </select>
          </div>

          <div class="form-group">
            <label>용량 (ml) *</label>
            <input type="number" id="capacityMl" name="capacity_ml" class="form-control" required>
          </div>

          <div class="form-group">
            <label>가격 *</label>
            <input type="number" id="price" name="price" class="form-control" step="0.01" required>
          </div>

          <div class="form-group">
            <label>재고수량 *</label>
            <input type="number" id="stockQuantity" name="stock_quantity" class="form-control" value="0" required>
          </div>

          <div class="form-group">
            <label>이미지 URL</label>
            <input type="text" id="imageUrl" name="image_url" class="form-control" placeholder="https://...">
          </div>

          <div class="form-group">
            <label>상태 *</label>
            <select id="isActive" name="is_active" class="form-control" required>
              <option value="1">활성</option>
              <option value="0">비활성</option>
            </select>
          </div>
        </div>

        <div class="grid-2">
          <div class="form-group">
            <label class="checkbox-label">
              <input type="checkbox" id="isAllergenFree" name="is_allergen_free" value="1">
              알러지 프리
            </label>
          </div>

          <div class="form-group">
            <label class="checkbox-label">
              <input type="checkbox" id="isEcoFriendly" name="is_eco_friendly" value="1">
              친환경
            </label>
          </div>
        </div>

        <div class="form-group">
          <label>설명</label>
          <textarea id="description" name="description" class="form-control" rows="2" placeholder="향 설명"></textarea>
        </div>

        <div class="form-group">
          <label>성분</label>
          <textarea id="ingredients" name="ingredients" class="form-control" rows="2" placeholder="주요 성분"></textarea>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn" onclick="closeScentModal()">취소</button>
      <button class="btn primary" onclick="saveScent()">저장</button>
    </div>
  </div>
</div>

<script>
// 페이지 이름 (AJAX 호출용)
window.pageName = '<?= $pageName ?>';

// 향카트리지 추가
window.openAddScentModal = function() {
  document.getElementById('scentFormTitle').textContent = '향카트리지 추가';
  document.getElementById('scentForm').reset();
  document.getElementById('scentId').value = '';
  document.getElementById('isActive').value = '1';
  document.getElementById('stockQuantity').value = '0';
  document.getElementById('modalScent').style.display = 'flex';
}

// 향카트리지 수정
window.editScent = function(scentId) {
  document.getElementById('scentFormTitle').textContent = '향카트리지 수정';
  document.getElementById('scentId').value = scentId;

  const data = {};
  data['<?= encryptValue('action') ?>'] = 'get_scent';
  data['<?= encryptValue('scent_id') ?>'] = scentId;

  updateAjaxContent(data, function(response) {
    if (response.result && response.item.scent) {
      const s = response.item.scent;
      document.getElementById('scentName').value = s.scent_name || '';
      document.getElementById('scentFamily').value = s.scent_family || '';
      document.getElementById('categoryId').value = s.category_id || '';
      document.getElementById('capacityMl').value = s.capacity_ml || '';
      document.getElementById('price').value = s.price || '';
      document.getElementById('stockQuantity').value = s.stock_quantity || '0';
      document.getElementById('imageUrl').value = s.image_url || '';
      document.getElementById('description').value = s.description || '';
      document.getElementById('ingredients').value = s.ingredients || '';
      document.getElementById('isAllergenFree').checked = s.is_allergen_free == 1;
      document.getElementById('isEcoFriendly').checked = s.is_eco_friendly == 1;
      document.getElementById('isActive').value = s.is_active || '1';
      document.getElementById('modalScent').style.display = 'flex';
    } else {
      alert('향카트리지 정보를 불러올 수 없습니다.');
    }
  }, false);
}

// 모달 닫기
window.closeScentModal = function() {
  document.getElementById('modalScent').style.display = 'none';
}

// 저장
window.saveScent = function() {
  const form = document.getElementById('scentForm');
  if (!form.checkValidity()) {
    alert('필수 항목을 입력해주세요.');
    return;
  }

  const scentId = document.getElementById('scentId').value;
  const data = {};
  data['<?= encryptValue('action') ?>'] = 'save_scent';
  if (scentId) data['<?= encryptValue('scent_id') ?>'] = scentId;
  data['<?= encryptValue('scent_name') ?>'] = document.getElementById('scentName').value;
  data['<?= encryptValue('scent_family') ?>'] = document.getElementById('scentFamily').value;
  data['<?= encryptValue('category_id') ?>'] = document.getElementById('categoryId').value;
  data['<?= encryptValue('capacity_ml') ?>'] = document.getElementById('capacityMl').value;
  data['<?= encryptValue('price') ?>'] = document.getElementById('price').value;
  data['<?= encryptValue('stock_quantity') ?>'] = document.getElementById('stockQuantity').value;
  data['<?= encryptValue('image_url') ?>'] = document.getElementById('imageUrl').value;
  data['<?= encryptValue('description') ?>'] = document.getElementById('description').value;
  data['<?= encryptValue('ingredients') ?>'] = document.getElementById('ingredients').value;
  data['<?= encryptValue('is_allergen_free') ?>'] = document.getElementById('isAllergenFree').checked ? 1 : 0;
  data['<?= encryptValue('is_eco_friendly') ?>'] = document.getElementById('isEcoFriendly').checked ? 1 : 0;
  data['<?= encryptValue('is_active') ?>'] = document.getElementById('isActive').value;

  updateAjaxContent(data, function(response) {
    if (response.result) {
      alert(response.msg || '저장되었습니다.');
      closeScentModal();
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
window.applyScentFilters = function() {
  const category = document.getElementById('categoryFilter')?.value || '';
  const allergen = document.getElementById('allergenFilter')?.value || '';
  const eco = document.getElementById('ecoFilter')?.value || '';
  const status = document.getElementById('statusFilter')?.value || '';
  const keyword = document.getElementById('searchKeyword')?.value || '';

  const data = {};
  data['<?= encryptValue('action') ?>'] = 'filter_scents';
  data['<?= encryptValue('category_id') ?>'] = category;
  data['<?= encryptValue('is_allergen_free') ?>'] = allergen;
  data['<?= encryptValue('is_eco_friendly') ?>'] = eco;
  data['<?= encryptValue('status') ?>'] = status;
  data['<?= encryptValue('keyword') ?>'] = keyword;

  updateAjaxContent(data, function(response) {
    if (response.result && response.html) {
      document.querySelector('#scentTableBody').innerHTML = response.html;

      // 페이징 업데이트
      if (response.pagination) {
        const pagingContainer = document.querySelector('.paging[data-id="#scentTableBody"]');
        if (pagingContainer) {
          pagingContainer.innerHTML = response.pagination;
        }
      }

      console.log(`필터링 결과: ${response.item.count}개 향카트리지 표시`);
    } else {
      alert('조회에 실패했습니다.');
    }
  }, false);
}

// CSV 내보내기
window.exportScentsToCsv = function() {
  const table = document.getElementById('tblScents');
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
  link.download = `HQ_향카트리지관리_${dateStr}.csv`;
  link.click();
}

// ESC 키로 모달 닫기
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeScentModal();
  }
});

// 페이지 로드 시 자동 조회
applyScentFilters();
</script>
