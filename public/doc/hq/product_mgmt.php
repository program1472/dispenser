
<?php
/**
 * HQ 제품관리 - 탭 구조
 * 기기/악세사리 탭으로 구성
 */

// 각 탭에 대한 암호화된 토큰 생성
$today = date('Y-m-d');
$deviceToken = encryptValue($today . '/product_device');
$accessoryToken = encryptValue($today . '/product_accessory');
$contentToken = encryptValue($today . '/product_content');
$fragranceToken = encryptValue($today . '/product_fragrance');
$suppliesToken = encryptValue($today . '/product_supplies');
?>
<div class="wrap">
  <section id="sec-product-mgmt" class="card section-card-first">
    <!-- 탭 버튼 영역 -->
    <div class="tab-nav-inline">
      <button class="tab-btn-inline active" data-token="<?= $deviceToken ?>" onclick="loadTabContent(this, '<?= $deviceToken ?>', '#product-tab-content', '#sec-product-mgmt')">
        기기
      </button>
      <button class="tab-btn-inline" data-token="<?= $accessoryToken ?>" onclick="loadTabContent(this, '<?= $accessoryToken ?>', '#product-tab-content', '#sec-product-mgmt')">
        악세사리
      </button>
      <button class="tab-btn-inline" data-token="<?= $contentToken ?>" onclick="loadTabContent(this, '<?= $contentToken ?>', '#product-tab-content', '#sec-product-mgmt')">
        콘텐츠
      </button>
      <button class="tab-btn-inline" data-token="<?= $fragranceToken ?>" onclick="loadTabContent(this, '<?= $fragranceToken ?>', '#product-tab-content', '#sec-product-mgmt')">
        향카트리지
      </button>
      <button class="tab-btn-inline" data-token="<?= $suppliesToken ?>" onclick="loadTabContent(this, '<?= $suppliesToken ?>', '#product-tab-content', '#sec-product-mgmt')">
        부자재
      </button>
    </div>

    <!-- 탭 컨텐츠 영역 -->
    <div class="tab-content-area" id="product-tab-content">
      <div class="table-text-center text-muted">
        <p>로딩 중...</p>
      </div>
    </div>
  </section>
</div>

<script>
// 페이지 로드 시 첫 번째 탭 자동 로드
(function() {
  const firstTab = document.querySelector('#sec-product-mgmt .tab-btn-inline.active');
  if (firstTab) {
    const token = firstTab.getAttribute('data-token');
    loadTabContent(firstTab, token, '#product-tab-content', '#sec-product-mgmt');
  }
})();

// 하위 탭에서 호출할 수 있도록 래퍼 함수 제공 (하위 호환성)
window.loadProductTab = function(btnElement, encryptedToken) {
  loadTabContent(btnElement, encryptedToken, '#product-tab-content', '#sec-product-mgmt');
}
</script>
