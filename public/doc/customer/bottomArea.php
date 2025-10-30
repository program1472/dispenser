<script>
/********** Utilities **********/
const LS={get:(k,def)=>{try{const v=localStorage.getItem(k);return v?JSON.parse(v):def}catch(e){return def}},set:(k,v)=>{try{localStorage.setItem(k,JSON.stringify(v))}catch(e){}}};
const toastWrap=document.getElementById('toasts');
function toast(m){const el=document.createElement('div');el.className='toast';el.textContent=m;toastWrap.appendChild(el);setTimeout(()=>el.remove(),1800)}
const fmt=n=>'₩'+(Number(n)||0).toLocaleString();
const today=()=>{const d=new Date();return d.toISOString().slice(0,10)};
function addMonths(dateStr,m){const [y,mo,d]=dateStr.split('-').map(Number);const dt=new Date(Date.UTC(y,mo-1+m,d));const yy=dt.getUTCFullYear();const mm=String(dt.getUTCMonth()+1).padStart(2,'0');const dd=String(dt.getUTCDate()).padStart(2,'0');return `${yy}-${mm}-${dd}`}
function daysBetween(a,b){const da=new Date(a), db=new Date(b);return Math.round((db-da)/86400000)}
function isNewWithin(dateStr, days=30){return daysBetween(dateStr,today())<=days}

/********** Global Policy **********/
const POLICY=LS.get('cust_policy',{
  subMonthly:29700,
  freePrintsPerYear:6,
  printPrice:5000,
  prices:{free:0,standard:50000,deluxe:100000,premium:200000,lucid:100000},
  scentFreeCount:6,
  autoSupplyMonths:2
});

/********** Seed Demo Data (multi-site, multi-device) **********/
(function seed(){
  if(LS.get('cust_seeded_v2',false)) return;
  const sites=[
    {id:'S1',name:'본점',address:'서울 강남구 테헤란로 100'},
    {id:'S2',name:'A지점',address:'경기 성남시 분당구 판교로 242'},
    {id:'S3',name:'B지점',address:'부산 해운대구 센텀북대로 45'}
  ];
  const installDate='2025-01-10';
  const devices=[];
  let serialSeed=250001;
  sites.forEach((s)=>{
    for(let i=0;i<3;i++){
      devices.push({siteId:s.id, place:['로비','연회장','라운지'][i%3], serial:'AP5-'+(serialSeed++), installed:installDate, scent:'그린티', content:'안전수칙_라운지', state:'정상'});
    }
  });
  const subStart='2025-01-10'; const subEnd=addMonths(subStart,12);
  const contents=[
    {id:'CT1',title:'안전수칙_라운지',type:'free',tags:'안전,라운지',created:'2025-08-20'},
    {id:'CT2',title:'행사안내_웨딩',type:'standard',tags:'웨딩,행사',created:'2025-07-20'},
    {id:'CT3',title:'시즌프로모션_골프',type:'deluxe',tags:'골프,시즌',created:'2025-09-10'},
    {id:'CT4',title:'브랜드스토리_프리미엄',type:'premium',tags:'브랜드',created:'2025-06-01'},
    {id:'CT5',title:'루시드 캐릭콘_봄',type:'lucid',tags:'루시드,캐릭',created:'2025-09-05'},
    {id:'CT6',title:'비상대피 안내',type:'free',tags:'안전,안내',created:'2025-09-15'}
  ];
  const scents=[
    {id:'SC1',name:'그린티',price:0,kind:'free',created:'2025-08-28'},
    {id:'SC2',name:'라벤더',price:0,kind:'free',created:'2025-06-20'},
    {id:'SC3',name:'시트러스',price:0,kind:'free',created:'2025-07-15'},
    {id:'SC4',name:'화이트머스크',price:0,kind:'free',created:'2025-07-30'},
    {id:'SC5',name:'자스민',price:0,kind:'free',created:'2025-08-10'},
    {id:'SC6',name:'우드세이지',price:0,kind:'free',created:'2025-05-22'},
    {id:'SC7',name:'블랙체리',price:22000,kind:'paid',created:'2025-09-12'},
    {id:'SC8',name:'피오니',price:22000,kind:'paid',created:'2025-03-02'}
  ];
  const requests=[
    {id:'RQ1001',type:'오일',detail:'A지점 400ml x 4',status:'DONE',date:'2025-08-12'},
    {id:'RQ1002',type:'콘텐츠',detail:'본점 행사안내 수정',status:'DONE',date:'2025-07-20'},
    {id:'RQ1003',type:'프린팅',detail:'B지점 안전표지 3건',status:'OPEN',date:'2025-09-25'}
  ];
  const bills=[
    {id:'BILL1001',date:'2025-09-01',siteId:'S1',item:'정기 구독료(9월)',amount:POLICY.subMonthly,status:'PAID',memo:''},
    {id:'BILL1002',date:'2025-07-20',siteId:'S1',item:'콘텐츠(Standard)',amount:POLICY.prices.standard,status:'PAID',memo:''}
  ];
  LS.set('cust_sites',sites);
  LS.set('cust_devices',devices);
  LS.set('cust_sub',{start:subStart,end:subEnd});
  LS.set('cust_freeLeft',POLICY.freePrintsPerYear);
  LS.set('cust_contents',contents);
  LS.set('cust_scents',scents);
  LS.set('cust_requests',requests);
  LS.set('cust_bills',bills);
  LS.set('cust_seeded_v2',true);
})();

/********** Tabs & Global Site Filter **********/
const tabs=[...document.querySelectorAll('header a')];
const sections={};
['Dashboard','Devices','Content','Scents','Billing','Help'].forEach(k=>sections[k]=document.getElementById('tab-'+k));
let active='Dashboard';
function showTab(key){
  active=key;
  tabs.forEach(a=>a.classList.toggle('active',a.dataset.target==='tab-'+key));
  Object.entries(sections).forEach(([k,el])=>el.classList.toggle('hidden',k!==key));
  render();
}

const siteSel=document.getElementById('siteFilter');
function buildSiteFilter(){
  const sites=LS.get('cust_sites',[]);
  const optAll=document.createElement('option'); optAll.value='ALL'; optAll.textContent='전체';
  siteSel.innerHTML=''; siteSel.appendChild(optAll);
  sites.forEach(s=>{const o=document.createElement('option');o.value=s.id;o.textContent=s.name;siteSel.appendChild(o)});
  siteSel.value=LS.get('site_filter','ALL');
}

/********** Renderers **********/
function render(){
  drawKpis(); drawDashDetails(); drawDashReq(); drawAlerts();
  drawDevices(); drawContent(); drawScents(); drawBilling(); drawSubSummary();
}

function visibleDevices(){const list=LS.get('cust_devices',[]); const f=LS.get('site_filter','ALL'); return f==='ALL'?list:list.filter(d=>d.siteId===f)}
function visibleSites(){const sites=LS.get('cust_sites',[]); const f=LS.get('site_filter','ALL'); return f==='ALL'?sites:sites.filter(s=>s.id===f)}

function kpi(label,value,sub=''){const div=document.createElement('div');div.className='kpi';div.innerHTML=`<div class="small">${label}</div><div class="v">${value}</div><div class="small">${sub}</div>`;return div}

function drawKpis(){
  const box=document.getElementById('dashKpis'); if(!box) return; box.innerHTML='';
  const dev=visibleDevices();
  const units=dev.length; const serials=dev.map(d=>d.serial).join(', ');
  const freeLeft=LS.get('cust_freeLeft',POLICY.freePrintsPerYear);
  const freePct=Math.round((freeLeft/POLICY.freePrintsPerYear)*100);
  const nextShip=nextAutoShipDate();
  box.appendChild(kpi('설치 대수', units, serials||'-'));
  box.appendChild(kpi('무료 프린팅 잔여', freeLeft, freePct+'%'));
  box.appendChild(kpi('다음 오일 배송', nextShip||'-', '설치일 + '+POLICY.autoSupplyMonths+'개월'));
  const sites=visibleSites();
  box.appendChild(kpi('선택 사업장', sites.map(s=>s.name).join(', ')||'전체'));
}

function nextAutoShipDate(){
  const dev=visibleDevices(); if(dev.length===0) return '-';
  const lastInstall=dev.reduce((latest,d)=> latest>d.installed?latest:d.installed, '0000-00-00');
  const base=addMonths(lastInstall, POLICY.autoSupplyMonths);
  return base;
}

function drawDashDetails(){
  const tb=document.getElementById('dashDeviceTbl'); if(!tb) return; const tbody=tb.querySelector('tbody'); tbody.innerHTML='';
  const sites=LS.get('cust_sites',[]); const siteMap=Object.fromEntries(sites.map(s=>[s.id,s.name]));
  visibleDevices().forEach(d=>{
    const tr=document.createElement('tr');
    tr.innerHTML=`<td>${siteMap[d.siteId]||''}</td><td>${d.place}</td><td>${d.serial}</td><td>${d.installed}</td><td>${d.scent||'-'}</td><td>${d.content||'-'}</td>`;
    tbody.appendChild(tr);
  });
}

function drawDashReq(){
  const tb=document.getElementById('dashReqTbl'); if(!tb) return; const tbody=tb.querySelector('tbody'); tbody.innerHTML='';
  const rq=LS.get('cust_requests',[]).slice().sort((a,b)=>a.date<b.date?1:-1);
  rq.forEach(r=>{const tr=document.createElement('tr'); tr.innerHTML=`<td>${r.id}</td><td>${r.type}</td><td>${r.detail}</td><td>${r.status}</td><td>${r.date}</td>`; tbody.appendChild(tr)});
}

function drawAlerts(){
  const box=document.getElementById('alertBox'); if(!box) return; const freeLeft=LS.get('cust_freeLeft',POLICY.freePrintsPerYear);
  const next=nextAutoShipDate();
  box.innerHTML=`<div>무료 프린팅 잔여: <b>${freeLeft}</b> / ${POLICY.freePrintsPerYear}</div>
  <div>다음 오일 자동 배송 예정일: <b>${next}</b></div>`;
}

function drawDevices(){
  const tb=document.getElementById('deviceTbl'); if(!tb) return; const tbody=tb.querySelector('tbody'); tbody.innerHTML='';
  const sites=LS.get('cust_sites',[]); const siteMap=Object.fromEntries(sites.map(s=>[s.id,s.name]));
  visibleDevices().forEach((d)=>{
    const tr=document.createElement('tr');
    tr.innerHTML=`<td>${siteMap[d.siteId]||''}</td>
    <td>${d.place}</td>
    <td>${d.serial}</td>
    <td>${d.installed}</td>
    <td><select class="select scentSel" data-idx="${d.serial}"></select></td>
    <td><select class="select contentSel" data-idx="${d.serial}"></select></td>
    <td><button class="btn" data-apply="${d.serial}">적용</button></td>`;
    tbody.appendChild(tr);
  });
  // 옵션 채우기
  const scents=LS.get('cust_scents',[]);
  const contents=LS.get('cust_contents',[]);
  tb.querySelectorAll('select.scentSel').forEach(sel=>{
    sel.innerHTML=scents.map(s=>`<option value="${s.name}">${s.name}${s.kind==='paid'?`(유료 ${fmt(s.price)})`:''}</option>`).join('');
    const dev=LS.get('cust_devices',[]).find(x=>x.serial===sel.dataset.idx); if(dev) sel.value=dev.scent||scents[0]?.name;
  });
  tb.querySelectorAll('select.contentSel').forEach(sel=>{
    sel.innerHTML=contents.map(c=>`<option value="${c.title}">${c.title}${c.type!=='free'?`(${c.type})`:''}</option>`).join('');
    const dev=LS.get('cust_devices',[]).find(x=>x.serial===sel.dataset.idx); if(dev) sel.value=dev.content||contents[0]?.title;
  });
}

function drawContent(){
  const all=LS.get('cust_contents',[]);
  const type=(document.getElementById('clType')||{value:'ALL'}).value; const q=(document.getElementById('clQuery')||{value:''}).value.trim();
  const list=all.filter(c=> (type==='ALL'||c.type===type) && (!q || c.title.includes(q)|| (c.tags||'').includes(q)) );
  const free=list.filter(c=>c.type==='free'); const paid=list.filter(c=>c.type!=='free');
  const mk=(c)=>{
    const isNew=isNewWithin(c.created,30);
    return `<div class="cl-card"><div class="cl-thumb">${c.title}</div>
      <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap">
        ${isNew?'<span class="badge">신규</span>':''}
        <span class="small">${c.type.toUpperCase()}</span><span class="small">${c.tags||''}</span>
      </div>
      <div style="display:flex;gap:6px;align-items:center;justify-content:flex-end">
        <button class="btn primary" data-apply-content="${c.id}">신청</button>
      </div></div>`;
  };
  const freeGrid=document.getElementById('freeGrid'); if(freeGrid) freeGrid.innerHTML=free.map(mk).join('');
  const paidGrid=document.getElementById('paidGrid'); if(paidGrid) paidGrid.innerHTML=paid.map(mk).join('');
  // 개별 바인딩 제거: 아래 위임 핸들러에서 처리
}

function applyContent(id){
  const c=LS.get('cust_contents',[]).find(x=>x.id===id); if(!c) return;
  const devs=visibleDevices(); if(devs.length===0) return toast('해당 사업장에 기기가 없습니다');
  const serial=devs[0].serial; const list=LS.get('cust_devices',[]); const idx=list.findIndex(x=>x.serial===serial);
  list[idx].content=c.title; LS.set('cust_devices',list);
  if(c.type==='free'){
    const left=Math.max(0, LS.get('cust_freeLeft',POLICY.freePrintsPerYear)-1); LS.set('cust_freeLeft',left);
  } else {
    const price=POLICY.prices[c.type]||0; const bills=LS.get('cust_bills',[]);
    const siteId=list[idx].siteId;
    bills.push({id:'BILL'+Date.now(),date:today(),siteId,item:'콘텐츠('+c.type+')',amount:price,status:'NEW',memo:(c.type==='lucid'?'루시드 50% 배분':'')});
    LS.set('cust_bills',bills);
  }
  toast('콘텐츠가 신청되었습니다');
  render();
}

function drawScents(){
  const all=LS.get('cust_scents',[]);
  const free=all.filter(s=>s.kind==='free'); const paid=all.filter(s=>s.kind!=='free');
  const mk=(s)=>{
    const isNew=isNewWithin(s.created,30);
    return `<div class="cl-card"><div class="cl-thumb">${s.name}</div>
      <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap">
        ${isNew?'<span class="badge">신규</span>':''}
        <span class="small">${s.kind==='free'?'무상':'유료 '+fmt(s.price)}</span>
      </div>
      <div style="display:flex;gap:6px;align-items:center;justify-content:flex-end">
        <button class="btn primary" data-apply-scent="${s.id}">신청</button>
      </div></div>`;
  };
  const freeGrid=document.getElementById('freeScentGrid'); if(freeGrid) freeGrid.innerHTML=free.slice(0,POLICY.scentFreeCount).map(mk).join('');
  const paidGrid=document.getElementById('paidScentGrid'); if(paidGrid) paidGrid.innerHTML=paid.map(mk).join('');
  // 개별 바인딩 제거: 아래 위임 핸들러에서 처리
}

function applyScent(id){
  const s=LS.get('cust_scents',[]).find(x=>x.id===id); if(!s) return;
  const devs=visibleDevices(); if(devs.length===0) return toast('해당 사업장에 기기가 없습니다');
  const serial=devs[0].serial; const list=LS.get('cust_devices',[]); const idx=list.findIndex(x=>x.serial===serial);
  list[idx].scent=s.name; LS.set('cust_devices',list);
  if(s.kind!=='free'){
    const bills=LS.get('cust_bills',[]);
    bills.push({id:'BILL'+Date.now(),date:today(),siteId:list[idx].siteId,item:'향('+s.name+')',amount:s.price,status:'NEW',memo:'추가 구매'});
    LS.set('cust_bills',bills);
  }
  toast('향이 신청되었습니다');
  render();
}

function drawBilling(){
  const tb=document.getElementById('billTbl'); if(!tb) return; const tbody=tb.querySelector('tbody'); tbody.innerHTML='';
  const bills=LS.get('cust_bills',[]).slice().sort((a,b)=>a.date<b.date?1:-1);
  const sites=LS.get('cust_sites',[]); const siteMap=Object.fromEntries(sites.map(s=>[s.id,s.name]));
  const f=LS.get('site_filter','ALL');
  bills.filter(b=> f==='ALL'||b.siteId===f).forEach(b=>{
    const tr=document.createElement('tr'); tr.innerHTML=`<td>${b.id}</td><td>${b.date}</td><td>${siteMap[b.siteId]||''}</td><td>${b.item}</td><td>${fmt(b.amount)}</td><td>${b.status}</td><td>${b.memo||''}</td>`; tbody.appendChild(tr);
  });
  const total=bills.filter(b=> f==='ALL'||b.siteId===f).reduce((s,x)=>s+x.amount,0);
  const kpi=document.getElementById('billKpis'); if(kpi){kpi.innerHTML=''; kpi.appendChild(kpiBox('이달 구독료', fmt(POLICY.subMonthly))); kpi.appendChild(kpiBox('추가 사용 합계', fmt(total)))}
  function kpiBox(label,value){const d=document.createElement('div'); d.className='kpi'; d.innerHTML=`<div class="small">${label}</div><div class="v">${value}</div>`; return d}
}

function drawSubSummary(){
  const boxStart=document.getElementById('subStart'); if(!boxStart) return;
  const sub=LS.get('cust_sub',{}); boxStart.textContent=sub.start||'-';
  document.getElementById('subEnd').textContent=sub.end||'-';
  const remain=daysBetween(today(), sub.end||today());
  document.getElementById('subRemain').textContent = (remain>0?remain+'일':'만료');
}

/********** Delegated Events (.off().on()) **********/
(function bindDelegated(){
  // 탭 전환
  $(document)
    .off('click','header a[data-target]')
    .on('click','header a[data-target]',function(e){
      e.preventDefault();
      const key=this.dataset.target.replace('tab-','');
      showTab(key);
    });

  // 사업장 필터
  $(document)
    .off('change','#siteFilter')
    .on('change','#siteFilter',function(){
      LS.set('site_filter', this.value);
      render();
    });

  // 기기 설정 적용 버튼 (Devices 탭 표 내)
  $(document)
    .off('click','button[data-apply]')
    .on('click','button[data-apply]',function(){
      const serial=this.getAttribute('data-apply');
      const $row=$(this).closest('tr');
      const scent=$row.find('select.scentSel').val();
      const content=$row.find('select.contentSel').val();

      const list=LS.get('cust_devices',[]);
      const idx=list.findIndex(x=>x.serial===serial);
      if(idx>-1){
        list[idx].scent=scent;
        list[idx].content=content;
        LS.set('cust_devices',list);
      }

      // 무료권/청구 처리
      const cont=LS.get('cust_contents',[]).find(c=>c.title===content);
      if(cont && cont.type==='free'){
        const left=Math.max(0, LS.get('cust_freeLeft',POLICY.freePrintsPerYear)-1);
        LS.set('cust_freeLeft',left);
      }else if(cont){
        const bills=LS.get('cust_bills',[]);
        const dev=list[idx];
        const price=POLICY.prices[cont.type]||0;
        bills.push({id:'BILL'+Date.now(),date:today(),siteId:dev.siteId,item:'콘텐츠('+cont.type+')',amount:price,status:'NEW',memo:(cont.type==='lucid'?'루시드 50% 배분':'')});
        LS.set('cust_bills',bills);
      }
      toast('기기에 적용되었습니다');
      render();
    });

  // 장소/기기 추가
  $(document)
    .off('click','#addPlaceBtn')
    .on('click','#addPlaceBtn',function(){
      const name=$('#placeInput').val().trim();
      if(!name){ toast('설치 장소를 입력하세요'); return; }
      const f=LS.get('site_filter','ALL');
      if(f==='ALL'){ toast('사업장을 먼저 선택하세요'); return; }
      const serial='AP5-'+(Math.floor(Math.random()*900000)+100000);
      const devs=LS.get('cust_devices',[]);
      devs.push({siteId:f, place:name, serial, installed:today(), scent:'그린티', content:'안전수칙_라운지', state:'정상'});
      LS.set('cust_devices',devs);
      $('#placeInput').val('');
      toast('설치 장소/기기 추가');
      render();
    });

  // 콘텐츠 필터/검색
  $(document)
    .off('change','#clType')
    .on('change','#clType',function(){ drawContent(); });

  let _clqTimer=null;
  $(document)
    .off('input','#clQuery')
    .on('input','#clQuery',function(){
      clearTimeout(_clqTimer);
      _clqTimer=setTimeout(drawContent,150);
    });

  // 콘텐츠 신청(동적)
  $(document)
    .off('click','[data-apply-content]')
    .on('click','[data-apply-content]',function(){
      const id=this.getAttribute('data-apply-content');
      applyContent(id);
    });

  // 향 신청(동적)
  $(document)
    .off('click','[data-apply-scent]')
    .on('click','[data-apply-scent]',function(){
      const id=this.getAttribute('data-apply-scent');
      applyScent(id);
    });

  // 문의 접수
  $(document)
    .off('click','#csSubmit')
    .on('click','#csSubmit',function(){
      const name=$('#csName').val().trim();
      const phone=$('#csPhone').val().trim();
      const subject=$('#csSubject').val().trim();
      const body=$('#csBody').val().trim();
      if(!name||!phone||!subject||!body) return toast('필수 입력을 확인하세요');
      const rq=LS.get('cust_requests',[]);
      rq.push({id:'RQ'+Date.now(),type:'문의',detail:subject,status:'NEW',date:today()});
      LS.set('cust_requests',rq);
      toast('문의가 등록되었습니다');
      $('#csName,#csPhone,#csSubject,#csBody').val('');
      render();
    });

  // 로컬 데이터 리셋
  $(document)
    .off('click','#resetSeed')
    .on('click','#resetSeed',function(){
      ['cust_sites','cust_devices','cust_sub','cust_freeLeft','cust_contents','cust_scents','cust_requests','cust_bills','cust_seeded_v2'].forEach(k=>localStorage.removeItem(k));
      location.reload();
    });
})();

/********** FAQ **********/
const FAQ=[
  {q:'무료 프린팅은 몇 회 제공되나요?',a:'연 6회 제공되며 Free 콘텐츠 선택 시 자동 차감됩니다.'},
  {q:'오일은 언제 배송되나요?',a:'설치일을 기준으로 2개월마다 자동 배송됩니다.'},
  {q:'다수 사업장을 어떻게 전환하나요?',a:'상단 고정 사업장 필터에서 "전체" 또는 지점을 선택하면 모든 탭이 동기화됩니다.'}
];
function buildFAQ(){
  const box=document.getElementById('faqBox'); if(!box) return; box.innerHTML='';
  FAQ.forEach(it=>{const d=document.createElement('div'); d.style.marginBottom='10px'; d.innerHTML=`<div style="font-weight:600;color:var(--accent)">${it.q}</div><div class="small">${it.a}</div>`; box.appendChild(d)})
}

/********** Init **********/
function init(){
  buildSiteFilter(); buildFAQ(); render();
}
init();
</script>
