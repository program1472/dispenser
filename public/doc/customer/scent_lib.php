<?php
// --- 유틸 ---
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

// --- 데이터(JSON 하드코딩) ---
// freeJson: price 하드코딩(15,000원 안팎, 100원 단위)
$freeJson = json_encode([
  ["img"=>"http://oilpick.co.kr/MiniERP/oil/image/그린티(43A).jpg","alt"=>"그린티","title"=>"그린티","type"=>"Green&Herb","tags"=>["프레시","자연","그린"],"reg_date"=>"2025-10-10","eta"=>"2025-02-10","is_new"=>true,"price"=>14700],
  ["img"=>"http://oilpick.co.kr/MiniERP/oil/image/라벤더(24CA).jpg","alt"=>"라벤더","title"=>"라벤더","type"=>"Floral","tags"=>["릴렉스","허브","플로럴"],"reg_date"=>"2025-10-11","eta"=>"2025-04-11","is_new"=>false,"price"=>16200],
  ["img"=>"http://oilpick.co.kr/MiniERP/oil/image/시트러스버베나(7860A).png","alt"=>"시트러스버베나","title"=>"시트러스버베나","type"=>"Citrus","tags"=>["상큼","레몬","청량감"],"reg_date"=>"2025-10-12","eta"=>"2025-06-12","is_new"=>true,"price"=>15100],
  ["img"=>"http://oilpick.co.kr/MiniERP/oil/image/비누(8147B).jpg.png","alt"=>"화이트머스크","title"=>"화이트머스크 프로그램 테스트","type"=>"Cotton","tags"=>["포근함","클린","머스크"],"reg_date"=>"2025-10-15","eta"=>"2025-08-15","is_new"=>false,"price"=>13800],
  ["img"=>"http://oilpick.co.kr/MiniERP/oil/image/화이트자스민(40A).png","alt"=>"화이트자스민","title"=>"화이트자스민","type"=>"Floral","tags"=>["플로럴","고급","여성스러움"],"reg_date"=>"2025-10-09","eta"=>"2025-10-09","is_new"=>true,"price"=>15000],
  ["img"=>"http://oilpick.co.kr/MiniERP/oil/image/우드세이지 앤 씨솔트(965A).png","alt"=>"우드세이지 앤 씨솔트","title"=>"우드세이지 앤 씨솔트","type"=>"Woody","tags"=>["우디","내추럴","세이지","우디","내추럴","세이지"],"reg_date"=>"2025-10-08","eta"=>"2025-12-08","is_new"=>true,"price"=>16900],
], JSON_UNESCAPED_UNICODE);

$paidJson = json_encode([
  ["img"=>"http://oilpick.co.kr/MiniERP/oil/image/미르토(678P).jpg","alt"=>"블랙체리","title"=>"블랙체리","type"=>"Fruity","tags"=>["달콤함","체리","머스크"],"reg_date"=>"2025-10-05","is_new"=>true,"price"=>14100],
  ["img"=>"http://oilpick.co.kr/MiniERP/oil/image/피오니(1790A).jpg","alt"=>"피오니","title"=>"피오니","type"=>"Floral","tags"=>["플로럴","작약","은은함"],"reg_date"=>"2025-10-06","is_new"=>true,"price"=>13400],
  ["img"=>"http://oilpick.co.kr/MiniERP/oil/image/쿨워터(5CA).jpg","alt"=>"화이트로즈","title"=>"화이트로즈","type"=>"Floral","tags"=>["깨끗함","청초함","로즈"],"reg_date"=>"2025-10-04","is_new"=>false,"price"=>13200],
  ["img"=>"http://oilpick.co.kr/MiniERP/oil/image/상탈(8279P).jpg","alt"=>"샌달우드","title"=>"샌달우드","type"=>"Woody&Spicy","tags"=>["우디","따뜻함","잔향","우디","따뜻함","잔향"],"reg_date"=>"2025-10-03","is_new"=>true,"price"=>13800],
  ["img"=>"http://oilpick.co.kr/MiniERP/oil/image/오션릴리 29A.png","alt"=>"그레이앰버","title"=>"그레이앰버","type"=>"Etc","tags"=>["앰버","해양성","고급스러움"],"reg_date"=>"2025-10-02","is_new"=>true,"price"=>15100],
  ["img"=>"http://oilpick.co.kr/MiniERP/oil/image/베르가못(18A).jpg","alt"=>"베르가못","title"=>"베르가못","type"=>"Citrus","tags"=>["상큼","베르가못","감귤"],"reg_date"=>"2025-10-01","is_new"=>false,"price"=>13600],
  ["img"=>"http://oilpick.co.kr/MiniERP/oil/image/믹스베리(135A).png","alt"=>"믹스베리","title"=>"믹스베리","type"=>"Fruity","tags"=>["과일","상큼함","베리"],"reg_date"=>"2025-09-29","is_new"=>false,"price"=>15800],
  ["img"=>"http://oilpick.co.kr/MiniERP/oil/image/비치워크(2918P).jpg","alt"=>"버가모트머스크","title"=>"버가모트머스크","type"=>"Woody","tags"=>["머스크","산뜻함","부드러움"],"reg_date"=>"2025-09-27","is_new"=>true,"price"=>16700],
  ["img"=>"http://oilpick.co.kr/MiniERP/oil/image/헤이즐넛(73A).jpg","alt"=>"페퍼민트","title"=>"페퍼민트","type"=>"Etc","tags"=>["청량함","집중력","시원함"],"reg_date"=>"2025-09-25","is_new"=>true,"price"=>17900],
], JSON_UNESCAPED_UNICODE);

// --- 디코드 ---
$freeItems = json_decode($freeJson, true) ?: [];
$paidItems = json_decode($paidJson, true) ?: [];
?>
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
  <section class="card" id="tab-Scents">
    <div class="card-hd">
      <div>
        <div class="card-ttl">향선택</div>
        <div class="card-sub">무료 제공 6개 상단 타일 · 추가는 유료</div>
      </div>
    </div>
    <div class="card-bd">
		<!-- Free Scent Grid -->
		<div class="cl-grid" id="freeScentGrid">
		<?php $seq = 1; foreach($freeItems as $it): ?>
		  <div class="cl-card" style="position:relative;">
			<div style="position:absolute;top:6px;left:6px;background:rgba(255,255,255,0.7);border-radius:4px;padding:2px 6px;font-weight:700;"><?= $seq++; ?></div>

			<div class="cl-thumb">
			  <img src="<?= h($it['img']); ?>" alt="<?= h($it['alt']); ?>"
				   style="width:100%;height:100px;object-fit:cover;border-radius:8px;">
			</div>

			<div class="title-line<?= empty($it['is_new']) ? ' no-badge' : '' ?>" style="margin-top:6px;font-weight:700">
			  <span class="name"><?= h($it['title']); ?></span>
			  <?php if(!empty($it['is_new'])): ?><span class="badge new">New</span><?php endif; ?>
			</div>

			<div class="small">타입: <?= h($it['type']); ?></div>
			<?php
			  $tags = isset($it['tags']) && is_array($it['tags']) ? array_values(array_unique($it['tags'])) : [];
			?>
			<div class="small tag-line">태그: <?= h(implode(', ', $tags)); ?></div>

			<div class="small">등록일: <?= h($it['reg_date']); ?></div>
      <div class="small">금액: 무료</div>
			<?php if(!empty($it['eta'])): ?>
			  <div class="small" style="color:var(--accent);">예상 배송일: <?= h($it['eta']); ?></div>
			<?php endif; ?>

			<button class="btn small change-btn" style="margin-top:4px;width:100%;">변경</button>
		  </div>
		<?php endforeach; ?>
		</div>

		<hr class="sep">
		<div class="card-hd">
		  <div>
			<div class="card-ttl">향라이브러리</div>
			<div class="card-sub">무료 제공 6개 상단 타일 · 추가는 유료</div>
		  </div>
			<div class="inner filter-bar">
			  <span class="label small">향타입</span>
			  <select id="libTypeFilter" class="select sm">
				<option value="ALL">전체</option>
				<option value="Green&Herb">Green&Herb</option>
				<option value="Floral">Floral</option>
				<option value="Woody&Spicy">Woody&Spicy</option>
				<option value="Fruity">Fruity</option>
				<option value="Citrus">Citrus</option>
				<option value="Cotton">Cotton</option>
				<option value="Etc">Etc</option>
				<option value="Woody">Woody</option>
			  </select>

			  <span class="label small">검색어</span>
			  <input id="libTagInput" class="input" placeholder="태그검색">

			  <button id="libSearchBtn" class="btn cta sm">검색</button>
			</div>
		</div>

		<!-- Paid Scent Grid -->
		<div class="cl-grid" id="paidScentGrid">
		<?php foreach($paidItems as $it): ?>
		  <div class="cl-card">
			<div class="cl-thumb">
			  <img src="<?= h($it['img']); ?>" alt="<?= h($it['alt']); ?>"
				   style="width:100%;height:100px;object-fit:cover;border-radius:8px;">
			</div>

			<div class="title-line<?= empty($it['is_new']) ? ' no-badge' : '' ?>" style="margin-top:6px;font-weight:700">
			  <span class="name"><?= h($it['title']); ?></span>
			  <?php if(!empty($it['is_new'])): ?><span class="badge new">New</span><?php endif; ?>
			</div>

			<div class="small">타입: <?= h($it['type']); ?></div>
			<?php
			  $tags = isset($it['tags']) && is_array($it['tags']) ? array_values(array_unique($it['tags'])) : [];
			?>
			<div class="small tag-line">태그: <?= h(implode(', ', $tags)); ?></div>

			<div class="small">등록일: <?= h($it['reg_date']); ?></div>
			<div class="small">금액: <?= number_format($it['price']); ?>원</div>

			<button class="btn small" style="margin-top:4px;width:100%;">장바구니 추가</button>
		  </div>
		<?php endforeach; ?>
		</div>

      <div class="small" style="margin-top:6px">※ 최초 6개 외 추가 향은 본사 책정가로 구매.</div>
    </div>
  </section>
</div>

<!-- 팝업 -->
<div id="scentModal" class="modal hidden" role="dialog" aria-modal="true" aria-labelledby="scentModalTitle">
  <div class="modal-dim"></div>
  <div class="modal-panel">
    <div class="modal-hd">
      <div class="ttl" id="scentModalTitle">향 선택</div>
      <div class="act">
        <button type="button" class="btn sm btn-outline" id="modalCloseBtn">닫기</button>
        <button type="button" class="btn sm btn-primary" id="modalApplyBtn">적용</button>
      </div>
    </div>
    <div class="modal-bd">
      <div class="modal-left">
        <div class="section-ttl">현재 슬롯</div>
        <div id="pickedPreview" class="cl-card preview"></div>
        <div class="eta-editor">
          <label class="label small" for="etaInput">예상 배송일</label>
          <div class="eta-row">
            <input type="date" id="etaInput" class="input sm" />
            <button type="button" id="etaApplyBtn" class="btn sm btn-primary">적용</button>
          </div>
          <div class="muted small">* 검색 결과 리스트에는 예상 배송일을 표시하지 않습니다.</div>
        </div>
      </div>
      <div class="modal-right">
        <div class="searchbar">
          <label class="label small" for="modalTypeFilter">향타입</label>
          <select id="modalTypeFilter" class="select sm">
            <option value="ALL">전체</option>
            <option value="Green&Herb">Green&Herb</option>
            <option value="Floral">Floral</option>
            <option value="Woody&Spicy">Woody&Spicy</option>
            <option value="Fruity">Fruity</option>
            <option value="Citrus">Citrus</option>
            <option value="Cotton">Cotton</option>
            <option value="Etc">Etc</option>
            <option value="Woody">Woody</option>
          </select>

          <label class="label small" for="modalTagInput">검색어</label>
          <input id="modalTagInput" class="input" placeholder="태그검색">

          <button id="modalSearchBtn" class="btn cta sm">검색</button>
        </div>

        <div id="modalResults" class="cl-grid results-grid"></div>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  'use strict';

  // 한 번만 세팅되는 전역(관찰자/폴링만 가드). 실마운트는 host.dataset.inited로 제어.
  if (window.__scentBootObserver__) return;
  window.__scentBootObserver__ = true;

  // ===== 유틸 =====
  const $ = (sel, root=document)=> root.querySelector(sel);
  const $$ = (sel, root=document)=> Array.from(root.querySelectorAll(sel));
  const escapeHtml = (s)=> String(s||'').replace(/[&<>"']/g, (m)=>{
    switch(m){
      case '&': return '&amp;';
      case '<': return '&lt;';
      case '>': return '&gt;';
      case '"': return '&quot;';
      default: return '&#39;';
    }
  });
  const ymd = (d)=> `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;

  function showToast(msg){
    let wrap = document.querySelector('.toast-wrap');
    if(!wrap){
      wrap = document.createElement('div');
      wrap.className = 'toast-wrap';
      wrap.style.cssText = 'position:fixed;left:50%;bottom:24px;transform:translateX(-50%);z-index:9999;display:flex;flex-direction:column;gap:8px;';
      document.body.appendChild(wrap);
    }
    const t = document.createElement('div');
    t.className = 'toast';
    t.textContent = msg;
    t.style.cssText = 'background:rgba(0,0,0,.8);color:#fff;padding:10px 14px;border-radius:10px;transition:all .35s;';
    wrap.appendChild(t);
    setTimeout(()=>{ t.style.opacity='0'; t.style.transform='translateY(6px)'; }, 1800);
    setTimeout(()=>{ t.remove(); }, 2300);
  }

  // 페이지 전용 네임스페이스
  window.SCENT_LIB ||= {};

  // PHP 주입 데이터
  const FREE_DATA = <?php echo json_encode($freeItems, JSON_UNESCAPED_UNICODE); ?>;
  const PAID_DATA = <?php echo json_encode($paidItems, JSON_UNESCAPED_UNICODE); ?>;
  const ALL_DATA  = [...FREE_DATA, ...PAID_DATA];

  // === 과거 ETA 마킹 ===
  function markPastCards(root=document){
    const grid = $('#freeScentGrid', root);
    if(!grid) return;
    const now = new Date();
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate()).getTime();
    $$('.cl-card', grid).forEach(card=>{
      const etaNode = $$('.small', card).find(el => /예상\s*배송일\s*:\s*\d{4}-\d{2}-\d{2}/.test(el.textContent));
      if(!etaNode) return;
      const m = etaNode.textContent.match(/(\d{4}-\d{2}-\d{2})/);
      if(!m) return;
      const eta = new Date(m[1] + 'T00:00:00').getTime();
      card.classList.toggle('past', eta < today);
    });
  }
  window.SCENT_LIB.markPastCards = ()=> markPastCards(document);

  // === 카드 렌더/치환 ===
  function extractCardData(card){
    const img = card.querySelector('.cl-thumb img');
    const titleEl = card.querySelector('.title-line .name')
                  || card.querySelector('.title-line')
                  || card.querySelector('[style*="font-weight:700"]');
    const rawTitle = titleEl ? titleEl.textContent.trim() : '';
    const title = rawTitle.replace(/\s*New\s*$/i,'').trim();
    const isNew = !!card.querySelector('.badge.new');
    const typeLine = $$('.small', card).find(el=>/^타입\s*:/.test(el.textContent))?.textContent || '';
    const tagLine  = $$('.small', card).find(el=>/^태그\s*:/.test(el.textContent))?.textContent || '';
    const regLine  = $$('.small', card).find(el=>/^등록일\s*:?\s*\d{4}-\d{2}-\d{2}$/.test(el.textContent.trim()))?.textContent || '';
    const etaLine  = $$('.small', card).find(el=>/예상\s*배송일\s*:/.test(el.textContent))?.textContent || '';
    const priceEl  = $$('.small', card).find(el=>/^금액\s*:/.test(el.textContent))?.textContent || '';
    let price = null;
    if (priceEl){
      const n = priceEl.replace(/[^0-9]/g,'');
      price = n ? Number(n) : null;
    }
    return {
      img: img?.getAttribute('src') || '',
      alt: img?.getAttribute('alt') || title || '',
      title,
      is_new: isNew,
      type: (typeLine.replace(/^타입\s*:\s*/,'').trim() || ''),
      tags: tagLine.replace(/^태그\s*:\s*/,''), // 문자열
      reg_date: (regLine.match(/(\d{4}-\d{2}-\d{2})/)||[])[1] || '',
      eta: (etaLine.match(/(\d{4}-\d{2}-\d{2})/)||[])[1] || '',
      price
    };
  }

  function renderClCard(container, data, options={}){
    const { showPrice=false, actionText='', onAction=null, etaMode='show' } = options;
    const etaHtml = (etaMode==='show' && data.eta)
      ? `<div class="small" style="color:var(--accent);">예상 배송일: ${escapeHtml(data.eta)}</div>`
      : '';

    container.innerHTML = `
      <div class="cl-thumb">
        <img src="${escapeHtml(data.img||'')}" alt="${escapeHtml(data.alt||data.title||'')}"
             style="width:100%;height:100px;object-fit:cover;border-radius:8px;">
      </div>
      <div class="title-line${data.is_new ? '' : ' no-badge'}" style="margin-top:6px;font-weight:700">
        <span class="name">${escapeHtml(data.title||'')}</span>
        ${data.is_new ? '<span class="badge new">New</span>' : ''}
      </div>
      ${data.type ? `<div class="small">타입: ${escapeHtml(data.type)}</div>` : ''}
      ${data.tags ? `<div class="small tag-line">태그: ${escapeHtml(String(data.tags))}</div>` : ''}
      ${data.reg_date ? `<div class="small">등록일: ${escapeHtml(data.reg_date)}</div>` : ''}
      ${etaHtml}
      ${showPrice && data.price ? `<div class="small">금액: ${Number(data.price).toLocaleString()}원</div>` : ''}
      ${actionText ? `<button type="button" class="btn small" style="margin-top:4px;width:100%;">${escapeHtml(actionText)}</button>` : ''}
    `.trim();

    if (actionText && typeof onAction === 'function') {
      container.querySelector('button.btn.small')?.addEventListener('click', onAction);
    }
  }

  function replaceCardContents(card, data){
    let img = card.querySelector('.cl-thumb img');
    if(!img){
      const thumb = document.createElement('div');
      thumb.className = 'cl-thumb';
      thumb.innerHTML = `<img style="width:100%;height:100px;object-fit:cover;border-radius:8px;">`;
      card.insertBefore(thumb, card.firstChild?.nextSibling || null);
      img = thumb.querySelector('img');
    }
    img.src = data.img || '';
    img.alt = data.alt || data.title || '';

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

    let typeNode = $$('.small', card).find(el=>/^타입\s*:/.test(el.textContent));
    if(data.type){
      if(!typeNode){
        typeNode = document.createElement('div');
        typeNode.className = 'small';
        ttl.after(typeNode);
      }
      typeNode.textContent = `타입: ${data.type}`;
    } else if (typeNode){ typeNode.remove(); }

    let tag = $$('.small', card).find(el=>/^태그\s*:/.test(el.textContent));
    if (data.tags) {
      if(!tag){
        tag = document.createElement('div');
        tag.className = 'small';
        (typeNode || ttl).after(tag);
      }
      tag.textContent = `태그: ${data.tags}`;
    } else if (tag) { tag.remove(); }

    let reg = $$('.small', card).find(el=>/^등록일\s*:?\s*\d{4}-\d{2}-\d{2}$/.test(el.textContent.trim()));
    if (data.reg_date){
      if(!reg){
        reg = document.createElement('div');
        reg.className = 'small';
        (tag || typeNode || ttl).after(reg);
      }
      reg.textContent = `등록일: ${data.reg_date}`;
    } else if (reg){ reg.remove(); }

    let etaNode = $$('.small', card).find(el=>/예상\s*배송일\s*:/.test(el.textContent));
    if (data.eta){
      if(!etaNode){
        etaNode = document.createElement('div');
        etaNode.className = 'small';
        etaNode.style.color = 'var(--accent)';
        (reg || tag || typeNode || ttl).after(etaNode);
      }
      etaNode.textContent = `예상 배송일: ${data.eta}`;
    } else if (etaNode){ etaNode.remove(); }

    // 가격 표시(무료/유료에 따라)
    let priceNode = $$('.small', card).find(el=>/^금액\s*:/.test(el.textContent));
    if (data.price != null){
      if(!priceNode){
        priceNode = document.createElement('div');
        priceNode.className = 'small';
        (etaNode || reg || tag || typeNode || ttl).after(priceNode);
      }
      priceNode.textContent = `금액: ${Number(data.price).toLocaleString()}원`;
    } else if (priceNode){ priceNode.remove(); }

    markPastCards(document);
  }

  // === 모달 ===
  const modalEl = $('#scentModal');
  const modalCloseBtn = $('#modalCloseBtn');
  const modalApplyBtn = $('#modalApplyBtn');
  const pickedPreview = $('#pickedPreview');
  const modalTypeFilter = $('#modalTypeFilter');
  const modalTagInput = $('#modalTagInput');
  const modalSearchBtn = $('#modalSearchBtn');
  const resultsGrid = $('#modalResults');
  const etaInput = $('#etaInput');
  const etaApplyBtn = $('#etaApplyBtn');

  // ★ 장바구니 추가 버튼(추가 모드 전용)
  let contentAddToCartBtn = document.createElement('button');
  contentAddToCartBtn.type = 'button';
  contentAddToCartBtn.id = 'contentAddToCartBtn';
  contentAddToCartBtn.className = 'btn cta';
  contentAddToCartBtn.dataset.action = 'cart';
  contentAddToCartBtn.textContent = '장바구니 추가';

  let targetCardEl = null;
  let pickedData = null;

  function setupEtaEditor(defaultYmd){
    const todayStr = ymd(new Date());
    etaInput.min = todayStr;
    etaInput.value = defaultYmd || '';
  }

  function matchKeyword(item, kw){
    if(!kw) return true;
    kw = kw.trim();
    if(!kw) return true;
    const hay = [
      item.title || '',
      (Array.isArray(item.tags) ? item.tags.join(',') : (item.tags || '')),
      item.alt || ''
    ].join(' ').toLowerCase();
    return hay.includes(kw.toLowerCase());
  }
  function filterType(item, typeVal){
    if(!typeVal || typeVal==='ALL') return true;
    return (item.type || '').toLowerCase() === typeVal.toLowerCase();
  }
  function normalizeItem(item){
    return {
      img: item.img || item.imgSrc || '',
      alt: item.alt || item.imgAlt || item.title || '',
      title: item.title || '',
      is_new: !!(item.is_new ?? item.isNew),
      type: item.type || '',
      tags: Array.isArray(item.tags) ? item.tags.join(', ') : (item.tags || ''),
      reg_date: item.reg_date || item.registeredAt || '',
      eta: item.eta || '',
      price: item.price ?? null
    };
  }
  function renderResults(list){
    resultsGrid.innerHTML = '';
    list.forEach(raw=>{
      const data = normalizeItem(raw);
      const card = document.createElement('div');
      card.className = 'cl-card';
      renderClCard(card, data, {
        showPrice: !!data.price, // 결과 카드 자체는 가격이 있으면 노출
        etaMode: 'hide',
        actionText: '선택',
        onAction: ()=>{
          pickedData = {...data, eta: etaInput.value || data.eta || ''};
          // ★ 미리보기에서는 금액 반드시 표시
          renderClCard(pickedPreview, pickedData, { showPrice:true, etaMode:'hide' });
        }
      });
      resultsGrid.appendChild(card);
    });
  }
  function searchModal(){
    const t = (modalTypeFilter?.value || 'ALL');
    const kw = (modalTagInput?.value || '');
    const filtered = ALL_DATA.filter(it => filterType(it, t) && matchKeyword(it, kw));
    renderResults(filtered.slice(0, 50));
  }

  // ★ 모드별 버튼 토글(추가 모드면 ETA 버튼 → 장바구니 추가)
  function toggleCartMode(isAddMode){
    const etaRow = etaInput?.closest('.eta-row');
    if(!etaRow) return;
    if(isAddMode){
      etaApplyBtn?.remove();
      if(!contentAddToCartBtn.isConnected) etaRow.appendChild(contentAddToCartBtn);
    } else {
      contentAddToCartBtn?.remove();
      if(!etaRow.querySelector('#etaApplyBtn')) etaRow.appendChild(etaApplyBtn);
    }
  }

  function openModal(fromCard, mode='change'){
    targetCardEl = fromCard;
    if (mode === 'add') {
      pickedData = null;
      pickedPreview.innerHTML = '';
      setupEtaEditor('');
    } else {
      pickedData = extractCardData(fromCard);
      renderClCard(pickedPreview, pickedData, { showPrice:true, etaMode:'hide' });
      setupEtaEditor(pickedData.eta || '');
    }
    renderResults(ALL_DATA.slice(0, 12));
    toggleCartMode(mode === 'add');
    modalEl?.classList.remove('hidden');
  }
  function closeModal(){
    modalEl?.classList.add('hidden');
    targetCardEl = null;
    pickedData = null;
    resultsGrid && (resultsGrid.innerHTML = '');
  }
  function applySelection(){
    if(!targetCardEl) return;
    if(!pickedData) pickedData = extractCardData(targetCardEl);
    pickedData.eta = etaInput?.value || pickedData.eta || '';
    replaceCardContents(targetCardEl, pickedData);
    closeModal();
  }

  // === 포인터 이벤트 DnD(원본 유지) ===
  function mountDnD_PE(root){
    const paid = $('#paidScentGrid', root);
    const free = $('#freeScentGrid', root);
    if(!paid || !free) return;

    function pickDropTarget(x, y){
      const el = document.elementFromPoint(x, y);
      if(!el) return null;
      const card = el.closest?.('.cl-card');
      if (!card) return null;
      if (!free.contains(card)) return null;
      if (card.classList.contains('add-card')) return null;
      return card;
    }

    function createProxy(fromCard){
      const r = fromCard.getBoundingClientRect();
      const proxy = document.createElement('div');
      proxy.className = 'drag-proxy';
      proxy.style.width = r.width + 'px';
      const img = fromCard.querySelector('.cl-thumb img');
      const ttl = fromCard.querySelector('.title-line .name')?.textContent?.trim()
                || fromCard.querySelector('.title-line')?.textContent?.trim()
                || '';
      proxy.innerHTML = `
        <div class="cl-thumb">
          <img src="${img?.src || ''}" alt="${img?.alt || ''}" style="width:100%;height:100px;object-fit:cover;border-radius:8px;">
        </div>
        <div class="title-line" style="margin-top:6px;font-weight:700">${escapeHtml(ttl)}</div>
      `;
      document.body.appendChild(proxy);
      return proxy;
    }

    let dragging = null;
    const DRAG_THRESHOLD = 6;

    function onPointerDown(e){
      if (!modalEl || !modalEl.classList.contains('hidden')) return;
      const card = e.target.closest('.cl-card');
      if(!card || !paid.contains(card) || card.classList.contains('add-card')) return;
      dragging = {
        srcCard: card,
        data: extractCardData(card),
        startX: e.clientX,
        startY: e.clientY,
        proxy: null,
        active: false,
        target: null
      };
      paid.classList.add('drag-lock');
      try { card.setPointerCapture(e.pointerId); } catch(_){ }
    }

    function onPointerMove(e){
      if(!dragging) return;
      const dx = e.clientX - dragging.startX;
      const dy = e.clientY - dragging.startY;
      if(!dragging.active && Math.hypot(dx, dy) > DRAG_THRESHOLD){
        dragging.active = true;
        dragging.proxy = createProxy(dragging.srcCard);
      }
      if(!dragging.active) return;
      e.preventDefault();
      const px = e.clientX + 8;
      const py = e.clientY + 8;
      dragging.proxy.style.transform = `translate(${px}px, ${py}px)`;
      const cand = pickDropTarget(e.clientX, e.clientY);
      if (dragging.target !== cand){
        dragging.target?.classList.remove('drop-on');
        dragging.target = cand;
        dragging.target?.classList.add('drop-on');
      }
    }

    function onPointerUp(e){
      if(!dragging) return;
      if(dragging.active && dragging.target){
        replaceCardContents(dragging.target, dragging.data);
        markPastCards(root);
      }
      dragging.target?.classList.remove('drop-on');
      dragging.proxy?.remove();
      try { dragging.srcCard.releasePointerCapture(e.pointerId); } catch(_){ }
      paid.classList.remove('drag-lock');
      dragging = null;
    }

    paid.addEventListener('pointerdown', onPointerDown, { passive: true });
    window.addEventListener('pointermove', onPointerMove, { passive: false });
    window.addEventListener('pointerup', onPointerUp, { passive: true });
    window.addEventListener('pointercancel', onPointerUp, { passive: true });
  }

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
    const day = dt.getDay();       // 0:일, 6:토
    if (day !== 0 && day !== 6) added++;
  }
  return dt;
}

  // === idempotent 마운트(이 파일 핵심) ===
  function mountOnce(root=document){
    const host = $('#tab-Scents', root);
    if(!host || host.dataset.inited === '1') return;

    // 1) add-icon
    const grid = $('#freeScentGrid', host);
    if(grid && !grid.querySelector('#addScentBtn')){
      const btn = document.createElement('button');
      btn.type = 'button'; btn.id = 'addScentBtn'; btn.className = 'cl-card add-card';
      btn.setAttribute('aria-label', '향 추가');
      btn.innerHTML = `<div class="add-icon">+</div>`;
      grid.appendChild(btn);
    }

    // 2) 변경 버튼 → 모달 (change 모드)
    $$('.change-btn', host).forEach(btn=>{
      if (!btn.__bound__) {
        btn.addEventListener('click', (e)=>{
          const card = e.currentTarget.closest('.cl-card');
          if(!card) return;
          openModal(card, 'change');
        });
        btn.__bound__ = true;
      }
    });

    // ★ add-icon → 모달 (add 모드)
    const addBtn = $('#addScentBtn', host);
    if (addBtn && !addBtn.__bound__) {
      addBtn.addEventListener('click', ()=> openModal(addBtn, 'add'));
      addBtn.__bound__ = true;
    }

    // 3) 모달 제어 바인딩(중복 방지)
    if(modalApplyBtn && !modalApplyBtn.__bound__){
      modalApplyBtn.addEventListener('click', applySelection);
      modalApplyBtn.__bound__ = true;
    }
    if(etaApplyBtn && !etaApplyBtn.__bound__){
      etaApplyBtn.addEventListener('click', applySelection);
      etaApplyBtn.__bound__ = true;
    }
    if(modalCloseBtn && !modalCloseBtn.__bound__){
      modalCloseBtn.addEventListener('click', closeModal);
      modalEl?.querySelector('.modal-dim')?.addEventListener('click', closeModal);
      modalCloseBtn.__bound__ = true;
    }
    if(!modalSearchBtn.__bound__){
      modalSearchBtn?.addEventListener('click', searchModal);
      modalTagInput?.addEventListener('keydown', (e)=>{ if(e.key==='Enter') searchModal(); });
      modalSearchBtn.__bound__ = true;
    }
    if(etaInput && !etaInput.__bound__){
      etaInput.addEventListener('change', ()=>{
        if(!pickedData) return;
        pickedData.eta = etaInput.value || '';
        // ★ 미리보기 가격 표시 유지
        renderClCard(pickedPreview, pickedData, { showPrice:true, etaMode:'hide' });
      });
      etaInput.addEventListener('keydown', (e)=>{
        if(e.key === 'Enter'){ e.preventDefault(); applySelection(); }
      });
      etaInput.__bound__ = true;
    }

	// ★ 장바구니 추가 버튼 핸들러(추가 모드)
	if (!contentAddToCartBtn.__bound__) {
	  contentAddToCartBtn.addEventListener('click', ()=>{
		if (!pickedData) {
		  alert('먼저 항목을 선택해 주세요.');
		  return;
		}

		const etaStr  = (etaInput && etaInput.value ? etaInput.value.trim() : '');
		const etaDate = parseYmd(etaStr);
		const today   = startOfDay(new Date());

		// 1) ETA 미입력
		if (!etaStr || !etaDate) {
		  alert('배송일을 지정해 주세요.');
		  return;
		}
		// 2) 오늘 이전
		if (etaDate < today) {
		  alert('과거 날짜는 지정할 수 없습니다. 배송일을 다시 선택해 주세요.');
		  return;
		}
		// 3) 영업일 +3일 이내 (토/일 제외)
		const threeBizDays = addBusinessDays(today, 3);
		if (etaDate <= threeBizDays) {
		  alert('배송 준비에 최소 영업일 기준 3일이 필요합니다. 더 여유 있는 날짜로 지정해 주세요.');
		  return;
		}

		// 통과 → 추가 진행
		pickedData.eta = etaStr;
		// TODO: 필요 시 AJAX로 장바구니 반영 (pickedData 사용)
		alert('장바구니에 추가했습니다.');
		closeModal();
	  });
	  contentAddToCartBtn.__bound__ = true;
	}


    // 4) DnD
    mountDnD_PE(host);

    // 5) 과거 표시
    markPastCards(host);

    host.dataset.inited = '1';
  }

  // === 최초/재삽입 마운트
  function ensureMount(){
    mountOnce(document);
    // 폴백 타이머(비동기 삽입 대응)
    let tries = 0;
    const tick = setInterval(()=>{
      mountOnce(document);
      if($('#tab-Scents') || ++tries > 40) clearInterval(tick);
    }, 50);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', ensureMount, { once:true });
  } else {
    ensureMount();
  }

  // DOM 재삽입 감지: #tab-Scents가 새로 들어오면 다시 mount
  const mo = new MutationObserver((muts)=>{
    for(const m of muts){
      m.addedNodes && m.addedNodes.forEach(node=>{
        if(!(node instanceof HTMLElement)) return;
        if(node.id === 'tab-Scents' || node.querySelector?.('#tab-Scents')){
          // 새 호스트에는 inited가 없으니 mountOnce가 다시 수행됨
          mountOnce(document);
        }
      });
    }
  });
  mo.observe(document.body, {childList:true, subtree:true});

})();
</script>
