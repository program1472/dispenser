<?php
/**
 * HQ 악세사리 제품 관리
 * 악세사리 제품 정보 및 재고 관리
 */

// $con 변수는 common.php에서 이미 연결됨

// POST 핸들러 처리
if (!empty($_POST) && (isset($_POST['action']) || isset($_POST['p']))) {
    header('Content-Type: application/json; charset=utf-8');

    $action = $_POST['action'] ?? 'filter_accessories';

    switch ($action) {
        case 'filter_accessories':
            $categoryId = $_POST['category_id'] ?? '';
            $status = $_POST['status'] ?? '';
            $keyword = $_POST['keyword'] ?? '';

            $sql = "SELECT
                      a.*,
                      c.category_name,
                      creator.name as creator_name,
                      updater.name as updater_name
                    FROM accessories a
                    LEFT JOIN categories c ON a.category_id = c.category_id
                    LEFT JOIN users creator ON a.created_by = creator.user_id AND creator.deleted_at IS NULL
                    LEFT JOIN users updater ON a.updated_by = updater.user_id AND updater.deleted_at IS NULL
                    WHERE a.deleted_at IS NULL";

            if ($categoryId) {
                $categoryIdEsc = mysqli_real_escape_string($con, $categoryId);
                $sql .= " AND a.category_id = '{$categoryIdEsc}'";
            }

            if ($status !== '') {
                $statusEsc = mysqli_real_escape_string($con, $status);
                $sql .= " AND a.is_active = '{$statusEsc}'";
            }

            if ($keyword) {
                $keywordEsc = mysqli_real_escape_string($con, $keyword);
                $sql .= " AND a.accessory_name LIKE '%{$keywordEsc}%'";
            }

            // 페이징 설정
            $searchString = "a.deleted_at IS NULL";
            if ($categoryId) $searchString .= " AND a.category_id = '{$categoryIdEsc}'";
            if ($status !== '') $searchString .= " AND a.is_active = '{$statusEsc}'";
            if ($keyword) $searchString .= " AND a.accessory_name LIKE '%{$keywordEsc}%'";

            $paginationConfig = [
                'table' => 'accessories a',
                'where' => $searchString,
                'join' => 'LEFT JOIN categories c ON a.category_id = c.category_id',
                'orderBy' => 'a.created_at DESC',
                'rowsPerPage' => $defaultRowsPage,
                'targetId' => '#accessoryTableBody',
                'atValue' => encryptValue('10')
            ];

            $rowsPage = $paginationConfig['rowsPerPage'];
            $p = $_POST['p'] ?? 1;
            $curPage = $rowsPage * ($p - 1);

            $sql .= " ORDER BY a.created_at DESC, a.accessory_id DESC LIMIT {$curPage}, {$rowsPage}";
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
                        $imageHtml = '<img src="' . htmlspecialchars($row['image_url']) . '" alt="' . htmlspecialchars($row['accessory_name']) . '">';
                    } else {
                        $imageHtml = '<img src="/dispenser/public/images/no-image.png" alt="No Image">';
                    }

                    $html .= '<tr>';
                    $html .= '<td>' . htmlspecialchars($row['accessory_id']) . '</td>';
                    $html .= '<td>' . $imageHtml . '</td>';
                    $html .= '<td><strong>' . htmlspecialchars($row['accessory_name']) . '</strong></td>';
                    $html .= '<td>' . htmlspecialchars($row['category_name'] ?? '-') . '</td>';
                    $html .= '<td>' . htmlspecialchars($row['device_name'] ?? '공용') . '</td>';
                    $html .= '<td>' . number_format($row['price'] ?? 0) . '원</td>';
                    $html .= '<td>' . number_format($row['stock_quantity'] ?? 0) . '</td>';
                    $html .= '<td><span class="badge ' . $statusClass . '">' . $statusLabel . '</span></td>';
                    $html .= '<td>' . date('Y-m-d', strtotime($row['created_at'])) . '</td>';
                    $html .= '<td>';
                    $html .= '<button class="btn-sm" onclick="editAccessory(' . $row['accessory_id'] . ')">수정</button>';
                    $html .= '</td>';
                    $html .= '</tr>';
                }
            } else {
                $html = '<tr><td colspan="10" class="table-empty-state">조회된 악세사리가 없습니다.</td></tr>';
            }

            $response['result'] = true;
            $response['html'] = $html;
            $response['item'] = ['count' => $count];
            Finish();

        case 'get_accessory':
            $accessoryId = $_POST['accessory_id'] ?? '';
            if (empty($accessoryId)) {
                $response['result'] = false;
                $response['error'] = ['msg' => '악세사리 ID가 필요합니다.', 'code' => 400];
                Finish();
            }

            $accessoryIdEsc = mysqli_real_escape_string($con, $accessoryId);
            $sql = "SELECT * FROM accessories WHERE accessory_id = {$accessoryIdEsc} AND deleted_at IS NULL";
            $result = mysqli_query($con, $sql);

            if ($result && $row = mysqli_fetch_assoc($result)) {
                $response['result'] = true;
                $response['item'] = ['accessory' => $row];
            } else {
                $response['result'] = false;
                $response['error'] = ['msg' => '악세사리를 찾을 수 없습니다.', 'code' => 404];
            }
            Finish();

        case 'save_accessory':
            $accessoryId = $_POST['accessory_id'] ?? '';
            $accessoryName = $_POST['accessory_name'] ?? '';
            $categoryId = $_POST['category_id'] ?? '';
            $description = $_POST['description'] ?? '';
            $isActive = $_POST['is_active'] ?? '1';

            if (empty($accessoryName)) {
                $response['result'] = false;
                $response['error'] = ['msg' => '악세사리 이름은 필수입니다.', 'code' => 400];
                Finish();
            }

            $accessoryNameEsc = mysqli_real_escape_string($con, $accessoryName);
            $categoryIdEsc = $categoryId ? mysqli_real_escape_string($con, $categoryId) : 'NULL';
            $descriptionEsc = mysqli_real_escape_string($con, $description);
            $isActiveEsc = mysqli_real_escape_string($con, $isActive);

            if ($accessoryId) {
                // Update
                $accessoryIdEsc = mysqli_real_escape_string($con, $accessoryId);
                $sql = "UPDATE accessories SET
                        accessory_name = '{$accessoryNameEsc}',
                        category_id = " . ($categoryId ? "'{$categoryIdEsc}'" : "NULL") . ",
                        description = '{$descriptionEsc}',
                        is_active = '{$isActiveEsc}',
                        updated_at = NOW(),
                        updated_by = {$mb_no}
                        WHERE accessory_id = {$accessoryIdEsc}";
            } else {
                // Insert
                $sql = "INSERT INTO accessories (accessory_name, category_id, description, is_active, created_at, created_by)
                        VALUES ('{$accessoryNameEsc}', " . ($categoryId ? "'{$categoryIdEsc}'" : "NULL") . ", '{$descriptionEsc}', '{$isActiveEsc}', NOW(), {$mb_no})";
            }

            if (mysqli_query($con, $sql)) {
                $response['result'] = true;
                $response['msg'] = $accessoryId ? '악세사리가 수정되었습니다.' : '악세사리가 등록되었습니다.';
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

// 모든 악세사리 데이터 조회
$sql = "SELECT
          a.*,
          c.category_name,
          d.model_name as device_name,
          creator.name as creator_name,
          updater.name as updater_name
        FROM accessories a
        LEFT JOIN categories c ON a.category_id = c.category_id
        LEFT JOIN devices d ON a.device_id = d.device_id AND d.deleted_at IS NULL
        LEFT JOIN users creator ON a.created_by = creator.user_id AND creator.deleted_at IS NULL
        LEFT JOIN users updater ON a.updated_by = updater.user_id AND updater.deleted_at IS NULL
        WHERE a.deleted_at IS NULL
        ORDER BY a.created_at DESC, a.accessory_id DESC";
$result = mysqli_query($con, $sql);

// SQL 로깅
$response['item']['sql'] = $sql;

// 데이터 가져오기
$accessoriesData = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $accessoriesData[] = $row;
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
        <div class="card-ttl">악세사리 제품 관리</div>
        <div class="card-sub">악세사리 제품 정보 및 재고 관리</div>
      </div>
      <div class="filter-toolbar">
        <div class="filter-group">
          <label>카테고리</label>
          <select id="categoryFilter" class="form-control filter-select" onchange="applyAccessoryFilters()">
            <option value="">전체</option>
            <?php
            $categorySql = "SELECT category_id, category_name FROM categories WHERE parent_id = 5 AND is_active = 1 ORDER BY display_order";
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
          <label>상태</label>
          <select id="statusFilter" class="form-control filter-select" onchange="applyAccessoryFilters()">
            <option value="">전체</option>
            <option value="1">활성</option>
            <option value="0">비활성</option>
          </select>
        </div>
        <div class="filter-group">
          <label>검색</label>
          <input type="text" id="searchKeyword" class="form-control filter-input" placeholder="제품명 검색" onkeypress="if(event.key==='Enter') applyAccessoryFilters()">
        </div>
        <button id="btnApplyFilter" class="btn primary" onclick="applyAccessoryFilters()">조회</button>
        <button id="btnAddAccessory" class="btn primary" onclick="openAddAccessoryModal()">악세사리 추가</button>
      </div>
    </div>
    <div class="row">
      <button id="btnExportCsv" class="btn" onclick="exportAccessoriesToCsv()">CSV 내보내기</button>
    </div>
  </div>

  <div class="card-bd">
    <div class="table-wrap">
      <table class="tbl-list" id="tblAccessories">
        <thead>
          <tr>
            <th>ID</th>
            <th>이미지</th>
            <th>제품명</th>
            <th>카테고리</th>
            <th>호환기기</th>
            <th>가격</th>
            <th>재고</th>
            <th>상태</th>
            <th>등록일</th>
            <th>관리</th>
          </tr>
        </thead>
        <tbody id="accessoryTableBody">
        </tbody>
      </table>
    </div>

    <!-- 페이징 영역 -->
    <div class="paging" data-id="#accessoryTableBody"></div>
  </div>
</section>

<!-- 악세사리 추가/수정 모달 -->
<div id="modalAccessory" class="modal">
  <div class="modal-content modal-lg">
    <div class="modal-header">
      <h3 id="accessoryFormTitle">악세사리 추가</h3>
      <button class="modal-close" onclick="closeAccessoryModal()">&times;</button>
    </div>
    <div class="modal-body">
      <form id="accessoryForm">
        <input type="hidden" id="accessoryId" name="accessory_id">

        <div class="grid-2">
          <div class="form-group">
            <label>제품명 *</label>
            <input type="text" id="accessoryName" name="accessory_name" class="form-control" required>
          </div>

          <div class="form-group">
            <label>카테고리 *</label>
            <select id="categoryId" name="category_id" class="form-control" required>
              <option value="">선택</option>
              <?php
              $categorySql = "SELECT category_id, category_name FROM categories WHERE parent_id = 5 AND is_active = 1 ORDER BY display_order";
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
            <label>호환기기</label>
            <select id="deviceId" name="device_id" class="form-control">
              <option value="">공용</option>
              <?php
              $deviceSql = "SELECT device_id, model_name FROM devices WHERE deleted_at IS NULL AND is_active = 1 ORDER BY model_name";
              $deviceResult = mysqli_query($con, $deviceSql);
              if ($deviceResult) {
                while ($dev = mysqli_fetch_assoc($deviceResult)) {
                  echo '<option value="' . htmlspecialchars($dev['device_id']) . '">' . htmlspecialchars($dev['model_name']) . '</option>';
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
            <label>상태 *</label>
            <select id="isActive" name="is_active" class="form-control" required>
              <option value="1">활성</option>
              <option value="0">비활성</option>
            </select>
          </div>

          <div class="form-group">
            <label>이미지 URL</label>
            <input type="text" id="imageUrl" name="image_url" class="form-control" placeholder="https://...">
          </div>
        </div>

        <div class="form-group">
          <label>설명</label>
          <textarea id="description" name="description" class="form-control" rows="3" placeholder="제품 설명"></textarea>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn" onclick="closeAccessoryModal()">취소</button>
      <button class="btn primary" onclick="saveAccessory()">저장</button>
    </div>
  </div>
</div>

<script>
// 페이지 이름 (AJAX 호출용)
window.pageName = '<?= $pageName ?>';

// 악세사리 추가
window.openAddAccessoryModal = function() {
  document.getElementById('accessoryFormTitle').textContent = '악세사리 추가';
  document.getElementById('accessoryForm').reset();
  document.getElementById('accessoryId').value = '';
  document.getElementById('isActive').value = '1';
  document.getElementById('stockQuantity').value = '0';
  document.getElementById('modalAccessory').style.display = 'flex';
}

// 악세사리 수정
window.editAccessory = function(accessoryId) {
  document.getElementById('accessoryFormTitle').textContent = '악세사리 수정';
  document.getElementById('accessoryId').value = accessoryId;

  const data = {};
  data['<?= encryptValue('action') ?>'] = 'get_accessory';
  data['<?= encryptValue('accessory_id') ?>'] = accessoryId;

  updateAjaxContent(data, function(response) {
    if (response.result && response.item.accessory) {
      const acc = response.item.accessory;
      document.getElementById('accessoryName').value = acc.accessory_name || '';
      document.getElementById('categoryId').value = acc.category_id || '';
      document.getElementById('deviceId').value = acc.device_id || '';
      document.getElementById('price').value = acc.price || '';
      document.getElementById('stockQuantity').value = acc.stock_quantity || '0';
      document.getElementById('imageUrl').value = acc.image_url || '';
      document.getElementById('description').value = acc.description || '';
      document.getElementById('isActive').value = acc.is_active || '1';
      document.getElementById('modalAccessory').style.display = 'flex';
    } else {
      alert('악세사리 정보를 불러올 수 없습니다.');
    }
  }, false);
}

// 모달 닫기
window.closeAccessoryModal = function() {
  document.getElementById('modalAccessory').style.display = 'none';
}

// 저장
window.saveAccessory = function() {
  const form = document.getElementById('accessoryForm');
  if (!form.checkValidity()) {
    alert('필수 항목을 입력해주세요.');
    return;
  }

  const accessoryId = document.getElementById('accessoryId').value;
  const data = {};
  data['<?= encryptValue('action') ?>'] = 'save_accessory';
  if (accessoryId) data['<?= encryptValue('accessory_id') ?>'] = accessoryId;
  data['<?= encryptValue('accessory_name') ?>'] = document.getElementById('accessoryName').value;
  data['<?= encryptValue('category_id') ?>'] = document.getElementById('categoryId').value;
  data['<?= encryptValue('device_id') ?>'] = document.getElementById('deviceId').value;
  data['<?= encryptValue('price') ?>'] = document.getElementById('price').value;
  data['<?= encryptValue('stock_quantity') ?>'] = document.getElementById('stockQuantity').value;
  data['<?= encryptValue('image_url') ?>'] = document.getElementById('imageUrl').value;
  data['<?= encryptValue('description') ?>'] = document.getElementById('description').value;
  data['<?= encryptValue('is_active') ?>'] = document.getElementById('isActive').value;

  updateAjaxContent(data, function(response) {
    if (response.result) {
      alert(response.msg || '저장되었습니다.');
      closeAccessoryModal();
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
window.applyAccessoryFilters = function() {
  const category = document.getElementById('categoryFilter')?.value || '';
  const status = document.getElementById('statusFilter')?.value || '';
  const keyword = document.getElementById('searchKeyword')?.value || '';

  const data = {};
  data['<?= encryptValue('action') ?>'] = 'filter_accessories';
  data['<?= encryptValue('category_id') ?>'] = category;
  data['<?= encryptValue('status') ?>'] = status;
  data['<?= encryptValue('keyword') ?>'] = keyword;

  updateAjaxContent(data, function(response) {
    if (response.result && response.html) {
      document.querySelector('#accessoryTableBody').innerHTML = response.html;

      // 페이징 업데이트
      if (response.pagination) {
        const pagingContainer = document.querySelector('.paging[data-id="#accessoryTableBody"]');
        if (pagingContainer) {
          pagingContainer.innerHTML = response.pagination;
        }
      }

      console.log(`필터링 결과: ${response.item.count}개 악세사리 표시`);
    } else {
      alert('조회에 실패했습니다.');
    }
  }, false);
}

// CSV 내보내기
window.exportAccessoriesToCsv = function() {
  const table = document.getElementById('tblAccessories');
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
  link.download = `HQ_악세사리관리_${dateStr}.csv`;
  link.click();
}

// 페이지 로드 시 자동 조회
applyAccessoryFilters();
</script>
