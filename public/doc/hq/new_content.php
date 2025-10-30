<div class="wrap">
    <section id="sec-new" class="card">
        <div class="card-hd">
            <div>
                <div class="card-ttl">신규 향/콘텐츠 등록</div>
                <div class="card-sub">최근 30일 NEW 배지 표시</div>
            </div>
            <div class="row">
                <input id="newName" class="input" placeholder="이름">
                <select id="newType" class="select">
                    <option value="scent">향</option>
                    <option value="content">콘텐츠</option>
                </select>
                <select id="newTier" class="select">
                    <option value="free">Free</option>
                    <option value="standard">Standard</option>
                    <option value="deluxe">Deluxe</option>
                    <option value="premium">Premium</option>
                    <option value="lucid">Lucid</option>
                </select>
                <input id="newPrice" class="input" type="number" placeholder="가격(원)" style="width:120px">
                <button id="btnAddNew" class="btn primary">등록</button>
            </div>
        </div>
        <div class="card-bd grid-2">
            <div class="card">
                <div class="card-hd">
                    <div class="card-ttl">향</div>
                </div>
                <div class="card-bd table-wrap">
                    <table class="table" id="tblNewScent">
                        <thead>
                            <tr>
                                <th>이름</th>
                                <th>종류</th>
                                <th>가격</th>
                                <th>등록일</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-hd">
                    <div class="card-ttl">콘텐츠</div>
                </div>
                <div class="card-bd table-wrap">
                    <table class="table" id="tblNewContent">
                        <thead>
                            <tr>
                                <th>이름</th>
                                <th>티어</th>
                                <th>가격</th>
                                <th>등록일</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>