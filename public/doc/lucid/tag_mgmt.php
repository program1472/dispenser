<div class="wrap">
<section id="sec-tags" class="card">
    <div class="card-hd">
      <div><div class="card-ttl">태그관리</div><div class="card-sub">그룹 생성/수정 + 선택 콘텐츠 일괄 적용 (루시드·본사 공용)</div></div>
      <div style="display:flex;gap:8px;align-items:center">
        <input id="grpName" class="input" placeholder="그룹명">
        <button id="btnAddGrp" class="btn">그룹 추가/수정</button>
        <button id="btnApplyTag" class="btn primary">선택 콘텐츠에 일괄 적용</button>
      </div>
    </div>
    <div class="card-bd grid-2">
      <div class="card">
        <div class="card-hd"><div class="card-ttl">그룹 목록</div></div>
        <div class="card-bd table-wrap">
          <table class="table" id="tblGroups"><thead><tr><th>그룹</th><th>태그 수</th><th>태그</th></tr></thead><tbody><tr><td>골프 성수기</td><td>3</td><td>시즌, 이벤트, 안내</td></tr><tr><td>호텔 프로모션</td><td>2</td><td>프로모션, 맴버십</td></tr><tr><td>병원/안전</td><td>2</td><td>안전, 안내</td></tr></tbody></table>
        </div>
      </div>
      <div class="card">
        <div class="card-hd"><div class="card-ttl">콘텐츠 선택</div><div class="card-sub">체크 후 적용</div></div>
        <div class="card-bd table-wrap">
          <table class="table" id="tblTagContent"><thead><tr><th><input type="checkbox" id="chkAllContents"></th><th>제목</th><th>업종</th><th>태그</th><th>등록일</th></tr></thead><tbody><tr><td><input type="checkbox" data-ck="C002"></td><td>템플릿 2</td><td>병원</td><td>인포, 고급</td><td>2025-10-13</td></tr><tr><td><input type="checkbox" data-ck="C018"></td><td>템플릿 18</td><td>호텔</td><td>안내, 안내</td><td>2025-10-10</td></tr><tr><td><input type="checkbox" data-ck="C006"></td><td>템플릿 6</td><td>골프장</td><td>심플, 안전</td><td>2025-10-09</td></tr><tr><td><input type="checkbox" data-ck="C024"></td><td>템플릿 24</td><td>병원</td><td>안내, 주말</td><td>2025-10-07</td></tr><tr><td><input type="checkbox" data-ck="C007"></td><td>템플릿 7</td><td>골프장</td><td>심플, 주말</td><td>2025-10-05</td></tr><tr><td><input type="checkbox" data-ck="C015"></td><td>템플릿 15</td><td>호텔</td><td>안전, 시즌</td><td>2025-10-04</td></tr><tr><td><input type="checkbox" data-ck="C017"></td><td>템플릿 17</td><td>카페</td><td>인포, 시즌</td><td>2025-10-04</td></tr><tr><td><input type="checkbox" data-ck="C001"></td><td>템플릿 1</td><td>병원</td><td>프로모션, 인포</td><td>2025-10-03</td></tr><tr><td><input type="checkbox" data-ck="C009"></td><td>템플릿 9</td><td>호텔</td><td>시즌, 이벤트</td><td>2025-10-03</td></tr><tr><td><input type="checkbox" data-ck="C022"></td><td>템플릿 22</td><td>예식장</td><td>고급, 고급</td><td>2025-10-02</td></tr><tr><td><input type="checkbox" data-ck="C016"></td><td>템플릿 16</td><td>호텔</td><td>안전, 주말</td><td>2025-09-28</td></tr><tr><td><input type="checkbox" data-ck="C023"></td><td>템플릿 23</td><td>호텔</td><td>안전, 주말</td><td>2025-09-21</td></tr><tr><td><input type="checkbox" data-ck="C004"></td><td>템플릿 4</td><td>호텔</td><td>주말, 심플</td><td>2025-09-17</td></tr><tr><td><input type="checkbox" data-ck="C010"></td><td>템플릿 10</td><td>예식장</td><td>이벤트, 고급</td><td>2025-09-17</td></tr><tr><td><input type="checkbox" data-ck="C012"></td><td>템플릿 12</td><td>카페</td><td>프로모션, 심플</td><td>2025-09-15</td></tr><tr><td><input type="checkbox" data-ck="C019"></td><td>템플릿 19</td><td>예식장</td><td>이벤트, 안전</td><td>2025-09-15</td></tr><tr><td><input type="checkbox" data-ck="C005"></td><td>템플릿 5</td><td>골프장</td><td>시즌, 심플</td><td>2025-09-14</td></tr><tr><td><input type="checkbox" data-ck="C013"></td><td>템플릿 13</td><td>병원</td><td>프로모션, 시즌</td><td>2025-09-12</td></tr><tr><td><input type="checkbox" data-ck="C020"></td><td>템플릿 20</td><td>골프장</td><td>프로모션, 안전</td><td>2025-09-09</td></tr><tr><td><input type="checkbox" data-ck="C011"></td><td>템플릿 11</td><td>카페</td><td>맴버십, 고급</td><td>2025-09-06</td></tr><tr><td><input type="checkbox" data-ck="C021"></td><td>템플릿 21</td><td>예식장</td><td>주말, 고급</td><td>2025-09-06</td></tr><tr><td><input type="checkbox" data-ck="C014"></td><td>템플릿 14</td><td>카페</td><td>심플, 프로모션</td><td>2025-09-04</td></tr><tr><td><input type="checkbox" data-ck="C008"></td><td>템플릿 8</td><td>카페</td><td>심플, 프로모션</td><td>2025-08-30</td></tr><tr><td><input type="checkbox" data-ck="C003"></td><td>템플릿 3</td><td>병원</td><td>맴버십, 주말</td><td>2025-08-29</td></tr></tbody></table>
        </div>
      </div>
    </div>
  </section>
</div>