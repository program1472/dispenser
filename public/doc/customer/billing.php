<div class="sticky-filter">
  <div class="inner">
    <span class="small" style="font-weight:700;color:var(--accent)">사업장</span>
    <select id="siteFilter" class="select"><option value="ALL">전체</option><option value="S1">본점</option><option value="S2">A지점</option><option value="S3">B지점</option></select>
    <button id="resetSeed" class="btn">테스트 데이터 재설정</button>
  </div>
</div>
<div class="wrap">
<section class="card" id="tab-Billing">
    <div class="card-hd">
      <div><div class="card-ttl">결제/구독</div><div class="card-sub">1년 구독 · 매달 자동 결제</div></div>
      <div class="small">계약: <span id="subStart">2025-01-10</span> ~ <span id="subEnd">2026-01-10</span> (잔여 <span id="subRemain">95일</span>)</div>
    </div>
    <div class="card-bd">
      <div class="kpi-grid" id="billKpis"><div class="kpi"><div class="small">이달 구독료</div><div class="v">₩29,700</div></div><div class="kpi"><div class="small">추가 사용 합계</div><div class="v">₩79,700</div></div></div>
      <table class="table" id="billTbl"><thead><tr><th>Bill ID</th><th>일자</th><th>사업장</th><th>항목</th><th>금액</th><th>상태</th><th>메모</th></tr></thead><tbody><tr><td>BILL1001</td><td>2025-09-01</td><td>본점</td><td>정기 구독료(9월)</td><td>₩29,700</td><td>PAID</td><td></td></tr><tr><td>BILL1002</td><td>2025-07-20</td><td>본점</td><td>콘텐츠(Standard)</td><td>₩50,000</td><td>PAID</td><td></td></tr></tbody></table>
    </div>
  </section>
</div>