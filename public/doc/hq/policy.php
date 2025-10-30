<div class="wrap">
	<section id="sec-policy" class="card">
		<div class="card-hd">
			<div>
				<div class="card-ttl">정책센터</div>
				<div class="card-sub">월 구독료·무료 프린팅·가격 정책 · 벤더커미션 프로모션 · 벤더상품할인율(기간)</div>
			</div>
			<div class="row"><button id="btnPolicyReset" class="btn warn">정책 초기화</button><span class="small">※ 손상 시 초기화로 복구</span></div>
		</div>
		<div class="card-bd grid-2">
			<div class="card">
				<div class="card-hd">
					<div class="card-ttl">요금/혜택</div>
				</div>
				<div class="card-bd">
					<div class="row"><label style="width:140px" class="small">월 구독료</label><input id="pSub" type="number" class="input" style="width:160px"></div>
					<div class="row" style="margin-top:6px"><label style="width:140px" class="small">연 무료 프린팅</label><input id="pFreePrint" type="number" class="input" style="width:160px"></div>
					<div class="row" style="margin-top:6px"><label style="width:140px" class="small">프린팅 단가</label><input id="pPrintPrice" type="number" class="input" style="width:160px"></div>
					<div class="row" style="margin-top:6px"><label style="width:140px" class="small">자동 공급(개월)</label><input id="pAutoSupply" type="number" class="input" style="width:160px"></div>
				</div>
			</div>
			<div class="card">
				<div class="card-hd">
					<div class="card-ttl">콘텐츠 가격</div>
				</div>
				<div class="card-bd">
					<div class="row"><label style="width:140px" class="small">Free</label><input id="prFree" type="number" class="input" style="width:160px"></div>
					<div class="row" style="margin-top:6px"><label style="width:140px" class="small">Standard</label><input id="prStd" type="number" class="input" style="width:160px"></div>
					<div class="row" style="margin-top:6px"><label style="width:140px" class="small">Deluxe</label><input id="prDeluxe" type="number" class="input" style="width:160px"></div>
					<div class="row" style="margin-top:6px"><label style="width:140px" class="small">Premium</label><input id="prPremium" type="number" class="input" style="width:160px"></div>
					<div class="row" style="margin-top:6px"><label style="width:140px" class="small">Lucid</label><input id="prLucid" type="number" class="input" style="width:160px"></div>
					<div class="row no-print" style="margin-top:8px"><button id="btnPolicySave" class="btn primary">저장</button></div>
				</div>
			</div>
			<div class="card">
				<div class="card-hd">
					<div class="card-ttl">벤더 커미션 프로모션</div>
				</div>
				<div class="card-bd">
					<div class="row"><label style="width:140px" class="small">기본 커미션(%)</label><input id="pVendorBase" type="number" class="input" style="width:160px"></div>
					<div class="row" style="margin-top:6px"><label style="width:140px" class="small">인센티브(%)</label><input id="pVendorInc" type="number" class="input" style="width:160px"></div>
					<div class="row" style="margin-top:6px"><label style="width:140px" class="small">목표 설치대수</label><input id="pVendorTarget" type="number" class="input" style="width:160px"></div>
					<div class="row" style="margin-top:6px"><label style="width:140px" class="small">프로모션 기간</label><input id="pPromoFrom" type="date" class="input"><span class="small">~</span><input id="pPromoTo" type="date" class="input"></div>
				</div>
			</div>
			<div class="card">
				<div class="card-hd">
					<div class="card-ttl">벤더 상품 할인율(기간)</div>
				</div>
				<div class="card-bd">
					<div class="row"><label style="width:140px" class="small">AP-5(%)</label><input id="pDiscAP5" type="number" class="input" style="width:160px"></div>
					<div class="row" style="margin-top:6px"><label style="width:140px" class="small">오일(%)</label><input id="pDiscOil" type="number" class="input" style="width:160px"></div>
					<div class="row" style="margin-top:6px"><label style="width:140px" class="small">컨텐츠(%)</label><input id="pDiscContent" type="number" class="input" style="width:160px"></div>
					<div class="row" style="margin-top:6px"><label style="width:140px" class="small">기간</label><input id="pDiscFrom" type="date" class="input"><span class="small">~</span><input id="pDiscTo" type="date" class="input"></div>
				</div>
			</div>
		</div>
	</section>
</div>