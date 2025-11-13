<div class="wrap">
<section id="sec-catalog" class="card">
    <div class="card-hd"><div><div class="card-ttl">신규 향/콘텐츠</div><div class="card-sub">최근 30일 신규 배지 표시</div></div>
      <div class="row">
        <input id="newName" class="input" placeholder="이름(예: 화이트머스크)">
        <select id="newType" class="select"><option value="scent">향</option><option value="content">콘텐츠</option></select>
        <select id="newTier" class="select"><option value="free">Free</option><option value="standard">Standard</option><option value="deluxe">Deluxe</option><option value="premium">Premium</option><option value="lucid">Lucid</option></select>
        <input id="newPrice" class="input" type="number" placeholder="가격(원)" style="width:120px">
        <button id="btnAddNew" class="btn primary">등록</button>
      </div>
    </div>
    <div class="card-bd grid-2">
      <div class="card"><div class="card-hd"><div class="card-ttl">향</div></div><div class="card-bd table-wrap"><table class="table" id="tblNewScent"><thead><tr><th>이름</th><th>종류</th><th>가격</th><th>등록일</th></tr></thead><tbody><tr><td>화이트머스크 <span class="badge new">NEW</span></td><td>free</td><td>₩0</td><td>2025-09-14</td></tr><tr><td>블랙체리 <span class="badge new">NEW</span></td><td>paid</td><td>₩22,000</td><td>2025-09-12</td></tr></tbody></table></div></div>
      <div class="card"><div class="card-hd"><div class="card-ttl">콘텐츠</div></div><div class="card-bd table-wrap"><table class="table" id="tblNewContent"><thead><tr><th>이름</th><th>티어</th><th>가격</th><th>등록일</th></tr></thead><tbody><tr><td>골프대회 공지 <span class="badge new">NEW</span></td><td>standard</td><td>₩50,000</td><td>2025-09-18</td></tr><tr><td>브랜드스토리_프리미엄 </td><td>premium</td><td>₩200,000</td><td>2025-06-01</td></tr></tbody></table></div></div>
    </div>
  </section>
</div>