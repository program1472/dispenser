<?php /* content_lib.php (모달: 요금제/텍스트영역/첨부/장바구니, 스마트에디터 제거 + 팝업 안정화) */ ?>

<div class="sticky-filter">
  <div class="inner filter-bar">
    <span class="label small">사업장</span>
    <select id="siteFilter" class="select sm">
      <option value="ALL">전체</option><option value="S1">본점</option>
      <option value="S2">A지점</option><option value="S3">B지점</option>
    </select>

    <span class="label small">그룹</span>
    <select id="groupFilter" class="select sm">
      <option value="ALL">전체</option><option value="GRP_WC">화장실</option>
      <option value="GRP_HALL">홀</option><option value="GRP_LOBBY">로비</option>
    </select>

    <span class="label small">설치위치</span>
    <select id="locationFilter" class="select sm">
      <option value="ALL">전체</option><option value="LOC_1F_M">1F-남</option>
      <option value="LOC_1F_F">1F-여</option><option value="LOC_2F_M">2F-남</option>
    </select>

    <button id="searchBtn" class="btn cta sm">검색</button>
  </div>
</div>

<div class="wrap">
  <section class="card" id="tab-Content">
    <div class="card-hd">
      <div>
        <div class="card-ttl">콘텐츠선택</div>
        <div class="card-sub">무료 제공 6개 상단 타일 · 추가는 유료</div>
      </div>
    </div>

    <div class="card-bd">
      <!-- Free Grid -->
      <div class="cl-grid" id="freeScentGrid"></div>

      <hr class="sep">

      <!-- 검색 영역 -->
      <div class="card-hd">
        <div>
          <div class="card-ttl">콘텐츠라이브러리</div>
          <div class="card-sub">무료 제공 6개 상단 타일 · 추가는 유료</div>
        </div>
        <div class="inner filter-bar">
          <span class="label small">카테고리</span>
          <select id="libTypeFilter" class="select sm">
            <option value="ALL">전체</option>
            <option value="사무실/회사">사무실/회사</option>
            <option value="병원">병원</option>
            <option value="학교">학교</option>
            <option value="골프">골프</option>
            <option value="피트니스">피트니스</option>
            <option value="ART">ART</option>
          </select>

          <span class="label small">검색어</span>
          <input id="libTagInput" class="input" placeholder="태그검색">

          <button id="libSearchBtn" class="btn cta sm">검색</button>
        </div>
      </div>

      <!-- Paid Grid -->
      <div class="cl-grid" id="paidScentGrid"></div>

      <div class="small" style="margin-top:6px">※ Free 선택 시 연 6회 무료권에서 자동 차감. 초과 시 유료 전환.</div>
    </div>
  </section>
</div>

<script>
(function(){
  if (window.__contentLibBoot__) return;
  window.__contentLibBoot__ = true;

  window.CONTENT_LIB ||= {};
  window.CONTENT_LIB.REF = window.CONTENT_LIB.REF || [
    {"카테고리":"회사","품명":"오늘도 힘내요","이미지":"https://alltogreen.com/web/product/medium/202509/d68879e3c0878474adf9780d3e13767d.png"},
    {"카테고리":"ART","품명":"Woman Reading 1880","이미지":"https://alltogreen.com/web/product/medium/202509/9120bf4af50db7581d422cfa603f4295.png","type":"new"},
    {"카테고리":"피트니스","품명":"기구 살살 내려놓기","이미지":"https://alltogreen.com/web/product/medium/202509/03e6f3ea71a222d580d335d6fce0cf5f.png"},
    {"카테고리":"학교","품명":"등교 시간은 학교와 우리의 약속","이미지":"https://alltogreen.com/web/product/medium/202509/3081425bfb98a655f9f59afcf18af698.png"},
    {"카테고리":"병원_약국","품명":"예약 시간 준수 모두를 위한 배려","이미지":"https://alltogreen.com/web/product/medium/202509/2b3a5de5025519b16c4231b084d00a6c.png","type":"new"},
    {"카테고리":"골프장","품명":"동반자 스윙 시 정숙","이미지":"https://alltogreen.com/web/product/medium/202509/26a6caf78fdb96e161cee3235cee6772.png"},
    {"카테고리":"ART","품명":"Pastoral Landscape with Ruins 1664","이미지":"https://alltogreen.com/web/product/medium/202509/ab57ada7d53500e20b5d228ab0286464.png"},
    {"카테고리":"피트니스","품명":"쓸만큼만 가져가기","이미지":"https://alltogreen.com/web/product/medium/202509/b59344ce6dbcfef135506dfe7aa4c63c.png","type":"new"},
    {"카테고리":"병원_약국","품명":"물 마시기 건강의 시작","이미지":"https://alltogreen.com/web/product/medium/202509/77a6e647564d50ee4d3dbd821bf2f5ce.png"},
    {"카테고리":"회사","품명":"퇴근할 때 전원 끄기","이미지":"https://alltogreen.com/web/product/medium/202509/dee654db98da151f654a071e3f2105eb.png"},
    {"카테고리":"ART","품명":"At Mouquin’s 1905","이미지":"https://alltogreen.com/web/product/medium/202509/a99f52802ca9423df708f061ebca1c9e.png","type":"new"},
    {"카테고리":"골프장","품명":"골프에 티켓 지켜주세요","이미지":"https://alltogreen.com/web/product/medium/202509/f6f140dfafa94df61c33c2537d4ea6bc.png"},
    {"카테고리":"학교","품명":"쓰레기를 아무곳에나","이미지":"https://alltogreen.com/web/product/medium/202509/0f652a577e1723e9788be61e3fba942f.png"},
    {"카테고리":"ART","품명":"Woman at the Piano 1875","이미지":"https://alltogreen.com/web/product/medium/202509/cc7d8cfa12b743f304ad0afad4ac4960.png"},
    {"카테고리":"골프장","품명":"연습 스윙 적당하게","이미지":"https://alltogreen.com/web/product/medium/202509/125e4aa6a5d21938c3a904a5d69ebaca.png","type":"new"},
    {"카테고리":"회사","품명":"업무의 우선 순위 체크하기","이미지":"https://alltogreen.com/web/product/medium/202509/aaea91ce77f48f09b0c3658c0078364f.png"},
    {"카테고리":"병원_약국","품명":"건강 인간의 권리","이미지":"https://alltogreen.com/web/product/medium/202509/0ba4b4ba75a1f8896bea7d8010256127.png"},
    {"카테고리":"ART","품명":"The Fountain, Villa Torlonia, Frascati, Italy 1907","이미지":"https://alltogreen.com/web/product/medium/202509/68597521056fa8c2deb22c3bc212ab48.png"},
    {"카테고리":"피트니스","품명":"벤치프레스에서 딴짓하기 금지","이미지":"https://alltogreen.com/web/product/medium/202509/21125ee4e3e7f638a8c799e172e3cdab.png","type":"new"},
    {"카테고리":"학교","품명":"나는 친구의 방어자 입니다","이미지":"https://alltogreen.com/web/product/medium/202509/ba7fbe30469bded9bc431f6971fd0aca.png"},
    {"카테고리":"ART","품명":"The Basket of Apples 1893 - Paul Cezanne","이미지":"https://alltogreen.com/web/product/medium/202509/863b56d3462213e9876a6cd13979c970.png"},
    {"카테고리":"병원_약국","품명":"30초 이상 손씻기","이미지":"https://alltogreen.com/web/product/medium/202509/67139b90ab086a192a2252c96125e20e.png"},
    {"카테고리":"ART","품명":"Self-Portrait 1887 - 빈센트 반 고흐","이미지":"https://alltogreen.com/web/product/medium/202509/05d6dfac12a3cfd5bf8c2c690dd2594c.png","type":"new"},
    {"카테고리":"회사","품명":"팀워크 없는 팀은 그냥 워크","이미지":"https://alltogreen.com/web/product/medium/202509/6361387d34849b6f5d7b8322628866e6.png"},
    {"카테고리":"골프장","품명":"스윙하기 전 사람 있는지 확인","이미지":"https://alltogreen.com/web/product/medium/202509/c25f5c594658d985be583deeb05c7c7c.png"},
    {"카테고리":"ART","품명":"Irises 1914 - 모네","이미지":"https://alltogreen.com/web/product/medium/202509/221332d20ca0ecba9aa8d3fbc3523b8a.png"},
    {"카테고리":"회사","품명":"회의 중에 폰은 안녕","이미지":"https://alltogreen.com/web/product/medium/202509/625ed5a12923fc26200afffe728e9eba.png","type":"new"},
    {"카테고리":"골프장","품명":"그린에서 뛰지 않기","이미지":"https://alltogreen.com/web/product/medium/202509/89a747093c72beb7f518301cbc3584e3.png"},
    {"카테고리":"ART","품명":"Landscape with Two Poplars 1912","이미지":"https://alltogreen.com/web/product/medium/202509/f85ca8393dc3bf9648b9547933057996.png"},
    {"카테고리":"피트니스","품명":"준비운동은 필수 운동","이미지":"https://alltogreen.com/web/product/medium/202509/4191ae47022afc55ed5d86de3331ab87.png"},
    {"카테고리":"학교","품명":"음식은 먹을 만큼만","이미지":"https://alltogreen.com/web/product/medium/202509/7ce9d3aeb6fda73d34ea1ead3f86c7c1.png","type":"new"},
    {"카테고리":"피트니스","품명":"운동기구는 모두의 것","이미지":"https://alltogreen.com/web/product/medium/202509/1cd33ca020a73a4b4b351c36238d1fb2.png"},
    {"카테고리":"ART","품명":"Cliff Walk at Pourville 1882 - 모네","이미지":"https://alltogreen.com/web/product/medium/202509/659d04091ec8c29b2865883fd407d2e8.png"},
    {"카테고리":"병원_약국","품명":"임신 알레르기는 미리 알려주세요","이미지":"https://alltogreen.com/web/product/medium/202509/fe8bd9229b47dc10460be36514c861e2.png","type":"new"},
    {"카테고리":"골프장","품명":"퍼팅 라인 밟지 않기","이미지":"https://alltogreen.com/web/product/medium/202509/2d2a01c1142a55453c7f704c02b1ccfa.png"},
    {"카테고리":"ART","품명":"Improvisation No. 30 (Cannons) 1913","이미지":"https://alltogreen.com/web/product/medium/202509/e2f3ec801ca7e14e8c147afabe10507a.png"},
    {"카테고리":"병원_약국","품명":"기침이 있을 땐 마스크 착용","이미지":"https://alltogreen.com/web/product/medium/202509/e666914a0749d37b50589796d8ac0932.png"},
    {"카테고리":"ART","품명":"Woman at Her Toilette 1875","이미지":"https://alltogreen.com/web/product/medium/202509/7850445ac3270f31d0ff43bd4d2acf8e.png","type":"new"},
    {"카테고리":"회사","품명":"일도 성과도 제대로 터트리자","이미지":"https://alltogreen.com/web/product/medium/202509/4c35ef9c5f0fd2830fb04ceb1eac7255.png"},
    {"카테고리":"피트니스","품명":"무리하면 다쳐요","이미지":"https://alltogreen.com/web/product/medium/202509/fe1cf0eb687274b3ee3628d051948a6f.png"},
    {"카테고리":"ART","품명":"Still Life with Geranium 1906","이미지":"https://alltogreen.com/web/product/medium/202509/6b7747da26db396ee85f5a996e153899.png","type":"new"},
    {"카테고리":"학교","품명":"실내화를 신고 등·하교를 하지 않아요","이미지":"https://alltogreen.com/web/product/medium/202509/2d28f3d3a1c69491475862856e889f0d.png"},
    {"카테고리":"회사","품명":"퇴근 시간에 업무 멈춰","이미지":"https://alltogreen.com/web/product/medium/202509/de6ededbac2957d075295f480784e466.png"},
    {"카테고리":"학교","품명":"올바르게 학교 기물 사용","이미지":"https://alltogreen.com/web/product/medium/202509/637bd3eabd8a6334ef48d3f56334f7a0.png"},
    {"카테고리":"병원_약국","품명":"의사의 진찰에 경청해 주세요","이미지":"https://alltogreen.com/web/product/medium/202509/df6d083a665528e9dfe655666cd2d15d.png","type":"new"},
    {"카테고리":"ART","품명":"파리 거리 비오는 날 1877","이미지":"https://alltogreen.com/web/product/medium/202509/82bae1220eac063096f9276afe627e82.png"},
    {"카테고리":"피트니스","품명":"사용 후 제자리에","이미지":"https://alltogreen.com/web/product/medium/202509/456b40411ed5fe0ee45c45886a573b1f.png"},
    {"카테고리":"골프장","품명":"골프 카트로 카트라이더를","이미지":"https://alltogreen.com/web/product/medium/202509/629e593d73f476b082a0f1b6ebb69b9d.png","type":"new"},
    {"카테고리":"병원_약국","품명":"폐의 약품은 약국으로","이미지":"https://alltogreen.com/web/product/medium/202509/369dacf1790b21984dc681383a3606f4.png"},
    {"카테고리":"피트니스","품명":"휴지는 필요한 만큼만","이미지":"https://alltogreen.com/web/product/medium/202509/29382533b6d36d4c0dfa501ad8697fb1.png"},
    {"카테고리":"ART","품명":"Still Life—Strawberries, Nuts, &c. 1822","이미지":"https://alltogreen.com/web/product/medium/202509/f27f90efbebca67fa46440379e49dea4.png","type":"new"}
  ];
  window.CONTENT_LIB.__defaultPaid = [];

  const TODAY = new Date(2025, 9, 15);
  const ETA_START = new Date(2025, 1, 12);
  function fmt(d){ return [d.getFullYear(), String(d.getMonth()+1).padStart(2,'0'), String(d.getDate()).padStart(2,'0')].join('-'); }
  function addMonths(date, m){ const d=new Date(date.getTime()); const day=d.getDate(); d.setMonth(d.getMonth()+m); if(d.getDate()<day) d.setDate(0); return d; }
  function splitRandomUnique(arr, a, b){ const pool=arr.slice(); for(let i=pool.length-1;i>0;i--){ const j=Math.floor(Math.random()*(i+1)); [pool[i],pool[j]]=[pool[j],pool[i]]; } return [ pool.slice(0,a), pool.slice(a,a+b) ]; }
  function escapeHtml(s){ return String(s||'').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[m])); }

  function makeTitleLine(item){
    const isNew = item.type === 'new';
    const ttl = document.createElement('div');
    ttl.className = 'title-line' + (isNew ? '' : ' no-badge');
    ttl.style.cssText = 'margin-top:6px;font-weight:700';
    ttl.innerHTML = `<span class="name">${escapeHtml(item.품명)}</span>${isNew ? ' <span class="badge new">New</span>' : ''}`;
    return ttl;
  }
  function updateTitleLine(container, data){
    let ttl = container.querySelector('.title-line');
    if(!ttl){
      ttl = document.createElement('div');
      ttl.className = 'title-line';
      ttl.style.cssText = 'margin-top:6px;font-weight:700';
      const thumb = container.querySelector('.cl-thumb');
      container.insertBefore(ttl, thumb ? thumb.nextSibling : null);
    }
    const isNew = !!data.isNew;
    ttl.className = 'title-line' + (isNew ? '' : ' no-badge');
    ttl.innerHTML = `<span class="name">${escapeHtml(data.title||'')}</span>${isNew ? ' <span class="badge new">New</span>' : ''}`;
  }

  function markPastCards(root){
    const freeGrid = root.querySelector('#freeGrid, #freeScentGrid'); if(!freeGrid) return;
    const today = new Date(TODAY.getFullYear(), TODAY.getMonth(), TODAY.getDate()).getTime();
    freeGrid.querySelectorAll('.cl-card:not(.add-card)').forEach(card=>{
      const etaNode = Array.from(card.querySelectorAll('.small'))
        .find(el => /예상\s*배송일\s*:\s*\d{4}-\d{2}-\d{2}/.test(el.textContent));
      if(!etaNode) return;
      const m = etaNode.textContent.match(/(\d{4}-\d{2}-\d{2})/);
      if(!m) return;
      const eta = new Date(m[1]+'T00:00:00').getTime();
      card.classList.toggle('past', eta < today);
    });
  }

  function buildFreeCard(item, idx){
    const eta = addMonths(ETA_START, idx*2);
    const isPast = eta.getTime() < new Date(TODAY.getFullYear(), TODAY.getMonth(), TODAY.getDate()).getTime();

    const card = document.createElement('div');
    card.className = 'cl-card' + (isPast ? ' past' : '');
    card.style.position = 'relative';

    const seq = document.createElement('div');
    seq.style.cssText = 'position:absolute;top:6px;left:6px;background:rgba(255,255,255,0.7);border-radius:4px;padding:2px 6px;font-weight:700;';
    seq.textContent = String(idx+1);
    card.appendChild(seq);

    const thumb = document.createElement('div');
    thumb.className = 'cl-thumb';
    thumb.innerHTML = `<img src="${item.이미지}" alt="${escapeHtml(item.품명)}" style="width:100%;height:100%;object-fit:contain;border-radius:8px;">`;
    card.appendChild(thumb);

    card.appendChild(makeTitleLine(item));

    const tag = document.createElement('div');
    tag.className = 'small';
    tag.textContent = `태그: ${item.카테고리}`;
    card.appendChild(tag);

    const reg = document.createElement('div');
    reg.className = 'small';
    reg.textContent = `등록일 2025-10-01`;
    card.appendChild(reg);

    const etaDiv = document.createElement('div');
    etaDiv.className = 'small';
    etaDiv.style.color = 'var(--accent)';
    etaDiv.textContent = `예상 배송일: ${fmt(eta)}`;
    card.appendChild(etaDiv);

    const btn = document.createElement('button');
    btn.className = 'btn small change-btn';
    btn.style.cssText = 'margin-top:4px;width:100%';
    btn.type = 'button';
    btn.textContent = '변경';
    card.appendChild(btn);

    return card;
  }

  function buildPaidCard(item){
    const card = document.createElement('div');
    card.className = 'cl-card';
    card.setAttribute('draggable','true');

    const thumb = document.createElement('div');
    thumb.className = 'cl-thumb';
    thumb.innerHTML = `<img src="${item.이미지}" alt="${escapeHtml(item.품명)}" style="width:100%;height:100%;object-fit:contain;border-radius:8px;">`;
    card.appendChild(thumb);

    card.appendChild(makeTitleLine(item));

    const tag = document.createElement('div');
    tag.className = 'small';
    tag.textContent = `태그: ${item.카테고리}`;
    card.appendChild(tag);

    const price = document.createElement('div');
    price.className = 'small';
    price.textContent = '금액: 프린팅 22,000원';
    card.appendChild(price);

    const reg = document.createElement('div');
    reg.className = 'small';
    reg.textContent = `등록일 2025-10-08`;
    card.appendChild(reg);

    const btn = document.createElement('button');
    btn.className = 'btn small add-btn';
    btn.style.cssText = 'margin-top:4px;width:100%';
    btn.type = 'button';
    btn.textContent = 'Add';
    card.appendChild(btn);

    return card;
  }

  function clEscape(s){ return String(s||'').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[m])); }
  function clYmd(d){ return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`; }

  (function hidePreRenderedContentModal(){
    const m = document.getElementById('contentModal');
    if (!m) return;
    if (!m.classList.contains('hidden')) m.classList.add('hidden');
    m.style.display = 'none';
  })();

  const PLAN_INFO = {
    Basic:    { comment: '프린팅',            price: '11,000원' },
    Standard: { comment: '워딩변경',          price: '22,000원' },
    Deluxe:   { comment: '워딩 + 이미지 변경', price: '110,000원' },
    Premium:  { comment: '맞춤형',            price: '220,000원' }
  };

  function ensurePlanRow(container){
    if (container.querySelector('.plan-row')) return;
    const planWrap = document.createElement('div');
    planWrap.className = 'plan-row';
    planWrap.style.cssText = 'display:flex;gap:8px;align-items:center;margin-top:10px;flex-wrap:wrap;';
    planWrap.innerHTML = `
      <label class="label small" for="contentPlanSelect" style="min-width:60px;">옵션</label>
      <select id="contentPlanSelect" class="select sm">
        <option value="Basic">Basic</option>
        <option value="Standard">Standard</option>
        <option value="Deluxe">Deluxe</option>
        <option value="Premium">Premium</option>
      </select>
      <span class="small" id="contentPlanComment" style="opacity:0.9;"></span>
      <span class="small" id="contentPlanPrice" style="color:#d32f2f;font-weight:700;"></span>
    `;
    container.appendChild(planWrap);
  }

  function ensureEditorArea(container){
    if (container.querySelector('#contentEditor')) return;
    const editorWrap = document.createElement('div');
    editorWrap.className = 'editor-wrap';
    editorWrap.style.cssText = 'margin-top:12px;width:100%';
    editorWrap.innerHTML = `
      <label class="label small" for="contentEditor">요청사항</label>
      <textarea id="contentEditor" rows="8" class="input" style="width:100%;box-sizing:border-box;resize:vertical;"></textarea>
    `;
    container.appendChild(editorWrap);
  }

  function ensureAttachmentArea(container){
    if (container.querySelector('#contentFiles')) return;
    const fileWrap = document.createElement('div');
    fileWrap.className = 'file-wrap';
    fileWrap.style.cssText = 'margin-top:12px;width:100%';
    fileWrap.innerHTML = `
      <label class="label small" for="contentFiles">첨부파일</label>
      <input type="file" id="contentFiles" class="input" multiple style="width:100%;box-sizing:border-box;">
      <div class="muted small" style="margin-top:4px;">* 이미지/문서 등 여러 개 선택 가능</div>
    `;
    container.appendChild(fileWrap);
  }

  function ensureCartButton(container){
    if (container.querySelector('#contentAddToCartBtn')) return;
    const btnWrap = document.createElement('div');
    btnWrap.style.cssText = 'margin-top:12px;display:flex;justify-content:flex-end;';
    btnWrap.innerHTML = `<button type="button" id="contentAddToCartBtn" class="btn cta" data-action="cart">장바구니 추가</button>`;
    container.appendChild(btnWrap);
  }

  function showModal(modal){
    if(!modal) return;
    modal.classList.remove('hidden');
    modal.style.display = '';
  }
  function hideModal(modal){
    if(!modal) return;
    if(!modal.classList.contains('hidden')) modal.classList.add('hidden');
    modal.style.display = 'none';
  }

  function ensureContentModal(){
    let modal = document.getElementById('contentModal');
    if(!modal){
      const wrap=document.createElement('div');
      wrap.innerHTML=`
      <div id="contentModal" class="modal hidden" role="dialog" aria-modal="true" aria-labelledby="contentModalTitle" style="display:none;">
        <div class="modal-dim"></div>
        <div class="modal-panel">
          <div class="modal-hd">
            <div class="ttl" id="contentModalTitle">콘텐츠 선택</div>
            <div class="act">
              <button type="button" class="btn sm btn-outline" id="contentModalCloseBtn">닫기</button>
              <button type="button" class="btn sm btn-primary" id="contentModalApplyBtn">적용</button>
            </div>
          </div>
          <div class="modal-bd">
            <div class="modal-left">
              <div class="section-ttl">현재 슬롯</div>
              <div id="contentPickedPreview" class="cl-card preview"></div>
              <div class="eta-editor">
                <label class="label small" for="contentEtaInput">예상 배송일</label>
                <div class="eta-row">
                  <input type="date" id="contentEtaInput" class="input sm" />
                  
                </div>
                <div class="muted small">* 라이브러리 검색 없이 현재 슬롯 ETA만 변경합니다.</div>
              </div>
              <div class="content-extend"></div>
            </div>
            <div class="modal-right">
              <div class="searchbar">
                <label class="label small" for="contentModalTypeFilter">카테고리</label>
                <select id="contentModalTypeFilter" class="select sm">
                  <option value="ALL">전체</option>
                  <option value="사무실/회사">사무실/회사</option>
                  <option value="병원">병원</option>
                  <option value="학교">학교</option>
                  <option value="골프">골프</option>
                  <option value="피트니스">피트니스</option>
                  <option value="ART">ART</option>
                </select>

                <label class="label small" for="contentModalTagInput">검색어</label>
                <input id="contentModalTagInput" class="input" placeholder="태그/제목 검색">

                <button id="contentModalSearchBtn" class="btn cta sm">검색</button>
              </div>
              <div id="contentModalResults" class="cl-grid results-grid"></div>
            </div>
          </div>
        </div>
      </div>`;
      document.body.appendChild(wrap.firstElementChild);
      modal = document.getElementById('contentModal');
    }

    const extend = modal.querySelector('.content-extend') || modal.querySelector('.modal-left');
    if (extend){
      ensurePlanRow(extend);
      ensureEditorArea(extend);
      ensureAttachmentArea(extend);
      ensureCartButton(extend);
    }

    if(!modal.querySelector('#contentEtaInput')){
      const left = modal.querySelector('.modal-left') || modal.querySelector('.modal-bd');
      if (left){
        const etaWrap = document.createElement('div');
        etaWrap.className = 'eta-editor';
        etaWrap.innerHTML = `
          <label class="label small" for="contentEtaInput">예상 배송일</label>
          <div class="eta-row">
            <input type="date" id="contentEtaInput" class="input sm" />
            
          </div>
          <div class="muted small">* 라이브러리 검색 없이 현재 슬롯 ETA만 변경합니다.</div>
        `;
        left.appendChild(etaWrap);
      }
    }
  }

  function clExtractCardData(card){
    const img = card.querySelector('.cl-thumb img');
    const ttlEl = card.querySelector('.title-line .name') || card.querySelector('.title-line');
    const title = (ttlEl?.textContent || '').replace(/\s*New\s*$/i,'').trim();
    const is_new = !!card.querySelector('.badge.new');
    const type = (Array.from(card.querySelectorAll('.small')).find(el=>/^타입\s*:/.test(el.textContent))?.textContent||'').replace(/^타입\s*:\s*/, '');
    const tags = (Array.from(card.querySelectorAll('.small')).find(el=>/^태그\s*:/.test(el.textContent))?.textContent||'').replace(/^태그\s*:\s*/, '');
    const reg_date = (Array.from(card.querySelectorAll('.small')).find(el=>/^등록일\s*:?\s*\d{4}-\d{2}-\d{2}$/.test(el.textContent.trim()))?.textContent.match(/(\d{4}-\d{2}-\d{2})/)||[])[1] || '';
    const eta = (Array.from(card.querySelectorAll('.small')).find(el=>/예상\s*배송일\s*:/.test(el.textContent))?.textContent.match(/(\d{4}-\d{2}-\d{2})/)||[])[1] || '';
    return { img: img?.src || '', alt: img?.alt || title || '', title, is_new, type, tags, reg_date, eta };
  }

  function clRenderPreview(container, data){
    const etaHtml = data.eta ? `<div class="small" style="color:var(--accent);">예상 배송일: ${clEscape(data.eta)}</div>` : '';
    container.innerHTML = `
      <div class="cl-thumb">
        <img src="${clEscape(data.img||'')}" alt="${clEscape(data.alt||data.title||'')}"
             style="width:100%;height:100px;object-fit:cover;border-radius:8px;">
      </div>
      <div class="title-line${data.is_new ? '' : ' no-badge'}" style="margin-top:6px;font-weight:700">
        <span class="name">${clEscape(data.title||'')}</span>
        ${data.is_new ? '<span class="badge new">New</span>' : ''}
      </div>
      ${data.type ? `<div class="small">타입: ${clEscape(data.type)}</div>` : ''}
      ${data.tags ? `<div class="small tag-line">태그: ${clEscape(String(data.tags))}</div>` : ''}
      ${data.reg_date ? `<div class="small">등록일: ${clEscape(data.reg_date)}</div>` : ''}
      ${etaHtml}
    `.trim();
  }

  function clReplaceCard(card, data){
    let img = card.querySelector('.cl-thumb img');
    if(!img){
      const thumb=document.createElement('div'); thumb.className='cl-thumb';
      thumb.innerHTML=`<img style="width:100%;height:100%;object-fit:contain;border-radius:8px;">`;
      card.insertBefore(thumb, card.firstChild||null);
      img=thumb.querySelector('img');
    }
    img.src=data.img||''; img.alt=data.alt||data.title||'';

    let ttl = card.querySelector('.title-line');
    if(!ttl){
      ttl = document.createElement('div');
      ttl.className = 'title-line';
      ttl.style.cssText = 'margin-top:6px;font-weight:700';
      const afterThumb = card.querySelector('.cl-thumb');
      card.insertBefore(ttl, afterThumb ? afterThumb.nextSibling : null);
    }
    ttl.classList.toggle('no-badge', !data.is_new);
    ttl.innerHTML = `<span class="name">${escapeHtml(data.title||'')}</span>` + (data.is_new ? ' <span class="badge new">New</span>' : '');

    let typeNode = Array.from(card.querySelectorAll('.small')).find(el=>/^타입\s*:/.test(el.textContent));
    if(data.type){
      if(!typeNode){ typeNode=document.createElement('div'); typeNode.className='small'; ttl.after(typeNode); }
      typeNode.textContent = `타입: ${data.type}`;
    } else if (typeNode){ typeNode.remove(); }

    let tag = Array.from(card.querySelectorAll('.small')).find(el=>/^태그\s*:/.test(el.textContent));
    if(data.tags){
      if(!tag){ tag=document.createElement('div'); tag.className='small'; (typeNode||ttl).after(tag); }
      tag.textContent = `태그: ${data.tags}`;
    } else if (tag){ tag.remove(); }

    let reg = Array.from(card.querySelectorAll('.small')).find(el=>/^등록일\s*:?\s*\d{4}-\d{2}-\d{2}$/.test(el.textContent.trim()));
    if(data.reg_date){
      if(!reg){ reg=document.createElement('div'); reg.className='small'; (tag||typeNode||ttl).after(reg); }
      reg.textContent = `등록일: ${data.reg_date}`;
    } else if (reg){ reg.remove(); }

    let etaNode = Array.from(card.querySelectorAll('.small')).find(el=>/예상\s*배송일\s*:/.test(el.textContent));
    if(data.eta){
      if(!etaNode){
        etaNode=document.createElement('div'); etaNode.className='small'; etaNode.style.color='var(--accent)';
        (reg||tag||typeNode||ttl).after(etaNode);
      }
      etaNode.textContent = `예상 배송일: ${data.eta}`;
    } else if (etaNode){ etaNode.remove(); }

    try { markPastCards(document); } catch(_){}
  }

  function clModalFilterItems(term, type){
    const all = window.CONTENT_LIB.REF || [];
    const norm = (s)=>String(s||'').toLowerCase();
    const t = norm(term);
    const ty = String(type||'ALL').trim();
    return all.filter(x=>{
      const hay = (norm(x.카테고리)+' '+norm(x.품명));
      const byTerm = !t || hay.includes(t);
      const byType = (ty==='ALL') || hay.includes(norm(ty));
      return byTerm && byType;
    });
  }

  function clRenderModalResults(container, items){
    container.innerHTML = '';
    items.forEach(item=>{
      const isNew = item.type === 'new';
      const card = document.createElement('div');
      card.className = 'cl-card';
      card.innerHTML = `
        <div class="cl-thumb">
          <img src="${clEscape(item.이미지)}" alt="${clEscape(item.품명)}"
               style="width:100%;height:100px;object-fit:cover;border-radius:8px;">
        </div>
        <div class="title-line${isNew ? '' : ' no-badge'}" style="margin-top:6px;font-weight:700">
          <span class="name">${clEscape(item.품명)}</span>
          ${isNew ? '<span class="badge new">New</span>' : ''}
        </div>
        <div class="small">태그: ${clEscape(item.카테고리||'')}</div>
        <div class="small">등록일: 2025-10-08</div>
        <button type="button" class="btn small pick-btn" style="margin-top:4px;width:100%;">선택</button>
      `.trim();
      container.appendChild(card);
    });
  }

  function renderPaid(root, items){
    const paidGrid = root.querySelector('#paidGrid, #paidScentGrid');
    if(!paidGrid) return;
    paidGrid.innerHTML = '';
    items.forEach(item=> paidGrid.appendChild(buildPaidCard(item)));
    mountDnDReplace(root);
  }

  function renderOnce(root){
    const freeGrid = root.querySelector('#freeGrid, #freeScentGrid');
    const paidGrid = root.querySelector('#paidGrid, #paidScentGrid');
    const host = root.querySelector('#tab-Content');
    if(!freeGrid || !paidGrid || !host) return;
    if(host.dataset.inited === '1') return;

    const ref = window.CONTENT_LIB.REF && window.CONTENT_LIB.REF.length ? window.CONTENT_LIB.REF : [];
    const [freeData, paidData] = splitRandomUnique(ref, 6, 12);

    freeGrid.innerHTML = '';
    paidGrid.innerHTML = '';

    freeData.forEach((item, idx)=> freeGrid.appendChild(buildFreeCard(item, idx)));

    const addBtn = document.createElement('button');
    addBtn.type = 'button';
    addBtn.id = 'addScentBtn';
    addBtn.className = 'cl-card add-card';
    addBtn.setAttribute('aria-label','향 추가');
    addBtn.innerHTML = `<div class="add-icon">+</div>`;
    freeGrid.appendChild(addBtn);

    renderPaid(root, paidData);
    window.CONTENT_LIB.__defaultPaid = paidData.slice();

    markPastCards(root);
    host.dataset.inited = '1';
    bindQuery(root);
  }

  function bindQuery(root){
    const $doc = window.jQuery || { off(){}, on(){} };
    const doFilter = ()=>{
      const term = String((root.querySelector('#libTagInput')?.value || '')).trim().toLowerCase();
      const type = String((root.querySelector('#libTypeFilter')?.value || 'ALL')).trim();

      const all = window.CONTENT_LIB.REF || [];
      const filtered = all.filter(x=>{
        const hay = [String(x.카테고리||''), String(x.품명||'')].join(' ').toLowerCase();
        const byTerm = !term || hay.includes(term);
        const byType = (type==='ALL') || hay.includes(String(type).toLowerCase());
        return byTerm && byType;
      });
      renderPaid(root, (term || type!=='ALL') ? filtered.slice(0,24) : (window.CONTENT_LIB.__defaultPaid.length ? window.CONTENT_LIB.__defaultPaid : all.slice(0,12)));
    };

    $doc.off?.('click', '#libSearchBtn')?.on?.('click', '#libSearchBtn', doFilter);
    $doc.off?.('keydown', '#libTagInput')?.on?.('keydown', '#libTagInput', function(e){ if(e.key==='Enter') doFilter(); });
    $doc.off?.('change', '#libTypeFilter')?.on?.('change', '#libTypeFilter', doFilter);
  }

  window.CONTENT_LIB.mount = function(){
    const root = document;
    const host = root.querySelector('#tab-Content');
    if(!host) return;
    host.dataset.inited = '0';
    renderOnce(root);
  };

  function ensureMount(){
    let tries = 0;
    const tick = setInterval(()=>{
      const host = document.querySelector('#tab-Content');
      if(host){
        renderOnce(document);
        clearInterval(tick);
      }
      if(++tries > 40) clearInterval(tick);
    }, 50);
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', ensureMount, { once:true });
  } else {
    ensureMount();
  }

  const mo = new MutationObserver((muts)=>{
    for(const m of muts){
      m.addedNodes && m.addedNodes.forEach(node=>{
        if(!(node instanceof HTMLElement)) return;
        if(node.id === 'tab-Content' || node.querySelector?.('#tab-Content')){
          window.CONTENT_LIB.mount();
        }
      });
    }
  });
  mo.observe(document.body, {childList:true, subtree:true});

  function mountDnDReplace(root){
    const $ = window.jQuery; if(!$) return;
    const $paid = $((root.querySelector('#paidGrid, #paidScentGrid')));
    const $free = $((root.querySelector('#freeGrid, #freeScentGrid')));
    if(!$paid.length || !$free.length) return;

    $paid.off('dragstart', '.cl-card')
         .on('dragstart', '.cl-card', function(e){
            const card = this;
            card.classList.add('dragging');
            e.originalEvent.dataTransfer.effectAllowed = 'copy';
            e.originalEvent.dataTransfer.setData('application/json', JSON.stringify(extractCardData(card)));
         });
    $paid.off('dragend', '.cl-card')
         .on('dragend', '.cl-card', function(){ this.classList.remove('dragging'); });

    $free.off('dragenter', '.cl-card:not(.add-card)')
         .on('dragenter', '.cl-card:not(.add-card)', function(e){ e.preventDefault(); this.classList.add('drop-on'); });
    $free.off('dragover', '.cl-card:not(.add-card)')
         .on('dragover',  '.cl-card:not(.add-card)', function(e){ e.preventDefault(); e.originalEvent.dataTransfer.dropEffect='copy'; });
    $free.off('dragleave', '.cl-card:not(.add-card)')
         .on('dragleave', '.cl-card:not(.add-card)', function(){ this.classList.remove('drop-on'); });
    $free.off('drop', '.cl-card:not(.add-card)')
         .on('drop', '.cl-card:not(.add-card)', function(e){
            e.preventDefault();
            const target = this;
            target.classList.remove('drop-on');
            let data;
            try { data = JSON.parse(e.originalEvent.dataTransfer.getData('application/json')||'{}'); } catch(_) { return; }
            if(!data.title) return;
            replaceCardContents(target, {
              img: data.imgSrc || data.img || '',
              alt: data.imgAlt || data.alt || data.title || '',
              title: data.title,
              is_new: !!(data.is_new ?? data.isNew),
              type: data.type || '',
              tags: data.tags || '',
              reg_date: data.registeredAt || data.reg_date || '',
              eta: data.eta || ''
            });
            markPastCards(root);
         });

    function extractCardData(card){
      const img=card.querySelector('.cl-thumb img');
      const title=(card.querySelector('.title-line .name')?.textContent || card.querySelector('.title-line')?.textContent || '').trim();
      const isNew=!!card.querySelector('.title-line .badge.new');
      const tagLine=Array.from(card.querySelectorAll('.small')).find(el=>/^태그\s*:/.test(el.textContent))?.textContent||'';
      const tags=tagLine.replace(/^태그\s*:\s*/, '');
      const regLine=Array.from(card.querySelectorAll('.small')).find(el=>/^등록일\s*\d{4}-\d{2}-\d{2}$/.test(el.textContent.trim()))?.textContent||'';
      const registeredAt=(regLine.match(/(\d{4}-\d{2}-\d{2})/)||[])[1]||'';
      return { imgSrc:img?.src||'', imgAlt:img?.alt||title||'', title, isNew, tags, registeredAt };
    }

    function replaceCardContents(card, data){
      let img = card.querySelector('.cl-thumb img');
      if(!img){
        const thumb=document.createElement('div'); thumb.className='cl-thumb';
        thumb.innerHTML=`<img style="width:100%;height:100%;object-fit:contain;border-radius:8px;">`;
        card.insertBefore(thumb, card.firstChild||null);
        img=thumb.querySelector('img');
      }
      img.src=data.img||''; img.alt=data.alt||data.title||'';

      updateTitleLine(card, { title:data.title, isNew:data.is_new });

      let tag=Array.from(card.querySelectorAll('.small')).find(el=>/^태그\s*:/.test(el.textContent));
      if(data.tags){
        if(!tag){ tag=document.createElement('div'); tag.className='small';
          const ttl = card.querySelector('.title-line'); ttl.after(tag);
        }
        tag.textContent=`태그: ${data.tags}`;
      }else if(tag){ tag.remove(); }

      let reg=Array.from(card.querySelectorAll('.small')).find(el=>/^등록일\s*\d{4}-\d{2}-\d{2}$/.test(el.textContent.trim()));
      if(data.reg_date){
        if(!reg){ reg=document.createElement('div'); reg.className='small';
          (tag || card.querySelector('.title-line')).after(reg);
        }
        reg.textContent=`등록일 ${data.reg_date}`;
      }

      if (data.eta){
        let etaNode = Array.from(card.querySelectorAll('.small')).find(el=>/예상\s*배송일\s*:/.test(el.textContent));
        if(!etaNode){
          etaNode = document.createElement('div');
          etaNode.className='small';
          etaNode.style.color='var(--accent)';
          (reg || tag || card.querySelector('.title-line')).after(etaNode);
        }
        etaNode.textContent = `예상 배송일: ${data.eta}`;
      }
    }
  }

  function applyPlanUI(modal){
    const mode = modal?.dataset?.mode || 'change';
    const sel = modal.querySelector('#contentPlanSelect');
    const cm  = modal.querySelector('#contentPlanComment');
    const pr  = modal.querySelector('#contentPlanPrice');
    const editorWrap = modal.querySelector('.editor-wrap');
    const fileWrap = modal.querySelector('.file-wrap');
    const cartBtn = modal.querySelector('#contentAddToCartBtn');

    if(!sel || !cm || !pr || !cartBtn) return;

    const basicOpt = sel.querySelector('option[value="Basic"]');
    const stdOpt   = sel.querySelector('option[value="Standard"]');
    if (basicOpt) { basicOpt.disabled = false; basicOpt.removeAttribute('disabled'); }
    if (stdOpt)   { stdOpt.disabled = false;   stdOpt.removeAttribute('disabled'); }

    const plan = sel.value || 'Basic';
    const info = PLAN_INFO[plan] || PLAN_INFO.Basic;

    cm.textContent = `· ${info.comment}`;
    pr.textContent = (mode === 'change' && plan === 'Basic') ? '· 무료' : `· ${info.price}`;

    if (mode === 'change' && plan === 'Basic') {
      if (editorWrap) editorWrap.style.display = 'none';
      if (fileWrap) fileWrap.style.display = 'none';
      cartBtn.textContent = '적용';
      cartBtn.dataset.action = 'apply';
    } else {
      if (editorWrap) editorWrap.style.display = '';
      if (fileWrap) fileWrap.style.display = '';
      cartBtn.textContent = '장바구니 추가';
      cartBtn.dataset.action = 'cart';
    }
  }

  (function bindChangePopup(){
    if (window.__content_change_bound__) return;
    window.__content_change_bound__ = true;

    let targetCard = null;
    let pickedData = null;

    function openModal(fromCard, mode){
      ensureContentModal();
      const modal = document.getElementById('contentModal');
      modal.dataset.mode = mode || 'change';

      targetCard = fromCard || null;
      if (fromCard) {
        pickedData = clExtractCardData(fromCard);
      } else {
        pickedData = pickedData || {};
      }

      const preview = document.getElementById('contentPickedPreview');
      const etaInput = document.getElementById('contentEtaInput');
      if (preview) clRenderPreview(preview, pickedData || {});
      if (etaInput){
        etaInput.min = clYmd(new Date());
        etaInput.value = pickedData?.eta || '';
      }

      const box = document.getElementById('contentModalResults');
      if (box) clRenderModalResults(box, (window.CONTENT_LIB.REF||[]).slice(0,12));

      const sel = modal.querySelector('#contentPlanSelect');
      if (modal.dataset.mode === 'add') {
        if (sel) sel.value = 'Standard';
      } else {
        if (sel && !sel.value) sel.value = 'Basic';
      }
      applyPlanUI(modal);
      if (sel && !sel.__bound) {
        sel.addEventListener('change', ()=> applyPlanUI(modal));
        sel.__bound = true;
      }

      showModal(modal);
    }

    function closeModal(){
      const modal = document.getElementById('contentModal');
      if (modal) {
        hideModal(modal);
        delete modal.dataset.picked;
        delete modal.dataset.mode;
      }
      targetCard = null;
      pickedData = null;
    }

    function applyModal(){
      if(!targetCard) return;
      const modal = document.getElementById('contentModal');
      if (modal?.dataset?.picked){
        try { pickedData = JSON.parse(modal.dataset.picked); } catch(_){}
      }
      const etaInput = document.getElementById('contentEtaInput');
      if(etaInput){
        pickedData = pickedData || clExtractCardData(targetCard);
        pickedData.eta = etaInput.value || pickedData.eta || '';
      }
      clReplaceCard(targetCard, pickedData);
      closeModal();
    }

    function addToCartFromModal(){
      const modal = document.getElementById('contentModal');
      let current = null;
      if (modal?.dataset?.picked){
        try { current = JSON.parse(modal.dataset.picked); } catch(_){}
      }
      if (!current && targetCard) {
        current = clExtractCardData(targetCard);
      }
      current = current || {};

      const plan = document.getElementById('contentPlanSelect')?.value || 'Standard';
      const planDetail = PLAN_INFO[plan] || PLAN_INFO.Standard;
      const memoText = document.getElementById('contentEditor')?.value || '';
      const filesEl = document.getElementById('contentFiles');
      const files = filesEl ? Array.from(filesEl.files || []) : [];

      console.log('[장바구니 추가]', { current, plan, planDetail, memoTextLength: memoText.length, files: files.map(f=>({name:f.name,size:f.size})) });

      alert('장바구니에 추가했습니다.');
      closeModal();
    }

    document.addEventListener('click', function(e){
      const btn = e.target.closest('#tab-Content #freeScentGrid .change-btn, #tab-Content #freeGrid .change-btn');
      if(!btn) return;
      const card = btn.closest('.cl-card');
      if(!card) return;
      openModal(card, 'change');
    });

    document.addEventListener('click', function(e){
      const addBtn = e.target.closest('#addScentBtn, .cl-card.add-card .add-icon, .cl-card.add-card');
      if(!addBtn) return;
      openModal(null, 'add');
    });

    // 수정: 유료 카드 Add 클릭 시, 해당 카드를 전달하여 프리뷰 즉시 표시
    document.addEventListener('click', function(e){
      const btn = e.target.closest('#paidScentGrid .add-btn, #paidGrid .add-btn');
      if(!btn) return;
      const card = btn.closest('.cl-card');
      if(!card) return;
      openModal(card, 'add');
    });

    document.addEventListener('click', function(e){
      if(e.target.closest('#contentModalCloseBtn')) { e.preventDefault(); return closeModal(); }
      if(e.target.closest('#contentModalApplyBtn') || e.target.closest('#contentEtaApplyBtn')) {
        e.preventDefault();
        const modal = document.getElementById('contentModal');
        if (modal?.dataset?.mode === 'add') {
          return addToCartFromModal();
        } else {
          return applyModal();
        }
      }
    });

    document.addEventListener('click', function(e){
      if (e.target && e.target.classList && e.target.classList.contains('modal-dim')){
        const m = e.target.closest('#contentModal'); if(m) hideModal(m);
      }
    });

    document.addEventListener('keydown', function(e){
      if (e.key !== 'Enter') return;
      const input = document.getElementById('contentEtaInput');
      if (input && document.activeElement === input){
        e.preventDefault();
        const modal = document.getElementById('contentModal');
        if (modal?.dataset?.mode === 'add') {
          addToCartFromModal();
        } else {
          applyModal();
        }
      }
    });

    document.addEventListener('click', function(e){
      const pickBtn = e.target.closest('#contentModalResults .pick-btn');
      if(pickBtn){
        const card = pickBtn.closest('.cl-card');
        if(!card) return;
        const img = card.querySelector('.cl-thumb img');
        const nameEl = card.querySelector('.title-line .name');
        const isNew = !!card.querySelector('.badge.new');
        const tagEl = Array.from(card.querySelectorAll('.small')).find(el=>/^태그\s*:/.test(el.textContent));
        const regEl = Array.from(card.querySelectorAll('.small')).find(el=>/^등록일\s*:/.test(el.textContent));

        const picked = {
          img: img?.src || '',
          alt: img?.alt || (nameEl?.textContent||''),
          title: (nameEl?.textContent||''),
          is_new: isNew,
          type: '',
          tags: tagEl ? tagEl.textContent.replace(/^태그\s*:\s*/, '') : '',
          reg_date: regEl ? regEl.textContent.replace(/^등록일\s*:\s*/, '') : '',
          eta: ''
        };
        const preview = document.getElementById('contentPickedPreview');
        if(preview) clRenderPreview(preview, picked);
        const modal = document.getElementById('contentModal');
        if(modal) modal.dataset.picked = JSON.stringify(picked);
      }

      if(e.target.closest('#contentModalSearchBtn')){
        const type = document.getElementById('contentModalTypeFilter')?.value || 'ALL';
        const term = document.getElementById('contentModalTagInput')?.value || '';
        const list = clModalFilterItems(term, type).slice(0, 24);
        const box = document.getElementById('contentModalResults');
        if(box) clRenderModalResults(box, list);
      }
    });

    document.addEventListener('keydown', function(e){
      if (e.key !== 'Enter') return;
      const input = document.getElementById('contentModalTagInput');
      if (input && document.activeElement === input){
        e.preventDefault();
        const type = document.getElementById('contentModalTypeFilter')?.value || 'ALL';
        const term = input.value || '';
        const list = clModalFilterItems(term, type).slice(0, 24);
        const box = document.getElementById('contentModalResults');
        if(box) clRenderModalResults(box, list);
      }
    });
    document.addEventListener('change', function(e){
      if(e.target && e.target.id === 'contentModalTypeFilter'){
        const type = e.target.value || 'ALL';
        const term = document.getElementById('contentModalTagInput')?.value || '';
        const list = clModalFilterItems(term, type).slice(0, 24);
        const box = document.getElementById('contentModalResults');
        if(box) clRenderModalResults(box, list);
      }
    });

    document.addEventListener('click', function(e){
      const btn = e.target.closest('#contentAddToCartBtn');
      if (!btn) return;

      const pickedPreview = document.getElementById('contentPickedPreview');
      const hasSelection = (()=>{
        if (!pickedPreview) return false;
        if (pickedPreview.querySelector('.cl-card, .card, .picked, [data-picked], .item, .row, .r')) return true;
        const txt = pickedPreview.textContent ? pickedPreview.textContent.replace(/\s+/g,'').trim() : '';
        return txt.length > 0;
      })();
      if (!hasSelection) {
        alert('먼저 항목을 선택해 주세요.');
        return;
      }

      const action   = btn.dataset.action || 'cart';
      const etaInput = document.getElementById('contentEtaInput');
      const etaStr   = (etaInput && etaInput.value ? etaInput.value.trim() : '');
      const etaDate  = parseYmd(etaStr);
      const today    = startOfDay(new Date());

      if (action === 'apply') {
        return applyModal();
      }

      if (!etaStr || !etaDate) {
        alert('배송일을 지정해 주세요.');
        return;
      }
      if (etaDate < today) {
        alert('과거 날짜는 지정할 수 없습니다. 배송일을 다시 선택해 주세요.');
        return;
      }
      const threeBizDays = addBusinessDays(today, 3);
      if (etaDate <= threeBizDays) {
        alert('배송 준비에 최소 영업일 기준 3일이 필요합니다. 더 여유 있는 날짜로 지정해 주세요.');
        return;
      }

      return addToCartFromModal();
    });
  })();

  const $j = window.jQuery;
  $j?.(document).off('click', '#resetSeed').on('click', '#resetSeed', function(){
    const host = document.querySelector('#tab-Content');
    if(host) host.dataset.inited = '0';
    window.CONTENT_LIB.mount();
  });

})();

function startOfDay(d){ return new Date(d.getFullYear(), d.getMonth(), d.getDate()); }
function parseYmd(s){
  if(!s || !/^\d{4}-\d{2}-\d{2}$/.test(s)) return null;
  const [y, m, d] = s.split('-').map(Number);
  return new Date(y, m-1, d);
}
function addBusinessDays(d, n){
  let dt = startOfDay(d);
  let added = 0;
  while (added < n) {
    dt.setDate(dt.getDate() + 1);
    const day = dt.getDay();
    if (day !== 0 && day !== 6) added++;
  }
  return dt;
}

</script>
