<?php
/**
 * pages/dev.php
 * - 파라미터(a 등) 없으면: HTML 프래그먼트만 출력 (초기 로드 화면)
 * - 파라미터 있으면: JSON만 출력 ($response 포맷, html 키에 부분 HTML)
 * - DB: $con (mysqli), 세션: $_SESSION, 공용 응답: $response (common.php에서 제공)
 * - AJAX는 반드시 메인 공통함수 updateAjaxContent(data, callback, isClose) 사용
 */

if (!defined('DISPENSER')) exit; // 개별 접근 차단

mb_internal_encoding('UTF-8');
ini_set('output_buffering', '4096');

global $con, $response;

// === [첨부 설정 - 추가] =======================================
$DEV_UPLOAD_DIR = FILES_ROOT . '/dev_uploads';   // 실제 저장 경로
$DEV_UPLOAD_URL = FILES_SRC . '/dev_uploads';        // 웹 접근 경로

function _ensure_dir($path){ if (!is_dir($path)) @mkdir($path, 0775, true); }
function _allowed_ext($name){
  $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
  $allow = ['jpg','jpeg','png','gif','pdf','doc','docx','xls','xlsx','xlsm','ppt','pptx','txt','zip','rar','7z','hwp','hwpx','csv'];
  return in_array($ext,$allow,true);
}
// ============================================================

// === [첨부: DB 액세스 - 추가] ================================
function get_request_files(int $uid): array {
  global $con;
  $rs = $con->query("SHOW TABLES LIKE 'dev_request_files'");
  if (!$rs || $rs->num_rows === 0) return [];
  $stmt = $con->prepare("SELECT file_id, uid, original_name, stored_name, mime, size, created_at
                         FROM dev_request_files WHERE uid=? ORDER BY file_id ASC");
  $stmt->bind_param('i',$uid); $stmt->execute();
  $out=[]; $r=$stmt->get_result(); while($row=$r->fetch_assoc()) $out[]=$row;
  return $out;
}

function delete_file_by_id(int $file_id): bool {
  global $con, $DEV_UPLOAD_DIR;
  $rs = $con->query("SHOW TABLES LIKE 'dev_request_files'");
  if (!$rs || $rs->num_rows === 0) return false;

  $stmt = $con->prepare("SELECT file_id, uid, stored_name FROM dev_request_files WHERE file_id=?");
  $stmt->bind_param('i',$file_id); $stmt->execute();
  $row = $stmt->get_result()->fetch_assoc();
  if (!$row) return false;

  $uid = (int)$row['uid'];
  $path = $DEV_UPLOAD_DIR . "/$uid/" . $row['stored_name'];
  if (is_file($path)) @unlink($path);

  $stmt = $con->prepare("DELETE FROM dev_request_files WHERE file_id=?");
  $stmt->bind_param('i',$file_id); $ok = $stmt->execute();

  if ($ok) $con->query("UPDATE dev_requests
                        SET attachment_count = GREATEST(attachment_count-1,0), updated_at=NOW()
                        WHERE uid={$uid}");
  return $ok;
}

function save_uploaded_files(int $uid, bool $replaceAll=false): array {
  global $con, $DEV_UPLOAD_DIR;
  $saved=[];

  // 교체 옵션: 기존 첨부 모두 삭제
  if ($replaceAll) {
    foreach (get_request_files($uid) as $f) delete_file_by_id((int)$f['file_id']);
  }

  if (empty($_FILES['attachments'])) return $saved;
  $f = $_FILES['attachments'];
  $cnt = is_array($f['name']) ? count($f['name']) : 0;

  _ensure_dir($DEV_UPLOAD_DIR . "/$uid");

  for ($i=0; $i<$cnt; $i++){
    $name=$f['name'][$i]??''; $tmp=$f['tmp_name'][$i]??''; $err=(int)($f['error'][$i]??4);
    $size=(int)($f['size'][$i]??0);
    if ($err!==UPLOAD_ERR_OK || !$name || !$tmp) continue;
    if ($size<=0 || $size>50*1024*1024) continue;
    if (!_allowed_ext($name)) continue;

    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    $stored = uniqid('f_', true) . ($ext?'.'.$ext:'');
    $dest = $DEV_UPLOAD_DIR . "/$uid/" . $stored;
    if (!@move_uploaded_file($tmp, $dest)) continue;

    $mime = @mime_content_type($dest) ?: '';
    $stmt = $con->prepare("INSERT INTO dev_request_files (uid, original_name, stored_name, mime, size, uploaded_by)
                           VALUES (?,?,?,?,?,?)");
    $by = (int)(function_exists('current_user_id') ? current_user_id() : 0);
    $stmt->bind_param('isssii', $uid, $name, $stored, $mime, $size, $by);
    if ($stmt->execute()){
      $con->query("UPDATE dev_requests SET attachment_count = attachment_count+1, updated_at=NOW() WHERE uid={$uid}");
      $saved[] = ['file_id'=>$con->insert_id,'name'=>$name,'stored'=>$stored,'size'=>$size];
    }
  }
  return $saved;
}
// ============================================================


function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
// 개발자 판별: 부서 키가 없으므로 userid가 'program1472'인지로 판단
function is_developer(): bool {
    $u = $_SESSION['user'] ?? null;
    return is_array($u) && ($u['userid'] ?? null) === 'program1472';
}

// 로그인 사용자 ID: user.user_id 사용 (정수 반환)
function current_user_id(): ?int {
    if (isset($_SESSION['user']['user_id'])) {
        return (int)$_SESSION['user']['user_id'];
    }
    // 레거시/호환(없을 때만)
    if (isset($_SESSION['uid'])) return (int)$_SESSION['uid'];
    return null;
}

// 로그인 사용자 표시 이름: user.name 우선, 없으면 user_id 문자열
function current_user_name(): string {
    if (!empty($_SESSION['user']['name'])) {
        return (string)$_SESSION['user']['name'];
    }
    if (isset($_SESSION['user']['user_id'])) {
        return (string)$_SESSION['user']['user_id'];
    }
    return '';
}
function status_row_class(string $status): string {
  switch ($status) {
    case '완료':     return 'status-completed';
    case '반려':     return 'status-rejected';
    case '취소':     return 'status-canceled';
    case '보류':     return 'status-onhold';
    case '재요청':   return 'status-reopen';
    case '확인중':   return 'status-review';
    case '진행중':   return 'status-inprogress';
    case '접수중':   return 'status-new';
    case '업체확인요청': return 'status-request-vendor';  // ← 추가
    default:         return '';
  }
}
function priority_row_class(string $priority): string {
  switch ($priority) {
    case '긴급': return 'priority-urgent';
    case '높음': return 'priority-high';
    case '보통': return 'priority-normal';
    case '낮음': return 'priority-low';
    default:     return '';
  }
}

/** 도메인 상수 */
$STATUS = ['접수중','확인중','진행중','보류','완료','반려','취소','재요청','업체확인요청'];
$PRIORITY = ['낮음','보통','높음','긴급'];

/** 액션 추출(POST 우선, 없으면 GET), 없으면 초기 HTML만 출력 */
$action = $_POST['a'] ?? ($_GET['a'] ?? null);

/* =========================
 * CSS 로드 + <style> 래핑 후 하나의 문자열로 반환
 * - 각 파일의 출력 버퍼를 받아서 <style> 태그가 없으면 감싸준다.
 * - 존재하지 않는 파일은 건너뛴다.
========================= */
function load_css_from_php(): string
{
    $files = [CSS_ROOT . '/dev.css',];
    $chunks = [];
    foreach ($files as $file) {
        if (!is_file($file)) continue;
        ob_start();
        require $file;	// 파일 안에서 echo한 CSS를 캡처
        $cssContent = trim(ob_get_clean());
        if ($cssContent === '') continue;
        // <style> 태그가 없으면 감싸기 (대소문자 무시)
        if (stripos($cssContent, '<style') === false) $cssContent = "<style>\n{$cssContent}\n</style>";
        $chunks[] = $cssContent;
    }
    return implode("\n", $chunks);
}

/* =========================
   form html 로드
========================= */
function load_html_from_php(): string {
  //ob_start();
  //require PAGES_ROOT . '/dev/devform.php';
  //$formContent = ob_get_clean();
  $formContent = "
		<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"
			width:100%; height:100%;
			border-collapse:separate; border-spacing:0;
			table-layout:fixed;
			font-family:'Segoe UI', Pretendard, 'Apple SD Gothic Neo', 'Malgun Gothic', sans-serif;
			font-size:14px; color:#2b2f36;
			background:#fff;
			box-shadow:0 1px 2px rgba(27,31,36,.04), 0 4px 12px rgba(27,31,36,.06);
			border:1px solid #d0d7de;
			border-radius:10px;
			overflow:hidden;\">
		  <colgroup>
			<col style=\"width:12%;\">
			<col style=\"width:88%;\">
		  </colgroup>

		  <thead>
			<tr style=\"height:44px;\">
			  <th style=\"
				  padding:10px 12px;
				  text-align:center;
				  font-weight:600;
				  color:#24292f;
				  background:#f6f8fa;
				  border-bottom:1px solid #d0d7de;
				  border-right:1px solid #e5e7eb;
				  border-top-left-radius:10px;\">
				항목
			  </th>
			  <th style=\"
				  padding:10px 12px;
				  text-align:center;
				  font-weight:600;
				  color:#24292f;
				  background:#f6f8fa;
				  border-bottom:1px solid #d0d7de;
				  border-top-right-radius:10px;\">
				내용
			  </th>
			</tr>
		  </thead>

		  <tbody style=\"height:calc(100% - 44px);\">
			<tr style=\"height:33.3333%;\">
			  <td style=\"
				  padding:14px 12px;
				  background:#fbfbfc;
				  border-right:1px solid #eef1f4;
				  border-bottom:1px solid #eef1f4;
				  text-align:center;
				  font-weight:500;
				  color:#444;\">
				주요내용
			  </td>
			  <td style=\"
				  padding:14px 12px;
				  border-bottom:1px solid #eef1f4;
				  vertical-align:middle;\"><p>매뉴명:</p><p>내&nbsp; &nbsp;용:</p></td>
			</tr>

			<tr style=\"height:33.3333%;\">
			  <td style=\"
				  padding:14px 12px;
				  background:#fbfbfc;
				  border-right:1px solid #eef1f4;
				  border-bottom:1px solid #eef1f4;
				  text-align:center;
				  font-weight:500;
				  color:#444;\">
				요청사항
			  </td>
			  <td style=\"
				  padding:14px 12px;
				  border-bottom:1px solid #eef1f4;
				  vertical-align:middle;\">
			  </td>
			</tr>

			<tr style=\"height:33.3333%;\">
			  <td style=\"
				  padding:14px 12px;
				  background:#fbfbfc;
				  border-right:1px solid #eef1f4;
				  text-align:center;
				  font-weight:500;
				  color:#444;
				  border-bottom-left-radius:10px;\">
				처리내용
			  </td>
			  <td style=\"
				  padding:14px 12px;
				  vertical-align:middle;
				  border-bottom-right-radius:10px;\">
			  </td>
			</tr>
		  </tbody>
		</table>
	";
  return $formContent;
}

function wrap_container(string $html): string {
  return '<div class="container">' . $html . '</div>';
}

/* ─────────────────────────────────────────────────────────
 * 1) 초기 진입: a 파라미터가 없으면 HTML만 출력 (JSON 절대 X)
 * ───────────────────────────────────────────────────────── */
if ($action === null) {
  $css = load_css_from_php();
  echo $css . wrap_container(render_list_html()); // 항상 container로 감싼다
  echo render_bootstrap_js();
  exit;
}

/* ─────────────────────────────────────────────────────────
 * 2) 액션 요청: JSON만 출력 (HTML echo 금지, $response로만 응답)
 * ───────────────────────────────────────────────────────── */
switch ($action) {
  case 'list':           action_list_json(); break;
  case 'create':         action_create_json(); break;
  case 'store':          action_store_json(); break;
  case 'show':           action_show_json(); break;
  case 'edit':           action_edit_json(); break;
  case 'update':         action_update_json(); break;
  case 'delete':         action_delete_json(); break;
  case 'status':         action_status_json(); break;
  case 'comment_store':  action_comment_store_json(); break;
  case 'reopen':         action_reopen_json(); break;   // <-- 추가
  case 'comment_update':  action_comment_update_json(); break;
  case 'comment_delete':  action_comment_delete_json(); break;
  case 'file_upload':    action_file_upload_json(); break;   // [추가]
  case 'file_delete':    action_file_delete_json(); break;   // [추가]
  default:               action_list_json(); break;
}

// === [첨부 액션 - 추가] ======================================
function action_file_upload_json(){
  $uid = (int)($_POST['uid'] ?? $_GET['uid'] ?? 0);
  if ($uid<=0) fail_msg('잘못된 UID', 400);
  save_uploaded_files($uid, false);
  $_POST['uid'] = $uid;
  action_show_json(); // 기존 방식: 상세 화면 조각 리턴
}

function action_file_delete_json(){
  $file_id = (int)($_POST['file_id'] ?? $_GET['file_id'] ?? 0);
  $uid     = (int)($_POST['uid'] ?? $_GET['uid'] ?? 0);
  if ($file_id<=0) fail_msg('잘못된 파일', 400);
  if (!delete_file_by_id($file_id)) fail_msg('삭제 실패', 500);
  if ($uid>0){ $_POST['uid']=$uid; action_show_json(); }
  else ok_msg('삭제되었습니다.');
}
// ============================================================


/* =========================
   유틸: JSON 응답
========================= */

function action_comment_update_json(){
  global $con;
  $cid = (int)($_POST['comment_id'] ?? 0);
  $uid = (int)($_POST['uid'] ?? 0);
  $body = trim($_POST['body'] ?? '');
  if ($cid<=0 || $uid<=0) fail_msg('잘못된 요청', 400);
  if ($body==='') fail_msg('코멘트를 입력하세요.', 422);

  // 권한: 본인만
  $stmt = $con->prepare('SELECT author_id FROM dev_request_comments WHERE comment_id=? AND uid=?');
  $stmt->bind_param('ii', $cid, $uid); $stmt->execute();
  $row = $stmt->get_result()->fetch_assoc();
  if (!$row) fail_msg('존재하지 않는 코멘트', 404);
  if ((int)$row['author_id'] !== (int)current_user_id()) fail_msg('권한이 없습니다.', 403);

  $stmt = $con->prepare('UPDATE dev_request_comments SET body=? WHERE comment_id=?');
  $stmt->bind_param('si', $body, $cid);
  if (!$stmt->execute()) fail_msg('코멘트 수정 실패: '.$stmt->error, 500);

  $_POST['uid'] = $uid;
  action_show_json();
}

function action_comment_delete_json(){
  global $con;
  $cid = (int)($_POST['comment_id'] ?? $_GET['comment_id'] ?? 0);
  $uid = (int)($_POST['uid'] ?? $_GET['uid'] ?? 0);
  if ($cid<=0 || $uid<=0) fail_msg('잘못된 요청', 400);

  // 권한: 본인만
  $stmt = $con->prepare('SELECT author_id FROM dev_request_comments WHERE comment_id=? AND uid=?');
  $stmt->bind_param('ii', $cid, $uid); $stmt->execute();
  $row = $stmt->get_result()->fetch_assoc();
  if (!$row) fail_msg('존재하지 않는 코멘트', 404);
  if ((int)$row['author_id'] !== (int)current_user_id()) fail_msg('권한이 없습니다.', 403);

  $stmt = $con->prepare('DELETE FROM dev_request_comments WHERE comment_id=?');
  $stmt->bind_param('i', $cid);
  if (!$stmt->execute()) fail_msg('코멘트 삭제 실패: '.$stmt->error, 500);

  $_POST['uid'] = $uid;
  action_show_json();
}

function respond_json(array $patch = []): void {
  global $response;
  if (!headers_sent()) {
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
  }

  // patch 적용
  $response = array_replace($response, $patch);

  // html이 있으면: css.php 붙이고, 반드시 container로 감싸서 넣는다
  if (!empty($response['html'])) {
    $css = load_css_from_php();
    $response['html'] = $css . wrap_container($response['html']);
  }

  echo json_encode($response, JSON_UNESCAPED_UNICODE);
  exit;
}

function action_reopen_json(){
  global $con;

  $uid = (int)($_POST['uid'] ?? $_GET['uid'] ?? 0);
  $note = trim($_POST['note'] ?? $_GET['note'] ?? '');

  if ($uid <= 0) fail_msg('잘못된 UID', 400);

  // 대상 존재 확인 + 현재 상태 조회
  $stmt = $con->prepare('SELECT status,reopened_at FROM dev_requests WHERE uid=? AND deleted_at IS NULL');
  $stmt->bind_param('i', $uid);
  $stmt->execute();
  $cur = $stmt->get_result()->fetch_assoc();
  if (!$cur) fail_msg('존재하지 않는 요청', 404);

  // 상태를 '재요청'으로 변경 + reopened_at 찍기
  $sql = 'UPDATE dev_requests SET status="재요청", reopened_at=IF(reopened_at IS NULL, NOW(), reopened_at) WHERE uid=?';
  $stmt = $con->prepare($sql);
  $stmt->bind_param('i', $uid);
  if (!$stmt->execute()) fail_msg('재요청 처리 실패: '.$stmt->error, 500);

  // 로그 테이블 있을 경우 기록
  if($con->query("SHOW TABLES LIKE 'dev_request_status_log'")->num_rows){
    $stmt=$con->prepare('INSERT INTO dev_request_status_log (uid,from_status,to_status,changed_by,note) VALUES (?,?,?,?,?)');
    $by=current_user_id();
    $from=$cur['status'];
    $to='재요청';
    $stmt->bind_param('issis',$uid,$from,$to,$by,$note);
    $stmt->execute();
  }

  // 완료 후 상세 재출력
  $_POST['uid']=$uid;
  action_show_json();
}

function ok_html(string $html, array $extra = []): void {
  respond_json(array_replace(['result'=>true, 'html'=>$html], $extra));
}

function fail_msg(string $msg, int $code = 0, string $html = ''): void {
  respond_json(['result'=>false, 'error'=>['msg'=>$msg, 'code'=>$code], 'html'=>$html]);
}

function capture_html(callable $fn): string { ob_start(); $fn(); return ob_get_clean(); }

/* =========================
   HTML 렌더러(초기/부분)
========================= */
function render_list_html(): string {
  global $con, $STATUS;

$q      = trim($_POST['q'] ?? $_GET['q'] ?? '');
$status = $_POST['status'] ?? $_GET['status'] ?? '';

$where  = 'r.deleted_at IS NULL';
$types  = '';
$params = [];

if ($q !== '') {
  $where   .= ' AND (r.title LIKE CONCAT("%", ?, "%") OR r.content LIKE CONCAT("%", ?, "%"))';
  $types   .= 'ss';
  $params[] = $q;
  $params[] = $q;
}
if ($status !== '') {
  $where   .= ' AND r.status = ?';
  $types   .= 's';
  $params[] = $status;
}

$sql = "
SELECT
  r.uid, r.status, r.title, r.priority, r.category,
  r.registered_at, r.due_at, r.completed_at, r.progress,
  l.note       AS last_reason,
  l.changed_at AS last_reason_at
FROM dev_requests AS r
LEFT JOIN (
  SELECT uid, MAX(log_id) AS last_log_id
  FROM dev_request_status_log
  GROUP BY uid
) AS lx ON lx.uid = r.uid
LEFT JOIN dev_request_status_log AS l ON l.log_id = lx.last_log_id
WHERE $where
ORDER BY r.uid DESC
LIMIT 100
";

$stmt = $con->prepare($sql);
if ($types) $stmt->bind_param($types, ...$params);
$stmt->execute();
$rs = $stmt->get_result();
$rows=[]; while($r=$rs->fetch_assoc()) $rows[]=$r;




  return capture_html(function() use($rows,$STATUS,$q,$status){ ?>
		<!-- 검색 영역 -->
		<div class="search-bar" style="justify-content: space-between;">
			<div class="search-left">
				<label for="searchField" style="margin-left: 20px;">상태 :</label>
				<select name="status">
				  <option value="">상태 전체</option>
				  <?php foreach ($STATUS as $s): ?>
					<option<?= $status===$s?' selected':''; ?>><?=h($s)?></option>
				  <?php endforeach; ?>
				</select>

				<input type="text" name="q" placeholder="제목/내용 검색" value="<?=h($q)?>">
				<a class="btn ajax-submit" data-action="list"><button class="btn-submit">검색</button></a>
			</div>
			<div class="search-right">
				<a class="btn primary ajax-link" data-action="create"><button>등록</button></a>
			</div>		
		</div>	  
		<!-- 테이블 영역 -->
		<div class="table-scroll">
			<table class="data-table">
				<thead>
					<tr>
					  <th>UID</th>
					  <th>상태</th>
					  <th>사유</th>
					  <th>제목</th>
					  <th>우선</th>
					  <th>카테고리</th>
					  <th>등록일</th>
					  <th>마감</th>
					  <th>완료일</th>
					  <th>%</th>
					</tr>
				</thead>

				<tbody id="datatable">
				  <?php if(!$rows): ?>
					<tr><td colspan="10" style="text-align:center;color:#aab4dd">데이터가 없습니다.</td></tr>
					<?php else: foreach($rows as $row): ?>
					  <tr class="<?= h(status_row_class((string)$row['status'])) ?> <?= h(priority_row_class((string)$row['priority'])) ?>">
						<td><a class="ajax-link" data-action="show" data-uid="<?= (int)$row['uid'] ?>"><?= (int)$row['uid'] ?></a></td>
						<td><?= h($row['status']) ?></td>
						<td><?= h($row['last_reason'] ?? '') ?></td>
						<td><a class="ajax-link" data-action="show" data-uid="<?= (int)$row['uid'] ?>"><?= h($row['title']) ?></a></td>
						<td><?= h($row['priority']) ?></td>
						<td><?= h($row['category']) ?></td>
						<td><?= h($row['registered_at']) ?></td>
						<td><?= h($row['due_at']) ?></td>
						<td><?= h($row['completed_at']) ?></td>
						<td><?= (int)$row['progress'] ?></td>
					  </tr>
					<?php endforeach; endif; ?>
				</tbody>
			</table>
		</div>

		<!-- 페이징 영역 -->
		<div class="paging"></div>

  <?php });
}

function render_form_html(array $v = [], ?int $uid = null): string {
  global $PRIORITY;
  $isEdit   = $uid !== null;
  $title    = $v['title']    ?? '';
  $content  = $v['content']  ?? '';
  $priority = $v['priority'] ?? '보통';
  $category = $v['category'] ?? '';
  $due_at   = $v['due_at']   ?? '';
  $progress = isset($v['progress']) ? (int)$v['progress'] : 0;

  return capture_html(function() use($isEdit,$uid,$title,$content,$priority,$category,$due_at,$progress,$PRIORITY){ ?>
    <div class="form-card ajax-scope">
      <label>제목<input type="text" name="title" value="<?=h($title)?>" required></label>
      <label>
        <textarea id="contentEditor" name="content" data-se2="1" rows="10" cols="100" style="width:100%; height:470px; min-width:610px; display:none;" required><?=h($content)?></textarea>
      </label>
      <div class="form-row-inline">
        <label>우선순위
          <select name="priority">
            <?php foreach ($PRIORITY as $p): ?>
              <option<?= $p===$priority?' selected':''; ?>><?=h($p)?></option>
            <?php endforeach; ?>
          </select>
        </label>

        <label>카테고리
          <input type="text" name="category" value="<?=h($category)?>">
        </label>

        <label>목표 완료일
          <input type="datetime-local" name="due_at" value="<?=h($due_at)?>">
        </label>
        <?php if ($isEdit): ?>
          <label>진행률(%)<input type="number" name="progress" min="0" max="100" value="<?=$progress?>"></label>
          <input type="hidden" name="uid" value="<?= (int)$uid ?>">
        <?php endif; ?>

		<label>첨부파일
		  <input type="file" name="attachments[]" multiple>
		</label>
		<?php if ($isEdit): ?>
		  <label class="inline"><input type="checkbox" name="replace_all" value="1"> 기존 첨부 모두 삭제 후 새로 첨부</label>
		<?php endif; ?>
      </div>

      <div class="form-actions">
        <?php if ($isEdit): ?>
          <!-- 재요청 체크박스 (수정 모드에서만 표시) -->
          <label style="margin-right:12px;display:inline-flex;align-items:center;gap:6px">
            <input type="checkbox" name="reopen" value="1">
            재요청
          </label>
        <?php endif; ?>

        <a class="btn primary ajax-submit" data-action="<?= $isEdit?'update':'store' ?>"><?= $isEdit?'수정 저장':'등록' ?></a>
        <a class="btn ajax-link" data-action="<?= $isEdit?'show':'list' ?>"<?= $isEdit?' data-uid="'.(int)$uid.'"':''; ?>>취소</a>
      </div>
    </div>
  <?php });
}

function render_show_html(array $row): string {
  return capture_html(function() use($row){ ?>
    <div class="page-head">
      <h2>#<?= (int)$row['uid'] ?> <?= h($row['title']) ?></h2>
      <div class="actions">
        <a class="btn ajax-link" data-action="list">목록</a>
        <?php if (can_edit_request($row)): ?>
          <a class="btn ajax-link" data-action="edit" data-uid="<?= (int)$row['uid'] ?>">수정</a>
        <?php endif; ?>
        <?php if (function_exists('can_delete_request') ? can_delete_request($row) : true): ?>
          <!-- 삭제 버튼 추가 -->
          <a
            class="btn danger ajax-submit"
            data-action="delete"
            data-uid="<?= (int)$row['uid'] ?>"
            onclick="return confirm('정말 삭제하시겠습니까?');"
          >삭제</a>
        <?php endif; ?>
      </div>
    </div>

    <div class="card"><div class="card-body">
      <div class="meta"><span class="tag"><?= h($row['status']) ?></span> · 우선순위 <?= h($row['priority']) ?> · 카테고리 <?= h($row['category']) ?></div>
      <div class="dates">등록일 <?= h($row['registered_at']) ?> / 마감 <?= h($row['due_at']) ?> / 완료 <?= h($row['completed_at']) ?></div>
      <div class="progress">진행률: <?= (int)$row['progress'] ?>%</div>
      <hr><pre class="content"><?= $row['content'] ?></pre>
    </div></div>
    <?php echo render_status_form_html($row); ?>
    <?php echo render_files_section_html((int)$row['uid']); ?>
    <?= render_comment_form_html((int)$row['uid']) ?>
    <?= render_comments_html((int)$row['uid']) ?>
  <?php });
}

function render_status_form_html(array $row): string {
  global $STATUS; $uid = (int)$row['uid'];

  // 개발자면 기존 그대로
  if (is_developer()) {
    return capture_html(function() use($STATUS,$row,$uid){ ?>
      <div class="card"><div class="card-body ajax-scope">
        <h3>상태 변경</h3>
        상태:<select name="to" class="status-select">
          <?php foreach ($STATUS as $o): ?>
            <option<?= $o===$row['status']?' selected':''; ?>><?=h($o)?></option>
          <?php endforeach; ?>
        </select>
        &nbsp; &nbsp;진행율:<input type="number" name="progress" placeholder="진척(%)" class="status-progress" min="0" max="100" step="1" value="<?= isset($row['progress']) ? (int)$row['progress'] : '' ?>" inputmode="numeric">
        &nbsp; &nbsp;사유:<input type="text" name="note" placeholder="변경 사유" class="status-note">
        <a class="btn ajax-submit" data-action="status" data-uid="<?=$uid?>">변경</a>
      </div></div>
    <?php });
  }

  // 비개발자면 “재요청”만
  return capture_html(function() use($uid){ ?>
    <div class="card"><div class="card-body ajax-scope">
      <h3>재요청</h3>
      <input type="text" name="note" placeholder="재요청 사유(선택)">
      <a class="btn primary ajax-submit" data-action="reopen" data-uid="<?=$uid?>">재요청</a>
    </div></div>
  <?php });
}

function render_files_section_html(int $uid): string {
  global $DEV_UPLOAD_URL;
  $files = get_request_files($uid);
  return capture_html(function() use($files,$uid,$DEV_UPLOAD_URL){ ?>
    <div class="card"><div class="card-body">
      <div style="display:flex;justify-content:space-between;align-items:center;gap:8px">
        <strong>첨부파일</strong>
        <form class="file-upload-form" enctype="multipart/form-data" method="post">
          <input type="hidden" name="a" value="file_upload">
          <input type="hidden" name="uid" value="<?= (int)$uid ?>">
          <input type="file" name="attachments[]" multiple>
          <a class="btn ajax-submit file-upload-button">추가 업로드</a>
        </form>
      </div>
      <?php if (!$files): ?>
        <div class="muted" style="margin-top:8px">첨부 없음</div>
      <?php else: ?>
        <ul class="file-list" style="margin-top:8px; list-style:none; padding-left:0">
          <?php foreach ($files as $f):
            $href = $DEV_UPLOAD_URL . '/' . $f['uid'] . '/' . rawurlencode($f['stored_name']);
          ?>
            <li style="display:flex;align-items:center;gap:10px;padding:4px 0">
              <a href="<?=h($href)?>" download="<?=h($f['original_name'])?>"><?=h($f['original_name'])?></a>
              <small class="muted">(<?= number_format((int)$f['size']) ?> bytes)</small>
              <a class="btn danger file-del-link" data-file-id="<?= (int)$f['file_id'] ?>" data-uid="<?= (int)$f['uid'] ?>">삭제</a>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div></div>
  <?php });
}


function render_comment_form_html(int $uid): string {
  return capture_html(function() use($uid){ ?>
    <div class="card"><div class="card-body ajax-scope">
      <h3>코멘트</h3>
      <textarea name="body" rows="3" class="comment-body" required></textarea>
      <div class="form-actions"><a class="btn ajax-submit" data-action="comment_store" data-uid="<?=$uid?>">등록</a></div>
    </div></div>
  <?php });
}

function render_comments_html(int $uid): string {
  global $con;
  if (!$con->query("SHOW TABLES LIKE 'dev_request_comments'")->num_rows) {
    return '<div class="card"><div class="card-body"><em style="color:#aab4dd">등록된 코멘트가 없습니다.</em></div></div>';
  }
  $stmt=$con->prepare('SELECT comment_id,author_id,author_name,body,created_at FROM dev_request_comments WHERE uid=? ORDER BY comment_id DESC');
  $stmt->bind_param('i',$uid); $stmt->execute(); $res=$stmt->get_result();
  $rows=[]; while($r=$res->fetch_assoc()) $rows[]=$r;

  $me = current_user_id();

  return capture_html(function() use($rows,$uid,$me){ ?>
    <div class="card"><div class="card-body">
      <ul class="comments" style="list-style:none;padding:0;margin:0">
        <?php if(!$rows): ?>
          <li class="empty" style="color:#aab4dd">등록된 코멘트가 없습니다.</li>
        <?php else: foreach($rows as $c):
          $isMine = $me !== null && (int)$c['author_id'] === (int)$me;
          $cid = (int)$c['comment_id'];
        ?>
          <li id="comment-<?=$cid?>" style="border-top:1px solid #1f2746;padding:12px 0">
            <div class="meta" style="font-size:12px;color:#aab4dd;margin-bottom:6px">
              <?= h($c['author_name'] ?: ('#'.$c['author_id'])) ?> · <?= h($c['created_at']) ?>
              <?php if ($isMine): ?>
                <span style="margin-left:8px">
                  <a href="#" class="comment-edit-link" data-cid="<?=$cid?>">수정</a> ·
                  <a href="#" class="comment-del-link" data-cid="<?=$cid?>" data-uid="<?=$uid?>">삭제</a>
                </span>
              <?php endif; ?>
            </div>

            <!-- 보기 모드 -->
            <pre class="comment-view" style="white-space:pre-wrap;margin:0"><?= str_replace("《HOST》", "../..", $c['body']) ?></pre>

            <?php if ($isMine): ?>
              <!-- 편집 모드 (초기엔 숨김) -->
              <div class="ajax-scope comment-edit-area" data-cid="<?=$cid?>" style="display:none;margin-top:8px">
                <input type="hidden" name="uid" value="<?=$uid?>">
                <input type="hidden" name="comment_id" value="<?=$cid?>">
                <textarea name="body" rows="3" style="width:100%"><?= str_replace("《HOST》", "../..", $c['body']) ?></textarea>
                <div class="form-actions" style="margin-top:6px; display:flex; gap:8px">
                  <a class="btn primary ajax-submit" data-action="comment_update" data-cid="<?=$cid?>">저장</a>
                  <a class="btn comment-cancel-link" data-cid="<?=$cid?>">취소</a>
                </div>
              </div>
            <?php endif; ?>
          </li>
        <?php endforeach; endif; ?>
      </ul>
    </div></div>
  <?php });
}

/* =========================
   액션(JSON) — HTML은 $response['html'] 로만 반환
========================= */
function action_list_json(){
  $html = render_list_html();
  ok_html($html);
}

function action_create_json(){
  ok_html( render_form_html() );
}

function action_show_json(){
  global $con;
  $uid=(int)($_POST['uid'] ?? $_GET['uid'] ?? 0);
  if($uid<=0) fail_msg('잘못된 UID', 400);

  $stmt=$con->prepare('SELECT * FROM dev_requests WHERE uid=? AND deleted_at IS NULL');
  $stmt->bind_param('i',$uid); $stmt->execute(); $row=$stmt->get_result()->fetch_assoc();
  if(!$row) fail_msg('존재하지 않는 요청', 404);

  ok_html('<div class="table-scroll dev-show">'. render_show_html($row) .'</div>');
}

function action_edit_json(){
  global $con;
  $uid=(int)($_POST['uid'] ?? $_GET['uid'] ?? 0);
  if($uid<=0) fail_msg('잘못된 UID', 400);

  $stmt=$con->prepare('SELECT * FROM dev_requests WHERE uid=? AND deleted_at IS NULL');
  $stmt->bind_param('i',$uid); $stmt->execute(); $row=$stmt->get_result()->fetch_assoc();
  if(!$row) fail_msg('존재하지 않는 요청', 404);
  if(!can_edit_request($row)) fail_msg('권한이 없습니다.', 403);

  ok_html( render_form_html($row, $uid) );
}

function action_store_json(){
  global $con, $PRIORITY;

  $title    = trim($_POST['title'] ?? '');
  $content  = trim($_POST['content'] ?? '');
  $priority = in_array($_POST['priority'] ?? '', $PRIORITY, true) ? $_POST['priority'] : '보통';
  $category = trim($_POST['category'] ?? '');
  $due_at   = trim($_POST['due_at'] ?? '');

  if($title==='' || $content===''){
    $html = '<div class="alert">제목과 내용을 입력하세요.</div>' . render_form_html($_POST);
    fail_msg('검증 실패', 422, $html);
  }

  $sql='INSERT INTO dev_requests (status,title,content,priority,category,requester_id,due_at) VALUES ("접수중",?,?,?,?,?,?)';
  $stmt=$con->prepare($sql);
  $rid=current_user_id(); $due=($due_at==='')?null:$due_at;
  $stmt->bind_param('ssssis',$title,$content,$priority,$category,$rid,$due);
  if(!$stmt->execute()){
    $html = '<div class="alert">저장 실패</div>' . render_form_html($_POST);
    fail_msg('DB 저장 실패: '.$stmt->error, 500, $html);
  }

  $_POST['uid']=(int)$con->insert_id;
  save_uploaded_files($_POST['uid'], false);	// [첨부 저장 - 추가]
  action_show_json();
}

function action_update_json(){
  global $con, $PRIORITY;
  $uid=(int)($_POST['uid'] ?? $_GET['uid'] ?? 0);
  if($uid<=0) fail_msg('잘못된 UID', 400);

  $stmt=$con->prepare('SELECT * FROM dev_requests WHERE uid=? AND deleted_at IS NULL');
  $stmt->bind_param('i',$uid); $stmt->execute(); $row=$stmt->get_result()->fetch_assoc();
  if(!$row) fail_msg('존재하지 않는 요청', 404);
  if(!can_edit_request($row)) fail_msg('권한이 없습니다.', 403);

  $title    = trim($_POST['title'] ?? '');
  $content  = trim($_POST['content'] ?? '');
  $priority = in_array($_POST['priority'] ?? '', $PRIORITY, true) ? $_POST['priority'] : $row['priority'];
  $category = trim($_POST['category'] ?? '');
  $due_at   = trim($_POST['due_at'] ?? '');
  $progress = isset($_POST['progress']) ? max(0,min(100,(int)$_POST['progress'])) : (int)$row['progress'];

  // 재요청 체크 추가
  $reopen   = ($_POST['reopen'] ?? '') === '1';

  if($title==='' || $content===''){
    $html = '<div class="alert">제목과 내용을 입력하세요.</div>' . render_form_html($_POST, $uid);
    fail_msg('검증 실패', 422, $html);
  }

  if ($reopen) {
    // 상태 포함 업데이트
    $sql='UPDATE dev_requests SET title=?,content=?,priority=?,category=?,due_at=?,progress=?,status="재요청",reopened_at=IF(reopened_at IS NULL, NOW(), reopened_at) WHERE uid=?';
    $stmt=$con->prepare($sql); $due=($due_at==='')?null:$due_at;
    $stmt->bind_param('sssssii',$title,$content,$priority,$category,$due,$progress,$uid);
  } else {
    // 기존대로
    $sql='UPDATE dev_requests SET title=?,content=?,priority=?,category=?,due_at=?,progress=? WHERE uid=?';
    $stmt=$con->prepare($sql); $due=($due_at==='')?null:$due_at;
    $stmt->bind_param('sssssii',$title,$content,$priority,$category,$due,$progress,$uid);
  }

  if(!$stmt->execute()){
    $html = '<div class="alert">수정 실패</div>' . render_form_html($_POST, $uid);
    fail_msg('DB 수정 실패: '.$stmt->error, 500, $html);
  }

  // 재요청으로 상태가 바뀐 경우 로그 남김
  if ($reopen && $con->query("SHOW TABLES LIKE 'dev_request_status_log'")->num_rows){
    $stmt=$con->prepare('INSERT INTO dev_request_status_log (uid,from_status,to_status,changed_by,note) VALUES (?,?,?,?,?)');
    $by=current_user_id();
    $from=$row['status'];
    $to='재요청';
    $note='수정 저장 시 재요청';
    $stmt->bind_param('issis',$uid,$from,$to,$by,$note);
    $stmt->execute();
  }

  $_POST['uid']=$uid;

  // [첨부 저장 - 추가]
  $replaceAll = (($_POST['replace_all'] ?? '') === '1');
  save_uploaded_files($uid, $replaceAll);

  action_show_json();
}


// === [요청 삭제] ==================================================
function action_delete_json(){
  global $con;
  // 업로드 경로(앞서 선언한 상수/변수 재사용)
  global $DEV_UPLOAD_DIR; // 예: __DIR__ . '/dev_uploads'

  $uid = (int)($_POST['uid'] ?? $_GET['uid'] ?? 0);
  if ($uid <= 0) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['result'=>false, 'error'=>['msg'=>'잘못된 UID']]);
    exit;
  }

  // 대상 로우 조회
  $stmt = $con->prepare("SELECT * FROM dev_requests WHERE uid=?");
  $stmt->bind_param('i', $uid);
  $stmt->execute();
  $row = $stmt->get_result()->fetch_assoc();
  if (!$row) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['result'=>false, 'error'=>['msg'=>'대상이 없습니다.']]);
    exit;
  }

  // 권한 체크 훅이 있으면 사용
  if (function_exists('can_delete_request') && !can_delete_request($row)) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['result'=>false, 'error'=>['msg'=>'삭제 권한이 없습니다.']]);
    exit;
  }

  // 1) 물리 파일 삭제 (DB는 FK로 지워지더라도 디스크는 남으므로 여기서 처리)
  if (function_exists('get_request_files')) {
    $files = get_request_files($uid);
    foreach ($files as $f) {
      $path = rtrim($DEV_UPLOAD_DIR, '/')."/{$uid}/".$f['stored_name'];
      if (is_file($path)) @unlink($path);
    }
    // uid 디렉토리 비었으면 제거 시도
    $udir = rtrim($DEV_UPLOAD_DIR, '/')."/{$uid}";
    if (is_dir($udir)) @rmdir($udir);
  }

  // 2) 코멘트/파일 레코드 정리 (FK 없을 수도 있으니 안전하게)
  //    FK가 걸려 있다면 아래 DELETE는 영향을 거의 안 주거나 0건일 수 있음.
  @$con->query("DELETE FROM dev_request_files WHERE uid={$uid}");
  @$con->query("DELETE FROM dev_request_comments WHERE uid={$uid}");

  // 3) 본문 삭제
  $stmt = $con->prepare("DELETE FROM dev_requests WHERE uid=?");
  $stmt->bind_param('i', $uid);
  $ok = $stmt->execute();

  if (!$ok || $stmt->affected_rows === 0) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['result'=>false, 'error'=>['msg'=>'삭제 실패']]);
    exit;
  }

  // 4) 응답: 코멘트/파일과 동일한 UX → html이 오면 root().innerHTML 교체
  action_list_json();
}
// ================================================================


function action_status_json(){
  global $con;
  if(!is_developer()) fail_msg('권한이 없습니다.', 403);

  $uid=(int)($_POST['uid'] ?? $_GET['uid'] ?? 0);
  $to = $_POST['to'] ?? $_GET['to'] ?? '';
  $note = trim($_POST['note'] ?? $_GET['note'] ?? '');
  $progress = trim($_POST['progress'] ?? $_GET['progress'] ?? '');
  if($uid<=0 || $to==='') fail_msg('잘못된 요청', 400);

  $stmt=$con->prepare('SELECT status FROM dev_requests WHERE uid=? AND deleted_at IS NULL');
  $stmt->bind_param('i',$uid); $stmt->execute(); $cur=$stmt->get_result()->fetch_assoc();
  if(!$cur) fail_msg('존재하지 않는 요청', 404);

  $from = $cur['status'];
  $dateCols=['접수중'=>'received_at','확인중'=>'started_at','진행중'=>'started_at','완료'=>'completed_at','반려'=>'rejected_at','취소'=>'canceled_at','재요청'=>'reopened_at'];
  $dateCol=$dateCols[$to]??null;

  // progress 들어오면 함께 업데이트
  $hasProgress = ($progress !== '' && is_numeric($progress));
  if ($hasProgress) $progress = max(0, min(100, (int)$progress));

  $set = 'status=?';
  if($dateCol) $set .= ", {$dateCol}=IF({$dateCol} IS NULL, NOW(), {$dateCol})";
  if($hasProgress) $set .= ', progress=?';

  $sql = "UPDATE dev_requests SET {$set} WHERE uid=?";
  $stmt = $con->prepare($sql);
  if($hasProgress){
    $stmt->bind_param('sii', $to, $progress, $uid);
  }else{
    $stmt->bind_param('si', $to, $uid);
  }
  if(!$stmt->execute()) fail_msg('상태 업데이트 실패: '.$stmt->error, 500);

  // 로그(있을 때만)
  if($con->query("SHOW TABLES LIKE 'dev_request_status_log'")->num_rows){
    $stmt=$con->prepare('INSERT INTO dev_request_status_log (uid,from_status,to_status,changed_by,note) VALUES (?,?,?,?,?)');
    $by=current_user_id();
    $stmt->bind_param('issis',$uid,$from,$to,$by,$note);
    $stmt->execute();
  }

  $_POST['uid']=$uid;
  action_show_json();
}

function action_comment_store_json(){
  global $con;
  $uid=(int)($_POST['uid'] ?? $_GET['uid'] ?? 0);
  $body=trim($_POST['body'] ?? $_GET['body'] ?? '');
  if($uid<=0 || $body==='') fail_msg('코멘트를 입력하세요.', 422);

  $con->query("CREATE TABLE IF NOT EXISTS dev_request_comments (
    comment_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uid BIGINT UNSIGNED NOT NULL,
    author_id BIGINT UNSIGNED NULL,
    author_name VARCHAR(100) NULL,
    body TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_uid_created (uid, created_at)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

  $stmt=$con->prepare('INSERT INTO dev_request_comments (uid,author_id,author_name,body) VALUES (?,?,?,?)');
  $aid=current_user_id(); $aname=current_user_name();
  $stmt->bind_param('iiss',$uid,$aid,$aname,$body);
  if(!$stmt->execute()) fail_msg('코멘트 저장 실패: '.$stmt->error, 500);

  $_POST['uid']=$uid;
  action_show_json();
}

/* =========================
   최초 진입용 최소 스크립트
   - 공통 함수 updateAjaxContent 사용 (URL: "/erp/" + pageName 내부에서 처리)
========================= */
function render_bootstrap_js(): string {
  global $pageName;
  $formHtml = json_encode(load_html_from_php(), JSON_UNESCAPED_UNICODE);
  return <<<JS
<script>
  var pageName = "{$pageName}";
(function(){
  if (window.__DEV_MIN_BOOT__) return; window.__DEV_MIN_BOOT__ = true;

  function root(){ return document.getElementById('content') || document.querySelector('.content') || document.body; }
  function collect(scope){
    const fd = new FormData();
    scope.querySelectorAll('[name]').forEach(el=>{
      if(!el.name) return;
      if(el.type === 'checkbox') fd.append(el.name, el.checked ? '1' : '0');
      else fd.append(el.name, el.value ?? '');
    });
    return fd;
  }

  // ===== SmartEditor2 로더/초기화 =====
  window.oEditors = window.oEditors || [];

  function ensureHuskyLoaded(cb){
    if (window.nhn && window.nhn.husky && window.nhn.husky.EZCreator) { cb(); return; }
    if (document.getElementById('__husky_loader__')) { // 로딩 중
      waitForHusky(cb); return;
    }
    var s = document.createElement('script');
    s.id = '__husky_loader__';
    s.src = '/erp/editor/js/service/HuskyEZCreator.js';
    s.charset = 'utf-8';
    s.onload = function(){ cb(); };
    document.head.appendChild(s);
  }

  function waitForHusky(cb){
    var t = setInterval(function(){
      if (window.nhn && window.nhn.husky && window.nhn.husky.EZCreator){
        clearInterval(t); cb();
      }
    }, 50);
    setTimeout(function(){ clearInterval(t); }, 5000);
  }

  // 기본 에디터 템플릿 (등록 화면에서 초기값으로 사용)
  function getDefaultSETemplate(){
    return {$formHtml};
  }


  function initSmartEditors(scope){
    scope = scope || root();
    ensureHuskyLoaded(function(){
      var areas = scope.querySelectorAll('textarea[data-se2]');
      areas.forEach(function(ta){
        // 이미 초기화된 요소는 건너뜀(엘리먼트 자체 플래그 사용)
        if (ta.dataset.se2Inited === '1') return;

        // id 없으면 부여
        if (!ta.id) ta.id = 'se2_' + Math.random().toString(36).slice(2,8);
		/*
        nhn.husky.EZCreator.createInIFrame({
          oAppRef: window.oEditors,
          elPlaceHolder: ta.id,
          sSkinURI: '/erp/editor/SmartEditor2Skin.html',
		  fOnAppLoad: function () {
			// 등록 진입일 때만, 초기값이 비어 있으면 기본 템플릿 주입
			if (window.__SE_NEED_DEFAULT__) {
			  var html = `
				  <style>
					.content h1 { font-size: 2rem; }
					.content p  { margin: 0 0 1em; }
				  </style>
				  <div class="content">
					<!-- 템플릿 본문 -->
				  </div>
			  `.trim();

			  try {
				// 에디터 준비 완료 → 내용 주입
				oEditors.getById[ta.id].exec("SET_IR", [html]);
			  } catch (err) {
				console.warn('[SmartEditor SET_IR 실패]', err);
			  }
			  // 1회만 적용되도록 플래그 해제
			  window.__SE_NEED_DEFAULT__ = false;
			}
		  },
		  fCreator: 'createSEditor2'
        });
		*/
        nhn.husky.EZCreator.createInIFrame({
          oAppRef: window.oEditors,
          elPlaceHolder: ta.id,
          sSkinURI: '/erp/editor/SmartEditor2Skin.html',
          fCreator: 'createSEditor2',
		  fOnAppLoad: function () {
			const doc = window.oEditors.getById[ta.id].getWYSIWYGDocument();
			// ① 외부 CSS 링크 주입
			const link = doc.createElement('link');
			link.rel = 'stylesheet';
			link.href = '<?= CSS_SRC ?>/xForm.css';
			doc.head.appendChild(link);
			// ② 필요하면 body에 클래스 부여해서 범위 한정
			doc.body.classList.add('content');
		  }
        });
        ta.dataset.se2Inited = '1';
      });
    });
  }

  function updateSmartEditors(scope){
    scope = scope || root();
    if (!window.oEditors || !window.oEditors.getById) return;
    scope.querySelectorAll('textarea[data-se2]').forEach(function(ta){
      try {
        var app = window.oEditors.getById && window.oEditors.getById[ta.id];
        if (app) app.exec('UPDATE_CONTENTS_FIELD', []);
      } catch(e) { /* ignore */ }
    });
  }

  // 최초 로드에서도 시도
  document.addEventListener('DOMContentLoaded', function(){
    initSmartEditors(root());
  });

  // ===== AJAX 내비게이션 =====
document.addEventListener('click', function(e){
  const a = e.target.closest('a.ajax-link'); const r = root();
  if (!a || !r.contains(a)) return;
  e.preventDefault();

  const act  = (a.dataset.action || 'list');
  const data = { a: act };
  if (a.dataset.uid) data.uid = a.dataset.uid;

  updateAjaxContent(data, function(res){
    if (res && typeof res.html === 'string') {
      r.innerHTML = res.html;

      // 등록(create) 화면일 때만, 에디터 초기값 주입
      if (act === 'create') {
        var ta = r.querySelector('textarea[data-se2]');
        if (ta && !ta.value.trim()) {
          ta.value = getDefaultSETemplate();
        }
      }
      initSmartEditors(r); // 새로 그려진 DOM에 대해 재초기화
    }
  }, false);
});


document.addEventListener('click', function(e){
  const btn = e.target.closest('a.ajax-submit,button.ajax-submit'); 
  const r = root();
  if (!btn || !r.contains(btn)) return;
  e.preventDefault();
  const scope = btn.closest('.ajax-scope') || r;

  updateSmartEditors(scope); // 제출 전 동기화

  const fd = collect(scope);
  const action = btn.dataset.action || 'list';   // ✅ 액션 추출
  fd.append('a', action);
  if (btn.dataset.uid) fd.append('uid', btn.dataset.uid);

  updateAjaxContent(fd, function(res){
    if (!res) return;

    if (res.result === true) {
      // 성공
      if (typeof res.html === 'string') {
        r.innerHTML = res.html;
        initSmartEditors(r);
      }
      // ✅ 검색(list)일 땐 메시지 제외
      if (action !== 'list') {
        alert('처리되었습니다.');
      }
    } else {
      // 실패
      let msg = (res.error && res.error.msg) ? res.error.msg : '알 수 없는 오류가 발생했습니다.';
      alert(msg);
      if (typeof res.html === 'string' && res.html) {
        r.innerHTML = res.html;
        initSmartEditors(r);
      }
    }
  }, false);
});

  // “등록” 버튼 클릭 시: 폼이 로드되면 se_content에 기본 템플릿 주입
  document.addEventListener('click', function (e) {
    const a = e.target.closest('a.btn.primary.ajax-link[data-action="create"]');
    if (!a) return;
	window.__SE_NEED_DEFAULT__ = true;
    // a 안에 <button>이 있어도 anchor가 기본 클릭 타겟임
    e.preventDefault();

    // 폼은 AJAX로 뜰 것이므로, textarea가 등장할 때까지 대기
    waitForEl('#se_content', function () {
      initSmartEditor('se_content', getDefaultTemplate());
    });
  });

// root() 없는 환경 방어
if (typeof window.root !== 'function') {
  window.root = () => document.querySelector('#content');
}
function initSmartEditorsSafe(scope){
  try { initSmartEditors(scope); } catch(e) {}
}

// === [첨부: 추가 업로드] =========================================
document.addEventListener('click', function(e){
  const btn = e.target.closest('.file-upload-button');
  if (!btn) return;
  e.preventDefault();
  e.stopPropagation();
  e.stopImmediatePropagation(); // 전역 .ajax-submit 차단

  const form = btn.closest('form.file-upload-form');
  if (!form) return;

  // 1) 파일 미선택 시 종료
  const fileInput = form.querySelector('input[type="file"][name="attachments[]"]');
  if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
    alert('업로드할 파일을 선택하세요.');
    fileInput && fileInput.focus();
    return;
  }

  const fd = new FormData(form);
  updateAjaxContent(fd, function(res){
    if (!res) return;
    if (res.result === true) {
      if (typeof res.html === 'string') {
        root().innerHTML = res.html;
        initSmartEditorsSafe(root());
      }
      alert('업로드되었습니다.');
    } else {
      alert((res.error && res.error.msg) ? res.error.msg : '업로드 실패');
      if (typeof res.html === 'string' && res.html) {
        root().innerHTML = res.html;
        initSmartEditorsSafe(root());
      }
    }
  }, false);
}, true);

// === [첨부: 삭제] ===============================================
document.addEventListener('click', function(e){
  const a = e.target.closest('.file-del-link');
  if (!a) return;
  e.preventDefault();
  e.stopPropagation();
  e.stopImmediatePropagation(); // 전역 .ajax-submit 차단

  if (!confirm('삭제하시겠습니까?')) return;

  const fd = new FormData();
  fd.append('a','file_delete');
  fd.append('file_id', a.dataset.fileId);
  if (a.dataset.uid) fd.append('uid', a.dataset.uid);

  updateAjaxContent(fd, function(res){
    if (!res) return;
    if (res.result === true) {
      if (typeof res.html === 'string') {
        // 코멘트처럼 현재 페이지 유지 + 전체 루트만 갱신
        root().innerHTML = res.html;
        initSmartEditorsSafe(root());
      } else {
        // html 없으면 최소 동작: 해당 li만 제거
        const li = a.closest('li');
        const ul = li && li.parentElement;
        if (li) li.remove();
        if (ul && ul.children.length === 0) {
          const empty = document.createElement('div');
          empty.className = 'muted';
          empty.style.marginTop = '8px';
          empty.textContent = '첨부 없음';
          ul.replaceWith(empty);
        }
      }
      alert('삭제되었습니다.');
    } else {
      alert((res.error && res.error.msg) ? res.error.msg : '삭제 실패');
      if (typeof res.html === 'string' && res.html) {
        root().innerHTML = res.html;
        initSmartEditorsSafe(root());
      }
    }
  }, false);
}, true);


// 코멘트: 편집 토글
document.addEventListener('click', function(e){
  const a = e.target.closest('.comment-edit-link');
  if (!a) return;
  e.preventDefault();
  const cid = a.dataset.cid;
  const li = document.getElementById('comment-' + cid);
  if (!li) return;
  const view = li.querySelector('.comment-view');
  const edit = li.querySelector('.comment-edit-area');
  if (view && edit) { view.style.display = 'none'; edit.style.display = ''; }
});

// 코멘트: 편집 취소
document.addEventListener('click', function(e){
  const a = e.target.closest('.comment-cancel-link');
  if (!a) return;
  e.preventDefault();
  const cid = a.dataset.cid;
  const li = document.getElementById('comment-' + cid);
  if (!li) return;
  const view = li.querySelector('.comment-view');
  const edit = li.querySelector('.comment-edit-area');
  if (view && edit) { edit.style.display = 'none'; view.style.display = ''; }
});

// 코멘트: 삭제 확인 후 진행
document.addEventListener('click', function(e){
  const a = e.target.closest('.comment-del-link');
  if (!a) return;
  e.preventDefault();
  if (!confirm('이 코멘트를 삭제하시겠습니까?')) return;

  const fd = new FormData();
  fd.append('a', 'comment_delete');
  fd.append('uid', a.dataset.uid);
  fd.append('comment_id', a.dataset.cid);

  updateAjaxContent(fd, function(res){
    if (!res) return;
    if (res.result === true) {
      if (typeof res.html === 'string') root().innerHTML = res.html;
      initSmartEditors(root());
      alert('삭제되었습니다.');
    } else {
      alert((res.error && res.error.msg) ? res.error.msg : '삭제 실패');
      if (typeof res.html === 'string' && res.html) {
        root().innerHTML = res.html; initSmartEditors(root());
      }
    }
  }, false);
});

})();
</script>
JS;
}


function can_edit_request($row){
  return is_developer() || ((int)($row['requester_id'] ?? 0) === (int)current_user_id());
}

function can_delete_request($row){
  return is_developer() || ((int)($row['requester_id'] ?? 0) === (int)current_user_id());
}

