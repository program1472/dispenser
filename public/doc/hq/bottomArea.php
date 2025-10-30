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
  const esc=s=>`"${String(s??'').replace(/"/g,'""')}"`;
  const text=[cols.join(','),...rows.map(r=>cols.map(c=>esc(r[c])).join(','))].join('\n');
  const blob=new Blob(["\ufeff"+text],{type:'text/csv;charset=utf-8;'});
  const a=document.createElement('a');a.href=URL.createObjectURL(blob);a.download=filename;document.body.appendChild(a);a.click();a.remove()
}

/********** Global Policy **********/
const DEFAULT_POLICY={
  subMonthly:29700,
  freePrintsPerYear:6,
  printPrice:5000,
  prices:{free:0,standard:50000,deluxe:100000,premium:200000,lucid:100000},
  scentFreeCount:6,
  autoSupplyMonths:2,
  vendor:{base:40, incentive:5, targetInstalls:10, promoFrom:today(), promoTo:addMonths(today(),1)},
  vendorDiscount:{ap5:20, oil:40, content:100, from:today(), to:addMonths(today(),1)}
};
function normalizePolicy(raw){
  const safeNum=(v,fb)=> (typeof v==="number" && !Number.isNaN(v))?v:fb;
  const out=JSON.parse(JSON.stringify(DEFAULT_POLICY));
  try{
    const p=(raw&&typeof raw==="object")?raw:{};
    out.subMonthly=safeNum(p.subMonthly, out.subMonthly);
    out.freePrintsPerYear=safeNum(p.freePrintsPerYear, out.freePrintsPerYear);
    out.printPrice=safeNum(p.printPrice, out.printPrice);
    const pr=(p.prices&&typeof p.prices==='object')?p.prices:{};
    out.prices={
      free:safeNum(pr.free,out.prices.free),
      standard:safeNum(pr.standard,out.prices.standard),
      deluxe:safeNum(pr.deluxe,out.prices.deluxe),
      premium:safeNum(pr.premium,out.prices.premium),
      lucid:safeNum(pr.lucid,out.prices.lucid)
    };
    const v=p.vendor||{}; const d=p.vendorDiscount||{};
    out.vendor.base=safeNum(v.base,out.vendor.base);
    out.vendor.incentive=safeNum(v.incentive,out.vendor.incentive);
    out.vendor.targetInstalls=safeNum(v.targetInstalls,out.vendor.targetInstalls);
    out.vendor.promoFrom=v.promoFrom||out.vendor.promoFrom;
    out.vendor.promoTo=v.promoTo||out.vendor.promoTo;
    out.vendorDiscount.ap5=safeNum(d.ap5,out.vendorDiscount.ap5);
    out.vendorDiscount.oil=safeNum(d.oil,out.vendorDiscount.oil);
    out.vendorDiscount.content=safeNum(d.content,out.vendorDiscount.content);
    out.vendorDiscount.from=d.from||out.vendorDiscount.from;
    out.vendorDiscount.to=d.to||out.vendorDiscount.to;
  }catch(e){}
  return out;
}
let POLICY=normalizePolicy(LS.get('hq_policy',null));
LS.set('hq_policy',POLICY);

/********** Seed Demo Data (20개 이상) **********/
(function seed(){
  if(LS.get('hq_seeded',false)) return;
  const rnd=(min,max)=>Math.floor(Math.random()*(max-min+1))+min;
  const pick=a=>a[rnd(0,a.length-1)];
  const monthsBack=m=>{const [y,mo]=today().split('-').map(Number);const dt=new Date(Date.UTC(y,mo-1-m,1));return `${dt.getUTCFullYear()}-${String(dt.getUTCMonth()+1).padStart(2,'0')}`};

  const custTypes=['골프장','예식장','병원','호텔'];
  const customers=[...Array(12)].map((_,i)=>{
    const start=`2025-${String(rnd(1,9)).padStart(2,'0')}-01`;
    const end=addMonths(start,12);
    return {id:`C${String(i+1).padStart(3,'0')}`, name:`고객${i+1}`, type:pick(custTypes), vendor:`V${String(rnd(1,5)).padStart(3,'0')}`, start, end, status:'활성'}
  });

  const vendors=[...Array(5)].map((_,i)=>({id:`V${String(i+1).padStart(3,'0')}`, name:`벤더${i+1}`, region:['서울','경기','부산','대구','광주'][i]}));

  const installs=[...Array(24)].map((_,i)=>({serial:`AP5-${1000+i}`, cust:pick(customers).id, site:["본관","로비","라운지","홀A","홀B"][rnd(0,4)], date:`2025-${String(rnd(1,9)).padStart(2,'0')}-${String(rnd(1,28)).padStart(2,'0')}`}));

  const bills=[]; let billId=1;
  customers.forEach(c=>{
    for(let m=0;m<9;m++){
      const ym=monthsBack(8-m);
      bills.push({id:`B${String(billId++).padStart(5,'0')}`, cust:c.id, ym, date:`${ym}-05`, item:'정기 구독료', amount:POLICY.subMonthly, state: pick(['PAID','PAID','PAID','INVOICED'])});
    }
  });

  const sales=[]; let sid=1; const tiers=['standard','deluxe','premium','lucid'];
  for(let i=0;i<40;i++){
    const ym=monthsBack(rnd(0,8)); const cust=pick(customers).id;
    const kind=pick(['print','content','scent']);
    let amount=0; let memo='';
    if(kind==='print'){amount=POLICY.printPrice*rnd(1,10); memo='프린팅';}
    if(kind==='content'){const t=pick(tiers); amount=POLICY.prices[t]; memo=`콘텐츠(${t})`}
    if(kind==='scent'){amount= rnd(20000,80000); memo='향 구매'}
    sales.push({id:`S${String(sid++).padStart(4,'0')}`, cust, ym, kind, amount, memo});
  }

  const tickets=[...Array(20)].map((_,i)=>({id:`T${1000+i}`, cust:pick(customers).id, type:pick(['프린팅','콘텐츠','출고','A/S']), body:'요청 내용 예시', state:pick(['OPEN','IN_PROGRESS','DONE']), date:`2025-${String(rnd(1,9)).padStart(2,'0')}-${String(rnd(1,28)).padStart(2,'0')}`}));

  const todayShip=[...Array(8)].map((_,i)=>({id:`SH${2000+i}`, date:today(), cust:pick(customers).id, site:pick(['본관','라운지','프런트','홀A']), item:pick(['AP-5','오일(400ml)','카트리지×6','프린팅']), qty:rnd(1,5), serial:`AP5-${rnd(1000,1100)}`, lot:`LOT-${rnd(1,5)}`, invoice:''}));

  const WO_TYPES=['출고','설치','회수','프린팅','콘텐츠','AS'];
  const wos=[...Array(25)].map((_,i)=>({
    id:`WO${3000+i}`, type:pick(WO_TYPES), cust:pick(customers).id, serial:`AP5-${rnd(1000,1100)}`, due:`2025-${String(rnd(1,12)).padStart(2,'0')}-${String(rnd(1,28)).padStart(2,'0')}`,
    state:pick(['OPEN','IN_PROGRESS','DONE']), hist:[{t:'OPEN'},{t:'IN_PROGRESS'},{t:'DONE'}].slice(0,rnd(1,3))
  }));

  const invoices=todayShip.map(s=>({shipId:s.id, date:s.date, cust:s.cust, site:s.site, invNo:'', courier:'CJ대한통운', state:'미입력'}));

  LS.set('hq_customers',customers);
  LS.set('hq_vendors',vendors);
  LS.set('hq_installs',installs);
  LS.set('hq_bills',bills);
  LS.set('hq_sales',sales);
  LS.set('hq_tickets',tickets);
  LS.set('hq_todayShip',todayShip);
  LS.set('hq_wos',wos);
  LS.set('hq_invoices',invoices);
  LS.set('hq_seeded',true);
})();

/********** State **********/
let CUSTOMERS=LS.get('hq_customers',[]);
let VENDORS=LS.get('hq_vendors',[]);
let INSTALLS=LS.get('hq_installs',[]);
let BILLS=LS.get('hq_bills',[]);
let SALES=LS.get('hq_sales',[]);
let TICKETS=LS.get('hq_tickets',[]);
let TODAY_SHIP=LS.get('hq_todayShip',[]);
let WOS=LS.get('hq_wos',[]);
let INVOICES=LS.get('hq_invoices',[]);

/********** Tabs **********/
function showTab(t){
  document.querySelectorAll('nav a').forEach(a=>a.classList.toggle('active',a.dataset.t===t));
  document.querySelectorAll('section[id^="sec-"]').forEach(s=>s.classList.add('hidden'));
  document.getElementById('sec-'+t).classList.remove('hidden');
}

/********** Dashboard **********/
function renderKPIs(){
  const active=CUSTOMERS.filter(c=>c.status==='활성').length;
  const ymNow=today().slice(0,7);
  const ymPrev=ymShift(ymNow,-1);
  const newThisMonth=randCountFromBills('NEW',ymNow);
  const renewals=calcRenewals(ymPrev,ymNow);
  const churn=calcChurn(ymPrev,ymNow);
  const subs=sum(BILLS.filter(b=>b.state==='PAID'&&b.ym>=ymShift(ymNow,-1)).map(b=>b.amount));
  const print=sum(SALES.filter(s=>s.kind==='print'&&s.ym>=ymShift(ymNow,-1)).map(s=>s.amount));
  const content=sum(SALES.filter(s=>s.kind==='content'&&s.ym>=ymShift(ymNow,-1)).map(s=>s.amount));
  const scent=sum(SALES.filter(s=>s.kind==='scent'&&s.ym>=ymShift(ymNow,-1)).map(s=>s.amount));
  const comm=calcVendorCommissionByMonth(ymNow).total;
  const k=[
    {k:'활성 고객 수',v:active},
    {k:'신규 고객 수(월)',v:newThisMonth},
    {k:'리뉴얼 고객 수(월)',v:renewals},
    {k:'이탈 고객 수(월)',v:churn},
    {k:'정기 구독 매출(최근2개월)',v:fmt(subs)},
    {k:'프린팅 매출(최근2개월)',v:fmt(print)},
    {k:'유료 콘텐츠 매출(최근2개월)',v:fmt(content)},
    {k:'유료 향 매출(최근2개월)',v:fmt(scent)},
    {k:'월별 벤더커미션 총합',v:fmt(comm)}
  ];
  const box=document.getElementById('kpis');
  box.innerHTML=k.map(i=>`<div class="kpi"><div class="small">${i.k}</div><div class="v">${i.v}</div></div>`).join('');
}
function ymShift(ym,delta){const [y,m]=ym.split('-').map(Number);const d=new Date(Date.UTC(y,m-1+delta,1));return `${d.getUTCFullYear()}-${String(d.getUTCMonth()+1).padStart(2,'0')}`}
function sum(a){return a.reduce((x,y)=>x+(y||0),0)}
function randCountFromBills(_,ym){return Math.floor(BILLS.filter(b=>b.ym===ym).length/30)}
function calcRenewals(){return Math.floor(CUSTOMERS.length/10)}
function calcChurn(){return Math.max(0,Math.floor(CUSTOMERS.length/20)-1)}

function renderTodayShip(){
  const tb=document.querySelector('#tblTodayShip tbody');
  tb.innerHTML=TODAY_SHIP.map(r=>`<tr><td><input type="checkbox" data-id="${r.id}"/></td><td>${r.cust}</td><td>${r.site}</td><td>${r.item}</td><td>${r.qty}</td><td>${r.invoice||'-'}</td></tr>`).join('');
}
function renderExpire(){
  const rows=CUSTOMERS.map(c=>{const rem=daysBetween(today(),c.end);return {...c,rem}}).filter(r=>r.rem<=90).sort((a,b)=>a.rem-b.rem);
  const tb=document.querySelector('#tblExpire tbody');
  tb.innerHTML=rows.map(r=>`<tr><td>${r.name}</td><td>${r.start}</td><td>${r.end}</td><td>${r.rem}</td><td>${r.rem<0?'<span class="badge warn">만료</span>':'만료예정'}</td></tr>`).join('');
}
function renderTickets(){
  const tb=document.querySelector('#tblTicket tbody');
  const rows=[...TICKETS].sort((a,b)=>b.id.localeCompare(a.id)).slice(0,10);
  tb.innerHTML=rows.map(t=>`<tr><td>${t.id}</td><td>${t.cust}</td><td>${t.type}</td><td>${t.body}</td><td>${t.state}</td><td>${t.date}</td></tr>`).join('');
}

/********** Vendor Performance **********/
function calcVendorCommissionByMonth(ym){
  const paid=BILLS.filter(b=>b.state==='PAID' && b.ym===ym);
  const byVendor={};
  paid.forEach(b=>{
    const cust=CUSTOMERS.find(c=>c.id===b.cust); if(!cust) return; const vId=cust.vendor;
    if(!byVendor[vId]) byVendor[vId]={amount:0};
    byVendor[vId].amount+=b.amount;
  });
  const rows=Object.keys(byVendor).map(vId=>{
    const base=POLICY.vendor.base/100, inc=POLICY.vendor.incentive/100;
    const installsThisMonth=INSTALLS.filter(i=>i.date.slice(0,7)===ym).length;
    const incentiveAchieved= installsThisMonth >= POLICY.vendor.targetInstalls;
    const commission= Math.round(byVendor[vId].amount*base);
    const incentive= incentiveAchieved? Math.round(byVendor[vId].amount*inc):0;
    return {vendor:vId, name:(VENDORS.find(v=>v.id===vId)||{}).name||vId, newCnt:Math.floor(installsThisMonth/5), renewCnt:Math.floor(installsThisMonth/8), installs:installsThisMonth, comm:commission, inc:incentive, sum:commission+incentive, qual:`${Math.min(100, Math.floor(installsThisMonth/POLICY.vendor.targetInstalls*100))}%`};
  });
  return {total: rows.reduce((s,r)=>s+r.sum,0), rows};
}
function renderVendorPerf(){
  const ym=document.getElementById('vpMonth').value||today().slice(0,7);
  const {total,rows}=calcVendorCommissionByMonth(ym);
  const k=[
    {k:'월별 커미션 총합',v:fmt(total)},
    {k:'기본 커미션',v:POLICY.vendor.base+'%'},
    {k:'인센티브',v:POLICY.vendor.incentive+'% (목표 '+POLICY.vendor.targetInstalls+'대)'}
  ];
  document.getElementById('vpKpis').innerHTML=k.map(i=>`<div class="kpi"><div class="small">${i.k}</div><div class="v">${i.v}</div></div>`).join('');
  const tb=document.querySelector('#tblVendorPerf tbody');
  tb.innerHTML=rows.map(r=>`<tr><td>${r.vendor}</td><td>${r.name}</td><td>${r.newCnt}</td><td>${r.renewCnt}</td><td>${r.installs}</td><td>${fmt(r.comm)}</td><td>${fmt(r.inc)}</td><td>${fmt(r.sum)}</td><td>${r.qual}</td></tr>`).join('');
  document.getElementById('vendorQualBox').innerHTML=`기본 커미션 ${POLICY.vendor.base}% + 인센티브 ${POLICY.vendor.incentive}% (목표 ${POLICY.vendor.targetInstalls}대) · 프로모션 ${POLICY.vendor.promoFrom} ~ ${POLICY.vendor.promoTo}`;
}

/********** Customer Performance **********/
function monthRange(fromYM,toYM){const out=[];let cur=fromYM;while(cur<=toYM){out.push(cur);cur=ymShift(cur,1)}return out}
function renderCustPerf(){
  const from=document.getElementById('cpFrom').value||ymShift(today().slice(0,7),-5);
  const to=document.getElementById('cpTo').value||today().slice(0,7);
  const months=monthRange(from,to);
  const rows=months.map(m=>{
    const active=CUSTOMERS.length - Math.floor(Math.random()*3);
    const newCnt=Math.floor(Math.random()*3);
    const renew=Math.floor(Math.random()*2);
    const churn=Math.max(0,Math.floor(Math.random()*2-0.3));
    const sub=sum(BILLS.filter(b=>b.ym===m && b.state==='PAID').map(b=>b.amount));
    const pr=sum(SALES.filter(s=>s.ym===m && s.kind==='print').map(s=>s.amount));
    const ct=sum(SALES.filter(s=>s.ym===m && s.kind==='content').map(s=>s.amount));
    const sc=sum(SALES.filter(s=>s.ym===m && s.kind==='scent').map(s=>s.amount));
    return {m,active,new:newCnt,renew,churn,sub,pr,ct,sc};
  });
  const tb=document.querySelector('#tblCustPerf tbody');
  tb.innerHTML=rows.map(r=>`<tr><td>${r.m}</td><td>${r.active}</td><td>${r.new}</td><td>${r.renew}</td><td>${r.churn}</td><td>${fmt(r.sub)}</td><td>${fmt(r.pr)}</td><td>${fmt(r.ct)}</td><td>${fmt(r.sc)}</td></tr>`).join('');
  const k=[
    {k:'활성 고객 수(현재)',v:CUSTOMERS.length},
    {k:'월별 평균 구독 매출',v:fmt(Math.round(sum(rows.map(r=>r.sub))/rows.length))},
    {k:'월별 평균 커미션(추정)',v:fmt(Math.round(sum(rows.map(r=>r.sub))*POLICY.vendor.base/100/rows.length))}
  ];
  document.getElementById('cpKpis').innerHTML=k.map(i=>`<div class="kpi"><div class="small">${i.k}</div><div class="v">${i.v}</div></div>`).join('');
  document.getElementById('cpMemo').textContent=`${from} ~ ${to} 기간의 실적을 집계했습니다.`;
}

/********** New Items **********/
function addNewItem(){
  const name=document.getElementById('newName').value.trim();
  const type=document.getElementById('newType').value;
  const tier=document.getElementById('newTier').value;
  const price=Number(document.getElementById('newPrice').value||0);
  if(!name){toast('이름을 입력하세요');return}
  const row={name,type,tier,price,date:today()};
  const key= type==='scent'?'hq_newScent':'hq_newContent';
  const list=LS.get(key,[]); list.unshift(row); LS.set(key,list);
  renderNewTables(); toast('등록되었습니다');
}
function renderNewTables(){
  const s=LS.get('hq_newScent',[]); const c=LS.get('hq_newContent',[]);
  const r1=document.querySelector('#tblNewScent tbody');
  r1.innerHTML=s.map(x=>`<tr><td>${x.name}${isNewWithin(x.date)?' <span class="badge new">NEW</span>':''}</td><td>${x.tier}</td><td>${fmt(x.price)}</td><td>${x.date}</td></tr>`).join('');
  const r2=document.querySelector('#tblNewContent tbody');
  r2.innerHTML=c.map(x=>`<tr><td>${x.name}${isNewWithin(x.date)?' <span class="badge new">NEW</span>':''}</td><td>${x.tier}</td><td>${fmt(x.price)}</td><td>${x.date}</td></tr>`).join('');
}

/********** Work Orders **********/
function renderWO(){
  const q=document.getElementById('qWO').value.trim();
  const fT=document.getElementById('fWOType').value;
  const fS=document.getElementById('fWOState').value;
  const rows=WOS.filter(w=>(!q || w.id.includes(q)||w.cust.includes(q)) && (!fT||w.type===fT) && (!fS||w.state===fS));
  const tb=document.querySelector('#tblWO tbody');
  tb.innerHTML=rows.map(w=>`<tr><td><input type="checkbox" data-id="${w.id}"/></td><td>${w.id}</td><td>${w.type}</td><td>${w.cust}</td><td>${w.serial}</td><td>${w.due}</td><td>${w.state}</td><td>${w.hist.map(h=>`<span class='tag'>${h.t}</span>`).join('')}</td></tr>`).join('');
}

/********** Shipping / Labels **********/
function renderShip(){
  const tb=document.querySelector('#tblShip tbody');
  tb.innerHTML=TODAY_SHIP.map(s=>`<tr><td><input type="checkbox" data-id="${s.id}"/></td><td>${s.id}</td><td>${s.date}</td><td>${s.cust}</td><td>${s.site}</td><td>${s.item}</td><td>${s.qty}</td><td>${s.serial}</td><td>${s.lot}</td><td>${s.invoice||''}</td></tr>`).join('');
}

/********** Invoices **********/
function renderInvoices(){
  const q=document.getElementById('qInv').value?.trim().toLowerCase()||'';
  const tb=document.querySelector('#tblInv tbody');
  const rows=INVOICES.filter(r=>!q || r.shipId.toLowerCase().includes(q) || r.cust.toLowerCase().includes(q));
  tb.innerHTML=rows.map(r=>`<tr><td>${r.shipId}</td><td>${r.date}</td><td>${r.cust}</td><td>${r.site}</td><td contenteditable>${r.invNo||''}</td><td contenteditable>${r.courier||''}</td><td>${r.state}</td></tr>`).join('');
}
function validateInvoices(){
  const rows=[...document.querySelectorAll('#tblInv tbody tr')].map(tr=>{
    const tds=tr.querySelectorAll('td');
    return {shipId:tds[0].textContent, date:tds[1].textContent, cust:tds[2].textContent, site:tds[3].textContent, invNo:tds[4].textContent.trim(), courier:tds[5].textContent.trim(), state:tds[6].textContent}
  });
  let ok=0, bad=0;
  rows.forEach(r=>{if(/^[A-Z0-9\-]{5,}$/.test(r.invNo)) {r.state='유효';ok++} else {r.state='미입력';bad++}});
  INVOICES=rows; LS.set('hq_invoices',INVOICES);
  renderInvoices(); toast(`검증 완료 · 유효 ${ok}, 미입력 ${bad}`);
}
function saveInvoices(){
  const rows=[...document.querySelectorAll('#tblInv tbody tr')].map(tr=>{
    const tds=tr.querySelectorAll('td');
    return {shipId:tds[0].textContent, date:tds[1].textContent, cust:tds[2].textContent, site:tds[3].textContent, invNo:tds[4].textContent.trim(), courier:tds[5].textContent.trim(), state:tds[6].textContent}
  });
  INVOICES=rows; LS.set('hq_invoices',INVOICES); toast('저장되었습니다');
}

/********** Policy Center **********/
function loadPolicyUI(){
  const p=POLICY;
  document.getElementById('pSub').value=p.subMonthly;
  document.getElementById('pFreePrint').value=p.freePrintsPerYear;
  document.getElementById('pPrintPrice').value=p.printPrice;
  document.getElementById('pAutoSupply').value=p.autoSupplyMonths;
  document.getElementById('prFree').value=p.prices.free;
  document.getElementById('prStd').value=p.prices.standard;
  document.getElementById('prDeluxe').value=p.prices.deluxe;
  document.getElementById('prPremium').value=p.prices.premium;
  document.getElementById('prLucid').value=p.prices.lucid;
  document.getElementById('pVendorBase').value=p.vendor.base;
  document.getElementById('pVendorInc').value=p.vendor.incentive;
  document.getElementById('pVendorTarget').value=p.vendor.targetInstalls;
  document.getElementById('pPromoFrom').value=p.vendor.promoFrom;
  document.getElementById('pPromoTo').value=p.vendor.promoTo;
  document.getElementById('pDiscAP5').value=p.vendorDiscount.ap5;
  document.getElementById('pDiscOil').value=p.vendorDiscount.oil;
  document.getElementById('pDiscContent').value=p.vendorDiscount.content;
  document.getElementById('pDiscFrom').value=p.vendorDiscount.from;
  document.getElementById('pDiscTo').value=p.vendorDiscount.to;
}
function savePolicy(){
  const p={...POLICY};
  p.subMonthly=Number(document.getElementById('pSub').value||p.subMonthly);
  p.freePrintsPerYear=Number(document.getElementById('pFreePrint').value||p.freePrintsPerYear);
  p.printPrice=Number(document.getElementById('pPrintPrice').value||p.printPrice);
  p.autoSupplyMonths=Number(document.getElementById('pAutoSupply').value||p.autoSupplyMonths);
  p.prices.free=Number(document.getElementById('prFree').value||p.prices.free);
  p.prices.standard=Number(document.getElementById('prStd').value||p.prices.standard);
  p.prices.deluxe=Number(document.getElementById('prDeluxe').value||p.prices.deluxe);
  p.prices.premium=Number(document.getElementById('prPremium').value||p.prices.premium);
  p.prices.lucid=Number(document.getElementById('prLucid').value||p.prices.lucid);
  p.vendor.base=Number(document.getElementById('pVendorBase').value||p.vendor.base);
  p.vendor.incentive=Number(document.getElementById('pVendorInc').value||p.vendor.incentive);
  p.vendor.targetInstalls=Number(document.getElementById('pVendorTarget').value||p.vendor.targetInstalls);
  p.vendor.promoFrom=document.getElementById('pPromoFrom').value||p.vendor.promoFrom;
  p.vendor.promoTo=document.getElementById('pPromoTo').value||p.vendor.promoTo;
  p.vendorDiscount.ap5=Number(document.getElementById('pDiscAP5').value||p.vendorDiscount.ap5);
  p.vendorDiscount.oil=Number(document.getElementById('pDiscOil').value||p.vendorDiscount.oil);
  p.vendorDiscount.content=Number(document.getElementById('pDiscContent').value||p.vendorDiscount.content);
  p.vendorDiscount.from=document.getElementById('pDiscFrom').value||p.vendorDiscount.from;
  p.vendorDiscount.to=document.getElementById('pDiscTo').value||p.vendorDiscount.to;
  POLICY=normalizePolicy(p); LS.set('hq_policy',POLICY); toast('정책이 저장되었습니다');
}
function resetPolicy(){POLICY=normalizePolicy(null); LS.set('hq_policy',POLICY); loadPolicyUI(); toast('정책이 초기화되었습니다')}

/********** Help / Tests **********/
function renderFAQ(){
  const box=document.getElementById('faqBox');
  box.innerHTML=`<ul class="small">
    <li>정책이 비정상일 경우 '정책 초기화'를 누르면 기본값으로 복구됩니다.</li>
    <li>출고 라벨은 '출고/라벨' 탭에서 멀티 선택 후 출력 가능합니다.</li>
    <li>커미션은 Billing의 PAID만 합산되며 익월 15일 지급 기준입니다.</li>
  </ul>`
}
function runTests(){
  const tests=[
    {name:'정책 유효성',fn:()=> POLICY.subMonthly>0 && POLICY.freePrintsPerYear>=0},
    {name:'더미데이터(고객≥12)',fn:()=> CUSTOMERS.length>=12},
    {name:'더미데이터(작업지시서≥20)',fn:()=> WOS.length>=20},
    {name:'송장 편집 가능',fn:()=> document.querySelector('#tblInv')!==null}
  ];
  const rows=tests.map((t,i)=>({i:i+1, name:t.name, ok:!!t.fn(), msg:t.fn()?'OK':'확인 필요'}));
  const tb=document.querySelector('#tblTests tbody');
  tb.innerHTML=rows.map(r=>`<tr><td>${r.i}</td><td>${r.name}</td><td>${r.ok?'<span class="badge ok">PASS</span>':'<span class="badge warn">FAIL</span>'}</td><td>${r.msg}</td></tr>`).join('');
}

/********** Delegated Events (.off().on) **********/
(function bindDelegated(){
  // 탭 전환
  $(document)
    .off('click','nav a[data-t]')
    .on('click','nav a[data-t]',function(e){e.preventDefault(); showTab(this.dataset.t)});

  // 대시보드 액션
  $(document)
    .off('click','#btnReset')
    .on('click','#btnReset',function(){LS.set('hq_seeded',false); location.reload()})
    .off('click','#btnSmoke')
    .on('click','#btnSmoke',function(){renderKPIs();renderTodayShip();renderExpire();renderTickets();toast('대시보드 새로고침')});

  // 벤더 성과
  $(document)
    .off('click','#btnCalcVP')
    .on('click','#btnCalcVP',renderVendorPerf)
    .off('click','#btnVPCsv')
    .on('click','#btnVPCsv',function(){
      const rows=[...document.querySelectorAll('#tblVendorPerf tbody tr')].map(tr=>{
        const t=tr.querySelectorAll('td');
        return{vendor:t[0].textContent,name:t[1].textContent,new:t[2].textContent,renew:t[3].textContent,installs:t[4].textContent,commission:t[5].textContent,incentive:t[6].textContent,sum:t[7].textContent,qualification:t[8].textContent}
      });
      csv('vendor_performance.csv',rows)
    });

  // 고객 성과
  $(document)
    .off('click','#btnCalcCP')
    .on('click','#btnCalcCP',renderCustPerf)
    .off('click','#btnCPCsv')
    .on('click','#btnCPCsv',function(){
      const rows=[...document.querySelectorAll('#tblCustPerf tbody tr')].map(tr=>{
        const t=tr.querySelectorAll('td');
        return{month:t[0].textContent, active:t[1].textContent, new:t[2].textContent, renew:t[3].textContent, churn:t[4].textContent, sub:t[5].textContent, print:t[6].textContent, content:t[7].textContent, scent:t[8].textContent}
      });
      csv('cust_performance.csv',rows)
    });

  // 신규 항목 등록
  $(document)
    .off('click','#btnAddNew')
    .on('click','#btnAddNew',addNewItem);

  // 작업지시서 필터/CSV/인쇄
  $(document)
    .off('input','#qWO')
    .on('input','#qWO',renderWO)
    .off('change','#fWOType,#fWOState')
    .on('change','#fWOType,#fWOState',renderWO)
    .off('click','#btnWoCsv')
    .on('click','#btnWoCsv',function(){
      const rows=[...document.querySelectorAll('#tblWO tbody tr')].map(tr=>{
        const t=tr.querySelectorAll('td');
        return{woId:t[1].textContent,type:t[2].textContent,cust:t[3].textContent,serial:t[4].textContent,due:t[5].textContent,state:t[6].textContent}
      });
      csv('work_orders.csv',rows)
    })
    .off('click','#btnWoPrintSel')
    .on('click','#btnWoPrintSel',function(){
      const ids=[...document.querySelectorAll('#tblWO tbody input[type=checkbox]:checked')].map(i=>i.dataset.id);
      if(!ids.length){toast('선택된 작업지시서가 없습니다');return}
      window.print();
    });

  // 출고/라벨
  $(document)
    .off('click','#btnAddShip')
    .on('click','#btnAddShip',function(){
      const n={id:`SH${Math.floor(Math.random()*9000)+1000}`, date:today(), cust:(CUSTOMERS[0]||{}).id||'C001', site:'본관', item:'오일(400ml)', qty:2, serial:'AP5-NEW', lot:'LOT-NEW', invoice:''};
      TODAY_SHIP.unshift(n); LS.set('hq_todayShip',TODAY_SHIP); renderShip(); toast('출고가 추가되었습니다');
    })
    .off('click','#btnShipPrint')
    .on('click','#btnShipPrint',function(){
      const ids=[...document.querySelectorAll('#tblShip tbody input[type=checkbox]:checked')].map(i=>i.dataset.id);
      if(!ids.length){toast('선택된 출고건이 없습니다');return}
      window.print();
    })
    .off('click','#btnShipCsv')
    .on('click','#btnShipCsv',function(){
      const rows=[...document.querySelectorAll('#tblTodayShip tbody tr')].map(tr=>{
        const t=tr.querySelectorAll('td');
        return{cust:t[1].textContent,site:t[2].textContent,item:t[3].textContent,qty:t[4].textContent,invoice:t[5].textContent}
      });
      csv('today_shipping.csv',rows)
    });

  // 송장
  $(document)
    .off('input','#qInv')
    .on('input','#qInv',renderInvoices)
    .off('click','#btnInvValidate')
    .on('click','#btnInvValidate',validateInvoices)
    .off('click','#btnInvSave')
    .on('click','#btnInvSave',saveInvoices);

  // 정책
  $(document)
    .off('click','#btnPolicySave')
    .on('click','#btnPolicySave',savePolicy)
    .off('click','#btnPolicyReset')
    .on('click','#btnPolicyReset',resetPolicy);

  // 도움말/테스트
  $(document)
    .off('click','#btnRunTests')
    .on('click','#btnRunTests',runTests);
})();

/********** Init **********/
function init(){
  loadPolicyUI();
  renderKPIs();
  renderTodayShip();
  renderExpire();
  renderTickets();
  renderVendorPerf();
  renderCustPerf();
  renderWO();
  renderShip();
  renderInvoices();
  renderNewTables();
  renderFAQ();
  document.getElementById('vpMonth').value=today().slice(0,7);
  document.getElementById('cpFrom').value=ymShift(today().slice(0,7),-5);
  document.getElementById('cpTo').value=today().slice(0,7);
}
init();
</script>
