
<?php
/**
 * HQ 대시보드
 * 실제 DB 데이터 기반 KPI 및 현황 표시
 */

// 현재 월 계산
$currentMonth = date('Y-m');
$currentMonthStart = date('Y-m-01');
$currentMonthEnd = date('Y-m-t');
$today = date('Y-m-d');

// ============================================
// KPI 데이터 조회
// ============================================

// 1. 활성 고객 수
$activeCustomersSql = "SELECT COUNT(DISTINCT c.customer_id) as count
                       FROM customers c
                       LEFT JOIN subscriptions s ON c.customer_id = s.customer_id
                       WHERE c.is_active = 1 AND c.deleted_at IS NULL";
$response['item']['activeCustomersSql'] = $activeCustomersSql;
$activeCustomersResult = mysqli_query($con, $activeCustomersSql);
$activeCustomers = mysqli_fetch_assoc($activeCustomersResult)['count'] ?? 0;

// 2. 신규 고객 수 (이번 달)
$newCustomersSql = "SELECT COUNT(*) as count
                    FROM customers
                    WHERE DATE(created_at) >= '{$currentMonthStart}'
                    AND DATE(created_at) <= '{$currentMonthEnd}'
                    AND deleted_at IS NULL";
$response['item']['newCustomersSql'] = $newCustomersSql;
$newCustomersResult = mysqli_query($con, $newCustomersSql);
$newCustomers = mysqli_fetch_assoc($newCustomersResult)['count'] ?? 0;

// 3. 활성 구독 수
$activeSubscriptionsSql = "SELECT COUNT(*) as count
                           FROM subscriptions
                           WHERE status = 'ACTIVE'
                           AND deleted_at IS NULL";
$response['item']['activeSubscriptionsSql'] = $activeSubscriptionsSql;
$activeSubscriptionsResult = mysqli_query($con, $activeSubscriptionsSql);
$activeSubscriptions = mysqli_fetch_assoc($activeSubscriptionsResult)['count'] ?? 0;

// 4. 만료 임박 구독 (90일 이내)
$expiringSubscriptionsSql = "SELECT
                              s.subscription_id,
                              c.customer_id,
                              c.company_name,
                              s.start_date,
                              s.end_date,
                              DATEDIFF(s.end_date, CURDATE()) as days_remaining
                            FROM subscriptions s
                            LEFT JOIN customers c ON s.customer_id = c.customer_id
                            WHERE s.status = 'ACTIVE'
                            AND DATEDIFF(s.end_date, CURDATE()) <= 90
                            AND DATEDIFF(s.end_date, CURDATE()) >= 0
                            AND s.deleted_at IS NULL
                            ORDER BY days_remaining ASC
                            LIMIT 20";
$response['item']['expiringSubscriptionsSql'] = $expiringSubscriptionsSql;
$expiringResult = mysqli_query($con, $expiringSubscriptionsSql);
$expiringSubscriptions = [];
while ($row = mysqli_fetch_assoc($expiringResult)) {
    $expiringSubscriptions[] = $row;
}

// 5. 총 벤더 수
$totalVendorsSql = "SELECT COUNT(*) as count FROM vendors WHERE deleted_at IS NULL";
$response['item']['totalVendorsSql'] = $totalVendorsSql;
$totalVendorsResult = mysqli_query($con, $totalVendorsSql);
$totalVendors = mysqli_fetch_assoc($totalVendorsResult)['count'] ?? 0;

// 6. 총 영업사원 수
$totalSalesRepsSql = "SELECT COUNT(*) as count
                      FROM users u
                      LEFT JOIN roles r ON u.role_id = r.role_id
                      WHERE r.role_name = 'SALES_REP' AND u.deleted_at IS NULL";
$response['item']['totalSalesRepsSql'] = $totalSalesRepsSql;
$totalSalesRepsResult = mysqli_query($con, $totalSalesRepsSql);
$totalSalesReps = mysqli_fetch_assoc($totalSalesRepsResult)['count'] ?? 0;

// 7. 총 사업장 수
$totalSitesSql = "SELECT COUNT(*) as count FROM customer_sites WHERE is_active = 1 AND deleted_at IS NULL";
$response['item']['totalSitesSql'] = $totalSitesSql;
$totalSitesResult = mysqli_query($con, $totalSitesSql);
if ($totalSitesResult) {
    $totalSites = mysqli_fetch_assoc($totalSitesResult)['count'] ?? 0;
} else {
    $totalSites = 0;
}

// 8. 총 기기 수
$totalDevicesSql = "SELECT COUNT(*) as count FROM devices WHERE deleted_at IS NULL";
$response['item']['totalDevicesSql'] = $totalDevicesSql;
$totalDevicesResult = mysqli_query($con, $totalDevicesSql);
$totalDevices = mysqli_fetch_assoc($totalDevicesResult)['count'] ?? 0;

// 9. 오늘 출고 예정 목록 (대시보드용 - 상위 10건)
$todayShipmentsSql = "SELECT
                        sh.shipment_id,
                        sh.shipment_number,
                        c.company_name as customer_name,
                        sh.recipient_name,
                        sh.status,
                        sh.shipped_date,
                        sh.courier_company,
                        sh.tracking_number
                      FROM shipments sh
                      LEFT JOIN customers c ON sh.customer_id = c.customer_id
                      WHERE DATE(sh.shipped_date) = '{$today}'
                      OR (sh.status = 'PENDING' AND DATE(sh.created_at) <= '{$today}')
                      ORDER BY sh.shipped_date ASC, sh.created_at ASC
                      LIMIT 10";
$response['item']['todayShipmentsSql'] = $todayShipmentsSql;
$todayShipmentsResult = mysqli_query($con, $todayShipmentsSql);
$todayShipments = [];
while ($row = mysqli_fetch_assoc($todayShipmentsResult)) {
    $todayShipments[] = $row;
}

// 오늘 출고 예정 통계
$todayShipmentsCount = count($todayShipments);

// 10. 벤더 목록 (필터용)
$vendorListSql = "SELECT vendor_id, company_name FROM vendors WHERE deleted_at IS NULL ORDER BY company_name";
$response['item']['vendorListSql'] = $vendorListSql;
$vendorListResult = mysqli_query($con, $vendorListSql);
$vendorList = [];
while ($row = mysqli_fetch_assoc($vendorListResult)) {
    $vendorList[] = $row;
}

// 11. 최근 티켓 목록 (대시보드용 - 상위 10건)
$recentTicketsSql = "SELECT
                      t.ticket_id,
                      t.ticket_number,
                      t.subject,
                      t.status,
                      t.priority,
                      t.category,
                      c.company_name as customer_name,
                      t.created_at
                    FROM tickets t
                    LEFT JOIN customers c ON t.customer_id = c.customer_id
                    ORDER BY t.created_at DESC
                    LIMIT 10";
$response['item']['recentTicketsSql'] = $recentTicketsSql;
$recentTicketsResult = mysqli_query($con, $recentTicketsSql);
$recentTickets = [];
while ($row = mysqli_fetch_assoc($recentTicketsResult)) {
    $recentTickets[] = $row;
}

// 12. 티켓 통계
$ticketStatsSql = "SELECT
                    COUNT(*) as total_tickets,
                    COUNT(CASE WHEN status = 'OPEN' THEN 1 END) as open_tickets,
                    COUNT(CASE WHEN status = 'IN_PROGRESS' THEN 1 END) as in_progress_tickets,
                    COUNT(CASE WHEN status = 'RESOLVED' THEN 1 END) as resolved_tickets
                  FROM tickets
                  WHERE DATE(created_at) >= '{$currentMonthStart}'";
$response['item']['ticketStatsSql'] = $ticketStatsSql;
$ticketStatsResult = mysqli_query($con, $ticketStatsSql);
$ticketStats = mysqli_fetch_assoc($ticketStatsResult);
$totalTickets = $ticketStats['total_tickets'] ?? 0;
$openTickets = $ticketStats['open_tickets'] ?? 0;

// 티켓 Status 한글 매핑
$ticketStatusLabels = [
    'OPEN' => '접수',
    'IN_PROGRESS' => '진행중',
    'ON_HOLD' => '보류',
    'RESOLVED' => '해결',
    'CLOSED' => '완료'
];

// 출고 Status 한글 매핑
$shipmentStatusLabels = [
    'PENDING' => '대기',
    'SHIPPED' => '출고',
    'IN_TRANSIT' => '배송중',
    'DELIVERED' => '완료',
    'FAILED' => '실패',
    'RETURNED' => '반송'
];
?>
<div class="wrap">
  <section id="sec-dash" class="card section-card-first">
    <div class="card-hd card-hd-wrap">
      <div class="card-hd-content">
        <div class="card-hd-title-area">
          <div class="card-ttl">본사 대시보드</div>
          <div class="card-sub">실시간 현황 요약</div>
        </div>
      </div>
      <div class="row">
        <button id="btnExportKPI" class="btn">KPI CSV 내보내기</button>
        <button id="btnRefresh" class="btn primary">새로고침</button>
      </div>
    </div>
    <div class="card-bd card-bd-padding">
      <!-- KPI 그리드 -->
      <div id="kpis" class="kpi-grid">
        <div class="kpi">
          <div class="small">활성 고객 수</div>
          <div class="v" id="kpi-active-customers"><?php echo number_format($activeCustomers); ?></div>
        </div>
        <div class="kpi">
          <div class="small">신규 고객 수 (월)</div>
          <div class="v" id="kpi-new-customers"><?php echo number_format($newCustomers); ?></div>
        </div>
        <div class="kpi">
          <div class="small">활성 구독 수</div>
          <div class="v" id="kpi-active-subscriptions"><?php echo number_format($activeSubscriptions); ?></div>
        </div>
        <div class="kpi">
          <div class="small">만료 임박 (90일)</div>
          <div class="v" id="kpi-expiring"><?php echo number_format(count($expiringSubscriptions)); ?></div>
        </div>
        <div class="kpi">
          <div class="small">총 벤더 수</div>
          <div class="v" id="kpi-vendors"><?php echo number_format($totalVendors); ?></div>
        </div>
        <div class="kpi">
          <div class="small">총 영업사원 수</div>
          <div class="v" id="kpi-sales-reps"><?php echo number_format($totalSalesReps); ?></div>
        </div>
        <div class="kpi">
          <div class="small">총 사업장 수</div>
          <div class="v" id="kpi-sites"><?php echo number_format($totalSites); ?></div>
        </div>
        <div class="kpi">
          <div class="small">총 기기 수</div>
          <div class="v" id="kpi-devices"><?php echo number_format($totalDevices); ?></div>
        </div>
        <div class="kpi">
          <div class="small">오늘 출고 예정</div>
          <div class="v" id="kpi-today-shipments"><?php echo number_format($todayShipmentsCount); ?></div>
        </div>
        <div class="kpi">
          <div class="small">미처리 티켓</div>
          <div class="v" id="kpi-open-tickets"><?php echo number_format($openTickets); ?></div>
        </div>
      </div>

      <!-- 3단 그리드 -->
      <div class="grid-3 dashboard-tables">
        <!-- 오늘 출고 예정 -->
        <div class="card">
          <div class="card-hd">
            <div class="card-hd-title-area">
              <div class="card-ttl">오늘 출고 예정</div>
              <div class="card-sub">최근 10건</div>
            </div>
            <button id="btnShipmentCsv" class="btn">CSV</button>
          </div>
          <div class="card-bd table-wrap">
            <table class="tbl-list" id="tblShipments">
              <thead>
                <tr>
                  <th>고객명</th>
                  <th>수령인</th>
                  <th>상태</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($todayShipments)): ?>
                <tr>
                  <td colspan="3" class="table-empty-state">오늘 출고 예정이 없습니다.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($todayShipments as $shipment): ?>
                <tr>
                  <td><strong><?php echo htmlspecialchars($shipment['customer_name'] ?? '-'); ?></strong></td>
                  <td><?php echo htmlspecialchars($shipment['recipient_name']); ?></td>
                  <td>
                    <?php
                    $shipStatus = $shipment['status'];
                    $shipStatusLabel = $shipmentStatusLabels[$shipStatus] ?? $shipStatus;
                    $shipStatusClass = '';
                    if ($shipStatus == 'PENDING') $shipStatusClass = 'badge-status-urgent';
                    elseif ($shipStatus == 'SHIPPED' || $shipStatus == 'IN_TRANSIT') $shipStatusClass = 'badge-status-warning';
                    elseif ($shipStatus == 'DELIVERED') $shipStatusClass = 'badge-status-normal';
                    else $shipStatusClass = 'badge-status-inactive';
                    ?>
                    <span class="badge <?php echo $shipStatusClass; ?>"><?php echo $shipStatusLabel; ?></span>
                  </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- 만료 임박 구독 -->
        <div class="card">
          <div class="card-hd">
            <div class="card-hd-title-area">
              <div class="card-ttl">만료 임박 구독</div>
              <div class="card-sub">90일 이내</div>
            </div>
            <button id="btnExpireCsv" class="btn">CSV</button>
          </div>
          <div class="card-bd table-wrap">
            <table class="tbl-list" id="tblExpire">
              <thead>
                <tr>
                  <th>고객명</th>
                  <th>종료일</th>
                  <th>상태</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($expiringSubscriptions)): ?>
                <tr>
                  <td colspan="3" class="table-empty-state">만료 임박 구독이 없습니다.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($expiringSubscriptions as $sub): ?>
                <tr>
                  <td><strong><?php echo htmlspecialchars($sub['company_name']); ?></strong></td>
                  <td><?php echo date('m-d', strtotime($sub['end_date'])); ?> (<?php echo $sub['days_remaining']; ?>일)</td>
                  <td>
                    <?php if ($sub['days_remaining'] <= 30): ?>
                      <span class="badge badge-status-urgent">긴급</span>
                    <?php elseif ($sub['days_remaining'] <= 60): ?>
                      <span class="badge badge-status-warning">주의</span>
                    <?php else: ?>
                      <span class="badge badge-status-normal">정상</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- 티켓 (요청) -->
        <div class="card">
          <div class="card-hd">
            <div class="card-hd-title-area">
              <div class="card-ttl">티켓 (요청)</div>
              <div class="card-sub">최근 10건</div>
            </div>
            <button id="btnTicketCsv" class="btn">CSV</button>
          </div>
          <div class="card-bd table-wrap">
            <table class="tbl-list" id="tblTickets">
              <thead>
                <tr>
                  <th>제목</th>
                  <th>고객</th>
                  <th>상태</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($recentTickets)): ?>
                <tr>
                  <td colspan="3" class="table-empty-state">등록된 티켓이 없습니다.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($recentTickets as $ticket): ?>
                <tr>
                  <td><strong><?php echo htmlspecialchars($ticket['subject']); ?></strong></td>
                  <td><?php echo htmlspecialchars($ticket['customer_name'] ?? '-'); ?></td>
                  <td>
                    <?php
                    $status = $ticket['status'];
                    $statusLabel = $ticketStatusLabels[$status] ?? $status;
                    $statusClass = '';
                    if ($status == 'OPEN') $statusClass = 'badge-status-urgent';
                    elseif ($status == 'IN_PROGRESS') $statusClass = 'badge-status-warning';
                    elseif ($status == 'RESOLVED' || $status == 'CLOSED') $statusClass = 'badge-status-normal';
                    else $statusClass = 'badge-status-pending';
                    ?>
                    <span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                  </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
// 페이지 이름 (AJAX 호출용)
window.pageName = '<?= $pageName ?>';

// CSV 내보내기 함수
function exportTableToCSV(tableId, filename) {
  const table = document.getElementById(tableId);
  const rows = Array.from(table.querySelectorAll('tr'));

  const csv = rows.map(row => {
    const cells = Array.from(row.querySelectorAll('th, td'));
    return cells.map(cell => {
      const badge = cell.querySelector('.badge');
      if (badge) return '"' + badge.textContent.trim() + '"';
      return '"' + cell.textContent.trim().replace(/"/g, '""') + '"';
    }).join(',');
  }).join('\n');

  const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = filename;
  link.click();
}

// 이벤트 리스너
document.getElementById('btnShipmentCsv')?.addEventListener('click', () => {
  exportTableToCSV('tblShipments', 'HQ_오늘출고_' + new Date().toISOString().slice(0,10) + '.csv');
});

document.getElementById('btnExpireCsv')?.addEventListener('click', () => {
  exportTableToCSV('tblExpire', 'HQ_만료임박_' + new Date().toISOString().slice(0,10) + '.csv');
});

document.getElementById('btnTicketCsv')?.addEventListener('click', () => {
  exportTableToCSV('tblTickets', 'HQ_티켓요청_' + new Date().toISOString().slice(0,10) + '.csv');
});

document.getElementById('btnExportKPI')?.addEventListener('click', () => {
  const kpis = document.querySelectorAll('.kpi');
  const csv = 'KPI항목,값\n' + Array.from(kpis).map(kpi => {
    const label = kpi.querySelector('.small').textContent.trim();
    const value = kpi.querySelector('.v').textContent.trim();
    return `"${label}","${value}"`;
  }).join('\n');

  const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = 'HQ_KPI_' + new Date().toISOString().slice(0,10) + '.csv';
  link.click();
});

// 새로고침
document.getElementById('btnRefresh')?.addEventListener('click', () => {
  location.reload();
});
</script>
