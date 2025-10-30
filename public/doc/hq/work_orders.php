<div class="wrap">
	<section id="sec-wo" class="card">
		<div class="card-hd">
			<div>
				<div class="card-ttl">작업지시서</div>
				<div class="card-sub">자동 생성 + 히스토리 타임라인 · 배치 출력</div>
			</div>
			<div class="row">
				<input id="qWO" class="input" placeholder="고객/WO 검색">
				<select id="fWOType" class="select">
					<option value="">전체 유형</option>
					<option>출고</option>
					<option>설치</option>
					<option>회수</option>
					<option>프린팅</option>
					<option>콘텐츠</option>
					<option>AS</option>
				</select>
				<select id="fWOState" class="select">
					<option value="">전체 상태</option>
					<option>OPEN</option>
					<option>IN_PROGRESS</option>
					<option>DONE</option>
				</select>
				<button id="btnWoCsv" class="btn">CSV</button>
				<button id="btnWoPrintSel" class="btn primary">선택 출력</button>
			</div>
		</div>
		<div class="card-bd table-wrap">
			<table class="table" id="tblWO">
				<thead>
					<tr>
						<th><input type="checkbox" id="chkAllWO"></th>
						<th>WO ID</th>
						<th>유형</th>
						<th>고객</th>
						<th>시리얼</th>
						<th>마감일</th>
						<th>상태</th>
						<th>히스토리</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><input type="checkbox" data-id="WO3000"></td>
						<td>WO3000</td>
						<td>설치</td>
						<td>C007</td>
						<td>AP5-1017</td>
						<td>2025-03-25</td>
						<td>OPEN</td>
						<td><span class="tag">OPEN</span></td>
					</tr>
					<tr>
						<td><input type="checkbox" data-id="WO3001"></td>
						<td>WO3001</td>
						<td>출고</td>
						<td>C008</td>
						<td>AP5-1089</td>
						<td>2025-10-25</td>
						<td>IN_PROGRESS</td>
						<td><span class="tag">OPEN</span><span class="tag">IN_PROGRESS</span></td>
					</tr>
					<tr>
						<td><input type="checkbox" data-id="WO3002"></td>
						<td>WO3002</td>
						<td>회수</td>
						<td>C002</td>
						<td>AP5-1052</td>
						<td>2025-06-01</td>
						<td>OPEN</td>
						<td><span class="tag">OPEN</span></td>
					</tr>
					<tr>
						<td><input type="checkbox" data-id="WO3003"></td>
						<td>WO3003</td>
						<td>회수</td>
						<td>C008</td>
						<td>AP5-1060</td>
						<td>2025-03-04</td>
						<td>DONE</td>
						<td><span class="tag">OPEN</span></td>
					</tr>
					<tr>
						<td><input type="checkbox" data-id="WO3004"></td>
						<td>WO3004</td>
						<td>출고</td>
						<td>C008</td>
						<td>AP5-1006</td>
						<td>2025-05-02</td>
						<td>IN_PROGRESS</td>
						<td><span class="tag">OPEN</span><span class="tag">IN_PROGRESS</span></td>
					</tr>
					<tr>
						<td><input type="checkbox" data-id="WO3005"></td>
						<td>WO3005</td>
						<td>AS</td>
						<td>C007</td>
						<td>AP5-1100</td>
						<td>2025-10-18</td>
						<td>DONE</td>
						<td><span class="tag">OPEN</span><span class="tag">IN_PROGRESS</span><span class="tag">DONE</span></td>
					</tr>
					<tr>
						<td><input type="checkbox" data-id="WO3006"></td>
						<td>WO3006</td>
						<td>프린팅</td>
						<td>C011</td>
						<td>AP5-1064</td>
						<td>2025-11-05</td>
						<td>DONE</td>
						<td><span class="tag">OPEN</span></td>
					</tr>
					<tr>
						<td><input type="checkbox" data-id="WO3007"></td>
						<td>WO3007</td>
						<td>AS</td>
						<td>C011</td>
						<td>AP5-1003</td>
						<td>2025-10-17</td>
						<td>OPEN</td>
						<td><span class="tag">OPEN</span><span class="tag">IN_PROGRESS</span></td>
					</tr>
					<tr>
						<td><input type="checkbox" data-id="WO3008"></td>
						<td>WO3008</td>
						<td>설치</td>
						<td>C006</td>
						<td>AP5-1099</td>
						<td>2025-05-10</td>
						<td>OPEN</td>
						<td><span class="tag">OPEN</span><span class="tag">IN_PROGRESS</span><span class="tag">DONE</span></td>
					</tr>
					<tr>
						<td><input type="checkbox" data-id="WO3009"></td>
						<td>WO3009</td>
						<td>회수</td>
						<td>C009</td>
						<td>AP5-1055</td>
						<td>2025-07-25</td>
						<td>DONE</td>
						<td><span class="tag">OPEN</span><span class="tag">IN_PROGRESS</span></td>
					</tr>
					<tr>
						<td><input type="checkbox" data-id="WO3010"></td>
						<td>WO3010</td>
						<td>콘텐츠</td>
						<td>C002</td>
						<td>AP5-1007</td>
						<td>2025-10-13</td>
						<td>OPEN</td>
						<td><span class="tag">OPEN</span></td>
					</tr>
					<tr>
						<td><input type="checkbox" data-id="WO3011"></td>
						<td>WO3011</td>
						<td>출고</td>
						<td>C006</td>
						<td>AP5-1014</td>
						<td>2025-06-23</td>
						<td>DONE</td>
						<td><span class="tag">OPEN</span></td>
					</tr>
					<tr>
						<td><input type="checkbox" data-id="WO3012"></td>
						<td>WO3012</td>
						<td>콘텐츠</td>
						<td>C008</td>
						<td>AP5-1061</td>
						<td>2025-10-18</td>
						<td>OPEN</td>
						<td><span class="tag">OPEN</span></td>
					</tr>
					<tr>
						<td><input type="checkbox" data-id="WO3013"></td>
						<td>WO3013</td>
						<td>AS</td>
						<td>C012</td>
						<td>AP5-1031</td>
						<td>2025-06-01</td>
						<td>OPEN</td>
						<td><span class="tag">OPEN</span><span class="tag">IN_PROGRESS</span></td>
					</tr>
					<tr>
						<td><input type="checkbox" data-id="WO3014"></td>
						<td>WO3014</td>
						<td>프린팅</td>
						<td>C002</td>
						<td>AP5-1014</td>
						<td>2025-10-02</td>
						<td>DONE</td>
						<td><span class="tag">OPEN</span><span class="tag">IN_PROGRESS</span></td>
					</tr>
					<tr>
						<td><input type="checkbox" data-id="WO3015"></td>
						<td>WO3015</td>
						<td>회수</td>
						<td>C012</td>
						<td>AP5-1032</td>
						<td>2025-03-25</td>
						<td>IN_PROGRESS</td>
						<td><span class="tag">OPEN</span><span class="tag">IN_PROGRESS</span></td>
					</tr>
					<tr>
						<td><input type="checkbox" data-id="WO3016"></td>
						<td>WO3016</td>
						<td>콘텐츠</td>
						<td>C012</td>
						<td>AP5-1089</td>
						<td>2025-11-26</td>
						<td>OPEN</td>
						<td><span class="tag">OPEN</span><span class="tag">IN_PROGRESS</span></td>
					</tr>
					<tr>
						<td><input type="checkbox" data-id="WO3017"></td>
						<td>WO3017</td>
						<td>콘텐츠</td>
						<td>C011</td>
						<td>AP5-1094</td>
						<td>2025-06-10</td>
						<td>IN_PROGRESS</td>
						<td><span class="tag">OPEN</span><span class="tag">IN_PROGRESS</span><span class="tag">DONE</span></td>
					</tr>
					<tr>
						<td><input type="checkbox" data-id="WO3018"></td>
						<td>WO3018</td>
						<td>콘텐츠</td>
						<td>C006</td>
						<td>AP5-1083</td>
						<td>2025-06-17</td>
						<td>DONE</td>
						<td><span class="tag">OPEN</span><span class="tag">IN_PROGRESS</span></td>
					</tr>
					<tr>
						<td><input type="checkbox" data-id="WO3019"></td>
						<td>WO3019</td>
						<td>프린팅</td>
						<td>C003</td>
						<td>AP5-1000</td>
						<td>2025-06-20</td>
						<td>IN_PROGRESS</td>
						<td><span class="tag">OPEN</span></td>
					</tr>
					<tr>
						<td><input type="checkbox" data-id="WO3020"></td>
						<td>WO3020</td>
						<td>회수</td>
						<td>C005</td>
						<td>AP5-1041</td>
						<td>2025-10-02</td>
						<td>DONE</td>
						<td><span class="tag">OPEN</span></td>
					</tr>
					<tr>
						<td><input type="checkbox" data-id="WO3021"></td>
						<td>WO3021</td>
						<td>설치</td>
						<td>C010</td>
						<td>AP5-1086</td>
						<td>2025-02-26</td>
						<td>IN_PROGRESS</td>
						<td><span class="tag">OPEN</span></td>
					</tr>
					<tr>
						<td><input type="checkbox" data-id="WO3022"></td>
						<td>WO3022</td>
						<td>설치</td>
						<td>C003</td>
						<td>AP5-1014</td>
						<td>2025-08-20</td>
						<td>IN_PROGRESS</td>
						<td><span class="tag">OPEN</span><span class="tag">IN_PROGRESS</span><span class="tag">DONE</span></td>
					</tr>
					<tr>
						<td><input type="checkbox" data-id="WO3023"></td>
						<td>WO3023</td>
						<td>콘텐츠</td>
						<td>C002</td>
						<td>AP5-1008</td>
						<td>2025-08-25</td>
						<td>IN_PROGRESS</td>
						<td><span class="tag">OPEN</span></td>
					</tr>
					<tr>
						<td><input type="checkbox" data-id="WO3024"></td>
						<td>WO3024</td>
						<td>출고</td>
						<td>C008</td>
						<td>AP5-1047</td>
						<td>2025-12-28</td>
						<td>OPEN</td>
						<td><span class="tag">OPEN</span></td>
					</tr>
				</tbody>
			</table>
			<div class="small">※ 상태는 조회만 가능(OPEN→IN_PROGRESS→DONE). 고객 포털 요청 시 자동 WO 생성.</div>
		</div>
	</section>
</div>