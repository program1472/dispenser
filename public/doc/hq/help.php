<div class="wrap">
	<section id="sec-help" class="card">
		<div class="card-hd">
			<div>
				<div class="card-ttl">도움</div>
				<div class="card-sub">FAQ / 진단</div>
			</div>
		</div>
		<div class="card-bd">
			<div class="grid-2">
				<div class="card">
					<div class="card-hd">
						<div class="card-ttl">FAQ</div>
					</div>
					<div class="card-bd" id="faqBox">
						<ul class="small">
							<li>정책이 비정상일 경우 '정책 초기화'를 누르면 기본값으로 복구됩니다.</li>
							<li>출고 라벨은 '출고/라벨' 탭에서 멀티 선택 후 출력 가능합니다.</li>
							<li>커미션은 Billing의 PAID만 합산되며 익월 15일 지급 기준입니다.</li>
						</ul>
					</div>
				</div>
				<div class="card">
					<div class="card-hd">
						<div class="card-ttl">진단/테스트</div>
						<div class="card-sub">정책/데이터 무결성 점검</div>
					</div>
					<div class="card-bd">
						<div class="row"><button id="btnRunTests" class="btn">테스트 실행</button><span class="small">에러 발생 시, 정책 초기화로 복구</span></div>
						<div class="table-wrap" style="margin-top:8px">
							<table class="table" id="tblTests">
								<thead>
									<tr>
										<th>#</th>
										<th>테스트</th>
										<th>결과</th>
										<th>메시지</th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>