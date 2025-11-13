<div class="sticky-filter">
  <div class="inner">
    <span class="small" style="font-weight:700;color:var(--accent)">사업장</span>
    <select id="siteFilter" class="select"><option value="ALL">전체</option><option value="S1">본점</option><option value="S2">A지점</option><option value="S3">B지점</option></select>
<span class="small" style="font-weight:700;color:var(--accent)">그룹</span>
    <select id="siteFilter" class="select"><option value="ALL">전체</option><option value="S1">화장실</option><option value="S2">홀</option><option value="S3">로비</option></select>	
<span class="small" style="font-weight:700;color:var(--accent)">설치위치</span>
    <select id="siteFilter" class="select"><option value="ALL">전체</option><option value="S1">1F-남</option><option value="S2">1F-여</option><option value="S3">2F-남</option></select>	
    <button id="resetSeed" class="btn">테스트 데이터 재설정</button>
  </div>
</div>
<div class="wrap">
<section class="card" id="tab-Help">
    <div class="card-hd"><div><div class="card-ttl">도움</div><div class="card-sub">FAQ / 문의 등록 / 연락처</div></div></div>
    <div class="card-bd">
      <div class="grid-2">
        <div class="card"><div class="card-hd"><div class="card-ttl">자주 묻는 질문</div></div><div class="card-bd" id="faqBox"><div style="margin-bottom: 10px;"><div style="font-weight:600;color:var(--accent)">무료 프린팅은 몇 회 제공되나요?</div><div class="small">연 6회 제공되며 Free 콘텐츠 선택 시 자동 차감됩니다.</div></div><div style="margin-bottom: 10px;"><div style="font-weight:600;color:var(--accent)">오일은 언제 배송되나요?</div><div class="small">설치일을 기준으로 2개월마다 자동 배송됩니다.</div></div><div style="margin-bottom: 10px;"><div style="font-weight:600;color:var(--accent)">다수 사업장을 어떻게 전환하나요?</div><div class="small">상단 고정 사업장 필터에서 "전체" 또는 지점을 선택하면 모든 탭이 동기화됩니다.</div></div></div></div>
        <div class="card"><div class="card-hd"><div class="card-ttl">문의 등록</div><div class="card-sub">티켓이 생성됩니다</div></div>
          <div class="card-bd">
            <div class="grid-2">
              <input id="csName" class="input" placeholder="성명">
              <input id="csPhone" class="input" placeholder="연락처">
            </div>
            <input id="csSubject" class="input" style="margin-top:8px" placeholder="제목">
            <textarea id="csBody" class="input" style="margin-top:8px;height:120px" placeholder="내용"></textarea>
            <div style="margin-top:8px;display:flex;gap:8px;align-items:center">
              <button class="btn primary" id="csSubmit">문의 등록</button>
              <span class="small">평일 09-18 응대</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>