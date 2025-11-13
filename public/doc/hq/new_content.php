<?php
// 콘텐츠 데이터 가져오기
$sql_contents = "SELECT content_id, title, category1, category2, image_url, tier, price, created_at, status
                 FROM contents
                 ORDER BY created_at DESC";
$contents = query($sql_contents);
if (!is_array($contents)) $contents = [];

// 향 데이터 가져오기
$sql_scents = "SELECT scent_id, name, category, image_url, description, price, created_at, is_active
               FROM scents
               ORDER BY created_at DESC";
$scents = query($sql_scents);
if (!is_array($scents)) $scents = [];

// 카테고리 매핑
$category_map = [
  'Green&Herb' => '그린&허브',
  'Fruity' => '프루티',
  'Floral' => '플로럴',
  'Woody&Spicy' => '우디&스파이시',
  'Citrus' => '시트러스',
  'Musk' => '머스크',
  'Aqua' => '아쿠아',
  'Sweet' => '스위트',
  'OTHER' => '기타'
];

// 티어 매핑
$tier_map = [
  'free' => 'Free',
  'standard' => 'Standard',
  'deluxe' => 'Deluxe',
  'premium' => 'Premium',
  'lucid' => 'Lucid'
];
?>

<div class="wrap">
  <section id="sec-new-content" class="card section-card-first">
    <div class="card-hd card-hd-wrap">
      <div class="card-hd-content">
        <div class="card-hd-title-area">
          <div class="card-ttl">신규 콘텐츠/향 관리</div>
          <div class="card-sub">올투그린 ERP 연동 데이터 (추가/수정 불가)</div>
        </div>

        <!-- 탭 네비게이션 -->
        <div class="tab-nav-inline">
          <button class="tab-btn-inline active" data-tab="content">콘텐츠</button>
          <button class="tab-btn-inline" data-tab="scent">향</button>
        </div>
      </div>
      <div class="row">
        <button id="btnExportCsv" class="btn">CSV 내보내기</button>
      </div>
    </div>
    <div class="card-bd card-bd-padding">

      <!-- 콘텐츠 탭 -->
      <div id="tab-content" class="tab-content active">
        <div class="table-wrap">
          <table class="table" id="tblContent">
            <thead>
              <tr>
                <th>ID</th>
                <th>썸네일</th>
                <th>콘텐츠명</th>
                <th>카테고리</th>
                <th>등급</th>
                <th>가격</th>
                <th>등록일</th>
                <th>상태</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($contents as $content):
                $image_url = !empty($content['image_url']) ? htmlspecialchars($content['image_url']) : 'https://via.placeholder.com/60x40/CCCCCC/FFFFFF?text=No+Image';
                $tier_display = isset($tier_map[$content['tier']]) ? $tier_map[$content['tier']] : ucfirst($content['tier']);
                $category_display = $content['category1'] ?: '-';
                $created_date = date('Y-m-d', strtotime($content['created_at']));
                $days_old = (time() - strtotime($content['created_at'])) / (60 * 60 * 24);
                $status_badge = $days_old <= 30 ? '<span class="badge badge-success">NEW</span>' : '<span class="badge badge-info">정상</span>';
                if ($content['status'] == 'INACTIVE') {
                  $status_badge = '<span class="badge badge-secondary">비활성</span>';
                }
              ?>
              <tr data-id="<?php echo htmlspecialchars($content['content_id']); ?>">
                <td><?php echo htmlspecialchars($content['content_id']); ?></td>
                <td><img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($content['title']); ?>" class="thumbnail" onclick="viewImage(this.src)"></td>
                <td><?php echo htmlspecialchars($content['title']); ?></td>
                <td><?php echo htmlspecialchars($category_display); ?></td>
                <td><?php echo htmlspecialchars($tier_display); ?></td>
                <td>₩<?php echo number_format($content['price']); ?></td>
                <td><?php echo $created_date; ?></td>
                <td><?php echo $status_badge; ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- 향 탭 -->
      <div id="tab-scent" class="tab-content">
        <div class="table-wrap">
          <table class="table" id="tblScent">
            <thead>
              <tr>
                <th>ID</th>
                <th>썸네일</th>
                <th>향명</th>
                <th>카테고리</th>
                <th>용량</th>
                <th>가격</th>
                <th>등록일</th>
                <th>상태</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // 향 카테고리별 색상 매핑
              $scent_colors = [
                'Green&Herb' => 'C8E6C9',
                'Fruity' => 'FFE0B2',
                'Floral' => 'E1BEE7',
                'Woody&Spicy' => 'BCAAA4',
                'Citrus' => 'FFF9C4',
                'Musk' => 'F5F5F5',
                'Aqua' => 'B2EBF2',
                'Sweet' => 'FFCCBC',
                'OTHER' => 'E0E0E0'
              ];

              foreach ($scents as $scent):
                // DB에 이미지가 있으면 사용, 없으면 카테고리별 색상 placeholder
                if (!empty($scent['image_url'])) {
                  $image_url = htmlspecialchars($scent['image_url']);
                } else {
                  $color = $scent_colors[$scent['category']] ?? 'E0E0E0';
                  $text_color = in_array($scent['category'], ['Woody&Spicy']) ? 'FFFFFF' : '000000';
                  $image_url = "https://via.placeholder.com/60x40/{$color}/{$text_color}?text=" . urlencode($scent['scent_id']);
                }
                $category_display = $category_map[$scent['category']] ?? $scent['category'];
                $created_date = date('Y-m-d', strtotime($scent['created_at']));
                $days_old = (time() - strtotime($scent['created_at'])) / (60 * 60 * 24);
                $status_badge = $days_old <= 30 ? '<span class="badge badge-success">NEW</span>' : '<span class="badge badge-info">정상</span>';
                if (!$scent['is_active']) {
                  $status_badge = '<span class="badge badge-secondary">비활성</span>';
                }
              ?>
              <tr data-id="<?php echo htmlspecialchars($scent['scent_id']); ?>">
                <td><?php echo htmlspecialchars($scent['scent_id']); ?></td>
                <td><img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($scent['name']); ?>" class="thumbnail" onclick="viewImage(this.src)"></td>
                <td><?php echo htmlspecialchars($scent['name']); ?></td>
                <td><?php echo htmlspecialchars($category_display); ?></td>
                <td>400ml</td>
                <td>₩<?php echo number_format($scent['price']); ?></td>
                <td><?php echo $created_date; ?></td>
                <td><?php echo $status_badge; ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- 이미지 뷰어 모달 -->
<div id="imageModal" class="modal" style="display:none" onclick="this.style.display='none'">
  <div class="modal-image-content">
    <span class="modal-close" onclick="event.stopPropagation(); this.closest('.modal').style.display='none'">&times;</span>
    <img id="modalImage" src="" alt="썸네일 확대" style="max-width: 90%; max-height: 90vh; border-radius: 8px;">
  </div>
</div>

<script>
// 탭 전환
document.querySelectorAll('.tab-btn-inline').forEach(btn => {
  btn.addEventListener('click', function() {
    const tabId = this.dataset.tab;
    document.querySelectorAll('.tab-btn-inline').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    document.getElementById('tab-' + tabId).classList.add('active');
  });
});

// 썸네일 클릭 시 확대 보기
function viewImage(src) {
  const modal = document.getElementById('imageModal');
  const modalImg = document.getElementById('modalImage');
  modalImg.src = src;
  modal.style.display = 'flex';
}

// CSV 내보내기
document.getElementById('btnExportCsv')?.addEventListener('click', () => {
  const activeTab = document.querySelector('.tab-content.active');
  const table = activeTab.querySelector('table');
  const rows = Array.from(table.querySelectorAll('thead tr, tbody tr'));

  const csv = rows.map(row => {
    const cells = Array.from(row.querySelectorAll('th, td'));
    return cells.map(cell => {
      if (cell.querySelector('img')) return '"이미지"';
      const badge = cell.querySelector('.badge');
      if (badge) return badge.textContent.trim();
      return '"' + cell.textContent.trim().replace(/"/g, '""') + '"';
    }).join(',');
  }).join('\n');

  const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  const tabName = document.querySelector('.tab-btn.active').textContent;
  link.download = `HQ_${tabName}_` + new Date().toISOString().slice(0,10) + '.csv';
  link.click();
});

// ESC 키로 모달 닫기
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    document.querySelectorAll('.modal').forEach(modal => {
      modal.style.display = 'none';
    });
  }
});
</script>

