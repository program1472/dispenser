<?php
/**
 * HQ 바코드/QR 관리
 * 시리얼 번호 생성 및 바코드/QR 코드 발행
 */

// $con 변수는 common.php에서 이미 연결됨

// 모든 시리얼 배치 데이터 조회 (필터는 JavaScript로 처리)
$sql = "SELECT * FROM serial_batches ORDER BY generated_date DESC, batch_id DESC";
$result = mysqli_query($con, $sql);

// 데이터 가져오기
$serialsData = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $serialsData[] = $row;
    }
}

// 상태 뱃지 표시 함수
function getStatusBadge($status) {
    $badges = [
        'COMPLETED' => '<span class="badge badge-success">완료</span>',
        'PROCESSING' => '<span class="badge badge-warning">처리 중</span>',
        'FAILED' => '<span class="badge badge-danger">실패</span>'
    ];
    return $badges[$status] ?? '<span class="badge badge-secondary">알 수 없음</span>';
}
?>

<section class="card">
  <div class="card-hd">
    <div style="display: flex; flex-direction: column; gap: 20px; flex: 1;">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="card-ttl">바코드/QR 관리</div>
        <div class="card-sub">시리얼 번호 생성 · 바코드 · QR 코드 관리</div>
      </div>
      <div class="row">
        <div class="form-group" style="margin-bottom: 0;">
          <label>상태</label>
          <select id="filterBatchStatus" class="form-control" style="min-width:150px;" onchange="applyBarcodeFilters()">
            <option value="">전체 상태</option>
            <option value="COMPLETED">완료</option>
            <option value="PROCESSING">처리 중</option>
            <option value="FAILED">실패</option>
          </select>
        </div>
        <div class="form-group" style="margin-bottom: 0;">
          <label>발주번호</label>
          <input type="text" id="searchOrderId" class="form-control" placeholder="발주번호 검색" style="min-width:180px;" onkeyup="applyBarcodeFilters()">
        </div>
        <button class="btn primary" onclick="applyBarcodeFilters()" style="align-self: flex-end;">검색</button>
        <button class="btn" onclick="resetBarcodeFilters()" style="align-self: flex-end;">초기화</button>
      </div>
    </div>
    <div class="row">
      <button class="btn primary" onclick="openGenerateSerialModal()">시리얼 번호 생성</button>
      <button class="btn" onclick="exportAllBarcodes()">전체 바코드 내보내기</button>
    </div>
  </div>

  <div class="card-bd">
    <!-- 시리얼 번호 형식 안내 -->
    <div style="background:#f0f7ff; border:1px solid #b3d9ff; border-radius:8px; padding:15px; margin-bottom:20px;">
      <h4 style="margin:0 0 10px 0; color:#0066cc;">📋 시리얼 번호 형식 안내</h4>
      <p style="margin:0 0 8px 0; color:#555;">
        <strong>형식:</strong> <code>{모델코드}-{연도}-{일련번호}</code>
      </p>
      <p style="margin:0; color:#555;">
        <strong>예시:</strong>
        <code>AP5S-2025-00001</code> (AP-5 Standard 모델의 2025년 첫 번째 기기)
      </p>
      <p style="margin:8px 0 0 0; color:#555;">
        <strong>QR 코드 링크:</strong>
        <code>https://dispenser.alltwogreen.com/register?serial={시리얼번호}</code>
      </p>
    </div>

    <div class="table-wrap">
      <table class="table" id="tblBarcodes">
    <thead>
      <tr>
        <th>배치 ID</th>
        <th>발주번호</th>
        <th>기기 모델</th>
        <th>생성일</th>
        <th>시리얼 개수</th>
        <th>시리얼 범위</th>
        <th>바코드</th>
        <th>QR 코드</th>
        <th>상태</th>
        <th>액션</th>
      </tr>
    </thead>
    <tbody id="barcodeTableBody">
      <?php foreach ($serialsData as $serial): ?>
        <tr data-order-id="<?= htmlspecialchars($serial['order_id']) ?>"
            data-status="<?= htmlspecialchars($serial['status']) ?>">
          <td><strong><?= htmlspecialchars($serial['batch_id']) ?></strong></td>
          <td><?= htmlspecialchars($serial['order_id']) ?></td>
          <td><?= htmlspecialchars($serial['device_model']) ?></td>
          <td><?= htmlspecialchars($serial['generated_date']) ?></td>
          <td><strong><?= number_format($serial['serial_count']) ?>개</strong></td>
          <td>
            <div style="font-size:0.85em;">
              <div><?= htmlspecialchars($serial['serial_start']) ?></div>
              <div style="color:#999;">~ <?= htmlspecialchars($serial['serial_end']) ?></div>
            </div>
          </td>
          <td>
            <?php if ($serial['barcode_generated']): ?>
              <span class="badge badge-success">✓ 생성됨</span>
            <?php else: ?>
              <span class="badge badge-secondary">미생성</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($serial['qr_generated']): ?>
              <span class="badge badge-success">✓ 생성됨</span>
            <?php else: ?>
              <span class="badge badge-secondary">미생성</span>
            <?php endif; ?>
          </td>
          <td><?= getStatusBadge($serial['status']) ?></td>
          <td>
            <div style="display:flex; gap:5px; flex-wrap:wrap;">
              <button class="btn-sm btn-info" onclick="viewSerialList('<?= $serial['batch_id'] ?>')">목록</button>
              <?php if ($serial['barcode_generated']): ?>
                <button class="btn-sm btn-secondary" onclick="downloadBarcodes('<?= $serial['batch_id'] ?>')">바코드</button>
              <?php endif; ?>
              <?php if ($serial['qr_generated']): ?>
                <button class="btn-sm btn-secondary" onclick="downloadQRCodes('<?= $serial['batch_id'] ?>')">QR</button>
              <?php endif; ?>
              <button class="btn-sm btn-primary" onclick="printLabels('<?= $serial['batch_id'] ?>')">라벨 출력</button>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
      </table>
    </div>
  </div>
</section>

<!-- 시리얼 번호 생성 모달 -->
<div id="generateSerialModal" class="modal">
  <div class="modal-content" style="max-width:600px;">
    <div class="modal-header">
      <h3>시리얼 번호 생성</h3>
      <button class="modal-close" onclick="closeGenerateSerialModal()">&times;</button>
    </div>
    <div class="modal-body">
      <form id="generateSerialForm">
        <div class="form-group">
          <label>발주번호 *</label>
          <select id="selectOrderId" class="form-control" required onchange="loadOrderDetails()">
            <option value="">발주번호를 선택하세요</option>
            <option value="PO20251101001">PO20251101001 - AP-5 Standard (500대)</option>
            <option value="PO20251028001">PO20251028001 - AP-5 Premium (300대)</option>
            <option value="PO20251025002">PO20251025002 - AP-5 Mini (200대)</option>
            <option value="PO20251020001">PO20251020001 - AP-5 Pro (150대)</option>
            <option value="PO20251015001">PO20251015001 - AP-5 Outdoor (100대)</option>
          </select>
        </div>

        <div class="form-group">
          <label>기기 모델</label>
          <input type="text" id="displayDeviceModel" class="form-control" readonly>
        </div>

        <div class="form-group">
          <label>발주 수량</label>
          <input type="text" id="displayQuantity" class="form-control" readonly>
        </div>

        <div class="form-group">
          <label>모델 코드 (시리얼 접두사) *</label>
          <input type="text" id="serialPrefix" class="form-control" placeholder="예: AP5S" required maxlength="10">
          <small style="color:#666;">시리얼 번호에 사용될 모델 코드를 입력하세요 (예: AP5S, AP5P, AP5M)</small>
        </div>

        <div class="form-group">
          <label>시작 번호</label>
          <input type="number" id="startNumber" class="form-control" value="1" min="1" required>
          <small style="color:#666;">생성할 시리얼 번호의 시작 번호 (기본값: 1)</small>
        </div>

        <div class="form-group">
          <label>생성 옵션</label>
          <div style="display:flex; gap:15px; margin-top:8px;">
            <label style="display:flex; align-items:center; gap:5px;">
              <input type="checkbox" id="generateBarcode" checked>
              <span>바코드 생성</span>
            </label>
            <label style="display:flex; align-items:center; gap:5px;">
              <input type="checkbox" id="generateQR" checked>
              <span>QR 코드 생성</span>
            </label>
          </div>
        </div>

        <div class="form-group">
          <label>미리보기</label>
          <div style="background:#f5f5f5; padding:12px; border-radius:6px; font-family:monospace;">
            <div style="margin-bottom:8px;"><strong>시작:</strong> <span id="previewStart">-</span></div>
            <div><strong>종료:</strong> <span id="previewEnd">-</span></div>
          </div>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn" id="btnCancelGenerateModal">취소</button>
      <button class="btn primary" id="btnSubmitGenerate">시리얼 번호 생성</button>
    </div>
  </div>
</div>

<!-- 시리얼 목록 모달 -->
<div id="serialListModal" class="modal">
  <div class="modal-content" style="max-width:800px;">
    <div class="modal-header">
      <h3>시리얼 번호 목록</h3>
      <button class="modal-close" onclick="closeSerialListModal()">&times;</button>
    </div>
    <div class="modal-body">
      <div style="max-height:400px; overflow-y:auto;">
        <table class="table" style="font-size:0.9em;">
          <thead>
            <tr>
              <th>번호</th>
              <th>시리얼 번호</th>
              <th>바코드</th>
              <th>QR 코드</th>
              <th>등록 상태</th>
            </tr>
          </thead>
          <tbody id="serialListBody">
            <!-- 동적으로 생성 -->
          </tbody>
        </table>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn" id="btnCloseSerialList">닫기</button>
      <button class="btn primary" id="btnExportSerialList">CSV 내보내기</button>
    </div>
  </div>
</div>

<script>
// 발주 상세 정보 로드
function loadOrderDetails() {
  const orderId = document.getElementById('selectOrderId').value;
  const orderData = {
    'PO20251101001': { model: 'AP-5 Standard', qty: 500, prefix: 'AP5S' },
    'PO20251028001': { model: 'AP-5 Premium', qty: 300, prefix: 'AP5P' },
    'PO20251025002': { model: 'AP-5 Mini', qty: 200, prefix: 'AP5M' },
    'PO20251020001': { model: 'AP-5 Pro', qty: 150, prefix: 'AP5PR' },
    'PO20251015001': { model: 'AP-5 Outdoor', qty: 100, prefix: 'AP5O' }
  };

  if (orderId && orderData[orderId]) {
    const order = orderData[orderId];
    document.getElementById('displayDeviceModel').value = order.model;
    document.getElementById('displayQuantity').value = order.qty + '대';
    document.getElementById('serialPrefix').value = order.prefix;
    updatePreview();
  } else {
    document.getElementById('displayDeviceModel').value = '';
    document.getElementById('displayQuantity').value = '';
    document.getElementById('serialPrefix').value = '';
    updatePreview();
  }
}

// 시리얼 번호 미리보기 업데이트
function updatePreview() {
  const prefix = document.getElementById('serialPrefix').value;
  const startNum = parseInt(document.getElementById('startNumber').value) || 1;
  const qty = parseInt(document.getElementById('displayQuantity').value) || 0;
  const year = new Date().getFullYear();

  if (prefix && qty > 0) {
    const startSerial = `${prefix}-${year}-${String(startNum).padStart(5, '0')}`;
    const endSerial = `${prefix}-${year}-${String(startNum + qty - 1).padStart(5, '0')}`;
    document.getElementById('previewStart').textContent = startSerial;
    document.getElementById('previewEnd').textContent = endSerial;
  } else {
    document.getElementById('previewStart').textContent = '-';
    document.getElementById('previewEnd').textContent = '-';
  }
}

// 입력 필드 변경 시 미리보기 업데이트
document.getElementById('serialPrefix')?.addEventListener('input', updatePreview);
document.getElementById('startNumber')?.addEventListener('input', updatePreview);

// 모달 열기/닫기
function openGenerateSerialModal() {
  document.getElementById('generateSerialModal').style.display = 'flex';
}

function closeGenerateSerialModal() {
  document.getElementById('generateSerialModal').style.display = 'none';
  document.getElementById('generateSerialForm').reset();
  updatePreview();
}

// 시리얼 번호 생성 제출
function submitGenerateSerial() {
  const form = document.getElementById('generateSerialForm');
  if (!form.checkValidity()) {
    alert('필수 항목을 모두 입력해주세요.');
    return;
  }

  const serialData = {
    orderId: document.getElementById('selectOrderId').value,
    prefix: document.getElementById('serialPrefix').value,
    startNumber: document.getElementById('startNumber').value,
    generateBarcode: document.getElementById('generateBarcode').checked,
    generateQR: document.getElementById('generateQR').checked
  };

  console.log('시리얼 번호 생성:', serialData);
  alert(`시리얼 번호가 생성되었습니다.\n\n배치 ID: BATCH${new Date().toISOString().split('T')[0].replace(/-/g, '')}001\n바코드: ${serialData.generateBarcode ? '생성됨' : '미생성'}\nQR 코드: ${serialData.generateQR ? '생성됨' : '미생성'}`);
  closeGenerateSerialModal();
  // TODO: AJAX로 서버에 전송
}

// 시리얼 목록 보기
function viewSerialList(batchId) {
  document.getElementById('serialListModal').style.display = 'flex';

  // 샘플 데이터 표시 (실제로는 AJAX로 로드)
  const tbody = document.getElementById('serialListBody');
  tbody.innerHTML = '';

  for (let i = 1; i <= 10; i++) {
    const serial = `AP5S-2025-${String(i).padStart(5, '0')}`;
    const row = `
      <tr>
        <td>${i}</td>
        <td><code>${serial}</code></td>
        <td><span class="badge badge-success">✓</span></td>
        <td><span class="badge badge-success">✓</span></td>
        <td><span class="badge badge-secondary">미등록</span></td>
      </tr>
    `;
    tbody.innerHTML += row;
  }

  // 10개 이상일 경우
  if (true) {
    tbody.innerHTML += `
      <tr>
        <td colspan="5" style="text-align:center; color:#999; padding:15px;">
          ... 및 ${500 - 10}개 더 (총 500개)
        </td>
      </tr>
    `;
  }
}

function closeSerialListModal() {
  document.getElementById('serialListModal').style.display = 'none';
}

// 바코드 다운로드
function downloadBarcodes(batchId) {
  alert(`바코드 다운로드: ${batchId}\n\n바코드 이미지가 ZIP 파일로 다운로드됩니다.`);
  // TODO: 바코드 ZIP 파일 생성 및 다운로드
}

// QR 코드 다운로드
function downloadQRCodes(batchId) {
  alert(`QR 코드 다운로드: ${batchId}\n\nQR 코드 이미지가 ZIP 파일로 다운로드됩니다.`);
  // TODO: QR 코드 ZIP 파일 생성 및 다운로드
}

// 라벨 출력
function printLabels(batchId) {
  alert(`라벨 출력: ${batchId}\n\n바코드와 QR 코드가 포함된 라벨이 인쇄용 PDF로 생성됩니다.\n\n• 시리얼 번호\n• 바코드\n• QR 코드\n• 모델명`);
  // TODO: 인쇄용 PDF 생성 및 다운로드
}

// 전체 바코드 내보내기
function exportAllBarcodes() {
  alert('전체 바코드/QR 코드를 CSV 파일로 내보냅니다.');
  // TODO: CSV 생성 및 다운로드
}

// 클라이언트 사이드 필터링
function applyBarcodeFilters() {
  const orderId = document.getElementById('searchOrderId').value.toLowerCase();
  const status = document.getElementById('filterBatchStatus').value;

  const rows = document.querySelectorAll('#barcodeTableBody tr');
  let visibleCount = 0;

  rows.forEach(row => {
    const rowOrderId = (row.dataset.orderId || '').toLowerCase();
    const rowStatus = row.dataset.status;

    const matchOrderId = !orderId || rowOrderId.includes(orderId);
    const matchStatus = !status || rowStatus === status;

    if (matchOrderId && matchStatus) {
      row.style.display = '';
      visibleCount++;
    } else {
      row.style.display = 'none';
    }
  });

  console.log(`필터링 결과: ${visibleCount}개 배치 표시`);
}

// 필터 초기화
function resetBarcodeFilters() {
  document.getElementById('searchOrderId').value = '';
  document.getElementById('filterBatchStatus').value = '';
  applyBarcodeFilters();
}

// 시리얼 목록 내보내기
function exportSerialList() {
  alert('시리얼 번호 목록이 CSV 형식으로 내보내집니다.');
  // TODO: CSV 생성 및 다운로드
}

// 필터 적용
function applyBarcodeFilters() {
  const status = document.getElementById('filterBatchStatus').value;
  const orderId = document.getElementById('searchOrderId').value;

  const params = new URLSearchParams();
  if (status) params.append('status', status);
  if (orderId) params.append('order_id', orderId);

  // 현재 탭 페이지 리로드
  const tabBtn = document.querySelector('.tab-btn-inline.active');
  if (tabBtn && typeof loadPurchaseTab === 'function') {
    loadPurchaseTab(tabBtn, 'purchase_barcode?' + params.toString());
  } else {
    window.location.href = '?page=purchase_barcode&' + params.toString();
  }
}

// 필터 초기화
function resetBarcodeFilters() {
  document.getElementById('filterBatchStatus').value = '';
  document.getElementById('searchOrderId').value = '';

  // 현재 탭 페이지 리로드
  const tabBtn = document.querySelector('.tab-btn-inline.active');
  if (tabBtn && typeof loadPurchaseTab === 'function') {
    loadPurchaseTab(tabBtn, 'purchase_barcode');
  } else {
    window.location.href = '?page=purchase_barcode';
  }
}
</script>
