
<?php
// HQ 실적 > 영업사원
?>
<div class="wrap">
  <section id="sec-sales-perf" class="card section-card-first">
    <div class="card-hd card-hd-wrap">
      <div class="card-hd-content">
        <div class="card-hd-title-area">
          <div class="card-ttl">영업사원 실적 관리</div>
          <div class="card-sub">KPI·인센티브·신규/리뉴얼 관리</div>
        </div>
        <div class="row filter-row">
          <input type="month" id="filterMonth" class="form-control">
          <input type="text" id="searchSales" class="form-control input-w-200" placeholder="사원명 검색">
        </div>
      </div>
      <div class="row">
        <button id="btnExportCsv" class="btn">CSV 내보내기</button>
      </div>
    </div>
    <div class="card-bd">
      <div class="table-wrap">
        <table class="table" id="tblSalesPerf">
          <thead>
            <tr>
              <th>사원ID</th>
              <th>사원명</th>
              <th>신규판매</th>
              <th>리뉴얼</th>
              <th>유지율(%)</th>
              <th>KPI점수</th>
              <th>판매인센티브</th>
              <th>리뉴얼인센티브</th>
              <th>총인센티브</th>
              <th>지급상태</th>
              <th>상세</th>
            </tr>
          </thead>
          <tbody>
            <tr data-id="S001"><td>S001</td><td>김영업</td><td>5대</td><td>3건</td><td>92%</td><td>88점</td><td>₩75,000</td><td>₩90,000</td><td>₩165,000</td><td><span class="badge badge-success">PAID</span></td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="S002"><td>S002</td><td>이판매</td><td>3대</td><td>2건</td><td>85%</td><td>76점</td><td>₩45,000</td><td>₩60,000</td><td>₩105,000</td><td><span class="badge badge-info">DUE</span></td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="S003"><td>S003</td><td>박세일즈</td><td>7대</td><td>4건</td><td>95%</td><td>94점</td><td>₩105,000</td><td>₩130,000</td><td>₩235,000</td><td><span class="badge badge-success">PAID</span></td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="S004"><td>S004</td><td>최마케팅</td><td>2대</td><td>1건</td><td>78%</td><td>65점</td><td>₩30,000</td><td>₩30,000</td><td>₩60,000</td><td><span class="badge badge-warning">PLANNED</span></td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="S005"><td>S005</td><td>정영업부</td><td>6대</td><td>3건</td><td>88%</td><td>85점</td><td>₩90,000</td><td>₩90,000</td><td>₩180,000</td><td><span class="badge badge-success">PAID</span></td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="S006"><td>S006</td><td>강비즈니스</td><td>4대</td><td>2건</td><td>82%</td><td>79점</td><td>₩60,000</td><td>₩60,000</td><td>₩120,000</td><td><span class="badge badge-info">DUE</span></td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="S007"><td>S007</td><td>조영업왕</td><td>8대</td><td>5건</td><td>98%</td><td>97점</td><td>₩120,000</td><td>₩170,000</td><td>₩290,000</td><td><span class="badge badge-success">PAID</span></td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="S008"><td>S008</td><td>윤판매왕</td><td>3대</td><td>2건</td><td>80%</td><td>72점</td><td>₩45,000</td><td>₩60,000</td><td>₩105,000</td><td><span class="badge badge-warning">PLANNED</span></td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="S009"><td>S009</td><td>장세일즈맨</td><td>5대</td><td>3건</td><td>90%</td><td>86점</td><td>₩75,000</td><td>₩90,000</td><td>₩165,000</td><td><span class="badge badge-success">PAID</span></td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="S010"><td>S010</td><td>임영업러</td><td>4대</td><td>1건</td><td>75%</td><td>70점</td><td>₩60,000</td><td>₩30,000</td><td>₩90,000</td><td><span class="badge badge-info">DUE</span></td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="S011"><td>S011</td><td>한판매원</td><td>6대</td><td>4건</td><td>93%</td><td>91점</td><td>₩90,000</td><td>₩130,000</td><td>₩220,000</td><td><span class="badge badge-success">PAID</span></td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="S012"><td>S012</td><td>오세일즈</td><td>2대</td><td>1건</td><td>70%</td><td>62점</td><td>₩30,000</td><td>₩30,000</td><td>₩60,000</td><td><span class="badge badge-warning">PLANNED</span></td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="S013"><td>S013</td><td>서영업맨</td><td>7대</td><td>3건</td><td>89%</td><td>87점</td><td>₩105,000</td><td>₩90,000</td><td>₩195,000</td><td><span class="badge badge-success">PAID</span></td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="S014"><td>S014</td><td>신판매부</td><td>3대</td><td>2건</td><td>83%</td><td>75점</td><td>₩45,000</td><td>₩60,000</td><td>₩105,000</td><td><span class="badge badge-info">DUE</span></td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="S015"><td>S015</td><td>유영업팀</td><td>5대</td><td>3건</td><td>91%</td><td>88점</td><td>₩75,000</td><td>₩90,000</td><td>₩165,000</td><td><span class="badge badge-success">PAID</span></td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="S016"><td>S016</td><td>노세일즈</td><td>4대</td><td>2건</td><td>86%</td><td>80점</td><td>₩60,000</td><td>₩60,000</td><td>₩120,000</td><td><span class="badge badge-warning">PLANNED</span></td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="S017"><td>S017</td><td>문판매왕</td><td>9대</td><td>5건</td><td>97%</td><td>96점</td><td>₩135,000</td><td>₩170,000</td><td>₩305,000</td><td><span class="badge badge-success">PAID</span></td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="S018"><td>S018</td><td>배영업팀장</td><td>6대</td><td>4건</td><td>94%</td><td>92점</td><td>₩90,000</td><td>₩130,000</td><td>₩220,000</td><td><span class="badge badge-info">DUE</span></td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="S019"><td>S019</td><td>황세일즈팀</td><td>3대</td><td>1건</td><td>77%</td><td>68점</td><td>₩45,000</td><td>₩30,000</td><td>₩75,000</td><td><span class="badge badge-warning">PLANNED</span></td><td><button class="btn-sm btn-detail">상세</button></td></tr>
            <tr data-id="S020"><td>S020</td><td>안영업본부</td><td>5대</td><td>3건</td><td>90%</td><td>87점</td><td>₩75,000</td><td>₩90,000</td><td>₩165,000</td><td><span class="badge badge-success">PAID</span></td><td><button class="btn-sm btn-detail">상세</button></td></tr>
          </tbody>
          <tfoot>
            <tr>
              <th colspan="2">합계</th>
              <th>100대</th>
              <th>56건</th>
              <th>-</th>
              <th>평균 83점</th>
              <th>₩1,500,000</th>
              <th>₩1,710,000</th>
              <th>₩3,210,000</th>
              <th colspan="2"></th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </section>
</div>

<!-- 영업사원 상세 모달 -->
<div id="modalSalesDetail" class="modal" style="display:none">
  <div class="modal-content">
    <div class="modal-header">
      <h3>영업사원 상세 정보</h3>
      <button class="modal-close">&times;</button>
    </div>
    <div class="modal-body">
      <div class="detail-grid">
        <div class="detail-item"><label>사원ID:</label><span id="dtl-sales-id"></span></div>
        <div class="detail-item"><label>사원명:</label><span id="dtl-sales-name"></span></div>
        <div class="detail-item"><label>신규판매:</label><span id="dtl-new-sales"></span></div>
        <div class="detail-item"><label>리뉴얼:</label><span id="dtl-renewal"></span></div>
        <div class="detail-item"><label>유지율:</label><span id="dtl-retention"></span></div>
        <div class="detail-item"><label>KPI점수:</label><span id="dtl-kpi"></span></div>
      </div>

      <h4 style="margin-top:20px">인센티브 상세</h4>
      <table class="table">
        <thead>
          <tr><th>구분</th><th>금액</th><th>산출식</th></tr>
        </thead>
        <tbody>
          <tr><td>판매 인센티브</td><td id="dtl-sales-incentive"></td><td>신규판매 × 15,000원 (분할 1회분)</td></tr>
          <tr><td>리뉴얼 인센티브</td><td id="dtl-renewal-incentive"></td><td>리뉴얼 × 30,000원 (기본)</td></tr>
          <tr><td>총 인센티브</td><td id="dtl-total-incentive"></td><td>-</td></tr>
        </tbody>
      </table>

      <h4 style="margin-top:20px">KPI 구성 (100점 만점)</h4>
      <table class="table">
        <thead>
          <tr><th>항목</th><th>비중</th><th>달성률</th><th>득점</th></tr>
        </thead>
        <tbody>
          <tr><td>판매</td><td>40%</td><td>85%</td><td>34점</td></tr>
          <tr><td>유지</td><td>25%</td><td>92%</td><td>23점</td></tr>
          <tr><td>리뉴얼</td><td>20%</td><td>75%</td><td>15점</td></tr>
          <tr><td>보고</td><td>15%</td><td>90%</td><td>14점</td></tr>
          <tr><td colspan="3"><strong>총점</strong></td><td><strong>86점</strong></td></tr>
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
document.getElementById('searchSales')?.addEventListener('input', function() {
  const keyword = this.value.toLowerCase();
  const rows = document.querySelectorAll('#tblSalesPerf tbody tr');
  rows.forEach(row => {
    const text = row.textContent.toLowerCase();
    row.style.display = text.includes(keyword) ? '' : 'none';
  });
});

// CSV 내보내기
document.getElementById('btnExportCsv')?.addEventListener('click', () => {
  const table = document.getElementById('tblSalesPerf');
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
  link.download = 'HQ_영업사원실적_' + new Date().toISOString().slice(0,10) + '.csv';
  link.click();
});

// 상세보기
document.querySelectorAll('.btn-detail').forEach(btn => {
  btn.addEventListener('click', function() {
    const row = this.closest('tr');
    const cells = row.querySelectorAll('td');

    document.getElementById('dtl-sales-id').textContent = cells[0].textContent;
    document.getElementById('dtl-sales-name').textContent = cells[1].textContent;
    document.getElementById('dtl-new-sales').textContent = cells[2].textContent;
    document.getElementById('dtl-renewal').textContent = cells[3].textContent;
    document.getElementById('dtl-retention').textContent = cells[4].textContent;
    document.getElementById('dtl-kpi').textContent = cells[5].textContent;
    document.getElementById('dtl-sales-incentive').textContent = cells[6].textContent;
    document.getElementById('dtl-renewal-incentive').textContent = cells[7].textContent;
    document.getElementById('dtl-total-incentive').textContent = cells[8].textContent;

    document.getElementById('modalSalesDetail').style.display = 'flex';
  });
});

// 모달 닫기
document.querySelectorAll('.modal-close').forEach(btn => {
  btn.addEventListener('click', () => {
    document.getElementById('modalSalesDetail').style.display = 'none';
  });
});

// ESC 키로 모달 닫기
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    document.getElementById('modalSalesDetail').style.display = 'none';
  }
});
</script>

