<?php
/**
 * HQ 출고관리 탭 컨테이너
 * 작업지시서 / 출고히스토리 탭 구성
 */

// 각 탭에 대한 암호화된 토큰 생성
$today = date('Y-m-d');
$workOrdersToken = encryptValue($today . '/work_orders');
$shippingHistoryToken = encryptValue($today . '/shipping_history');
?>


<div class="wrap">
  <section id="sec-shipping-mgmt" class="card section-card-first">
    <!-- 탭 버튼 영역 -->
    <div class="tab-nav-inline">
      <button class="tab-btn-inline active" data-token="<?= $workOrdersToken ?>" onclick="loadTabContent(this, '<?= $workOrdersToken ?>', '#shipping-tab-content', '#sec-shipping-mgmt')">
        작업지시서
      </button>
      <button class="tab-btn-inline" data-token="<?= $shippingHistoryToken ?>" onclick="loadTabContent(this, '<?= $shippingHistoryToken ?>', '#shipping-tab-content', '#sec-shipping-mgmt')">
        출고히스토리
      </button>
    </div>

    <!-- 탭 컨텐츠 영역 -->
    <div class="tab-content-area" id="shipping-tab-content">
      <div class="table-text-center text-muted">
        <p>로딩 중...</p>
      </div>
    </div>
  </section>
</div>

<script>
// 페이지 로드 시 첫 번째 탭 자동 로드
(function() {
  const firstTab = document.querySelector('#sec-shipping-mgmt .tab-btn-inline.active');
  if (firstTab) {
    const token = firstTab.getAttribute('data-token');
    loadTabContent(firstTab, token, '#shipping-tab-content', '#sec-shipping-mgmt');
  }
})();

// 하위 호환성을 위한 래퍼 함수
window.loadShippingTab = function(btnElement, encryptedToken) {
  loadTabContent(btnElement, encryptedToken, '#shipping-tab-content', '#sec-shipping-mgmt');
}
</script>
