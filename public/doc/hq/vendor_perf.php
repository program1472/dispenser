<div class="wrap">
    <section id="sec-vendorPerf" class="card">
        <div class="card-hd">
            <div>
                <div class="card-ttl">벤더 실적 관리</div>
                <div class="card-sub">월별 커미션(총합/벤더별) · 신규/리뉴얼/자격 달성률</div>
            </div>
            <div class="row">
                <input id="vpMonth" type="month" class="input">
                <button id="btnCalcVP" class="btn primary">계산</button>
                <button id="btnVPCsv" class="btn">CSV</button>
            </div>
        </div>
        <div class="card-bd grid-2">
            <div>
                <div class="kpi-grid" id="vpKpis">
                    <div class="kpi">
                        <div class="small">월별 커미션 총합</div>
                        <div class="v">₩106,920</div>
                    </div>
                    <div class="kpi">
                        <div class="small">기본 커미션</div>
                        <div class="v">40%</div>
                    </div>
                    <div class="kpi">
                        <div class="small">인센티브</div>
                        <div class="v">5% (목표 10대)</div>
                    </div>
                </div>
                <div class="table-wrap" style="margin-top:10px">
                    <table class="table" id="tblVendorPerf">
                        <thead>
                            <tr>
                                <th>벤더ID</th>
                                <th>벤더명</th>
                                <th>신규</th>
                                <th>리뉴얼</th>
                                <th>설치대수</th>
                                <th>커미션(40%)</th>
                                <th>인센티브(5%)</th>
                                <th>합계</th>
                                <th>자격달성률</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>V005</td>
                                <td>벤더5</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>₩35,640</td>
                                <td>₩0</td>
                                <td>₩35,640</td>
                                <td>0%</td>
                            </tr>
                            <tr>
                                <td>V004</td>
                                <td>벤더4</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>₩47,520</td>
                                <td>₩0</td>
                                <td>₩47,520</td>
                                <td>0%</td>
                            </tr>
                            <tr>
                                <td>V002</td>
                                <td>벤더2</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>₩11,880</td>
                                <td>₩0</td>
                                <td>₩11,880</td>
                                <td>0%</td>
                            </tr>
                            <tr>
                                <td>V001</td>
                                <td>벤더1</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>₩11,880</td>
                                <td>₩0</td>
                                <td>₩11,880</td>
                                <td>0%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div>
                <div class="card">
                    <div class="card-hd">
                        <div class="card-ttl">벤더 자격 정책</div>
                        <div class="card-sub">최소 설치/매출 기준</div>
                    </div>
                    <div class="card-bd small" id="vendorQualBox">기본 커미션 40% + 인센티브 5% (목표 10대) · 프로모션 2025-10-02 ~ 2025-11-02</div>
                </div>
                <div class="card" style="margin-top:10px">
                    <div class="card-hd">
                        <div class="card-ttl">커미션 지급 일정</div>
                    </div>
                    <div class="card-bd small">익월 15일 지급 예정 · 계산 근거는 Billing의 <b>PAID</b>만 집계</div>
                </div>
            </div>
        </div>
    </section>
</div>