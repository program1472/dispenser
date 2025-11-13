
<?php
// HQ 실적 > 벤더
?>
<div class="wrap">
  <section id="sec-vendor-perf" class="card section-card-first">
    <div class="card-hd card-hd-wrap">
      <div class="card-hd-content">
        <div class="card-hd-title-area">
          <div class="card-ttl">벤더 실적 관리</div>
          <div class="card-sub">매출·커미션·인센티브 관리</div>
        </div>
        <div class="row filter-row">
          <input type="month" id="filterMonth" class="form-control">
          <input type="text" id="searchVendor" class="form-control input-w-200" placeholder="벤더명 검색">
        </div>
      </div>
      <div class="row">
        <button id="btnExportCsv" class="btn">CSV 내보내기</button>
      </div>
    </div>
    <div class="card-bd">
      <div class="table-wrap">
        <table class="table" id="tblVendorPerf">
          <thead>
            <tr>
              <th>벤더ID</th>
              <th>벤더명</th>
              <th>거래기간</th>
              <th>유료매출</th>
              <th>커미션 (40%)</th>
              <th>인센티브 (5%)</th>
              <th>총지급액</th>
              <th>지급상태</th>
              <th>지급예정일</th>
              <th>상세</th>
            </tr>
          </thead>
          <tbody>
            <tr data-id="V001"><td>V001</td><td>서울디스펜서</td><td>2024-01~현재</td><td>₩8,500,000</td><td>₩3,400,000</td><td>₩425,000</td><td>₩3,825,000</td><td><span class="badge badge-success">PAID</span></td><td>2025-12-15</td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="V002"><td>V002</td><td>강남향기</td><td>2024-02~현재</td><td>₩7,200,000</td><td>₩2,880,000</td><td>₩360,000</td><td>₩3,240,000</td><td><span class="badge badge-info">DUE</span></td><td>2025-12-15</td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="V003"><td>V003</td><td>경기에어로마</td><td>2024-03~현재</td><td>₩6,800,000</td><td>₩2,720,000</td><td>₩340,000</td><td>₩3,060,000</td><td><span class="badge badge-warning">PLANNED</span></td><td>2026-01-15</td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="V004"><td>V004</td><td>인천프래그런스</td><td>2024-01~현재</td><td>₩9,100,000</td><td>₩3,640,000</td><td>₩455,000</td><td>₩4,095,000</td><td><span class="badge badge-success">PAID</span></td><td>2025-12-15</td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="V005"><td>V005</td><td>부산센트</td><td>2024-04~현재</td><td>₩5,400,000</td><td>₩2,160,000</td><td>₩270,000</td><td>₩2,430,000</td><td><span class="badge badge-info">DUE</span></td><td>2025-12-15</td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="V006"><td>V006</td><td>대구아로마</td><td>2024-02~현재</td><td>₩7,900,000</td><td>₩3,160,000</td><td>₩395,000</td><td>₩3,555,000</td><td><span class="badge badge-success">PAID</span></td><td>2025-12-15</td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="V007"><td>V007</td><td>광주향수</td><td>2024-05~현재</td><td>₩4,200,000</td><td>₩1,680,000</td><td>₩210,000</td><td>₩1,890,000</td><td><span class="badge badge-warning">PLANNED</span></td><td>2026-01-15</td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="V008"><td>V008</td><td>대전에센스</td><td>2024-03~현재</td><td>₩6,500,000</td><td>₩2,600,000</td><td>₩325,000</td><td>₩2,925,000</td><td><span class="badge badge-info">DUE</span></td><td>2025-12-15</td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="V009"><td>V009</td><td>울산디퓨저</td><td>2024-06~현재</td><td>₩3,800,000</td><td>₩1,520,000</td><td>₩190,000</td><td>₩1,710,000</td><td><span class="badge badge-success">PAID</span></td><td>2025-12-15</td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="V010"><td>V010</td><td>세종향기</td><td>2024-04~현재</td><td>₩5,600,000</td><td>₩2,240,000</td><td>₩280,000</td><td>₩2,520,000</td><td><span class="badge badge-warning">PLANNED</span></td><td>2026-01-15</td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="V011"><td>V011</td><td>수원센트</td><td>2024-01~현재</td><td>₩8,800,000</td><td>₩3,520,000</td><td>₩440,000</td><td>₩3,960,000</td><td><span class="badge badge-success">PAID</span></td><td>2025-12-15</td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="V012"><td>V012</td><td>성남아로마</td><td>2024-07~현재</td><td>₩3,200,000</td><td>₩1,280,000</td><td>₩160,000</td><td>₩1,440,000</td><td><span class="badge badge-info">DUE</span></td><td>2025-12-15</td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="V013"><td>V013</td><td>용인향수</td><td>2024-02~현재</td><td>₩7,400,000</td><td>₩2,960,000</td><td>₩370,000</td><td>₩3,330,000</td><td><span class="badge badge-success">PAID</span></td><td>2025-12-15</td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="V014"><td>V014</td><td>고양디퓨저</td><td>2024-05~현재</td><td>₩4,700,000</td><td>₩1,880,000</td><td>₩235,000</td><td>₩2,115,000</td><td><span class="badge badge-warning">PLANNED</span></td><td>2026-01-15</td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="V015"><td>V015</td><td>안양에센스</td><td>2024-03~현재</td><td>₩6,200,000</td><td>₩2,480,000</td><td>₩310,000</td><td>₩2,790,000</td><td><span class="badge badge-info">DUE</span></td><td>2025-12-15</td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="V016"><td>V016</td><td>부천프래그런스</td><td>2024-08~현재</td><td>₩2,900,000</td><td>₩1,160,000</td><td>₩145,000</td><td>₩1,305,000</td><td><span class="badge badge-success">PAID</span></td><td>2025-12-15</td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="V017"><td>V017</td><td>남양주향기</td><td>2024-04~현재</td><td>₩5,300,000</td><td>₩2,120,000</td><td>₩265,000</td><td>₩2,385,000</td><td><span class="badge badge-warning">PLANNED</span></td><td>2026-01-15</td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="V018"><td>V018</td><td>평택센트</td><td>2024-01~현재</td><td>₩8,300,000</td><td>₩3,320,000</td><td>₩415,000</td><td>₩3,735,000</td><td><span class="badge badge-success">PAID</span></td><td>2025-12-15</td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="V019"><td>V019</td><td>화성아로마</td><td>2024-06~현재</td><td>₩4,100,000</td><td>₩1,640,000</td><td>₩205,000</td><td>₩1,845,000</td><td><span class="badge badge-info">DUE</span></td><td>2025-12-15</td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="V020"><td>V020</td><td>김포디퓨저</td><td>2024-02~현재</td><td>₩7,600,000</td><td>₩3,040,000</td><td>₩380,000</td><td>₩3,420,000</td><td><span class="badge badge-success">PAID</span></td><td>2025-12-15</td><td><button class="btn-sm btn-detail">상세</button></td></tr>
          </tbody>
          <tfoot>
            <tr>
              <th colspan="3">합계</th>
              <th id="totalSales">₩123,500,000</th>
              <th id="totalCommission">₩49,400,000</th>
              <th id="totalIncentive">₩6,175,000</th>
              <th id="totalPayout">₩55,575,000</th>
              <th colspan="3"></th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </section>
</div>

<!-- 벤더 상세 모달 -->
<div id="modalVendorDetail" class="modal" style="display:none">
  <div class="modal-content">
    <div class="modal-header">
      <h3>벤더 상세 정보</h3>
      <button class="modal-close">&times;</button>
    </div>
    <div class="modal-body" id="modalBodyVendor">
      <div class="detail-grid">
        <div class="detail-item"><label>벤더ID:</label><span id="dtl-vendor-id"></span></div>
        <div class="detail-item"><label>벤더명:</label><span id="dtl-vendor-name"></span></div>
        <div class="detail-item"><label>거래기간:</label><span id="dtl-period"></span></div>
        <div class="detail-item"><label>유료매출:</label><span id="dtl-sales"></span></div>
        <div class="detail-item"><label>커미션 (40%):</label><span id="dtl-commission"></span></div>
        <div class="detail-item"><label>인센티브 (5%):</label><span id="dtl-incentive"></span></div>
        <div class="detail-item"><label>총지급액:</label><span id="dtl-total"></span></div>
        <div class="detail-item"><label>지급상태:</label><span id="dtl-status"></span></div>
        <div class="detail-item"><label>지급예정일:</label><span id="dtl-date"></span></div>
      </div>
      <h4 style="margin-top:20px">월별 상세 내역</h4>
      <table class="table">
        <thead>
          <tr><th>월</th><th>매출</th><th>커미션</th><th>인센티브</th><th>상태</th></tr>
        </thead>
        <tbody id="dtl-monthly">
          <tr><td>2025-11</td><td>₩850,000</td><td>₩340,000</td><td>₩42,500</td><td><span class="badge badge-success">PAID</span></td></tr>
          <tr><td>2025-10</td><td>₩920,000</td><td>₩368,000</td><td>₩46,000</td><td><span class="badge badge-success">PAID</span></td></tr>
          <tr><td>2025-09</td><td>₩780,000</td><td>₩312,000</td><td>₩39,000</td><td><span class="badge badge-success">PAID</span></td></tr>
        </tbody>
      </table>
    </div>
    <div class="modal-footer">
      <button class="btn modal-close">닫기</button>
    </div>
  </div>
</div>

<script>
// 검색 기능
document.getElementById('searchVendor')?.addEventListener('input', function() {
  const keyword = this.value.toLowerCase();
  const rows = document.querySelectorAll('#tblVendorPerf tbody tr');
  rows.forEach(row => {
    const text = row.textContent.toLowerCase();
    row.style.display = text.includes(keyword) ? '' : 'none';
  });
});

// CSV 내보내기
document.getElementById('btnExportCsv')?.addEventListener('click', () => {
  const table = document.getElementById('tblVendorPerf');
  const rows = Array.from(table.querySelectorAll('thead tr, tbody tr, tfoot tr'));

  const csv = rows.map(row => {
    const cells = Array.from(row.querySelectorAll('th, td'));
    return cells.map(cell => {
      if (cell.querySelector('button')) return '';
      const badge = cell.querySelector('.badge');
      if (badge) return badge.textContent.trim();
      return '"' + cell.textContent.trim().replace(/"/g, '""') + '"';
    }).filter(Boolean).join(',');
  }).join('\n');

  const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = 'HQ_벤더실적_' + new Date().toISOString().slice(0,10) + '.csv';
  link.click();
});

// 상세보기
document.querySelectorAll('.btn-detail').forEach(btn => {
  btn.addEventListener('click', function() {
    const row = this.closest('tr');
    const cells = row.querySelectorAll('td');

    document.getElementById('dtl-vendor-id').textContent = cells[0].textContent;
    document.getElementById('dtl-vendor-name').textContent = cells[1].textContent;
    document.getElementById('dtl-period').textContent = cells[2].textContent;
    document.getElementById('dtl-sales').textContent = cells[3].textContent;
    document.getElementById('dtl-commission').textContent = cells[4].textContent;
    document.getElementById('dtl-incentive').textContent = cells[5].textContent;
    document.getElementById('dtl-total').textContent = cells[6].textContent;
    document.getElementById('dtl-status').innerHTML = cells[7].innerHTML;
    document.getElementById('dtl-date').textContent = cells[8].textContent;

    document.getElementById('modalVendorDetail').style.display = 'flex';
  });
});

// 모달 닫기
document.querySelectorAll('.modal-close').forEach(btn => {
  btn.addEventListener('click', () => {
    document.getElementById('modalVendorDetail').style.display = 'none';
  });
});

// ESC 키로 모달 닫기
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    document.getElementById('modalVendorDetail').style.display = 'none';
  }
});
</script>

