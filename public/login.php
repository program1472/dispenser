<?php
	/****************************************************
	 * dispenser/login.php (단일 파일: HTML + JS + PHP)
	 * - 스키마 호환: 컬럼 존재 여부 동적 처리
	 * - 로그인 키 선택: 이메일 / 아이디(user_extra.userid 또는 users.login_id/users.userid)
	 * - 타입별 라우팅: HQ / VENDOR / CUSTOMER → index 로딩 후 doc/{role}/index.* AJAX 로드
	 * - 출력 데이터: encryptValue()로 암호화 (있으면), 입력 데이터: decryptValue()/decryptArrayRecursive() 처리
	 * - 공통 규칙: inc/common.php($con), $response + Finish(), decryptArrayRecursive()
	 ****************************************************/
	require_once __DIR__ . "/inc/common.php"; // $con, $response, Finish(), decryptArrayRecursive()

	/* ---------- 유틸 & 호환 ---------- */
	function j($v){ return mysqli_real_escape_string($GLOBALS['con'], (string)$v); }

	/* 입력 복호화 (decryptArrayRecursive 선호, 없으면 decryptValue로 키/값 복호화) */
	function smart_decrypt_input($arr){
		if (function_exists('decryptArrayRecursive')) return decryptArrayRecursive($arr);
		if (function_exists('decryptValue')){
			$out = [];
			foreach($arr as $k=>$v){
				$kk = is_string($k)? decryptValue($k) : $k;
				if (is_array($v)) $out[$kk] = smart_decrypt_input($v);
				else $out[$kk] = is_string($v)? decryptValue($v) : $v;
			}
			return $out;
		}
		return $arr;
	}

	/* 출력 암호화 (encryptValue가 있으면 모든 스칼라 값 재귀 암호화) */
	function enc($v){
		if (!function_exists('encryptValue')) return $v;
		if (is_array($v)){
			$o=[]; foreach($v as $k=>$vv){ $o[ is_string($k)? encryptValue($k) : $k ] = enc($vv); } return $o;
		}
		if (is_object($v)) return enc((array)$v);
		return is_string($v) || is_numeric($v) || is_bool($v) || $v===null ? encryptValue((string)$v) : $v;
	}

	/* 컬럼 캐시 */
	$_TABLE_COLUMNS_CACHE = [];
	function table_columns($table){
		global $_TABLE_COLUMNS_CACHE, $con;
		if (isset($_TABLE_COLUMNS_CACHE[$table])) return $_TABLE_COLUMNS_CACHE[$table];
		$cols = [];
		$res = @mysqli_query($con, "SHOW COLUMNS FROM `{$table}`");
		if ($res) while ($row = mysqli_fetch_assoc($res)) $cols[$row['Field']] = $row;
		$_TABLE_COLUMNS_CACHE[$table] = $cols;
		return $cols;
	}
	function has_col($table, $col){ $cols = table_columns($table); return isset($cols[$col]); }

	/* 사용자 조회(이메일 또는 아이디) */
	/* 사용자 조회(이메일 또는 아이디) — v3 스키마 대응: 항상 user_extra.userid 포함 */
	function find_user($identifier, $mode){ // $mode: email | id
		global $con;
		$identifier = trim($identifier);

		// user_extra.userid 지원 여부
		$hasUx = table_columns('user_extra') && has_col('user_extra','userid');
		$select_userid = $hasUx ? "ux.userid AS userid," : "NULL AS userid,";
		$join_userid   = $hasUx ? " LEFT JOIN user_extra ux ON ux.user_id = u.user_id " : "";

		$select = "
			u.user_id, u.email, u.password_hash, u.name,
			".(has_col('users','phone')?'u.phone':'NULL AS phone').",
			u.role_id,
			".(has_col('users','vendor_id')?'u.vendor_id':'NULL AS vendor_id').",
			".(has_col('users','customer_id')?'u.customer_id':'NULL AS customer_id').",
			".(has_col('users','is_active')?'u.is_active':'1 AS is_active').",
			{$select_userid}
			r.code AS role_code, r.name AS role_name
		";

		if ($mode === 'email') {
			// 이메일로 찾기 (항상 ux LEFT JOIN)
			$sql = "SELECT {$select}
					FROM users u
					JOIN roles r ON r.role_id = u.role_id
					{$join_userid}
					WHERE u.email = '".j($identifier)."'
					LIMIT 1";
			$rs  = mysqli_query($con, $sql);
			if ($rs && ($u = mysqli_fetch_assoc($rs))) return $u;
			return null;
		}

		/* 아이디 모드: users.login_id → users.userid → user_extra.userid (스키마 호환)
		   - v3에는 users.login_id/users.userid 없음. 마지막 단계(ux.userid)에서 매칭됨. */
		if (has_col('users','login_id')) {
			$sql = "SELECT {$select}
					FROM users u
					JOIN roles r ON r.role_id = u.role_id
					{$join_userid}
					WHERE u.login_id = '".j($identifier)."'
					LIMIT 1";
			$rs = mysqli_query($con, $sql);
			if ($rs && ($u = mysqli_fetch_assoc($rs))) return $u;
		}
		if (has_col('users','userid')) {
			$sql = "SELECT {$select}
					FROM users u
					JOIN roles r ON r.role_id = u.role_id
					{$join_userid}
					WHERE u.userid = '".j($identifier)."'
					LIMIT 1";
			$rs = mysqli_query($con, $sql);
			if ($rs && ($u = mysqli_fetch_assoc($rs))) return $u;
		}
		if ($hasUx) {
			$sql = "SELECT {$select}
					FROM users u
					JOIN roles r ON r.role_id = u.role_id
					LEFT JOIN user_extra ux ON ux.user_id = u.user_id
					WHERE ux.userid = '".j($identifier)."'
					LIMIT 1";
			$rs = mysqli_query($con, $sql);
			if ($rs && ($u = mysqli_fetch_assoc($rs))) return $u;
		}
		return null;
	}

	/* 감사로그 (존재 시) */
	function write_audit($user_id, $action, $target='users', $payloadArr=[]){
		global $con;
		if (!table_columns('audit_log')) return;
		$cols=[]; $vals=[];
		if (has_col('audit_log','actor_user_id')) { $cols[]='actor_user_id'; $vals[]=(string)((int)$user_id); }
		$cols[]='action'; $vals[]="'".j($action)."'";
		if (has_col('audit_log','target'))  { $cols[]='target';  $vals[]="'".j($target)."'"; }
		if (has_col('audit_log','payload')) { $cols[]='payload'; $vals[]="'".j(json_encode($payloadArr, JSON_UNESCAPED_UNICODE))."'"; }
		$sql = "INSERT INTO audit_log (".implode(',',$cols).") VALUES (".implode(',',$vals).")";
		@mysqli_query($con,$sql);
	}

	/* ---------- 입력 복호화 파이프 ---------- */
	if (!empty($_GET))   { $_GET   = smart_decrypt_input($_GET); }
	$ctype = $_SERVER['CONTENT_TYPE'] ?? '';
	if ($_SERVER['REQUEST_METHOD']==='POST'){
		if (stripos($ctype,'application/json')!==false){
			$raw = file_get_contents('php://input');
			$json = json_decode($raw,true);
			if (is_array($json)) $_POST = $json;
		}
		$_POST = smart_decrypt_input($_POST);
	}

	/* ---------- POST 처리(로그인) ---------- */
	if ($_SERVER['REQUEST_METHOD']==='POST'){
		try{
			$login_mode = ($_POST['mode'] ?? 'email') === 'id' ? 'id' : 'email'; // 'email' | 'id'
			$identifier = trim((string)($_POST['email_or_id'] ?? ''));
			$password   = (string)($_POST['password'] ?? '');

			if ($identifier === '') throw new Exception(($login_mode==='email'?'이메일':'아이디').'을 입력하세요.');
			if ($password   === '') throw new Exception('비밀번호를 입력하세요.');

			if ($login_mode === 'email' && !filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
				throw new Exception('유효한 이메일을 입력하세요.');
			}

			$user = find_user($identifier, $login_mode);
			if (!$user || !password_verify($password, $user['password_hash'])) {
				throw new Exception('등록되지 않은 계정이거나 비밀번호가 올바르지 않습니다.');
			}
			if ((int)$user['is_active'] === 0) {
				throw new Exception('비활성화된 계정입니다. 관리자에게 문의하세요.');
			}

			$role = strtoupper($user['role_code'] ?? '');
			$role_lc = strtolower((string)$role);
			/* 인덱스 페이지로 이동 (이후 모든 작업은 AJAX)
			   - index.* 내부에서 doc_base에 맞춰 doc/{role}/index.* 를 AJAX 로드
			   - 메뉴 클릭 시 doc_base/{menuId}.* 를 AJAX 로드 */
			$redirect_to_app_index = '';

			/* 역할 → 문서 베이스 경로 */
			$role_to_doc = [
				'HQ'       => 'doc/hq',
				'VENDER'   => 'doc/vendor',
				'CUSTOMER' => 'doc/customer',
			];
			$doc_base = $role_to_doc[$role] ?? 'doc/customer';

			/* 세션 — userid 키 항상 존재하도록 보강 */
			if (session_status() === PHP_SESSION_NONE) { @session_start(); }
			$_SESSION['user'] = [
				'user_id'     => (int)$user['user_id'],
				'userid'      => $user['userid'] ?? null,   // <- NULL일 수는 있어도 키는 항상 존재
				'email'       => $user['email'],
				'name'        => $user['name'],
				'phone'       => $user['phone'],
				'role_code'   => $role_lc,
				'role_name'   => $user['role_name'],
				'vendor_id'   => $user['vendor_id'],
				'customer_id' => $user['customer_id'],
				'logged_at'   => date('Y-m-d H:i:s'),
			];		
			$mb_id = $user['userid'] ?? null;
			$response['mb_id'] = $mb_id;
			write_audit($user['user_id'], 'LOGIN', 'users', [
				'mode'=>$login_mode, 'id'=>$identifier,
				'ua'=>$_SERVER['HTTP_USER_AGENT'] ?? '', 'ip'=>$_SERVER['REMOTE_ADDR'] ?? ''
			]);

			if (has_col('users','last_login')) {
				@mysqli_query($con, "UPDATE users SET last_login = NOW() WHERE user_id = ".(int)$user['user_id']." LIMIT 1");
			}
			
			/* 응답(암호화 적용) */
			$_SESSION['role'] = $role_lc;
			$_SESSION['menu'] = $menus[$role_lc];
			$payload = [
				'redirect'   => $redirect_to_app_index,  // 1. 로그인 후 index 로딩
				'role'       => $role,                   // 2. 이후는 AJAX만 사용
				'doc_base'   => $doc_base,               // 3. index에서 doc/{role}/index.* AJAX로 로딩
				'route_rule' => [                        // 4. 메뉴 클릭 규칙
					'index' => $doc_base.'/',  //   초기 진입 페이지
					'menu'  => $menus[$role_lc]	//   메뉴 아이디 기반 로딩 규칙
				],
			];

			$response['result'] = true;
			$response['msg']    = '로그인 되었습니다.';
			$response['item']   = $payload; //enc($payload);          // encryptValue 적용
			$response['SESSION'] = $_SESSION;
			$response['SESSION']['user'] = enc($_SESSION['user']); // 세션 정보도 암호화해 전달(필요 시)
		} catch (Throwable $e){
			$response['result'] = false;
			$response['msg']    = $e->getMessage();
			$response['error']  = ['code'=>401,'msg'=>$e->getMessage()];
		}
		Finish(); exit;
	}
?>
<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<title>올투그린 디스펜서 — 로그인</title>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<style>
:root{
  --border:#e5e7eb; --muted:#6b7280; --fg:#0f172a; --bg:#f8fafc; --white:#fff;
  --accent:#047857; --accent-weak:#ecfdf5; --warn:#dc2626; --ok:#065f46;
}
*{box-sizing:border-box}
html,body{height:100%}
body{
  margin:0;background:var(--bg);color:var(--fg);
  font-family:ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,'Noto Sans KR',Arial;
  -webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;
  display:flex;align-items:center;justify-content:center;
}
.card{
  width:100%;max-width:460px;background:var(--white);border:1px solid var(--border);
  border-radius:16px;box-shadow:0 10px 30px rgba(0,0,0,.04);overflow:hidden;
}
.card-hd{padding:18px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.card-ttl{font-size:18px;font-weight:800;color:var(--accent)}
.card-bd{padding:18px}
.row{display:grid;gap:8px;margin:12px 0}
label{font-size:13px;color:#111827;font-weight:600}
select,input[type="text"],input[type="email"],input[type="password"]{
  width:100%;padding:12px;border-radius:12px;border:1px solid var(--border);outline:none;font-size:14px;background:#fff;color:#111827;
}
input:focus,select:focus{border-color:#93c5fd;box-shadow:0 0 0 3px rgba(147,197,253,.35)}
.btn{width:100%;padding:12px 14px;border-radius:12px;border:1px solid #059669;background:#047857;color:#fff;font-weight:800;cursor:pointer;text-align:center;text-decoration:none}
.btn:disabled{opacity:.6;cursor:not-allowed}
.btn.secondary{background:#fff;color:#047857;border-color:#059669}
.small{font-size:12px;color:var(--muted)}
#msg{margin-top:10px;font-size:14px}
#msg.ok{color:var(--ok)}
#msg.err{color:var(--warn)}
.footer{padding:14px;border-top:1px solid var(--border);text-align:center}
.badge{display:inline-block;padding:2px 10px;border:1px solid var(--border);border-radius:999px;font-size:12px;color:var(--accent);background:var(--accent-weak)}
</style>
</head>
<body>
  <div class="card">
    <div class="card-hd">
      <div class="card-ttl">로그인</div>
      <div class="badge">HQ / VENDOR / CUSTOMER</div>
    </div>
    <div class="card-bd">
      <div class="row">
        <label for="mode">로그인 방식</label>
        <select id="mode">
          <option value="email" selected>이메일로 로그인</option>
          <option value="id">아이디로 로그인</option>
        </select>
      </div>
      <div class="row">
        <label id="label-id">이메일</label>
        <input id="email_or_id" type="text" placeholder="you@example.com" autofocus>
      </div>
      <div class="row">
        <label for="password">비밀번호</label>
        <input id="password" type="password" placeholder="비밀번호를 입력하세요">
      </div>
      <div class="row">
        <button class="btn" id="btnLogin">로그인</button>
        <div id="msg"></div>
      </div>
      <!-- 추가: 회원가입 버튼 -->
      <div class="row" style="margin-top:4px">
        <a class="btn secondary" href="member.php" id="btnSignup">회원가입</a>
      </div>
      <div class="small">로그인 후 index 페이지가 로드되고, 모든 화면은 AJAX로 전환됩니다.</div>
    </div>
    <div class="footer small">
      © Alltogreen Dispenser
    </div>
  </div>

<script>
	(function($){
	  function v(id){ return $.trim($(id).val()||''); }
	  function showMsg(ok, t){ $('#msg').removeClass('ok err').addClass(ok?'ok':'err').text(t); }

	  $('#mode').on('change', function(){
		var m = $(this).val();
		if(m==='id'){
		  $('#label-id').text('아이디');
		  $('#email_or_id').attr('placeholder','아이디를 입력하세요');
		}else{
		  $('#label-id').text('이메일');
		  $('#email_or_id').attr('placeholder','you@example.com');
		}
		$('#email_or_id').focus();
	  });

	  $('#btnLogin').on('click', function(e){
		e.preventDefault();
		var payload = {
		  mode: $('#mode').val(),                // 'email' | 'id'
		  email_or_id: v('#email_or_id'),       // 단일 입력칸
		  password: v('#password')
		};
		if(!payload.email_or_id){ showMsg(false, (payload.mode==='email'?'이메일':'아이디')+'을 입력하세요.'); return; }
		if(!payload.password){ showMsg(false,'비밀번호를 입력하세요.'); return; }

		$('#btnLogin').prop('disabled', true);
		$.ajax({
		  url: location.href, method:'POST',
		  contentType:'application/json; charset=utf-8',
		  data: JSON.stringify(payload), dataType:'json'
		}).done(function(res){
		  if(res && res.result){
			showMsg(true, res.msg || '로그인 성공');

			/* 서버 응답은 encryptValue 로 암호화되어 있음.
			   index 페이지에서 decryptValue를 통해 'item' 내부 값을 복호화해 사용.
			   여기서는 리다이렉트만 수행 (index.*에서 역할별 doc 페이지를 AJAX로 로딩) */
			var to = '<?= SRC ?>';
			if(res.item && typeof decryptValue === 'function'){
			  try{
				// decryptValue 가 전역 JS로 노출되어 있지 않으면 단순 리다이렉트
				// (해당 함수는 서버측 규칙에 따라 프론트엔드에서 사용할 수 없을 수 있음)
			  }catch(e){}
			}
			setTimeout(function(){ window.location.href = to; }, 300);
		  }else{
			showMsg(false, (res && res.msg) ? res.msg : '로그인에 실패했습니다.');
		  }
		}).fail(function(){
		  showMsg(false,'네트워크 오류가 발생했습니다.');
		}).always(function(){
		  $('#btnLogin').prop('disabled', false);
		});
	  });

	  $('#password').on('keydown', function(e){
		if(e.key === 'Enter'){ $('#btnLogin').click(); }
	  });
	})(jQuery);
</script>
</body>
</html>
