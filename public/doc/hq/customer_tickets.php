<?php
// HQ 고객 문의 관리
// 벤더와 고객의 모든 문의를 확인하고, 본사로 이관된 문의 처리

// $con 변수는 common.php에서 이미 연결됨

// 모든 티켓 조회 (클라이언트 사이드 필터링)
// 고객명과 벤더명을 JOIN으로 가져오기
$sql = "SELECT
    t.*,
    c.customer_name,
    v.vendor_name
FROM tickets t
LEFT JOIN customers c ON t.customer_id = c.customer_id
LEFT JOIN vendors v ON t.vendor_id = v.vendor_id
ORDER BY t.created_at DESC";
$result = mysqli_query($con, $sql);

// 데이터 가져오기
$filteredTickets = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // 고객명과 벤더명이 없을 경우 기본값 설정
        if (!isset($row['customer_name']) || $row['customer_name'] === null) {
            $row['customer_name'] = '-';
        }
        if (!isset($row['vendor_name']) || $row['vendor_name'] === null) {
            $row['vendor_name'] = '-';
        }
        $filteredTickets[] = $row;
    }
}

// 통계
$totalTickets = count($filteredTickets);
$statusCounts = [];
$sourceCounts = [];
$assignedCounts = [];

foreach ($filteredTickets as $ticket) {
    $statusCounts[$ticket['status']] = ($statusCounts[$ticket['status']] ?? 0) + 1;
    $sourceCounts[$ticket['source']] = ($sourceCounts[$ticket['source']] ?? 0) + 1;
    $assignedCounts[$ticket['assigned_to']] = ($assignedCounts[$ticket['assigned_to']] ?? 0) + 1;
}

// 상태 라벨
$statusLabels = [
    'OPEN' => '접수',
    'IN_PROGRESS' => '처리중',
    'TRANSFERRED' => '이관됨',
    'RESOLVED' => '완료',
    'CLOSED' => '종료'
];

$statusBadges = [
    'OPEN' => 'badge-info',
    'IN_PROGRESS' => 'badge-warning',
    'TRANSFERRED' => 'badge-secondary',
    'RESOLVED' => 'badge-success',
    'CLOSED' => 'badge-secondary'
];

$sourceLabels = [
    'CUSTOMER' => '고객',
    'VENDOR' => '벤더'
];

$assignedLabels = [
    'HQ' => '본사',
    'VENDOR' => '벤더'
];
?>

<div class="wrap">
  <section id="sec-customer-tickets" class="card section-card-first">
    <div class="card-hd card-hd-wrap">
      <div class="card-hd-content">
        <div class="card-hd-title-area">
          <div class="card-ttl">고객 문의 관리</div>
          <div class="card-sub">벤더/고객 문의 통합 관리 (본사/영업사원 고객은 본사 처리)</div>
        </div>
        <div class="row filter-row">
          <div class="form-group-inline">
            <label>시작일</label>
            <input type="date" id="startDate" class="form-control input-w-160" value="<?php echo date('Y-m-01'); ?>">
          </div>
          <div class="form-group-inline">
            <label>종료일</label>
            <input type="date" id="endDate" class="form-control input-w-160" value="<?php echo date('Y-m-t'); ?>">
          </div>
          <div class="form-group-inline">
            <label>문의 출처</label>
            <select id="sourceFilter" class="form-control input-w-150">
              <option value="">전체</option>
              <?php foreach ($sourceLabels as $key => $label): ?>
              <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group-inline">
            <label>담당</label>
            <select id="assignedFilter" class="form-control input-w-150">
              <option value="">전체</option>
              <?php foreach ($assignedLabels as $key => $label): ?>
              <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group-inline">
            <label>상태</label>
            <select id="statusFilter" class="form-control input-w-150">
              <option value="">전체</option>
              <?php foreach ($statusLabels as $key => $label): ?>
              <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <button id="btnApplyFilter" class="btn primary" style="align-self: flex-end;">조회</button>
        </div>
      </div>
      <div class="row">
        <button id="btnExportCsv" class="btn">CSV 내보내기</button>
        <button id="btnRefresh" class="btn">새로고침</button>
      </div>
    </div>

    <!-- 필터 -->
    <div class="card-bd" style="border-bottom: 1px solid var(--border);">

    <!-- 통계 요약 -->
    <div class="card-bd" style="border-bottom: 1px solid var(--border);">
      <h3 style="margin-top: 0; margin-bottom: 16px; font-size: 16px;">문의 현황</h3>
      <div class="kpi-grid">
        <div class="kpi">
          <div class="small">총 문의 건수</div>
          <div class="v" style="color: var(--accent);"><?php echo number_format($totalTickets); ?></div>
        </div>
        <div class="kpi">
          <div class="small">본사 담당</div>
          <div class="v" style="color: var(--warn);"><?php echo number_format($assignedCounts['HQ'] ?? 0); ?></div>
        </div>
        <div class="kpi">
          <div class="small">벤더 담당</div>
          <div class="v"><?php echo number_format($assignedCounts['VENDOR'] ?? 0); ?></div>
        </div>
        <div class="kpi">
          <div class="small">처리 완료</div>
          <div class="v" style="color: var(--ok);"><?php echo number_format($statusCounts['RESOLVED'] ?? 0); ?></div>
        </div>
      </div>
    </div>

    <!-- 문의 목록 -->
    <div class="card-bd">
      <h3 style="margin-top: 0; margin-bottom: 16px; font-size: 16px;">문의 상세 내역</h3>
      <div class="table-wrap">
        <table class="table" id="tblTickets">
          <thead>
            <tr>
              <th>문의번호</th>
              <th>출처</th>
              <th>고객명</th>
              <th>벤더명</th>
              <th>제목</th>
              <th>상태</th>
              <th>담당</th>
              <th>등록일시</th>
              <th>최종수정</th>
              <th>관리</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($filteredTickets)): ?>
            <tr>
              <td colspan="10" class="table-empty">조건에 맞는 문의가 없습니다.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($filteredTickets as $ticket): ?>
            <tr data-ticket-id="<?php echo htmlspecialchars($ticket['ticket_id']); ?>"
                data-source="<?php echo htmlspecialchars($ticket['source']); ?>"
                data-assigned="<?php echo htmlspecialchars($ticket['assigned_to']); ?>"
                data-status="<?php echo htmlspecialchars($ticket['status']); ?>"
                data-created-date="<?php echo htmlspecialchars(substr($ticket['created_at'], 0, 10)); ?>">
              <td><strong><?php echo htmlspecialchars($ticket['ticket_id']); ?></strong></td>
              <td><?php echo $sourceLabels[$ticket['source']]; ?></td>
              <td><?php echo htmlspecialchars($ticket['customer_name']); ?></td>
              <td><?php echo htmlspecialchars($ticket['vendor_name']); ?></td>
              <td><?php echo htmlspecialchars($ticket['subject']); ?></td>
              <td>
                <span class="badge <?php echo $statusBadges[$ticket['status']]; ?>">
                  <?php echo $statusLabels[$ticket['status']]; ?>
                </span>
              </td>
              <td>
                <span style="font-weight: bold; color: <?php echo $ticket['assigned_to'] === 'HQ' ? 'var(--warn)' : 'var(--accent)'; ?>">
                  <?php echo $assignedLabels[$ticket['assigned_to']]; ?>
                </span>
              </td>
              <td><?php echo htmlspecialchars($ticket['created_at']); ?></td>
              <td><?php echo htmlspecialchars($ticket['updated_at']); ?></td>
              <td>
                <button class="btn-sm btn-primary btn-ticket-detail" data-ticket-id="<?php echo $ticket['ticket_id']; ?>">상세</button>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

<script>
// 클라이언트 사이드 필터링
function applyTicketFilters() {
  const startDate = document.getElementById('startDate')?.value || '';
  const endDate = document.getElementById('endDate')?.value || '';
  const source = document.getElementById('sourceFilter')?.value || '';
  const assigned = document.getElementById('assignedFilter')?.value || '';
  const status = document.getElementById('statusFilter')?.value || '';

  const rows = document.querySelectorAll('#tblTickets tbody tr[data-ticket-id]');
  let visibleCount = 0;

  rows.forEach(row => {
    const rowSource = row.dataset.source || '';
    const rowAssigned = row.dataset.assigned || '';
    const rowStatus = row.dataset.status || '';
    const rowDate = row.dataset.createdDate || '';

    // 날짜 필터
    const matchStartDate = !startDate || rowDate >= startDate;
    const matchEndDate = !endDate || rowDate <= endDate;

    // 기타 필터
    const matchSource = !source || rowSource === source;
    const matchAssigned = !assigned || rowAssigned === assigned;
    const matchStatus = !status || rowStatus === status;

    if (matchStartDate && matchEndDate && matchSource && matchAssigned && matchStatus) {
      row.style.display = '';
      visibleCount++;
    } else {
      row.style.display = 'none';
    }
  });

  console.log(`필터링 결과: ${visibleCount}개 문의 표시`);
}

// AJAX 로드 대응: 기존 이벤트 제거 후 재등록
$(document).off('click', '#btnApplyFilter').on('click', '#btnApplyFilter', applyTicketFilters);
$(document).off('change', '#startDate, #endDate, #sourceFilter, #assignedFilter, #statusFilter')
  .on('change', '#startDate, #endDate, #sourceFilter, #assignedFilter, #statusFilter', applyTicketFilters);

// CSV 내보내기 (AJAX 대응)
$(document).off('click', '#btnExportCsv').on('click', '#btnExportCsv', function() {
  const table = document.getElementById('tblTickets');
  const rows = Array.from(table.querySelectorAll('tr'));

  const csv = '\uFEFF' + rows.map(row => {
    const cells = Array.from(row.querySelectorAll('th, td'));
    return cells.slice(0, -1).map(cell => { // 관리 컬럼 제외
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
  link.download = `HQ_고객문의_${dateStr}.csv`;
  link.click();
});

// 새로고침 (AJAX 대응)
$(document).off('click', '#btnRefresh').on('click', '#btnRefresh', function() {
  location.reload();
});

// 문의 상세 보기
function viewTicketDetail(ticketId) {
  alert('문의 상세: ' + ticketId + '\n\n실제 구현 시 모달 팝업 또는 상세 페이지로 이동합니다.');
}

// 상세 버튼 이벤트 (AJAX 대응)
$(document).off('click', '.btn-ticket-detail').on('click', '.btn-ticket-detail', function() {
  const ticketId = this.dataset.ticketId;
  viewTicketDetail(ticketId);
});
</script>
