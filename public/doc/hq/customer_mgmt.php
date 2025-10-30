<?php
/** dispenser/doc/hq/customer_mgmt.php
 * - 로딩 경로
 *   ① 메뉴 클릭 → dispenser/index.php 가 이 파일을 require (뷰 렌더)
 *   ② AJAX POST → .htaccess → _ajax_.php → require 이 파일(핸들러만 실행)
 * - 규약
 *   • 이 파일은 상단/하단 공통(inc/topArea.php, inc/bottomArea.php) 사이의 “본문”만 출력
 *   • 공통 include/세션/DB는 _ajax_.php 또는 index.php 에서 처리됨 (여기서 require 금지)
 *   • POST(ajax)면 HTML 미출력, 전역 $response 에 필드 단위로 세팅 후 Finish()
 */

/* ===================== 스키마 유틸 ===================== */
if (!function_exists('hasTable')) {
  function hasTable(mysqli $con, string $table): bool {
    static $cache = [];
    if (isset($cache[$table])) return $cache[$table];
    $esc = $con->real_escape_string($table);
    $rs = $con->query("SHOW TABLES LIKE '{$esc}'");
    return $cache[$table] = (bool)($rs && $rs->num_rows);
  }
}
if (!function_exists('hasColumn')) {
  function hasColumn(mysqli $con, string $table, string $column): bool {
    static $cache = [];
    $key = $table.'.'.$column;
    if (isset($cache[$key])) return $cache[$key];
    if (!hasTable($con, $table)) return $cache[$key] = false;
    $rs = $con->query("SHOW COLUMNS FROM `{$table}` LIKE '{$con->real_escape_string($column)}'");
    return $cache[$key] = (bool)($rs && $rs->num_rows);
  }
}

/* ===================== POST 핸들러 ===================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST)) {
  try {
    // 라우터 박스 정규화: NW(평문) → OG(원본/암호) → 루트
    $POST_RAW = $_POST ?? [];
    $POST_BOX = $POST_RAW['NW'] ?? $POST_RAW['OG'] ?? $POST_RAW;

    $action  = $POST_BOX['action']  ?? '';
    $payload = $POST_BOX['payload'] ?? '';

    // payload 디코드
    $p = [];
    if (is_string($payload) && $payload !== '') {
      $tmp = json_decode($payload, true);
      if (json_last_error() === JSON_ERROR_NONE && is_array($tmp)) $p = $tmp;
    } elseif (is_array($payload)) {
      $p = $payload;
    } else {
      $p = $POST_BOX;
    }

    switch ($action) {
      case 'HQ_VENDOR_LIST':                  return hq_vendor_list();
      case 'HQ_CUSTOMER_LIST':                return hq_customer_list($p);
      case 'HQ_CUSTOMER_BULK_VENDOR_CHANGE':  return hq_customer_bulk_vendor_change($p);
      default:
        global $response;
        $response['result'] = false;
        $response['error']['msg']  = '지원하지 않는 action 입니다.';
        $response['error']['code'] = 400;
        return Finish();
    }
  } catch (Throwable $e) {
    global $response;
    $response['result'] = false;
    $response['error']['msg']  = '처리 중 오류가 발생했습니다.';
    $response['error']['code'] = 500;
    return Finish();
  }
  exit;
}

/* ===================== 액션 구현부 ===================== */
function hq_vendor_list() {
  global $con, $response;

  $select = "SELECT `vendor_id`, `name`";
  $from   = " FROM `vendors`";
  $where  = " WHERE 1=1";
  // v4에는 is_active가 없으므로, 있을 때만 조건 추가
  if (hasColumn($con, 'vendors', 'is_active')) $where .= " AND COALESCE(`is_active`,1)=1";
  $order  = " ORDER BY `name`";

  $sql = $select.$from.$where.$order;
  $vendors = [];
  if ($rs = $con->query($sql)) {
    while ($r = $rs->fetch_assoc()) $vendors[] = $r;
  } else {
    $response['result'] = false;
    $response['error']['msg']  = '벤더 목록 조회 실패';
    $response['error']['code'] = 500;
    return Finish();
  }

  $response['result'] = true; // ← boolean으로 세팅
  $response['item']['vendors'] = $vendors;
  return Finish();
}

function hq_customer_list(array $p) {
  global $con, $response;

  $vendor_id       = trim((string)($p['vendor_id'] ?? ''));
  $q               = trim((string)($p['q'] ?? ''));
  $only_unassigned = !empty($p['only_unassigned']);

  $has_u_created   = hasColumn($con, 'users', 'created_at');
  $has_u_active    = hasColumn($con, 'users', 'is_active');

  $join_by_ucid    = hasColumn($con, 'users', 'customer_id'); // u.customer_id
  $join_by_cuid    = hasColumn($con, 'customers', 'user_id'); // c.user_id

  $has_c_vendor_id = hasColumn($con, 'customers', 'vendor_id');
  $has_vendors     = hasTable($con, 'vendors');

  $selects = [
    "u.`user_id`",
    "u.`name`",
    "u.`email`",
    ($has_u_created ? "u.`created_at`" : "NULL AS `created_at`"),
    ($has_u_active  ? "u.`is_active`"  : "1 AS `is_active`"),
  ];

  $joins = [];
  $customers_joined = false;
  if ($join_by_ucid) {
    $joins[] = "LEFT JOIN `customers` c ON c.`customer_id` = u.`customer_id`";
    $customers_joined = true;
  } elseif ($join_by_cuid) {
    $joins[] = "LEFT JOIN `customers` c ON c.`user_id` = u.`user_id`";
    $customers_joined = true;
  }

  if ($customers_joined && $has_c_vendor_id && $has_vendors) {
    $joins[] = "LEFT JOIN `vendors` v ON v.`vendor_id` = c.`vendor_id`";
    $selects[] = "v.`name` AS `vendor_name`";
  } else {
    $selects[] = "NULL AS `vendor_name`";
  }

  $sql  = "SELECT ".implode(", ", $selects)." FROM `users` u";
  $sql .= " JOIN `roles` r ON r.`role_id` = u.`role_id` AND r.`code` = 'CUSTOMER'";
  if ($joins) $sql .= " ".implode(" ", $joins);
  $sql .= " WHERE 1=1";

  $bind = []; $types = '';

  if ($vendor_id !== '' && $customers_joined && $has_c_vendor_id) {
    $sql   .= " AND c.`vendor_id` = ?";
    $types .= 's'; $bind[] = $vendor_id;
  }
  if ($q !== '') {
    $sql   .= " AND u.`name` LIKE CONCAT('%',?,'%')";
    $types .= 's'; $bind[] = $q;
  }
  if ($only_unassigned && $customers_joined && $has_c_vendor_id) {
    $sql   .= " AND c.`vendor_id` IS NULL";
  }

  $sql .= " ORDER BY ".($has_u_created ? "u.`created_at` DESC" : "u.`user_id` DESC");
  $sql .= " LIMIT 1000";

  if ($types) {
    $stmt = $con->prepare($sql);
    if (!$stmt) {
      $response['result'] = false;
      $response['error']['msg']  = '쿼리 준비 실패';
      $response['error']['code'] = 500;
      return Finish();
    }
    $stmt->bind_param($types, ...$bind);
    if (!$stmt->execute()) {
      $response['result'] = false;
      $response['error']['msg']  = '쿼리 실행 실패';
      $response['error']['code'] = 500;
      return Finish();
    }
    $rs = $stmt->get_result();
  } else {
    $rs = $con->query($sql);
    if (!$rs) {
      $response['result'] = false;
      $response['error']['msg']  = '쿼리 실행 실패';
      $response['error']['code'] = 500;
      return Finish();
    }
  }

  $rows = [];
  if ($rs) while ($r = $rs->fetch_assoc()) $rows[] = $r;

  $response['result'] = true; // ← boolean
  $response['item']['rows'] = $rows;
  return Finish();
}

function hq_customer_bulk_vendor_change(array $p) {
  global $con, $response;

  $has_c_vendor_id = hasColumn($con, 'customers', 'vendor_id');
  $join_by_ucid    = hasColumn($con, 'users', 'customer_id');
  $join_by_cuid    = hasColumn($con, 'customers', 'user_id');

  if (!$has_c_vendor_id) {
    $response['result'] = false;
    $response['error']['msg']  = '스키마에 customers.vendor_id 가 없습니다.';
    $response['error']['code'] = 500;
    return Finish();
  }
  if (!$join_by_ucid && !$join_by_cuid) {
    $response['result'] = false;
    $response['error']['msg']  = 'users↔customers 연계 컬럼이 없습니다.';
    $response['error']['code'] = 500;
    return Finish();
  }

  $ids           = $p['user_ids'] ?? [];
  $new_vendor_id = array_key_exists('new_vendor_id', $p) ? $p['new_vendor_id'] : null;
  if ($new_vendor_id === '') $new_vendor_id = null; // "" → null(해제)

  if (!is_array($ids) || !count($ids)) {
    $response['result'] = false;
    $response['error']['msg']  = '선택된 회원이 없습니다.';
    $response['error']['code'] = 400;
    return Finish();
  }

  // 벤더 지정인 경우에만 존재 검증
  if ($new_vendor_id !== null) {
    if (!hasTable($con, 'vendors')) {
      $response['result'] = false;
      $response['error']['msg']  = 'vendors 테이블이 없습니다.';
      $response['error']['code'] = 500;
      return Finish();
    }
    $stmt = $con->prepare("SELECT 1 FROM `vendors` WHERE `vendor_id`=? LIMIT 1");
    if (!$stmt) {
      $response['result'] = false;
      $response['error']['msg']  = '벤더 검증 실패';
      $response['error']['code'] = 500;
      return Finish();
    }
    $stmt->bind_param('s', $new_vendor_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) {
      $response['result'] = false;
      $response['error']['msg']  = '존재하지 않는 밴더입니다.';
      $response['error']['code'] = 404;
      return Finish();
    }
  }

  $in    = implode(',', array_fill(0, count($ids), '?'));
  $types = str_repeat('i', count($ids));

  if ($join_by_ucid) {
    $sql = "
      SELECT u.`user_id`, u.`customer_id` AS cid
      FROM `users` u
      JOIN `roles` r ON r.`role_id`=u.`role_id` AND r.`code`='CUSTOMER'
      WHERE u.`user_id` IN ($in)
    ";
  } else {
    $sql = "
      SELECT u.`user_id`, c.`customer_id` AS cid
      FROM `users` u
      JOIN `roles` r ON r.`role_id`=u.`role_id` AND r.`code`='CUSTOMER'
      LEFT JOIN `customers` c ON c.`user_id` = u.`user_id`
      WHERE u.`user_id` IN ($in)
    ";
  }

  $stmt = $con->prepare($sql);
  if (!$stmt) {
    $response['result'] = false;
    $response['error']['msg']  = '쿼리 준비 실패';
    $response['error']['code'] = 500;
    return Finish();
  }
  $stmt->bind_param($types, ...$ids);
  $stmt->execute();
  $rs = $stmt->get_result();

  $customerIds = [];
  while ($r = $rs->fetch_assoc()) {
    if (!empty($r['cid'])) $customerIds[] = $r['cid'];
  }
  if (!count($customerIds)) {
    $response['result'] = false;
    $response['error']['msg']  = '변경할 고객 데이터가 없습니다.';
    $response['error']['code'] = 404;
    return Finish();
  }

  $con->begin_transaction();
  try {
    $in2 = implode(',', array_fill(0, count($customerIds), '?'));

    if ($new_vendor_id === null) {
      // 해제
      $types2 = str_repeat('s', count($customerIds));
      $sql2   = "UPDATE `customers` SET `vendor_id`=NULL WHERE `customer_id` IN ($in2)";
      $stmt2  = $con->prepare($sql2);
      if (!$stmt2) throw new RuntimeException('쿼리 준비 실패');
      $stmt2->bind_param($types2, ...$customerIds);
    } else {
      // 지정
      $types2 = 's' . str_repeat('s', count($customerIds));
      $sql2   = "UPDATE `customers` SET `vendor_id`=? WHERE `customer_id` IN ($in2)";
      $stmt2  = $con->prepare($sql2);
      if (!$stmt2) throw new RuntimeException('쿼리 준비 실패');
      $stmt2->bind_param($types2, $new_vendor_id, ...$customerIds);
    }

    if (!$stmt2->execute()) throw new RuntimeException('쿼리 실행 실패');
    $updated = $stmt2->affected_rows;

    $con->commit();
    $response['result'] = true; // ← boolean
    $response['item']['updated'] = $updated;
    $response['msg'] = ($new_vendor_id===null ? "밴더 해제" : "밴더 변경")." 완료 ({$updated}명)";
  } catch (Throwable $e) {
    $con->rollback();
    $response['result'] = false;
    $response['error']['msg']  = '처리 중 오류가 발생했습니다.';
    $response['error']['code'] = 500;
  }
  return Finish();
}

/* ===================== GET 렌더(본문만) ===================== */
?>
<div class="wrap">
  <div class="card">
    <div class="card-hd">
      <div>
        <div class="card-ttl">회원관리 (고객)</div>
        <div class="card-sub">밴더사 기준 고객 조회 및 일괄 소속 변경</div>
      </div>
      <div class="card-actions"></div>
    </div>

    <div class="card-bd">
      <div class="toolbar grid-4">
        <div class="field">
          <label class="label" for="srchVendor">밴더사</label>
          <select id="srchVendor" class="input"><option value="">전체</option></select>
        </div>
        <div class="field">
          <label class="label" for="srchName">회원이름</label>
          <input id="srchName" class="input" type="text" placeholder="이름 일부로 검색">
        </div>
        <div class="field">
          <label class="label">&nbsp;</label>
          <label class="chkline">
            <input id="onlyUnassigned" type="checkbox" class="checkbox">
            <span>소속되지 않은 회원만</span>
          </label>
        </div>
        <div class="field align-end">
          <button id="btnSearch" class="btn btn-outline">검색</button>
        </div>
      </div>

      <div class="table-wrap">
        <table id="grid" class="table">
          <thead>
            <tr>
              <th><input type="checkbox" id="chkAll" class="checkbox"></th>
              <th>회원ID</th>
              <th>이름</th>
              <th>이메일</th>
              <th>밴더사</th>
              <th>가입일</th>
              <th>상태</th>
            </tr>
          </thead>
          <tbody>
            <tr><td colspan="7" class="muted">검색 결과가 없습니다.</td></tr>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="7">
                <div class="stickybar">
                  <div class="flex-row">
                    <span class="notice">선택된 회원을 다음 밴더로 일괄 변경:</span>
                    <select id="bulkVendor" class="input"><option value="">밴더 해제</option></select>
                    <button id="btnBulkChange" class="btn btn-primary" disabled>밴더 일괄변경</button>
                  </div>
                  <span class="right muted" id="summary">0명 선택됨</span>
                </div>
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div><!-- /.card-bd -->
  </div><!-- /.card -->
</div><!-- /.wrap -->

<script>
  // 서버에서 내려준 페이지 토큰(라우터가 "<?= SRC ?>/{pageName}" 로 받음)
  var pageName = '<?= $pageName ?>';

  // x-www-form-urlencoded 로 POST (규약 준수)
  async function callAjax(action, payload = {}) {
    const params = new URLSearchParams();
    params.append('action', action);
    params.append('payload', JSON.stringify(payload));

    const res = await fetch("<?= SRC ?>/" + pageName, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-Requested-With': 'XMLHttpRequest'
      },
      credentials: 'include',
      body: params.toString()
    });
    if (!res.ok) throw new Error('Network error');

    const data = await res.json();

    // 성공 판정 완화
    const r = data && data.result;
    const ok = (r === true || r === 1 || r === 'ok' || r === 'OK' || r === 'success' || r === 'SUCCESS');
    if (!ok) {
      const msg = (data && data.error && data.error.msg) || data.msg || 'Server error';
      throw new Error(msg);
    }
    return data;
  }

  const el = {
    gridBody: document.querySelector('#grid tbody'),
    chkAll: document.getElementById('chkAll'),
    srchVendor: document.getElementById('srchVendor'),
    srchName: document.getElementById('srchName'),
    onlyUnassigned: document.getElementById('onlyUnassigned'),
    btnSearch: document.getElementById('btnSearch'),
    bulkVendor: document.getElementById('bulkVendor'),
    btnBulkChange: document.getElementById('btnBulkChange'),
    summary: document.getElementById('summary'),
  };

  let state = { rows: [], selection: new Set(), vendors: [] };

  function renderVendors() {
    const opts = ['<option value="">전체</option>']
      .concat(state.vendors.map(v => `<option value="${escapeHtml(v.vendor_id)}">${escapeHtml(v.name)}</option>`));
    el.srchVendor.innerHTML = opts.join('');
    const opts2 = ['<option value="">밴더 해제</option>']
      .concat(state.vendors.map(v => `<option value="${escapeHtml(v.vendor_id)}">${escapeHtml(v.name)}</option>`));
    el.bulkVendor.innerHTML = opts2.join('');
  }

  function renderRows() {
    if (!state.rows.length) {
      el.gridBody.innerHTML = `<tr><td colspan="7" class="muted">검색 결과가 없습니다.</td></tr>`;
      return;
    }
    const html = state.rows.map(r => `
      <tr data-id="${escapeHtml(r.user_id)}">
        <td><input type="checkbox" class="rowchk" ${state.selection.has(String(r.user_id)) ? 'checked':''}></td>
        <td>${escapeHtml(r.user_id)}</td>
        <td>${escapeHtml(r.name || '')}</td>
        <td class="muted">${escapeHtml(r.email || '')}</td>
        <td>${r.vendor_name ? `<span class="badge">${escapeHtml(r.vendor_name)}</span>` : '<span class="muted">미배정</span>'}</td>
        <td class="muted">${escapeHtml(r.created_at || '')}</td>
        <td>${r.is_active ? '<span class="status-on">활성</span>' : '<span class="status-off">중지</span>'}</td>
      </tr>
    `).join('');
    el.gridBody.innerHTML = html;
    bindRowChecks();
    syncSummary();
  }

  function bindRowChecks() {
    el.gridBody.querySelectorAll('.rowchk').forEach((chk, idx) => {
      const id = String(state.rows[idx].user_id);
      chk.addEventListener('change', () => {
        if (chk.checked) state.selection.add(id); else state.selection.delete(id);
        syncSummary();
      });
    });
  }

  function syncSummary() {
    const n = state.selection.size;
    el.summary.textContent = `${n}명 선택됨`;
    // '밴더 해제'도 허용하므로, 선택만 있으면 활성화
    el.btnBulkChange.disabled = !(n > 0);
  }

  function getFilters() {
    return {
      vendor_id: el.srchVendor.value || null,
      q: el.srchName.value?.trim() || null,
      only_unassigned: !!el.onlyUnassigned.checked
    };
  }

  function escapeHtml(s) {
    return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  }

  // 이벤트
  el.btnSearch.addEventListener('click', loadList);
  el.chkAll.addEventListener('change', () => {
    const all = el.gridBody.querySelectorAll('.rowchk');
    state.selection.clear();
    all.forEach((c, i) => { c.checked = el.chkAll.checked; if (c.checked) state.selection.add(String(state.rows[i].user_id)); });
    syncSummary();
  });
  el.bulkVendor.addEventListener('change', syncSummary);
  el.btnBulkChange.addEventListener('click', onBulkChange);

  // 초기화
  init().catch(err => alert(err.message || err));
  async function init() {
    const vres = await callAjax('HQ_VENDOR_LIST', {});
    state.vendors = vres.item?.vendors || [];
    renderVendors();
    await loadList();
  }

  async function loadList() {
    try {
      state.selection.clear();
      el.chkAll.checked = false;
      const res = await callAjax('HQ_CUSTOMER_LIST', getFilters());
      state.rows = res.item?.rows || [];
      renderRows();
    } catch (e) {
      alert(e.message || e);
    }
  }

  async function onBulkChange() {
    const ids = Array.from(state.selection);
    const v = el.bulkVendor.value; // "" = 해제
    if (!ids.length) { alert('선택된 회원이 없습니다.'); return; }
    if (!confirm(`선택한 ${ids.length}명의 소속을 ${v===''?'해제':'변경'}하시겠습니까?`)) return;

    try {
      await callAjax('HQ_CUSTOMER_BULK_VENDOR_CHANGE', { user_ids: ids, new_vendor_id: (v === '' ? null : v) });
      alert(v === '' ? '밴더 해제 완료' : '일괄변경 완료');
      await loadList();
    } catch (e) {
      alert(e.message || e);
    }
  }
</script>
