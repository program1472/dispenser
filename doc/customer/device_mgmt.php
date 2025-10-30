<?php
// JSON 데이터 로드
$filterDataPath = DOC_ROOT . "/doc/sticky-filter.json";
$scentDataPath = DOC_ROOT . "/doc/scent.json";
$contentDataPath = DOC_ROOT . "/doc/content.json";

$filterData = json_decode(file_get_contents($filterDataPath), true);
$scentData = json_decode(file_get_contents($scentDataPath), true);
$contentData = json_decode(file_get_contents($contentDataPath), true);

// 랜덤 6개 선택 함수
function getRandomItems($arr, $count = 6) {
    shuffle($arr);
    return array_slice($arr, 0, min($count, count($arr)));
}

// 테이블 데이터
$devices = array(
    array('branch' => 'A지점', 'group' => '2층', 'location' => '회의실 1', 'serial' => 'AP5-250001', 'date' => '2025-01-10'),
    array('branch' => 'A지점', 'group' => '지하', 'location' => '전기실', 'serial' => 'AP5-250002', 'date' => '2025-01-10'),
    array('branch' => 'B지점', 'group' => '1층', 'location' => '리셉션', 'serial' => 'AP5-250003', 'date' => '2025-01-10'),
    array('branch' => 'A지점', 'group' => '1층', 'location' => '로비', 'serial' => 'AP5-250004', 'date' => '2025-01-10'),
    array('branch' => 'A지점', 'group' => '1층', 'location' => '연회장', 'serial' => 'AP5-250005', 'date' => '2025-01-10'),
    array('branch' => 'A지점', 'group' => '2층', 'location' => '라운지', 'serial' => 'AP5-250006', 'date' => '2025-01-10'),
    array('branch' => 'B지점', 'group' => '1층', 'location' => '로비', 'serial' => 'AP5-250007', 'date' => '2025-01-10'),
    array('branch' => 'B지점', 'group' => '별관', 'location' => '연회장', 'serial' => 'AP5-250008', 'date' => '2025-01-10'),
    array('branch' => 'B지점', 'group' => '옥상', 'location' => '라운지', 'serial' => 'AP5-250009', 'date' => '2025-01-10'),
);

// branch별 group, location 데이터 생성
function getBranchGroups($filterData, $branch) {
    foreach($filterData as $item) {
        if($item['branch'] === $branch) {
            return $item['groups'];
        }
    }
    return array();
}

function getGroupLocations($groups, $group) {
    foreach($groups as $g) {
        if($g['group'] === $group) {
            return $g['locations'];
        }
    }
    return array();
}

// 통계 계산
$totalDevices = count($devices);
$uniqueBranches = count(array_unique(array_column($devices, 'branch')));
$uniqueLocations = count(array_unique(array_column($devices, 'location')));

$encScent  = encryptValue($today . '/scent_lib');
$encContent  = encryptValue($today . '/content_lib');
?>

<style>
/* 고도화된 스타일 */
.enhanced-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
}

.search-box {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
    min-width: 200px;
}

.search-box input {
    flex: 1;
    min-width: 200px;
}

.action-buttons {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.table-wrapper {
    overflow-x: auto;
    margin-top: 12px;
}

.table-enhanced {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.table-enhanced thead {
    background: #f9fafb;
    position: sticky;
    top: 0;
    z-index: 10;
}

.table-enhanced th {
    padding: 12px 10px;
    border-bottom: 2px solid var(--border);
    text-align: left;
    font-weight: 700;
    color: var(--accent);
    white-space: nowrap;
    cursor: pointer;
    user-select: none;
}

.table-enhanced th:hover {
    background: #f3f4f6;
}

.table-enhanced th.sortable::after {
    content: ' ↕';
    opacity: 0.3;
    font-size: 10px;
}

.table-enhanced th.sort-asc::after {
    content: ' ↑';
    opacity: 1;
    color: var(--accent);
}

.table-enhanced th.sort-desc::after {
    content: ' ↓';
    opacity: 1;
    color: var(--accent);
}

.table-enhanced td {
    padding: 10px;
    border-bottom: 1px solid var(--border);
    vertical-align: middle;
}

.table-enhanced tbody tr {
    transition: background-color 0.15s;
}

.table-enhanced tbody tr:nth-child(even) {
    background: #fafbfc;
}

.table-enhanced tbody tr:hover {
    background: #f0fdf4;
}

.table-enhanced tbody tr.filtered-out {
    display: none;
}

.pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 16px;
    gap: 12px;
    flex-wrap: wrap;
}

.pagination-info {
    font-size: 12px;
    color: var(--muted);
}

.pagination-controls {
    display: flex;
    gap: 6px;
}

.page-btn {
    padding: 6px 12px;
    border: 1px solid var(--border);
    border-radius: 8px;
    background: #fff;
    cursor: pointer;
    font-size: 12px;
    min-width: 36px;
}

.page-btn:hover:not(:disabled) {
    background: #f9fafb;
}

.page-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.page-btn.active {
    background: var(--accent);
    color: #fff;
    border-color: var(--accent);
}

.delete-btn {
    padding: 4px 8px;
    border: 1px solid #fecaca;
    border-radius: 6px;
    background: #fef2f2;
    color: #b91c1c;
    cursor: pointer;
    font-size: 11px;
}

.delete-btn:hover {
    background: #fee2e2;
}

.icon-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

@media (max-width: 768px) {
    .enhanced-header {
        flex-direction: column;
        align-items: stretch;
    }

    .search-box {
        width: 100%;
    }

    .action-buttons {
        width: 100%;
        justify-content: flex-start;
    }
}

/* 이미지 확대 모달 */
.image-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.85);
    align-items: center;
    justify-content: center;
}

.image-modal.active {
    display: flex;
}

.image-modal-content {
    position: relative;
    max-width: 90%;
    max-height: 90%;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 16px;
}

.image-modal-img {
    max-width: 100%;
    max-height: 80vh;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
}

.image-modal-title {
    color: #fff;
    font-size: 18px;
    font-weight: 700;
    text-align: center;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
}

.image-modal-close {
    position: absolute;
    top: -40px;
    right: -40px;
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.2);
    border: 2px solid #fff;
    border-radius: 50%;
    color: #fff;
    font-size: 24px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.image-modal-close:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.1);
}

.img-item {
    cursor: pointer;
    transition: transform 0.2s;
}

.img-item:hover {
    transform: scale(1.05);
}

@media (max-width: 768px) {
    .image-modal-close {
        top: -50px;
        right: 10px;
    }
}
</style>

<!-- 통계 대시보드 -->
<div class="wrap">
    <section class="card">
        <div class="card-hd">
            <div class="card-ttl">통계 요약</div>
        </div>
        <div class="card-bd">
            <div class="kpi-grid">
                <div class="kpi">
                    <div class="small">총 기기 수</div>
                    <div class="v"><?php echo $totalDevices; ?>대</div>
                </div>
                <div class="kpi">
                    <div class="small">사업장 수</div>
                    <div class="v"><?php echo $uniqueBranches; ?>개</div>
                </div>
                <div class="kpi">
                    <div class="small">설치 장소</div>
                    <div class="v"><?php echo $uniqueLocations; ?>곳</div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- 필터 -->
<div class="sticky-filter">
    <div class="inner">
        <span class="small" style="font-weight:700;color:var(--accent)">사업장</span>
        <select id="branchSel" class="select">
            <option value="">전체</option>
            <?php foreach($filterData as $item): ?>
            <option value="<?php echo htmlspecialchars($item['branch']); ?>"><?php echo htmlspecialchars($item['branch']); ?></option>
            <?php endforeach; ?>
        </select>

        <span class="small" style="font-weight:700;color:var(--accent)">그룹</span>
        <select id="groupSel" class="select">
            <option value="">전체</option>
        </select>

        <span class="small" style="font-weight:700;color:var(--accent)">설치위치</span>
        <select id="locationSel" class="select">
            <option value="">전체</option>
        </select>

        <button id="filterBtn" class="btn primary icon-btn">
            <span>필터 적용</span>
        </button>

        <button id="resetFilter" class="btn">초기화</button>
    </div>
</div>

<div class="wrap">
    <section class="card" id="tab-Devices">
        <div class="card-hd">
            <div>
                <div class="card-ttl">기기관리</div>
                <div class="card-sub">설치일/장소/시리얼 · 선택 향/콘텐츠</div>
            </div>
            <div class="enhanced-header">
                <div class="search-box">
                    <input id="searchInput" class="input" placeholder="🔍 시리얼 번호 또는 설치 장소 검색...">
                </div>
                <div class="action-buttons">
                    <button class="btn primary icon-btn" id="exportCSV">
                        <span>📥 CSV 내보내기</span>
                    </button>
                    <button class="btn" id="resetSeed">🔄 테스트 데이터 재설정</button>
                </div>
            </div>
        </div>
        <div class="card-bd">
            <div class="table-wrapper">
                <table class="table-enhanced" id="deviceTbl">
                    <thead>
                        <tr>
                            <th class="sortable" data-sort="branch">사업장</th>
                            <th class="sortable" data-sort="group">그룹</th>
                            <th class="sortable" data-sort="location">설치 장소</th>
                            <th class="sortable" data-sort="serial">시리얼</th>
                            <th class="sortable" data-sort="date">설치일</th>
                            <th>향</th>
                            <th>콘텐츠</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody id="deviceTbody">
                        <?php foreach($devices as $idx => $device):
                            $scents = getRandomItems($scentData);
                            $contents = getRandomItems($contentData);
                            $groups = getBranchGroups($filterData, $device['branch']);
                            $locations = getGroupLocations($groups, $device['group']);
                        ?>
                        <tr data-row="<?php echo $idx; ?>">
                            <td data-branch="<?php echo htmlspecialchars($device['branch']); ?>">
                                <select class="select branch-sel" data-row="<?php echo $idx; ?>">
                                    <option value="">선택</option>
                                    <?php foreach($filterData as $item): ?>
                                    <option value="<?php echo htmlspecialchars($item['branch']); ?>" <?php echo $device['branch'] === $item['branch'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($item['branch']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td data-group="<?php echo htmlspecialchars($device['group']); ?>">
                                <select class="select group-sel" data-row="<?php echo $idx; ?>">
                                    <option value="">선택</option>
                                    <?php foreach($groups as $g): ?>
                                    <option value="<?php echo htmlspecialchars($g['group']); ?>" <?php echo $device['group'] === $g['group'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($g['group']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td data-location="<?php echo htmlspecialchars($device['location']); ?>">
                                <select class="select location-sel" data-row="<?php echo $idx; ?>">
                                    <option value="">선택</option>
                                    <?php foreach($locations as $loc): ?>
                                    <option value="<?php echo htmlspecialchars($loc['location']); ?>" <?php echo $device['location'] === $loc['location'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($loc['location']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td data-serial="<?php echo htmlspecialchars($device['serial']); ?>">
                                <strong><?php echo htmlspecialchars($device['serial']); ?></strong>
                            </td>
                            <td data-date="<?php echo htmlspecialchars($device['date']); ?>">
                                <?php echo htmlspecialchars($device['date']); ?>
                            </td>
                            <td>
                                <div class="img-row">
                                    <?php foreach($scents as $scent): ?>
                                    <div class="img-item" style="background-image:url('<?php echo htmlspecialchars($scent['img']); ?>')"
                                         data-img="<?php echo htmlspecialchars($scent['img']); ?>"
                                         data-title="<?php echo htmlspecialchars($scent['title']); ?>"
                                         onclick="openImageModal(this)">
                                        <span class="tooltip"><?php echo htmlspecialchars($scent['title']); ?></span>
                                    </div>
                                    <?php endforeach; ?>
                                    <button class="gear-btn" onclick="loadPage('<?= $encScent ?>')" title="향 설정">⚙</button>
                                </div>
                            </td>
                            <td>
                                <div class="img-row">
                                    <?php foreach($contents as $content): ?>
                                    <div class="img-item" style="background-image:url('<?php echo htmlspecialchars($content['이미지']); ?>')"
                                         data-img="<?php echo htmlspecialchars($content['이미지']); ?>"
                                         data-title="<?php echo htmlspecialchars($content['품명']); ?>"
                                         onclick="openImageModal(this)">
                                        <span class="tooltip"><?php echo htmlspecialchars($content['품명']); ?></span>
                                    </div>
                                    <?php endforeach; ?>
                                    <button class="gear-btn" onclick="loadPage('<?= $encContent ?>')" title="콘텐츠 설정">⚙</button>
                                </div>
                            </td>
                            <td>
                                <button class="delete-btn" onclick="deleteRow(<?php echo $idx; ?>)">삭제</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- 페이지네이션 -->
            <div class="pagination" id="pagination">
                <div class="pagination-info" id="paginationInfo">
                    전체 <span id="totalRows">0</span>개 중 <span id="displayedRows">0</span>개 표시
                </div>
                <div class="pagination-controls" id="paginationControls">
                    <!-- 동적으로 생성됨 -->
                </div>
            </div>

            <div class="small" style="margin-top:12px">
                ※ 향 6종 무상 제공, 설치일 기준 2개월마다 자동 공급. 첫 구입 시 1개는 고객 선택, 나머지는 랜덤 공급.
            </div>
        </div>
    </section>
</div>

<!-- 이미지 확대 모달 -->
<div class="image-modal" id="imageModal">
    <div class="image-modal-content">
        <button class="image-modal-close" onclick="closeImageModal()">×</button>
        <img class="image-modal-img" id="modalImage" src="" alt="">
        <div class="image-modal-title" id="modalTitle"></div>
    </div>
</div>

<script>
// ============================================
// 데이터 및 전역 변수
// ============================================
var filterData = <?php echo json_encode($filterData); ?>;
var currentSort = { column: null, direction: 'asc' };
var currentPage = 1;
var rowsPerPage = 10;

// ============================================
// 필터 기능
// ============================================

// 필터 셀렉트 변경 이벤트 (캐스케이딩)
document.getElementById('branchSel').addEventListener('change', function() {
    var branch = this.value;
    var groupSel = document.getElementById('groupSel');
    var locationSel = document.getElementById('locationSel');

    groupSel.innerHTML = '<option value="">전체</option>';
    locationSel.innerHTML = '<option value="">전체</option>';

    if(!branch) return;

    var branchData = filterData.find(function(item) { return item.branch === branch; });
    if(!branchData) return;

    branchData.groups.forEach(function(g) {
        var opt = document.createElement('option');
        opt.value = g.group;
        opt.textContent = g.group;
        groupSel.appendChild(opt);
    });
});

document.getElementById('groupSel').addEventListener('change', function() {
    var branch = document.getElementById('branchSel').value;
    var group = this.value;
    var locationSel = document.getElementById('locationSel');

    locationSel.innerHTML = '<option value="">전체</option>';

    if(!branch || !group) return;

    var branchData = filterData.find(function(item) { return item.branch === branch; });
    if(!branchData) return;

    var groupData = branchData.groups.find(function(g) { return g.group === group; });
    if(!groupData) return;

    groupData.locations.forEach(function(loc) {
        var opt = document.createElement('option');
        opt.value = loc.location;
        opt.textContent = loc.location;
        locationSel.appendChild(opt);
    });
});

// 필터 적용 버튼
document.getElementById('filterBtn').addEventListener('click', applyFilters);

// 필터 초기화 버튼
document.getElementById('resetFilter').addEventListener('click', function() {
    document.getElementById('branchSel').value = '';
    document.getElementById('groupSel').value = '';
    document.getElementById('locationSel').value = '';
    document.getElementById('searchInput').value = '';
    applyFilters();
});

// 필터 적용 함수
function applyFilters() {
    var branchFilter = document.getElementById('branchSel').value.toLowerCase();
    var groupFilter = document.getElementById('groupSel').value.toLowerCase();
    var locationFilter = document.getElementById('locationSel').value.toLowerCase();
    var searchQuery = document.getElementById('searchInput').value.toLowerCase();

    var rows = document.querySelectorAll('#deviceTbody tr');
    var visibleCount = 0;

    rows.forEach(function(row) {
        var branch = (row.querySelector('[data-branch]')?.dataset.branch || '').toLowerCase();
        var group = (row.querySelector('[data-group]')?.dataset.group || '').toLowerCase();
        var location = (row.querySelector('[data-location]')?.dataset.location || '').toLowerCase();
        var serial = (row.querySelector('[data-serial]')?.dataset.serial || '').toLowerCase();

        var matchBranch = !branchFilter || branch === branchFilter;
        var matchGroup = !groupFilter || group === groupFilter;
        var matchLocation = !locationFilter || location === locationFilter;
        var matchSearch = !searchQuery || serial.includes(searchQuery) || location.includes(searchQuery);

        if (matchBranch && matchGroup && matchLocation && matchSearch) {
            row.classList.remove('filtered-out');
            visibleCount++;
        } else {
            row.classList.add('filtered-out');
        }
    });

    updatePaginationInfo();
    renderPagination();
}

// ============================================
// 검색 기능
// ============================================

document.getElementById('searchInput').addEventListener('input', function() {
    applyFilters();
});

// ============================================
// 정렬 기능
// ============================================

document.querySelectorAll('.table-enhanced th.sortable').forEach(function(header) {
    header.addEventListener('click', function() {
        var column = this.dataset.sort;

        // 정렬 방향 토글
        if (currentSort.column === column) {
            currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
        } else {
            currentSort.column = column;
            currentSort.direction = 'asc';
        }

        // 헤더 스타일 업데이트
        document.querySelectorAll('.table-enhanced th').forEach(function(th) {
            th.classList.remove('sort-asc', 'sort-desc');
        });
        this.classList.add(currentSort.direction === 'asc' ? 'sort-asc' : 'sort-desc');

        sortTable(column, currentSort.direction);
    });
});

function sortTable(column, direction) {
    var tbody = document.getElementById('deviceTbody');
    var rows = Array.from(tbody.querySelectorAll('tr'));

    rows.sort(function(a, b) {
        var aVal = a.querySelector('[data-' + column + ']')?.dataset[column] || '';
        var bVal = b.querySelector('[data-' + column + ']')?.dataset[column] || '';

        if (direction === 'asc') {
            return aVal.localeCompare(bVal);
        } else {
            return bVal.localeCompare(aVal);
        }
    });

    // 재정렬된 행을 다시 추가
    rows.forEach(function(row) {
        tbody.appendChild(row);
    });
}

// ============================================
// 페이지네이션
// ============================================

function updatePaginationInfo() {
    var allRows = document.querySelectorAll('#deviceTbody tr');
    var visibleRows = document.querySelectorAll('#deviceTbody tr:not(.filtered-out)');

    document.getElementById('totalRows').textContent = allRows.length;
    document.getElementById('displayedRows').textContent = visibleRows.length;
}

function renderPagination() {
    var visibleRows = document.querySelectorAll('#deviceTbody tr:not(.filtered-out)');
    var totalPages = Math.ceil(visibleRows.length / rowsPerPage);

    var controls = document.getElementById('paginationControls');
    controls.innerHTML = '';

    // 이전 버튼
    var prevBtn = document.createElement('button');
    prevBtn.className = 'page-btn';
    prevBtn.textContent = '‹';
    prevBtn.disabled = currentPage === 1;
    prevBtn.onclick = function() {
        if (currentPage > 1) {
            currentPage--;
            showPage(currentPage);
        }
    };
    controls.appendChild(prevBtn);

    // 페이지 번호 버튼
    for (var i = 1; i <= totalPages; i++) {
        var pageBtn = document.createElement('button');
        pageBtn.className = 'page-btn' + (i === currentPage ? ' active' : '');
        pageBtn.textContent = i;
        pageBtn.dataset.page = i;
        pageBtn.onclick = function() {
            currentPage = parseInt(this.dataset.page);
            showPage(currentPage);
        };
        controls.appendChild(pageBtn);
    }

    // 다음 버튼
    var nextBtn = document.createElement('button');
    nextBtn.className = 'page-btn';
    nextBtn.textContent = '›';
    nextBtn.disabled = currentPage === totalPages || totalPages === 0;
    nextBtn.onclick = function() {
        if (currentPage < totalPages) {
            currentPage++;
            showPage(currentPage);
        }
    };
    controls.appendChild(nextBtn);

    showPage(currentPage);
}

function showPage(page) {
    var visibleRows = Array.from(document.querySelectorAll('#deviceTbody tr:not(.filtered-out)'));
    var start = (page - 1) * rowsPerPage;
    var end = start + rowsPerPage;

    visibleRows.forEach(function(row, idx) {
        if (idx >= start && idx < end) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });

    // 페이지 버튼 활성화 상태 업데이트
    document.querySelectorAll('.page-btn[data-page]').forEach(function(btn) {
        btn.classList.toggle('active', parseInt(btn.dataset.page) === page);
    });
}

// ============================================
// CSV 내보내기
// ============================================

document.getElementById('exportCSV').addEventListener('click', function() {
    var rows = document.querySelectorAll('#deviceTbody tr:not(.filtered-out)');
    var csv = [];

    // 헤더
    csv.push('사업장,그룹,설치장소,시리얼,설치일');

    // 데이터
    rows.forEach(function(row) {
        if (row.style.display !== 'none') {
            var branch = row.querySelector('[data-branch]').dataset.branch;
            var group = row.querySelector('[data-group]').dataset.group;
            var location = row.querySelector('[data-location]').dataset.location;
            var serial = row.querySelector('[data-serial]').dataset.serial;
            var date = row.querySelector('[data-date]').dataset.date;

            csv.push([branch, group, location, serial, date].join(','));
        }
    });

    var csvContent = '\uFEFF' + csv.join('\n'); // UTF-8 BOM 추가
    var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    var link = document.createElement('a');
    var url = URL.createObjectURL(blob);

    link.setAttribute('href', url);
    link.setAttribute('download', '기기관리_' + new Date().toISOString().split('T')[0] + '.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
});

// ============================================
// 행 삭제 기능
// ============================================

function deleteRow(rowIndex) {
    if (confirm('이 기기를 삭제하시겠습니까?')) {
        var row = document.querySelector('tr[data-row="' + rowIndex + '"]');
        if (row) {
            row.remove();
            updatePaginationInfo();
            renderPagination();
        }
    }
}

// ============================================
// 테이블 행별 셀렉트 체인
// ============================================

document.querySelectorAll('.branch-sel').forEach(function(branchSel) {
    var row = branchSel.closest('tr');
    var groupSel = row.querySelector('.group-sel');
    var locationSel = row.querySelector('.location-sel');

    branchSel.addEventListener('change', function() {
        var branch = this.value;
        groupSel.innerHTML = '<option value="">선택</option>';
        locationSel.innerHTML = '<option value="">선택</option>';

        if(!branch) return;

        var branchData = filterData.find(function(item) { return item.branch === branch; });
        if(!branchData) return;

        branchData.groups.forEach(function(g) {
            var opt = document.createElement('option');
            opt.value = g.group;
            opt.textContent = g.group;
            groupSel.appendChild(opt);
        });

        // 데이터 속성 업데이트
        row.querySelector('[data-branch]').dataset.branch = branch;
    });

    groupSel.addEventListener('change', function() {
        var branch = branchSel.value;
        var group = this.value;
        locationSel.innerHTML = '<option value="">선택</option>';

        if(!branch || !group) return;

        var branchData = filterData.find(function(item) { return item.branch === branch; });
        if(!branchData) return;

        var groupData = branchData.groups.find(function(g) { return g.group === group; });
        if(!groupData) return;

        groupData.locations.forEach(function(loc) {
            var opt = document.createElement('option');
            opt.value = loc.location;
            opt.textContent = loc.location;
            locationSel.appendChild(opt);
        });

        // 데이터 속성 업데이트
        row.querySelector('[data-group]').dataset.group = group;
    });

    locationSel.addEventListener('change', function() {
        row.querySelector('[data-location]').dataset.location = this.value;
    });
});

// ============================================
// 리셋 버튼
// ============================================

document.getElementById('resetSeed').addEventListener('click', function() {
    if (confirm('테스트 데이터를 재설정하시겠습니까?')) {
        location.reload();
    }
});

// ============================================
// 이미지 확대 모달
// ============================================

function openImageModal(element) {
    var imgUrl = element.getAttribute('data-img');
    var title = element.getAttribute('data-title');

    var modal = document.getElementById('imageModal');
    var modalImg = document.getElementById('modalImage');
    var modalTitle = document.getElementById('modalTitle');

    modal.classList.add('active');
    modalImg.src = imgUrl;
    modalTitle.textContent = title;

    // ESC 키로 닫기
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImageModal();
        }
    });
}

function closeImageModal() {
    var modal = document.getElementById('imageModal');
    modal.classList.remove('active');
}

// 모달 배경 클릭 시 닫기
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});

// ============================================
// 초기화
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    updatePaginationInfo();
    renderPagination();
});
</script>
