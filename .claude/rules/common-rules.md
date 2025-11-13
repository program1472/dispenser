# 공통 규약 및 UI 규칙

> 모든 포털에서 공통으로 적용되는 코딩 규약, UI/UX 가이드라인, 상태 정의

---

## 🌐 언어 정책

**⚠️ 모든 설명과 커뮤니케이션은 한국어로 작성합니다.**
- AI 어시스턴트의 모든 응답은 한국어로 제공
- 코드 주석도 가능한 한국어로 작성
- 에러 메시지 및 사용자 안내 메시지는 반드시 한국어

---

## 📋 공통 코딩 규약

### 파일 구조 규칙

- 모든 페이지는 `inc/common.php`를 로드해 **$con(MySQLi)** 로 DB 쿼리 수행
- 공통 응답은 전역 **$response** 배열에 담고 **Finish()**로 종료 (항상 JSON)
- 모든 `$_POST`, `$_GET`은 **`_ajax_.php`를 반드시 경유**하고 `decryptArrayRecursive()`로 복호화 후 사용
- 서버에서 내려주는 민감/구조 데이터는 필요 시 `encryptValue()`로 암호화 가능
- **입력값(프런트→서버)은 암호화 하지 않는다**

### 전역 변수 규칙

**⚠️ 중요: `$con`과 `$response`는 `common.php`에서 이미 전역으로 선언된 변수입니다.**

- **$con (MySQLi 연결)**:
  - ❌ **개별 페이지에서 `global $con;` 선언 금지**
  - ✅ `dbconfig.php` → `MySQLi.php` → `common.php` 경로로 이미 연결됨
  - ✅ 모든 페이지에서 바로 `$con` 사용 가능

- **$response (응답 배열)**:
  - ❌ **개별 페이지에서 `$response = [...]` 초기화 금지**
  - ❌ **개별 페이지에서 `global $response;` 선언 금지**
  - ✅ `common.php`에서 이미 초기화되어 있으므로 바로 사용

```php
// ❌ 잘못된 예시
global $con;  // 불필요한 선언!
global $response;  // 불필요한 선언!
$response = ['result' => false];  // 초기화 금지!

// ✅ 올바른 예시
// 아무 선언 없이 바로 사용
$sql = "SELECT * FROM users";
$result = mysqli_query($con, $sql);

$response['result'] = 'ok';
$response['msg'] = '조회 성공';
$response['item'] = mysqli_fetch_all($result, MYSQLI_ASSOC);
Finish();
```

### ⚠️ 절대 금지 규칙

**1. DB 설정 파일 중복 로드 금지**
- ❌ `require_once dirname(__DIR__, 2) . '/dbconfig.php';` 각 페이지에 삽입 금지
- ✅ `common.php`에서 이미 `dbconfig.php`를 로드하므로 중복 불필요

**2. 개별 CSS 파일 로드 금지**
- ❌ 각 페이지에서 `<link rel="stylesheet">` 직접 삽입 금지
- ✅ `common.php`에서 동적으로 CSS를 로드하므로 중복 불필요
- ✅ 필요한 스타일은 기존 공용 CSS 파일에 추가

**3. 페이지 내 CSS 코드 작성 금지**
- ❌ `<style>` 태그 또는 인라인 스타일 사용 금지
- ✅ 모든 스타일은 공용 CSS 파일(`/css/style.css`, `/css/tem.css` 등)에 작성
- ✅ 기존 페이지에 삽입된 CSS는 공용 CSS로 이동 후 삭제

**4. 페이지 내 JavaScript 최소화**
- ❌ 재사용 가능한 함수를 페이지 내 `<script>`에 작성 금지
- ✅ 공통 기능은 `/js/js.php` 또는 별도 공용 JS 파일로 분리
- ✅ 페이지별 특수 로직만 인라인으로 허용

**5. 탭 AJAX 요청 시 menuName 형식 준수**
- ❌ `menuName`에 단순 페이지명만 전송 금지 (예: `'customer_vendor'`)
- ✅ 반드시 `날짜/페이지명` 형식으로 전송 (예: `'2025-11-07/customer_vendor'`)
- ✅ `url: "/"` 사용 (❌ `url: "#"` 금지)
- **올바른 코드 예시:**
  ```javascript
  const today = '<?= date('Y-m-d') ?>';
  const menuNameValue = today + '/' + pageName;
  const data = {};
  data['<?= encryptValue('menuName') ?>'] = menuNameValue;

  $.ajax({
    type: "POST",
    url: "/",  // 반드시 "/"
    dataType: "html",
    data: data,
    cache: false
  });
  ```
- **이유**: `index.php`에서 `menuName` 형식 검증 시 날짜 불일치하면 자동 로그아웃 처리됨

### 응답 포맷 (표준)

**⚠️ 중요: $response 변수는 `common.php`에서 전역으로 선언된 변수입니다.**
- ❌ **개별 페이지에서 `$response` 초기화 금지** (예: `$response = ['result' => false, 'data' => []]`)
- ✅ `common.php`에서 이미 선언되었으므로 바로 사용

**올바른 응답 키**:
- `result`: 반드시 `'ok'` 또는 `'error'` 문자열 사용 (❌ `true`/`false` 금지)
- `msg`: 응답 메시지
- `html`: HTML 콘텐츠 (페이지 로드 시)
- `item`: 옵션 데이터 배열 (❌ `data` 키 사용 금지)

**중요: 모든 응답은 JSON 형식으로 통일**
- `index.php`는 출력 버퍼링(`ob_start()`)을 사용하여 페이지 HTML을 캡처
- 캡처된 HTML은 `$response['html']`에 담아 JSON으로 반환
- `loadPage()` 함수는 `response.html`을 받아 `#content`에 삽입
- ❌ 페이지에서 직접 HTML을 `echo`하지 않음 (출력 버퍼링으로 캡처됨)

```php
// ✅ 페이지 로드 응답 (index.php에서 자동 처리)
ob_start();
require $filePath;  // 페이지 HTML 출력
$html = ob_get_clean();

$response['result'] = 'ok';
$response['html'] = $html;
Finish();

// ✅ AJAX 데이터 응답
$response['result'] = 'ok';
$response['msg'] = '성공 메시지';
$response['item'] = [...]; // 옵션 데이터
Finish();

// ✅ 오류 응답
$response['result'] = 'error';
$response['msg'] = '오류 메시지';
Finish();

// ❌ 잘못된 예시
$response = ['result' => false, 'data' => []]; // 초기화 금지!
$response = ['success' => true]; // 'success' 키 사용 금지!
$response['data'] = [...]; // 'data' 키 사용 금지, 'item' 사용!
echo $html; exit(); // 직접 출력 금지! ob_start()로 캡처 후 $response['html']에 담기
```

### 사용자 친화적 에러 메시지

**⚠️ 중요: MySQL 에러를 사용자가 이해하기 쉬운 메시지로 변환합니다.**

**공통 함수 사용:**
`inc/functions/error.php`의 `getFriendlyErrorMessage()` 함수를 사용하여 에러 메시지를 변환합니다.

```php
} catch (Exception $e) {
    mysqli_rollback($con);
    $response['result'] = false;

    // 공통 함수로 에러 메시지 변환
    $errorMsg = getFriendlyErrorMessage($e->getMessage());

    $response['error'] = ['msg' => $errorMsg, 'code' => 500];
}
```

**변환 규칙 (`getFriendlyErrorMessage()` 함수 내부):**
- `Duplicate entry ... email`: "이미 사용 중인 이메일입니다. 다른 이메일을 사용해주세요."
- `Duplicate entry`: "중복된 데이터가 있습니다. 다른 값을 입력해주세요."
- `Cannot delete or update a parent row`: "다른 데이터에서 사용 중이므로 삭제할 수 없습니다."
- `Data too long`: "입력한 데이터가 너무 깁니다. 더 짧게 입력해주세요."

### 탭 로딩 함수 (공통)

**⚠️ 중요: 탭 페이지는 `js.php`의 공통 `loadTabContent` 함수를 사용합니다.**

모든 탭 구조 페이지에서 일관된 방식으로 탭을 로드하기 위해 공통 함수를 사용합니다.

```html
<!-- 탭 버튼 예시 -->
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

  <!-- 탭 컨텐츠 영역 -->
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

**loadTabContent 함수 파라미터:**
- `btnElement`: 클릭된 탭 버튼 요소 (this)
- `encryptedToken`: 암호화된 페이지 토큰
- `containerSelector`: 탭 컨텐츠를 표시할 컨테이너 CSS 선택자
- `tabButtonsSelector`: 탭 버튼들의 부모 요소 CSS 선택자

**특징:**
- JSON 응답 자동 처리
- 스크립트 자동 실행 (줄바꿈 문자 오류 없음)
- 오류 처리 및 로딩 상태 자동 표시
- 모든 탭 페이지에서 동일한 동작 보장

### 트랜잭션/검증/로그

- 관련 엔터티를 함께 생성/갱신하는 작업은 **트랜잭션** 적용 (성공 시 커밋, 실패 시 롤백)
- **공통 검증**: 이메일 형식/중복, 필수값 체크, 권한 검사
- **감사 로그**: 주요 상태 변경/로그인 등은 기록 (로그 실패가 본처리를 막지 않도록 예외 삼킴)

---

## 🔐 보안 규칙

### 토큰 기반 라우팅

- 모든 페이지 경로는 **암호화된 토큰** 형태로 전송
- 토큰 형식: `encryptValue("YYYY-MM-DD/menuName")`
- 날짜 검증: 요청 날짜가 오늘(`$today`)과 일치해야 함
- 복호화 실패 또는 날짜 불일치 시 로그아웃 처리

### 데이터 암복호화

```php
// 암호화 (서버 → 클라이언트)
$encrypted = encryptValue($value);

// 복호화 (클라이언트 → 서버)
$decrypted = decryptValue($encryptedValue);

// 배열 재귀 복호화
$_POST = decryptArrayRecursive($_POST);
$_GET = decryptArrayRecursive($_GET);
```

### 입력값 검증

- 모든 입력값은 **화이트리스트 검증** 적용
- SQL Injection 방지: Prepared Statement 사용
- XSS 방지: `htmlspecialchars()` 또는 `strip_tags()` 적용
- 권한 검증: 포털별 접근 권한 체크

---

## 🎨 UI/UX 공통 규칙

### 기본 원칙

- 페이지 전체 리로드 금지, **AJAX만** 사용
- **공통 CSS**를 사용하여 레이아웃, 색상, 버튼, 폰트, 배지를 통일
- 포털 간 UI 구조 및 색상 체계는 HQ 포털을 기준으로 통일
- 반응형 UI: 1440px 기본, 1200px 이하에서는 카드형 자동 전환

### CSS 로드 순서

```html
<link rel="stylesheet" href="/css/style.css">      <!-- 1. 공통 기본 -->
<link rel="stylesheet" href="/css/tem.css">        <!-- 2. 템플릿 -->
<link rel="stylesheet" href="/css/{role}.css">     <!-- 3. 포털별 -->
<link rel="stylesheet" href="/css/header.css">     <!-- 4. 헤더/메뉴 -->
```

### 공통 UI 요소

```html
<!-- 헤더 -->
<div class="brand">포털명</div>

<!-- 메뉴 -->
<div id="tabs">
  <a class="active" onclick="loadPage(this, 'token')">메뉴1</a>
  <div class="dropdown">
    <a class="dropdown-toggle">메뉴2</a>
    <div class="dropdown-menu">
      <a onclick="loadPage(this, 'token')">서브메뉴</a>
    </div>
  </div>
</div>

<!-- 콘텐츠 영역 -->
<div id="content">
  <!-- 동적 로드 영역 -->
</div>

<!-- 모달 팝업 -->
<div class="pop" style="display:none;">
  <div class="pop-content">
    <!-- 팝업 내용 -->
  </div>
</div>
```

---

## 🏷️ 메뉴 및 탭 구조

### 메뉴 ID 규칙

| 포털 | ID 형식 | 예시 |
|------|---------|------|
| HQ | H + 2자리 숫자 | H01, H02, H03 |
| VENDOR | V + 2자리 숫자 | V01, V02, V03 |
| CUSTOMER | C + 2자리 숫자 | C01, C02, C03 |
| LUCID | L + 2자리 숫자 | L01, L02, L03 |

### 서브메뉴 지원

```php
// inc/menus.php 예시
$menus = [
  "hq" => [
    ["name" => "대시보드", "id" => "H01", "path" => "dashboard", "enabled" => true],
    [
      "name" => "실적",
      "id" => "H02",
      "path" => null, // 서브메뉴가 있으면 path는 null
      "sub" => [
        ["name" => "벤더", "id" => "H02-1", "path" => "vendor_perf", "enabled" => true],
        ["name" => "영업사원", "id" => "H02-2", "path" => "sales_perf", "enabled" => true],
      ]
    ],
  ]
];
```

### 드롭다운 메뉴 동작

- 클릭 또는 호버로 메뉴 열기/닫기
- 활성 하위메뉴는 부모 드롭다운도 `active` 클래스 추가
- 외부 클릭 시 드롭다운 자동 닫기

---

## 📊 공통 상태 정의

### 계약 상태 (Contract Status)

| 상태 | 의미 | 배지 색상 |
|------|------|-----------|
| ACTIVE | 정상 활성 | 초록 |
| WARNING | 경고 (결제 지연 등) | 노랑 |
| GRACE | 유예 기간 | 주황 |
| TERMINATED | 종료 | 회색 |

### 지급 상태 (Payment Status)

| 상태 | 의미 | 배지 색상 |
|------|------|-----------|
| PLANNED | 지급 예정 | 회색 |
| DUE | 지급 대기 중 | 파랑 |
| PAID | 지급 완료 | 초록 |

### 티켓 상태 (Ticket Status)

| 상태 | 의미 | 배지 색상 |
|------|------|-----------|
| OPEN | 접수 | 파랑 |
| IN_PROGRESS | 처리 중 | 주황 |
| RESOLVED | 완료 | 초록 |

### 작업지시서 상태 (Work Order Status)

| 상태 | 의미 | 배지 색상 |
|------|------|-----------|
| OPEN | 대기 | 파랑 |
| IN_PROGRESS | 진행 중 | 주황 |
| DONE | 완료 | 초록 |

### 배송 상태 (Shipping Status)

| 상태 | 의미 | 배지 색상 |
|------|------|-----------|
| REQUESTED | 요청 | 회색 |
| CONFIRMED | 확인 | 파랑 |
| SHIPPED | 배송 중 | 주황 |
| DELIVERED | 배송 완료 | 초록 |

---

## 🎨 상태 배지 CSS

```html
<!-- 계약 상태 -->
<span class="badge badge-active">ACTIVE</span>
<span class="badge badge-warning">WARNING</span>
<span class="badge badge-grace">GRACE</span>
<span class="badge badge-terminated">TERMINATED</span>

<!-- 지급 상태 -->
<span class="badge badge-planned">PLANNED</span>
<span class="badge badge-due">DUE</span>
<span class="badge badge-paid">PAID</span>

<!-- 티켓/작업 상태 -->
<span class="badge badge-open">OPEN</span>
<span class="badge badge-progress">IN_PROGRESS</span>
<span class="badge badge-done">DONE</span>
```

### CSS 스타일 예시

```css
.badge {
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: bold;
}

.badge-active, .badge-done, .badge-paid { background: #28a745; color: white; }
.badge-warning { background: #ffc107; color: black; }
.badge-grace, .badge-progress { background: #fd7e14; color: white; }
.badge-terminated, .badge-planned { background: #6c757d; color: white; }
.badge-open, .badge-due { background: #007bff; color: white; }
```

---

## 📋 공통 기능 구현

### CSV 내보내기

```javascript
function exportToCSV(tableId, filename) {
  const table = document.getElementById(tableId);
  const rows = table.querySelectorAll('tr');
  let csv = [];

  rows.forEach(row => {
    const cols = row.querySelectorAll('td, th');
    const rowData = Array.from(cols).map(col => col.innerText);
    csv.push(rowData.join(','));
  });

  const csvContent = 'data:text/csv;charset=utf-8,' + csv.join('\n');
  const link = document.createElement('a');
  link.setAttribute('href', encodeURI(csvContent));
  link.setAttribute('download', filename + '.csv');
  link.click();
}
```

### 검색 및 필터링

```javascript
function filterTable(inputId, tableId) {
  const input = document.getElementById(inputId);
  const filter = input.value.toUpperCase();
  const table = document.getElementById(tableId);
  const rows = table.getElementsByTagName('tr');

  for (let i = 1; i < rows.length; i++) { // 헤더 제외
    const cells = rows[i].getElementsByTagName('td');
    let match = false;

    for (let j = 0; j < cells.length; j++) {
      if (cells[j].innerText.toUpperCase().indexOf(filter) > -1) {
        match = true;
        break;
      }
    }

    rows[i].style.display = match ? '' : 'none';
  }
}
```

### 정렬

```javascript
function sortTable(tableId, columnIndex, isNumeric = false) {
  const table = document.getElementById(tableId);
  const rows = Array.from(table.rows).slice(1); // 헤더 제외

  rows.sort((a, b) => {
    const aVal = a.cells[columnIndex].innerText;
    const bVal = b.cells[columnIndex].innerText;

    if (isNumeric) {
      return parseFloat(aVal.replace(/[^0-9.-]/g, '')) - parseFloat(bVal.replace(/[^0-9.-]/g, ''));
    } else {
      return aVal.localeCompare(bVal);
    }
  });

  rows.forEach(row => table.appendChild(row));
}
```

---

## 📱 반응형 규칙

### 브레이크포인트

- **Desktop**: 1440px 이상 (기본 레이아웃)
- **Tablet**: 1200px ~ 1439px (테이블 가로 스크롤)
- **Mobile**: 1200px 미만 (카드형 자동 전환)

### 반응형 테이블

```css
@media (max-width: 1200px) {
  table {
    display: block;
    overflow-x: auto;
    white-space: nowrap;
  }

  /* 카드형 전환 */
  .card-view {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }

  .card-view .card {
    border: 1px solid #ddd;
    padding: 16px;
    border-radius: 8px;
  }
}
```

---

## 🔔 알림 및 팝업

### 모달 팝업 구조

```html
<div class="pop" id="detailModal" style="display:none;">
  <div class="pop-overlay" onclick="closeModal('detailModal')"></div>
  <div class="pop-content">
    <div class="pop-header">
      <h3>상세 정보</h3>
      <button class="pop-close" onclick="closeModal('detailModal')">×</button>
    </div>
    <div class="pop-body">
      <!-- 팝업 내용 -->
    </div>
    <div class="pop-footer">
      <button onclick="closeModal('detailModal')">닫기</button>
    </div>
  </div>
</div>
```

### 팝업 동작 스크립트

```javascript
function openModal(modalId) {
  document.getElementById(modalId).style.display = 'flex';
}

function closeModal(modalId) {
  document.getElementById(modalId).style.display = 'none';
}

// ESC 키로 닫기
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    const modals = document.querySelectorAll('.pop');
    modals.forEach(modal => modal.style.display = 'none');
  }
});
```

---

## 📝 코드 작성 체크리스트

### 신규 페이지 개발 시
- [ ] `inc/common.php` 로드 확인
- [ ] ❌ `dbconfig.php` 직접 로드하지 않기
- [ ] ❌ 페이지에 `<link>` CSS 로드 금지
- [ ] ❌ 페이지에 `<style>` 태그 사용 금지
- [ ] ✅ 필요한 스타일은 공용 CSS에 추가
- [ ] ✅ 재사용 가능한 JS는 공용 파일로 분리
- [ ] 암복호화 처리 적용
- [ ] 표준 응답 포맷 사용 (`Finish()`)
- [ ] 더미데이터 20건 이상 포함
- [ ] CSV 내보내기 구현
- [ ] 상태 배지 적용
- [ ] 검색/필터/정렬 기능 추가

### 탭 페이지 개발 시
- [ ] ✅ AJAX URL을 `"/"` 로 설정
- [ ] ✅ `menuName`을 `날짜/페이지명` 형식으로 전송
- [ ] ✅ `today` 변수를 `<?= date('Y-m-d') ?>`로 선언
- [ ] ❌ `url: "#"` 절대 사용 금지
- [ ] ❌ 단순 페이지명만 전송 금지

### UI 작성 시
- [ ] 반응형 레이아웃 적용
- [ ] 상단 필터 고정 (사업장 선택 등)
- [ ] 모든 버튼에 명확한 액션 정의
- [ ] 로딩 상태 표시 (AJAX 요청 중)
- [ ] 에러 메시지 사용자 친화적으로 표시

---

## 🔤 UTF-8 인코딩 처리 가이드

### 문제 증상

#### 1. MySQL CLI에서 한글이 깨짐
```bash
# 증상
mysql> SELECT * FROM scents WHERE scent_id = 152;
scent_name: ?Һ긣?ܷ?  # 롬브르단로가 깨짐

# 원인
- Windows CMD/PowerShell의 기본 인코딩이 CP949
- MySQL CLI가 UTF-8을 제대로 표시하지 못함
```

#### 2. MySQL CLI로 INSERT/UPDATE 시 한글이 깨짐
```bash
# 잘못된 방법
mysql> UPDATE scents SET scent_name = '롬브르단로' WHERE scent_id = 152;
# DB에 깨진 데이터로 저장됨: ?Һ긣?ܷ?

# 원인
- MySQL CLI의 character_set_client가 UTF-8이 아님
- 입력된 한글이 잘못된 인코딩으로 변환됨
```

#### 3. PHP에서 조회한 데이터가 깨짐
```php
// 증상
$result = mysqli_query($con, "SELECT scent_name FROM scents WHERE scent_id = 152");
$row = mysqli_fetch_assoc($result);
echo $row['scent_name']; // ?Һ긣?ܷ? 출력

// 원인
- mysqli 연결 시 charset 설정 누락
- SET NAMES utf8mb4 실행 누락
```

### 해결 방법

#### 1. MySQL CLI는 데이터 조회용으로만 사용 (한글 표시는 무시)
```bash
# MySQL CLI에서 한글이 깨져 보이는 것은 정상
# 실제 DB에 저장된 데이터가 UTF-8이면 문제없음

# 검증 방법: HEX로 확인
mysql> SELECT HEX(scent_name) FROM scents WHERE scent_id = 152;
# EC9BAEBCABEBA5B4EBA19C -> UTF-8 바이트 시퀀스 확인

# UTF-8 바이트가 EC로 시작하면 정상 (한글 UTF-8의 특징)
```

#### 2. 한글 데이터 INSERT/UPDATE는 반드시 PHP 사용
```php
<?php
// 올바른 방법: PHP mysqli로 UTF-8 처리

$host = "127.0.0.1";
$user = "program1472";
$pass = "\$gPfls1129";
$db = "dispenser";

// 1. 연결
$con = mysqli_connect($host, $user, $pass, $db);

// 2. 필수: UTF-8 설정 (반드시 연결 직후 실행)
mysqli_set_charset($con, "utf8mb4");
mysqli_query($con, "SET NAMES utf8mb4");

// 3. 한글 데이터 처리
$name = "롬브르단로";
$name_escaped = mysqli_real_escape_string($con, $name);
$sql = "UPDATE scents SET scent_name = '$name_escaped' WHERE scent_id = 152";
mysqli_query($con, $sql);

// 4. 검증
$result = mysqli_query($con, "SELECT scent_name FROM scents WHERE scent_id = 152");
$row = mysqli_fetch_assoc($result);
echo $row['scent_name']; // "롬브르단로" 정상 출력

mysqli_close($con);
?>
```

#### 3. 모든 PHP 파일의 DB 연결 표준 코드
```php
<?php
// 파일 상단에 반드시 포함
header('Content-Type: text/html; charset=UTF-8');

// DB 연결 표준 템플릿
function getDBConnection() {
    $host = "127.0.0.1";
    $user = "program1472";
    $pass = "\$gPfls1129";
    $db = "dispenser";

    $con = mysqli_connect($host, $user, $pass, $db);

    if (!$con) {
        die("연결 실패: " . mysqli_connect_error());
    }

    // 필수: UTF-8 설정
    mysqli_set_charset($con, "utf8mb4");
    mysqli_query($con, "SET NAMES utf8mb4");

    return $con;
}

// 사용 예시
$con = getDBConnection();

// INSERT
$name = "롬브르단로";
$name = mysqli_real_escape_string($con, $name);
mysqli_query($con, "INSERT INTO scents (scent_name) VALUES ('$name')");

// SELECT
$result = mysqli_query($con, "SELECT scent_name FROM scents WHERE scent_id = 152");
$row = mysqli_fetch_assoc($result);
echo $row['scent_name']; // 정상 출력

mysqli_close($con);
?>
```

### 데이터 검증 방법

#### 1. PHP로 검증 (권장)
```php
<?php
$con = mysqli_connect("127.0.0.1", "program1472", "\$gPfls1129", "dispenser");
mysqli_set_charset($con, "utf8mb4");
mysqli_query($con, "SET NAMES utf8mb4");

$result = mysqli_query($con, "SELECT scent_id, scent_name FROM scents WHERE scent_id IN (152, 153, 154)");
while ($row = mysqli_fetch_assoc($result)) {
    echo "ID: " . $row['scent_id'] . " | 이름: " . $row['scent_name'] . "\n";
}
// 출력: ID: 152 | 이름: 롬브르단로
// 한글이 정상적으로 보이면 OK
mysqli_close($con);
?>
```

#### 2. MySQL CLI로 HEX 검증
```bash
mysql> SELECT scent_id, scent_name, HEX(scent_name) FROM scents WHERE scent_id = 152;
# scent_id | scent_name | HEX(scent_name)
# 152      | ?Һ긣?ܷ?   | EC9BAEBCABEBA5B4EBA19C

# HEX가 EC로 시작하면 UTF-8 정상
# HEX가 3F(?)로 시작하면 인코딩 깨짐
```

### UTF-8 체크리스트

#### 모든 PHP 파일에서 확인할 것

- [ ] 파일 상단에 `header('Content-Type: text/html; charset=UTF-8');` 포함
- [ ] DB 연결 직후 `mysqli_set_charset($con, "utf8mb4");` 실행
- [ ] DB 연결 직후 `SET NAMES utf8mb4` 실행
- [ ] INSERT/UPDATE 시 `mysqli_real_escape_string()` 사용
- [ ] HTML 출력 시 `htmlspecialchars($value, ENT_QUOTES, 'UTF-8')` 사용

#### 절대 하지 말 것

- [ ] MySQL CLI로 한글 데이터 INSERT/UPDATE 금지
- [ ] charset 설정 없이 mysqli_connect() 사용 금지
- [ ] 한글 데이터를 직접 SQL에 하드코딩 금지

### 트러블슈팅

**Q: MySQL CLI에서 한글이 깨져 보입니다**
A: 정상입니다. MySQL CLI는 Windows에서 UTF-8을 제대로 표시하지 못합니다. PHP로 검증하세요.

**Q: PHP로 조회해도 한글이 깨집니다**
A: mysqli_set_charset()와 SET NAMES utf8mb4를 실행했는지 확인하세요.

**Q: 데이터가 이미 깨진 상태입니다**
A: MySQL CLI로 수정하지 말고, PHP 스크립트로 올바른 데이터를 다시 INSERT하세요.

**Q: HEX 값이 3F3F3F...입니다**
A: 데이터가 완전히 손상되었습니다. 원격 서버나 백업에서 복구하세요.

---

## 📄 페이징 구현 가이드

### 개요

HQ 모든 페이지에 통합 페이징 시스템이 적용되었습니다.

#### 주요 특징
- **공통 변수**: `$defaultRowsPage` (common.php에서 정의, 현재 5개/페이지)
- **공통 파일**: `public/inc/common_pagination.php` (자동 페이징 HTML 생성)
- **공통 스크립트**: `public/js/js.php` (이벤트 위임 방식)
- **AJAX 기반**: 전체 페이지 리로드 없이 테이블만 업데이트
- **필터 유지**: 검색/필터 조건을 페이지 전환 시에도 유지

### 공통 파일 구조

#### 1. `public/inc/common.php`
페이지당 기본 레코드 수 정의:
```php
// 페이징 기본 설정
$defaultRowsPage = 5;  // 페이지당 표시할 레코드 수
```

#### 2. `public/inc/common_pagination.php`
자동 페이징 HTML 생성 파일

**입력 변수:**
```php
$paginationConfig = [
    'table' => 'customers c',           // 필수: 테이블명 (별칭 포함 가능)
    'where' => 'c.deleted_at IS NULL',  // 필수: WHERE 조건
    'join' => 'LEFT JOIN ...',          // 선택: JOIN 구문
    'orderBy' => 'c.created_at DESC',   // 선택: ORDER BY (기본: created_at DESC)
    'rowsPerPage' => $defaultRowsPage,  // 선택: 페이지당 레코드 수 (기본: 25)
    'targetId' => '#customerTableBody', // 선택: 타겟 tbody ID
    'atValue' => encryptValue('10')     // 선택: 액션 토큰
];
```

**출력 변수:**
```php
$pagination  // 페이징 HTML (문자열)
```

#### 3. `public/js/js.php`
공통 JavaScript 페이징 처리 함수

```javascript
/**
 * 공용 페이징 시스템
 * 이벤트 위임 방식으로 동적 생성된 페이징 버튼 자동 처리
 */
window.changePage = function(elem, atValue) {
    const pageNum = elem.getAttribute('data-p');      // 페이지 번호
    const targetId = elem.getAttribute('data-id');    // 타겟 tbody ID

    // FormData 생성
    const formData = new FormData();
    formData.append('p', pageNum);
    formData.append('at', atValue);

    // 현재 페이지의 검색 필터 값 자동 수집
    const searchArea = elem.closest('.tab-page, .wrap, .container')
                          ?.querySelector('.filter-toolbar, .search-bar');
    if (searchArea) {
        const inputs = searchArea.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            if (input.name && input.value) {
                formData.append(input.name, input.value);
            }
        });
    }

    // AJAX 요청
    updateAjaxContent(formData, function(data) {
        // 테이블 업데이트
        if (targetId && data.html) {
            document.querySelector(targetId).innerHTML = data.html;
        }

        // 페이징 업데이트
        if (data.pagination) {
            const pagingContainer = document.querySelector('.paging[data-id="' + targetId + '"]');
            if (pagingContainer) {
                pagingContainer.innerHTML = data.pagination;
            }
        }
    });
};
```

### 페이징 처리 방법

#### 기본 패턴 (일반 쿼리)

```php
// 1. POST 핸들러 수정: 페이지 파라미터 'p' 허용
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!empty($_POST['action']) || isset($_POST['p']))) {
    $action = $_POST['action'] ?? 'filter_customers';

    switch ($action) {
        case 'filter_customers':
            // 2. 필터 파라미터 받기
            $searchKeyword = $_POST['search'] ?? '';
            $filterVendor = $_POST['vendor'] ?? '';

            // 3. WHERE 조건 구성
            $searchString = "c.deleted_at IS NULL";
            if ($searchKeyword) {
                $searchKeywordEsc = escapeString($searchKeyword);
                $searchString .= " AND c.company_name LIKE '%{$searchKeywordEsc}%'";
            }
            if ($filterVendor) {
                $filterVendorEsc = escapeString($filterVendor);
                $searchString .= " AND c.vendor_id = '{$filterVendorEsc}'";
            }

            // 4. SELECT 쿼리 작성
            $sql = "SELECT c.*, v.company_name as vendor_name
                    FROM customers c
                    LEFT JOIN vendors v ON c.vendor_id = v.vendor_id
                    WHERE {$searchString}";

            // 5. 페이징 설정
            $paginationConfig = [
                'table' => 'customers c',
                'where' => $searchString,
                'join' => 'LEFT JOIN vendors v ON c.vendor_id = v.vendor_id',
                'orderBy' => 'c.created_at DESC',
                'rowsPerPage' => $defaultRowsPage,
                'targetId' => '#customerTableBody',
                'atValue' => encryptValue('10')
            ];

            // 6. LIMIT 추가
            $rowsPage = $paginationConfig['rowsPerPage'];
            $p = $_POST['p'] ?? 1;
            $curPage = $rowsPage * ($p - 1);
            $sql .= " ORDER BY c.created_at DESC LIMIT {$curPage}, {$rowsPage}";

            // 7. 쿼리 실행
            $result = mysqli_query($con, $sql);

            // 8. 페이징 HTML 생성
            require $_SERVER['DOCUMENT_ROOT'] . '/inc/common_pagination.php';
            $response['pagination'] = $pagination ?? '';

            // 9. tbody HTML 생성
            $html = '';
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $html .= '<tr>';
                    $html .= '<td>' . htmlspecialchars($row['company_name']) . '</td>';
                    // ...
                    $html .= '</tr>';
                }
            } else {
                $html = '<tr><td colspan="10">데이터가 없습니다.</td></tr>';
            }

            $response['result'] = true;
            $response['html'] = $html;
            $response['item']['count'] = mysqli_num_rows($result);
            break;
    }
}
```

### GROUP BY 처리

**중요**: `common_pagination.php`는 GROUP BY가 있는 쿼리를 지원하지 않습니다.
GROUP BY를 사용하는 경우 **수동으로 페이징 HTML을 생성**해야 합니다.

#### GROUP BY 수동 페이징 패턴

```php
case 'filter_vendors':
    $searchKeyword = $_POST['search'] ?? '';

    // WHERE 조건
    $searchString = "v.deleted_at IS NULL";
    if ($searchKeyword) {
        $keywordEsc = escapeString($searchKeyword);
        $searchString .= " AND v.company_name LIKE '%{$keywordEsc}%'";
    }

    // SELECT 쿼리 (GROUP BY 포함)
    $sql = "SELECT v.*,
                   u.name as user_name,
                   COUNT(aa.vendor_id) as account_count
            FROM vendors v
            LEFT JOIN users u ON v.user_id = u.user_id
            LEFT JOIN account_assignments aa ON v.vendor_id = aa.vendor_id
            WHERE {$searchString}
            GROUP BY v.vendor_id";

    // 수동 페이징 처리
    $rowsPage = $defaultRowsPage;
    $p = $_POST['p'] ?? 1;
    $curPage = $rowsPage * ($p - 1);
    $sql .= " ORDER BY v.created_at DESC LIMIT {$curPage}, {$rowsPage}";

    // COUNT DISTINCT 사용
    $countSql = "SELECT COUNT(DISTINCT v.vendor_id) as total
                 FROM vendors v
                 WHERE {$searchString}";

    $countResult = mysqli_query($con, $countSql);
    $totalRows = 0;
    if ($countResult && $row = mysqli_fetch_assoc($countResult)) {
        $totalRows = $row['total'];
    }

    // 페이징 HTML 수동 생성
    $pagination = '';
    if ($totalRows > 0) {
        $totalPages = ceil($totalRows / $rowsPage);
        $currentPage = $p;
        $startPage = max(1, $currentPage - 5);
        $endPage = min($totalPages, $currentPage + 5);
        $atValue = encryptValue('10');
        $targetId = '#vendorTableBody';

        $pagination .= '<div class="pagination" data-at="' . $atValue . '">';

        // 이전 버튼
        if ($currentPage > 1) {
            $prevPage = $currentPage - 1;
            $pagination .= '<a href="#" data-p="' . $prevPage . '" data-id="' . htmlspecialchars($targetId) . '">&laquo; 이전</a>';
        }

        // 페이지 번호들
        for ($i = $startPage; $i <= $endPage; $i++) {
            if ($i == $currentPage) {
                $pagination .= '<a href="#" class="active" data-p="' . $i . '" data-id="' . htmlspecialchars($targetId) . '">' . $i . '</a>';
            } else {
                $pagination .= '<a href="#" data-p="' . $i . '" data-id="' . htmlspecialchars($targetId) . '">' . $i . '</a>';
            }
        }

        // 다음 버튼
        if ($currentPage < $totalPages) {
            $nextPage = $currentPage + 1;
            $pagination .= '<a href="#" data-p="' . $nextPage . '" data-id="' . htmlspecialchars($targetId) . '">다음 &raquo;</a>';
        }

        $pagination .= '</div>';
        $pagination .= '<div class="pagination-info">전체 ' . number_format($totalRows) . '개 / ' . $currentPage . ' / ' . $totalPages . ' 페이지</div>';
    }

    $response['pagination'] = $pagination;
    break;
```

### 레이아웃 구조

#### HTML 구조

```html
<!-- 카드 헤더: 제목 + 필터 -->
<div class="card-hd-content">
    <div class="card-hd-title-area">
        <div class="card-ttl">고객 목록</div>
        <div class="card-sub">고객 정보 관리</div>
    </div>

    <!-- 필터 툴바 -->
    <div class="filter-toolbar">
        <div class="filter-group">
            <label>벤더</label>
            <select id="filterVendor" name="vendor" class="form-control">
                <option value="">전체</option>
            </select>
        </div>

        <div class="filter-group">
            <label>검색</label>
            <input type="text" id="searchKeyword" name="search" class="form-control"
                   onkeypress="if(event.key==='Enter') filterCustomers()">
        </div>

        <button class="btn primary" onclick="filterCustomers()">조회</button>
    </div>
</div>

<!-- 테이블 영역 -->
<div class="table-scroll">
    <table class="data-table">
        <thead>
            <tr>
                <th>고객명</th>
                <th>벤더</th>
                <th>담당자</th>
                <th>등록일</th>
                <th>관리</th>
            </tr>
        </thead>
        <tbody id="customerTableBody">
            <!-- AJAX로 동적 로드 -->
        </tbody>
    </table>
</div>

<!-- 페이징 영역 (중요: data-id 속성 필수!) -->
<div class="paging" data-id="#customerTableBody"></div>
```

#### 필터 입력 필드

**중요**: 페이지 전환 시 필터 상태를 유지하려면 모든 필터 입력에 `name` 속성이 필요합니다.

```html
<!-- ✅ 올바른 예 -->
<select id="filterVendor" name="vendor" class="form-control">
<input type="text" id="searchKeyword" name="search" class="form-control">

<!-- ❌ 잘못된 예 (name 속성 없음) -->
<select id="filterVendor" class="form-control">
<input type="text" id="searchKeyword" class="form-control">
```

### 페이징 트러블슈팅

#### 문제 1: 페이징 영역이 안 보임

**증상:**
```json
{
  "pagination": ""
}
```

**원인:**
- GROUP BY를 사용했는데 `common_pagination.php`를 require 했음
- `$paginationConfig` 변수가 잘못 설정됨

**해결:**
- GROUP BY 쿼리는 수동 페이징 사용
- `$paginationConfig`의 `table`, `where` 확인

#### 문제 2: 페이지 전환 시 필터가 초기화됨

**원인:**
- 필터 입력 필드에 `name` 속성 누락

**해결:**
```html
<!-- ✅ 올바른 예 -->
<input type="text" id="searchKeyword" name="search" class="form-control">
<select id="filterVendor" name="vendor" class="form-control">
```

#### 문제 3: 페이징 버튼 클릭이 작동 안 함

**원인:**
- `data-id` 속성 누락 또는 잘못된 셀렉터
- `data-at` 속성 누락

**해결:**
```html
<!-- 페이징 컨테이너에 data-id 속성 추가 -->
<div class="paging" data-id="#customerTableBody"></div>
```

```php
// atValue 설정 확인
$paginationConfig = [
    // ...
    'atValue' => encryptValue('10')
];
```

### 페이징 체크리스트

#### 백엔드 (PHP)
- [ ] POST 핸들러에 `isset($_POST['p'])` 조건 추가
- [ ] `$defaultRowsPage` 변수 사용
- [ ] WHERE 조건 `$searchString` 생성
- [ ] GROUP BY 사용 여부 확인
  - [ ] 사용 안 함 → `common_pagination.php` require
  - [ ] 사용함 → 수동 페이징 HTML 생성
- [ ] LIMIT 절 추가: `LIMIT {$curPage}, {$rowsPage}`
- [ ] `$response['pagination']` 설정

#### 프론트엔드 (HTML)
- [ ] `<div class="paging" data-id="#tableBodyId"></div>` 추가
- [ ] 모든 필터 입력에 `name` 속성 추가
- [ ] tbody에 고유 ID 부여

#### 프론트엔드 (JavaScript)
- [ ] 필터 함수에서 `response.pagination` 처리
- [ ] 페이징 컨테이너 업데이트 로직 추가
- [ ] 페이지 로드 시 자동 조회 호출

---

**마지막 업데이트**: 2025-11-13 (v1.3) - UTF-8 인코딩 가이드 및 페이징 구현 가이드 추가
