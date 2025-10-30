<div class="wrap">
<section id="sec-dash" class="card">
    <div class="card-hd">
      <div><div class="card-ttl">본사 대시보드</div><div class="card-sub">KPI · 출고/티켓/만료 요약</div></div>
      <div class="row"><button id="btnSmoke" class="btn">간단 점검</button><button id="btnReset" class="btn">더미데이터 재설정</button></div>
    </div>
    <div class="card-bd">
      <div id="kpis" class="kpi-grid"><div class="kpi"><div class="small">활성 고객 수</div><div class="v">12</div></div><div class="kpi"><div class="small">신규 고객 수(월)</div><div class="v">0</div></div><div class="kpi"><div class="small">리뉴얼 고객 수(월)</div><div class="v">1</div></div><div class="kpi"><div class="small">이탈 고객 수(월)</div><div class="v">0</div></div><div class="kpi"><div class="small">정기 구독 매출(최근2개월)</div><div class="v">₩504,900</div></div><div class="kpi"><div class="small">프린팅 매출(최근2개월)</div><div class="v">₩30,000</div></div><div class="kpi"><div class="small">유료 콘텐츠 매출(최근2개월)</div><div class="v">₩450,000</div></div><div class="kpi"><div class="small">유료 향 매출(최근2개월)</div><div class="v">₩289,484</div></div><div class="kpi"><div class="small">월별 벤더커미션 총합</div><div class="v">₩106,920</div></div></div>
      <div class="grid-3" style="margin-top:12px">
        <div class="card"><div class="card-hd"><div class="card-ttl">오늘 출고</div><div class="card-sub">라벨/지시서 동시 출력</div></div>
          <div class="card-bd table-wrap">
            <table class="table" id="tblTodayShip"><thead><tr><th><input type="checkbox" id="chkAllShip"></th><th>고객</th><th>사업장</th><th>항목</th><th>수량</th><th>송장</th></tr></thead><tbody><tr><td><input type="checkbox" data-id="SH2000"></td><td>C006</td><td>프런트</td><td>오일(400ml)</td><td>4</td><td>-</td></tr><tr><td><input type="checkbox" data-id="SH2001"></td><td>C003</td><td>프런트</td><td>프린팅</td><td>5</td><td>-</td></tr><tr><td><input type="checkbox" data-id="SH2002"></td><td>C004</td><td>본관</td><td>AP-5</td><td>1</td><td>-</td></tr><tr><td><input type="checkbox" data-id="SH2003"></td><td>C003</td><td>홀A</td><td>AP-5</td><td>2</td><td>-</td></tr><tr><td><input type="checkbox" data-id="SH2004"></td><td>C004</td><td>프런트</td><td>AP-5</td><td>5</td><td>-</td></tr><tr><td><input type="checkbox" data-id="SH2005"></td><td>C003</td><td>프런트</td><td>AP-5</td><td>2</td><td>-</td></tr><tr><td><input type="checkbox" data-id="SH2006"></td><td>C010</td><td>홀A</td><td>오일(400ml)</td><td>1</td><td>-</td></tr><tr><td><input type="checkbox" data-id="SH2007"></td><td>C008</td><td>본관</td><td>오일(400ml)</td><td>5</td><td>-</td></tr></tbody></table>
            <div class="row no-print" style="margin-top:8px">
              <button id="btnPrintSelected" class="btn primary">선택 라벨/지시서 출력</button>
              <button id="btnPrintAll" class="btn">오늘 전체 출력</button>
              <button id="btnShipCsv" class="btn">CSV</button>
            </div>
          </div>
        </div>
        <div class="card"><div class="card-hd"><div class="card-ttl">만료/리뉴얼(90일)</div></div>
          <div class="card-bd table-wrap"><table class="table" id="tblExpire"><thead><tr><th>고객</th><th>시작</th><th>종료</th><th>잔여</th><th>상태</th></tr></thead><tbody></tbody></table></div>
        </div>
        <div class="card"><div class="card-hd"><div class="card-ttl">티켓(요청)</div><div class="card-sub">최근 10건</div></div>
          <div class="card-bd table-wrap"><table class="table" id="tblTicket"><thead><tr><th>ID</th><th>고객</th><th>유형</th><th>내용</th><th>상태</th><th>일자</th></tr></thead><tbody><tr><td>T1019</td><td>C012</td><td>출고</td><td>요청 내용 예시</td><td>DONE</td><td>2025-04-08</td></tr><tr><td>T1018</td><td>C002</td><td>콘텐츠</td><td>요청 내용 예시</td><td>IN_PROGRESS</td><td>2025-09-04</td></tr><tr><td>T1017</td><td>C009</td><td>출고</td><td>요청 내용 예시</td><td>IN_PROGRESS</td><td>2025-04-26</td></tr><tr><td>T1016</td><td>C012</td><td>A/S</td><td>요청 내용 예시</td><td>OPEN</td><td>2025-03-19</td></tr><tr><td>T1015</td><td>C005</td><td>A/S</td><td>요청 내용 예시</td><td>DONE</td><td>2025-03-02</td></tr><tr><td>T1014</td><td>C012</td><td>출고</td><td>요청 내용 예시</td><td>DONE</td><td>2025-08-20</td></tr><tr><td>T1013</td><td>C011</td><td>출고</td><td>요청 내용 예시</td><td>OPEN</td><td>2025-05-24</td></tr><tr><td>T1012</td><td>C006</td><td>A/S</td><td>요청 내용 예시</td><td>DONE</td><td>2025-08-02</td></tr><tr><td>T1011</td><td>C007</td><td>프린팅</td><td>요청 내용 예시</td><td>DONE</td><td>2025-02-20</td></tr><tr><td>T1010</td><td>C004</td><td>A/S</td><td>요청 내용 예시</td><td>IN_PROGRESS</td><td>2025-01-27</td></tr></tbody></table></div>
        </div>
      </div>
    </div>
  </section>
</div>