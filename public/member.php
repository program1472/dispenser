<?php
/****************************************************
 * dispenser/member.php (단일 파일: HTML + JS + PHP, 포털 스타일)
 * - 가입 타입: HQ / VENDOR / CUSTOMER
 * - 규칙: inc/common.php($con), $response + Finish(), decryptArrayRecursive()
 * - 컬럼 자동매핑: 테이블 컬럼 존재 여부를 조회해 INSERT 컬럼을 동적으로 구성
 ****************************************************/
require_once __DIR__ . "/inc/common.php"; // $con, $response, Finish(), decryptArrayRecursive()

/* ---------- 유틸 ---------- */
function j($v){ return mysqli_real_escape_string($GLOBALS['con'], (string)$v); }
function now(){ return date('Y-m-d H:i:s'); }
function make_id($prefix){ return $prefix . date('Ymd') . substr((string)mt_rand(1000,9999), -4); }

/* 컬럼 존재 캐시 */
$_TABLE_COLUMNS_CACHE = [];

/* 테이블 컬럼 목록 조회 */
function table_columns($table){
    global $_TABLE_COLUMNS_CACHE, $con;
    if (isset($_TABLE_COLUMNS_CACHE[$table])) return $_TABLE_COLUMNS_CACHE[$table];
    $cols = [];
    $res = mysqli_query($con, "SHOW COLUMNS FROM `{$table}`");
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $cols[$row['Field']] = $row;
        }
    }
    $_TABLE_COLUMNS_CACHE[$table] = $cols;
    return $cols;
}

/* 컬럼 존재 여부 */
function has_col($table, $col){
    $cols = table_columns($table);
    return isset($cols[$col]);
}

/* 값 → SQL 문자열(널이면 NULL) */
function sqlv($v){
    if ($v === null) return "NULL";
    return "'".j($v)."'";
}

/* 존재하는 컬럼만 골라 INSERT 수행 */
function insert_row($table, $assoc, &$debugSqlOut = null){
    global $con;
    $colsMeta = table_columns($table);
    if (!$colsMeta) throw new Exception("테이블 없음 또는 조회 실패: {$table}");

    $cols = [];
    $vals = [];
    foreach ($assoc as $k => $v) {
        if (!isset($colsMeta[$k])) continue; // 스키마에 없는 컬럼은 스킵
        $cols[] = "`{$k}`";
        $vals[] = sqlv($v);
    }
    if (empty($cols)) throw new Exception("{$table}에 매핑 가능한 컬럼이 없습니다.");
    $sql = "INSERT INTO `{$table}` (".implode(',', $cols).") VALUES (".implode(',', $vals).")";
    $debugSqlOut = $sql;
    if (!mysqli_query($con, $sql)) {
        throw new Exception("{$table} 생성 실패: ".mysqli_error($con));
    }
    return mysqli_insert_id($con);
}

/* roles.code 또는 role_name 보장 (새 스키마 대응) */
function require_role($code){
    global $con;
    $code = strtoupper($code);
    $rid = null;
    // 스키마에 따라 code 또는 role_name 컬럼 사용
    if (has_col('roles', 'code')) {
        $rs = mysqli_query($con, "SELECT role_id FROM roles WHERE code='".j($code)."' LIMIT 1");
    } else {
        $rs = mysqli_query($con, "SELECT role_id FROM roles WHERE role_name='".j($code)."' LIMIT 1");
    }
    if ($rs && ($r = mysqli_fetch_assoc($rs))) return (int)$r['role_id'];

    // 역할이 없으면 생성 (code 또는 role_name)
    if (has_col('roles', 'code')) {
        $sql = "INSERT INTO roles (code".(has_col('roles','name')?",name":"").(has_col('roles','created_at')?",created_at":"").") VALUES ('".j($code)."'".(has_col('roles','name')?(",'".j($code)."'"):"").(has_col('roles','created_at')?(",".sqlv(now())):"").")";
    } else {
        $sql = "INSERT INTO roles (role_name".(has_col('roles','created_at')?",created_at":"").") VALUES ('".j($code)."'".(has_col('roles','created_at')?(",".sqlv(now())):"").")";
    }
    if (!mysqli_query($con, $sql)) throw new Exception('역할 생성 실패: '.mysqli_error($con));
    return (int)mysqli_insert_id($con);
}

function commit_ok($msg, $extra=[]){
    global $response;
    mysqli_commit($GLOBALS['con']);
    $response['result']=true; $response['msg']=$msg; if($extra) $response['item']=$extra;
    Finish(); exit;
}
function fail_rollback($e){
    global $response;
    mysqli_rollback($GLOBALS['con']);
    $response['result']=false; $response['msg']=$e instanceof Throwable ? $e->getMessage() : (string)$e;
    $response['error']=['code'=>500,'msg'=>$response['msg']]; // 내부 에러는 로그로
    Finish(); exit;
}

/* ---------- 동의 이력 기록 유틸 ---------- */
function insert_user_consent($userId, $code, $agreed, $version, $ip, $ua){
    // user_consents 테이블이 있을 때만 기록
    if (!table_columns('user_consents')) return;
    $row = [
        'user_id'    => (int)$userId,
        'consent_code'=> strtoupper($code),
        'agreed'     => (int)$agreed,
        'version'    => $version ?: null,
        'agreed_at'  => now(),
        'ip'         => $ip ?: null,
        'user_agent' => $ua ?: null,
    ];
    $sqlC=''; insert_row('user_consents', $row, $sqlC);
    // 필요하면 디버그용 로그
    $GLOBALS['response']['data']['reg']['consent_'.$code] = $sqlC;
}

/* ---------- 입력 복호화 파이프 ---------- */
if (!empty($_GET))   { $_GET   = decryptArrayRecursive($_GET); }
$ctype = $_SERVER['CONTENT_TYPE'] ?? '';
if ($_SERVER['REQUEST_METHOD']==='POST'){
    if (stripos($ctype,'application/json')!==false){
        $raw = file_get_contents('php://input');
        $json = json_decode($raw,true);
        if (is_array($json)) $_POST = $json;
    }
    $_POST = decryptArrayRecursive($_POST);
}

/* ---------- POST 처리(가입) ---------- */
if ($_SERVER['REQUEST_METHOD']==='POST'){
    $response['data']['POST'] = $_POST;
    try{
        $con = $GLOBALS['con'];
        mysqli_begin_transaction($con);

        $type          = strtoupper(trim($_POST['type'] ?? 'CUSTOMER')); // HQ / VENDOR / CUSTOMER
        $userid        = trim($_POST['userid']        ?? '');
        $email         = trim($_POST['email']         ?? '');
        $password      = (string)($_POST['password']  ?? '');
        $password_re   = (string)($_POST['password_re'] ?? '');
        $wname         = trim($_POST['wname']         ?? '');
        $cname         = trim($_POST['cname']         ?? '');
        $saupja        = preg_replace('/\D/','', (string)($_POST['saupja'] ?? ''));
        $zipcode       = trim($_POST['zipcode']       ?? '');
        $address       = trim($_POST['address']       ?? '');
        $detailaddress = trim($_POST['detailaddress'] ?? '');
        $phone         = trim($_POST['phone']         ?? '');
        $hphone        = trim($_POST['hphone']        ?? '');
        $emailagree    = (int)($_POST['emailagree']   ?? 0);
        $smsagree      = (int)($_POST['smsagree']     ?? 0);

        // ▼ 신규: 약관 동의 값/버전
        $privacyagree  = (int)($_POST['privacyagree'] ?? 0);
        $termsagree    = (int)($_POST['termsagree']   ?? 0);
        $privacy_ver   = trim($_POST['privacy_version'] ?? '');
        $terms_ver     = trim($_POST['terms_version']   ?? '');

        $year          = trim($_POST['year']          ?? '');
        $month         = trim($_POST['month']         ?? '');
        $day           = trim($_POST['day']           ?? '');
        $sex           = trim($_POST['sex']           ?? '');
        $businessArr   = $_POST['business']           ?? [];
        $businessStr   = is_array($businessArr) ? implode(',', array_map('trim',$businessArr)) : trim((string)$businessArr);

        // 요청 메타(동의 이력에 사용)
        $req_ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $req_ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

        /* ----- 서버측 검증 ----- */
        if (!in_array($type, ['HQ','VENDOR','CUSTOMER'], true)) throw new Exception('가입 타입이 올바르지 않습니다.');
        if ($email==='' || !filter_var($email, FILTER_VALIDATE_EMAIL)) throw new Exception('유효한 이메일을 입력하세요.');
        if ($wname==='') throw new Exception('이름을 입력하세요.');
        if ($password==='' || $password!==$password_re) throw new Exception('비밀번호/확인을 확인하세요.');
        if ($type!=='HQ' && $cname==='') throw new Exception(($type==='VENDOR'?'업체명':'고객사/매장명').'을 입력하세요.');
        // ▼ 필수 동의 강제
        if (!$privacyagree) throw new Exception('개인정보 수집·이용(필수)에 동의해야 합니다.');
        if (!$termsagree)   throw new Exception('서비스 이용약관(필수)에 동의해야 합니다.');

        // 이메일 중복
        $du = mysqli_query($con, "SELECT user_id FROM users WHERE email='".j($email)."' LIMIT 1");
        if ($du && mysqli_fetch_assoc($du)) throw new Exception('이미 사용 중인 이메일입니다.');

        $roleId = require_role($type);

        /* ----- 새 스키마: USERS 먼저 생성 (vendor_id, customer_id 컬럼 제거됨) ----- */
        $pwd = password_hash($password, PASSWORD_BCRYPT);
        $userRow = [
            'email'         => $email,
            'password_hash' => $pwd,
            'name'          => $wname,
            'phone'         => has_col('users','phone') ? ($hphone?:$phone) : null,
            'role_id'       => $roleId,
            'is_active'     => has_col('users','is_active') ? 1 : null,
            'last_login'    => has_col('users','last_login') ? null : null,
            'created_at'    => has_col('users','created_at') ? now() : null,
        ];
        $sqlU=''; $uid = insert_row('users', $userRow, $sqlU);
        $response['data']['reg']['sqlU'] = $sqlU;

        $extra_create = ['user_id'=>$uid];
        $vendorId = null;
        $customerId = null;

        /* ----- VENDOR 생성 (user_id FK로 연결) ----- */
        if ($type==='VENDOR'){
            $addr_json = ['zipcode'=>$zipcode,'address'=>$address,'detail'=>$detailaddress];
            $vendorRow = [
                'user_id'      => $uid,  // FK to users (새 스키마)
                'company_name' => has_col('vendors','company_name') ? $cname : null,
                'name'         => has_col('vendors','name') ? $cname : null,  // 호환성
                'biz_no'       => has_col('vendors','biz_no') ? $saupja : null,
                'phone'        => has_col('vendors','phone') ? ($phone?:$hphone) : null,
                'email'        => has_col('vendors','email') ? $email : null,
                'address_json' => has_col('vendors','address_json') ? json_encode($addr_json, JSON_UNESCAPED_UNICODE) : null,
                'created_at'   => has_col('vendors','created_at') ? now() : null,
                'bank_account' => has_col('vendors','bank_account') ? '' : null,
                'default_commission_pct' => has_col('vendors','default_commission_pct') ? '40.00' : null,
                'incentive_pct' => has_col('vendors','incentive_pct') ? '5.00' : null,
                'qualification_min_units' => has_col('vendors','qualification_min_units') ? 500 : null,
            ];
            $sqlV=''; $vendorId = insert_row('vendors', $vendorRow, $sqlV);
            $response['data']['reg']['sqlV'] = $sqlV;
            $extra_create['vendor_id'] = $vendorId;
        }

        /* ----- CUSTOMER 생성 (user_id FK로 연결) ----- */
        if ($type==='CUSTOMER'){
            $contact = [
                'name'=>$wname, 'phone'=>$hphone?:$phone, 'email'=>$email,
                'zipcode'=>$zipcode, 'address'=>$address, 'detail'=>$detailaddress, 'saupja'=>$saupja
            ];
            $customerRow = [
                'user_id'         => $uid,  // FK to users (새 스키마)
                'company_name'    => has_col('customers','company_name') ? $cname : null,
                'name'            => has_col('customers','name') ? $cname : null,  // 호환성
                'category'        => has_col('customers','category') ? ($businessStr!=='' ? explode(',',$businessStr)[0] : '기타') : null,
                'contract_state'  => has_col('customers','contract_state') ? 'ACTIVE' : null,
                'biz_no'          => has_col('customers','biz_no') ? $saupja : null,
                'billing_contact' => has_col('customers','billing_contact') ? json_encode($contact, JSON_UNESCAPED_UNICODE) : null,
                'shipping_contact'=> has_col('customers','shipping_contact') ? json_encode($contact, JSON_UNESCAPED_UNICODE) : null,
                'created_at'      => has_col('customers','created_at') ? now() : null,
            ];
            $sqlC=''; $customerId = insert_row('customers', $customerRow, $sqlC);
            $response['data']['reg']['sqlC'] = $sqlC;
            $extra_create['customer_id'] = $customerId;

            // 주소가 있으면 기본 사이트 (customer_sites 테이블)
            if (($address!=='' || $detailaddress!=='') && table_columns('customer_sites')){
                $siteRow = [
                    'customer_id' => $customerId,
                    'site_name'   => has_col('customer_sites','site_name') ? '본점' : null,
                    'address'     => has_col('customer_sites','address') ? $address : null,
                    'address_detail' => has_col('customer_sites','address_detail') ? $detailaddress : null,
                    'contact_name'   => has_col('customer_sites','contact_name') ? $wname : null,
                    'contact_phone'  => has_col('customer_sites','contact_phone') ? ($hphone?:$phone) : null,
                    'created_at'  => has_col('customer_sites','created_at') ? now() : null,
                ];
                $sqlS=''; $siteId = insert_row('customer_sites', $siteRow, $sqlS);
                $response['data']['reg']['sqlS'] = $sqlS;
                $extra_create['site_id'] = $siteId;
            }
        }

        /* ----- USER_EXTRA (캐시 + 부가정보 + 동의 캐시) ----- */
        if (table_columns('user_extra')){
            $birth = null;
            if ($year!=='' && $month!=='' && $day!==''){
                $mm = str_pad(preg_replace('/\D/','',$month),2,'0',STR_PAD_LEFT);
                $dd = str_pad(preg_replace('/\D/','',$day),2,'0',STR_PAD_LEFT);
                $birth = sprintf('%04d-%s-%s', (int)$year, $mm, $dd);
            }
            $extraRow = [
                'user_id'         => $uid,
                'userid'          => has_col('user_extra','userid') ? ($userid!==''?$userid:null) : null,
                'birth_date'      => has_col('user_extra','birth_date') ? $birth : null,
                'sex'             => has_col('user_extra','sex') ? ($sex!==''?$sex:null) : null,
                'email_agree'     => has_col('user_extra','email_agree') ? (int)$emailagree : null,
                'sms_agree'       => has_col('user_extra','sms_agree') ? (int)$smsagree : null,

                // ▼ 신규: 약관 동의 캐시 필드 (존재 시에만)
                'privacy_agree'     => has_col('user_extra','privacy_agree') ? (int)$privacyagree : null,
                'terms_agree'       => has_col('user_extra','terms_agree')   ? (int)$termsagree   : null,
                'privacy_version'   => has_col('user_extra','privacy_version')? ($privacy_ver ?: null) : null,
                'terms_version'     => has_col('user_extra','terms_version')  ? ($terms_ver   ?: null) : null,
                'privacy_agreed_at' => has_col('user_extra','privacy_agreed_at') ? now() : null,
                'terms_agreed_at'   => has_col('user_extra','terms_agreed_at')   ? now() : null,

                'zipcode'         => has_col('user_extra','zipcode') ? $zipcode : null,
                'address'         => has_col('user_extra','address') ? $address : null,
                'detailaddress'   => has_col('user_extra','detailaddress') ? $detailaddress : null,
                'business_tags'   => has_col('user_extra','business_tags') ? $businessStr : null,
                'created_at'      => has_col('user_extra','created_at') ? now() : null,
            ];
            $sqlE=''; insert_row('user_extra', $extraRow, $sqlE);
            $response['data']['reg']['sqlE'] = $sqlE;
        }

        /* ----- 동의 이력(user_consents) 누적 기록 ----- */
        insert_user_consent($uid, 'PRIVACY', $privacyagree, $privacy_ver, $req_ip, $req_ua);
        insert_user_consent($uid, 'TERMS',   $termsagree,   $terms_ver,   $req_ip, $req_ua);
        insert_user_consent($uid, 'EMAIL',   $emailagree,   null,         $req_ip, $req_ua);
        insert_user_consent($uid, 'SMS',     $smsagree,     null,         $req_ip, $req_ua);

        commit_ok('회원가입이 완료되었습니다.', array_merge(['user_id'=>$uid,'type'=>$type], $extra_create));
    } catch(Throwable $e){
        fail_rollback($e);
    }
    exit;
}
?>

<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<title>올투그린 디스펜서 — 회원가입</title>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script>
<style>
/* ===== Design Tokens ===== */
:root{
  --border:#e5e7eb; --muted:#6b7280; --fg:#0f172a; --bg:#f8fafc; --white:#fff;
  --accent:#047857; --accent-weak:#ecfdf5; --warn:#dc2626; --ok:#065f46;
  --ring:#93c5fd; --radius:14px; --radius-sm:12px; --shadow:0 10px 30px rgba(0,0,0,.04);
}
@media (prefers-color-scheme: dark){
  :root{ --border:#334155; --muted:#94a3b8; --fg:#e5e7eb; --bg:#0b1220; --white:#0f172a; --accent:#10b981; --accent-weak:#0b2a22; --warn:#ef4444; --ok:#34d399; --ring:#38bdf8; }
}

/* ===== Base ===== */
*{box-sizing:border-box}
html,body{height:100%}
body{
  margin:0;background:var(--bg);color:var(--fg);
  font-family:ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,'Noto Sans KR',Arial;
  -webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;
  line-height:1.5;
}
a{color:var(--accent);text-decoration:none}
button{font:inherit}

/* ===== Layout ===== */
.wrap{ padding:16px;display:grid;gap:16px;max-width:960px;margin:0 auto; }
.card{ background:var(--white);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow) }
.card-hd{ padding:12px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap; }
.card-hd-left{display:flex;align-items:center;gap:8px}
.card-ttl{font-weight:800;color:var(--accent);font-size:18px}
.card-bd{padding:14px}

/* Header actions */
.actions{display:flex;gap:8px;align-items:center}
.linkbtn{
  display:inline-flex;align-items:center;gap:8px;
  padding:10px 14px;border-radius:999px;border:1px solid var(--border);
  background:var(--white);color:var(--accent);font-weight:700;
}
.linkbtn:focus-visible{outline:3px solid var(--ring);outline-offset:2px}

/* ===== GroupBox ===== */
.group{
  border:1px solid var(--border);
  border-radius:12px;
  background:#fff;
  margin:14px 0;
  overflow:hidden;
}
@media (prefers-color-scheme: dark){ .group{background:#0f172a} }
.group-hd{
  padding:10px 14px;
  background:var(--accent-weak);
  display:flex;align-items:center;gap:8px;
  border-bottom:1px solid var(--border);
}
.group-ttl{font-weight:800;color:#065f46}
.group-bd{padding:12px 14px}

/* ===== Form ===== */
.form-row{ display:grid;grid-template-columns:1fr;gap:8px;align-items:center;margin:10px 0; }
label{color:#111827;font-weight:700;font-size:14px}
@media (prefers-color-scheme: dark){ label{color:#e2e8f0} }

input[type="text"],input[type="password"],input[type="tel"],input[type="email"]{
  width:100%;padding:12px 14px;border-radius:var(--radius-sm);
  border:1px solid var(--border);background:#fff;color:#111827;font-size:15px;outline:none;
}
@media (prefers-color-scheme: dark){
  input[type="text"],input[type="password"],input[type="tel"],input[type="email"]{ background:#0b1220;color:#e5e7eb; }
}
input:focus{border-color:var(--ring);box-shadow:0 0 0 4px color-mix(in srgb, var(--ring) 35%, transparent)}
.input-inline{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
.note{color:var(--muted);font-size:12px}
.checkbox-set{display:flex;gap:12px;flex-wrap:wrap}
.small{font-size:12px;color:var(--muted)}

/* Buttons */
.btn{ padding:12px 16px;border-radius:12px;border:1px solid #059669;background:var(--accent);color:#fff;font-weight:800;cursor:pointer;min-width:120px;text-align:center; }
.btn:disabled{opacity:.6;cursor:not-allowed}
.btn.secondary{background:#fff;border-color:var(--border);color:var(--accent)}
.btn.full{width:100%}

/* Center helper */
.center{display:flex;justify-content:center;align-items:center}

/* Status */
#msg{margin-top:10px;font-size:14px;text-align:center}
#msg.ok{color:var(--ok)}
#msg.err{color:var(--warn)}

/* Badges */
.badge{ display:inline-block;padding:4px 12px;border:1px solid var(--border);border-radius:999px;font-size:12px;color:var(--accent);background:var(--accent-weak) }

/* ===== Dialog (약관보기) ===== */
dialog.modal{
  width:min(880px, 92vw); border:1px solid var(--border); border-radius:16px; padding:0; box-shadow:var(--shadow); background:var(--white); color:var(--fg);
}
.modal-hd{display:flex;align-items:center;justify-content:space-between;gap:8px;padding:12px 16px;border-bottom:1px solid var(--border)}
.modal-ttl{font-weight:800}
.modal-bd{padding:16px;max-height:min(70vh, 640px);overflow:auto}
.modal-ft{display:flex;justify-content:flex-end;gap:8px;padding:12px 16px;border-top:1px solid var(--border)}
.modal-close{padding:8px 12px;border-radius:10px;border:1px solid var(--border);background:#fff;color:var(--fg);cursor:pointer}
.modal-open{color:var(--accent);text-decoration:underline;cursor:pointer}

/* ===== Responsive Tweaks ===== */
@media (min-width:640px){
  .card-bd{padding:18px}
  .card-ttl{font-size:20px}
  .group-bd .form-row{grid-template-columns:220px 1fr}
}
@media (min-width:768px){
  .wrap{padding:24px}
  .card-bd{padding:20px}
}
@media (prefers-reduced-motion: reduce){ *{transition:none !important;animation:none !important} }
</style>
<script>
function execDaumPostcode() {
  new daum.Postcode({
    oncomplete: function(data){
      var addr = (data.userSelectedType === 'R') ? data.roadAddress : data.jibunAddress;
      document.getElementById('zipcode').value = data.zonecode;
      document.getElementById('address').value = addr;
      document.getElementById('detailaddress').focus();
    }
  }).open();
}
</script>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <div class="card-hd">
        <div class="card-hd-left">
          <div class="card-ttl">회원가입</div>
          <div class="badge">HQ / VENDOR / CUSTOMER</div>
        </div>
        <div class="actions">
          <a href="login.php" class="linkbtn" aria-label="로그인 페이지로 이동">← 로그인으로 돌아가기</a>
        </div>
      </div>

      <div class="card-bd">

        <!-- 가입 타입 -->
        <section class="group" aria-labelledby="grp-type">
          <div class="group-hd"><div class="group-ttl" id="grp-type">가입 타입</div></div>
          <div class="group-bd">
            <div class="form-row" role="group" aria-label="가입 유형">
              <label>가입 유형 *</label>
              <div>
                <label><input type="radio" name="type" value="HQ" checked> 본사(HQ)</label>&nbsp;&nbsp;
                <label><input type="radio" name="type" value="VENDOR"> 밴더(VENDOR)</label>&nbsp;&nbsp;
                <label><input type="radio" name="type" value="CUSTOMER"> 고객(CUSTOMER)</label>
              </div>
            </div>
          </div>
        </section>

        <!-- 계정 정보 -->
        <section class="group" aria-labelledby="grp-account">
          <div class="group-hd"><div class="group-ttl" id="grp-account">계정 정보</div></div>
          <div class="group-bd">
            <div class="form-row"><label for="userid">아이디</label><input id="userid" placeholder="선택 (별칭/로그인ID)"></div>
            <div class="form-row"><label for="email">이메일 *</label><input id="email" type="email" placeholder="you@example.com" inputmode="email"></div>
            <div class="form-row"><label for="password">비밀번호 *</label><input id="password" type="password" placeholder="영문+숫자 조합 권장"></div>
            <div class="form-row"><label for="password_re">비밀번호 확인 *</label><input id="password_re" type="password"></div>
          </div>
        </section>

        <!-- 사용자 정보 -->
        <section class="group" aria-labelledby="grp-user">
          <div class="group-hd"><div class="group-ttl" id="grp-user">사용자 정보</div></div>
          <div class="group-bd">
            <div class="form-row"><label for="wname">이름 *</label><input id="wname" placeholder="홍길동" autocomplete="name"></div>
            <div class="form-row"><label for="cname">업체/고객사명</label><input id="cname" placeholder="VENDOR/CUSTOMER에서 필수"></div>
            <div class="form-row"><label for="saupja">사업자번호</label><input id="saupja" placeholder="숫자만" inputmode="numeric"></div>
            <div class="form-row"><label for="phone">회사전화</label><input id="phone" placeholder="" inputmode="tel" autocomplete="tel"></div>
            <div class="form-row"><label for="hphone">휴대폰</label><input id="hphone" placeholder="" inputmode="tel" autocomplete="tel-national"></div>
          </div>
        </section>

        <!-- 주소 -->
        <section class="group" aria-labelledby="grp-address">
          <div class="group-hd"><div class="group-ttl" id="grp-address">주소</div></div>
          <div class="group-bd">
            <div class="form-row">
              <label for="zipcode">우편번호</label>
              <div class="input-inline">
                <input id="zipcode" placeholder="우편번호" style="max-width:180px">
                <button class="btn secondary" type="button" onclick="execDaumPostcode()">주소찾기</button>
              </div>
            </div>
            <div class="form-row"><label for="address">주소</label><input id="address" placeholder=""></div>
            <div class="form-row"><label for="detailaddress">상세주소</label><input id="detailaddress" placeholder=""></div>
            <div class="form-row"><label></label><div class="small">CUSTOMER는 주소가 있으면 기본 사업장(본점)이 같이 생성됩니다.</div></div>
          </div>
        </section>

        <!-- 수신/약관 동의 -->
        <section class="group" aria-labelledby="grp-consent">
          <div class="group-hd"><div class="group-ttl" id="grp-consent">수신/약관 동의</div></div>
          <div class="group-bd">
            <div class="form-row">
              <label>동의 항목</label>
              <div>
                <div class="checkbox-set">
                  <label><input type="checkbox" id="emailagree" value="1"> 이메일 수신</label>
                  <label><input type="checkbox" id="smsagree" value="1"> 문자 수신</label>
                </div>
                <div class="checkbox-set" style="margin-top:8px">
                  <label>
                    <input type="checkbox" id="privacyagree" value="1" required>
                    개인정보 수집·이용 동의(필수)
                    <button type="button" class="modal-open" data-target="dlg-privacy">보기</button>
                  </label>
                  <label>
                    <input type="checkbox" id="termsagree" value="1" required>
                    서비스 이용약관 동의(필수)
                    <button type="button" class="modal-open" data-target="dlg-terms">보기</button>
                  </label>
                </div>
                <div class="small" style="margin-top:6px">필수 항목 미동의 시 가입이 제한됩니다.</div>
              </div>
            </div>
          </div>
        </section>

        <!-- 추가 정보 -->
        <section class="group" aria-labelledby="grp-extra">
          <div class="group-hd"><div class="group-ttl" id="grp-extra">추가 정보</div></div>
          <div class="group-bd">
            <div class="form-row">
              <label>생년월일</label>
              <div class="input-inline">
                <input id="year" placeholder="1990" style="max-width:120px" inputmode="numeric">
                <input id="month" placeholder="01" style="max-width:90px" inputmode="numeric">
                <input id="day" placeholder="07" style="max-width:90px" inputmode="numeric">
              </div>
            </div>
            <div class="form-row" role="group" aria-label="성별">
              <label>성별</label>
              <div class="checkbox-set">
                <label><input type="radio" name="sex" value="1"> 남</label>
                <label><input type="radio" name="sex" value="2"> 여</label>
              </div>
            </div>
            <div class="form-row" role="group" aria-label="사업분야">
              <label>사업분야</label>
              <div class="checkbox-set">
                <label><input type="checkbox" name="business" value="공방"> 공방</label>
                <label><input type="checkbox" name="business" value="디퓨저"> 디퓨저</label>
                <label><input type="checkbox" name="business" value="캔들"> 캔들</label>
                <label><input type="checkbox" name="business" value="인센스"> 인센스</label>
                <label><input type="checkbox" name="business" value="룸스프레이"> 룸스프레이</label>
                <label><input type="checkbox" name="business" value="드레스퍼퓸"> 드레스퍼퓸</label>
                <label><input type="checkbox" name="business" value="석고"> 석고</label>
              </div>
            </div>
          </div>
        </section>

        <!-- 버튼 (가운데 정렬) -->
        <div class="form-row">
          <label></label>
          <div class="center" style="gap:12px;flex-wrap:wrap">
            <button class="btn" id="btnJoin">가입하기</button>
          </div>
        </div>
        <div id="msg"></div>

        <div class="form-row">
          <label></label>
          <div class="small">※ 전송값은 서버에서 decryptArrayRecursive()로 복호화되어 처리됩니다.</div>
        </div>

      </div>
    </div>
  </div>

  <!-- 개인정보 수집동의 (요약본) -->
  <dialog id="dlg-privacy" class="modal">
    <div class="modal-hd">
      <div class="modal-ttl">개인정보 수집·이용 동의 (v1.0)</div>
      <button class="modal-close" data-close="dlg-privacy">닫기</button>
    </div>
    <div class="modal-bd">
      <p><strong>수집항목</strong> : 이메일, 비밀번호, 이름, 가입 유형 및 업체/고객사 정보, 연락처(전화/휴대폰), 주소, 생년월일·성별(선택), 수신 동의내역(IP·일시 포함)</p>
      <p><strong>수집·이용목적</strong> : 회원 식별·인증, 서비스 제공 및 계약 이행, 고객지원, 고지·안내 전송, 법령 및 약관 위반 방지, 서비스 품질 개선</p>
      <p><strong>보유·이용기간</strong> : 회원 탈퇴 시까지 또는 관련 법령에 따른 보관기간(전자상거래 등에서의 소비자보호에 관한 법률 등) 동안</p>
      <p><strong>동의 거부권</strong> : 필수정보 제공 및 동의 거부 시 서비스 가입/이용이 제한될 수 있습니다.</p>
      <hr>
      <p class="small">※ 네이버 회원가입 동의 화면 및 개인정보 처리방침 형식을 참고해 당사 서비스에 맞추어 요약 작성되었습니다.</p>
    </div>
    <div class="modal-ft">
      <button class="btn secondary modal-close" data-close="dlg-privacy">닫기</button>
    </div>
  </dialog>

  <!-- 서비스 이용약관 (요약본) -->
  <dialog id="dlg-terms" class="modal">
    <div class="modal-hd">
      <div class="modal-ttl">올투그린 디스펜서 서비스 이용약관 (v1.0)</div>
      <button class="modal-close" data-close="dlg-terms">닫기</button>
    </div>
    <div class="modal-bd">
      <ol>
        <li><strong>목적</strong> : 디스펜서 관련 서비스의 이용조건 및 절차, 회사와 회원의 권리·의무를 규정합니다.</li>
        <li><strong>회원계정</strong> : 회원은 정확한 정보를 제공해야 하며, 계정 보안은 회원의 책임입니다. 위법·부정 사용이 확인되면 이용이 제한될 수 있습니다.</li>
        <li><strong>서비스 제공</strong> : 설비/자재 관리, 주문·정산, 알림, 포털 접속 등. 회사는 운영상·기술상 사유로 서비스 내용을 변경할 수 있으며, 사전 공지합니다.</li>
        <li><strong>이용제한</strong> : 약관·법령 위반, 타인의 권리 침해, 서비스 또는 시스템의 안정성 저해 행위에 대해 이용을 제한하거나 계약을 해지할 수 있습니다.</li>
        <li><strong>지식재산권</strong> : 서비스 및 제공 콘텐츠의 권리는 회사에 귀속됩니다. 회원은 사전 허가 없이 복제·배포·변형할 수 없습니다.</li>
        <li><strong>개인정보보호</strong> : 개인정보 처리에 관한 사항은 개인정보 처리방침에 따릅니다.</li>
        <li><strong>고지 및 통지</strong> : 서비스 내 게시, 이메일, 문자 등으로 통지할 수 있습니다.</li>
        <li><strong>책임의 한계</strong> : 회사는 천재지변, 불가항력, 회원 귀책으로 인한 손해에 대해 책임을 지지 않습니다. 법령이 허용하는 범위 내에서 간접·특별손해는 책임을 제한합니다.</li>
        <li><strong>분쟁해결</strong> : 분쟁은 대한민국 법령을 따르며, 관할은 회사 소재지 관할 법원으로 합니다.</li>
        <li><strong>부칙</strong> : 본 약관은 2025-10-09부터 시행합니다. 중요한 변경 시 시행 7일 전 공지합니다.</li>
      </ol>
      <hr>
      <p class="small">※ 네이버 약관 UI/구성(참고 URL)을 참고해 당사 서비스 실정에 맞게 요약·정리했습니다.</p>
    </div>
    <div class="modal-ft">
      <button class="btn secondary modal-close" data-close="dlg-terms">닫기</button>
    </div>
  </dialog>

<script>
	/* 약관 보기/닫기 */
	(function(){
	  function openDialog(id){ const d=document.getElementById(id); if(d && typeof d.showModal==='function'){ d.showModal(); } }
	  function closeDialog(id){ const d=document.getElementById(id); if(d && typeof d.close==='function'){ d.close(); } }

	  document.addEventListener('click', function(e){
		var t = e.target;
		if(t.classList.contains('modal-open')){
		  e.preventDefault();
		  openDialog(t.getAttribute('data-target'));
		}
		if(t.classList.contains('modal-close')){
		  e.preventDefault();
		  closeDialog(t.getAttribute('data-close'));
		}
	  });
	})();
	</script>

	<!-- 전송 스크립트 (동일, 버튼 중앙 정렬과 무관) -->
	<script>
	(function($){
	  const TERMS_VERSION = 'v1.0';
	  const PRIVACY_VERSION = 'v1.0';

	  function v(id){ return $.trim($(id).val()||''); }
	  function getType(){ return $('input[name="type"]:checked').val(); }
	  function getBusinesses(){ var a=[]; $('input[name="business"]:checked').each(function(){a.push($(this).val());}); return a; }
	  function showMsg(ok, t){ $('#msg').removeClass('ok err').addClass(ok?'ok':'err').text(t); }

	  $('#saupja,#phone,#hphone,#year,#month,#day,#zipcode').on('input', function(){ this.value=this.value.replace(/[^0-9]/g,''); });

	  $('#btnJoin').on('click', function(e){
		e.preventDefault();
		var payload = {
		  type:getType(), userid:v('#userid'), email:v('#email'),
		  password:v('#password'), password_re:v('#password_re'),
		  wname:v('#wname'), cname:v('#cname'), saupja:v('#saupja'),
		  zipcode:v('#zipcode'), address:v('#address'), detailaddress:v('#detailaddress'),
		  phone:v('#phone'), hphone:v('#hphone'),
		  emailagree: $('#emailagree').is(':checked')?1:0,
		  smsagree: $('#smsagree').is(':checked')?1:0,
		  privacyagree: $('#privacyagree').is(':checked')?1:0,
		  termsagree: $('#termsagree').is(':checked')?1:0,
		  privacy_version: PRIVACY_VERSION,
		  terms_version: TERMS_VERSION,
		  year:v('#year'), month:v('#month'), day:v('#day'),
		  sex:$('input[name="sex"]:checked').val()||'',
		  business:getBusinesses()
		};

		if(!payload.email){ showMsg(false,'이메일을 입력하세요.'); return; }
		if(!payload.password || !payload.password_re){ showMsg(false,'비밀번호/확인을 입력하세요.'); return; }
		if(payload.password!==payload.password_re){ showMsg(false,'비밀번호가 일치하지 않습니다.'); return; }
		if(!payload.wname){ showMsg(false,'이름을 입력하세요.'); return; }
		if(payload.type!=='HQ' && !payload.cname){ showMsg(false, (payload.type==='VENDOR'?'업체명':'고객사/매장명')+'을 입력하세요.'); return; }
		if(!payload.privacyagree){ showMsg(false,'개인정보 수집·이용에 동의가 필요합니다.'); return; }
		if(!payload.termsagree){ showMsg(false,'서비스 이용약관에 동의가 필요합니다.'); return; }

		$('#btnJoin').prop('disabled', true);
		$.ajax({
		  url: location.href, method:'POST',
		  contentType:'application/json; charset=utf-8',
		  data: JSON.stringify(payload), dataType:'json'
		}).done(function(res){
		  console.log(res);
		  showMsg(!!(res&&res.result), (res&&res.msg)?res.msg:'오류가 발생했습니다.');
		  if (res && res.result) {
			setTimeout(function(){ window.location.href = 'login.php'; }, 800);
			return;
		  }
		}).fail(function(){ showMsg(false,'네트워크 오류가 발생했습니다.'); })
		  .always(function(){ $('#btnJoin').prop('disabled', false); });
	  });
	})(jQuery);
</script>
</body>
</html>
