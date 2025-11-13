
<div class="wrap">
    <section id="sec-hqPerf" class="card section-card-first">
        <div class="card-hd card-hd-wrap">
            <div class="card-hd-content">
                <div class="card-hd-title-area">
                    <div class="card-ttl">본사 실적 관리</div>
                    <div class="card-sub">전사 매출/수익 현황 · 부서별/사업부별 성과</div>
                </div>
                <div class="row">
                    <input id="hpMonth" type="month" class="input">
                    <button id="btnCalcHP" class="btn primary">조회</button>
                </div>
            </div>
            <div class="row">
                <button id="btnHPCsv" class="btn">CSV</button>
            </div>
        </div>
        <div class="card-bd grid-2">
            <div>
                <div class="kpi-grid" id="hpKpis">
                    <div class="kpi">
                        <div class="small">월 총 매출</div>
                        <div class="v">₩0</div>
                    </div>
                    <div class="kpi">
                        <div class="small">월 순이익</div>
                        <div class="v">₩0</div>
                    </div>
                    <div class="kpi">
                        <div class="small">수익률</div>
                        <div class="v">0%</div>
                    </div>
                    <div class="kpi">
                        <div class="small">전월 대비</div>
                        <div class="v">0%</div>
                    </div>
                </div>
                <div class="table-wrap" style="margin-top:10px">
                    <table class="table" id="tblHqPerf">
                        <thead>
                            <tr>
                                <th>부서/사업부</th>
                                <th>매출액</th>
                                <th>비용</th>
                                <th>순이익</th>
                                <th>수익률</th>
                                <th>목표 달성률</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="empty">데이터가 없습니다</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div>
                <div class="card">
                    <div class="card-hd">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div class="card-ttl">부서별 목표 현황</div>
                            <div class="card-sub">월간 목표 대비 진행률</div>
                        </div>
                    </div>
                    <div class="card-bd small" id="deptGoalBox">
                        <ul>
                            <li><b>영업팀:</b> 목표 ₩50,000,000 / 달성 ₩0 (0%)</li>
                            <li><b>기술팀:</b> 목표 ₩30,000,000 / 달성 ₩0 (0%)</li>
                            <li><b>고객지원팀:</b> 목표 ₩20,000,000 / 달성 ₩0 (0%)</li>
                        </ul>
                    </div>
                </div>
                <div class="card" style="margin-top:10px">
                    <div class="card-hd">
                        <div class="card-ttl">주요 지표</div>
                    </div>
                    <div class="card-bd small">
                        <ul>
                            <li><b>신규 고객:</b> 0명</li>
                            <li><b>이탈 고객:</b> 0명</li>
                            <li><b>고객 만족도:</b> 0점/5점</li>
                            <li><b>평균 계약 금액:</b> ₩0</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
