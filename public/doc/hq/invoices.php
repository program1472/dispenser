
<div class="wrap">
	<section id="sec-invoice" class="card section-card-first">
		<div class="card-hd card-hd-wrap">
			<div class="card-hd-title-area">
				<div class="card-ttl">송장번호 관리</div>
				<div class="card-sub">멀티 편집 · 검증</div>
			</div>
			<div class="row"><input id="qInv" class="input" placeholder="고객/출고ID 검색"><button id="btnInvExport" class="btn">CSV</button></div>
		</div>
		<div class="card-bd table-wrap">
			<table class="table" id="tblInv">
				<thead>
					<tr>
						<th>출고ID</th>
						<th>일자</th>
						<th>고객</th>
						<th>사업장</th>
						<th>송장번호</th>
						<th>택배사</th>
						<th>상태</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>SH2000</td>
						<td>2025-10-02</td>
						<td>C006</td>
						<td>프런트</td>
						<td contenteditable=""></td>
						<td contenteditable="">CJ대한통운</td>
						<td>미입력</td>
					</tr>
					<tr>
						<td>SH2001</td>
						<td>2025-10-02</td>
						<td>C003</td>
						<td>프런트</td>
						<td contenteditable=""></td>
						<td contenteditable="">CJ대한통운</td>
						<td>미입력</td>
					</tr>
					<tr>
						<td>SH2002</td>
						<td>2025-10-02</td>
						<td>C004</td>
						<td>본관</td>
						<td contenteditable=""></td>
						<td contenteditable="">CJ대한통운</td>
						<td>미입력</td>
					</tr>
					<tr>
						<td>SH2003</td>
						<td>2025-10-02</td>
						<td>C003</td>
						<td>홀A</td>
						<td contenteditable=""></td>
						<td contenteditable="">CJ대한통운</td>
						<td>미입력</td>
					</tr>
					<tr>
						<td>SH2004</td>
						<td>2025-10-02</td>
						<td>C004</td>
						<td>프런트</td>
						<td contenteditable=""></td>
						<td contenteditable="">CJ대한통운</td>
						<td>미입력</td>
					</tr>
					<tr>
						<td>SH2005</td>
						<td>2025-10-02</td>
						<td>C003</td>
						<td>프런트</td>
						<td contenteditable=""></td>
						<td contenteditable="">CJ대한통운</td>
						<td>미입력</td>
					</tr>
					<tr>
						<td>SH2006</td>
						<td>2025-10-02</td>
						<td>C010</td>
						<td>홀A</td>
						<td contenteditable=""></td>
						<td contenteditable="">CJ대한통운</td>
						<td>미입력</td>
					</tr>
					<tr>
						<td>SH2007</td>
						<td>2025-10-02</td>
						<td>C008</td>
						<td>본관</td>
						<td contenteditable=""></td>
						<td contenteditable="">CJ대한통운</td>
						<td>미입력</td>
					</tr>
				</tbody>
			</table>
			<div class="row" style="margin-top:8px"><button id="btnInvValidate" class="btn">검증</button><button id="btnInvSave" class="btn primary">저장</button></div>
		</div>
	</section>
</div>