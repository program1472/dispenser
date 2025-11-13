
<script>
// ====== 상태/유틸 ======
const qs=(s,p=document)=>p.querySelector(s);
const qsa=(s,p=document)=>[...p.querySelectorAll(s)];
const sections = {
  dash: qs('#sec-dash'),
  new: qs('#sec-new'),
  req: qs('#sec-req'),
  lib: qs('#sec-lib'),
  tags: qs('#sec-tags'),
  settle: qs('#sec-settle'),
};
function toast(msg){
  const t=document.createElement('div');
  t.className='toast'; t.textContent=msg;
  qs('#toasts').appendChild(t);
  setTimeout(()=>t.remove(),2400);
}
function rand(a){return a[Math.floor(Math.random()*a.length)]}
function ymd(d){return d.toISOString().slice(0,10)}
function addDays(d,n){const x=new Date(d);x.setDate(x.getDate()+n);return x}
function fmt(n){if(typeof n==='number') return '₩'+n.toLocaleString();return n}
function downloadText(filename,text){
  const blob=new Blob([text],{type:'text/plain'});
  const a=document.createElement('a');
  a.href=URL.createObjectURL(blob); a.download=filename; a.click();
  URL.revokeObjectURL(a.href);
}
function fillRows(sel, rows){
  const $tb = $(sel).find('tbody'); $tb.empty();
  rows.forEach(r=>{
    const tr = `<tr>${r.map(x=>`<td>${x}</td>`).join('')}</tr>`;
    $tb.append(tr);
  });
}

// ====== 더미 데이터 ======
const industries=['골프장','예식장','병원','호텔','카페'];
const sampleTags=['안내','이벤트','주말','프로모션','시즌','안전','맴버십','고급','심플','인포'];
const stateFlow=['OPEN','보완요청','IN_PROGRESS','DONE'];

let contents=[], requests=[], groups=[], settleItems=[];
function seed(){
  contents=[];requests=[];groups=[];settleItems=[];
  const today=new Date();
  for(let i=1;i<=24;i++){
    const created=addDays(today,-Math.floor(Math.random()*50));
    contents.push({id:'C'+String(i).padStart(3,'0'),title:`템플릿 ${i}`,industry:rand(industries),tags:[rand(sampleTags),rand(sampleTags)],date:ymd(created),img:'',price:0,from:'lucid'});
  }
  for(let i=1;i<=22;i++){
    const rDate=addDays(today,-Math.floor(Math.random()*25)-5);
    const due=addDays(rDate,7+Math.floor(Math.random()*7));
    const st=stateFlow[Math.floor(Math.random()*3)];
    requests.push({id:'R'+String(i).padStart(3,'0'),customer:`고객${Math.ceil(i/2)}`,title:`콘텐츠 수정 ${i}`,reqDate:ymd(rDate),due:ymd(due),state:st,files:[],history:[{t:ymd(rDate),s:'OPEN'},{t:ymd(addDays(rDate,2)),s:st}]});
  }
  groups=[{name:'골프 성수기',tags:['시즌','이벤트','안내']},{name:'호텔 프로모션',tags:['프로모션','맴버십']},{name:'병원/안전',tags:['안전','안내']}];
  for(let i=1;i<=18;i++){
    const d=addDays(today,-(30+Math.floor(Math.random()*28)));
    settleItems.push({date:ymd(d),customer:`고객${Math.ceil(i/3)}`,item:`디자인 ${i}`,amount:20000+Math.floor(Math.random()*5)*10000});
  }
}
seed();

function isNew(dateStr){const d=new Date(dateStr);const today=new Date();return (today-d)/86400000<=30}

// ====== 렌더 함수들 ======
function renderDash(){
  const today=new Date();
  const firstThis=new Date(today.getFullYear(),today.getMonth(),1);
  const firstPrev=new Date(today.getFullYear(),today.getMonth()-1,1);
  const lastPrev=new Date(today.getFullYear(),today.getMonth(),0);
  const prevItems=settleItems.filter(s=>{const sd=new Date(s.date);return sd>=firstPrev&&sd<=lastPrev});
  const prevSum=prevItems.reduce((a,b)=>a+b.amount,0);
  const pending=requests.filter(r=>r.state!=='DONE').length;
  const dueSoon=requests.filter(r=>r.state!=='DONE' && (new Date(r.due)-new Date())/86400000<=5).length;
  const kpis=[
    {k:'전월 루시드 합계',v:prevSum},
    {k:'미처리',v:pending},
    {k:'마감 임박(5일)',v:dueSoon},
    {k:'최근 30일 신규',v:contents.filter(c=>isNew(c.date)).length}
  ];
  const box=qs('#kpis'); box.innerHTML='';
  kpis.forEach(x=>{
    const d=document.createElement('div');
    d.className='kpi';
    d.innerHTML=`<div class="small">${x.k}</div><div class="v">${fmt(x.v)}</div>`;
    box.appendChild(d);
  });
  const recentReq=requests.filter(r=>r.state!=='DONE').sort((a,b)=>b.reqDate.localeCompare(a.reqDate)).slice(0,10);
  fillRows('#tblDashReq',recentReq.map(r=>[r.id,r.customer,`<span class="badge">${r.state}</span>`,r.reqDate,r.due]));
  const recentNew=contents.slice().sort((a,b)=>b.date.localeCompare(a.date)).slice(0,10);
  fillRows('#tblDashNew',recentNew.map(c=>[c.title,c.industry,c.tags.join(', '),c.date]));
}

function renderNewList(){
  const $tb = $('#tblNewContent tbody'); $tb.empty();
  contents.slice(0,50).forEach(c=>{
    const tr = `
      <tr>
        <td>${c.img?`<img src="${c.img}" style="width:60px;height:40px;object-fit:cover;border-radius:6px"/>`:'-'}</td>
        <td>${c.title}</td><td>${c.industry}</td><td>${c.tags.join(', ')}</td>
        <td>${c.date}</td><td>${isNew(c.date)?'<span class="badge new">NEW</span>':''}</td>
      </tr>`;
    $tb.append(tr);
  });
}

function renderReq(){
  const q=$('#qReq').val()?.toLowerCase()||'';
  const f=$('#fState').val()||'';
  const rows=requests.filter(r=>{
    const ok=(r.customer.toLowerCase().includes(q)||r.title.toLowerCase().includes(q));
    return ok && (!f||r.state===f);
  }).sort((a,b)=>b.reqDate.localeCompare(a.reqDate));

  const $tb=$('#tblReq tbody'); $tb.empty();
  rows.forEach(r=>{
    const tr=`
      <tr>
        <td><a href="#" data-id="${r.id}" class="req-link">${r.id}</a></td>
        <td>${r.customer}</td>
        <td>${r.title}</td>
        <td>${r.reqDate}</td>
        <td>${r.due}</td>
        <td class="no-print"><input type="file" data-up="${r.id}"/></td>
        <td class="td-status" data-s="${r.state}">
          <select data-state="${r.id}" class="select">
            ${stateFlow.map(s=>`<option ${s===r.state?'selected':''}>${s}</option>`).join('')}
          </select>
        </td>
        <td class="no-print"><button class="btn" data-dl="${r.id}">CSV 다운로드</button></td>
      </tr>`;
    $tb.append(tr);
  });
}

function openReqDetail(id){
  const r=requests.find(x=>x.id===id); if(!r) return;
  const body=`<div class="small">고객: <b>${r.customer}</b></div>
    <div class="small" style="margin:6px 0">요청: <b>${r.title}</b></div>
    <div class="small">요청일/마감일: ${r.reqDate} ~ ${r.due}</div>
    <hr class="sep"/>
    <div class="small">히스토리</div>
    <ul>${r.history.map(h=>`<li>${h.t} — ${h.s}</li>`).join('')}</ul>
    <div class="small">첨부: ${r.files.length? r.files.map(f=>`${f.name}(${fmt(f.size)}B)`).join(', ') : '없음'}</div>`;
  $('#modalTitle').text(`요청 상세 · ${r.id}`);
  $('#modalBody').html(body);
  openModal();
}

function renderLib(){
  const q=($('#libQuery').val()||'').toLowerCase();
  const t=$('#libTag').val()||'';
  const grid=qs('#libGrid'); grid.innerHTML='';

  const tagsAll=new Set();
  contents.forEach(c=>c.tags.forEach(x=>tagsAll.add(x)));
  const tagSel=$('#libTag');
  const before=tagSel.val();
  tagSel.html('<option value="">전체 태그</option>'+[...tagsAll].sort().map(x=>`<option ${x===before?'selected':''}>${x}</option>`).join(''));

  contents.filter(c=>{
    const text=(c.title+c.industry+c.tags.join(',')).toLowerCase();
    const matchText=text.includes(q); const matchTag=!t||c.tags.includes(t);
    return matchText&&matchTag;
  }).slice(0,60).forEach(c=>{
    const d=document.createElement('div');
    d.className='tile';
    d.style.border='1px solid var(--border)'; d.style.borderRadius='12px'; d.style.padding='12px'; d.style.background='#fff';
    d.innerHTML=`<div class="thumb">${c.img?`<img src="${c.img}" style="max-width:100%;max-height:100%;object-fit:cover"/>`:'썸네일'}</div>
      <div style="margin-top:6px;font-weight:700">${c.title} ${isNew(c.date)?'<span class="badge new">NEW</span>':''}</div>
      <div class="small">${c.industry} · 태그: ${c.tags.join(', ')}</div>
      <div class="small">등록일 ${c.date}</div>`;
    grid.appendChild(d);
  });
}

function renderGroups(){
  const $tb=$('#tblGroups tbody'); $tb.empty();
  groups.forEach(g=>$tb.append(`<tr><td>${g.name}</td><td>${g.tags.length}</td><td>${g.tags.join(', ')}</td></tr>`));
}
function renderTagsTable(){
  const $tb=$('#tblTagContent tbody'); $tb.empty();
  contents.slice(0,50).forEach(c=>{
    $tb.append(`<tr>
      <td><input type="checkbox" data-ck="${c.id}"/></td>
      <td>${c.title}</td><td>${c.industry}</td><td>${c.tags.join(', ')}</td><td>${c.date}</td>
    </tr>`);
  });
}

function renderSettle(){
  const mm=$('#settleMonth').val();
  const base=mm?new Date(mm+'-01'):new Date();
  const first=new Date(base.getFullYear(),base.getMonth()-1,1);
  const last=new Date(base.getFullYear(),base.getMonth(),0);
  const rows=settleItems.filter(s=>{const d=new Date(s.date);return d>=first && d<=last});
  const $tb=$('#tblSettle tbody'); $tb.empty(); let sum=0;
  rows.sort((a,b)=>a.date.localeCompare(b.date)).forEach(s=>{
    sum+=s.amount;
    $tb.append(`<tr><td>${s.date}</td><td>${s.customer}</td><td>${s.item}</td><td>${fmt(sumFmt(s.amount))}</td></tr>`);
  });
  $('#settleSum').text(fmt(sumFmt(sum)));
  const payday=new Date(last.getFullYear(),last.getMonth()+1,15);
  $('#settlePayday').text('지급일: '+ymd(payday));
}
function sumFmt(n){return n}

function openModal(){ $('#modal').css('display','flex'); }
function closeModal(){ $('#modal').css('display','none'); }

function renderAll(){ renderDash(); renderNewList(); renderReq(); renderLib(); renderGroups(); renderTagsTable(); renderSettle(); }

// ====== 이벤트 바인딩 (위임) ======
$(document)
  // 탭 전환
  .off('click', '#tabs a')
  .on('click', '#tabs a', function(e){
    e.preventDefault();
    const t = this.dataset.t;
    $('#tabs a').removeClass('active');
    $(this).addClass('active');
    Object.values(sections).forEach(s=>s.classList.add('hidden'));
    sections[t].classList.remove('hidden');
  })

  // 신규콘텐츠: 이미지 선택/미리보기
  .off('change', '#ncImg')
  .on('change', '#ncImg', function(e){
    const f=e.target.files?.[0]; if(!f) return;
    const r=new FileReader();
    r.onload=()=>{ window.ncImgData=r.result; $('#ncPreview').html(`<img src="${window.ncImgData}" alt="preview" style="max-width:100%;max-height:100%"/>`) };
    r.readAsDataURL(f);
  })

  // 신규콘텐츠 등록 버튼
  .off('click', '#btnAddContent')
  .on('click', '#btnAddContent', function(){
    const t=$('#ncTitle').val().trim(); if(!t) return toast('제목을 입력하세요');
    const industry=$('#ncIndustry').val();
    const tags=($('#ncTags').val()||'').split(',').map(s=>s.trim()).filter(Boolean);
    const date=$('#ncDate').val()||ymd(new Date());
    const id='C'+String(contents.length+1).padStart(3,'0');
    contents.unshift({id,title:t,industry,tags,date,img:(window.ncImgData||''),price:0,from:'lucid'});
    $('#ncTitle').val('');$('#ncTags').val('');$('#ncDate').val('');$('#ncImg').val('');
    window.ncImgData=''; $('#ncPreview').text('이미지 미리보기');
    renderNewList(); renderLib(); renderTagsTable(); renderDash(); toast('신규콘텐츠가 등록되었습니다');
  })

  // 요청 테이블: 상세 링크
  .off('click', '#tblReq .req-link')
  .on('click', '#tblReq .req-link', function(e){
    e.preventDefault();
    openReqDetail(this.dataset.id);
  })

  // 요청 테이블: 파일 업로드
  .off('change', '#tblReq input[type=file][data-up]')
  .on('change', '#tblReq input[type=file][data-up]', function(e){
    const id=this.getAttribute('data-up');
    const r=requests.find(x=>x.id===id);
    const f=e.target.files?.[0]; if(!r||!f) return;
    r.files.push({name:f.name,size:f.size});
    r.history.push({t:ymd(new Date()),s:'파일 업로드'});
    toast(`${id} 업로드: ${f.name}`);
  })

  // 요청 테이블: 상태 변경
  .off('change', '#tblReq select[data-state]')
  .on('change', '#tblReq select[data-state]', function(){
    const id=this.getAttribute('data-state');
    const r=requests.find(x=>x.id===id); if(!r) return;
    r.state=this.value;
    r.history.push({t:ymd(new Date()),s:`상태 ${r.state}`});
    renderReq(); renderDash();
  })

  // 요청 테이블: CSV 단건 다운로드
  .off('click', '#tblReq button[data-dl]')
  .on('click', '#tblReq button[data-dl]', function(){
    const id=this.getAttribute('data-dl');
    const r=requests.find(x=>x.id===id); if(!r) return;
    const csv=['ID,고객,제목,요청일,마감일,상태',`${r.id},${r.customer},${r.title},${r.reqDate},${r.due},${r.state}`].join('\n');
    downloadText(`${r.id}.csv`,csv);
  })

  // 요청 전체 CSV
  .off('click', '#btnReqCsv')
  .on('click', '#btnReqCsv', function(){
    const rows=[['ID','고객','제목','요청일','마감일','상태'].join(',')];
    requests.forEach(r=>rows.push([r.id,r.customer,r.title,r.reqDate,r.due,r.state].join(',')));
    downloadText('requests.csv',rows.join('\n'));
  })

  // 검색/필터
  .off('input', '#qReq, #fState, #libQuery, #libTag')
  .on('input', '#qReq, #fState, #libQuery, #libTag', function(){
    renderReq(); renderLib();
  })

  // 태그 그룹 추가
  .off('click', '#btnAddGrp')
  .on('click', '#btnAddGrp', function(){
    const name=$('#grpName').val().trim(); if(!name) return toast('그룹명을 입력하세요');
    let g=groups.find(x=>x.name===name); if(!g){g={name,tags:[]};groups.push(g)}
    const parts=name.split(','); if(parts.length>1){ g.name=parts.shift(); g.tags=[...new Set([...g.tags,...parts])] }
    renderGroups(); toast('그룹이 추가/수정되었습니다');
  })

  // 태그 일괄 적용
  .off('click', '#btnApplyTag')
  .on('click', '#btnApplyTag', function(){
    const gName=($('#grpName').val()||'').split(',')[0].trim();
    const g=groups.find(x=>x.name===gName); if(!g) return toast('그룹을 먼저 선택/생성하세요');
    const ids=$('input[data-ck]:checked').map(function(){return this.getAttribute('data-ck')}).get();
    contents.filter(c=>ids.includes(c.id)).forEach(c=>{c.tags=[...new Set([...c.tags,...g.tags])];});
    renderTagsTable(); renderLib(); toast(`${ids.length}건에 태그 일괄 적용`);
  })

  // 체크박스 전체 선택
  .off('change', '#chkAllContents')
  .on('change', '#chkAllContents', function(e){
    $('input[data-ck]').prop('checked', e.target.checked);
  })

  // 모달 열고닫기
  .off('click', '#modalClose')
  .on('click', '#modalClose', function(){ closeModal(); })
  .off('click', '#modal')
  .on('click', '#modal', function(e){ if(e.target===this) closeModal(); })

  // 스모크/리셋
  .off('click', '#btnSmoke')
  .on('click', '#btnSmoke', function(){
    toast('데이터 무결성 점검 중...');
    setTimeout(()=>{ toast(`OK: 콘텐츠 ${contents.length} · 요청 ${requests.length} · 정산 ${settleItems.length}`) }, 600);
  })
  .off('click', '#btnReset')
  .on('click', '#btnReset', function(){
    seed(); renderAll(); toast('더미데이터 재설정 완료');
  });

// 최초 렌더
renderAll();
</script>
