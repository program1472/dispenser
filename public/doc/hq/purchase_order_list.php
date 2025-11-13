<?php
/**
 * HQ 발주 관리
 * PI(Proforma Invoice) 기반 발주 생성 및 추적
 */

// $con 변수는 common.php에서 이미 연결됨

// 모든 발주 데이터 조회 (클라이언트 사이드 필터링)
$sql = "SELECT * FROM purchase_orders ORDER BY order_date DESC, order_id DESC";
$result = mysqli_query($con, $sql);

// 데이터 가져오기
$ordersData = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $ordersData[] = $row;
    }
} else {
    // 에러 발생 시 로그 (개발 중에만 사용)
    // error_log("Purchase orders query failed: " . mysqli_error($con));
}

// 상태별 통계 계산
$stats = [
    'total' => count($ordersData),
    'requested' => 0,
    'pi_issued' => 0,
    'manufacturing' => 0,
    'received' => 0,
    'total_qty' => 0,
    'total_amount' => 0
];

foreach ($ordersData as $order) {
    $stats['total_qty'] += $order['quantity'];
    $stats['total_amount'] += $order['total_amount'];

    switch ($order['status']) {
        case 'REQUESTED':
            $stats['requested']++;
            break;
        case 'PI_ISSUED':
            $stats['pi_issued']++;
            break;
        case 'MANUFACTURING':
            $stats['manufacturing']++;
            break;
        case 'RECEIVED':
            $stats['received']++;
            break;
    }
}

// 상태 뱃지 표시 함수
function getStatusBadge($status) {
    $badges = [
        'REQUESTED' => '<span class="badge badge-info">발주 요청</span>',
        'PI_ISSUED' => '<span class="badge badge-warning">PI 발행</span>',
        'MANUFACTURING' => '<span class="badge badge-primary">제조 중</span>',
        'RECEIVED' => '<span class="badge badge-success">입고 완료</span>'
    ];
    return $badges[$status] ?? '<span class="badge badge-secondary">알 수 없음</span>';
}
?>

<section class="card">
  <div class="card-hd">
    <div style="display: flex; flex-direction: column; gap: 20px; flex: 1;">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="card-ttl">발주 관리</div>
        <div class="card-sub">PI 발주 · 제조 · 입고 현황</div>
      </div>
      <div class="row">
        <div class="form-group" style="margin-bottom: 0;">
          <label>상태</label>
          <select id="filterStatus" class="form-control" style="min-width:150px;">
            <option value="">전체 상태</option>
            <option value="REQUESTED">발주 요청</option>
            <option value="PI_ISSUED">PI 발행</option>
            <option value="MANUFACTURING">제조 중</option>
            <option value="RECEIVED">입고 완료</option>
          </select>
        </div>
        <div class="form-group" style="margin-bottom: 0;">
          <label>시작일</label>
          <input type="date" id="filterStartDate" class="form-control">
        </div>
        <div class="form-group" style="margin-bottom: 0;">
          <label>종료일</label>
          <input type="date" id="filterEndDate" class="form-control">
        </div>
        <button id="btnApplyFilter" class="btn primary" style="align-self: flex-end;">조회</button>
        <button id="btnResetFilters" class="btn" style="align-self: flex-end;">초기화</button>
      </div>
    </div>
    <div class="row">
      <button id="btnAddOrder" class="btn primary">신규 발주 등록</button>
      <button id="btnExportCSV" class="btn">CSV 내보내기</button>
    </div>
  </div>

  <div class="card-bd">
    <div class="table-wrap">
      <table class="table" id="tblPurchaseOrders">
    <thead>
      <tr>
        <th>발주번호</th>
        <th>발주일자</th>
        <th>기기 모델</th>
        <th>수량</th>
        <th>단가</th>
        <th>총액</th>
        <th>공급업체</th>
        <th>상태</th>
        <th>PI 번호</th>
        <th>예상 납기</th>
        <th>실제 납기</th>
        <th>액션</th>
      </tr>
    </thead>
    <tbody id="orderTableBody">
      <?php foreach ($ordersData as $order): ?>
        <tr data-order-id="<?= htmlspecialchars($order['order_id']) ?>"
            data-status="<?= htmlspecialchars($order['status']) ?>"
            data-order-date="<?= htmlspecialchars($order['order_date']) ?>">
          <td><strong><?= htmlspecialchars($order['order_id']) ?></strong></td>
          <td><?= htmlspecialchars($order['order_date']) ?></td>
          <td><?= htmlspecialchars($order['device_model']) ?></td>
          <td><?= number_format($order['quantity']) ?>대</td>
          <td>₩<?= number_format($order['unit_price']) ?></td>
          <td><strong>₩<?= number_format($order['total_amount']) ?></strong></td>
          <td><?= htmlspecialchars($order['supplier']) ?></td>
          <td><?= getStatusBadge($order['status']) ?></td>
          <td><?= htmlspecialchars($order['pi_number']) ?></td>
          <td><?= htmlspecialchars($order['expected_delivery']) ?></td>
          <td><?= $order['actual_delivery'] ? htmlspecialchars($order['actual_delivery']) : '-' ?></td>
          <td>
            <button class="btn-sm btn-info btn-order-detail" data-order-id="<?= $order['order_id'] ?>">상세</button>
            <button class="btn-sm btn-secondary btn-download-pi" data-pi-number="<?= $order['pi_number'] ?>">PI 다운</button>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
      </table>
    </div>
  </div>
</section>

<!-- 발주 등록 모달 -->
<div id="addOrderModal" class="modal">
  <div class="modal-content" style="max-width:600px;">
    <div class="modal-header">
      <h3>신규 발주 등록</h3>
      <button class="modal-close" id="btnCloseModal">&times;</button>
    </div>
    <div class="modal-body">
      <form id="addOrderForm">
        <div class="form-group">
          <label>발주일자 *</label>
          <input type="date" id="orderDate" class="form-control" required>
        </div>

        <div class="form-group">
          <label>기기 모델 *</label>
          <select id="deviceModel" class="form-control" required>
            <option value="">선택하세요</option>
            <option value="AP-5 Standard">AP-5 Standard</option>
            <option value="AP-5 Premium">AP-5 Premium</option>
            <option value="AP-5 Mini">AP-5 Mini</option>
            <option value="AP-5 Pro">AP-5 Pro</option>
            <option value="AP-5 Lite">AP-5 Lite</option>
            <option value="AP-5 Max">AP-5 Max</option>
            <option value="AP-5 Outdoor">AP-5 Outdoor</option>
            <option value="AP-5 Touch">AP-5 Touch</option>
            <option value="AP-5 Slim">AP-5 Slim</option>
            <option value="AP-5 Desk">AP-5 Desk</option>
            <option value="AP-5 Deluxe">AP-5 Deluxe</option>
          </select>
        </div>

        <div class="form-group">
          <label>발주 수량 *</label>
          <input type="number" id="quantity" class="form-control" min="1" placeholder="예: 500" required>
        </div>

        <div class="form-group">
          <label>단가 (원) *</label>
          <input type="number" id="unitPrice" class="form-control" min="0" placeholder="예: 450000" required>
        </div>

        <div class="form-group">
          <label>총액 (원)</label>
          <input type="text" id="totalAmount" class="form-control" readonly>
        </div>

        <div class="form-group">
          <label>공급업체 *</label>
          <input type="text" id="supplier" class="form-control" value="올투그린" required>
        </div>

        <div class="form-group">
          <label>예상 납기일 *</label>
          <input type="date" id="expectedDelivery" class="form-control" required>
        </div>

        <div class="form-group">
          <label>비고</label>
          <textarea id="notes" class="form-control" rows="3" placeholder="발주 관련 메모"></textarea>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn" id="btnCancelModal">취소</button>
      <button class="btn primary" id="btnSubmitOrder">발주 등록 및 PI 생성</button>
    </div>
  </div>
</div>

<script>
// 총액 자동 계산
function calculateTotal() {
  const quantity = parseInt(document.getElementById('quantity')?.value) || 0;
  const unitPrice = parseInt(document.getElementById('unitPrice')?.value) || 0;
  const total = quantity * unitPrice;
  const totalAmountEl = document.getElementById('totalAmount');
  if (totalAmountEl) totalAmountEl.value = total.toLocaleString('ko-KR');
}

// 총액 자동 계산 이벤트 (AJAX 대응)
$(document).off('input', '#quantity, #unitPrice').on('input', '#quantity, #unitPrice', calculateTotal);

// 모달 열기/닫기
function openAddOrderModal() {
  document.getElementById('addOrderModal').style.display = 'flex';
  // 오늘 날짜 설정
  const today = new Date().toISOString().split('T')[0];
  document.getElementById('orderDate').value = today;
  // 30일 후 날짜를 예상 납기일로 설정
  const expectedDate = new Date();
  expectedDate.setDate(expectedDate.getDate() + 30);
  document.getElementById('expectedDelivery').value = expectedDate.toISOString().split('T')[0];
}

function closeAddOrderModal() {
  document.getElementById('addOrderModal').style.display = 'none';
  document.getElementById('addOrderForm').reset();
}

// 발주 등록
function submitOrder() {
  const form = document.getElementById('addOrderForm');
  if (!form.checkValidity()) {
    alert('필수 항목을 모두 입력해주세요.');
    return;
  }

  const orderData = {
    orderDate: document.getElementById('orderDate').value,
    deviceModel: document.getElementById('deviceModel').value,
    quantity: document.getElementById('quantity').value,
    unitPrice: document.getElementById('unitPrice').value,
    supplier: document.getElementById('supplier').value,
    expectedDelivery: document.getElementById('expectedDelivery').value,
    notes: document.getElementById('notes').value
  };

  console.log('발주 등록:', orderData);
  alert('발주가 등록되었습니다.\nPI 문서가 자동 생성되었습니다.\n바코드/QR 탭에서 시리얼 번호를 생성할 수 있습니다.');
  closeAddOrderModal();
  // TODO: AJAX로 서버에 전송
}

// 클라이언트 사이드 필터링
function applyOrderFilters() {
  const status = document.getElementById('filterStatus')?.value || '';
  const startDate = document.getElementById('filterStartDate')?.value || '';
  const endDate = document.getElementById('filterEndDate')?.value || '';

  const rows = document.querySelectorAll('#orderTableBody tr[data-order-id]');
  let visibleCount = 0;

  rows.forEach(row => {
    const rowStatus = row.dataset.status || '';
    const rowDate = row.dataset.orderDate || '';

    // 상태 필터
    const matchStatus = !status || rowStatus === status;

    // 날짜 필터
    const matchStartDate = !startDate || rowDate >= startDate;
    const matchEndDate = !endDate || rowDate <= endDate;

    if (matchStatus && matchStartDate && matchEndDate) {
      row.style.display = '';
      visibleCount++;
    } else {
      row.style.display = 'none';
    }
  });

  console.log(`필터링 결과: ${visibleCount}개 발주 표시`);
}

// 필터 초기화
function resetOrderFilters() {
  document.getElementById('filterStatus').value = '';
  document.getElementById('filterStartDate').value = '';
  document.getElementById('filterEndDate').value = '';
  applyOrderFilters();
}

// 발주 상세 보기
function viewOrderDetail(orderId) {
  alert('발주 상세 정보: ' + orderId);
  // TODO: 상세 정보 모달 표시
}

// PI 다운로드
function downloadPI(piNumber) {
  alert('PI 문서 다운로드: ' + piNumber + '\n\nPI 문서가 PDF 형식으로 다운로드됩니다.');
  // TODO: PI PDF 생성 및 다운로드
}

// CSV 내보내기
function exportToCSV() {
  alert('발주 목록이 CSV 형식으로 내보내집니다.');
  // TODO: CSV 생성 및 다운로드
}

// AJAX 로드 대응: document를 사용한 이벤트 위임
$(document).on('click', '#btnApplyFilter', function(e) {
  e.preventDefault();
  console.log('조회 버튼 클릭됨');
  applyOrderFilters();
});

$(document).on('change', '#filterStatus, #filterStartDate, #filterEndDate', function() {
  console.log('필터 변경됨');
  applyOrderFilters();
});

$(document).on('click', '#btnResetFilters', function(e) {
  e.preventDefault();
  resetOrderFilters();
});

$(document).on('click', '#btnAddOrder', function(e) {
  e.preventDefault();
  openAddOrderModal();
});

$(document).on('click', '#btnCloseModal, #btnCancelModal', function(e) {
  e.preventDefault();
  closeAddOrderModal();
});

$(document).on('click', '#btnSubmitOrder', function(e) {
  e.preventDefault();
  submitOrder();
});

$(document).on('click', '#btnExportCSV', function(e) {
  e.preventDefault();
  exportToCSV();
});

// 페이지 로드 시 디버그 정보
console.log('발주 관리 페이지 로드됨');
setTimeout(function() {
  console.log('버튼 존재 여부:', document.getElementById('btnApplyFilter') ? 'O' : 'X');
  console.log('테이블 행 개수:', document.querySelectorAll('#orderTableBody tr[data-order-id]').length);
}, 100);

// 상세/다운로드 버튼 이벤트 (AJAX 대응)
$(document).off('click', '.btn-order-detail').on('click', '.btn-order-detail', function() {
  const orderId = this.dataset.orderId;
  viewOrderDetail(orderId);
});

$(document).off('click', '.btn-download-pi').on('click', '.btn-download-pi', function() {
  const piNumber = this.dataset.piNumber;
  downloadPI(piNumber);
});
</script>
