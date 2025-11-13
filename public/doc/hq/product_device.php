<?php
/**
 * HQ 기기 제품 관리
 * 디스펜서 모델, 사양, 재고 관리
 */

// $con 변수는 common.php에서 이미 연결됨

// POST 핸들러 처리
if (!empty($_POST) && (isset($_POST['action']) || isset($_POST['p']))) {
    header('Content-Type: application/json; charset=utf-8');

    $action = $_POST['action'] ?? 'filter_devices';

    switch ($action) {
        case 'filter_devices':
            $manufacturer = $_POST['manufacturer'] ?? '';
            $categoryId = $_POST['category_id'] ?? '';
            $status = $_POST['status'] ?? '';
            $keyword = $_POST['keyword'] ?? '';

            // 디버깅: 받은 필터 파라미터 기록
            $response['data']['search']['filters'] = [
                'manufacturer' => $manufacturer,
                'category_id' => $categoryId,
                'status' => $status,
                'keyword' => $keyword,
                'page' => $_POST['p'] ?? 1
            ];

            $sql = "SELECT
                      d.*,
                      c.category_name,
                      creator.name as creator_name,
                      updater.name as updater_name
                    FROM devices d
                    LEFT JOIN categories c ON d.category_id = c.category_id
                    LEFT JOIN users creator ON d.created_by = creator.user_id AND creator.deleted_at IS NULL
                    LEFT JOIN users updater ON d.updated_by = updater.user_id AND updater.deleted_at IS NULL
                    WHERE d.deleted_at IS NULL";

            if ($manufacturer) {
                $manufacturerEsc = mysqli_real_escape_string($con, $manufacturer);
                $sql .= " AND d.manufacturer = '{$manufacturerEsc}'";
            }

            if ($categoryId) {
                $categoryIdEsc = mysqli_real_escape_string($con, $categoryId);
                $sql .= " AND d.category_id = '{$categoryIdEsc}'";
            }

            if ($status !== '') {
                $statusEsc = mysqli_real_escape_string($con, $status);
                $sql .= " AND d.is_active = '{$statusEsc}'";
            }

            if ($keyword) {
                $keywordEsc = mysqli_real_escape_string($con, $keyword);
                $sql .= " AND (d.model_name LIKE '%{$keywordEsc}%' OR d.manufacturer LIKE '%{$keywordEsc}%')";
            }

            // 페이징 설정
            $searchString = "d.deleted_at IS NULL";
            if ($manufacturer) $searchString .= " AND d.manufacturer = '{$manufacturerEsc}'";
            if ($categoryId) $searchString .= " AND d.category_id = '{$categoryIdEsc}'";
            if ($status !== '') $searchString .= " AND d.is_active = '{$statusEsc}'";
            if ($keyword) $searchString .= " AND (d.model_name LIKE '%{$keywordEsc}%' OR d.manufacturer LIKE '%{$keywordEsc}%')";

            $paginationConfig = [
                'table' => 'devices d',
                'where' => $searchString,
                'join' => 'LEFT JOIN categories c ON d.category_id = c.category_id',
                'orderBy' => 'd.created_at DESC',
                'rowsPerPage' => $defaultRowsPage,
                'targetId' => '#deviceTableBody',
                'atValue' => encryptValue('10')
            ];

            $rowsPage = $paginationConfig['rowsPerPage'];
            $p = $_POST['p'] ?? 1;
            $curPage = $rowsPage * ($p - 1);

            $sql .= " ORDER BY d.created_at DESC, d.device_id DESC LIMIT {$curPage}, {$rowsPage}";

            // 디버깅: 실행된 SQL 기록
            $response['data']['search']['sql'] = $sql;

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
                        $imageHtml = '<img src="' . htmlspecialchars($row['image_url']) . '" alt="' . htmlspecialchars($row['model_name']) . '">';
                    } else {
                        $imageHtml = '<img src="/dispenser/public/images/no-image.png" alt="No Image">';
                    }

                    $html .= '<tr>';
                    $html .= '<td>' . htmlspecialchars($row['device_id']) . '</td>';
                    $html .= '<td>' . $imageHtml . '</td>';
                    $html .= '<td><strong>' . htmlspecialchars($row['model_name']) . '</strong></td>';
                    $html .= '<td>' . htmlspecialchars($row['manufacturer'] ?? '-') . '</td>';
                    $html .= '<td>' . htmlspecialchars($row['category_name'] ?? '-') . '</td>';
                    $html .= '<td>' . htmlspecialchars(mb_substr($row['specifications'] ?? '-', 0, 50)) . '</td>';
                    $html .= '<td><span class="badge ' . $statusClass . '">' . $statusLabel . '</span></td>';
                    $html .= '<td>' . date('Y-m-d', strtotime($row['created_at'])) . '</td>';
                    $html .= '<td>';
                    $html .= '<button class="btn-sm" onclick="editDevice(' . $row['device_id'] . ')">수정</button>';
                    $html .= '</td>';
                    $html .= '</tr>';
                }
            } else {
                $html = '<tr><td colspan="9" class="table-empty-state">조회된 기기가 없습니다.</td></tr>';
            }

            $response['result'] = true;
            $response['html'] = $html;
            $response['item'] = ['count' => $count];
            Finish();

        case 'get_device':
            $deviceId = $_POST['device_id'] ?? '';
            if (empty($deviceId)) {
                $response['result'] = false;
                $response['error'] = ['msg' => '기기 ID가 필요합니다.', 'code' => 400];
                Finish();
            }

            $deviceIdEsc = mysqli_real_escape_string($con, $deviceId);
            $sql = "SELECT * FROM devices WHERE device_id = {$deviceIdEsc} AND deleted_at IS NULL";
            $result = mysqli_query($con, $sql);

            if ($result && $row = mysqli_fetch_assoc($result)) {
                $response['result'] = true;
                $response['item'] = ['device' => $row];
            } else {
                $response['result'] = false;
                $response['error'] = ['msg' => '기기를 찾을 수 없습니다.', 'code' => 404];
            }
            Finish();

        case 'save_device':
            $deviceId = $_POST['device_id'] ?? '';
            $modelName = $_POST['model_name'] ?? '';
            $manufacturer = $_POST['manufacturer'] ?? '';
            $categoryId = $_POST['category_id'] ?? '';
            $specifications = $_POST['specifications'] ?? '';
            $imageUrl = $_POST['image_url'] ?? '';
            $manualUrl = $_POST['manual_url'] ?? '';
            $isActive = $_POST['is_active'] ?? '1';

            if (empty($modelName)) {
                $response['result'] = false;
                $response['error'] = ['msg' => '모델명은 필수입니다.', 'code' => 400];
                Finish();
            }

            $modelNameEsc = mysqli_real_escape_string($con, $modelName);
            $manufacturerEsc = mysqli_real_escape_string($con, $manufacturer);
            $categoryIdEsc = $categoryId ? mysqli_real_escape_string($con, $categoryId) : 'NULL';
            $specificationsEsc = mysqli_real_escape_string($con, $specifications);
            $imageUrlEsc = mysqli_real_escape_string($con, $imageUrl);
            $manualUrlEsc = mysqli_real_escape_string($con, $manualUrl);
            $isActiveEsc = mysqli_real_escape_string($con, $isActive);

            if ($deviceId) {
                // Update
                $deviceIdEsc = mysqli_real_escape_string($con, $deviceId);
                $sql = "UPDATE devices SET
                        model_name = '{$modelNameEsc}',
                        manufacturer = '{$manufacturerEsc}',
                        category_id = " . ($categoryId ? "'{$categoryIdEsc}'" : "NULL") . ",
                        specifications = '{$specificationsEsc}',
                        image_url = '{$imageUrlEsc}',
                        manual_url = '{$manualUrlEsc}',
                        is_active = '{$isActiveEsc}',
                        updated_at = NOW(),
                        updated_by = {$mb_no}
                        WHERE device_id = {$deviceIdEsc}";
            } else {
                // Insert
                $sql = "INSERT INTO devices (model_name, manufacturer, category_id, specifications, image_url, manual_url, is_active, created_at, created_by)
                        VALUES ('{$modelNameEsc}', '{$manufacturerEsc}', " . ($categoryId ? "'{$categoryIdEsc}'" : "NULL") . ", '{$specificationsEsc}', '{$imageUrlEsc}', '{$manualUrlEsc}', '{$isActiveEsc}', NOW(), {$mb_no})";
            }

            if (mysqli_query($con, $sql)) {
                $response['result'] = true;
                $response['msg'] = $deviceId ? '기기가 수정되었습니다.' : '기기가 등록되었습니다.';
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

// 모든 기기 데이터 조회
$sql = "SELECT
          d.*,
          c.category_name,
          creator.name as creator_name,
          updater.name as updater_name
        FROM devices d
        LEFT JOIN categories c ON d.category_id = c.category_id
        LEFT JOIN users creator ON d.created_by = creator.user_id AND creator.deleted_at IS NULL
        LEFT JOIN users updater ON d.updated_by = updater.user_id AND updater.deleted_at IS NULL
        WHERE d.deleted_at IS NULL
        ORDER BY d.created_at DESC, d.device_id DESC";
$result = mysqli_query($con, $sql);

// SQL 로깅
$response['item']['sql'] = $sql;

// 데이터 가져오기
$devicesData = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $devicesData[] = $row;
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
        <div class="card-ttl">기기 제품 관리</div>
        <div class="card-sub">기기 모델별 제품 정보 관리</div>
      </div>
      <div class="filter-toolbar">
        <div class="filter-group">
          <label>제조사</label>
          <select id="manufacturerFilter" class="form-control filter-select" onchange="applyDeviceFilters()">
            <option value="">전체</option>
            <?php
            $manufacturerSql = "SELECT DISTINCT manufacturer FROM devices WHERE deleted_at IS NULL AND manufacturer != '' ORDER BY manufacturer";
            $response['item']['manufacturerSql'] = $manufacturerSql;
            $manufacturerResult = mysqli_query($con, $manufacturerSql);
            if ($manufacturerResult) {
              while ($mfr = mysqli_fetch_assoc($manufacturerResult)) {
                echo '<option value="' . htmlspecialchars($mfr['manufacturer']) . '">' . htmlspecialchars($mfr['manufacturer']) . '</option>';
              }
            }
            ?>
          </select>
        </div>
        <div class="filter-group">
          <label>카테고리</label>
          <select id="categoryFilter" class="form-control filter-select" onchange="applyDeviceFilters()">
            <option value="">전체</option>
            <?php
            // 동적으로 "디스펜서 타입" 카테고리 찾기
            $deviceTypeId = null;
            $typeResult = mysqli_query($con, "SELECT category_id FROM categories WHERE category_name = '디스펜서 타입' AND (parent_id = 0 OR parent_id IS NULL)");
            if ($typeResult && $typeRow = mysqli_fetch_assoc($typeResult)) {
              $deviceTypeId = $typeRow['category_id'];
            }

            if ($deviceTypeId) {
              $categorySql = "SELECT category_id, category_name FROM categories WHERE parent_id = {$deviceTypeId} AND is_active = 1 ORDER BY display_order";
            } else {
              // Fallback to hardcoded if category not found
              $categorySql = "SELECT category_id, category_name FROM categories WHERE parent_id = 3 AND is_active = 1 ORDER BY display_order";
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
          <label>상태</label>
          <select id="statusFilter" class="form-control filter-select" onchange="applyDeviceFilters()">
            <option value="">전체</option>
            <option value="1">활성</option>
            <option value="0">비활성</option>
          </select>
        </div>
        <div class="filter-group">
          <label>검색</label>
          <input type="text" id="searchKeyword" class="form-control filter-input" placeholder="모델명 검색" onkeypress="if(event.key==='Enter') applyDeviceFilters()">
        </div>
        <button id="btnApplyFilter" class="btn primary" onclick="applyDeviceFilters()">조회</button>
        <button id="btnAddDevice" class="btn primary" onclick="openAddDeviceModal()">기기 추가</button>
      </div>
    </div>
    <div class="row">
      <button id="btnExportCsv" class="btn" onclick="exportDevicesToCsv()">CSV 내보내기</button>
    </div>
  </div>

  <div class="card-bd card-bd-padding">
    <div class="table-wrap">
      <table class="tbl-list" id="tblDevices">
        <thead>
          <tr>
            <th>ID</th>
            <th>이미지</th>
            <th>모델명</th>
            <th>제조사</th>
            <th>카테고리</th>
            <th>사양</th>
            <th>상태</th>
            <th>등록일</th>
            <th>관리</th>
          </tr>
        </thead>
        <tbody id="deviceTableBody">
        </tbody>
      </table>
    </div>

    <!-- 페이징 영역 -->
    <div class="paging" data-id="#deviceTableBody"></div>
  </div>
</section>

<!-- 기기 추가/수정 모달 -->
<div id="modalDevice" class="modal">
  <div class="modal-content modal-lg">
    <div class="modal-header">
      <h3 id="deviceFormTitle">기기 추가</h3>
      <button class="modal-close" onclick="closeDeviceModal()">&times;</button>
    </div>
    <div class="modal-body">
      <form id="deviceForm">
        <input type="hidden" id="deviceId">

        <div class="grid-2">
          <div class="form-group">
            <label>모델명 *</label>
            <input type="text" id="modelName" class="form-control" required>
          </div>

          <div class="form-group">
            <label>제조사 *</label>
            <input type="text" id="manufacturer" class="form-control" value="올투그린" required>
          </div>

          <div class="form-group">
            <label>카테고리 *</label>
            <select id="categoryId" class="form-control" required>
              <option value="">선택</option>
              <?php
              // 모달용 카테고리 - 위의 동적 lookup 결과 재사용
              if ($deviceTypeId) {
                $categorySql = "SELECT category_id, category_name FROM categories WHERE parent_id = {$deviceTypeId} AND is_active = 1 ORDER BY display_order";
              } else {
                $categorySql = "SELECT category_id, category_name FROM categories WHERE parent_id = 3 AND is_active = 1 ORDER BY display_order";
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
            <label>상태 *</label>
            <select id="isActive" class="form-control" required>
              <option value="1">활성</option>
              <option value="0">비활성</option>
            </select>
          </div>

          <div class="form-group">
            <label>이미지 URL</label>
            <input type="text" id="imageUrl" class="form-control" placeholder="https://...">
          </div>

          <div class="form-group">
            <label>매뉴얼 URL</label>
            <input type="text" id="manualUrl" class="form-control" placeholder="https://...">
          </div>
        </div>

        <div class="form-group">
          <label>사양</label>
          <textarea id="specifications" class="form-control" rows="3" placeholder="화면: 10.1인치, 연결: WiFi/LTE, 전원: 12V 2A, 무게: 2.5kg"></textarea>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn" onclick="closeDeviceModal()">취소</button>
      <button class="btn primary" onclick="saveDevice()">저장</button>
    </div>
  </div>
</div>

<script>
// 페이지 이름 (AJAX 호출용)
window.pageName = '<?= $pageName ?>';

// 기기 추가
window.openAddDeviceModal = function() {
  document.getElementById('deviceFormTitle').textContent = '기기 추가';
  document.getElementById('deviceForm').reset();
  document.getElementById('deviceId').value = '';
  document.getElementById('manufacturer').value = '올투그린';
  document.getElementById('isActive').value = '1';
  document.getElementById('modalDevice').style.display = 'flex';
}

// 기기 수정
window.editDevice = function(deviceId) {
  document.getElementById('deviceFormTitle').textContent = '기기 수정';
  document.getElementById('deviceId').value = deviceId;

  // AJAX로 기기 정보 조회
  const data = {};
  data['<?= encryptValue('action') ?>'] = 'get_device';
  data['<?= encryptValue('device_id') ?>'] = deviceId;

  updateAjaxContent(data, function(response) {
    if (response.result && response.item.device) {
      const device = response.item.device;
      document.getElementById('modelName').value = device.model_name || '';
      document.getElementById('manufacturer').value = device.manufacturer || '';
      document.getElementById('categoryId').value = device.category_id || '';
      document.getElementById('specifications').value = device.specifications || '';
      document.getElementById('imageUrl').value = device.image_url || '';
      document.getElementById('manualUrl').value = device.manual_url || '';
      document.getElementById('isActive').value = device.is_active || '1';

      document.getElementById('modalDevice').style.display = 'flex';
    } else {
      alert('기기 정보를 불러올 수 없습니다.');
    }
  }, false);
}

// 모달 닫기
window.closeDeviceModal = function() {
  document.getElementById('modalDevice').style.display = 'none';
}

// 저장
window.saveDevice = function() {
  const form = document.getElementById('deviceForm');
  if (!form.checkValidity()) {
    alert('필수 항목을 입력해주세요.');
    return;
  }

  const deviceId = document.getElementById('deviceId').value;

  const data = {};
  data['<?= encryptValue('action') ?>'] = 'save_device';
  if (deviceId) {
    data['<?= encryptValue('device_id') ?>'] = deviceId;
  }
  data['<?= encryptValue('model_name') ?>'] = document.getElementById('modelName').value;
  data['<?= encryptValue('manufacturer') ?>'] = document.getElementById('manufacturer').value;
  data['<?= encryptValue('category_id') ?>'] = document.getElementById('categoryId').value;
  data['<?= encryptValue('specifications') ?>'] = document.getElementById('specifications').value;
  data['<?= encryptValue('image_url') ?>'] = document.getElementById('imageUrl').value;
  data['<?= encryptValue('manual_url') ?>'] = document.getElementById('manualUrl').value;
  data['<?= encryptValue('is_active') ?>'] = document.getElementById('isActive').value;

  updateAjaxContent(data, function(response) {
    if (response.result) {
      alert(response.msg || '저장되었습니다.');
      closeDeviceModal();
      // 현재 탭 리로드
      const firstTab = document.querySelector('#sec-product-mgmt .tab-btn-inline.active');
      if (firstTab && typeof loadProductTab === 'function') {
        const token = firstTab.getAttribute('data-token');
        loadProductTab(firstTab, token);
      } else {
        location.reload();
      }
    } else {
      alert(response.error?.msg || '저장에 실패했습니다.');
    }
  }, false);
}

// 서버 사이드 필터링
window.applyDeviceFilters = function() {
  const manufacturer = document.getElementById('manufacturerFilter')?.value || '';
  const category = document.getElementById('categoryFilter')?.value || '';
  const status = document.getElementById('statusFilter')?.value || '';
  const keyword = document.getElementById('searchKeyword')?.value || '';

  const data = {};
  data['<?= encryptValue('action') ?>'] = 'filter_devices';
  data['<?= encryptValue('manufacturer') ?>'] = manufacturer;
  data['<?= encryptValue('category_id') ?>'] = category;
  data['<?= encryptValue('status') ?>'] = status;
  data['<?= encryptValue('keyword') ?>'] = keyword;

  updateAjaxContent(data, function(response) {
    if (response.result && response.html) {
      document.querySelector('#deviceTableBody').innerHTML = response.html;

      // 페이징 업데이트
      if (response.pagination) {
        const pagingContainer = document.querySelector('.paging[data-id="#deviceTableBody"]');
        if (pagingContainer) {
          pagingContainer.innerHTML = response.pagination;
        }
      }

      console.log(`필터링 결과: ${response.item.count}개 기기 표시`);
    } else {
      alert('조회에 실패했습니다.');
    }
  }, false);
}

// CSV 내보내기
window.exportDevicesToCsv = function() {
  const table = document.getElementById('tblDevices');
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
  link.download = `HQ_기기관리_${dateStr}.csv`;
  link.click();
}

// ESC 키로 모달 닫기
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeDeviceModal();
  }
});

// 페이지 로드 시 자동 조회
applyDeviceFilters();
</script>
