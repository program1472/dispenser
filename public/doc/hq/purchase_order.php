
<?php
/**
 * HQ 구매/발주 관리 - 탭 구조
 * 발주관리/바코드·QR 탭으로 구성
 */

// 각 탭에 대한 암호화된 토큰 생성
$today = date('Y-m-d');
$orderListToken = encryptValue($today . '/purchase_order_list');
$barcodeToken = encryptValue($today . '/purchase_barcode');
?>
<div class="wrap">
  <section id="sec-purchase-order" class="card section-card-first">
    <!-- 탭 버튼 영역 -->
    <div class="tab-nav-inline">
      <button class="tab-btn-inline active" data-token="<?= $orderListToken ?>" onclick="loadTabContent(this, '<?= $orderListToken ?>', '#purchase-tab-content', '#sec-purchase-order')">
        발주 관리
      </button>
      <button class="tab-btn-inline" data-token="<?= $barcodeToken ?>" onclick="loadTabContent(this, '<?= $barcodeToken ?>', '#purchase-tab-content', '#sec-purchase-order')">
        바코드/QR 관리
      </button>
    </div>

    <!-- 탭 컨텐츠 영역 -->
    <div class="tab-content-area" id="purchase-tab-content">
      <div class="table-text-center text-muted">
        <p>로딩 중...</p>
      </div>
    </div>
  </section>
</div>

<script>
// 페이지 로드 시 첫 번째 탭 자동 로드
setTimeout(function() {
  const firstTab = document.querySelector('#sec-purchase-order .tab-btn-inline.active');
  if (firstTab) {
    const token = firstTab.getAttribute('data-token');
    loadTabContent(firstTab, token, '#purchase-tab-content', '#sec-purchase-order');
  }
}, 100);

// 하위 호환성을 위한 래퍼 함수
window.loadPurchaseTab = function(btnElement, pageName) {
  // pageName을 받아서 토큰을 생성하는 대신, data-token 속성을 사용
  const token = btnElement.getAttribute('data-token');
  if (token) {
    loadTabContent(btnElement, token, '#purchase-tab-content', '#sec-purchase-order');
  }
}
</script>
