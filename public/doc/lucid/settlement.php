<div class="wrap">
<section id="sec-settle" class="card">
    <div class="card-hd">
      <div><div class="card-ttl">정산</div><div class="card-sub">전월 완료분만 집계 · 지급일 자동 계산(익월 15일)</div></div>
      <div class="small">※ 루시드 몫 기준 합계만 표시 (비율·본사 매출 표기 없음)</div>
      <div style="display:flex;gap:8px;align-items:center">
        <input id="settleMonth" type="month" class="input">
        <button id="btnSettle" class="btn primary">정산 계산</button>
      </div>
    </div>
    <div class="card-bd grid-2">
      <div class="table-wrap"><table class="table" id="tblSettle"><thead><tr><th>완료일</th><th>고객</th><th>항목</th><th>금액(루시드)</th></tr></thead><tbody><tr><td>2025-09-05</td><td>고객2</td><td>디자인 6</td><td>₩50,000</td></tr><tr><td>2025-09-05</td><td>고객5</td><td>디자인 14</td><td>₩30,000</td></tr><tr><td>2025-09-08</td><td>고객1</td><td>디자인 3</td><td>₩40,000</td></tr><tr><td>2025-09-09</td><td>고객4</td><td>디자인 12</td><td>₩50,000</td></tr><tr><td>2025-09-10</td><td>고객1</td><td>디자인 2</td><td>₩30,000</td></tr><tr><td>2025-09-14</td><td>고객1</td><td>디자인 1</td><td>₩60,000</td></tr><tr><td>2025-09-14</td><td>고객4</td><td>디자인 10</td><td>₩20,000</td></tr><tr><td>2025-09-14</td><td>고객6</td><td>디자인 16</td><td>₩30,000</td></tr><tr><td>2025-09-14</td><td>고객6</td><td>디자인 17</td><td>₩40,000</td></tr></tbody></table></div>
      <div>
        <div class="kpi" style="margin-bottom:10px">
          <div class="small">정산 합계(루시드)</div>
          <div class="v" id="settleSum">₩350,000</div>
          <div class="small" id="settlePayday">지급일: 2025-10-14</div>
        </div>
        <div class="card small">
          <div class="card-hd"><div class="card-ttl">집계 기준</div></div>
          <div class="card-bd">전월 <b>DONE</b> 상태만 집계합니다. 금액은 루시드 몫만 반영하며, 비율·본사 금액은 표시하지 않습니다.</div>
        </div>
      </div>
    </div>
  </section>
</div>