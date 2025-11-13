<?php
/**
 * HQ 고객관리 - 탭 구조
 * 벤더/영업사원/고객 탭으로 구성
 */

// 각 탭에 대한 암호화된 토큰 생성
$today = date('Y-m-d');
$vendorToken = encryptValue($today . '/vendor_mgmt');
$salesRepToken = encryptValue($today . '/sales_rep_mgmt');
$customerToken = encryptValue($today . '/customer_list');
?>

<div class="wrap">
  <section id="sec-customer-mgmt" class="card section-card-first">
    <!-- 탭 버튼 영역 -->
    <div class="tab-nav-inline">
      <button class="tab-btn-inline active" data-token="<?= $vendorToken ?>" onclick="loadTabContent(this, '<?= $vendorToken ?>', '#customer-tab-content', '#sec-customer-mgmt')">
        벤더
      </button>
      <button class="tab-btn-inline" data-token="<?= $salesRepToken ?>" onclick="loadTabContent(this, '<?= $salesRepToken ?>', '#customer-tab-content', '#sec-customer-mgmt')">
        영업사원
      </button>
      <button class="tab-btn-inline" data-token="<?= $customerToken ?>" onclick="loadTabContent(this, '<?= $customerToken ?>', '#customer-tab-content', '#sec-customer-mgmt')">
        고객
      </button>
    </div>

    <!-- 탭 컨텐츠 영역 -->
    <div class="tab-content-area" id="customer-tab-content">
      <div class="table-text-center text-muted">
        <p>로딩 중...</p>
      </div>
    </div>
  </section>
</div>

<script>
// 페이지 로드 시 첫 번째 탭 자동 로드
(function() {
  const firstTab = document.querySelector('#sec-customer-mgmt .tab-btn-inline.active');
  if (firstTab) {
    const token = firstTab.getAttribute('data-token');
    loadTabContent(firstTab, token, '#customer-tab-content', '#sec-customer-mgmt');
  }
})();

// 하위 호환성을 위한 래퍼 함수
window.loadCustomerTab = function(btnElement, encryptedToken) {
  loadTabContent(btnElement, encryptedToken, '#customer-tab-content', '#sec-customer-mgmt');
}
</script>
