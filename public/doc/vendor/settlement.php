<div class="wrap">
<section id="sec-settle" class="card">
    <div class="card-hd">
      <div><div class="card-ttl">정산(익월 15일 지급 예정)</div><div class="card-sub">벤더 40% + 인센티브 5%(목표 달성 시)</div></div>
      <div class="row">
        <input id="settleMonth" type="month" class="input">
        <button id="btnSettle" class="btn primary">정산 계산</button>
        <span class="small">※ 기간 내 <b>PAID</b>만 집계</span>
      </div>
    </div>
    <div class="card-bd grid-2">
      <div class="table-wrap"><table class="table" id="tblSettle"><thead><tr><th>Bill ID</th><th>고객</th><th>금액</th><th>벤더 40%</th><th>본사 60%</th></tr></thead><tbody></tbody></table></div>
      <div>
        <div class="kpi" style="margin-bottom:10px">
          <div class="small">정산 합계</div>
          <div class="v" id="settleVendor">₩0</div>
          <div class="small" id="settleMemo">익월 15일 지급 예정. 목표 달성 시 추가 5% 인센티브 별도 반영.</div>
        </div>
        <div class="kpi">
          <div class="small">인센티브 진행률</div>
          <div class="progress"><div id="incBar" style="width:0%"></div></div>
          <div class="small" id="incText">0 / 목표 10대</div>
        </div>
      </div>
    </div>
  </section>
</div>