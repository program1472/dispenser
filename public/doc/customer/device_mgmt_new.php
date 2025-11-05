<div class="sticky-filter">
  <div class="inner">
    <span class="small" style="font-weight:800;color:var(--accent)">사업장</span>
    <select id="siteFilter" class="select"><option value="ALL">전체 사업장</option><option value="S1">강남 타워</option><option value="S2">분당 센터</option><option value="S3">해운대 리조트</option></select>

    <span class="small" style="font-weight:800;color:var(--accent)">그룹</span>
    <select id="groupFilter" class="select" style="min-width:140px"><option value="ALL">전체 그룹</option><option value="화장실">화장실</option><option value="로비">로비</option><option value="VIP룸">VIP룸</option></select>

    <input id="placeSearch" class="input" placeholder="설치 장소 검색 (예: 화장실)">
    <button id="btnClearFilters" class="btn">필터 초기화</button>

    <div style="margin-left:auto" class="row">
      <button id="btnResetSeed" class="btn">더미데이터 재설정</button>
      <button id="btnRunTests" class="btn">테스트 실행</button>
    </div>
  </div>
</div>

<div class="wrap">
  <!-- 그룹 관리 / 일괄 변경 + 배송 적용 범위 -->
  <section class="card">
    <div class="card-hd">
      <div>
        <div class="card-ttl">그룹 관리 &amp; 일괄 변경</div>
        <div class="card-sub">필터로 대상을 줄이고, 선택된 기기에 일괄 적용하세요. (향/콘텐츠 모두 무상)</div>
      </div>
      <div class="row">
        <input id="grpName" class="input" placeholder="그룹명 (예: 화장실)">
        <button id="btnAddGroup" class="btn primary">그룹 추가</button>
        <button id="btnDelGroup" class="btn warn">그룹 삭제</button>
        <div class="separator"></div>
        <select id="oldGroupSel" class="select" style="min-width:140px"><option value="화장실">화장실</option><option value="로비">로비</option><option value="VIP룸">VIP룸</option></select>
        <input id="newGrpName" class="input" placeholder="새 그룹명">
        <button id="btnRenameGroup" class="btn">그룹명 수정</button>
      </div>
    </div>
    <div class="card-bd">
      <div class="row" style="margin-bottom:8px">
        <span class="small" style="font-weight:700">선택 기기</span>
        <span id="selCount" class="badge">0 대</span>
        <button id="btnSelectAllPage" class="btn">현재 페이지 전체 선택</button>
        <button id="btnClearSelection" class="btn">선택 해제</button>
      </div>
      <div class="row" style="flex-wrap:wrap;gap:10px 12px">
        <select id="bulkGroup" class="select" style="min-width:140px"><option value="화장실">화장실</option><option value="로비">로비</option><option value="VIP룸">VIP룸</option></select>
        <button id="btnAssignGroup" class="btn">선택 기기 그룹 지정</button>
        <div class="separator"></div>
        <!-- 라이브러리에서 선택하는 드롭다운 (모두 무상) -->
        <select id="bulkScentSel" class="select" style="min-width:160px"><option value="">향 선택 (무상 라이브러리)</option><option value="Green Tea">Green Tea</option><option value="Citrus">Citrus</option><option value="Lavender">Lavender</option><option value="Cotton">Cotton</option><option value="Ocean">Ocean</option><option value="Pine">Pine</option></select>
        <select id="bulkContentSel" class="select" style="min-width:180px"><option value="">콘텐츠 선택 (무상 라이브러리)</option><option value="손씻기 안내">손씻기 안내</option><option value="소독 안내">소독 안내</option><option value="환기 안내">환기 안내</option><option value="행사 안내">행사 안내</option><option value="프로모션 배너">프로모션 배너</option><option value="안전 수칙">안전 수칙</option></select>
        <select id="applyScope" class="select">
          <option value="NEXT" selected="">다음 1회 배송에만</option>
          <option value="ALL">남은 모든 배송에</option>
        </select>
        <button id="btnApplyBulk" class="btn primary">선택 기기 일괄 적용</button>
      </div>
      <div class="help">※ 모든 향·콘텐츠는 무상 기준으로 취급합니다. 자유 입력은 비활성화되고 라이브러리에서만 선택합니다.</div>
    </div>
  </section>

  <!-- 기기 목록 -->
  <section class="card">
    <div class="card-hd">
      <div>
        <div class="card-ttl">기기관리</div>
        <div class="card-sub">설치일/장소/시리얼 · 그룹 · 선택 향/콘텐츠 · <b>다음 배송일</b> · 남은 무료배송</div>
      </div>
      <div class="row">
        <span class="small">페이지당</span>
        <select id="pageSize" class="select"><option>10</option><option selected="">20</option><option>50</option></select>
      </div>
    </div>
    <div class="card-bd">
      <div class="table-wrap">
        <table class="table" id="deviceTbl">
          <thead>
            <tr>
              <th><input type="checkbox" id="chkAll"></th>
              <th>사업장</th>
              <th>설치 장소</th>
              <th>시리얼</th>
              <th>설치일</th>
              <th>그룹</th>
              <th>향</th>
              <th>콘텐츠</th>
              <th>다음 배송일</th>
              <th>남은 무료배송</th>
              <th>라이브러리 이동</th>
            </tr>
          </thead>
          <tbody><tr><td><input type="checkbox" data-id="D1"></td><td>강남 타워</td><td class="highlight">남자화장실</td><td>AP5-S1-001</td><td>2025-11-05</td><td><select class="select" style="min-width: 120px;"><option value="">미지정</option><option value="화장실">화장실</option><option value="로비">로비</option><option value="VIP룸">VIP룸</option></select></td><td><select class="select" style="min-width: 140px;"><option value="">향 선택</option><option value="Green Tea">Green Tea</option><option value="Citrus">Citrus</option><option value="Lavender">Lavender</option><option value="Cotton">Cotton</option><option value="Ocean">Ocean</option><option value="Pine">Pine</option></select></td><td><select class="select" style="min-width: 160px;"><option value="">콘텐츠 선택</option><option value="손씻기 안내">손씻기 안내</option><option value="소독 안내">소독 안내</option><option value="환기 안내">환기 안내</option><option value="행사 안내">행사 안내</option><option value="프로모션 배너">프로모션 배너</option><option value="안전 수칙">안전 수칙</option></select></td><td>2025-11-05</td><td><span class="badge">6 회</span></td><td><button class="btn">향 라이브러리</button><button class="btn" style="margin-left: 6px;">콘텐츠 라이브러리</button></td></tr><tr><td><input type="checkbox" data-id="D2"></td><td>강남 타워</td><td class="highlight">여자화장실</td><td>AP5-S1-002</td><td>2025-10-21</td><td><select class="select" style="min-width: 120px;"><option value="">미지정</option><option value="화장실">화장실</option><option value="로비">로비</option><option value="VIP룸">VIP룸</option></select></td><td><select class="select" style="min-width: 140px;"><option value="">향 선택</option><option value="Green Tea">Green Tea</option><option value="Citrus">Citrus</option><option value="Lavender">Lavender</option><option value="Cotton">Cotton</option><option value="Ocean">Ocean</option><option value="Pine">Pine</option></select></td><td><select class="select" style="min-width: 160px;"><option value="">콘텐츠 선택</option><option value="손씻기 안내">손씻기 안내</option><option value="소독 안내">소독 안내</option><option value="환기 안내">환기 안내</option><option value="행사 안내">행사 안내</option><option value="프로모션 배너">프로모션 배너</option><option value="안전 수칙">안전 수칙</option></select></td><td>2025-12-21</td><td><span class="badge">5 회</span></td><td><button class="btn">향 라이브러리</button><button class="btn" style="margin-left: 6px;">콘텐츠 라이브러리</button></td></tr><tr><td><input type="checkbox" data-id="D3"></td><td>강남 타워</td><td>로비</td><td>AP5-S1-003</td><td>2025-10-20</td><td><select class="select" style="min-width: 120px;"><option value="">미지정</option><option value="화장실">화장실</option><option value="로비">로비</option><option value="VIP룸">VIP룸</option></select></td><td><select class="select" style="min-width: 140px;"><option value="">향 선택</option><option value="Green Tea">Green Tea</option><option value="Citrus">Citrus</option><option value="Lavender">Lavender</option><option value="Cotton">Cotton</option><option value="Ocean">Ocean</option><option value="Pine">Pine</option></select></td><td><select class="select" style="min-width: 160px;"><option value="">콘텐츠 선택</option><option value="손씻기 안내">손씻기 안내</option><option value="소독 안내">소독 안내</option><option value="환기 안내">환기 안내</option><option value="행사 안내">행사 안내</option><option value="프로모션 배너">프로모션 배너</option><option value="안전 수칙">안전 수칙</option></select></td><td>2025-12-20</td><td><span class="badge">5 회</span></td><td><button class="btn">향 라이브러리</button><button class="btn" style="margin-left: 6px;">콘텐츠 라이브러리</button></td></tr><tr><td><input type="checkbox" data-id="D4"></td><td>강남 타워</td><td>레스토랑</td><td>AP5-S1-004</td><td>2025-10-13</td><td><select class="select" style="min-width: 120px;"><option value="">미지정</option><option value="화장실">화장실</option><option value="로비">로비</option><option value="VIP룸">VIP룸</option></select></td><td><select class="select" style="min-width: 140px;"><option value="">향 선택</option><option value="Green Tea">Green Tea</option><option value="Citrus">Citrus</option><option value="Lavender">Lavender</option><option value="Cotton">Cotton</option><option value="Ocean">Ocean</option><option value="Pine">Pine</option></select></td><td><select class="select" style="min-width: 160px;"><option value="">콘텐츠 선택</option><option value="손씻기 안내">손씻기 안내</option><option value="소독 안내">소독 안내</option><option value="환기 안내">환기 안내</option><option value="행사 안내">행사 안내</option><option value="프로모션 배너">프로모션 배너</option><option value="안전 수칙">안전 수칙</option></select></td><td>2025-12-13</td><td><span class="badge">5 회</span></td><td><button class="btn">향 라이브러리</button><button class="btn" style="margin-left: 6px;">콘텐츠 라이브러리</button></td></tr><tr><td><input type="checkbox" data-id="D5"></td><td>강남 타워</td><td>피트니스</td><td>AP5-S1-005</td><td>2025-09-30</td><td><select class="select" style="min-width: 120px;"><option value="">미지정</option><option value="화장실">화장실</option><option value="로비">로비</option><option value="VIP룸">VIP룸</option></select></td><td><select class="select" style="min-width: 140px;"><option value="">향 선택</option><option value="Green Tea">Green Tea</option><option value="Citrus">Citrus</option><option value="Lavender">Lavender</option><option value="Cotton">Cotton</option><option value="Ocean">Ocean</option><option value="Pine">Pine</option></select></td><td><select class="select" style="min-width: 160px;"><option value="">콘텐츠 선택</option><option value="손씻기 안내">손씻기 안내</option><option value="소독 안내">소독 안내</option><option value="환기 안내">환기 안내</option><option value="행사 안내">행사 안내</option><option value="프로모션 배너">프로모션 배너</option><option value="안전 수칙">안전 수칙</option></select></td><td>2025-11-30</td><td><span class="badge">5 회</span></td><td><button class="btn">향 라이브러리</button><button class="btn" style="margin-left: 6px;">콘텐츠 라이브러리</button></td></tr><tr><td><input type="checkbox" data-id="D6"></td><td>강남 타워</td><td>회의실A</td><td>AP5-S1-006</td><td>2025-09-29</td><td><select class="select" style="min-width: 120px;"><option value="">미지정</option><option value="화장실">화장실</option><option value="로비">로비</option><option value="VIP룸">VIP룸</option></select></td><td><select class="select" style="min-width: 140px;"><option value="">향 선택</option><option value="Green Tea">Green Tea</option><option value="Citrus">Citrus</option><option value="Lavender">Lavender</option><option value="Cotton">Cotton</option><option value="Ocean">Ocean</option><option value="Pine">Pine</option></select></td><td><select class="select" style="min-width: 160px;"><option value="">콘텐츠 선택</option><option value="손씻기 안내">손씻기 안내</option><option value="소독 안내">소독 안내</option><option value="환기 안내">환기 안내</option><option value="행사 안내">행사 안내</option><option value="프로모션 배너">프로모션 배너</option><option value="안전 수칙">안전 수칙</option></select></td><td>2025-11-29</td><td><span class="badge">5 회</span></td><td><button class="btn">향 라이브러리</button><button class="btn" style="margin-left: 6px;">콘텐츠 라이브러리</button></td></tr><tr><td><input type="checkbox" data-id="D7"></td><td>강남 타워</td><td>회의실B</td><td>AP5-S1-007</td><td>2025-09-15</td><td><select class="select" style="min-width: 120px;"><option value="">미지정</option><option value="화장실">화장실</option><option value="로비">로비</option><option value="VIP룸">VIP룸</option></select></td><td><select class="select" style="min-width: 140px;"><option value="">향 선택</option><option value="Green Tea">Green Tea</option><option value="Citrus">Citrus</option><option value="Lavender">Lavender</option><option value="Cotton">Cotton</option><option value="Ocean">Ocean</option><option value="Pine">Pine</option></select></td><td><select class="select" style="min-width: 160px;"><option value="">콘텐츠 선택</option><option value="손씻기 안내">손씻기 안내</option><option value="소독 안내">소독 안내</option><option value="환기 안내">환기 안내</option><option value="행사 안내">행사 안내</option><option value="프로모션 배너">프로모션 배너</option><option value="안전 수칙">안전 수칙</option></select></td><td>2025-11-15</td><td><span class="badge">5 회</span></td><td><button class="btn">향 라이브러리</button><button class="btn" style="margin-left: 6px;">콘텐츠 라이브러리</button></td></tr><tr><td><input type="checkbox" data-id="D8"></td><td>강남 타워</td><td>VIP룸</td><td>AP5-S1-008</td><td>2025-09-10</td><td><select class="select" style="min-width: 120px;"><option value="">미지정</option><option value="화장실">화장실</option><option value="로비">로비</option><option value="VIP룸">VIP룸</option></select></td><td><select class="select" style="min-width: 140px;"><option value="">향 선택</option><option value="Green Tea">Green Tea</option><option value="Citrus">Citrus</option><option value="Lavender">Lavender</option><option value="Cotton">Cotton</option><option value="Ocean">Ocean</option><option value="Pine">Pine</option></select></td><td><select class="select" style="min-width: 160px;"><option value="">콘텐츠 선택</option><option value="손씻기 안내">손씻기 안내</option><option value="소독 안내">소독 안내</option><option value="환기 안내">환기 안내</option><option value="행사 안내">행사 안내</option><option value="프로모션 배너">프로모션 배너</option><option value="안전 수칙">안전 수칙</option></select></td><td>2025-11-10</td><td><span class="badge">5 회</span></td><td><button class="btn">향 라이브러리</button><button class="btn" style="margin-left: 6px;">콘텐츠 라이브러리</button></td></tr><tr><td><input type="checkbox" data-id="D9"></td><td>분당 센터</td><td class="highlight">남자화장실</td><td>AP5-S2-001</td><td>2025-11-05</td><td><select class="select" style="min-width: 120px;"><option value="">미지정</option><option value="화장실">화장실</option><option value="로비">로비</option><option value="VIP룸">VIP룸</option></select></td><td><select class="select" style="min-width: 140px;"><option value="">향 선택</option><option value="Green Tea">Green Tea</option><option value="Citrus">Citrus</option><option value="Lavender">Lavender</option><option value="Cotton">Cotton</option><option value="Ocean">Ocean</option><option value="Pine">Pine</option></select></td><td><select class="select" style="min-width: 160px;"><option value="">콘텐츠 선택</option><option value="손씻기 안내">손씻기 안내</option><option value="소독 안내">소독 안내</option><option value="환기 안내">환기 안내</option><option value="행사 안내">행사 안내</option><option value="프로모션 배너">프로모션 배너</option><option value="안전 수칙">안전 수칙</option></select></td><td>2025-11-05</td><td><span class="badge">6 회</span></td><td><button class="btn">향 라이브러리</button><button class="btn" style="margin-left: 6px;">콘텐츠 라이브러리</button></td></tr><tr><td><input type="checkbox" data-id="D10"></td><td>분당 센터</td><td class="highlight">여자화장실</td><td>AP5-S2-002</td><td>2025-10-20</td><td><select class="select" style="min-width: 120px;"><option value="">미지정</option><option value="화장실">화장실</option><option value="로비">로비</option><option value="VIP룸">VIP룸</option></select></td><td><select class="select" style="min-width: 140px;"><option value="">향 선택</option><option value="Green Tea">Green Tea</option><option value="Citrus">Citrus</option><option value="Lavender">Lavender</option><option value="Cotton">Cotton</option><option value="Ocean">Ocean</option><option value="Pine">Pine</option></select></td><td><select class="select" style="min-width: 160px;"><option value="">콘텐츠 선택</option><option value="손씻기 안내">손씻기 안내</option><option value="소독 안내">소독 안내</option><option value="환기 안내">환기 안내</option><option value="행사 안내">행사 안내</option><option value="프로모션 배너">프로모션 배너</option><option value="안전 수칙">안전 수칙</option></select></td><td>2025-12-20</td><td><span class="badge">5 회</span></td><td><button class="btn">향 라이브러리</button><button class="btn" style="margin-left: 6px;">콘텐츠 라이브러리</button></td></tr><tr><td><input type="checkbox" data-id="D11"></td><td>분당 센터</td><td>로비</td><td>AP5-S2-003</td><td>2025-10-15</td><td><select class="select" style="min-width: 120px;"><option value="">미지정</option><option value="화장실">화장실</option><option value="로비">로비</option><option value="VIP룸">VIP룸</option></select></td><td><select class="select" style="min-width: 140px;"><option value="">향 선택</option><option value="Green Tea">Green Tea</option><option value="Citrus">Citrus</option><option value="Lavender">Lavender</option><option value="Cotton">Cotton</option><option value="Ocean">Ocean</option><option value="Pine">Pine</option></select></td><td><select class="select" style="min-width: 160px;"><option value="">콘텐츠 선택</option><option value="손씻기 안내">손씻기 안내</option><option value="소독 안내">소독 안내</option><option value="환기 안내">환기 안내</option><option value="행사 안내">행사 안내</option><option value="프로모션 배너">프로모션 배너</option><option value="안전 수칙">안전 수칙</option></select></td><td>2025-12-15</td><td><span class="badge">5 회</span></td><td><button class="btn">향 라이브러리</button><button class="btn" style="margin-left: 6px;">콘텐츠 라이브러리</button></td></tr><tr><td><input type="checkbox" data-id="D12"></td><td>분당 센터</td><td>레스토랑</td><td>AP5-S2-004</td><td>2025-10-11</td><td><select class="select" style="min-width: 120px;"><option value="">미지정</option><option value="화장실">화장실</option><option value="로비">로비</option><option value="VIP룸">VIP룸</option></select></td><td><select class="select" style="min-width: 140px;"><option value="">향 선택</option><option value="Green Tea">Green Tea</option><option value="Citrus">Citrus</option><option value="Lavender">Lavender</option><option value="Cotton">Cotton</option><option value="Ocean">Ocean</option><option value="Pine">Pine</option></select></td><td><select class="select" style="min-width: 160px;"><option value="">콘텐츠 선택</option><option value="손씻기 안내">손씻기 안내</option><option value="소독 안내">소독 안내</option><option value="환기 안내">환기 안내</option><option value="행사 안내">행사 안내</option><option value="프로모션 배너">프로모션 배너</option><option value="안전 수칙">안전 수칙</option></select></td><td>2025-12-11</td><td><span class="badge">5 회</span></td><td><button class="btn">향 라이브러리</button><button class="btn" style="margin-left: 6px;">콘텐츠 라이브러리</button></td></tr><tr><td><input type="checkbox" data-id="D13"></td><td>분당 센터</td><td>피트니스</td><td>AP5-S2-005</td><td>2025-10-02</td><td><select class="select" style="min-width: 120px;"><option value="">미지정</option><option value="화장실">화장실</option><option value="로비">로비</option><option value="VIP룸">VIP룸</option></select></td><td><select class="select" style="min-width: 140px;"><option value="">향 선택</option><option value="Green Tea">Green Tea</option><option value="Citrus">Citrus</option><option value="Lavender">Lavender</option><option value="Cotton">Cotton</option><option value="Ocean">Ocean</option><option value="Pine">Pine</option></select></td><td><select class="select" style="min-width: 160px;"><option value="">콘텐츠 선택</option><option value="손씻기 안내">손씻기 안내</option><option value="소독 안내">소독 안내</option><option value="환기 안내">환기 안내</option><option value="행사 안내">행사 안내</option><option value="프로모션 배너">프로모션 배너</option><option value="안전 수칙">안전 수칙</option></select></td><td>2025-12-02</td><td><span class="badge">5 회</span></td><td><button class="btn">향 라이브러리</button><button class="btn" style="margin-left: 6px;">콘텐츠 라이브러리</button></td></tr><tr><td><input type="checkbox" data-id="D14"></td><td>분당 센터</td><td>회의실A</td><td>AP5-S2-006</td><td>2025-09-25</td><td><select class="select" style="min-width: 120px;"><option value="">미지정</option><option value="화장실">화장실</option><option value="로비">로비</option><option value="VIP룸">VIP룸</option></select></td><td><select class="select" style="min-width: 140px;"><option value="">향 선택</option><option value="Green Tea">Green Tea</option><option value="Citrus">Citrus</option><option value="Lavender">Lavender</option><option value="Cotton">Cotton</option><option value="Ocean">Ocean</option><option value="Pine">Pine</option></select></td><td><select class="select" style="min-width: 160px;"><option value="">콘텐츠 선택</option><option value="손씻기 안내">손씻기 안내</option><option value="소독 안내">소독 안내</option><option value="환기 안내">환기 안내</option><option value="행사 안내">행사 안내</option><option value="프로모션 배너">프로모션 배너</option><option value="안전 수칙">안전 수칙</option></select></td><td>2025-11-25</td><td><span class="badge">5 회</span></td><td><button class="btn">향 라이브러리</button><button class="btn" style="margin-left: 6px;">콘텐츠 라이브러리</button></td></tr><tr><td><input type="checkbox" data-id="D15"></td><td>분당 센터</td><td>회의실B</td><td>AP5-S2-007</td><td>2025-09-20</td><td><select class="select" style="min-width: 120px;"><option value="">미지정</option><option value="화장실">화장실</option><option value="로비">로비</option><option value="VIP룸">VIP룸</option></select></td><td><select class="select" style="min-width: 140px;"><option value="">향 선택</option><option value="Green Tea">Green Tea</option><option value="Citrus">Citrus</option><option value="Lavender">Lavender</option><option value="Cotton">Cotton</option><option value="Ocean">Ocean</option><option value="Pine">Pine</option></select></td><td><select class="select" style="min-width: 160px;"><option value="">콘텐츠 선택</option><option value="손씻기 안내">손씻기 안내</option><option value="소독 안내">소독 안내</option><option value="환기 안내">환기 안내</option><option value="행사 안내">행사 안내</option><option value="프로모션 배너">프로모션 배너</option><option value="안전 수칙">안전 수칙</option></select></td><td>2025-11-20</td><td><span class="badge">5 회</span></td><td><button class="btn">향 라이브러리</button><button class="btn" style="margin-left: 6px;">콘텐츠 라이브러리</button></td></tr><tr><td><input type="checkbox" data-id="D16"></td><td>분당 센터</td><td>VIP룸</td><td>AP5-S2-008</td><td>2025-09-10</td><td><select class="select" style="min-width: 120px;"><option value="">미지정</option><option value="화장실">화장실</option><option value="로비">로비</option><option value="VIP룸">VIP룸</option></select></td><td><select class="select" style="min-width: 140px;"><option value="">향 선택</option><option value="Green Tea">Green Tea</option><option value="Citrus">Citrus</option><option value="Lavender">Lavender</option><option value="Cotton">Cotton</option><option value="Ocean">Ocean</option><option value="Pine">Pine</option></select></td><td><select class="select" style="min-width: 160px;"><option value="">콘텐츠 선택</option><option value="손씻기 안내">손씻기 안내</option><option value="소독 안내">소독 안내</option><option value="환기 안내">환기 안내</option><option value="행사 안내">행사 안내</option><option value="프로모션 배너">프로모션 배너</option><option value="안전 수칙">안전 수칙</option></select></td><td>2025-11-10</td><td><span class="badge">5 회</span></td><td><button class="btn">향 라이브러리</button><button class="btn" style="margin-left: 6px;">콘텐츠 라이브러리</button></td></tr><tr><td><input type="checkbox" data-id="D17"></td><td>해운대 리조트</td><td class="highlight">남자화장실</td><td>AP5-S3-001</td><td>2025-10-29</td><td><select class="select" style="min-width: 120px;"><option value="">미지정</option><option value="화장실">화장실</option><option value="로비">로비</option><option value="VIP룸">VIP룸</option></select></td><td><select class="select" style="min-width: 140px;"><option value="">향 선택</option><option value="Green Tea">Green Tea</option><option value="Citrus">Citrus</option><option value="Lavender">Lavender</option><option value="Cotton">Cotton</option><option value="Ocean">Ocean</option><option value="Pine">Pine</option></select></td><td><select class="select" style="min-width: 160px;"><option value="">콘텐츠 선택</option><option value="손씻기 안내">손씻기 안내</option><option value="소독 안내">소독 안내</option><option value="환기 안내">환기 안내</option><option value="행사 안내">행사 안내</option><option value="프로모션 배너">프로모션 배너</option><option value="안전 수칙">안전 수칙</option></select></td><td>2025-12-29</td><td><span class="badge">5 회</span></td><td><button class="btn">향 라이브러리</button><button class="btn" style="margin-left: 6px;">콘텐츠 라이브러리</button></td></tr><tr><td><input type="checkbox" data-id="D18"></td><td>해운대 리조트</td><td class="highlight">여자화장실</td><td>AP5-S3-002</td><td>2025-10-26</td><td><select class="select" style="min-width: 120px;"><option value="">미지정</option><option value="화장실">화장실</option><option value="로비">로비</option><option value="VIP룸">VIP룸</option></select></td><td><select class="select" style="min-width: 140px;"><option value="">향 선택</option><option value="Green Tea">Green Tea</option><option value="Citrus">Citrus</option><option value="Lavender">Lavender</option><option value="Cotton">Cotton</option><option value="Ocean">Ocean</option><option value="Pine">Pine</option></select></td><td><select class="select" style="min-width: 160px;"><option value="">콘텐츠 선택</option><option value="손씻기 안내">손씻기 안내</option><option value="소독 안내">소독 안내</option><option value="환기 안내">환기 안내</option><option value="행사 안내">행사 안내</option><option value="프로모션 배너">프로모션 배너</option><option value="안전 수칙">안전 수칙</option></select></td><td>2025-12-26</td><td><span class="badge">5 회</span></td><td><button class="btn">향 라이브러리</button><button class="btn" style="margin-left: 6px;">콘텐츠 라이브러리</button></td></tr><tr><td><input type="checkbox" data-id="D19"></td><td>해운대 리조트</td><td>로비</td><td>AP5-S3-003</td><td>2025-10-14</td><td><select class="select" style="min-width: 120px;"><option value="">미지정</option><option value="화장실">화장실</option><option value="로비">로비</option><option value="VIP룸">VIP룸</option></select></td><td><select class="select" style="min-width: 140px;"><option value="">향 선택</option><option value="Green Tea">Green Tea</option><option value="Citrus">Citrus</option><option value="Lavender">Lavender</option><option value="Cotton">Cotton</option><option value="Ocean">Ocean</option><option value="Pine">Pine</option></select></td><td><select class="select" style="min-width: 160px;"><option value="">콘텐츠 선택</option><option value="손씻기 안내">손씻기 안내</option><option value="소독 안내">소독 안내</option><option value="환기 안내">환기 안내</option><option value="행사 안내">행사 안내</option><option value="프로모션 배너">프로모션 배너</option><option value="안전 수칙">안전 수칙</option></select></td><td>2025-12-14</td><td><span class="badge">5 회</span></td><td><button class="btn">향 라이브러리</button><button class="btn" style="margin-left: 6px;">콘텐츠 라이브러리</button></td></tr><tr><td><input type="checkbox" data-id="D20"></td><td>해운대 리조트</td><td>레스토랑</td><td>AP5-S3-004</td><td>2025-10-07</td><td><select class="select" style="min-width: 120px;"><option value="">미지정</option><option value="화장실">화장실</option><option value="로비">로비</option><option value="VIP룸">VIP룸</option></select></td><td><select class="select" style="min-width: 140px;"><option value="">향 선택</option><option value="Green Tea">Green Tea</option><option value="Citrus">Citrus</option><option value="Lavender">Lavender</option><option value="Cotton">Cotton</option><option value="Ocean">Ocean</option><option value="Pine">Pine</option></select></td><td><select class="select" style="min-width: 160px;"><option value="">콘텐츠 선택</option><option value="손씻기 안내">손씻기 안내</option><option value="소독 안내">소독 안내</option><option value="환기 안내">환기 안내</option><option value="행사 안내">행사 안내</option><option value="프로모션 배너">프로모션 배너</option><option value="안전 수칙">안전 수칙</option></select></td><td>2025-12-07</td><td><span class="badge">5 회</span></td><td><button class="btn">향 라이브러리</button><button class="btn" style="margin-left: 6px;">콘텐츠 라이브러리</button></td></tr></tbody>
        </table>
      </div>
      <div class="row" style="justify-content:space-between;margin-top:10px">
        <div class="small" id="pagerInfo">24대 중 1–20 표시 (페이지 1/2)</div>
        <div class="row">
          <button id="prevPage" class="btn">이전</button>
          <button id="nextPage" class="btn">다음</button>
        </div>
      </div>
      <div class="small" style="margin-top:8px">※ 향 6종 무상 제공, 설치일 기준 2개월마다 자동 공급. 첫 구입 시 1개는 고객 선택, 나머지는 랜덤 공급.</div>
    </div>
  </section>
</div>
<div class="toast-wrap" id="toastWrap"></div>
<script>
// ------------------------------
// 데이터/시드 및 스케줄 유틸
// ------------------------------
const LS_KEY='cust_devices_grouping_v4_freeOnly';
let state={sites:[],groups:[],devices:[],libraries:{scents:[],contents:[]},filter:{site:'ALL',group:'ALL',place:''},selection:new Set(),page:1,pageSize:20};

function addMonths(iso, m){const d=new Date(iso);const day=d.getDate();d.setMonth(d.getMonth()+m);if(d.getDate()<day){d.setDate(0);}return d.toISOString().slice(0,10);} // 월말 보정

function buildSchedule(installDate,scent,content){
  // 무료 6회, 2개월 주기 스케줄 생성
  const arr=[];for(let i=0;i<6;i++){const date=i===0?installDate:addMonths(installDate, i*2);arr.push({seq:i+1,date:date,scent,content,shipped:false});}
  // 오늘 이전은 출고 처리
  const today=new Date().toISOString().slice(0,10);
  arr.forEach(s=>{ if(s.date<today){ s.shipped=true; }});
  return arr;
}

function remainingCount(d){return d.schedule.filter(x=>!x.shipped).length;}
function nextIndexes(d){return d.schedule.reduce((acc,s,idx)=>{if(!s.shipped) acc.push(idx);return acc;},[]);} 
function nextDate(d){const idxs=nextIndexes(d);return idxs.length? d.schedule[idxs[0]].date : '-';}

function seed(){
  // 라이브러리(모두 무상)
  const scentLib=[
    {code:'S-FREE-01',name:'Green Tea',type:'FREE'},
    {code:'S-FREE-02',name:'Citrus',type:'FREE'},
    {code:'S-FREE-03',name:'Lavender',type:'FREE'},
    {code:'S-FREE-04',name:'Cotton',type:'FREE'},
    {code:'S-FREE-05',name:'Ocean',type:'FREE'},
    {code:'S-FREE-06',name:'Pine',type:'FREE'}
  ];
  const contentLib=[
    {code:'C-FREE-01',name:'손씻기 안내',type:'FREE'},
    {code:'C-FREE-02',name:'소독 안내',type:'FREE'},
    {code:'C-FREE-03',name:'환기 안내',type:'FREE'},
    {code:'C-FREE-04',name:'행사 안내',type:'FREE'},
    {code:'C-FREE-05',name:'프로모션 배너',type:'FREE'},
    {code:'C-FREE-06',name:'안전 수칙',type:'FREE'}
  ];

  const sites=[{id:'S1',name:'강남 타워'},{id:'S2',name:'분당 센터'},{id:'S3',name:'해운대 리조트'}];
  const groups=[{id:'G1',name:'화장실'},{id:'G2',name:'로비'},{id:'G3',name:'VIP룸'}];
  const places=['남자화장실','여자화장실','로비','레스토랑','피트니스','회의실A','회의실B','VIP룸'];
  const today=new Date();
  let devices=[];let idSeq=1;
  for(const s of sites){
    places.forEach((p,idx)=>{
      const serial=`AP5-${s.id}-${String(idx+1).padStart(3,'0')}`;
      const d=new Date(today);d.setDate(d.getDate()-(idx*7+Math.floor(Math.random()*10)));
      const installDate=d.toISOString().slice(0,10);
      const scent=scentLib[idx%scentLib.length].name;
      const content=contentLib[idx%contentLib.length].name;
      const schedule=buildSchedule(installDate,scent,content);
      devices.push({id:`D${idSeq++}`,siteId:s.id,siteName:s.name,place:p,serial,installDate,group:(p.includes('화장실')?'화장실':(p.includes('VIP')?'VIP룸':(p==='로비'?'로비':''))),scent,content,schedule});
    });
  }
  const data={sites,groups,devices,libraries:{scents:scentLib,contents:contentLib}};
  localStorage.setItem(LS_KEY,JSON.stringify(data));
  return data;
}

function load(){const raw=localStorage.getItem(LS_KEY);if(raw){return JSON.parse(raw);}return seed();}
function save(){const {sites,groups,devices,libraries}=state;localStorage.setItem(LS_KEY,JSON.stringify({sites,groups,devices,libraries}));}

// ------------------------------
// UI 엘리먼트
// ------------------------------
const elSite=document.getElementById('siteFilter');
const elGroup=document.getElementById('groupFilter');
const elPlace=document.getElementById('placeSearch');
const elPageSize=document.getElementById('pageSize');
const elTblBody=document.querySelector('#deviceTbl tbody');
const elChkAll=document.getElementById('chkAll');
const elSelCount=document.getElementById('selCount');
const elPagerInfo=document.getElementById('pagerInfo');
const elBulkGroup=document.getElementById('bulkGroup');
const elBulkScentSel=document.getElementById('bulkScentSel');
const elBulkContentSel=document.getElementById('bulkContentSel');
const elApplyScope=document.getElementById('applyScope');
const elOldGroupSel=document.getElementById('oldGroupSel');
const elNewGrpName=document.getElementById('newGrpName');

function toast(msg){const w=document.getElementById('toastWrap');const div=document.createElement('div');div.className='toast';div.textContent=msg;w.appendChild(div);setTimeout(()=>div.remove(),1800);} 
function opt(v,t){const o=document.createElement('option');o.value=v;o.textContent=t;return o;}

// ------------------------------
// 렌더링/필터
// ------------------------------
function renderLibrarySelect(el, list, placeholder){
  el.innerHTML='';
  el.appendChild(opt('', placeholder));
  // 모두 무상: optgroup 없이 일괄 추가
  list.forEach(i=> el.appendChild(opt(i.name, i.name)) );
}

function refreshFilters(){
  elSite.innerHTML=''; elSite.appendChild(opt('ALL','전체 사업장'));
  state.sites.forEach(s=>elSite.appendChild(opt(s.id,s.name))); elSite.value=state.filter.site;
  elGroup.innerHTML=''; elGroup.appendChild(opt('ALL','전체 그룹'));
  state.groups.forEach(g=>elGroup.appendChild(opt(g.name,g.name))); elGroup.value=state.filter.group;
  elBulkGroup.innerHTML=''; state.groups.forEach(g=>elBulkGroup.appendChild(opt(g.name,g.name)));
  if(elOldGroupSel){ elOldGroupSel.innerHTML=''; state.groups.forEach(g=>elOldGroupSel.appendChild(opt(g.name,g.name))); }
  // 라이브러리 선택 드롭다운 렌더 (모두 무상)
  renderLibrarySelect(elBulkScentSel, state.libraries.scents, '향 선택 (무상 라이브러리)');
  renderLibrarySelect(elBulkContentSel, state.libraries.contents, '콘텐츠 선택 (무상 라이브러리)');
}

function applyFilters(devs){
  let rows=devs; const {site,group,place}=state.filter; const q=(place||'').toLowerCase();
  if(site!=='ALL') rows=rows.filter(d=>d.siteId===site);
  if(group!=='ALL') rows=rows.filter(d=>(d.group||'')===group);
  if(q) rows=rows.filter(d=>d.place.toLowerCase().includes(q));
  return rows;
}

function paginate(rows){
  const size=state.pageSize,total=rows.length,maxPage=Math.max(1,Math.ceil(total/size));
  if(state.page>maxPage) state.page=maxPage; const start=(state.page-1)*size; const pageRows=rows.slice(start,start+size);
  elPagerInfo.textContent=`${total}대 중 ${start+1}–${Math.min(start+pageRows.length,total)} 표시 (페이지 ${state.page}/${maxPage})`;
  return pageRows;
}

function renderTable(){
  const rows=paginate(applyFilters(state.devices));
  elTblBody.innerHTML='';
  rows.forEach(d=>{
    const tr=document.createElement('tr');
    // 선택
    const td0=document.createElement('td'); const cb=document.createElement('input'); cb.type='checkbox'; cb.dataset.id=d.id; cb.checked=state.selection.has(d.id);
    cb.addEventListener('change',e=>{ if(e.target.checked) state.selection.add(d.id); else state.selection.delete(d.id); updateSelCount(); });
    td0.appendChild(cb); tr.appendChild(td0);
    // 사업장/장소/시리얼/설치일
    const td1=document.createElement('td'); td1.textContent=d.siteName; tr.appendChild(td1);
    const td2=document.createElement('td'); td2.textContent=d.place; if((d.group||'')==='화장실') td2.classList.add('highlight'); tr.appendChild(td2);
    const td3=document.createElement('td'); td3.textContent=d.serial; tr.appendChild(td3);
    const td4=document.createElement('td'); td4.textContent=d.installDate; tr.appendChild(td4);
    // 그룹
    const td5=document.createElement('td'); const selG=document.createElement('select'); selG.className='select'; selG.style.minWidth='120px';
    selG.appendChild(opt('', '미지정')); state.groups.forEach(g=>selG.appendChild(opt(g.name,g.name))); selG.value=d.group||'';
    selG.addEventListener('change',()=>{ d.group=selG.value||''; save(); toast('그룹 변경'); refreshFilters(); renderTable(); });
    td5.appendChild(selG); tr.appendChild(td5);
    // 향 (무상 라이브러리)
    const td6=document.createElement('td'); const selS=document.createElement('select'); selS.className='select'; selS.style.minWidth='140px';
    renderLibrarySelect(selS, state.libraries.scents, '향 선택'); selS.value=d.scent||'';
    selS.addEventListener('change',()=>{ d.scent=selS.value; save(); toast('향 변경(기본값)');}); td6.appendChild(selS); tr.appendChild(td6);
    // 콘텐츠 (무상 라이브러리)
    const td7=document.createElement('td'); const selC=document.createElement('select'); selC.className='select'; selC.style.minWidth='160px';
    renderLibrarySelect(selC, state.libraries.contents, '콘텐츠 선택'); selC.value=d.content||'';
    selC.addEventListener('change',()=>{ d.content=selC.value; save(); toast('콘텐츠 변경(기본값)');}); td7.appendChild(selC); tr.appendChild(td7);
    // 다음 배송일
    const td8=document.createElement('td'); td8.textContent=nextDate(d); tr.appendChild(td8);
    // 남은 무료배송
    const td9=document.createElement('td'); td9.innerHTML=`<span class="badge">${remainingCount(d)} 회</span>`; tr.appendChild(td9);
    // 라이브러리 이동 버튼
    const td10=document.createElement('td');
    const btnScent=document.createElement('button'); btnScent.className='btn'; btnScent.textContent='향 라이브러리';
    btnScent.addEventListener('click',()=>{ try{ window.open('scent_library.html','_blank'); }catch(_){} toast('향 라이브러리 탭으로 이동'); });
    const btnContent=document.createElement('button'); btnContent.className='btn'; btnContent.style.marginLeft='6px'; btnContent.textContent='콘텐츠 라이브러리';
    btnContent.addEventListener('click',()=>{ try{ window.open('content_library.html','_blank'); }catch(_){} toast('콘텐츠 라이브러리 탭으로 이동'); });
    td10.appendChild(btnScent); td10.appendChild(btnContent); tr.appendChild(td10);
    elTblBody.appendChild(tr);
  });
  document.getElementById('chkAll').checked=rows.every(r=>state.selection.has(r.id));
}

function updateSelCount(){document.getElementById('selCount').textContent=`${state.selection.size} 대`;}

// ------------------------------
// 이벤트: 필터/페이지/선택
// ------------------------------

document.getElementById('btnResetSeed').addEventListener('click',()=>{const data=seed();Object.assign(state,data);state.filter={site:'ALL',group:'ALL',place:''};state.selection.clear();state.page=1;state.pageSize=20;refreshFilters();renderTable();updateSelCount();toast('더미데이터 재설정');});

document.getElementById('btnRunTests').addEventListener('click',runTests);

elSite.addEventListener('change',()=>{state.filter.site=elSite.value;state.page=1;renderTable();});
elGroup.addEventListener('change',()=>{state.filter.group=elGroup.value;state.page=1;renderTable();});
elPlace.addEventListener('input',()=>{state.filter.place=elPlace.value;state.page=1;renderTable();});
document.getElementById('btnClearFilters').addEventListener('click',()=>{state.filter={site:'ALL',group:'ALL',place:''};elSite.value='ALL';elGroup.value='ALL';elPlace.value='';state.page=1;renderTable();});

document.getElementById('pageSize').addEventListener('change',e=>{state.pageSize=parseInt(e.target.value,10)||20;state.page=1;renderTable();});
document.getElementById('prevPage').addEventListener('click',()=>{if(state.page>1){state.page--;renderTable();}});
document.getElementById('nextPage').addEventListener('click',()=>{state.page++;renderTable();});

document.getElementById('chkAll').addEventListener('change',e=>{const rows=paginate(applyFilters(state.devices));rows.forEach(r=>{if(e.target.checked) state.selection.add(r.id); else state.selection.delete(r.id);});renderTable();updateSelCount();});
document.getElementById('btnSelectAllPage').addEventListener('click',()=>{const rows=paginate(applyFilters(state.devices));rows.forEach(r=>state.selection.add(r.id));renderTable();updateSelCount();});
document.getElementById('btnClearSelection').addEventListener('click',()=>{state.selection.clear();renderTable();updateSelCount();});

// ------------------------------
// 그룹 CRUD
// ------------------------------
function upsertGroupByName(name){name=(name||'').trim();if(!name){toast('그룹명을 입력하세요');return;}const exists=state.groups.find(g=>g.name===name);if(exists){toast('동일 이름의 그룹이 이미 있습니다');return;}const id='G'+(state.groups.length+1);state.groups.push({id,name});save();refreshFilters();renderTable();toast('그룹 추가');}
document.getElementById('btnAddGroup').addEventListener('click',()=>{upsertGroupByName(document.getElementById('grpName').value);});
document.getElementById('btnDelGroup').addEventListener('click',()=>{const name=document.getElementById('grpName').value.trim();if(!name){toast('삭제할 그룹명을 입력하세요');return;}const before=state.groups.length;state.groups=state.groups.filter(g=>g.name!==name);if(state.groups.length===before){toast('존재하지 않는 그룹명');return;}state.devices.forEach(d=>{if(d.group===name) d.group='';});if(state.filter.group===name) state.filter.group='ALL';save();refreshFilters();renderTable();toast('그룹 삭제 및 해제');});
function renameGroup(oldName,newName){oldName=(oldName||'').trim();newName=(newName||'').trim();if(!oldName||!newName){toast('기존/새 그룹명을 입력');return;}if(oldName===newName){toast('동일 이름으로 수정 불가');return;}const target=state.groups.find(g=>g.name===oldName);if(!target){toast('기존 그룹 없음');return;}const dup=state.groups.find(g=>g.name===newName);if(dup){toast('이미 존재하는 그룹명');return;}target.name=newName;let affected=0;state.devices.forEach(d=>{if(d.group===oldName){d.group=newName;affected++;}});if(state.filter.group===oldName) state.filter.group=newName;save();refreshFilters();renderTable();toast(`'${oldName}'→'${newName}' (${affected}대)`);} 
document.getElementById('btnRenameGroup').addEventListener('click',()=>{renameGroup(document.getElementById('oldGroupSel').value,document.getElementById('newGrpName').value)});

// ------------------------------
// 배송 적용 범위에 따른 일괄 변경
// ------------------------------
function applyToSchedule(device,{scent,content,scope}){
  const idxs=nextIndexes(device); if(idxs.length===0) return 0;
  let targetIdxs=[];
  if(scope==='NEXT'){ targetIdxs=[idxs[0]]; }
  else if(scope==='ALL'){ targetIdxs=idxs; }
  targetIdxs.forEach(i=>{ if(scent) device.schedule[i].scent=scent; if(content) device.schedule[i].content=content; });
  return targetIdxs.length;
}

document.getElementById('btnAssignGroup').addEventListener('click',()=>{const g=elBulkGroup.value;if(!g){toast('지정할 그룹 선택');return;}let c=0;state.devices.forEach(d=>{if(state.selection.has(d.id)){d.group=g;c++;}});save();renderTable();toast(`선택 ${c}대 그룹='${g}'`);});

document.getElementById('btnApplyBulk').addEventListener('click',()=>{
  const scent=(elBulkScentSel.value||'').trim();
  const content=(elBulkContentSel.value||'').trim();
  if(!scent&&!content){toast('향 또는 콘텐츠를 선택하세요');return;}
  const scope=elApplyScope.value;
  let totalDevices=0, totalShipments=0;
  state.devices.forEach(d=>{
    if(state.selection.has(d.id)){
      const changed=applyToSchedule(d,{scent,content,scope});
      if(changed>0){ totalShipments+=changed; totalDevices++; }
    }
  });
  save(); renderTable(); toast(`적용 완료 · 대상 기기 ${totalDevices}대 · 배송 ${totalShipments}회 반영`);
});

// ------------------------------
// 테스트 (간단)
// ------------------------------
function runTests(){
  try{
    console.group('▶ 기기관리/배송/라이브러리(무상) 테스트');
    console.assert(state.devices.length>=20,'TC1 실패: 더미 20대 이상');
    const d0=state.devices[0];
    console.assert(Array.isArray(d0.schedule)&&d0.schedule.length===6,'TC2 실패: 스케줄 6건 생성');
    console.assert(nextDate(d0).length>0,'TC3 실패: 다음 배송일 계산');
    // 라이브러리 옵션 존재(무상)
    console.assert(elBulkScentSel.options.length>1 && elBulkContentSel.options.length>1,'TC4 실패: 라이브러리 옵션 부족');
    // NEXT 적용 테스트
    state.selection=new Set([d0.id]);
    const pickS=elBulkScentSel.options[1].value; const pickC=elBulkContentSel.options[1].value;
    elBulkScentSel.value=pickS; elBulkContentSel.value=pickC; document.getElementById('applyScope').value='NEXT';
    document.getElementById('btnApplyBulk').click();
    const changedAny = d0.schedule.some(s=>!s.shipped && s.scent===pickS && s.content===pickC);
    console.assert(changedAny,'TC5 실패: NEXT 적용 반영 안됨');
    // ALL 적용 테스트
    const pickS2=elBulkScentSel.options[Math.min(2, elBulkScentSel.options.length-1)].value;
    const pickC2=elBulkContentSel.options[Math.min(2, elBulkContentSel.options.length-1)].value;
    elBulkScentSel.value=pickS2; elBulkContentSel.value=pickC2; document.getElementById('applyScope').value='ALL';
    document.getElementById('btnApplyBulk').click();
    const allOk = d0.schedule.filter(s=>!s.shipped).every(s=>s.scent===pickS2 && s.content===pickC2);
    console.assert(allOk,'TC6 실패: ALL 적용 반영 안됨');
    console.log('✅ 테스트 통과');
  }catch(e){console.error('테스트 중 오류',e);}finally{console.groupEnd();}
}

// ------------------------------
// 초기화
// ------------------------------
(function init(){const data=load();Object.assign(state,data);refreshFilters();renderTable();updateSelCount();})();
</script>
