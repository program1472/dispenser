<?php
// 각 탭에 대한 암호화된 토큰 생성
$today = date('Y-m-d');
$allToken = encryptValue($today . '/perf_all');
$vendorToken = encryptValue($today . '/perf_vendor');
$salesToken = encryptValue($today . '/perf_sales');
$hqToken = encryptValue($today . '/perf_hq');
?>

<div class="wrap">
  <section id="sec-perf-mgmt" class="card section-card-first">
    <!-- 탭 버튼 영역 -->
    <div class="tab-nav-inline">
      <button class="tab-btn-inline active" data-token="<?= $allToken ?>" onclick="loadTabContent(this, '<?= $allToken ?>', '#perf-tab-content', '#sec-perf-mgmt')">
        전체
      </button>
      <button class="tab-btn-inline" data-token="<?= $vendorToken ?>" onclick="loadTabContent(this, '<?= $vendorToken ?>', '#perf-tab-content', '#sec-perf-mgmt')">
        벤더
      </button>
      <button class="tab-btn-inline" data-token="<?= $salesToken ?>" onclick="loadTabContent(this, '<?= $salesToken ?>', '#perf-tab-content', '#sec-perf-mgmt')">
        영업사원
      </button>
      <button class="tab-btn-inline" data-token="<?= $hqToken ?>" onclick="loadTabContent(this, '<?= $hqToken ?>', '#perf-tab-content', '#sec-perf-mgmt')">
        본사
      </button>
    </div>

    <!-- 탭 컨텐츠 영역 -->
    <div class="tab-content-area" id="perf-tab-content">
      <div class="table-text-center text-muted">
        <p>로딩 중...</p>
      </div>
    </div>
  </section>
</div>

<script>
// 페이지 로드 시 첫 번째 탭 자동 로드
(function() {
  const firstTab = document.querySelector('#sec-perf-mgmt .tab-btn-inline.active');
  if (firstTab) {
    const token = firstTab.getAttribute('data-token');
    loadTabContent(firstTab, token, '#perf-tab-content', '#sec-perf-mgmt');
  }
})();

// 하위 호환성을 위한 래퍼 함수
window.loadPerfTab = function(btnElement, encryptedToken) {
  loadTabContent(btnElement, encryptedToken, '#perf-tab-content', '#sec-perf-mgmt');
}
</script>
