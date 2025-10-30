<div class="wrap">
    <section id="sec-custPerf" class="card">
        <div class="card-hd">
            <div>
                <div class="card-ttl">고객 실적 관리</div>
                <div class="card-sub">KPI · 월별/분기별 증감</div>
            </div>
            <div class="row">
                <input id="cpFrom" type="month" class="input">
                <input id="cpTo" type="month" class="input">
                <button id="btnCalcCP" class="btn primary">집계</button>
                <button id="btnCPCsv" class="btn">CSV</button>
            </div>
        </div>
        <div class="card-bd grid-2">
            <div>
                <div class="kpi-grid" id="cpKpis">
                    <div class="kpi">
                        <div class="small">활성 고객 수(현재)</div>
                        <div class="v">12</div>
                    </div>
                    <div class="kpi">
                        <div class="small">월별 평균 구독 매출</div>
                        <div class="v">₩262,350</div>
                    </div>
                    <div class="kpi">
                        <div class="small">월별 평균 커미션(추정)</div>
                        <div class="v">₩104,940</div>
                    </div>
                </div>
                <div class="table-wrap" style="margin-top:10px">
                    <table class="table" id="tblCustPerf">
                        <thead>
                            <tr>
                                <th>월</th>
                                <th>활성 고객 수</th>
                                <th>신규</th>
                                <th>리뉴얼</th>
                                <th>이탈</th>
                                <th>정기 구독 매출</th>
                                <th>프린팅 매출</th>
                                <th>유료 콘텐츠 매출</th>
                                <th>유료 향 매출</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2025-05</td>
                                <td>10</td>
                                <td>1</td>
                                <td>0</td>
                                <td>0</td>
                                <td>₩267,300</td>
                                <td>₩0</td>
                                <td>₩0</td>
                                <td>₩31,638</td>
                            </tr>
                            <tr>
                                <td>2025-06</td>
                                <td>12</td>
                                <td>1</td>
                                <td>0</td>
                                <td>0</td>
                                <td>₩267,300</td>
                                <td>₩70,000</td>
                                <td>₩0</td>
                                <td>₩63,368</td>
                            </tr>
                            <tr>
                                <td>2025-07</td>
                                <td>10</td>
                                <td>2</td>
                                <td>1</td>
                                <td>1</td>
                                <td>₩237,600</td>
                                <td>₩105,000</td>
                                <td>₩100,000</td>
                                <td>₩55,749</td>
                            </tr>
                            <tr>
                                <td>2025-08</td>
                                <td>12</td>
                                <td>1</td>
                                <td>1</td>
                                <td>1</td>
                                <td>₩297,000</td>
                                <td>₩30,000</td>
                                <td>₩300,000</td>
                                <td>₩190,725</td>
                            </tr>
                            <tr>
                                <td>2025-09</td>
                                <td>11</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>₩237,600</td>
                                <td>₩30,000</td>
                                <td>₩0</td>
                                <td>₩223,300</td>
                            </tr>
                            <tr>
                                <td>2025-10</td>
                                <td>11</td>
                                <td>1</td>
                                <td>1</td>
                                <td>0</td>
                                <td>₩267,300</td>
                                <td>₩0</td>
                                <td>₩450,000</td>
                                <td>₩66,184</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div>
                <div class="card">
                    <div class="card-hd">
                        <div class="card-ttl">메모</div>
                    </div>
                    <div class="card-bd small" id="cpMemo">2025-05 ~ 2025-10 기간의 실적을 집계했습니다.</div>
                </div>
            </div>
        </div>
    </section>
</div>