# API 레퍼런스 (API Reference)

> 필수 함수, 표준 응답 포맷, 메뉴 ID 규칙, 암복호화

---

## 목차

1. [PHP 필수 함수](#php-필수-함수)
2. [JavaScript 필수 함수](#javascript-필수-함수)
3. [표준 응답 포맷](#표준-응답-포맷)
4. [암복호화 함수](#암복호화-함수)
5. [메뉴 ID 규칙](#메뉴-id-규칙)
6. [커스텀 ID 생성 패턴](#커스텀-id-생성-패턴)

---

## PHP 필수 함수

### 전역 변수

**`common.php`에서 이미 초기화된 전역 변수** (별도 선언 불필요):

```php
$con            // MySQLi 연결
$mb_id          // 로그인 사용자 ID
$mb_role        // 사용자 역할 코드
$roleName       // 포털명 (hq/vendor/customer/lucid)
$response       // 표준 응답 배열
```

### 암복호화

#### encryptValue($value)

**용도**: 단일 값을 암호화합니다.

**파라미터**:
- `$value` (string): 암호화할 값

**반환**: 암호화된 문자열

**예시**:
```php
// 토큰 생성
$token = encryptValue(date('Y-m-d') . '/dashboard');

// 메뉴 ID 암호화
$encryptedId = encryptValue('H01');
```

#### decryptValue($value)

**용도**: 암호화된 단일 값을 복호화합니다.

**파라미터**:
- `$value` (string): 복호화할 값

**반환**: 복호화된 문자열

**예시**:
```php
// 토큰 복호화
$decrypted = decryptValue($_POST['menuName']);
// 결과: "2025-11-12/dashboard"

// 날짜와 페이지 분리
list($date, $pageName) = explode('/', $decrypted);
```

#### decryptArrayRecursive($array)

**용도**: 배열의 모든 값을 재귀적으로 복호화합니다.

**파라미터**:
- `$array` (array): 복호화할 배열

**반환**: 복호화된 배열

**예시**:
```php
// _ajax_.php에서 자동으로 처리됨
$_POST = decryptArrayRecursive($_POST);
$_GET = decryptArrayRecursive($_GET);

// 이후 평범하게 사용
$action = $_POST['action'];
$searchKeyword = $_POST['search'];
```

### 응답 처리

#### Finish()

**용도**: JSON 응답을 출력하고 스크립트를 종료합니다.

**파라미터**: 없음 (전역 `$response` 배열 사용)

**반환**: 없음 (출력 후 exit)

**예시**:
```php
// 성공 응답
$response['result'] = true;
$response['msg'] = '저장되었습니다.';
Finish();

// 실패 응답
$response['result'] = false;
$response['error'] = ['msg' => '오류 발생', 'code' => 500];
Finish();

// 데이터 반환
$response['result'] = true;
$response['items'] = $rows;
Finish();
```

### DB 헬퍼 함수

#### mysqli_real_escape_string($con, $value)

**용도**: SQL Injection 방지를 위한 문자열 이스케이프

**파라미터**:
- `$con` (mysqli): DB 연결
- `$value` (string): 이스케이프할 값

**반환**: 이스케이프된 문자열

**예시**:
```php
$name = mysqli_real_escape_string($con, $_POST['name']);
$sql = "INSERT INTO users (name) VALUES ('{$name}')";
```

### Composer Autoloader

#### require_once __DIR__ . '/../utility/autoload.php'

**용도**: 외부 패키지 사용 (mpdf, PHPMailer 등)

**예시**:
```php
// PDF 생성
require_once __DIR__ . '/../utility/autoload.php';

$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML('<h1>Hello World</h1>');
$mpdf->Output('document.pdf', 'D');
```

---

## JavaScript 필수 함수

### 페이지 로드

#### loadPage(el, menuName)

**용도**: AJAX로 페이지를 로드합니다.

**파라미터**:
- `el` (Element | string): 클릭된 요소 또는 암호화된 토큰
- `menuName` (string, optional): 암호화된 토큰 (el이 문자열일 때 생략)

**반환**: 없음

**예시**:
```javascript
// 메뉴 클릭 시
<a onclick="loadPage(this, '<?= $token ?>')">대시보드</a>

// 직접 호출
loadPage('<?= $token ?>');
```

**동작**:
1. 모든 탭의 active 클래스 제거
2. 클릭된 탭에 active 클래스 추가
3. AJAX POST 요청 → index.php
4. HTML 응답 → `#content` 영역에 삽입
5. 스크립트 자동 실행

### 데이터 통신

#### updateAjaxContent(data, callback, isAlert = true)

**용도**: 서버와 JSON 데이터를 주고받습니다.

**파라미터**:
- `data` (object): POST로 전송할 데이터 객체
- `callback` (function): 응답 성공 시 실행할 콜백 함수
- `isAlert` (boolean, optional): 오류 시 자동 alert 표시 여부 (기본: true)

**반환**: 없음

**중요**: 사용 전 필수로 `window.pageName` 선언 필요

**예시**:
```javascript
// 스크립트 상단에 선언
window.pageName = '<?= encryptValue(date('Y-m-d') . '/customer_list') ?>';

// 데이터 조회
const data = {};
data['<?= encryptValue('action') ?>'] = 'filter_customers';
data['<?= encryptValue('search') ?>'] = searchValue;

updateAjaxContent(data, function(response) {
  if (response.result && response.html) {
    document.querySelector('#tblCustomers tbody').innerHTML = response.html;
  }
});

// 데이터 추가 (alert 비활성화)
const data = {};
data['<?= encryptValue('action') ?>'] = 'add_customer';
data['<?= encryptValue('name') ?>'] = customerName;

updateAjaxContent(data, function(response) {
  if (response.result) {
    console.log('저장 성공');
    location.reload();
  } else {
    console.error('저장 실패');
  }
}, false);
```

### 날짜 필터

#### setDate(type, pid = '')

**용도**: 날짜 입력 필드에 프리셋 값을 설정합니다.

**파라미터**:
- `type` (string): 날짜 프리셋 타입
  - `'today'`: 오늘
  - `'thisWeek'`: 이번 주 (월요일~오늘)
  - `'prevWeek'`: 지난 주 (월요일~일요일)
  - `'thisMonth'`: 이번 달 (1일~오늘)
  - `'prevMonth'`: 지난 달 (1일~말일)
  - `'30days'`: 최근 30일
  - `'week'`: 최근 7일
- `pid` (string, optional): 날짜 입력 필드 ID 접미사

**반환**: 없음

**예시**:
```html
<!-- 기본 사용 -->
<input type="date" id="startDate">
<input type="date" id="endDate">
<button onclick="setDate('today')">오늘</button>
<button onclick="setDate('thisMonth')">당월</button>

<!-- 접미사 사용 (여러 날짜 필터) -->
<input type="date" id="startDate2">
<input type="date" id="endDate2">
<button onclick="setDate('prevWeek', '2')">전주</button>
```

### 탭 로딩

#### loadTabContent(btnElement, encryptedToken, containerSelector, tabButtonsSelector)

**용도**: 탭 버튼 클릭 시 콘텐츠를 로드합니다.

**파라미터**:
- `btnElement` (Element): 클릭된 탭 버튼 요소
- `encryptedToken` (string): 암호화된 페이지 토큰
- `containerSelector` (string): 탭 컨텐츠를 표시할 컨테이너 CSS 선택자
- `tabButtonsSelector` (string): 탭 버튼들의 부모 요소 CSS 선택자

**반환**: 없음

**예시**:
```html
<div id="sec-product-mgmt" class="card">
  <div class="tab-nav-inline">
    <button class="tab-btn-inline active"
            data-token="<?= $deviceToken ?>"
            onclick="loadTabContent(this, '<?= $deviceToken ?>', '#product-tab-content', '#sec-product-mgmt')">
      기기
    </button>
    <button class="tab-btn-inline"
            data-token="<?= $accessoryToken ?>"
            onclick="loadTabContent(this, '<?= $accessoryToken ?>', '#product-tab-content', '#sec-product-mgmt')">
      악세사리
    </button>
  </div>

  <div class="tab-content-area" id="product-tab-content">
    <p>로딩 중...</p>
  </div>
</div>

<script>
// 페이지 로드 시 첫 번째 탭 자동 로드
setTimeout(function() {
  const firstTab = document.querySelector('#sec-product-mgmt .tab-btn-inline.active');
  if (firstTab) {
    const token = firstTab.getAttribute('data-token');
    loadTabContent(firstTab, token, '#product-tab-content', '#sec-product-mgmt');
  }
}, 100);
</script>
```

---

## 표준 응답 포맷

### 성공 응답

#### 기본 성공
```php
$response['result'] = true;
$response['msg'] = '작업이 완료되었습니다.';
Finish();
```

```json
{
  "result": true,
  "msg": "작업이 완료되었습니다."
}
```

#### 단일 데이터 반환
```php
$row = mysqli_fetch_assoc($result);
$response['result'] = true;
$response['item'] = $row;
Finish();
```

```json
{
  "result": true,
  "item": {
    "id": 1,
    "name": "홍길동",
    "email": "hong@example.com"
  }
}
```

#### 복수 데이터 반환
```php
$rows = [];
while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
}
$response['result'] = true;
$response['items'] = $rows;
Finish();
```

```json
{
  "result": true,
  "items": [
    {"id": 1, "name": "홍길동"},
    {"id": 2, "name": "김철수"}
  ]
}
```

#### HTML 반환 (필터/조회)
```php
$html = '';
while ($row = mysqli_fetch_assoc($result)) {
    $html .= '<tr>';
    $html .= '<td>' . htmlspecialchars($row['id']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['name']) . '</td>';
    $html .= '</tr>';
}

if (empty($html)) {
    $html = '<tr><td colspan="2" style="text-align:center;">데이터 없음</td></tr>';
}

$response['result'] = true;
$response['html'] = $html;
Finish();
```

```json
{
  "result": true,
  "html": "<tr><td>1</td><td>홍길동</td></tr>..."
}
```

### 실패 응답

#### 기본 실패
```php
$response['result'] = false;
$response['error'] = ['msg' => '오류가 발생했습니다.', 'code' => 500];
Finish();
```

```json
{
  "result": false,
  "error": {
    "msg": "오류가 발생했습니다.",
    "code": 500
  }
}
```

#### 검증 실패
```php
if (empty($name)) {
    $response['result'] = false;
    $response['error'] = ['msg' => '필수 항목을 입력해주세요.', 'code' => 400];
    Finish();
}
```

```json
{
  "result": false,
  "error": {
    "msg": "필수 항목을 입력해주세요.",
    "code": 400
  }
}
```

#### DB 오류
```php
if (!$result) {
    $response['result'] = false;
    $response['error'] = ['msg' => '데이터베이스 오류: ' . mysqli_error($con), 'code' => 500];
    Finish();
}
```

### 응답 키 규칙

**허용된 키**:
- `result` (boolean, required): 성공/실패 여부
- `msg` (string, optional): 응답 메시지
- `html` (string, optional): HTML 콘텐츠
- `item` (object, optional): 단일 데이터
- `items` (array, optional): 복수 데이터
- `error` (object, optional): 오류 정보 (`msg`, `code`)

**금지된 키**:
- `data`, `SESSION`, `menus`, `events`, `totalCount`, `approval`, `pagination`, `table_array` 등

---

## 암복호화 함수

### 암호화 (PHP)

```php
// 단일 값
$encrypted = encryptValue('hello world');

// 토큰 생성
$token = encryptValue(date('Y-m-d') . '/dashboard');
// 결과: "U2FsdGVkX1..." (CryptoJS 호환)

// 배열 (각 요소를 개별 암호화)
$data = [
    'name' => encryptValue('홍길동'),
    'email' => encryptValue('hong@example.com')
];
```

### 복호화 (PHP)

```php
// 단일 값
$decrypted = decryptValue('U2FsdGVkX1...');

// 배열 재귀 복호화
$_POST = decryptArrayRecursive($_POST);

// 이제 평범하게 사용
$action = $_POST['action'];
$name = $_POST['name'];
```

### 암호화 (JavaScript)

JavaScript에서는 PHP가 제공한 암호화된 값을 그대로 사용:

```javascript
// PHP에서 암호화된 키 사용
const data = {};
data['<?= encryptValue('action') ?>'] = 'get_data';
data['<?= encryptValue('name') ?>'] = '홍길동';  // 값은 평문

// 잘못된 사용 - JavaScript에서 encryptValue() 호출 불가
data[encryptValue('action')] = 'get_data';  // 에러!
```

---

## 메뉴 ID 규칙

### 포털별 ID 형식

| 포털 | ID 형식 | 예시 |
|------|---------|------|
| HQ | H + 2자리 숫자 | H01, H02, H03 |
| VENDOR | V + 2자리 숫자 | V01, V02, V03 |
| CUSTOMER | C + 2자리 숫자 | C01, C02, C03 |
| LUCID | L + 2자리 숫자 | L01, L02, L03 |

### 서브메뉴 ID

- 하위메뉴 ID: `상위ID-순번`
- 예시: `H02-1`, `H02-2`, `H02-3`

### 메뉴 등록 예시

```php
// inc/menus.php
$menus = [
  "hq" => [
    ["name" => "대시보드", "id" => "H01", "path" => "dashboard", "enabled" => true],
    [
      "name" => "실적",
      "id" => "H02",
      "path" => null,
      "sub" => [
        ["name" => "벤더", "id" => "H02-1", "path" => "vendor_perf", "enabled" => true],
        ["name" => "영업사원", "id" => "H02-2", "path" => "sales_perf", "enabled" => true],
        ["name" => "본사", "id" => "H02-3", "path" => "hq_perf", "enabled" => true],
      ]
    ],
    ["name" => "고객 관리", "id" => "H03", "path" => "customer_mgmt", "enabled" => true],
  ]
];
```

---

## 커스텀 ID 생성 패턴

### ID 형식

| 테이블 | ID 형식 | 예시 |
|--------|---------|------|
| vendors | VYYYYMMDDNNNN | V202511120001 |
| customers | CYYYYMMDDNNNN | C202511120001 |
| sites | SYYYYMMDDNNNN | S202511120001 |

### 생성 함수 예시

#### 벤더 ID 생성

```php
function generateVendorId($con) {
    $today = date('Ymd');
    $prefix = 'V' . $today;

    // 오늘 생성된 마지막 번호 조회
    $sql = "SELECT vendor_id FROM vendors
            WHERE vendor_id LIKE '{$prefix}%'
            ORDER BY vendor_id DESC LIMIT 1";
    $result = mysqli_query($con, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $lastId = $row['vendor_id'];
        $lastNum = (int)substr($lastId, -4);
        $newNum = $lastNum + 1;
    } else {
        $newNum = 1;
    }

    return $prefix . str_pad($newNum, 4, '0', STR_PAD_LEFT);
}

// 사용 예시
$vendorId = generateVendorId($con);
// 결과: V202511120001
```

#### 고객 ID 생성

```php
function generateCustomerId($con) {
    $today = date('Ymd');
    $prefix = 'C' . $today;

    $sql = "SELECT customer_id FROM customers
            WHERE customer_id LIKE '{$prefix}%'
            ORDER BY customer_id DESC LIMIT 1";
    $result = mysqli_query($con, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $lastSeq = intval(substr($row['customer_id'], -4));
        $newSeq = str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);
    } else {
        $newSeq = '0001';
    }

    return $prefix . $newSeq;
}

// 사용 예시
$customerId = generateCustomerId($con);
// 결과: C202511120001
```

#### 범용 ID 생성 함수

```php
function generateCustomId($con, $table, $idField, $prefix) {
    $today = date('Ymd');
    $fullPrefix = $prefix . $today;

    $sql = "SELECT {$idField} FROM {$table}
            WHERE {$idField} LIKE '{$fullPrefix}%'
            ORDER BY {$idField} DESC LIMIT 1";
    $result = mysqli_query($con, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $lastId = $row[$idField];
        $lastSeq = intval(substr($lastId, -4));
        $newSeq = str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);
    } else {
        $newSeq = '0001';
    }

    return $fullPrefix . $newSeq;
}

// 사용 예시
$vendorId = generateCustomId($con, 'vendors', 'vendor_id', 'V');
$customerId = generateCustomId($con, 'customers', 'customer_id', 'C');
$siteId = generateCustomId($con, 'sites', 'site_id', 'S');
```

---

**마지막 업데이트**: 2025-11-12

**문서 작성자**: Claude Code Team
