<?php
/**
 * HQ 부자재 제품 관리
 * 교체 부품 및 부자재 관리
 */

// $con 변수는 common.php에서 이미 연결됨

// POST 핸들러 처리
if (!empty($_POST) && (isset($_POST['action']) || isset($_POST['p']))) {
    header('Content-Type: application/json; charset=utf-8');

    $action = $_POST['action'] ?? 'filter_parts';

    switch ($action) {
        case 'filter_parts':
            $categoryId = $_POST['category_id'] ?? '';
            $status = $_POST['status'] ?? '';
            $keyword = $_POST['keyword'] ?? '';

            $sql = "SELECT
                      p.*,
                      c.category_name,
                      creator.name as creator_name,
                      updater.name as updater_name
                    FROM parts p
                    LEFT JOIN categories c ON p.category_id = c.category_id
                    LEFT JOIN users creator ON p.created_by = creator.user_id AND creator.deleted_at IS NULL
                    LEFT JOIN users updater ON p.updated_by = updater.user_id AND updater.deleted_at IS NULL
                    WHERE p.deleted_at IS NULL";

            if ($categoryId) {
                $categoryIdEsc = mysqli_real_escape_string($con, $categoryId);
                $sql .= " AND p.category_id = '{$categoryIdEsc}'";
            }

            if ($status !== '') {
                $statusEsc = mysqli_real_escape_string($con, $status);
                $sql .= " AND p.is_active = '{$statusEsc}'";
            }

            if ($keyword) {
                $keywordEsc = mysqli_real_escape_string($con, $keyword);
                $sql .= " AND p.part_name LIKE '%{$keywordEsc}%'";
            }

            // 페이징 설정
            $searchString = "p.deleted_at IS NULL";
            if ($categoryId) $searchString .= " AND p.category_id = '{$categoryIdEsc}'";
            if ($status !== '') $searchString .= " AND p.is_active = '{$statusEsc}'";
            if ($keyword) $searchString .= " AND p.part_name LIKE '%{$keywordEsc}%'";

            $paginationConfig = [
                'table' => 'parts p',
                'where' => $searchString,
                'join' => 'LEFT JOIN categories c ON p.category_id = c.category_id',
                'orderBy' => 'p.created_at DESC',
                'rowsPerPage' => $defaultRowsPage,
                'targetId' => '#partTableBody',
                'atValue' => encryptValue('10')
            ];

            $rowsPage = $paginationConfig['rowsPerPage'];
            $p = $_POST['p'] ?? 1;
            $curPage = $rowsPage * ($p - 1);

            $sql .= " ORDER BY p.created_at DESC, p.part_id DESC LIMIT {$curPage}, {$rowsPage}";
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

                    $warrantyLabel = ($row['warranty_type'] ?? 'FREE') == 'FREE' ? '무상' : '유상';
                    $warrantyClass = ($row['warranty_type'] ?? 'FREE') == 'FREE' ? 'badge-status-active' : 'badge-status-warning';

                    $imageHtml = '';
                    if (!empty($row['image_url'])) {
                        $imageHtml = '<img src="' . htmlspecialchars($row['image_url']) . '" alt="' . htmlspecialchars($row['part_name']) . '">';
                    } else {
                        $imageHtml = '<img src="/dispenser/public/images/no-image.png" alt="No Image">';
                    }

                    $html .= '<tr>';
                    $html .= '<td>' . htmlspecialchars($row['part_id']) . '</td>';
                    $html .= '<td>' . $imageHtml . '</td>';
                    $html .= '<td><strong>' . htmlspecialchars($row['part_name']) . '</strong></td>';
                    $html .= '<td>' . htmlspecialchars($row['part_number'] ?? '-') . '</td>';
                    $html .= '<td>' . htmlspecialchars($row['category_name'] ?? '-') . '</td>';
                    $html .= '<td>' . htmlspecialchars($row['compatible_device_name'] ?? '공용') . '</td>';
                    $html .= '<td>' . number_format($row['price'] ?? 0) . '원</td>';
                    $html .= '<td>' . number_format($row['stock_quantity'] ?? 0) . '</td>';
                    $html .= '<td><span class="badge ' . $warrantyClass . '">' . $warrantyLabel . '</span></td>';
                    $html .= '<td><span class="badge ' . $statusClass . '">' . $statusLabel . '</span></td>';
                    $html .= '<td>' . date('Y-m-d', strtotime($row['created_at'])) . '</td>';
                    $html .= '<td>';
                    $html .= '<button class="btn-sm" onclick="editPart(' . $row['part_id'] . ')">수정</button>';
                    $html .= '</td>';
                    $html .= '</tr>';
                }
            } else {
                $html = '<tr><td colspan="12" class="table-empty-state">조회된 부자재가 없습니다.</td></tr>';
            }

            $response['result'] = true;
            $response['html'] = $html;
            $response['item'] = ['count' => $count];
            Finish();

        case 'get_part':
            $partId = $_POST['part_id'] ?? '';
            if (empty($partId)) {
                $response['result'] = false;
                $response['error'] = ['msg' => '부자재 ID가 필요합니다.', 'code' => 400];
                Finish();
            }

            $partIdEsc = mysqli_real_escape_string($con, $partId);
            $sql = "SELECT * FROM parts WHERE part_id = {$partIdEsc} AND deleted_at IS NULL";
            $result = mysqli_query($con, $sql);

            if ($result && $row = mysqli_fetch_assoc($result)) {
                $response['result'] = true;
                $response['item'] = ['part' => $row];
            } else {
                $response['result'] = false;
                $response['error'] = ['msg' => '부자재를 찾을 수 없습니다.', 'code' => 404];
            }
            Finish();

        case 'save_part':
            $partId = $_POST['part_id'] ?? '';
            $partName = $_POST['part_name'] ?? '';
            $categoryId = $_POST['category_id'] ?? '';
            $description = $_POST['description'] ?? '';
            $isActive = $_POST['is_active'] ?? '1';

            if (empty($partName)) {
                $response['result'] = false;
                $response['error'] = ['msg' => '부자재 이름은 필수입니다.', 'code' => 400];
                Finish();
            }

            $partNameEsc = mysqli_real_escape_string($con, $partName);
            $categoryIdEsc = $categoryId ? mysqli_real_escape_string($con, $categoryId) : 'NULL';
            $descriptionEsc = mysqli_real_escape_string($con, $description);
            $isActiveEsc = mysqli_real_escape_string($con, $isActive);

            if ($partId) {
                // Update
                $partIdEsc = mysqli_real_escape_string($con, $partId);
                $sql = "UPDATE parts SET
                        part_name = '{$partNameEsc}',
                        category_id = " . ($categoryId ? "'{$categoryIdEsc}'" : "NULL") . ",
                        description = '{$descriptionEsc}',
                        is_active = '{$isActiveEsc}',
                        updated_at = NOW(),
                        updated_by = {$mb_no}
                        WHERE part_id = {$partIdEsc}";
            } else {
                // Insert
                $sql = "INSERT INTO parts (part_name, category_id, description, is_active, created_at, created_by)
                        VALUES ('{$partNameEsc}', " . ($categoryId ? "'{$categoryIdEsc}'" : "NULL") . ", '{$descriptionEsc}', '{$isActiveEsc}', NOW(), {$mb_no})";
            }

            if (mysqli_query($con, $sql)) {
                $response['result'] = true;
                $response['msg'] = $partId ? '부자재가 수정되었습니다.' : '부자재가 등록되었습니다.';
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

// 모든 부자재 데이터 조회
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
        WHERE p.deleted_at IS NULL
        ORDER BY p.created_at DESC, p.part_id DESC";
$result = mysqli_query($con, $sql);

// SQL 로깅
$response['item']['sql'] = $sql;

// 데이터 가져오기
$partsData = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $partsData[] = $row;
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

$warrantyTypeLabels = [
    'FREE' => '무상',
    'PAID' => '유상'
];

$warrantyBadges = [
    'FREE' => 'badge-status-active',
    'PAID' => 'badge-status-warning'
];
?>

<section class="card">
  <div class="card-hd card-hd-wrap">
    <div class="card-hd-content">
      <div class="card-hd-title-area">
        <div class="card-ttl">부자재 관리</div>
        <div class="card-sub">교체 부품 및 부자재 정보 및 재고 관리</div>
      </div>
      <div class="filter-toolbar">
        <div class="filter-group">
          <label>카테고리</label>
          <select id="categoryFilter" class="form-control filter-select" onchange="applyPartFilters()">
            <option value="">전체</option>
            <?php
            // 동적으로 "부자재 종류" 카테고리 찾기
            $partTypeId = null;
            $typeResult = mysqli_query($con, "SELECT category_id FROM categories WHERE category_name = '부자재 종류' AND (parent_id = 0 OR parent_id IS NULL)");
            if ($typeResult && $typeRow = mysqli_fetch_assoc($typeResult)) {
              $partTypeId = $typeRow['category_id'];
            }

            if ($partTypeId) {
              $categorySql = "SELECT category_id, category_name FROM categories WHERE parent_id = {$partTypeId} AND is_active = 1 ORDER BY display_order";
            } else {
              // Fallback to hardcoded if category not found
              $categorySql = "SELECT category_id, category_name FROM categories WHERE parent_id = 4 AND is_active = 1 ORDER BY display_order";
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
          <label>보증 타입</label>
          <select id="warrantyFilter" class="form-control filter-select" onchange="applyPartFilters()">
            <option value="">전체</option>
            <option value="FREE">무상</option>
            <option value="PAID">유상</option>
          </select>
        </div>
        <div class="filter-group">
          <label>상태</label>
          <select id="statusFilter" class="form-control filter-select" onchange="applyPartFilters()">
            <option value="">전체</option>
            <option value="1">활성</option>
            <option value="0">비활성</option>
          </select>
        </div>
        <div class="filter-group">
          <label>검색</label>
          <input type="text" id="searchKeyword" class="form-control filter-input" placeholder="부품명 검색" onkeypress="if(event.key==='Enter') applyPartFilters()">
        </div>
        <button class="btn primary" onclick="applyPartFilters()">조회</button>
        <button class="btn primary" onclick="openAddPartModal()">부자재 추가</button>
      </div>
    </div>
    <div class="row">
      <button class="btn" onclick="exportPartsToCsv()">CSV 내보내기</button>
    </div>
  </div>

  <div class="card-bd">
    <div class="table-wrap">
      <table class="tbl-list" id="tblParts">
        <thead>
          <tr>
            <th>ID</th>
            <th>이미지</th>
            <th>부품명</th>
            <th>부품번호</th>
            <th>카테고리</th>
            <th>호환 기기</th>
            <th>가격</th>
            <th>재고</th>
            <th>보증</th>
            <th>상태</th>
            <th>등록일</th>
            <th>관리</th>
          </tr>
        </thead>
        <tbody id="partTableBody">
        </tbody>
      </table>
    </div>

    <!-- 페이징 영역 -->
    <div class="paging" data-id="#partTableBody"></div>
  </div>
</section>

<!-- 부자재 추가/수정 모달 -->
<div id="modalPart" class="modal">
  <div class="modal-content modal-lg">
    <div class="modal-header">
      <h3 id="partFormTitle">부자재 추가</h3>
      <button class="modal-close" onclick="closePartModal()">&times;</button>
    </div>
    <div class="modal-body">
      <form id="partForm">
        <input type="hidden" id="partId" name="part_id">

        <div class="grid-2">
          <div class="form-group">
            <label>부품명 *</label>
            <input type="text" id="partName" name="part_name" class="form-control" required>
          </div>

          <div class="form-group">
            <label>부품번호</label>
            <input type="text" id="partNumber" name="part_number" class="form-control" placeholder="예: PN-12345">
          </div>

          <div class="form-group">
            <label>카테고리 *</label>
            <select id="categoryId" name="category_id" class="form-control" required>
              <option value="">선택</option>
              <?php
              // 모달용 카테고리 - 위의 동적 lookup 결과 재사용
              if ($partTypeId) {
                $categorySql = "SELECT category_id, category_name FROM categories WHERE parent_id = {$partTypeId} AND is_active = 1 ORDER BY display_order";
              } else {
                $categorySql = "SELECT category_id, category_name FROM categories WHERE parent_id = 4 AND is_active = 1 ORDER BY display_order";
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
            <label>호환 기기</label>
            <select id="compatibleDeviceId" name="compatible_device_id" class="form-control">
              <option value="">공용</option>
              <?php
              $deviceSql = "SELECT device_id, model_name FROM devices WHERE is_active = 1 AND deleted_at IS NULL ORDER BY model_name";
              $deviceResult = mysqli_query($con, $deviceSql);
              if ($deviceResult) {
                while ($device = mysqli_fetch_assoc($deviceResult)) {
                  echo '<option value="' . htmlspecialchars($device['device_id']) . '">' . htmlspecialchars($device['model_name']) . '</option>';
                }
              }
              ?>
            </select>
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
            <label>보증 타입 *</label>
            <select id="warrantyType" name="warranty_type" class="form-control" required>
              <option value="FREE">무상</option>
              <option value="PAID">유상</option>
            </select>
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
          <textarea id="description" name="description" class="form-control" rows="3" placeholder="부품 설명"></textarea>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn" onclick="closePartModal()">취소</button>
      <button class="btn primary" onclick="savePart()">저장</button>
    </div>
  </div>
</div>

<script>
// 페이지 이름 (AJAX 호출용)
window.pageName = '<?= $pageName ?>';

// 부자재 추가
window.openAddPartModal = function() {
  document.getElementById('partFormTitle').textContent = '부자재 추가';
  document.getElementById('partForm').reset();
  document.getElementById('partId').value = '';
  document.getElementById('warrantyType').value = 'FREE';
  document.getElementById('isActive').value = '1';
  document.getElementById('stockQuantity').value = '0';
  document.getElementById('modalPart').style.display = 'flex';
}

// 부자재 수정
window.editPart = function(partId) {
  document.getElementById('partFormTitle').textContent = '부자재 수정';
  document.getElementById('partId').value = partId;

  const data = {};
  data['<?= encryptValue('action') ?>'] = 'get_part';
  data['<?= encryptValue('part_id') ?>'] = partId;

  updateAjaxContent(data, function(response) {
    if (response.result && response.item.part) {
      const p = response.item.part;
      document.getElementById('partName').value = p.part_name || '';
      document.getElementById('partNumber').value = p.part_number || '';
      document.getElementById('categoryId').value = p.category_id || '';
      document.getElementById('compatibleDeviceId').value = p.compatible_device_id || '';
      document.getElementById('price').value = p.price || '';
      document.getElementById('stockQuantity').value = p.stock_quantity || '0';
      document.getElementById('warrantyType').value = p.warranty_type || 'FREE';
      document.getElementById('description').value = p.description || '';
      document.getElementById('isActive').value = p.is_active || '1';
      document.getElementById('modalPart').style.display = 'flex';
    } else {
      alert('부자재 정보를 불러올 수 없습니다.');
    }
  }, false);
}

// 모달 닫기
window.closePartModal = function() {
  document.getElementById('modalPart').style.display = 'none';
}

// 저장
window.savePart = function() {
  const form = document.getElementById('partForm');
  if (!form.checkValidity()) {
    alert('필수 항목을 입력해주세요.');
    return;
  }

  const partId = document.getElementById('partId').value;
  const data = {};
  data['<?= encryptValue('action') ?>'] = 'save_part';
  if (partId) data['<?= encryptValue('part_id') ?>'] = partId;
  data['<?= encryptValue('part_name') ?>'] = document.getElementById('partName').value;
  data['<?= encryptValue('part_number') ?>'] = document.getElementById('partNumber').value;
  data['<?= encryptValue('category_id') ?>'] = document.getElementById('categoryId').value;
  data['<?= encryptValue('compatible_device_id') ?>'] = document.getElementById('compatibleDeviceId').value;
  data['<?= encryptValue('price') ?>'] = document.getElementById('price').value;
  data['<?= encryptValue('stock_quantity') ?>'] = document.getElementById('stockQuantity').value;
  data['<?= encryptValue('warranty_type') ?>'] = document.getElementById('warrantyType').value;
  data['<?= encryptValue('description') ?>'] = document.getElementById('description').value;
  data['<?= encryptValue('is_active') ?>'] = document.getElementById('isActive').value;

  updateAjaxContent(data, function(response) {
    if (response.result) {
      alert(response.msg || '저장되었습니다.');
      closePartModal();
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
window.applyPartFilters = function() {
  const category = document.getElementById('categoryFilter')?.value || '';
  const warranty = document.getElementById('warrantyFilter')?.value || '';
  const status = document.getElementById('statusFilter')?.value || '';
  const keyword = document.getElementById('searchKeyword')?.value || '';

  const data = {};
  data['<?= encryptValue('action') ?>'] = 'filter_parts';
  data['<?= encryptValue('category_id') ?>'] = category;
  data['<?= encryptValue('warranty_type') ?>'] = warranty;
  data['<?= encryptValue('status') ?>'] = status;
  data['<?= encryptValue('keyword') ?>'] = keyword;

  updateAjaxContent(data, function(response) {
    if (response.result && response.html) {
      document.querySelector('#partTableBody').innerHTML = response.html;

      // 페이징 업데이트
      if (response.pagination) {
        const pagingContainer = document.querySelector('.paging[data-id="#partTableBody"]');
        if (pagingContainer) {
          pagingContainer.innerHTML = response.pagination;
        }
      }

      console.log(`필터링 결과: ${response.item.count}개 부자재 표시`);
    } else {
      alert('조회에 실패했습니다.');
    }
  }, false);
}

// CSV 내보내기
window.exportPartsToCsv = function() {
  const table = document.getElementById('tblParts');
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
  link.download = `HQ_부자재관리_${dateStr}.csv`;
  link.click();
}

// ESC 키로 모달 닫기
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closePartModal();
  }
});

// 페이지 로드 시 자동 조회
applyPartFilters();
</script>
