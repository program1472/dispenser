<div class="sticky-filter">
    <div class="inner">
        <span class="small" style="font-weight:700;color:var(--accent)">사업장</span>
        <select id="siteFilter" class="select">
            <option value="ALL">전체</option>
            <option value="S1">본점</option>
            <option value="S2">A지점</option>
            <option value="S3">B지점</option>
        </select>
        <button id="resetSeed" class="btn">테스트 데이터 재설정</button>
    </div>
</div>
<div class="wrap">
    <section class="card" id="tab-Dashboard">
        <div class="card-hd">
            <div>
                <div class="card-ttl">나의 현황</div>
                <div class="card-sub">다수 사업장/다수 기기 기준 요약</div>
            </div>
        </div>
        <div class="card-bd">
            <div class="kpi-grid" id="dashKpis">
                <div class="kpi">
                    <div class="small">설치 대수</div>
                    <div class="v">9</div>
                    <div class="small">AP5-250001, AP5-250002, AP5-250003, AP5-250004, AP5-250005, AP5-250006, AP5-250007, AP5-250008, AP5-250009</div>
                </div>
                <!--div class="kpi">
                    <div class="small">무료 프린팅 잔여</div>
                    <div class="v">6</div>
                    <div class="small">100%</div>
                </div-->
                <div class="kpi">
                    <div class="small">다음 오일 배송</div>
                    <div class="v">2025-03-10</div>
                    <div class="small">설치일 + 2개월</div>
                </div>
                <!--div class="kpi">
                    <div class="small">선택 사업장</div>
                    <div class="v">본점, A지점, B지점</div>
                    <div class="small"></div>
                </div-->
            </div>
            <div class="grid-2" style="margin-top:12px">
                <div class="card">
                    <div class="card-hd">
                        <div class="card-ttl">설치/기기 상세</div>
                        <div class="card-sub">필터와 동기화</div>
                    </div>
                    <div class="card-bd">
                        <table class="table" id="dashDeviceTbl">
                            <thead>
                                <tr>
                                    <th>사업장</th>
                                    <th>설치 장소</th>
                                    <th>시리얼</th>
                                    <th>설치일</th>
                                    <th>선택 향</th>
                                    <th>선택 콘텐츠</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>본점</td>
                                    <td>로비</td>
                                    <td>AP5-250001</td>
                                    <td>2025-01-10</td>
                                    <td>그린티</td>
                                    <td>안전수칙_라운지</td>
                                </tr>
                                <tr>
                                    <td>본점</td>
                                    <td>연회장</td>
                                    <td>AP5-250002</td>
                                    <td>2025-01-10</td>
                                    <td>그린티</td>
                                    <td>안전수칙_라운지</td>
                                </tr>
                                <tr>
                                    <td>본점</td>
                                    <td>라운지</td>
                                    <td>AP5-250003</td>
                                    <td>2025-01-10</td>
                                    <td>그린티</td>
                                    <td>안전수칙_라운지</td>
                                </tr>
                                <tr>
                                    <td>A지점</td>
                                    <td>로비</td>
                                    <td>AP5-250004</td>
                                    <td>2025-01-10</td>
                                    <td>그린티</td>
                                    <td>안전수칙_라운지</td>
                                </tr>
                                <tr>
                                    <td>A지점</td>
                                    <td>연회장</td>
                                    <td>AP5-250005</td>
                                    <td>2025-01-10</td>
                                    <td>그린티</td>
                                    <td>안전수칙_라운지</td>
                                </tr>
                                <tr>
                                    <td>A지점</td>
                                    <td>라운지</td>
                                    <td>AP5-250006</td>
                                    <td>2025-01-10</td>
                                    <td>그린티</td>
                                    <td>안전수칙_라운지</td>
                                </tr>
                                <tr>
                                    <td>B지점</td>
                                    <td>로비</td>
                                    <td>AP5-250007</td>
                                    <td>2025-01-10</td>
                                    <td>그린티</td>
                                    <td>안전수칙_라운지</td>
                                </tr>
                                <tr>
                                    <td>B지점</td>
                                    <td>연회장</td>
                                    <td>AP5-250008</td>
                                    <td>2025-01-10</td>
                                    <td>그린티</td>
                                    <td>안전수칙_라운지</td>
                                </tr>
                                <tr>
                                    <td>B지점</td>
                                    <td>라운지</td>
                                    <td>AP5-250009</td>
                                    <td>2025-01-10</td>
                                    <td>그린티</td>
                                    <td>안전수칙_라운지</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card">
                    <div class="card-hd">
                        <div class="card-ttl">오일 배송 스케줄</div>
                        <div class="card-sub">설치일 기준 2개월마다 자동 공급</div>
                    </div>
                    <div class="card-bd" id="dashShipBox"></div>
                </div>
            </div>
            <div class="card" style="margin-top:12px">
                <div class="card-hd">
                    <div class="card-ttl">최근 요청/알림</div>
                </div>
                <div class="card-bd">
                    <div class="grid-2">
                        <div>
                            <table class="table" id="dashReqTbl">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>유형</th>
                                        <th>내용</th>
                                        <th>상태</th>
                                        <th>일자</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>RQ1003</td>
                                        <td>프린팅</td>
                                        <td>B지점 안전표지 3건</td>
                                        <td>OPEN</td>
                                        <td>2025-09-25</td>
                                    </tr>
                                    <tr>
                                        <td>RQ1001</td>
                                        <td>오일</td>
                                        <td>A지점 400ml x 4</td>
                                        <td>DONE</td>
                                        <td>2025-08-12</td>
                                    </tr>
                                    <tr>
                                        <td>RQ1002</td>
                                        <td>콘텐츠</td>
                                        <td>본점 행사안내 수정</td>
                                        <td>DONE</td>
                                        <td>2025-07-20</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div>
                            <div id="alertBox" class="small">
                                <div>무료 프린팅 잔여: <b>6</b> / 6</div>
                                <div>다음 오일 자동 배송 예정일: <b>2025-03-10</b></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>