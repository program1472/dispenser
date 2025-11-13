# 코딩 표준 (Coding Standards)

> 모든 포털에 적용되는 코딩 규약, 파일 구조, 보안 규칙

---

## 목차

1. [언어 정책](#언어-정책)
2. [파일 구조 규칙](#파일-구조-규칙)
3. [AJAX 라우팅 규칙](#ajax-라우팅-규칙)
4. [응답 포맷 표준](#응답-포맷-표준)
5. [폼 데이터 전송 패턴](#폼-데이터-전송-패턴)
6. [보안 규칙](#보안-규칙)
7. [페이징 시스템](#페이징-시스템-pagination)
8. [트랜잭션 및 검증](#트랜잭션-및-검증)
9. [개발 체크리스트](#개발-체크리스트)

---

## 언어 정책

**모든 설명과 커뮤니케이션은 한국어로 작성합니다.**

- AI 어시스턴트의 모든 응답은 한국어로 제공
- 코드 주석도 가능한 한국어로 작성
- 에러 메시지 및 사용자 안내 메시지는 반드시 한국어

---

## 파일 구조 규칙

### 공통 파일 로드

- 모든 페이지는 `inc/common.php`를 로드해 **$con(MySQLi)** 로 DB 쿼리 수행
- 공통 응답은 전역 **$response** 배열에 담고 **Finish()**로 종료 (항상 JSON)
- 모든 `$_POST`, `$_GET`은 **`_ajax_.php`를 반드시 경유**하고 `decryptArrayRecursive()`로 복호화 후 사용
- 서버에서 내려주는 민감/구조 데이터는 필요 시 `encryptValue()`로 암호화 가능
- **입력값(프런트→서버)은 암호화 하지 않는다**

### 전역 변수 규칙

**중요: `$con`과 `$response`는 `common.php`에서 이미 전역으로 선언된 변수입니다.**

- **$con (MySQLi 연결)**:
  - 개별 페이지에서 `global $con;` 선언 금지
  - `dbconfig.php` → `MySQLi.php` → `common.php` 경로로 이미 연결됨
  - 모든 페이지에서 바로 `$con` 사용 가능

- **$response (응답 배열)**:
  - 개별 페이지에서 `$response = [...]` 초기화 금지
  - 개별 페이지에서 `global $response;` 선언 금지
  - `common.php`에서 이미 초기화되어 있으므로 바로 사용

```php
// 잘못된 예시
global $con;  // 불필요한 선언!
global $response;  // 불필요한 선언!
$response = ['result' => false];  // 초기화 금지!

// 올바른 예시
// 아무 선언 없이 바로 사용
$sql = "SELECT * FROM users";
$result = mysqli_query($con, $sql);

$response['result'] = true;
$response['msg'] = '조회 성공';
$response['items'] = mysqli_fetch_all($result, MYSQLI_ASSOC);
Finish();
```

### 경로 상수 규칙

**중요: 파일 경로는 반드시 `common.php`에 정의된 상수를 사용해야 합니다.**

로컬 서버와 원격 서버의 경로가 다를 수 있으므로, `$_SERVER['DOCUMENT_ROOT']`를 직접 사용하지 않습니다.

**사용 가능한 경로 상수:**
- `INC_ROOT`: `/inc` 디렉토리 경로
- `INCLUDES_ROOT`: `/inc` 디렉토리 경로 (INC_ROOT와 동일)
- 기타 `common.php`에 정의된 경로 상수

```php
// ❌ 잘못된 예시 (서버 경로 의존)
require $_SERVER['DOCUMENT_ROOT'] . '/inc/common_pagination.php';
require dirname(__DIR__) . '/inc/common_pagination.php';

// ✅ 올바른 예시 (경로 상수 사용)
require INC_ROOT . '/common_pagination.php';
require INCLUDES_ROOT . '/common_pagination.php';
```

**적용 대상:**
- 모든 `require`, `require_once`, `include`, `include_once` 구문
- 특히 공통 파일 로드 시 필수 적용
  - `common_pagination.php`
  - `functions/*.php`
  - 기타 공통 헬퍼 파일

### 절대 금지 규칙

**1. DB 설정 파일 중복 로드 금지**
- `require_once dirname(__DIR__, 2) . '/dbconfig.php';` 각 페이지에 삽입 금지
- `common.php`에서 이미 `dbconfig.php`를 로드하므로 중복 불필요

**2. 개별 CSS 파일 로드 금지**
- 각 페이지에서 `<link rel="stylesheet">` 직접 삽입 금지
- `common.php`에서 동적으로 CSS를 로드하므로 중복 불필요
- 필요한 스타일은 기존 공용 CSS 파일에 추가

**3. 페이지 내 CSS 코드 작성 금지**
- `<style>` 태그 또는 인라인 스타일 사용 금지
- 모든 스타일은 공용 CSS 파일(`/css/style.css`, `/css/tem.css` 등)에 작성
- 기존 페이지에 삽입된 CSS는 공용 CSS로 이동 후 삭제

**4. 페이지 내 JavaScript 최소화**
- 재사용 가능한 함수를 페이지 내 `<script>`에 작성 금지
- 공통 기능은 `/js/js.php` 또는 별도 공용 JS 파일로 분리
- 페이지별 특수 로직만 인라인으로 허용

---

## AJAX 라우팅 규칙

### URL 형식 및 용도

모든 AJAX 요청은 **용도에 따라** 다른 URL을 사용해야 합니다:

**1) 페이지 초기 로드 (index.php 경유)**
- `loadPage()` 함수: `url: "<?= SRC ?>/"`
- `loadTabContent()` 함수: `url: "<?= SRC ?>/"`
- **라우팅**: `index.php`로 전달됨
- **용도**: 전체 페이지 HTML을 반환받을 때

**2) 서버와의 모든 데이터 통신 (_ajax_.php 경유)**
- `updateAjaxContent()` 함수: `url: "<?= SRC ?>/" + window.pageName`
- **라우팅**: `.htaccess`가 `_ajax_.php`로 자동 라우팅
- **용도**: CRUD 작업, 필터링, 검색 등 모든 데이터 통신

### .htaccess 라우팅 규칙

```apache
# public/.htaccess
RewriteEngine On
RewriteBase /

# 실제 파일/디렉터리면 리라이트 패스
RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]

# 모든 경로를 _ajax_.php로 자동 리라이트
RewriteRule ^(.+)$ _ajax_.php  [L]
```

**매우 중요: AJAX URL 라우팅 규칙**

**모든 페이지는 스크립트 상단에 `window.pageName` 선언 필수**

```javascript
<script>
// 페이지 이름 (AJAX 호출용)
window.pageName = '<?= encryptValue(date('Y-m-d') . '/my_page') ?>';
</script>
```

**추가 규칙:**
- **절대 `/_ajax_.php/`를 명시적으로 포함하지 말 것** (중복 라우팅 발생)
- `_ajax_.php`는 URL path에서 암호화된 토큰을 추출하여 복호화 처리
- 복호화된 형식: `YYYY-MM-DD/page_name` (예: `2025-01-08/customer_list`)

### 탭 AJAX 요청 규칙

**탭 AJAX 요청 시 menuName 형식 준수:**
- 단순 페이지명만 전송 금지 (예: `'customer_vendor'`)
- 반드시 `날짜/페이지명` 형식으로 전송 (예: `'2025-11-07/customer_vendor'`)

**올바른 코드 예시:**
```javascript
const today = '<?= date('Y-m-d') ?>';
const menuNameValue = today + '/' + pageName;
const data = {};
data['<?= encryptValue('menuName') ?>'] = menuNameValue;

$.ajax({
  type: "POST",
  url: "<?= SRC ?>/",  // index.php로 전달
  dataType: "html",
  data: data,
  cache: false
});
```

---

## 응답 포맷 표준

### PHP 서버 응답

**중요: $response 변수는 `common.php`에서 전역으로 선언된 변수입니다.**
- 개별 페이지에서 `$response` 초기화 금지 (예: `$response = ['result' => false, 'data' => []]`)
- `common.php`에서 이미 선언되었으므로 바로 사용

**올바른 응답 키**:
- `result`: 반드시 `true` 또는 `false` 사용
- `msg`: 응답 메시지
- `html`: HTML 콘텐츠 (페이지 로드 시)
- `item`: 단일 데이터 객체
- `items`: 복수 데이터 배열
- `error`: 오류 정보 객체

**허용된 키 이외 사용 금지**: `data`, `SESSION`, `menus`, `events`, `totalCount`, `approval`, `pagination`, `table_array` 등 사용 금지

```php
// 성공
$response['result'] = true;
$response['msg'] = '성공 메시지';
Finish();

// 단일 데이터 반환
$response['result'] = true;
$response['item'] = $row;  // 단일 객체
Finish();

// 복수 데이터 반환
$response['result'] = true;
$response['items'] = $rows;  // 배열
Finish();

// HTML 반환 (필터/조회용)
$response['result'] = true;
$response['html'] = '<tr>...</tr>';  // tbody 내부 HTML만
Finish();

// 실패
$response['result'] = false;
$response['error'] = ['msg' => '오류 메시지', 'code' => 400];
Finish();
```

### JavaScript 처리

```javascript
// updateAjaxContent 사용 예시
const data = {};
data['<?= encryptValue('action') ?>'] = 'filter_customers';
data['<?= encryptValue('search') ?>'] = searchValue;

updateAjaxContent(data, function(response) {
    if (response.result && response.html) {
        // tbody만 업데이트 (전체 페이지 리로드 하지 않음)
        document.querySelector('#tblCustomers tbody').innerHTML = response.html;
    }
});
```

### 필터/조회 HTML 생성 주의사항

**초기 로드 HTML과 완전히 동일한 구조 유지**

```php
case 'filter_customers':
    // _ajax_.php에서 이미 복호화되므로 일반 키로 접근
    $searchKeyword = isset($_POST['search']) ? $_POST['search'] : '';

    // SQL 쿼리 실행
    $result = mysqli_query($con, $sql);

    // HTML 생성 - 초기 로드와 동일한 구조로
    $html = '';
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($row['id']) . '</td>';
            $html .= '<td><strong>' . htmlspecialchars($row['name']) . '</strong></td>'; // 초기 로드처럼 <strong> 사용
            $html .= '<td>' . number_format($row['count']) . '</td>'; // number_format 적용
            // ... 나머지 필드들도 초기 로드와 동일하게
            $html .= '</tr>';
        }
    } else {
        $html = '<tr><td colspan="N" style="text-align:center;">데이터 없음</td></tr>'; // colspan 정확히
    }

    $response['result'] = true;
    $response['html'] = $html;
    Finish();
```

---

## 폼 데이터 전송 패턴

### FormData + fieldMap 패턴 (권장)

**이 패턴을 사용해야 하는 이유:**
- 폼의 모든 필드를 자동으로 수집
- HTML form의 `name` 속성 기반으로 동작
- 코드 중복 최소화
- 필드 추가/제거 시 JavaScript 수정 불필요
- 벤더/영업사원/고객 탭에서 검증된 안정적인 방식

```javascript
// 올바른 방법 - FormData + fieldMap 패턴
window.saveCustomer = function() {
  const form = document.getElementById('frmCustomer');

  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  const formData = new FormData(form);
  const data = {};

  const mode = document.getElementById('modalMode').value;
  data['<?= encryptValue('action') ?>'] = mode === 'add' ? 'add_customer' : 'update_customer';

  // Pre-encrypted field names mapping
  const fieldMap = {
    'customer_id': '<?= encryptValue('customer_id') ?>',
    'name': '<?= encryptValue('name') ?>',
    'email': '<?= encryptValue('email') ?>',
    'phone': '<?= encryptValue('phone') ?>',
    'address': '<?= encryptValue('address') ?>',
    'vendor_id': '<?= encryptValue('vendor_id') ?>',
    'sales_rep_id': '<?= encryptValue('sales_rep_id') ?>',
    'payment_method': '<?= encryptValue('payment_method') ?>',
    'cms_bank_name': '<?= encryptValue('cms_bank_name') ?>',
    'cms_account_number': '<?= encryptValue('cms_account_number') ?>',
    'cms_account_holder': '<?= encryptValue('cms_account_holder') ?>',
    'contact_person': '<?= encryptValue('contact_person') ?>',
    'contact_phone': '<?= encryptValue('contact_phone') ?>',
    'contact_email': '<?= encryptValue('contact_email') ?>',
    'notes': '<?= encryptValue('notes') ?>'
  };

  // FormData의 모든 항목을 암호화된 키로 변환
  for (let [key, value] of formData.entries()) {
    if (fieldMap[key]) {
      data[fieldMap[key]] = value;
    }
  }

  updateAjaxContent(data, function(response) {
    if (response.result) {
      alert(response.msg || '저장되었습니다.');
      closeCustomerModal();
      location.reload();
    } else {
      alert(response.error?.msg || '저장에 실패했습니다.');
    }
  }, false);
}
```

### HTML 폼 구조 요구사항

FormData 패턴을 사용하려면 HTML form에 다음 사항이 필요합니다:

```html
<!-- 올바른 폼 구조 -->
<form id="frmCustomer" onsubmit="return false;">
  <!-- form 자동 제출 방지 필수 -->

  <input type="hidden" id="customerId" name="customer_id">
  <!-- name 속성이 fieldMap의 key와 일치해야 함 -->

  <input type="text" id="name" name="name" required>
  <!-- id는 JavaScript용, name은 FormData용 -->

  <select id="vendorId" name="vendor_id">
    <!-- select도 동일하게 name 속성 필요 -->
  </select>

  <textarea id="notes" name="notes"></textarea>
  <!-- textarea도 동일 -->
</form>

<!-- 버튼은 type="button" 필수 (form 제출 방지) -->
<button type="button" class="btn primary" onclick="saveCustomer()">저장</button>
```

### 폼 데이터 전송 체크리스트

폼 저장 시 데이터가 하나만 전송되거나 로그아웃되는 경우:

- [ ] **form 태그에 `onsubmit="return false;"` 추가** (자동 제출 방지)
- [ ] **버튼 type을 `type="button"`으로 설정** (submit 방지)
- [ ] **모든 input/select/textarea에 `name` 속성 추가**
- [ ] **`name` 속성이 fieldMap의 key와 일치하는지 확인**
- [ ] **FormData + fieldMap 패턴 사용**
- [ ] **`window.pageName` 설정 확인** (각 탭 페이지 스크립트 상단)

### 일반적인 오류 패턴

```javascript
// 오류 1: form 자동 제출로 인한 페이지 리로드
<form id="frmCustomer">  <!-- onsubmit 없음 -->
  <button class="btn primary" onclick="saveCustomer()">저장</button>
  <!-- type이 없으면 기본값 submit으로 form 제출됨 -->
</form>
// 결과: POST 데이터가 하나만 전송되고 페이지가 리로드됨

// 수정
<form id="frmCustomer" onsubmit="return false;">
  <button type="button" class="btn primary" onclick="saveCustomer()">저장</button>
</form>

// 오류 2: name 속성 누락
<input type="text" id="email" class="form-control">
// FormData는 name 속성이 있는 필드만 수집
// 결과: email 필드가 전송되지 않음

// 수정
<input type="text" id="email" name="email" class="form-control">

// 오류 3: fieldMap key와 name 속성 불일치
const fieldMap = {
  'vendor_id': '<?= encryptValue('vendor_id') ?>'
};
<select id="vendorId" name="vendorID">  <!-- 대소문자 불일치 -->
// 결과: vendor_id가 전송되지 않음

// 수정
<select id="vendorId" name="vendor_id">  <!-- fieldMap과 일치 -->
```

---

## 보안 규칙

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

### 사용자 친화적 에러 메시지

**MySQL 에러를 사용자가 이해하기 쉬운 메시지로 변환합니다.**

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

---

## 페이징 시스템 (Pagination)

### 개요

HQ 모든 페이지에 통합 페이징 시스템이 적용되었습니다.

**핵심 파일:**
- `public/inc/common.php`: `$defaultRowsPage = 5` 정의
- `public/inc/common_pagination.php`: 자동 페이징 HTML 생성
- `public/js/js.php`: `changePage()` 함수 (이벤트 위임)
- `public/css/hq.css`: 페이징 스타일

**적용된 페이지:**
- 고객관리: customer_list.php, vendor_mgmt.php, sales_rep_mgmt.php
- 제품관리: product_device.php, product_fragrance.php, product_accessory.php, product_supplies.php, product_content.php

### 기본 구현 패턴 (일반 쿼리)

```php
// 1. POST 핸들러에 페이지 파라미터 허용
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!empty($_POST['action']) || isset($_POST['p']))) {
    $action = $_POST['action'] ?? 'filter_customers';

    switch ($action) {
        case 'filter_customers':
            // 2. 필터 파라미터 받기
            $searchKeyword = $_POST['search'] ?? '';

            // 3. WHERE 조건 구성
            $searchString = "c.deleted_at IS NULL";
            if ($searchKeyword) {
                $searchKeywordEsc = escapeString($searchKeyword);
                $searchString .= " AND c.company_name LIKE '%{$searchKeywordEsc}%'";
            }

            // 4. SELECT 쿼리
            $sql = "SELECT c.* FROM customers c WHERE {$searchString}";

            // 5. 페이징 설정
            $paginationConfig = [
                'table' => 'customers c',
                'where' => $searchString,
                'join' => 'LEFT JOIN vendors v ON c.vendor_id = v.vendor_id',
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
            require INC_ROOT . '/common_pagination.php';
            $response['pagination'] = $pagination ?? '';

            // 9. tbody HTML 생성
            $html = '';
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $html .= '<tr><td>' . htmlspecialchars($row['company_name']) . '</td></tr>';
                }
            }

            $response['result'] = true;
            $response['html'] = $html;
            break;
    }
}
```

### GROUP BY 쿼리 처리 (수동 페이징)

**중요:** `common_pagination.php`는 GROUP BY 쿼리를 지원하지 않습니다. GROUP BY 사용 시 수동으로 페이징 HTML을 생성해야 합니다.

```php
case 'filter_vendors':
    $searchString = "v.deleted_at IS NULL";

    // GROUP BY 쿼리
    $sql = "SELECT v.*, GROUP_CONCAT(c.customer_name) as customers
            FROM vendors v
            LEFT JOIN account_assignments aa ON v.vendor_id = aa.vendor_id
            LEFT JOIN customers c ON aa.customer_id = c.customer_id
            WHERE {$searchString}
            GROUP BY v.vendor_id";

    // 수동 페이징
    $rowsPage = $defaultRowsPage;
    $p = $_POST['p'] ?? 1;
    $curPage = $rowsPage * ($p - 1);
    $sql .= " ORDER BY v.created_at DESC LIMIT {$curPage}, {$rowsPage}";

    // COUNT DISTINCT 사용
    $countSql = "SELECT COUNT(DISTINCT v.vendor_id) as total
                 FROM vendors v WHERE {$searchString}";
    $countResult = mysqli_query($con, $countSql);
    $totalRows = ($countResult && $row = mysqli_fetch_assoc($countResult)) ? $row['total'] : 0;

    // 페이징 HTML 수동 생성
    $pagination = '';
    if ($totalRows > 0) {
        $totalPages = ceil($totalRows / $rowsPage);
        $currentPage = $p;
        $startPage = max(1, $currentPage - 5);
        $endPage = min($totalPages, $currentPage + 5);

        $pagination .= '<div class="pagination" data-at="' . encryptValue('10') . '">';

        // 이전 버튼
        if ($currentPage > 1) {
            $pagination .= '<a href="#" data-p="' . ($currentPage - 1) . '" data-id="#vendorTableBody">&laquo; 이전</a>';
        }

        // 페이지 번호
        for ($i = $startPage; $i <= $endPage; $i++) {
            $active = ($i == $currentPage) ? ' class="active"' : '';
            $pagination .= '<a href="#"' . $active . ' data-p="' . $i . '" data-id="#vendorTableBody">' . $i . '</a>';
        }

        // 다음 버튼
        if ($currentPage < $totalPages) {
            $pagination .= '<a href="#" data-p="' . ($currentPage + 1) . '" data-id="#vendorTableBody">다음 &raquo;</a>';
        }

        $pagination .= '</div>';
        $pagination .= '<div class="pagination-info">전체 ' . number_format($totalRows) . '개 / ' . $currentPage . ' / ' . $totalPages . ' 페이지</div>';
    }

    $response['pagination'] = $pagination;
    break;
```

### HTML 레이아웃

```html
<!-- 필터 툴바: 모든 입력에 name 속성 필수 -->
<div class="filter-toolbar">
    <select id="filterVendor" name="vendor" class="form-control">
        <option value="">전체</option>
    </select>
    <input type="text" id="searchKeyword" name="search" class="form-control">
    <button class="btn primary" onclick="filterCustomers()">조회</button>
</div>

<!-- 테이블 -->
<div class="table-scroll">
    <table class="data-table">
        <thead><tr><th>고객명</th></tr></thead>
        <tbody id="customerTableBody">
            <!-- AJAX로 동적 로드 -->
        </tbody>
    </table>
</div>

<!-- 페이징 영역: data-id 속성 필수 -->
<div class="paging" data-id="#customerTableBody"></div>
```

### JavaScript 패턴

```javascript
// 필터 함수
window.filterCustomers = function() {
    const vendor = document.getElementById('filterVendor').value;
    const search = document.getElementById('searchKeyword').value;

    const data = {};
    data['<?= encryptValue('action') ?>'] = 'filter_customers';
    data['<?= encryptValue('vendor') ?>'] = vendor;
    data['<?= encryptValue('search') ?>'] = search;

    updateAjaxContent(data, function(response) {
        if (response.result && response.html) {
            // 테이블 업데이트
            document.querySelector('#customerTableBody').innerHTML = response.html;

            // 페이징 업데이트
            if (response.pagination) {
                const pagingContainer = document.querySelector('.paging[data-id="#customerTableBody"]');
                if (pagingContainer) {
                    pagingContainer.innerHTML = response.pagination;
                }
            }
        }
    });
};

// 페이지 로드 시 자동 조회
filterCustomers();
```

### 페이징 개발 체크리스트

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

### 주의사항

**필터 상태 유지:**
- 모든 필터 입력에 `name` 속성 필수
- `changePage()` 함수가 자동으로 필터 값을 수집하여 전송

**GROUP BY 주의:**
- `common_pagination.php`는 GROUP BY 쿼리 미지원
- GROUP BY 사용 시 반드시 수동 페이징 구현
- COUNT 쿼리에 `COUNT(DISTINCT primary_key)` 사용

**상세 가이드:**
- 상세 구현 예제: `docs/PAGINATION_IMPLEMENTATION.md`
- 참고 파일: `customer_list.php`, `vendor_mgmt.php`

---

## 트랜잭션 및 검증

### 트랜잭션 사용

관련 엔터티를 함께 생성/갱신하는 작업은 **트랜잭션** 필수:

```php
// 예시: 벤더 + 사용자 동시 생성
mysqli_begin_transaction($con);

try {
    // 1. vendors 테이블에 삽입
    $vendorId = generateVendorId($con);
    $sql1 = "INSERT INTO vendors (vendor_id, vendor_name, ...) VALUES (?, ?, ...)";
    $stmt1 = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($stmt1, "ss...", $vendorId, $vendorName, ...);
    mysqli_stmt_execute($stmt1);

    // 2. users 테이블에 삽입
    $sql2 = "INSERT INTO users (email, password, role_id, vendor_id, ...) VALUES (?, ?, ?, ?, ...)";
    $stmt2 = mysqli_prepare($con, $sql2);
    mysqli_stmt_bind_param($stmt2, "ssis...", $email, $hashedPassword, $roleId, $vendorId, ...);
    mysqli_stmt_execute($stmt2);

    // 커밋
    mysqli_commit($con);
    $response['result'] = true;
    $response['msg'] = '벤더 및 사용자 생성 완료';
} catch (Exception $e) {
    // 롤백
    mysqli_rollback($con);
    $response['result'] = false;
    $response['error'] = ['msg' => '생성 실패: ' . $e->getMessage(), 'code' => 500];
}

Finish();
```

### 공통 검증 항목

- **이메일**: 형식 검증 (`filter_var($email, FILTER_VALIDATE_EMAIL)`)
- **이메일 중복**: 가입 전 체크
- **필수값**: NULL 체크
- **권한**: 포털별 접근 권한 검증
- **ENUM/상태값**: 허용된 값만 저장

### 감사 로그

**로그 실패가 본처리를 막지 않도록 예외 삼킴**:

```php
try {
    // 본처리 (사용자 업데이트 등)
    $sql = "UPDATE users SET email = ? WHERE user_id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $newEmail, $userId);
    mysqli_stmt_execute($stmt);

    // 감사 로그 기록 (실패해도 무시)
    try {
        $logSql = "INSERT INTO audit_log (actor_user_id, action, target_table, target_id, old_value, new_value)
                   VALUES (?, 'UPDATE', 'users', ?, ?, ?)";
        $logStmt = mysqli_prepare($con, $logSql);
        mysqli_stmt_bind_param($logStmt, "iiss", $mb_id, $userId, $oldEmail, $newEmail);
        mysqli_stmt_execute($logStmt);
    } catch (Exception $logError) {
        // 로그 실패는 무시
        error_log("Audit log failed: " . $logError->getMessage());
    }

    $response['result'] = true;
    $response['msg'] = '업데이트 완료';
} catch (Exception $e) {
    $response['result'] = false;
    $response['error'] = ['msg' => $e->getMessage(), 'code' => 500];
}

Finish();
```

---

## 개발 체크리스트

### 신규 페이지 개발 시

- [ ] `inc/common.php` 로드 확인
- [ ] `dbconfig.php` 직접 로드하지 않기
- [ ] 페이지에 `<link>` CSS 로드 금지
- [ ] 페이지에 `<style>` 태그 사용 금지
- [ ] 필요한 스타일은 공용 CSS에 추가
- [ ] 재사용 가능한 JS는 공용 파일로 분리
- [ ] 암복호화 처리 적용
- [ ] 표준 응답 포맷 사용 (`$response` + `Finish()`, `echo json_encode()` 금지)
- [ ] **스크립트 상단에 `window.pageName` 전역 선언 (var 대신)**
- [ ] **`updateAjaxContent()` 함수 사용 (fetch/$.ajax 직접 사용 금지)**
- [ ] 더미데이터 20건 이상 포함
- [ ] CSV 내보내기 구현
- [ ] 상태 배지 적용
- [ ] 공통 CSS 로드 확인

### AJAX 동적 로드 페이지 개발 시 (탭 구조 등)

- [ ] **`window.pageName` 사용 (var 사용 금지 - 스코프 문제)**
- [ ] **onclick 인라인 이벤트 + `window.functionName` 패턴 사용**
- [ ] **모든 이벤트 핸들러 함수를 `window.functionName = function() {...}` 형식으로 선언**
- [ ] **FormData 사용 시 필드명 매핑 객체 사용 (JS 변수를 PHP encryptValue()에 직접 전달 금지)**
- [ ] **AJAX 필터 응답 HTML을 초기 로드와 완전히 동일하게 생성 (스타일, 구조, colspan 등)**
- [ ] **탭 로드 시 스크립트를 `new Function()` 또는 `appendChild()`로 실행**
- [ ] 페이지 최초 로드 시 버튼 클릭 정상 작동 테스트
- [ ] 검색/필터 결과가 초기 로드와 동일한 스타일로 표시되는지 확인

### 탭 페이지 개발 시

- [ ] AJAX URL을 `"<?= SRC ?>/"`로 설정
- [ ] `menuName`을 `날짜/페이지명` 형식으로 전송
- [ ] `today` 변수를 `<?= date('Y-m-d') ?>`로 선언
- [ ] `url: "#"` 절대 사용 금지
- [ ] 단순 페이지명만 전송 금지

### UI 작성 시

- [ ] 반응형 레이아웃 적용
- [ ] 상단 필터 고정 (사업장 선택 등)
- [ ] 모든 버튼에 명확한 액션 정의
- [ ] 로딩 상태 표시 (AJAX 요청 중)
- [ ] 에러 메시지 사용자 친화적으로 표시

### 배포 전 체크

- [ ] 문법 오류 검증 완료
- [ ] 계산식 정책 기준 검증
- [ ] 권한별 접근 제어 테스트
- [ ] AJAX 라우팅 정상 동작 확인
- [ ] 로그 기록 정상 작동 확인

---

**마지막 업데이트**: 2025-11-12

**문서 작성자**: Claude Code Team
