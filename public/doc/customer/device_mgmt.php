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
$encScent  = encryptValue($today . '/scent_lib');
$encContent  = encryptValue($today . '/content_lib');
?>

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
        
        <button id="resetSeed" class="btn">테스트 데이터 재설정</button>
    </div>
</div>

<div class="wrap">
    <section class="card" id="tab-Devices">
        <div class="card-hd">
            <div>
                <div class="card-ttl">기기관리</div>
                <div class="card-sub">설치일/장소/시리얼 · 선택 향/콘텐츠</div>
            </div>
            <div style="display:flex;gap:8px;align-items:center">
                <input id="placeInput" class="input" placeholder="설치 장소 추가 (예: 로비)">
                <button class="btn" id="addPlaceBtn">추가</button>
            </div>
        </div>
        <div class="card-bd">
            <table class="table" id="deviceTbl">
                <thead>
                    <tr>
                        <th>사업장</th>
                        <th>그룹</th>
                        <th>설치 장소</th>
                        <th>시리얼</th>
                        <th>설치일</th>
                        <th>향</th>
                        <th>콘텐츠</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($devices as $idx => $device): 
                        $scents = getRandomItems($scentData);
                        $contents = getRandomItems($contentData);
                        $groups = getBranchGroups($filterData, $device['branch']);
                        $locations = getGroupLocations($groups, $device['group']);
                    ?>
                    <tr>
                        <td>
                            <select class="select branch-sel" data-row="<?php echo $idx; ?>">
                                <option value="">선택</option>
                                <?php foreach($filterData as $item): ?>
                                <option value="<?php echo htmlspecialchars($item['branch']); ?>" <?php echo $device['branch'] === $item['branch'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($item['branch']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <select class="select group-sel" data-row="<?php echo $idx; ?>">
                                <option value="">선택</option>
                                <?php foreach($groups as $g): ?>
                                <option value="<?php echo htmlspecialchars($g['group']); ?>" <?php echo $device['group'] === $g['group'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($g['group']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <select class="select location-sel" data-row="<?php echo $idx; ?>">
                                <option value="">선택</option>
                                <?php foreach($locations as $loc): ?>
                                <option value="<?php echo htmlspecialchars($loc['location']); ?>" <?php echo $device['location'] === $loc['location'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($loc['location']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><?php echo htmlspecialchars($device['serial']); ?></td>
                        <td><?php echo htmlspecialchars($device['date']); ?></td>
                        <td>
                            <div class="img-row">
								<?php foreach($scents as $scent): ?>
                                <div class="img-item" style="background-image:url('<?php echo htmlspecialchars($scent['img']); ?>')">
                                    <span class="tooltip"><?php echo htmlspecialchars($scent['title']); ?></span>
                                </div>
                                <?php endforeach; ?>
                                <button class="gear-btn" onclick="loadPage('<?= $encScent ?>')">⚙</button>
                            </div>
                        </td>
                        <td>
                            <div class="img-row">
                                <?php foreach($contents as $content): ?>
                                <div class="img-item" style="background-image:url('<?php echo htmlspecialchars($content['이미지']); ?>')">
                                    <span class="tooltip"><?php echo htmlspecialchars($content['품명']); ?></span>
                                </div>
                                <?php endforeach; ?>
                                <button class="gear-btn" onclick="loadPage('<?= $encContent ?>')">⚙</button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="small" style="margin-top:8px">※ 향 6종 무상 제공, 설치일 기준 2개월마다 자동 공급. 첫 구입 시 1개는 고객 선택, 나머지는 랜덤 공급.</div>
        </div>
    </section>
</div>

<script>
var filterData = <?php echo json_encode($filterData); ?>;

// 필터 셀렉트 변경 이벤트
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

// 테이블 행별 셀렉트 체인
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
    });
});

// 리셋 버튼
document.getElementById('resetSeed').addEventListener('click', function() {
    location.reload();
});
</script>