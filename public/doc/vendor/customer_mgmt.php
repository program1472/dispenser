<?php
// dispenser/doc/vendor/customer_mgmt.php
// 규약: GET=본문 출력, POST=핸들러 실행 후 Finish()
// 주: 공통 로딩은 index.php에서 처리 (본 파일에서는 하지 않음)

// --- POST 핸들러 -----------------------------------------------------------
if (!empty($_POST)) {
  // 공통 규약: POST 박스 정규화
  $POST_BOX = $_POST['NW'] ?? $_POST['OG'] ?? $_POST;
  $action   = $POST_BOX['action']  ?? '';
  $payload  = $POST_BOX['payload'] ?? '';
  if (is_string($payload)) {
    $decoded = json_decode($payload, true);
    if (json_last_error() === JSON_ERROR_NONE) { $payload = $decoded; }
  }

  // 전역 $response 사용 (index/_ajax_에서 초기화됨 가정)
  global $response; if (!is_array($response)) { $response = []; }

  // 유틸: Finish() 존재 가정 (inc/functions/JsonHelper.php)
  if (!function_exists('Finish')) {
    function Finish() { global $response; header('Content-Type: application/json; charset=utf-8'); echo json_encode($response, JSON_UNESCAPED_UNICODE); exit; }
  }

  // 유틸: 안전 값
  $vendorId = $payload['vendor_id'] ?? ($POST_BOX['vendor_id'] ?? '');

  // 액션 스위치
  switch ($action) {
    case 'VND_CUSTOMER_LIST': {
      // 실제 DB 연동이 없다면 데모 데이터를 반환 (UI 점검용)
      $rows = demo_customers();
      // 간단한 필터 (q/type/state)
      $q = trim(strval($payload['q'] ?? ''));
      $type = trim(strval($payload['type'] ?? ''));
      $state = trim(strval($payload['state'] ?? ''));
      $filtered = array_values(array_filter($rows, function($r) use($q,$type,$state){
        $ok = true;
        if ($q !== '') {
          $hay = $r['id'].' '.$r['name'].' '.$r['serials'].' '.$r['phone'].' '.$r['addr'];
          $ok = $ok && (mb_stripos($hay, $q) !== false);
        }
        if ($type !== '') $ok = $ok && ($r['biz'] === $type);
        if ($state !== '') $ok = $ok && ($r['remain_state'] === $state);
        return $ok;
      }));
      $response['result'] = true;
      $response['item']['rows'] = $filtered;
      return Finish();
    }
    case 'VND_CUSTOMER_DETAIL': {
      $id = strval($payload['customer_id'] ?? '');
      $rows = demo_customers();
      $row = null; foreach ($rows as $r) { if ($r['id'] === $id) { $row = $r; break; } }
      if (!$row) { $response['result']=false; $response['error']['msg']='고객을 찾을 수 없습니다.'; $response['error']['code']=404; return Finish(); }
      $response['result']=true;
      $response['item'] = [
        'customer'=>$row,
        'devices'=>demo_devices_for($id),
        'thumbs'=>demo_thumbs(),
        'extraSum'=>0,
        'payLink'=>'-'
      ];
      return Finish();
    }
    case 'VND_UPLOAD_IMAGE': {
      // 실제 업로드는 서버별 구현 필요. 여기서는 성공 시뮬레이션
      $name = $payload['filename'] ?? 'upload.jpg';
      $response['result'] = true;
      $response['msg'] = '업로드 완료: '.$name;
      return Finish();
    }
    case 'VND_NEW_CUSTOMER_SAVE': {
      // 필수값 검증(간단)
      $n = $payload['new'] ?? [];
      $required = ['user','company','manager','email','phone','biz','qty','addr'];
      foreach ($required as $k) {
        if (empty($n[$k])) { $response['result']=false; $response['error']['msg']='필수값 누락: '.$k; $response['error']['code']=400; return Finish(); }
      }
      // 저장 시뮬레이션 응답
      $response['result'] = true;
      $response['msg'] = '신규 고객 등록이 접수되었습니다.';
      $response['item']['customer_id'] = 'C'.date('Ymd').rand(100,999);
      return Finish();
    }
    default: {
      $response['result'] = false;
      $response['error']['msg'] = '지원하지 않는 요청입니다.';
      $response['error']['code'] = 400;
      return Finish();
    }
  }
}

// --- 데모 데이터 생성기 -----------------------------------------------------
function demo_customers() {
  // 사용자가 제공한 그리드 예시를 서버 측 데이터로 반영
  $rows = [
    ['id'=>'CUST001','name'=>'고객1','biz'=>'병원','qty'=>2,'serials'=>'S479510, S849443','period'=>'2025-02-12 ~ 2026-02-12','remain'=>'113일','remain_state'=>'활성','phone'=>'010-7286-7550','addr'=>'서울시 중구 세종대로 66'],
    ['id'=>'CUST002','name'=>'고객2','biz'=>'골프장','qty'=>6,'serials'=>'S678653, S861687, S934180, S881722, S259131, S822677','period'=>'2025-04-28 ~ 2026-04-28','remain'=>'188일','remain_state'=>'활성','phone'=>'010-7541-6162','addr'=>'-'],
    ['id'=>'CUST003','name'=>'고객3','biz'=>'골프장','qty'=>6,'serials'=>'S672931, S714549, S660241, S414677, S163471, S712746','period'=>'2025-07-10 ~ 2026-07-10','remain'=>'261일','remain_state'=>'활성','phone'=>'010-1510-3813','addr'=>'-'],
    ['id'=>'CUST004','name'=>'고객4','biz'=>'호텔','qty'=>5,'serials'=>'S773170, S772005, S923156, S522234, S875481','period'=>'2025-05-18 ~ 2026-05-18','remain'=>'208일','remain_state'=>'활성','phone'=>'010-1277-5241','addr'=>'-'],
    ['id'=>'CUST005','name'=>'고객5','biz'=>'병원','qty'=>4,'serials'=>'S832206, S696636, S814307, S252270','period'=>'2025-04-05 ~ 2026-04-05','remain'=>'165일','remain_state'=>'활성','phone'=>'010-1116-6471','addr'=>'-'],
    ['id'=>'CUST006','name'=>'고객6','biz'=>'카페','qty'=>2,'serials'=>'S820424, S863238','period'=>'2025-01-10 ~ 2026-01-10','remain'=>'80일','remain_state'=>'만료예정','phone'=>'010-2263-9015','addr'=>'-'],
    ['id'=>'CUST007','name'=>'고객7','biz'=>'골프장','qty'=>3,'serials'=>'S437498, S636012, S135345','period'=>'2025-07-10 ~ 2026-07-10','remain'=>'261일','remain_state'=>'활성','phone'=>'010-6202-2314','addr'=>'-'],
    ['id'=>'CUST008','name'=>'고객8','biz'=>'골프장','qty'=>3,'serials'=>'S676295, S261452, S578379','period'=>'2025-08-02 ~ 2026-08-02','remain'=>'284일','remain_state'=>'활성','phone'=>'010-4314-2218','addr'=>'-'],
    ['id'=>'CUST009','name'=>'고객9','biz'=>'예식장','qty'=>4,'serials'=>'S846009, S783168, S990535, S627234','period'=>'2025-06-29 ~ 2026-06-29','remain'=>'250일','remain_state'=>'활성','phone'=>'010-9213-9727','addr'=>'-'],
    ['id'=>'CUST010','name'=>'고객10','biz'=>'예식장','qty'=>6,'serials'=>'S935058, S162542, S602328, S621165, S810400, S456599','period'=>'2025-07-25 ~ 2026-07-25','remain'=>'276일','remain_state'=>'활성','phone'=>'010-4839-2875','addr'=>'-'],
    ['id'=>'CUST011','name'=>'고객11','biz'=>'골프장','qty'=>6,'serials'=>'S809183, S502519, S684515, S710343, S560447, S476531','period'=>'2024-12-15 ~ 2025-12-15','remain'=>'54일','remain_state'=>'만료예정','phone'=>'010-6304-3414','addr'=>'-'],
    ['id'=>'CUST012','name'=>'고객12','biz'=>'병원','qty'=>5,'serials'=>'S702322, S685649, S599984, S493951, S998085','period'=>'2025-07-22 ~ 2026-07-22','remain'=>'273일','remain_state'=>'활성','phone'=>'010-5553-7282','addr'=>'-'],
    ['id'=>'CUST013','name'=>'고객13','biz'=>'예식장','qty'=>5,'serials'=>'S588295, S315080, S865462, S190432, S791846','period'=>'2025-04-02 ~ 2026-04-02','remain'=>'162일','remain_state'=>'활성','phone'=>'010-8658-6551','addr'=>'-'],
    ['id'=>'CUST014','name'=>'고객14','biz'=>'카페','qty'=>4,'serials'=>'S518923, S487548, S300042, S757678','period'=>'2024-12-18 ~ 2025-12-18','remain'=>'57일','remain_state'=>'만료예정','phone'=>'010-7274-1347','addr'=>'-'],
    ['id'=>'CUST015','name'=>'고객15','biz'=>'카페','qty'=>3,'serials'=>'S706933, S532685, S756315','period'=>'2025-06-26 ~ 2026-06-26','remain'=>'247일','remain_state'=>'활성','phone'=>'010-1171-6737','addr'=>'-'],
    ['id'=>'CUST016','name'=>'고객16','biz'=>'골프장','qty'=>2,'serials'=>'S662048, S232998','period'=>'2025-01-02 ~ 2026-01-02','remain'=>'72일','remain_state'=>'만료예정','phone'=>'010-5652-2518','addr'=>'-'],
    ['id'=>'CUST017','name'=>'고객17','biz'=>'병원','qty'=>5,'serials'=>'S516862, S379795, S638776, S958995, S642787','period'=>'2025-09-14 ~ 2026-09-14','remain'=>'327일','remain_state'=>'활성','phone'=>'010-1494-2338','addr'=>'-'],
    ['id'=>'CUST018','name'=>'고객18','biz'=>'호텔','qty'=>2,'serials'=>'S974358, S995942','period'=>'2025-09-18 ~ 2026-09-18','remain'=>'331일','remain_state'=>'활성','phone'=>'010-7692-4219','addr'=>'-'],
    ['id'=>'CUST019','name'=>'고객19','biz'=>'예식장','qty'=>2,'serials'=>'S290325, S652936','period'=>'2024-12-26 ~ 2025-12-26','remain'=>'65일','remain_state'=>'만료예정','phone'=>'010-5223-5787','addr'=>'-'],
    ['id'=>'CUST020','name'=>'고객20','biz'=>'예식장','qty'=>4,'serials'=>'S494416, S902247, S481620, S342433','period'=>'2025-07-25 ~ 2026-07-25','remain'=>'276일','remain_state'=>'활성','phone'=>'010-2025-4629','addr'=>'-'],
    ['id'=>'CUST021','name'=>'고객21','biz'=>'병원','qty'=>5,'serials'=>'S411469, S699050, S372752, S556967, S814844','period'=>'2024-12-15 ~ 2025-12-15','remain'=>'54일','remain_state'=>'만료예정','phone'=>'010-4989-7295','addr'=>'-'],
    ['id'=>'CUST022','name'=>'고객22','biz'=>'예식장','qty'=>6,'serials'=>'S864414, S474058, S709233, S954748, S714736, S556822','period'=>'2025-09-21 ~ 2026-09-21','remain'=>'334일','remain_state'=>'활성','phone'=>'010-6851-9233','addr'=>'-'],
    ['id'=>'CUST023','name'=>'고객23','biz'=>'예식장','qty'=>2,'serials'=>'S927908, S502156','period'=>'2025-04-19 ~ 2026-04-19','remain'=>'179일','remain_state'=>'활성','phone'=>'010-5666-5661','addr'=>'-'],
    ['id'=>'CUST024','name'=>'고객24','biz'=>'예식장','qty'=>3,'serials'=>'S842146, S978909, S366988','period'=>'2025-04-24 ~ 2026-04-24','remain'=>'184일','remain_state'=>'활성','phone'=>'010-7229-1296','addr'=>'-'],
  ];
  return $rows;
}
function demo_devices_for($custId) {
  // 간단 더미 (CUST001만 상세 예시 제공)
  if ($custId === 'CUST001') {
    return [
      ['no'=>1,'serial'=>'S479510','install_at'=>'2025-02-18','sub_from'=>'2025-02-12','sub_to'=>'2026-02-12','place'=>'카운터','ship_req'=>'2025-10-22'],
      ['no'=>2,'serial'=>'S849443','install_at'=>'2025-02-20','sub_from'=>'2025-02-12','sub_to'=>'2026-02-12','place'=>'라운지','ship_req'=>'2025-10-22'],
    ];
  }
  return [];
}
function demo_thumbs() {
  // 썸네일(향/콘텐츠) 더미
  $arr = [];
  for ($i=1;$i<=8;$i++) { $arr[] = ['name'=>'썸네일 '.$i]; }
  return $arr;
}

// --- GET: 본문 출력 ---------------------------------------------------------
?>

<div class="toolbar row" style="justify-content:space-between;margin-bottom:8px">
  <div class="row">
    <input id="qCust" class="input" placeholder="고객명/ID/주소 검색"/>
    <select id="fType" class="select"><option value="">전체 업종</option><option>골프장</option><option>예식장</option><option>병원</option><option>호텔</option><option>카페</option></select>
    <select id="fState" class="select"><option value="">전체 계약상태</option><option>활성</option><option>만료예정</option><option>해지</option></select>
  </div>
  <div class="row">
    <button class="btn" id="btnExport">CSV</button>
    <button class="btn primary" id="btnNewCust">신규 고객 등록</button>
  </div>
</div>

<div class="table-wrap">
<table id="grid" class="table"><thead><tr>
        <th>ID</th><th>고객명</th><th>업종</th><th>설치 대수</th><th>시리얼</th><th>계약</th><th>남은기간</th><th>연락처</th><th>상세</th>
      </tr></thead><tbody><tr>
      <td>CUST001</td>
      <td>고객1</td>
      <td>병원</td>
      <td>2</td>
      <td>S479510, S849443</td>
      <td>2025-02-12 ~ 2026-02-12</td>
      <td>113일 <span class="badge ">활성</span></td>
      <td>010-7286-7550</td>
      <td><button class="btn" data-open="CUST001">상세</button></td></tr><tr>
      <td>CUST002</td>
      <td>고객2</td>
      <td>골프장</td>
      <td>6</td>
      <td>S678653, S861687, S934180, S881722, S259131, S822677</td>
      <td>2025-04-28 ~ 2026-04-28</td>
      <td>188일 <span class="badge ">활성</span></td>
      <td>010-7541-6162</td>
      <td><button class="btn" data-open="CUST002">상세</button></td></tr><tr>
      <td>CUST003</td>
      <td>고객3</td>
      <td>골프장</td>
      <td>6</td>
      <td>S672931, S714549, S660241, S414677, S163471, S712746</td>
      <td>2025-07-10 ~ 2026-07-10</td>
      <td>261일 <span class="badge ">활성</span></td>
      <td>010-1510-3813</td>
      <td><button class="btn" data-open="CUST003">상세</button></td></tr><tr>
      <td>CUST004</td>
      <td>고객4</td>
      <td>호텔</td>
      <td>5</td>
      <td>S773170, S772005, S923156, S522234, S875481</td>
      <td>2025-05-18 ~ 2026-05-18</td>
      <td>208일 <span class="badge ">활성</span></td>
      <td>010-1277-5241</td>
      <td><button class="btn" data-open="CUST004">상세</button></td></tr><tr>
      <td>CUST005</td>
      <td>고객5</td>
      <td>병원</td>
      <td>4</td>
      <td>S832206, S696636, S814307, S252270</td>
      <td>2025-04-05 ~ 2026-04-05</td>
      <td>165일 <span class="badge ">활성</span></td>
      <td>010-1116-6471</td>
      <td><button class="btn" data-open="CUST005">상세</button></td></tr><tr>
      <td>CUST006</td>
      <td>고객6</td>
      <td>카페</td>
      <td>2</td>
      <td>S820424, S863238</td>
      <td>2025-01-10 ~ 2026-01-10</td>
      <td>80일 <span class="badge badge-expire">만료예정</span></td>
      <td>010-2263-9015</td>
      <td><button class="btn" data-open="CUST006">상세</button></td></tr><tr>
      <td>CUST007</td>
      <td>고객7</td>
      <td>골프장</td>
      <td>3</td>
      <td>S437498, S636012, S135345</td>
      <td>2025-07-10 ~ 2026-07-10</td>
      <td>261일 <span class="badge ">활성</span></td>
      <td>010-6202-2314</td>
      <td><button class="btn" data-open="CUST007">상세</button></td></tr><tr>
      <td>CUST008</td>
      <td>고객8</td>
      <td>골프장</td>
      <td>3</td>
      <td>S676295, S261452, S578379</td>
      <td>2025-08-02 ~ 2026-08-02</td>
      <td>284일 <span class="badge ">활성</span></td>
      <td>010-4314-2218</td>
      <td><button class="btn" data-open="CUST008">상세</button></td></tr><tr>
      <td>CUST009</td>
      <td>고객9</td>
      <td>예식장</td>
      <td>4</td>
      <td>S846009, S783168, S990535, S627234</td>
      <td>2025-06-29 ~ 2026-06-29</td>
      <td>250일 <span class="badge ">활성</span></td>
      <td>010-9213-9727</td>
      <td><button class="btn" data-open="CUST009">상세</button></td></tr><tr>
      <td>CUST010</td>
      <td>고객10</td>
      <td>예식장</td>
      <td>6</td>
      <td>S935058, S162542, S602328, S621165, S810400, S456599</td>
      <td>2025-07-25 ~ 2026-07-25</td>
      <td>276일 <span class="badge ">활성</span></td>
      <td>010-4839-2875</td>
      <td><button class="btn" data-open="CUST010">상세</button></td></tr><tr>
      <td>CUST011</td>
      <td>고객11</td>
      <td>골프장</td>
      <td>6</td>
      <td>S809183, S502519, S684515, S710343, S560447, S476531</td>
      <td>2024-12-15 ~ 2025-12-15</td>
      <td>54일 <span class="badge badge-expire">만료예정</span></td>
      <td>010-6304-3414</td>
      <td><button class="btn" data-open="CUST011">상세</button></td></tr><tr>
      <td>CUST012</td>
      <td>고객12</td>
      <td>병원</td>
      <td>5</td>
      <td>S702322, S685649, S599984, S493951, S998085</td>
      <td>2025-07-22 ~ 2026-07-22</td>
      <td>273일 <span class="badge ">활성</span></td>
      <td>010-5553-7282</td>
      <td><button class="btn" data-open="CUST012">상세</button></td></tr><tr>
      <td>CUST013</td>
      <td>고객13</td>
      <td>예식장</td>
      <td>5</td>
      <td>S588295, S315080, S865462, S190432, S791846</td>
      <td>2025-04-02 ~ 2026-04-02</td>
      <td>162일 <span class="badge ">활성</span></td>
      <td>010-8658-6551</td>
      <td><button class="btn" data-open="CUST013">상세</button></td></tr><tr>
      <td>CUST014</td>
      <td>고객14</td>
      <td>카페</td>
      <td>4</td>
      <td>S518923, S487548, S300042, S757678</td>
      <td>2024-12-18 ~ 2025-12-18</td>
      <td>57일 <span class="badge badge-expire">만료예정</span></td>
      <td>010-7274-1347</td>
      <td><button class="btn" data-open="CUST014">상세</button></td></tr><tr>
      <td>CUST015</td>
      <td>고객15</td>
      <td>카페</td>
      <td>3</td>
      <td>S706933, S532685, S756315</td>
      <td>2025-06-26 ~ 2026-06-26</td>
      <td>247일 <span class="badge ">활성</span></td>
      <td>010-1171-6737</td>
      <td><button class="btn" data-open="CUST015">상세</button></td></tr><tr>
      <td>CUST016</td>
      <td>고객16</td>
      <td>골프장</td>
      <td>2</td>
      <td>S662048, S232998</td>
      <td>2025-01-02 ~ 2026-01-02</td>
      <td>72일 <span class="badge badge-expire">만료예정</span></td>
      <td>010-5652-2518</td>
      <td><button class="btn" data-open="CUST016">상세</button></td></tr><tr>
      <td>CUST017</td>
      <td>고객17</td>
      <td>병원</td>
      <td>5</td>
      <td>S516862, S379795, S638776, S958995, S642787</td>
      <td>2025-09-14 ~ 2026-09-14</td>
      <td>327일 <span class="badge ">활성</span></td>
      <td>010-1494-2338</td>
      <td><button class="btn" data-open="CUST017">상세</button></td></tr><tr>
      <td>CUST018</td>
      <td>고객18</td>
      <td>호텔</td>
      <td>2</td>
      <td>S974358, S995942</td>
      <td>2025-09-18 ~ 2026-09-18</td>
      <td>331일 <span class="badge ">활성</span></td>
      <td>010-7692-4219</td>
      <td><button class="btn" data-open="CUST018">상세</button></td></tr><tr>
      <td>CUST019</td>
      <td>고객19</td>
      <td>예식장</td>
      <td>2</td>
      <td>S290325, S652936</td>
      <td>2024-12-26 ~ 2025-12-26</td>
      <td>65일 <span class="badge badge-expire">만료예정</span></td>
      <td>010-5223-5787</td>
      <td><button class="btn" data-open="CUST019">상세</button></td></tr><tr>
      <td>CUST020</td>
      <td>고객20</td>
      <td>예식장</td>
      <td>4</td>
      <td>S494416, S902247, S481620, S342433</td>
      <td>2025-07-25 ~ 2026-07-25</td>
      <td>276일 <span class="badge ">활성</span></td>
      <td>010-2025-4629</td>
      <td><button class="btn" data-open="CUST020">상세</button></td></tr><tr>
      <td>CUST021</td>
      <td>고객21</td>
      <td>병원</td>
      <td>5</td>
      <td>S411469, S699050, S372752, S556967, S814844</td>
      <td>2024-12-15 ~ 2025-12-15</td>
      <td>54일 <span class="badge badge-expire">만료예정</span></td>
      <td>010-4989-7295</td>
      <td><button class="btn" data-open="CUST021">상세</button></td></tr><tr>
      <td>CUST022</td>
      <td>고객22</td>
      <td>예식장</td>
      <td>6</td>
      <td>S864414, S474058, S709233, S954748, S714736, S556822</td>
      <td>2025-09-21 ~ 2026-09-21</td>
      <td>334일 <span class="badge ">활성</span></td>
      <td>010-6851-9233</td>
      <td><button class="btn" data-open="CUST022">상세</button></td></tr><tr>
      <td>CUST023</td>
      <td>고객23</td>
      <td>예식장</td>
      <td>2</td>
      <td>S927908, S502156</td>
      <td>2025-04-19 ~ 2026-04-19</td>
      <td>179일 <span class="badge ">활성</span></td>
      <td>010-5666-5661</td>
      <td><button class="btn" data-open="CUST023">상세</button></td></tr><tr>
      <td>CUST024</td>
      <td>고객24</td>
      <td>예식장</td>
      <td>3</td>
      <td>S842146, S978909, S366988</td>
      <td>2025-04-24 ~ 2026-04-24</td>
      <td>184일 <span class="badge ">활성</span></td>
      <td>010-7229-1296</td>
      <td><button class="btn" data-open="CUST024">상세</button></td></tr></tbody></table>
</div>

<!-- 고객 상세 모달 -->
<div class="modal" id="mdCust">
  <div class="panel">
    <div class="panel-hd">
      <div>
        <div style="font-weight:800;color:var(--accent)" id="mdCustName">고객 상세</div>
        <div class="small" id="mdCustMeta">-</div>
      </div>
      <div class="row">
        <button class="btn" id="btnAddDevice">기기 추가</button>
        <button class="btn primary" id="btnMakePay">전체 결제링크 생성</button>
        <span class="close" data-close>✕</span>
      </div>
    </div>
    <div class="panel-bd">
      <div class="grid-2">
        <div>
          <div class="row"><div style="font-weight:700;color:var(--accent)">기기/설치</div><span class="small">(행 클릭 시 선택 기기의 향/콘텐츠 썸네일 표시)</span></div>
          <div class="table-wrap" style="margin-top:6px">
            <table class="table" id="tblDevices"><thead><tr><th>#</th><th>시리얼</th><th>설치일</th><th>구독 시작</th><th>종료</th><th>설치 위치</th><th>배송 요청일</th></tr></thead><tbody></tbody></table>
          </div>
          <div class="small" style="margin-top:6px">※ 향 6종 무상 제공 · 설치일 기준 2개월마다 자동 공급</div>
        </div>
        <div>
          <div class="row"><div style="font-weight:700;color:var(--accent)">선택 기기 — 향/콘텐츠</div><span class="small">(모든 카트리지·콘텐츠 썸네일)</span></div>
          <div id="thumbBox" class="grid-auto" style="margin-top:8px"><div class="small">왼쪽에서 기기를 선택하세요.</div></div>
          <hr class="sep">
          <div class="row">
            <input type="file" id="uploadImg" class="input" accept="image/*">
            <button class="btn" id="btnUpload">업로드(프린팅 요청)</button>
            <div class="small" id="uploadStatus"></div>
          </div>
          <div id="thumbPreview" class="thumb" style="margin-top:8px">업로드 미리보기</div>
        </div>
      </div>
      <hr class="sep">
      <div>
        <div style="font-weight:700;color:var(--accent);margin-bottom:6px">배송/청구 요약</div>
        <div class="row">
          <div class="small">추가요금 합계: <b id="extraSum">₩0</b></div>
          <div class="small">결제링크: <a id="payLink" target="_blank">-</a></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 신규 고객 등록 모달 -->
<div class="modal" id="mdNew">
  <div class="panel">
    <div class="panel-hd">
      <div>
        <div style="font-weight:800;color:var(--accent)">신규 고객 등록</div>
        <div class="small">※ 이 화면에서는 오일/콘텐츠 각 1개만 선택 가능(추가 생성은 고객 포털에서)</div>
      </div>
      <div class="row">
        <button class="btn primary" id="btnSaveNew">등록 완료</button>
        <span class="close" data-close>✕</span>
      </div>
    </div>
    <div class="panel-bd">
      <div class="grid-2">
        <div>
          <div style="font-weight:700;color:var(--accent);margin-bottom:6px">고객 정보</div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
            <input id="nUser" class="input" placeholder="회원 아이디">
            <input id="nCompany" class="input" placeholder="회사명">
            <input id="nManager" class="input" placeholder="담당자명">
            <input id="nEmail" class="input" placeholder="대표이메일">
            <input id="nPhone" class="input" placeholder="연락처">
            <input id="nBiz" class="input" placeholder="업종(예: 골프장)">
            <input id="nQty" type="number" class="input" placeholder="구매기기대수">
            <input id="nAddr" class="input" placeholder="주소">
            <textarea id="nMemo" class="input" style="grid-column:1/-1" placeholder="배송요청"></textarea>
          </div>
        </div>
        <div>
          <div class="row" style="justify-content:space-between">
            <div style="font-weight:700;color:var(--accent)">기기 및 콘텐츠 입력</div>
            <div class="small">배송 요청일 · 오일 · 콘텐츠 · 기타 · 배송지 · 담당자</div>
          </div>
          <div class="table-wrap" style="margin-top:6px">
            <table class="table" id="tblNewDevices"><thead><tr><th>#</th><th>배송요청일</th><th>오일</th><th>상품코드</th><th>콘텐츠</th><th>구분</th><th>상품코드</th><th>업로드</th><th>기타</th><th>추가요금</th><th>배송지</th><th>담당자</th><th>연락처</th></tr></thead><tbody></tbody></table>
          </div>
          <div class="row" style="margin-top:8px">
            <button id="btnOilPick" class="btn">요청오일 선택</button>
            <button id="btnContPick" class="btn">요청콘텐츠 선택</button>
            <div class="small">선택 결과: <span id="pickOil">-</span> / <span id="pickContent">-</span></div>
          </div>
          <div class="row" style="margin-top:8px">
            <button class="btn primary" id="btnGenRows">대수만큼 행 생성</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  const pageName = 'doc/vendor/customer_mgmt.php';
  const menuName = 'customer_mgmt';
  const $ = (s,el=document)=>el.querySelector(s);
  const $$ = (s,el=document)=>Array.from(el.querySelectorAll(s));

  function toast(msg){
    let wrap = document.querySelector('.toast-wrap');
    if(!wrap){ wrap = document.createElement('div'); wrap.className='toast-wrap'; document.body.appendChild(wrap); }
    const t = document.createElement('div'); t.className='toast'; t.textContent=msg; wrap.appendChild(t);
    setTimeout(()=>{ t.remove(); if(!wrap.children.length) wrap.remove(); }, 2200);
  }

  function post(action, payload={}){
    const params = new URLSearchParams();
    params.append('action', action);
    params.append('payload', JSON.stringify(payload));
    params.append('pageName', pageName);
    params.append('menuName', menuName);
    return fetch(pageName, { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: params.toString() })
      .then(r=>r.json());
  }

  // 필터/검색 → 서버(데모) 조회 후 테이블 재그리기
  function refresh(){
    const q = $('#qCust')?.value?.trim()||'';
    const type = $('#fType')?.value||'';
    const state = $('#fState')?.value||'';
    post('VND_CUSTOMER_LIST', { q, type, state }).then(data=>{
      const r = data?.result; const ok = (r===true||r===1||r==='ok'||r==='OK'||r==='success'||r==='SUCCESS');
      if(!ok){ toast(data?.error?.msg||'목록 조회 실패'); return; }
      const tb = $('#grid tbody'); if(!tb) return;
      tb.innerHTML = data.item.rows.map(row => `
        <tr>
          <td>${row.id}</td>
          <td>${row.name}</td>
          <td>${row.biz}</td>
          <td>${row.qty}</td>
          <td>${row.serials}</td>
          <td>${row.period}</td>
          <td>${row.remain} <span class="badge ${row.remain_state==='만료예정'?'badge-expire':''}">${row.remain_state}</span></td>
          <td>${row.phone}</td>
          <td><button class="btn" data-open="${row.id}">상세</button></td>
        </tr>`).join('');
    }).catch(()=>toast('서버 통신 오류'));
  }

  // 상세 모달 열기
  function openDetail(id){
    post('VND_CUSTOMER_DETAIL', { customer_id:id }).then(data=>{
      const okVals=[true,1,'ok','OK','success','SUCCESS']; if(!okVals.includes(data?.result)){ toast(data?.error?.msg||'상세 불러오기 실패'); return; }
      const c = data.item.customer;
      $('#mdCustName').textContent = `${c.name} (${c.id})`;
      $('#mdCustMeta').textContent = `${c.biz} · ${c.addr||'-'} · ${c.phone||'-'}`;
      // devices
      const dt = $('#tblDevices tbody');
      dt.innerHTML = (data.item.devices||[]).map((d,i)=>`
        <tr style="cursor:pointer" data-serial="${d.serial}"><td>${d.no||i+1}</td><td>${d.serial}</td><td>${d.install_at||'-'}</td><td>${d.sub_from||'-'}</td><td>${d.sub_to||'-'}</td><td>${d.place||'-'}</td><td>${d.ship_req||'-'}</td></tr>
      `).join('');
      // thumbs (기본)
      const tb = $('#thumbBox'); tb.innerHTML='';
      (data.item.thumbs||[]).forEach(t=>{ const div=document.createElement('div'); div.className='thumb'; div.textContent=t.name; tb.appendChild(div); });
      $('#extraSum').textContent = '₩' + (data.item.extraSum||0);
      const link = data.item.payLink||'-';
      const a = $('#payLink'); a.textContent = link; a.href = (link&&link!=='-')?link:'#';
      $('#mdCust').style.display='flex';
    }).catch(()=>toast('서버 통신 오류'));
  }

  // 이벤트 바인딩
  function bind(){
    $('#qCust')?.addEventListener('input', debounce(refresh, 300));
    $('#fType')?.addEventListener('change', refresh);
    $('#fState')?.addEventListener('change', refresh);

    // 상세 버튼(위임)
    document.addEventListener('click', (e)=>{
      const btn = e.target.closest('button[data-open]');
      if(btn){ openDetail(btn.getAttribute('data-open')); }
      if(e.target.matches('[data-close]')){
        e.target.closest('.modal').style.display='none';
      }
    });

    // 업로드 미리보기
    $('#uploadImg')?.addEventListener('change', (e)=>{
      const file = e.target.files && e.target.files[0];
      if(!file){ $('#thumbPreview').textContent='업로드 미리보기'; return; }
      const reader = new FileReader();
      reader.onload = ()=>{ const tp=$('#thumbPreview'); tp.innerHTML=''; const img=new Image(); img.src=reader.result; img.style.maxHeight='100%'; img.style.maxWidth='100%'; tp.appendChild(img); };
      reader.readAsDataURL(file);
    });

    // 업로드(시뮬)
    $('#btnUpload')?.addEventListener('click', ()=>{
      const file = $('#uploadImg')?.files?.[0];
      if(!file){ toast('이미지를 선택하세요.'); return; }
      post('VND_UPLOAD_IMAGE', { filename: file.name }).then(d=>{
        const okVals=[true,1,'ok','OK','success','SUCCESS'];
        $('#uploadStatus').textContent = okVals.includes(d?.result) ? '요청 완료' : (d?.error?.msg||'실패');
        if(okVals.includes(d?.result)) toast('프린팅 요청 접수');
      });
    });

    // 신규 버튼
    $('#btnNewCust')?.addEventListener('click', ()=>{ $('#mdNew').style.display='flex'; });

    // 오일/콘텐츠 선택(간단 프롬프트)
    $('#btnOilPick')?.addEventListener('click', ()=>{ const v=prompt('요청 오일 입력 (예: Forest Breeze)'); if(v){ $('#pickOil').textContent=v; } });
    $('#btnContPick')?.addEventListener('click', ()=>{ const v=prompt('요청 콘텐츠 입력 (예: Welcome Loop)'); if(v){ $('#pickContent').textContent=v; } });

    // 대수만큼 행 생성
    $('#btnGenRows')?.addEventListener('click', ()=>{
      const qty = parseInt($('#nQty')?.value||'0',10); if(!qty||qty<1){ toast('구매 대수를 입력하세요.'); return; }
      const oil=$('#pickOil').textContent||'-', cont=$('#pickContent').textContent||'-';
      const tb = $('#tblNewDevices tbody'); tb.innerHTML='';
      for(let i=0;i<qty;i++){
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${i+1}</td>
          <td><input type="date" class="input"/></td>
          <td>${oil}</td><td>-</td>
          <td>${cont}</td><td>-</td><td>-</td>
          <td><input type="file" class="input" accept="image/*"/></td>
          <td><input class="input" placeholder="메모"/></td>
          <td><input type="number" class="input" placeholder="0" style="width:90px"/></td>
          <td><input class="input" placeholder="배송지"/></td>
          <td><input class="input" placeholder="담당자"/></td>
          <td><input class="input" placeholder="연락처"/></td>`;
        tb.appendChild(tr);
      }
      toast('행 생성 완료');
    });

    // 신규 저장
    $('#btnSaveNew')?.addEventListener('click', ()=>{
      const payload = {
        vendor_id: (document.querySelector('#vendorId')?.value||'').trim(),
        new: {
          user: $('#nUser').value.trim(),
          company: $('#nCompany').value.trim(),
          manager: $('#nManager').value.trim(),
          email: $('#nEmail').value.trim(),
          phone: $('#nPhone').value.trim(),
          biz: $('#nBiz').value.trim(),
          qty: parseInt($('#nQty').value||'0',10),
          addr: $('#nAddr').value.trim(),
          memo: $('#nMemo').value.trim(),
          oil: $('#pickOil').textContent,
          content: $('#pickContent').textContent,
        }
      };
      post('VND_NEW_CUSTOMER_SAVE', payload).then(d=>{
        const okVals=[true,1,'ok','OK','success','SUCCESS'];
        if(okVals.includes(d?.result)){
          toast('등록 완료');
          $('#mdNew').style.display='none';
          refresh();
        } else {
          toast(d?.error?.msg||'등록 실패');
        }
      });
    });

    // 디바이스 행 클릭 → 썸네일 강조(샘플)
    $('#tblDevices')?.addEventListener('click', (e)=>{
      const tr = e.target.closest('tr'); if(!tr) return;
      $$('#tblDevices tbody tr').forEach(x=>x.style.outline='');
      tr.style.outline='2px solid var(--accent)';
      // 썸네일은 데모 그대로 유지
    });

    // CSV 내보내기(간단)
    $('#btnExport')?.addEventListener('click', ()=>{
      const rows = [['ID','고객명','업종','설치대수','시리얼','계약','남은기간','연락처']];
      $$('#grid tbody tr').forEach(tr=>{
        const cells=[...tr.children].slice(0,8).map(td=>td.textContent.replace(/\s+/g,' ').trim()); rows.push(cells);
      });
      const csv = rows.map(r=>r.map(v=>`"${(v||'').replace(/"/g,'""')}"`).join(',')).join('\n');
      const blob = new Blob([csv],{type:'text/csv;charset=utf-8;'});
      const a=document.createElement('a'); a.href=URL.createObjectURL(blob); a.download='customers.csv'; a.click();
    });
  }

  function debounce(fn,ms){ let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn.apply(null,a),ms); }; }

  // 초기화
  bind();
  // 첫 렌더(서버 데모와 동기화)
  refresh();
})();
</script>
<script>(function(){
  // 모달을 문서 최상위로 이동 + 기본 숨김 보장
  var modals = document.querySelectorAll('.modal');
  modals.forEach(function(m){ if(m.parentElement!==document.body){ document.body.appendChild(m); } m.style.display='none'; });
  function fixModal(m){ if(!m) return; m.style.position='fixed'; m.style.left='0'; m.style.top='0'; m.style.right='0'; m.style.bottom='0'; m.style.display = m.style.display||'flex'; m.style.zIndex='9999'; }
  // 스타일 변경으로 표시될 때도 포지셔닝 고정
  var obs = new MutationObserver(function(list){
    list.forEach(function(mu){
      var el = mu.target;
      if(el.classList && el.classList.contains('modal')){
        if(window.getComputedStyle(el).display!=='none'){ fixModal(el); }
      }
    });
  });
  modals.forEach(function(m){ obs.observe(m,{attributes:true, attributeFilter:['style','class']}); });
  // 신규/닫기 버튼 보강 처리
  document.addEventListener('click', function(e){
    if(e.target && e.target.id==='btnNewCust'){
      var m=document.getElementById('mdNew'); if(m){ if(m.parentElement!==document.body){ document.body.appendChild(m);} fixModal(m); m.style.display='flex'; }
    }
    if(e.target && (e.target.hasAttribute && e.target.hasAttribute('data-close'))){
      var md = e.target.closest('.modal'); if(md){ md.style.display='none'; }
    }
  });
})();</script>

