<script>
/********** Utilities **********/
const LS={get:(k,def)=>{try{const v=localStorage.getItem(k);return v?JSON.parse(v):def}catch(e){return def}},set:(k,v)=>{try{localStorage.setItem(k,JSON.stringify(v))}catch(e){}}};
const toastWrap=document.getElementById('toasts');
function toast(m){const el=document.createElement('div');el.className='toast';el.textContent=m;toastWrap.appendChild(el);setTimeout(()=>el.remove(),1800)}
const fmt=n=>'₩'+(Number(n)||0).toLocaleString();
const today=()=>new Date().toISOString().slice(0,10);
function addMonths(dateStr,m){const [y,mo,d]=dateStr.split('-').map(Number);const dt=new Date(Date.UTC(y,mo-1+m,d));const yy=dt.getUTCFullYear();const mm=String(dt.getUTCMonth()+1).padStart(2,'0');const dd=String(dt.getUTCDate()).padStart(2,'0');return `${yy}-${mm}-${dd}`}
function daysBetween(a,b){const da=new Date(a), db=new Date(b);return Math.round((db-da)/86400000)}
function isNewWithin(dateStr, days=30){return daysBetween(dateStr,today())<=days}
function csv(filename, rows){
  if(!rows.length){toast('데이터 없음');return}
  const cols=[...new Set(rows.flatMap(r=>Object.keys(r)))];
  const esc=v=>{if(v==null)return'';const s=String(v).replaceAll('"','""');return /[",\n]/.test(s)?`"${s}"`:s};
  const body=[cols.join(','),...rows.map(r=>cols.map(c=>esc(r[c])).join(','))].join('\n');
  const a=document.createElement('a');
  a.href=URL.createObjectURL(new Blob([body],{type:'text/csv;charset=utf-8;'}));
  a.download=filename;a.click();URL.revokeObjectURL(a.href);
}

/********** Policy **********/
const POLICY=LS.get('vendor_policy',{vendorMargin:0.40,incentive:0.05,autoSupplyMonths:2,deviceValue:70000,renewGoal:10,subMonthly:29700});

/********** Seed Demo (20+ rows) **********/
(function seed(){
  if(LS.get('vendor_seed_v5',false)) return;
  const vendorId='V001';
  const customers=[
    {id:'C001',name:'그린밸리 골프장',type:'골프장',vendor:vendorId,contractStart:'2025-01-10',term:12,sites:[{place:'라운지',serial:'AP5-250001',installed:'2025-01-10'},{place:'로비',serial:'AP5-250002',installed:'2025-01-10'}]},
    {id:'C002',name:'스마일 예식장',type:'예식장',vendor:vendorId,contractStart:'2025-02-01',term:24,sites:[{place:'연회장',serial:'AP5-250010',installed:'2025-02-01'}]},
    {id:'C003',name:'메디웰 병원',type:'병원',vendor:vendorId,contractStart:'2024-12-05',term:12,sites:[{place:'외래',serial:'AP5-249900',installed:'2024-12-05'},{place:'로비',serial:'AP5-249901',installed:'2024-12-05'}]},
    {id:'C004',name:'오션뷰 호텔',type:'호텔',vendor:vendorId,contractStart:'2025-03-15',term:12,sites:[{place:'프런트',serial:'AP5-250050',installed:'2025-03-15'}]},
    {id:'C005',name:'블루힐 골프클럽',type:'골프장',vendor:vendorId,contractStart:'2024-11-01',term:12,sites:[{place:'클럽하우스',serial:'AP5-249800',installed:'2024-11-01'},{place:'라커룸',serial:'AP5-249801',installed:'2024-11-01'},{place:'레스토랑',serial:'AP5-249802',installed:'2024-11-01'}]},
    {id:'C006',name:'라이트웨딩',type:'예식장',vendor:vendorId,contractStart:'2025-06-20',term:12,sites:[{place:'홀A',serial:'AP5-250120',installed:'2025-06-20'}]},
    {id:'C007',name:'썬샤인 병원',type:'병원',vendor:vendorId,contractStart:'2024-10-10',term:12,sites:[{place:'병동',serial:'AP5-249700',installed:'2024-10-10'}]},
    {id:'C008',name:'마운틴 리조트',type:'호텔',vendor:vendorId,contractStart:'2025-04-01',term:12,sites:[{place:'체크인',serial:'AP5-250080',installed:'2025-04-01'},{place:'라운지',serial:'AP5-250081',installed:'2025-04-01'}]},
    {id:'C009',name:'피닉스 골프',type:'골프장',vendor:vendorId,contractStart:'2025-07-05',term:12,sites:[{place:'로비',serial:'AP5-250150',installed:'2025-07-05'}]},
    {id:'C010',name:'하모니 예식홀',type:'예식장',vendor:vendorId,contractStart:'2024-12-28',term:18,sites:[{place:'홀B',serial:'AP5-249950',installed:'2024-12-28'}]}
  ];
  const WOTYPES=['출고','설치','회수','프린팅','콘텐츠','AS'];
  const wo=[]; let woSeed=1000;
  customers.forEach((c,ci)=>c.sites.forEach((s,si)=>{
    wo.push({id:'WO'+(woSeed++),type:WOTYPES[(ci+si)%WOTYPES.length],customer:c.name,serial:s.serial,due:addMonths(s.installed, (si%3)+1),state:['OPEN','IN_PROGRESS','DONE'][(ci+si)%3],history:['생성','OPEN'].concat(((ci+si)%3>0)?['IN_PROGRESS']:[]).concat(((ci+si)%3>1)?['DONE']:[])});
  }));
  while(wo.length<22){
    wo.push({id:'WO'+(woSeed++),type:WOTYPES[wo.length%WOTYPES.length],customer:'임시 고객',serial:'AP5-'+(240000+wo.length),due:'2025-10-10',state:'OPEN',history:['생성','OPEN']});
  }

  const bills=[]; let bSeed=2000; 
  customers.forEach((c,ci)=>{
    bills.push({id:'BL'+(bSeed++),customer:c.name,date:'2025-09-01',item:'정기 구독료',amount:POLICY.subMonthly,state:['NEW','INVOICED','PAID'][ci%3],memo:''});
    bills.push({id:'BL'+(bSeed++),customer:c.name,date:'2025-08-01',item:'정기 구독료',amount:POLICY.subMonthly,state:['PAID','PAID','INVOICED'][ci%3],memo:''});
  });

  const tickets=[
    {id:'T1001',customer:'그린밸리 골프장',type:'오일',detail:'400ml x 4',state:'OPEN',date:'2025-09-12'},
    {id:'T1002',customer:'스마일 예식장',type:'콘텐츠',detail:'안전수칙 수정',state:'IN_PROGRESS',date:'2025-09-10'},
    {id:'T1003',customer:'메디웰 병원',type:'AS',detail:'소음 점검',state:'OPEN',date:'2025-09-08'},
    {id:'T1004',customer:'오션뷰 호텔',type:'프린팅',detail:'A3 2건',state:'DONE',date:'2025-09-01'},
    {id:'T1005',customer:'블루힐 골프클럽',type:'오일',detail:'랜덤 6종 요청',state:'OPEN',date:'2025-09-15'},
    {id:'T1006',customer:'마운틴 리조트',type:'콘텐츠',detail:'시즌 프로모션',state:'OPEN',date:'2025-09-20'},
    {id:'T1007',customer:'피닉스 골프',type:'AS',detail:'스위치 교체',state:'IN_PROGRESS',date:'2025-09-05'},
    {id:'T1008',customer:'하모니 예식홀',type:'프린팅',detail:'비상안내',state:'DONE',date:'2025-08-28'},
    {id:'T1009',customer:'썬샤인 병원',type:'오일',detail:'화이트머스크',state:'OPEN',date:'2025-09-18'},
    {id:'T1010',customer:'라이트웨딩',type:'콘텐츠',detail:'웨딩안내',state:'OPEN',date:'2025-09-22'}
  ];

  const lots=[{lot:'LOT001',from:'AP5-250001',to:'AP5-250050'},{lot:'LOT002',from:'AP5-249800',to:'AP5-249950'}];
  const serials=[
    {serial:'AP5-250001',state:'설치',customer:'그린밸리 골프장',loc:'라운지',memo:''},
    {serial:'AP5-250002',state:'설치',customer:'그린밸리 골프장',loc:'로비',memo:''},
    {serial:'AP5-250010',state:'설치',customer:'스마일 예식장',loc:'연회장',memo:''},
    {serial:'AP5-249900',state:'설치',customer:'메디웰 병원',loc:'외래',memo:''},
    {serial:'AP5-249901',state:'설치',customer:'메디웰 병원',loc:'로비',memo:''},
    {serial:'AP5-250050',state:'설치',customer:'오션뷰 호텔',loc:'프런트',memo:''},
    {serial:'AP5-249800',state:'설치',customer:'블루힐 골프클럽',loc:'클럽하우스',memo:''},
    {serial:'AP5-249801',state:'설치',customer:'블루힐 골프클럽',loc:'라커룸',memo:''},
    {serial:'AP5-249802',state:'설치',customer:'블루힐 골프클럽',loc:'레스토랑',memo:''},
    {serial:'AP5-250120',state:'설치',customer:'라이트웨딩',loc:'홀A',memo:''}
  ];

  const newScents=[{name:'화이트머스크',kind:'free',price:0,created:'2025-09-14'},{name:'블랙체리',kind:'paid',price:22000,created:'2025-09-12'}];
  const newContents=[{name:'골프대회 공지',tier:'standard',price:50000,created:'2025-09-18'},{name:'브랜드스토리_프리미엄',tier:'premium',price:200000,created:'2025-06-01'}];

  const shop=[{sku:'AP-5 본체',price:62500,disc:'20%',vendor:50000},{sku:'오일 400ml',price:37000,disc:'40%',vendor:22000},{sku:'기본 콘텐츠(샘플)',price:5000,disc:'100%',vendor:0}];

  LS.set('v_customers',customers);
  LS.set('v_wo',wo);
  LS.set('v_bills',bills);
  LS.set('v_tickets',tickets);
  LS.set('v_lots',lots);
  LS.set('v_serials',serials);
  LS.set('v_newScents',newScents);
  LS.set('v_newContents',newContents);
  LS.set('v_shop',shop);
  LS.set('vendor_seed_v5',true);
})();

/********** Tab routing **********/
const sections={
  'dash':document.getElementById('sec-dash'),
  'cust':document.getElementById('sec-cust'),
  'wo':document.getElementById('sec-wo'),
  'billing':document.getElementById('sec-billing'),
  'settle':document.getElementById('sec-settle'),
  'tickets':document.getElementById('sec-tickets'),
  'stock':document.getElementById('sec-stock'),
  'alerts':document.getElementById('sec-alerts'),
  'catalog':document.getElementById('sec-catalog'),
  'shop':document.getElementById('sec-shop')
};

function showTabKey(k){
  const tabEl=document.getElementById('tabs');
  if(tabEl){
    [...tabEl.querySelectorAll('a')].forEach(x=>x.classList.toggle('active',x.dataset.t===k));
  }
  Object.values(sections).forEach(s=>s?.classList.add('hidden'));
  sections[k]?.classList.remove('hidden');
  render();
}

/********** Renderers **********/
function renderDash(){
  const customers=LS.get('v_customers',[]), wo=LS.get('v_wo',[]), bills=LS.get('v_bills',[]), tickets=LS.get('v_tickets',[]);
  const activeSubs=bills.filter(b=>b.state!=='CANCEL');
  const kpisEl=document.getElementById('kpis'); kpisEl.innerHTML='';
  const kpiData=[
    {k:'활성 구독',v:activeSubs.length},
    {k:'설치 대수',v:customers.reduce((s,c)=>s+c.sites.length,0)},
    {k:'이번달 PAID',v:fmt(bills.filter(b=>b.state==='PAID' && b.date.slice(0,7)===today().slice(0,7)).reduce((s,b)=>s+b.amount,0))},
    {k:'OPEN 티켓',v:tickets.filter(t=>t.state==='OPEN').length}
  ];
  kpiData.forEach(i=>{const d=document.createElement('div');d.className='kpi';d.innerHTML=`<div class="small">${i.k}</div><div class="v">${i.v}</div>`;kpisEl.appendChild(d)});

  const expTbody=document.querySelector('#tblExpire tbody'); expTbody.innerHTML='';
  customers.forEach(c=>{
    const start=c.contractStart; const end=addMonths(start,c.term); const left=daysBetween(today(),end);
    if(left<=90){const tr=document.createElement('tr'); tr.innerHTML=`<td>${c.name}</td><td>${start}</td><td>${end}</td><td>${left}</td><td>${left<0?'<span class="badge-danger">만료</span>':'<span class="badge-expire">만료예정</span>'}</td>`; expTbody.appendChild(tr)}
  });

  const shipTbody=document.querySelector('#tblShip tbody'); shipTbody.innerHTML='';
  customers.forEach(c=>c.sites.forEach(s=>{
    const next=addMonths(s.installed, Math.ceil(daysBetween(s.installed,today())/(30*POLICY.autoSupplyMonths))*POLICY.autoSupplyMonths);
    const tr=document.createElement('tr'); tr.innerHTML=`<td>${c.name}</td><td>${s.place}</td><td>${s.installed}</td><td>${next}</td><td>${daysBetween(today(),next)<=7?'<span class="badge">출고 준비</span>':''}</td>`; shipTbody.appendChild(tr);
  }));
}

function renderCust(){
  const customers=LS.get('v_customers',[]);
  const q=(document.getElementById('qCust').value||'').toLowerCase();
  const fType=document.getElementById('fType').value;
  const fState=document.getElementById('fState').value;
  const tbody=document.querySelector('#tblCust tbody'); tbody.innerHTML='';
  customers.forEach(c=>{
    const start=c.contractStart; const end=addMonths(start,c.term); const left=daysBetween(today(),end); const state=left<0?'해지':(left<=90?'만료예정':'활성');
    if(fType && c.type!==fType) return; if(fState && state!==fState) return; if(q && !(c.name+" "+c.id).toLowerCase().includes(q)) return;
    const serials=c.sites.map(s=>s.serial).join('<br/>');
    const tr=document.createElement('tr');
    tr.innerHTML=`<td>${c.id}</td><td>${c.name}</td><td>${c.type}</td><td>${c.sites.length}</td><td>${serials}</td><td>${start} ~ ${end}</td><td>${left}일</td><td><span class="badge">${Math.floor(Math.random()*3)}</span></td><td><button class="btn">보기</button></td>`;
    tbody.appendChild(tr);
  });
}

function renderWO(){
  const data=LS.get('v_wo',[]), tbody=document.querySelector('#tblWO tbody');
  const q=(document.getElementById('qWO').value||'').toLowerCase();
  const fType=document.getElementById('fWOType').value; const fState=document.getElementById('fWOState').value;
  tbody.innerHTML='';
  data.forEach(w=>{
    if(q && !(w.customer+" "+w.id).toLowerCase().includes(q)) return;
    if(fType && w.type!==fType) return; if(fState && w.state!==fState) return;
    const tr=document.createElement('tr');
    const pct=w.state==='DONE'?100:(w.state==='IN_PROGRESS'?60:20);
    tr.innerHTML=`<td>${w.id}</td><td>${w.type}</td><td>${w.customer}</td><td>${w.serial}</td><td>${w.due}</td><td>${w.state}</td>
    <td><div class="timeline">${['OPEN','IN_PROGRESS','DONE'].map(s=>`<span class='dot ${w.state===s||w.history.includes(s)?'active':''}'></span>`).join('')}<div class="progress" style="flex:1"><div style="width:${pct}%"></div></div></div></td>`;
    tbody.appendChild(tr);
  });
}

function renderBilling(){
  const data=LS.get('v_bills',[]);
  const tbody=document.querySelector('#tblBill tbody');
  const q=(document.getElementById('qBill').value||'').toLowerCase();
  const fState=document.getElementById('fBillState').value; 
  const mf=document.getElementById('mFrom').value; const mt=document.getElementById('mTo').value;
  tbody.innerHTML='';
  let sum=0, cnt=0; const rows=[];
  data.forEach(b=>{
    if(b.item!=='정기 구독료') return;
    if(q && !(b.customer+" "+b.id).toLowerCase().includes(q)) return;
    if(fState && b.state!==fState) return;
    if(mf && b.date<mf+'-01') return;
    if(mt && b.date>mt+'-31') return;
    const r={BillID:b.id,고객:b.customer,일자:b.date,항목:b.item,금액:b.amount,상태:b.state,메모:b.memo}; rows.push(r);
    const tr=document.createElement('tr'); tr.innerHTML=`<td>${b.id}</td><td>${b.customer}</td><td>${b.date}</td><td>${b.item}</td><td>${fmt(b.amount)}</td><td>${b.state}</td><td>${b.memo}</td>`; tbody.appendChild(tr);
    sum+=b.amount; cnt++;
  });
  document.getElementById('billSum').textContent=`표시 ${cnt}건 합계 ${fmt(sum)}`;
  const k=document.getElementById('billKPIs'); k.innerHTML='';
  const k1=document.createElement('div'); k1.className='kpi'; k1.innerHTML=`<div class='small'>이번달 구독(ALL)</div><div class='v'>${fmt(data.filter(b=>b.date.slice(0,7)===today().slice(0,7)&&b.item==='정기 구독료').reduce((s,x)=>s+x.amount,0))}</div>`; k.appendChild(k1);
  const k2=document.createElement('div'); k2.className='kpi'; k2.innerHTML=`<div class='small'>PAID 건수</div><div class='v'>${data.filter(b=>b.state==='PAID' && b.item==='정기 구독료').length}</div>`; k.appendChild(k2);
}

function renderSettle(){
  const data=LS.get('v_bills',[]).filter(b=>b.item==='정기 구독료');
  const m=document.getElementById('settleMonth').value || today().slice(0,7);
  const monthRows=data.filter(b=>b.date.slice(0,7)===m && b.state==='PAID');
  const tbody=document.querySelector('#tblSettle tbody'); tbody.innerHTML='';
  let vend=0, hq=0; monthRows.forEach(b=>{const v=b.amount*0.40; const h=b.amount*0.60; vend+=v; hq+=h; const tr=document.createElement('tr'); tr.innerHTML=`<td>${b.id}</td><td>${b.customer}</td><td>${fmt(b.amount)}</td><td>${fmt(v)}</td><td>${fmt(h)}</td>`; tbody.appendChild(tr)});
  document.getElementById('settleVendor').textContent=fmt(vend);
  const customers=LS.get('v_customers',[]); const goal=POLICY.renewGoal||10;
  const newCount=customers.filter(c=>c.contractStart.slice(0,7)===m).length;
  const ratio=Math.min(100,Math.round(newCount/goal*100));
  document.getElementById('incBar').style.width=ratio+'%';
  document.getElementById('incText').textContent=`${newCount} / 목표 ${goal}대`;
  document.getElementById('settleMemo').textContent=`익월 15일 지급 예정. 목표 달성 시 추가 5% 인센티브 별도 반영.`;
}

function renderTickets(){
  const data=LS.get('v_tickets',[]), tbody=document.querySelector('#tblTicket tbody');
  const f=document.getElementById('fTicket').value; tbody.innerHTML='';
  data.forEach(t=>{if(f && t.state!==f) return; const tr=document.createElement('tr'); tr.innerHTML=`<td>${t.id}</td><td>${t.customer}</td><td>${t.type}</td><td>${t.detail}</td><td>${t.state}</td><td>${t.date}</td><td><button class='btn'>답변</button> <button class='btn'>본사 이관</button></td>`; tbody.appendChild(tr)});
}

function renderCatalog(){
  const sn=LS.get('v_newScents',[]), ct=LS.get('v_newContents',[]);
  const sbody=document.querySelector('#tblNewScent tbody'); const cbody=document.querySelector('#tblNewContent tbody'); sbody.innerHTML=''; cbody.innerHTML='';
  sn.forEach(s=>{const tr=document.createElement('tr'); tr.innerHTML=`<td>${s.name} ${isNewWithin(s.created)?'<span class="badge new">NEW</span>':''}</td><td>${s.kind}</td><td>${fmt(s.price)}</td><td>${s.created}</td>`; sbody.appendChild(tr)});
  ct.forEach(c=>{const tr=document.createElement('tr'); tr.innerHTML=`<td>${c.name} ${isNewWithin(c.created)?'<span class="badge new">NEW</span>':''}</td><td>${c.tier}</td><td>${fmt(c.price)}</td><td>${c.created}</td>`; cbody.appendChild(tr)});
}

function renderShop(){
  const items=LS.get('v_shop',[]), tbody=document.querySelector('#tblShop tbody'); tbody.innerHTML='';
  items.forEach(it=>{const tr=document.createElement('tr'); tr.innerHTML=`<td>${it.sku}</td><td>${fmt(it.price)}</td><td>${it.disc}</td><td>${fmt(it.vendor)}</td><td><input type='number' class='input qty' value='1' min='1' style='width:70px'/></td><td><button class='btn addCart'>담기</button></td>`; tbody.appendChild(tr)});
}

function renderCart(){
  const cart=LS.get('v_cart',{}), tbody=document.querySelector('#tblCart tbody'); tbody.innerHTML=''; let sum=0;
  Object.entries(cart).forEach(([sku,v])=>{sum+=v.qty*v.price; const tr=document.createElement('tr'); tr.innerHTML=`<td>${sku}</td><td>${v.qty}</td><td>${fmt(v.qty*v.price)}</td><td><button class='btn rm' data-sku='${sku}'>X</button></td>`; tbody.appendChild(tr)});
  document.getElementById('cartSum').textContent=fmt(sum);
}

function renderStock(){
  const lots=LS.get('v_lots',[]), serials=LS.get('v_serials',[]);
  const lbody=document.querySelector('#tblLot tbody'); lbody.innerHTML='';
  lots.forEach(l=>{const qty=Number(l.to.split('-')[1]) - Number(l.from.split('-')[1]) + 1; const tr=document.createElement('tr'); tr.innerHTML=`<td>${l.lot}</td><td>${l.from} ~ ${l.to}</td><td>${qty}</td>`; lbody.appendChild(tr)});
  const sbody=document.querySelector('#tblSerial tbody'); sbody.innerHTML='';
  serials.forEach(s=>{const tr=document.createElement('tr'); tr.innerHTML=`<td>${s.serial}</td><td>${s.state}</td><td>${s.customer||''}</td><td>${s.loc||''}</td><td>${s.memo||''}</td>`; sbody.appendChild(tr)});
}

function renderAlerts(){
  const box=document.getElementById('alertBox'); box.innerHTML='';
  const customers=LS.get('v_customers',[]), tickets=LS.get('v_tickets',[]), sn=LS.get('v_newScents',[]), ct=LS.get('v_newContents',[]);
  const expire=customers.filter(c=>daysBetween(today(),addMonths(c.contractStart,c.term))<=30).map(c=>`⚠️ ${c.name} 계약 만료 임박`);
  const ship=customers.flatMap(c=>c.sites).filter(s=>daysBetween(today(),addMonths(s.installed,POLICY.autoSupplyMonths))<=7).map(s=>`📦 ${s.serial} 자동배송 예정`);
  const nt=[...sn.filter(x=>isNewWithin(x.created)).map(x=>`🆕 신규 향: ${x.name}`), ...ct.filter(x=>isNewWithin(x.created)).map(x=>`🆕 신규 콘텐츠: ${x.name}`)];
  [...expire,...ship,...nt, ...tickets.filter(t=>t.state==='OPEN').map(t=>`🎫 티켓 OPEN: ${t.customer} - ${t.type}`)].forEach(m=>{const p=document.createElement('div'); p.className='tag'; p.textContent=m; box.appendChild(p)});
}

function render(){renderDash();renderCust();renderWO();renderBilling();renderSettle();renderTickets();renderCatalog();renderShop();renderCart();renderStock();renderAlerts();}

/********** Delegated Events (.off().on()) **********/
(function bindDelegated(){
  // 탭 클릭
  $(document)
    .off('click','#tabs a[data-t]')
    .on('click','#tabs a[data-t]',function(e){
      e.preventDefault();
      const k=this.dataset.t;
      showTabKey(k);
    });

  // 더미데이터 리셋
  $(document)
    .off('click','#resetSeed')
    .on('click','#resetSeed',function(){
      ['vendor_seed_v5','v_customers','v_wo','v_bills','v_tickets','v_lots','v_serials','v_newScents','v_newContents','v_shop','v_cart']
        .forEach(k=>localStorage.removeItem(k));
      toast('더미데이터 초기화 완료');
      location.reload();
    });

  // CSV 버튼들
  $(document)
    .off('click','#woCsv')
    .on('click','#woCsv',function(){
      const rows=[...document.querySelectorAll('#tblWO tbody tr')]
        .map(tr=>{const tds=[...tr.children].map(td=>td.innerText);
          return {WO_ID:tds[0],유형:tds[1],고객:tds[2],시리얼:tds[3],마감일:tds[4],상태:tds[5]}});
      csv('workorders.csv',rows);
    });

  $(document)
    .off('click','#billCsv')
    .on('click','#billCsv',function(){
      const rows=[...document.querySelectorAll('#tblBill tbody tr')]
        .map(tr=>{const tds=[...tr.children].map(td=>td.innerText);
          return {BillID:tds[0],고객:tds[1],일자:tds[2],항목:tds[3],금액:tds[4],상태:tds[5],메모:tds[6]}});
      csv('billing_subscriptions.csv',rows);
    });

  // 티켓/로트 추가
  $(document)
    .off('click','#btnTicketNew')
    .on('click','#btnTicketNew',function(){
      const t=LS.get('v_tickets',[]); const nid='T'+(1000+t.length+1);
      t.push({id:nid,customer:'임시 고객',type:'AS',detail:'테스트',state:'OPEN',date:today()});
      LS.set('v_tickets',t); renderTickets(); toast('티켓 추가');
    });

  $(document)
    .off('click','#btnAddLot')
    .on('click','#btnAddLot',function(){
      const lots=LS.get('v_lots',[]); const n=lots.length+1;
      lots.push({lot:'LOT'+String(n).padStart(3,'0'),from:'AP5-260000',to:'AP5-260099'});
      LS.set('v_lots',lots); renderStock(); toast('샘플 로트 추가');
    });

  // Catalog add
  $(document)
    .off('click','#btnAddNew')
    .on('click','#btnAddNew',function(){
      const name=$('#newName').val().trim(); if(!name){toast('이름 입력'); return}
      const type=$('#newType').val(); const tier=$('#newTier').val(); const price=Number($('#newPrice').val()||0); const d=today();
      if(type==='scent'){
        const s=LS.get('v_newScents',[]); s.push({name,kind:tier==='free'?'free':'paid',price,created:d}); LS.set('v_newScents',s);
      }else{
        const c=LS.get('v_newContents',[]); c.push({name,tier,price,created:d}); LS.set('v_newContents',c);
      }
      renderCatalog(); toast('등록 완료');
    });

  // Shop: 담기/삭제(섹션 위임)
  $(document)
    .off('click','#sec-shop .addCart')
    .on('click','#sec-shop .addCart',function(){
      const tr=$(this).closest('tr')[0];
      const sku=tr.children[0].innerText;
      const price=Number(tr.children[3].innerText.replace(/[^\d]/g,''));
      const qty=Number($(tr).find('.qty').val()||1);
      const cart=LS.get('v_cart',{}); cart[sku]={qty:(cart[sku]?.qty||0)+qty,price};
      LS.set('v_cart',cart); renderCart(); toast('담김');
    });

  $(document)
    .off('click','#sec-shop .rm')
    .on('click','#sec-shop .rm',function(){
      const sku=this.dataset.sku; const cart=LS.get('v_cart',{}); delete cart[sku];
      LS.set('v_cart',cart); renderCart();
    });

  $(document)
    .off('click','#btnCheckout')
    .on('click','#btnCheckout',function(){
      const cart=LS.get('v_cart',{}); if(!Object.keys(cart).length){toast('장바구니 비어있음'); return}
      LS.set('v_cart',{}); renderCart(); toast('주문이 접수되었습니다');
    });

  // Filters
  $(document).off('input','#qCust,#fType,#fState').on('input','#qCust,#fType,#fState',renderCust);
  $(document).off('input','#qWO,#fWOType,#fWOState').on('input','#qWO,#fWOType,#fWOState',renderWO);
  $(document).off('input','#qBill,#fBillState,#mFrom,#mTo').on('input','#qBill,#fBillState,#mFrom,#mTo',renderBilling);
  $(document).off('input','#fTicket').on('input','#fTicket',renderTickets);

  // 티켓→WO 변환
  $(document)
    .off('click','#btnWoFromTicket')
    .on('click','#btnWoFromTicket',function(){
      const tickets=LS.get('v_tickets',[]); const opens=tickets.filter(t=>t.state==='OPEN');
      if(!opens.length){toast('OPEN 티켓 없음'); return}
      const wo=LS.get('v_wo',[]); let seed=Math.max(1000,...wo.map(w=>Number(w.id.replace(/\D/g,''))));
      opens.slice(0,3).forEach(t=>{
        wo.push({id:'WO'+(++seed),type:(t.type==='AS'?'AS':'콘텐츠'),customer:t.customer,serial:'AP5-NEW',due:addMonths(today(),1),state:'OPEN',history:['생성','OPEN']});
        t.state='IN_PROGRESS';
      });
      LS.set('v_wo',wo); LS.set('v_tickets',tickets); renderWO(); renderTickets(); toast('OPEN 티켓 3건을 WO로 변환');
    });

  // Sync (mock)
  $(document)
    .off('click','#btnSync')
    .on('click','#btnSync',function(){ toast('고객 포털 변경 내역을 동기화했습니다(샘플)'); });

  // Smoke
  $(document)
    .off('click','#btnRunSmoke')
    .on('click','#btnRunSmoke',function(){
      const out=[]; const push=(name,ok,msg='')=>out.push({name,ok,msg});
      try{ render(); push('렌더 호출',true) }catch(e){ push('렌더 호출',false,e.message) }
      try{
        const all=LS.get('v_bills',[]); const others=all.filter(b=>b.item!=='정기 구독료');
        const domRows=[...document.querySelectorAll('#tblBill tbody tr')].length;
        const onlySub = others.length===0 || domRows<=LS.get('v_customers',[]).length*2;
        push('빌링 정책(구독만)',onlySub, onlySub?'OK':'구독 외 항목 존재')
      }catch(e){ push('빌링 정책(구독만)',false,e.message) }
      try{ const has=!!document.querySelector('#tblWO .timeline'); push('WO 타임라인',has, has?'OK':'미표시') }catch(e){ push('WO 타임라인',false,e.message) }
      try{ const dev=LS.get('v_customers',[]).reduce((s,c)=>s+c.sites.length,0); push('더미 장비 20대+', dev>=20, '장비수='+dev) }catch(e){ push('더미 장비 20대+',false,e.message) }
      alert(out.map((r,i)=>`${i+1}. ${r.ok?'✅':'❌'} ${r.name} ${r.msg?'- '+r.msg:''}`).join('\n'));
    });
})();

/********** Boot **********/
render();
</script>
