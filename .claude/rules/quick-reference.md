# 빠른 참조 (Quick Reference)

> 자주 사용하는 핵심 규칙 요약 — 개발 시 가장 먼저 확인하는 문서

---

## 📁 핵심 파일 구조

```
public/
├── index.php                   # 메인 진입점 (로그인 체크 + 라우팅)
├── _ajax_.php                  # AJAX 중계 허브 (암복호화 처리)
├── inc/
│   ├── common.php              # 전역 설정, DB 연결
│   ├── menus.php               # 포털별 메뉴 구조
│   ├── topArea.php             # 헤더/메뉴 렌더링
│   └── functions/
│       ├── ende.php            # 암복호화
│       └── MySQLi.php          # DB 헬퍼
├── utility/
│   └── autoload.php            # Composer Autoloader (mpdf, 기타 패키지)
├── doc/{role}/                 # 포털별 페이지
│   ├── hq/
│   ├── vendor/
│   ├── customer/
│   └── lucid/
├── css/                        # 공통 & 포털별 CSS
└── js/js.php                   # 공통 JavaScript
```

---

## 🔑 필수 함수 & 변수

### PHP
```php
// 전역 변수 (inc/common.php에서 초기화)
$con            // MySQLi 연결
$mb_id          // 로그인 사용자 ID
$mb_role        // 사용자 역할 코드
$roleName       // 포털명 (hq/vendor/customer/lucid)
$response       // 표준 응답 배열

// 암복호화
encryptValue($value)              // 단일 값 암호화
decryptValue($value)              // 단일 값 복호화
decryptArrayRecursive($array)     // 배열 재귀 복호화

// 응답 처리
Finish()                          // JSON 응답 출력 후 종료

// Composer Autoloader (외부 패키지 사용 시)
require_once __DIR__ . '/../utility/autoload.php';  // mpdf 등
```

### JavaScript
```javascript
// 페이지 로드 (메뉴 클릭)
loadPage(el, menuName)            // AJAX 페이지 로드, 탭 active 관리

// AJAX 데이터 처리 (필수!)
updateAjaxContent(data, callback, isAlert = true)
// - 서버와 통신하는 표준 함수 (fetch/$.ajax 직접 사용 금지!)
// - data: POST로 전송할 데이터 객체
// - callback: 응답 성공 시 실행할 콜백 함수
// - isAlert: 오류 시 자동 alert 표시 여부 (기본: true)
// - 사용 전 필수: 스크립트 상단에 window.pageName = '<?= encryptValue(date('Y-m-d') . '/menuName') ?>'; 선언
// - ⚠️ AJAX 동적 로드 페이지에서는 반드시 window.pageName 사용 (var 사용 시 스코프 문제 발생)

// 날짜 필터 (js.php에서 제공)
setDate(type, pid = '')           // 날짜 프리셋 설정
// 사용 가능한 타입:
// - 'today': 오늘
// - 'thisWeek': 이번 주 (월요일~오늘)
// - 'prevWeek': 지난 주 (월요일~일요일)
// - 'thisMonth': 이번 달 (1일~오늘)
// - 'prevMonth': 지난 달 (1일~말일)
// - '30days': 최근 30일
// - 'week': 최근 7일
```

---

## 🎯 요청 흐름

### 페이지 로드
```
1. 메뉴 클릭 → loadPage(this, 암호화토큰)
2. AJAX POST → index.php
3. index.php → doc/{role}/{menuName}.php 로드
4. HTML 응답 → #content 영역 삽입
```

### 데이터 처리 (AJAX)
```
1. updateAjaxContent(data, callback) 호출
2. AJAX POST → /{암호화토큰}
3. .htaccess RewriteRule이 자동으로 _ajax_.php로 라우팅
4. _ajax_.php → 토큰 복호화 → doc/{role}/{menuName}.php
5. 비즈니스 로직 처리 → Finish() JSON 응답
6. callback 실행 또는 alert

⚠️ IMPORTANT: updateAjaxContent()의 URL은 "<?= SRC ?>/" + pageName 형식 유지
   - .htaccess가 자동으로 _ajax_.php로 리라이트하므로 명시적으로 _ajax_.php를 포함하지 않음
   - 예: url: "<?= SRC ?>/" + pageName ✅
   - 예: url: "<?= SRC ?>/_ajax_.php/" + pageName ❌
```

### 탭 페이지 로드 (AJAX)
```
1. 탭 버튼 클릭 → loadCustomerTab(this, 암호화토큰)
2. AJAX POST → index.php
3. index.php → doc/{role}/{menuName}.php 로드
4. HTML 응답 → contentArea.innerHTML에 삽입
5. 스크립트 추출 후 new Function() 또는 appendChild로 실행
6. ✅ 중요: window.pageName으로 전역 변수 선언 (var 대신)
7. ✅ 중요: onclick 인라인 이벤트 + window.functionName 패턴 사용
8. ✅ 중요: AJAX 필터 응답 HTML은 초기 로드와 동일한 구조/스타일 유지
```

**🔧 AJAX 로드된 HTML에서 스크립트 실행 패턴:**
```javascript
// ❌ 잘못된 방법 - 스크립트가 실행되지 않음
$('#content').html(response);

// ✅ 올바른 방법 - 스크립트를 수동으로 파싱하여 실행
const tempDiv = document.createElement('div');
tempDiv.innerHTML = response;

// 스크립트 태그를 추출
const scripts = tempDiv.querySelectorAll('script');

// 스크립트를 제외한 내용을 먼저 삽입
const scriptsArray = Array.from(scripts);
scriptsArray.forEach(script => script.remove());
$('#content').html(tempDiv.innerHTML);

// 스크립트를 새로 생성하여 실행
scriptsArray.forEach(oldScript => {
  if (oldScript.src) {
    // 외부 스크립트
    const newScript = document.createElement('script');
    newScript.src = oldScript.src;
    newScript.async = false;
    document.body.appendChild(newScript);
  } else {
    // 인라인 스크립트 - Function 생성자로 전역 스코프에서 실행
    try {
      const scriptText = oldScript.textContent || oldScript.innerHTML;
      (new Function(scriptText))();
    } catch (e) {
      console.error('스크립트 실행 오류:', e, oldScript.textContent);
    }
  }
});
```

**⚠️ 중요:**
- `loadPage()` 함수와 탭 로드 함수 모두 이 패턴을 사용해야 함
- jQuery의 `.html()` 메서드는 보안상의 이유로 `<script>` 태그를 실행하지 않음
- `new Function()`으로 실행하면 전역 스코프에서 실행되어 `window.functionName` 선언이 제대로 동작함

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

**중요 사항:**
- 모든 AJAX 요청은 `.htaccess`가 자동으로 `_ajax_.php`로 라우팅
- `updateAjaxContent()` 함수에서 URL을 `"<?= SRC ?>/" + pageName` 형식으로 구성
- **절대 `/_ajax_.php/`를 명시적으로 포함하지 말 것** (중복 라우팅 발생)
- `_ajax_.php`는 URL path에서 암호화된 토큰을 추출하여 복호화 처리
- 복호화된 형식: `YYYY-MM-DD/page_name` (예: `2025-01-08/customer_list`)

---

## 📋 표준 응답 포맷

### PHP 서버 응답
```php
// common.php에서 선언된 전역 변수 $response 사용
// Finish() 함수로 JSON 출력 후 종료
// 허용 키: result, msg, html, item, items, error (이 외 사용 금지)

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
data[encryptedActionKey] = 'filter_customers';
data[encryptedSearchKey] = searchValue;

updateAjaxContent(data, function(response) {
    if (response.result && response.html) {
        // tbody만 업데이트 (전체 페이지 리로드 하지 않음)
        document.querySelector('#tblCustomers tbody').innerHTML = response.html;
    }
});
```

---

## 📝 폼 데이터 전송 패턴

### ✅ FormData + fieldMap 패턴 (권장)

**이 패턴을 사용해야 하는 이유:**
- 폼의 모든 필드를 자동으로 수집
- HTML form의 `name` 속성 기반으로 동작
- 코드 중복 최소화
- 필드 추가/제거 시 JavaScript 수정 불필요
- 벤더/영업사원/고객 탭에서 검증된 안정적인 방식

```javascript
// ✅ 올바른 방법 - FormData + fieldMap 패턴
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
      toast(response.msg || '저장되었습니다.');
      closeCustomerModal();
    } else {
      alert(response.error?.msg || '저장에 실패했습니다.');
    }
  }, false);
}
```

### ❌ 잘못된 방법 - getElementById 직접 사용

```javascript
// ❌ 잘못된 방법 - 각 필드를 개별적으로 읽음
window.saveCustomer = function() {
  const data = {};
  data['<?= encryptValue('action') ?>'] = 'add_customer';
  data['<?= encryptValue('name') ?>'] = document.getElementById('name').value;
  data['<?= encryptValue('email') ?>'] = document.getElementById('email').value;
  // ... 모든 필드에 대해 반복

  // 문제점:
  // 1. 코드 중복이 많음
  // 2. 필드 추가 시 JavaScript 수정 필요
  // 3. 필드 누락 가능성 높음
  // 4. 유지보수 어려움
}
```

### HTML 폼 구조 요구사항

FormData 패턴을 사용하려면 HTML form에 다음 사항이 필요합니다:

```html
<!-- ✅ 올바른 폼 구조 -->
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

<!-- ✅ 버튼은 type="button" 필수 (form 제출 방지) -->
<button type="button" class="btn primary" onclick="saveCustomer()">저장</button>
```

### 체크리스트: 폼 데이터 전송 문제 해결

폼 저장 시 데이터가 하나만 전송되거나 로그아웃되는 경우:

- [ ] **form 태그에 `onsubmit="return false;"` 추가** (자동 제출 방지)
- [ ] **버튼 type을 `type="button"`으로 설정** (submit 방지)
- [ ] **모든 input/select/textarea에 `name` 속성 추가**
- [ ] **`name` 속성이 fieldMap의 key와 일치하는지 확인**
- [ ] **FormData + fieldMap 패턴 사용**
- [ ] **`window.pageName` 설정 확인** (각 탭 페이지 스크립트 상단)
- [ ] **다른 작동하는 탭(벤더/영업사원)과 비교**

### 일반적인 오류 패턴

```javascript
// ❌ 오류 1: form 자동 제출로 인한 페이지 리로드
<form id="frmCustomer">  <!-- onsubmit 없음 -->
  <button class="btn primary" onclick="saveCustomer()">저장</button>
  <!-- type이 없으면 기본값 submit으로 form 제출됨 -->
</form>
// 결과: POST 데이터가 하나만 전송되고 페이지가 리로드됨

// ✅ 수정
<form id="frmCustomer" onsubmit="return false;">
  <button type="button" class="btn primary" onclick="saveCustomer()">저장</button>
</form>

// ❌ 오류 2: name 속성 누락
<input type="text" id="email" class="form-control">
// FormData는 name 속성이 있는 필드만 수집
// 결과: email 필드가 전송되지 않음

// ✅ 수정
<input type="text" id="email" name="email" class="form-control">

// ❌ 오류 3: fieldMap key와 name 속성 불일치
const fieldMap = {
  'vendor_id': '<?= encryptValue('vendor_id') ?>'
};
<select id="vendorId" name="vendorID">  <!-- 대소문자 불일치 -->
// 결과: vendor_id가 전송되지 않음

// ✅ 수정
<select id="vendorId" name="vendor_id">  <!-- fieldMap과 일치 -->
```

---

## 🔐 보안 규칙

### 토큰 기반 라우팅
- 모든 페이지 경로는 **암호화된 토큰** 형태로 전송
- 토큰 형식: `encryptValue("YYYY-MM-DD/menuName")`
- 날짜 검증: 요청 날짜가 `$today`와 일치해야 함

### 데이터 처리
- `$_POST`, `$_GET`은 `_ajax_.php`에서 자동 복호화
- 서버→클라이언트 민감 데이터는 필요 시 암호화
- **입력값(클라이언트→서버)은 암호화 하지 않음**

---

## 🎨 UI 공통 규칙

### CSS 로드 순서
1. `style.css` (기본 공통)
2. `tem.css` (템플릿)
3. `{role}.css` (포털별: hq.css, vendor.css 등)
4. `header.css` (헤더/메뉴/드롭다운)

### 공통 UI 요소
```html
<div id="tabs">         <!-- 메뉴 -->
<div id="content">      <!-- 동적 콘텐츠 영역 -->
<div class="pop">       <!-- 모달 팝업 -->
```

### 상태 배지 컬러
- **ACTIVE**: 초록
- **WARNING**: 노랑
- **GRACE**: 주황
- **TERMINATED**: 회색
- **PLANNED**: 회색
- **DUE**: 파랑
- **PAID**: 초록

---

## 📊 메뉴 ID 규칙

| 포털 | ID 형식 | 예시 |
|------|---------|------|
| HQ | H + 2자리 숫자 | H01, H02, H03 |
| VENDOR | V + 2자리 숫자 | V01, V02, V03 |
| CUSTOMER | C + 2자리 숫자 | C01, C02, C03 |
| LUCID | L + 2자리 숫자 | L01, L02, L03 |

### 서브메뉴
- 하위메뉴 ID: `상위ID-순번`
- 예시: `H02-1`, `H02-2`

---

## 💰 주요 정책 값

### 구독료
- 정기구독료: **29,700원/월**

### 벤더 정책
- 커미션: **매출 × 40%**
- 인센티브: **매출 × 5%**

### 루시드 정책
- 배분율: **콘텐츠 단가 × 50%** (고객 수정 요청 건만)

### 영업사원 인센티브
- 판매: **90,000원/대** → 15,000원 × 6회 분할
- 리뉴얼: **30,000원** (기본) / **40,000원** (연속)

### KPI 공식
```
KPI = 판매(40%) + 유지(25%) + 리뉴얼(20%) + 보고(15%)
```

### 콘텐츠 가격
- Basic: 11,000원
- Standard: 22,000원
- Deluxe: 110,000원
- Premium: 220,000원

---

## 📝 코드 작성 규칙

### ✅ Good (권장)
```php
// 표준 응답 - 전역 변수 $response 사용 (개별 키 할당)
// ✅ $response 전역 변수 + Finish() 사용
// 허용되는 키: result, msg, html, item, items, error (이 외 사용 금지)
$response['result'] = true;
$response['msg'] = '성공';
Finish();

// 에러 응답
$response['result'] = false;
$response['error'] = ['msg' => '오류 메시지', 'code' => 400];
Finish();

// 단일 데이터 반환
$response['result'] = true;
$response['item'] = $row;  // 단일 객체
Finish();

// 복수 데이터 반환
$response['result'] = true;
$response['items'] = $rows;  // 배열
Finish();

// HTML 반환 (필터/조회)
$response['result'] = true;
$response['html'] = '<tr>...</tr>';
Finish();

// ⚠️ 필터/조회 - tbody HTML 생성 시 주의사항
// 1. 초기 로드 HTML과 완전히 동일한 구조 유지
// 2. <strong>, 배지, number_format() 등 모든 스타일 요소 포함
// 3. colspan 숫자 정확히 일치시키기
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

```javascript
// JavaScript - AJAX 동적 로드 페이지 패턴 (onclick + window 함수)
// ✅ 권장: onclick 인라인 이벤트 + window.functionName 패턴

// 1. 스크립트 상단에 window.pageName 전역 변수 선언 (필수!)
window.pageName = '<?= encryptValue(date('Y-m-d') . '/vendor_mgmt') ?>';

// 2. HTML - onclick 인라인 이벤트 사용
<button id="btnFilter" onclick="filterVendors()">조회</button>
<input type="text" onkeypress="if(event.key==='Enter') filterVendors()">

// 3. 모든 함수를 window 객체에 할당
window.filterVendors = function() {
  const searchKeyword = document.getElementById('searchKeyword').value || '';

  const data = {};
  data['<?= encryptValue('action') ?>'] = 'filter_vendors';
  data['<?= encryptValue('search') ?>'] = searchKeyword;

  updateAjaxContent(data, function(response) {
    if (response.result && response.html) {
      document.getElementById('tblVendors').querySelector('tbody').innerHTML = response.html;
    } else {
      alert(response.error?.msg || '조회에 실패했습니다.');
    }
  });
};

// 4. FormData 사용 시 - 필드명 미리 암호화
window.saveVendor = function() {
  const form = document.getElementById('vendorForm');
  const formData = new FormData(form);
  const data = {};

  // Pre-encrypted field names mapping (PHP에서 미리 암호화)
  const fieldMap = {
    'vendor_id': '<?= encryptValue('vendor_id') ?>',
    'name': '<?= encryptValue('name') ?>',
    'email': '<?= encryptValue('email') ?>'
    // ... 모든 필드
  };

  for (let [key, value] of formData.entries()) {
    if (fieldMap[key]) {
      data[fieldMap[key]] = value;
    }
  }

  updateAjaxContent(data, callback);
};
```

### ❌ Bad (비권장)
```php
// ❌ echo json_encode() 직접 사용 금지!
echo json_encode(['result' => true, 'msg' => '성공']);
exit;

// ❌ echo json_encode() + exit 패턴 금지!
echo json_encode(['result' => false, 'error' => ['msg' => '오류', 'code' => 400]]);
exit;

// ❌ 비표준 응답 형식
echo json_encode(['result' => 'ok']);
exit();

// ❌ 직접 출력
echo "성공";

// ❌ 금지된 $response 키 사용
$response['SESSION'] = [];      // 금지!
$response['menus'] = [];        // 금지!
$response['data'] = [];         // 금지!
$response['events'] = [];       // 금지!
$response['totalCount'] = 0;    // 금지!
$response['approval'] = null;   // 금지!
$response['pagination'] = null; // 금지!
$response['table_array'] = [];  // 금지!
// 허용: result, msg, html, item, items, error만 사용
```

```javascript
// ❌ fetch 직접 사용 금지!
fetch(window.location.href, {
  method: 'POST',
  body: formData
}).then(response => response.json())
  .then(data => { /* ... */ });

// ❌ $.ajax 직접 사용 금지! (탭 로드 제외)
$.ajax({
  url: "<?= SRC ?>/" + pageName,
  type: "POST",
  data: data,
  dataType: "json"
}).done(function(response) {
  // ❌ updateAjaxContent 함수를 사용해야 함!
});

// ❌ var pageName 사용 (AJAX 동적 로드 페이지에서)
var pageName = '...';  // ❌ new Function()으로 실행 시 전역 스코프 접근 불가
// ✅ 올바른 방법: window.pageName 사용

// ❌ JavaScript 변수를 PHP encryptValue()에 직접 전달
for (let [key, value] of formData.entries()) {
  data['<?= encryptValue(key) ?>'] = value; // ❌ PHP Warning 발생!
}
// ✅ 올바른 방법: 필드명을 미리 암호화한 매핑 객체 사용

// ❌ AJAX 필터 응답 HTML이 초기 로드와 다른 구조
// 초기 로드: <td><strong>이름</strong></td>
// AJAX 응답: <td>이름</td>  // ❌ <strong> 누락, 스타일 깨짐
// ✅ 올바른 방법: 완전히 동일한 HTML 구조 사용
```

---

## 🛠️ 개발 체크리스트

### 신규 페이지 개발 시
- [ ] `inc/common.php` 로드 확인
- [ ] 암복호화 처리 적용
- [ ] **표준 응답 포맷 사용 (`$response` + `Finish()`, `echo json_encode()` 금지)**
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

### 배포 전 체크
- [ ] 문법 오류 검증 완료
- [ ] 계산식 정책 기준 검증
- [ ] 권한별 접근 제어 테스트
- [ ] AJAX 라우팅 정상 동작 확인
- [ ] 로그 기록 정상 작동 확인

---

## 🗄️ 데이터베이스 & 스키마 관리

### 스키마 일치 원칙
**⚠️ CRITICAL: schema.sql과 PHP 쿼리문은 반드시 일치해야 합니다**

```php
// ❌ Bad - schema.sql과 불일치
// schema.sql: customer_id VARCHAR(20) NOT NULL (PRIMARY KEY)
$sql = "INSERT INTO customers (name, email) VALUES (...)";
// → customer_id 누락으로 SQL 오류 발생, 세션 로그아웃 유발

// ✅ Good - schema.sql과 일치
// 1. customer_id 생성 로직 추가
$today = date('Ymd');
$prefix = 'C' . $today;
$lastIdSql = "SELECT customer_id FROM customers WHERE customer_id LIKE '{$prefix}%' ORDER BY customer_id DESC LIMIT 1";
$lastIdResult = mysqli_query($con, $lastIdSql);

if ($lastIdResult && mysqli_num_rows($lastIdResult) > 0) {
    $lastRow = mysqli_fetch_assoc($lastIdResult);
    $lastSeq = intval(substr($lastRow['customer_id'], -4));
    $newSeq = str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);
} else {
    $newSeq = '0001';
}
$customerId = $prefix . $newSeq; // CYYYYMMDDNNNN

// 2. INSERT 문에 customer_id 포함
$sql = "INSERT INTO customers (customer_id, name, email, ...)
        VALUES ('{$customerId}', ...)";
```

### 커스텀 ID 생성 패턴

```php
// 고객 ID (CYYYYMMDDNNNN)
$customerId = 'C' . date('Ymd') . str_pad($seq, 4, '0', STR_PAD_LEFT);
// 예시: C202501080001, C202501080002

// 벤더 ID (VYYYYMMDDNNNN)
$vendorId = 'V' . date('Ymd') . str_pad($seq, 4, '0', STR_PAD_LEFT);
// 예시: V202501080001, V202501080002

// 공통 패턴
$today = date('Ymd');
$prefix = '{PREFIX}' . $today; // C/V/S 등
$lastIdSql = "SELECT {id_field} FROM {table} WHERE {id_field} LIKE '{$prefix}%' ORDER BY {id_field} DESC LIMIT 1";
$lastIdResult = mysqli_query($con, $lastIdSql);

if ($lastIdResult && mysqli_num_rows($lastIdResult) > 0) {
    $lastRow = mysqli_fetch_assoc($lastIdResult);
    $lastSeq = intval(substr($lastRow['{id_field}'], -4));
    $newSeq = str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);
} else {
    $newSeq = '0001';
}
$newId = $prefix . $newSeq;
```

### 스키마 변경 워크플로우

**IMPORTANT: schema.sql 변경 시 반드시 다음 순서를 따르세요**

1. **schema.sql 수정**
   - 테이블 구조 변경 (ALTER TABLE)
   - 새 테이블 추가 (CREATE TABLE)
   - 인덱스/제약조건 추가

2. **PHP 코드 업데이트**
   - INSERT/UPDATE 쿼리문을 schema.sql과 일치시키기
   - 새 컬럼 추가 시 필수/선택 여부 확인
   - 커스텀 ID 생성 로직 추가 (PRIMARY KEY가 VARCHAR인 경우)

3. **더미 데이터 업데이트 (필수!)**
   - `utility/generate_dummy_data.php` 수정
   - 변경된 스키마에 맞춰 더미 데이터 생성 로직 수정
   - 새 테이블 추가 시 최소 30개 더미 레코드 생성

4. **더미 데이터 재생성**
   ```bash
   # Windows (XAMPP 환경)
   C:\AutoSet9\server\bin\php.exe utility/generate_dummy_data.php > dummy_data.sql

   # 생성된 SQL 실행
   mysql -u root -p dispenser < dummy_data.sql
   ```

5. **검증**
   - [ ] schema.sql과 PHP INSERT/UPDATE 쿼리 필드 일치 확인
   - [ ] 더미 데이터 정상 삽입 확인
   - [ ] CRUD 기능 정상 작동 확인

### 더미 데이터 생성 규칙

```php
// utility/generate_dummy_data.php 예시

// 1. 기존 데이터 삭제
echo "-- 기존 {테이블명} 데이터 삭제\n";
echo "DELETE FROM {테이블명};\n\n";

// 2. 30개 이상 더미 데이터 생성
$count = 30;
for ($i = 1; $i <= $count; $i++) {
    // 커스텀 ID 생성 (필요한 경우)
    $id = generateCustomId($i);

    // INSERT 쿼리 생성
    echo "INSERT INTO {테이블명} (field1, field2, ...) VALUES ";
    echo "('{$value1}', '{$value2}', ...);\n";
}

// 3. 완료 메시지
echo "\n-- ✓ {테이블명}: {$count}개 생성 완료\n\n";
```

### 스키마 변경 체크리스트

**테이블 추가 시:**
- [ ] schema.sql에 CREATE TABLE 추가
- [ ] generate_dummy_data.php에 30개 더미 데이터 생성 로직 추가
- [ ] CRUD PHP 파일 생성 (doc/{role}/{table}_mgmt.php)
- [ ] 메뉴에 등록 (inc/menus.php)
- [ ] 더미 데이터 재생성 실행

**컬럼 추가/변경 시:**
- [ ] schema.sql에 ALTER TABLE 추가
- [ ] 관련 PHP INSERT/UPDATE 쿼리 수정
- [ ] generate_dummy_data.php 업데이트
- [ ] 더미 데이터 재생성 실행
- [ ] 기존 데이터 마이그레이션 (필요 시)

**PRIMARY KEY 변경 시:**
- [ ] AUTO_INCREMENT → VARCHAR: 커스텀 ID 생성 로직 추가
- [ ] VARCHAR → AUTO_INCREMENT: 기존 ID 매핑 테이블 생성 (필요 시)
- [ ] 외래 키 참조 테이블 모두 업데이트

### 일반적인 오류 패턴

```php
// ❌ 오류 1: 필수 컬럼 누락
// schema.sql: customer_id VARCHAR(20) NOT NULL
INSERT INTO customers (name) VALUES ('홍길동');
// → ERROR: Field 'customer_id' doesn't have a default value
// → 로그아웃 유발!

// ✅ 수정: 커스텀 ID 생성 후 포함
$customerId = 'C' . date('Ymd') . '0001';
INSERT INTO customers (customer_id, name) VALUES ('{$customerId}', '홍길동');

// ❌ 오류 2: 더미 데이터 스키마 불일치
// schema.sql: ALTER TABLE vendors ADD COLUMN tax_id_number VARCHAR(50)
// generate_dummy_data.php: (업데이트 안 함)
// → 더미 데이터에 tax_id_number 누락

// ✅ 수정: generate_dummy_data.php 업데이트
echo "INSERT INTO vendors (..., tax_id_number) VALUES (..., '123-45-67890');\n";

// ❌ 오류 3: $response['result'] = false 누락
if ($result) {
    $response['result'] = true;
} else {
    // $response['result'] = false; ← 누락!
    $response['error'] = ['msg' => '오류', 'code' => 500];
}
// → 프론트엔드에서 result 체크 시 undefined 오류

// ✅ 수정: 모든 에러 응답에 result = false 명시
if ($result) {
    $response['result'] = true;
} else {
    $response['result'] = false; // ← 필수!
    $response['error'] = ['msg' => '오류', 'code' => 500];
}
```

### 실전 예시: customers 테이블 추가

**1. schema.sql**
```sql
CREATE TABLE `customers` (
  `customer_id` varchar(20) NOT NULL COMMENT 'CYYYYMMDDNNNN 형식',
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  -- ... 기타 컬럼
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**2. customer_list.php (CRUD)**
```php
case 'add_customer':
    // 커스텀 ID 생성
    $today = date('Ymd');
    $prefix = 'C' . $today;
    $lastIdSql = "SELECT customer_id FROM customers WHERE customer_id LIKE '{$prefix}%' ORDER BY customer_id DESC LIMIT 1";
    $lastIdResult = mysqli_query($con, $lastIdSql);

    if ($lastIdResult && mysqli_num_rows($lastIdResult) > 0) {
        $lastRow = mysqli_fetch_assoc($lastIdResult);
        $lastSeq = intval(substr($lastRow['customer_id'], -4));
        $newSeq = str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);
    } else {
        $newSeq = '0001';
    }
    $customerId = $prefix . $newSeq;

    // INSERT 실행
    $sql = "INSERT INTO customers (customer_id, name, email, ...) VALUES ('{$customerId}', ...)";
    $result = query($sql);

    if ($result) {
        $response['result'] = true;
        $response['msg'] = '고객이 등록되었습니다.';
        $response['item'] = ['customer_id' => $customerId];
    } else {
        $response['result'] = false; // 필수!
        $response['error'] = ['msg' => '등록 실패', 'code' => 500];
    }
    Finish();
```

**3. generate_dummy_data.php**
```php
// 고객 더미 데이터 생성
echo "-- 3. Customers 더미 데이터\n";
echo "DELETE FROM customers;\n\n";

$customerCount = 30;
for ($i = 1; $i <= $customerCount; $i++) {
    $customerId = 'C20250108' . str_pad($i, 4, '0', STR_PAD_LEFT);
    $name = "고객{$i}";
    $email = "customer{$i}@example.com";
    // ... 기타 필드

    echo "INSERT INTO customers (customer_id, name, email, ...) ";
    echo "VALUES ('{$customerId}', '{$name}', '{$email}', ...);\n";
}

echo "\n-- ✓ Customers: {$customerCount}개 생성 완료\n\n";
```

**4. 더미 데이터 실행**
```bash
C:\AutoSet9\server\bin\php.exe utility/generate_dummy_data.php > dummy_data.sql
mysql -u root -p dispenser < dummy_data.sql
```

---

## 🎨 표준 페이지 레이아웃 구조

### 표준 페이지 구조 (Single Page)

**⚠️ IMPORTANT: 이 레이아웃 구조는 HQ, Vendor, Customer, Lucid 등 모든 포털의 모든 페이지에 동일하게 적용됩니다.**

모든 페이지는 다음 구조를 따릅니다:

```html
<div class="wrap">
  <section id="sec-{페이지명}" class="card section-card-first">
    <div class="card-hd card-hd-wrap">
      <div class="card-hd-content">
        <div class="card-hd-title-area">
          <div class="card-ttl">{페이지 제목}</div>
          <div class="card-sub">{부제목/설명}</div>
        </div>
        <div class="row filter-row">
          <!-- 필터 & 검색 영역 -->
          <select id="filter{Name}" class="form-control input-w-150">
            <option value="">전체</option>
          </select>
          <input type="text" id="search{Name}" class="form-control input-w-200" placeholder="검색">
          <button id="btnFilter" class="btn primary">조회</button>
          <button id="btnAdd{Name}" class="btn primary">{항목} 추가</button>
          <button id="btnExportCsv" class="btn">CSV 내보내기</button>
        </div>
      </div>
    </div>
    <div class="card-bd">
      <div class="table-wrap">
        <table class="table" id="tbl{Name}">
          <thead>
            <tr>
              <th><input type="checkbox" id="chkAll"></th>
              <th>컬럼1</th>
              <th>컬럼2</th>
              <th>관리</th>
            </tr>
          </thead>
          <tbody>
            <!-- 데이터 행 -->
          </tbody>
        </table>
      </div>
      <div class="row" style="margin-top:12px">
        <button id="btnBulk{Action}" class="btn">일괄 {작업}</button>
      </div>
    </div>
  </section>
</div>

<!-- 모달 -->
<div id="modal{Name}Form" class="modal" style="display:none">
  <div class="modal-content">
    <div class="modal-header">
      <h3 id="formTitle">{제목}</h3>
      <button class="modal-close">&times;</button>
    </div>
    <div class="modal-body">
      <form id="{name}Form">
        <!-- 폼 필드 -->
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn modal-close">취소</button>
      <button id="btnSave{Name}" class="btn primary">저장</button>
    </div>
  </div>
</div>

<script>
// 페이지 암호화 토큰 설정
var pageName = '<?= encryptValue(date('Y-m-d') . '/{page_name}') ?>';

(function() {
  // 이벤트 핸들러
})();
</script>
```

### 탭 페이지 구조 (Tab Layout)

탭으로 구성된 페이지 (_tab.php):

```html
<div class="wrap">
  <section id="sec-{페이지명}" class="card">
    <div class="card-hd">
      <div class="card-hd-content">
        <div class="card-hd-title-area">
          <div class="card-ttl">{페이지 제목}</div>
          <div class="card-sub">{부제목/설명}</div>
        </div>

        <!-- 탭 버튼 영역 -->
        <div class="tab-nav-inline">
          <button class="tab-btn-inline active" data-token="<?= $tab1Token ?>"
                  onclick="load{Name}Tab(this, '<?= $tab1Token ?>')">
            탭1
          </button>
          <button class="tab-btn-inline" data-token="<?= $tab2Token ?>"
                  onclick="load{Name}Tab(this, '<?= $tab2Token ?>')">
            탭2
          </button>
        </div>
      </div>
    </div>

    <div class="card-bd">
      <div id="{name}-tab-content">
        <div class="table-text-center" style="color:#999;">
          <p>로딩 중...</p>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
// 탭 로드 함수
window.load{Name}Tab = function(btnElement, encryptedToken) {
  // 모든 탭 버튼 비활성화
  document.querySelectorAll('.tab-btn-inline').forEach(btn => {
    btn.classList.remove('active');
  });

  // 클릭된 탭 활성화
  if (btnElement) {
    btnElement.classList.add('active');
  }

  // 로딩 표시
  const contentArea = document.getElementById('{name}-tab-content');
  contentArea.innerHTML = '<div class="table-text-center"><p>로딩 중...</p></div>';

  // AJAX로 페이지 로드
  const data = {};
  data['<?= encryptValue('menuName') ?>'] = encryptedToken;

  $.ajax({
    type: "POST",
    url: "#",
    dataType: "html",
    data: data,
    cache: false
  }).done(function(response){
    // 스크립트를 수동으로 파싱하여 실행
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = response;

    const scripts = tempDiv.querySelectorAll('script');
    const scriptsArray = Array.from(scripts);
    scriptsArray.forEach(script => script.remove());
    contentArea.innerHTML = tempDiv.innerHTML;

    // 스크립트 실행
    scriptsArray.forEach(oldScript => {
      if (oldScript.src) {
        const newScript = document.createElement('script');
        newScript.src = oldScript.src;
        newScript.async = false;
        document.body.appendChild(newScript);
      } else {
        try {
          const scriptText = oldScript.textContent || oldScript.innerHTML;
          (new Function(scriptText))();
        } catch (e) {
          console.error('스크립트 실행 오류:', e, oldScript.textContent);
        }
      }
    });
  }).fail(function(xhr, status, error){
    console.warn('AJAX 오류:', error);
    contentArea.innerHTML = '<div class="table-text-center" style="color:#d32f2f;"><p>페이지를 불러올 수 없습니다.</p></div>';
  });
}

// 페이지 로드 시 첫 번째 탭 자동 로드
setTimeout(function() {
  const firstTab = document.querySelector('.tab-btn-inline.active');
  if (firstTab) {
    const token = firstTab.getAttribute('data-token');
    load{Name}Tab(firstTab, token);
  }
}, 0);
</script>
```

### CSS 클래스 규칙

#### 레이아웃 클래스
```css
.wrap              /* 페이지 전체 래퍼 (padding: 14px, grid gap: 14px) */
.card              /* 카드 컨테이너 (white bg, border, rounded-16px) */
.card-hd           /* 카드 헤더 (기본) */
.card-hd-wrap      /* 카드 헤더 (확장, padding: 20px 24px) */
.card-hd-content   /* 헤더 콘텐츠 영역 (flex-col) */
.card-hd-title-area /* 제목 영역 */
.card-ttl          /* 카드 제목 (green, 16px, bold) */
.card-sub          /* 카드 부제목 (gray, 12px) */
.card-bd           /* 카드 본문 (padding: 16px) */
.card-bd-padding   /* 카드 본문 확장 (padding: 24px) */
```

#### 그리드 클래스
```css
.grid-2            /* 2단 그리드 (1fr 1fr @980px+) */
.grid-3            /* 3단 그리드 (1fr -> 2fr@768px -> 3fr@1200px) */
.row               /* Flexbox 행 (gap: 8px) */
.filter-row        /* 필터 행 (gap: 10px, wrap) */
```

#### 폼 클래스
```css
.form-control      /* Input/Select 기본 */
.input-w-150       /* 너비 150px */
.input-w-200       /* 너비 200px */
.form-group        /* 폼 그룹 (margin-bottom) */
```

#### 버튼 클래스
```css
.btn               /* 기본 버튼 */
.btn.primary       /* 주요 버튼 (green) */
.btn-sm            /* 작은 버튼 */
.btn-edit          /* 수정 버튼 */
.btn-delete        /* 삭제 버튼 */
```

#### 테이블 클래스
```css
.table-wrap        /* 테이블 래퍼 (overflow-x: auto) */
.table             /* 테이블 기본 스타일 */
.table-text-center /* 테이블 가운데 정렬 */
```

#### 배지 클래스
```css
.badge             /* 배지 기본 */
.badge-success     /* 성공 (green) */
.badge-warning     /* 경고 (yellow) */
.badge-danger      /* 위험 (red) */
.badge-info        /* 정보 (blue) */
.badge-secondary   /* 보조 (gray) */
```

#### 모달 클래스
```css
.modal             /* 모달 오버레이 */
.modal-content     /* 모달 콘텐츠 박스 */
.modal-header      /* 모달 헤더 */
.modal-body        /* 모달 본문 */
.modal-footer      /* 모달 푸터 */
.modal-close       /* 모달 닫기 버튼 */
```

#### 탭 클래스
```css
.tab-nav-inline    /* 인라인 탭 네비게이션 */
.tab-btn-inline    /* 인라인 탭 버튼 */
.tab-btn-inline.active /* 활성화된 탭 */
```

### 공통 JavaScript 패턴

#### 필터 & 검색
```javascript
// 필터 조회 (페이지 리로드 방식)
$(document).on('click', '#btnFilter', function() {
  const filter1 = document.getElementById('filter{Name}').value;
  const search = document.getElementById('search{Name}').value;
  const params = new URLSearchParams();
  if (filter1) params.append('filter1', filter1);
  if (search) params.append('search', search);
  window.location.href = '?' + params.toString();
});

// 엔터키로 검색
$(document).on('keypress', '#search{Name}', function(e) {
  if (e.key === 'Enter') {
    $('#btnFilter').click();
  }
});

// 필터 변경 시 자동 조회
$(document).on('change', '#filter{Name}', function() {
  $('#btnFilter').click();
});
```

#### CSV 내보내기
```javascript
$(document).on('click', '#btnExportCsv', function() {
  const table = document.getElementById('tbl{Name}');
  const rows = Array.from(table.querySelectorAll('thead tr, tbody tr'));

  const csv = rows.map(row => {
    const cells = Array.from(row.querySelectorAll('th, td'));
    return cells.map(cell => {
      if (cell.querySelector('input[type="checkbox"]')) return '';
      if (cell.querySelector('button')) return '';
      const badge = cell.querySelector('.badge');
      if (badge) return badge.textContent.trim();
      return '"' + cell.textContent.trim().replace(/"/g, '""') + '"';
    }).filter(Boolean).join(',');
  }).join('\n');

  const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = '{페이지명}_' + new Date().toISOString().slice(0,10) + '.csv';
  link.click();
});
```

#### 전체 선택
```javascript
$(document).on('change', '#chkAll', function() {
  const checkboxes = document.querySelectorAll('#tbl{Name} tbody input[type="checkbox"]');
  checkboxes.forEach(cb => cb.checked = this.checked);
});
```

#### 모달 열기/닫기
```javascript
// 모달 열기 - 추가
$(document).on('click', '#btnAdd{Name}', function() {
  document.getElementById('formTitle').textContent = '{항목} 추가';
  document.getElementById('{name}Form').reset();
  document.getElementById('{id}Field').value = '';
  document.getElementById('modal{Name}Form').style.display = 'flex';
});

// 모달 열기 - 수정
$(document).on('click', '.btn-edit', function() {
  document.getElementById('formTitle').textContent = '{항목} 수정';
  document.getElementById('{id}Field').value = this.getAttribute('data-{id}');
  document.getElementById('{name}Field').value = this.getAttribute('data-{name}');
  // ... 기타 필드
  document.getElementById('modal{Name}Form').style.display = 'flex';
});

// 모달 닫기
$(document).on('click', '.modal-close', function() {
  $(this).closest('.modal').css('display', 'none');
});

// ESC 키로 모달 닫기
$(document).on('keydown', function(e) {
  if (e.key === 'Escape') {
    $('.modal').css('display', 'none');
  }
});
```

#### AJAX 저장
```javascript
$(document).on('click', '#btnSave{Name}', function() {
  const form = document.getElementById('{name}Form');
  if (!form.checkValidity()) {
    alert('필수 항목을 입력해주세요.');
    return;
  }

  const idValue = document.getElementById('{id}Field').value;
  const action = idValue ? 'update_{name}' : 'add_{name}';

  const data = {};
  data['<?= encryptValue('action') ?>'] = action;
  if (idValue) data['<?= encryptValue('{id}') ?>'] = idValue;
  data['<?= encryptValue('{field1}') ?>'] = document.getElementById('{field1}').value;
  // ... 기타 필드

  updateAjaxContent(data, function(response) {
    if (response.result) {
      alert(`{항목} ${idValue ? '수정' : '등록'}이 완료되었습니다.`);
      document.getElementById('modal{Name}Form').style.display = 'none';
      location.reload();
    } else {
      alert(response.error?.msg || '오류가 발생했습니다.');
    }
  });
});
```

### PHP 백엔드 구조

```php
<?php
// POST 핸들러 처리
if (!empty($_POST)) {
    header('Content-Type: application/json; charset=utf-8');

    $action = $_POST['action'] ?? '';
    $response = ['result' => false, 'error' => ['msg' => '', 'code' => 0]];

    // Escape 함수
    function escapeInput($con, $value) {
        return mysqli_real_escape_string($con, trim($value));
    }

    // 커스텀 ID 생성 함수 (필요 시)
    function generate{Name}Id($con) {
        $today = date('Ymd');
        $prefix = '{PREFIX}' . $today;
        $sql = "SELECT {id_field} FROM {table} WHERE {id_field} LIKE '{$prefix}%' ORDER BY {id_field} DESC LIMIT 1";
        $result = mysqli_query($con, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $lastId = $row['{id_field}'];
            $sequence = intval(substr($lastId, -4)) + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    // 추가
    if ($action === 'add_{name}') {
        // 필드 받기
        $field1 = escapeInput($con, $_POST['field1'] ?? '');

        // 필수 필드 검증
        if (empty($field1)) {
            $response['error']['msg'] = '필수 항목을 입력해주세요.';
            $response['error']['code'] = 400;
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }

        // ID 생성 (커스텀 ID인 경우)
        $id = generate{Name}Id($con);

        // INSERT
        $sql = "INSERT INTO {table} ({id}, {field1}, created_at, updated_at)
                VALUES ('{$id}', '{$field1}', NOW(), NOW())";

        if (mysqli_query($con, $sql)) {
            $response['result'] = true;
            $response['item'] = ['{id}' => $id, '{field1}' => $field1];
        } else {
            $response['error']['msg'] = '등록 중 오류가 발생했습니다: ' . mysqli_error($con);
            $response['error']['code'] = 500;
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 수정
    if ($action === 'update_{name}') {
        $id = escapeInput($con, $_POST['{id}'] ?? '');
        $field1 = escapeInput($con, $_POST['field1'] ?? '');

        if (empty($id) || empty($field1)) {
            $response['error']['msg'] = '필수 항목을 입력해주세요.';
            $response['error']['code'] = 400;
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }

        $sql = "UPDATE {table} SET {field1} = '{$field1}', updated_at = NOW() WHERE {id} = '{$id}'";

        if (mysqli_query($con, $sql)) {
            $response['result'] = true;
            $response['item'] = ['{id}' => $id];
        } else {
            $response['error']['msg'] = '수정 중 오류가 발생했습니다: ' . mysqli_error($con);
            $response['error']['code'] = 500;
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 일괄 처리 (선택)
    if ($action === 'bulk_{operation}') {
        $ids = $_POST['{id}s'] ?? [];
        // ... 처리 로직
    }

    // 알 수 없는 액션
    $response['error']['msg'] = '지원하지 않는 요청입니다.';
    $response['error']['code'] = 400;
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// 필터 파라미터
$filterParam1 = isset($_GET['filter1']) ? $_GET['filter1'] : '';
$searchKeyword = isset($_GET['search']) ? $_GET['search'] : '';

// 데이터 조회 (Prepared Statement 사용)
$sql = "SELECT * FROM {table} WHERE is_active = 1";

if ($filterParam1) {
    $sql .= " AND {field} = ?";
}

if ($searchKeyword) {
    $sql .= " AND {field} LIKE ?";
}

$sql .= " ORDER BY created_at DESC LIMIT 100";

$stmt = mysqli_prepare($con, $sql);

if ($filterParam1 && $searchKeyword) {
    $searchParam = "%{$searchKeyword}%";
    mysqli_stmt_bind_param($stmt, 'ss', $filterParam1, $searchParam);
} elseif ($filterParam1) {
    mysqli_stmt_bind_param($stmt, 's', $filterParam1);
} elseif ($searchKeyword) {
    $searchParam = "%{$searchKeyword}%";
    mysqli_stmt_bind_param($stmt, 's', $searchParam);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$items = [];
while ($row = mysqli_fetch_assoc($result)) {
    $items[] = $row;
}

mysqli_stmt_close($stmt);
?>
```

### 페이지 작성 체크리스트

**Single Page:**
- [ ] `<div class="wrap">` 래퍼로 시작
- [ ] `section id="sec-{페이지명}" class="card section-card-first"` 사용
- [ ] `card-hd-wrap` + `card-hd-content` 헤더 구조
- [ ] `card-ttl` + `card-sub` 제목 영역
- [ ] `filter-row`에 필터/검색/버튼 배치
- [ ] `table-wrap` + `table` 구조 사용
- [ ] 전체 선택 체크박스 ID는 `chkAll`
- [ ] 모달 구조: `modal` > `modal-content` > `modal-header/body/footer`
- [ ] `var pageName = '<?= encryptValue(...) ?>'` 설정
- [ ] IIFE `(function() { ... })()` 로 스크립트 감싸기

**Tab Page:**
- [ ] 탭 버튼에 `data-token` 속성 추가
- [ ] `load{Name}Tab(btnElement, token)` 함수 구현
- [ ] 스크립트 수동 파싱 및 실행 로직 포함
- [ ] `setTimeout(..., 0)` 으로 첫 탭 자동 로드
- [ ] 각 탭 콘텐츠 파일은 `window.pageName` 사용

**PHP Backend:**
- [ ] POST 핸들러에서 `header('Content-Type: application/json')` 설정
- [ ] `$response` 배열 사용 (`result`, `error`, `item` 구조)
- [ ] `escapeInput()` 함수로 모든 입력값 이스케이프
- [ ] Prepared Statement 사용 (SELECT 쿼리)
- [ ] 커스텀 ID 생성 함수 구현 (필요 시)
- [ ] 각 case 끝에 `echo json_encode()` + `exit` 필수
- [ ] 오류 시 `$response['result'] = false` 명시

---

## 🔗 관련 문서

- **상세 아키텍처**: [architecture.md](./architecture.md)
- **DB 규약**: [database.md](./database.md)
- **공통 규칙**: [common-rules.md](./common-rules.md)
- **포털별 규칙**: [portals/](./portals/)
- **정책 상세**: [policies.md](./policies.md)

---

**마지막 업데이트**: 2025-11-10 (v1.7)

### v1.7 변경사항 (2025-11-10)
- **🎨 표준 페이지 레이아웃 구조 섹션 업데이트**
- 섹션명 변경: "HQ 페이지 레이아웃 구조" → "표준 페이지 레이아웃 구조"
- ⚠️ CRITICAL: 모든 포털(HQ, Vendor, Customer, Lucid)의 모든 페이지에 동일하게 적용
- CSV 내보내기 파일명에서 'HQ_' 접두사 제거 (범용성 확보)
- 일관된 UI/UX를 위한 전사 표준 레이아웃 정립

### v1.6 변경사항 (2025-11-10)
- **🎨 페이지 레이아웃 구조 섹션 추가**
- 표준 Single Page 구조 템플릿 제공
- 탭 페이지 (Tab Layout) 구조 템플릿 제공
- CSS 클래스 규칙 체계화 (레이아웃, 그리드, 폼, 버튼, 테이블, 배지, 모달, 탭)
- 공통 JavaScript 패턴 제공 (필터/검색, CSV 내보내기, 전체 선택, 모달, AJAX 저장)
- PHP 백엔드 구조 템플릿 제공 (POST 핸들러, ID 생성, CRUD, Prepared Statement)
- 페이지 작성 체크리스트 추가 (Single Page, Tab Page, PHP Backend)

### v1.5 변경사항 (2025-11-08)
- **📝 폼 데이터 전송 패턴 섹션 추가**
- FormData + fieldMap 패턴 상세 설명 (권장 방식)
- HTML 폼 구조 요구사항 명시 (name 속성, onsubmit, type="button")
- 폼 데이터 전송 문제 해결 체크리스트 추가
- 일반적인 오류 패턴 3가지 추가 (form 자동 제출, name 속성 누락, fieldMap 불일치)
- 벤더/영업사원/고객 탭에서 검증된 안정적인 방식 문서화
- ⚠️ CRITICAL: form 자동 제출 방지 필수 (`onsubmit="return false;"`, `type="button"`)

### v1.4 변경사항 (2025-11-08)
- .htaccess 라우팅 규칙 섹션 추가
- AJAX 요청이 자동으로 _ajax_.php로 라우팅되는 메커니즘 명시
- updateAjaxContent() URL 구성 규칙 명확화
- AJAX 로드된 HTML에서 스크립트 실행 패턴 추가
- `loadPage()` 함수 스크립트 수동 파싱 및 실행 방식 문서화
- `setTimeout(..., 0)` 패턴으로 DOM 렌더링 완료 대기
- ⚠️ CRITICAL: `/_ajax_.php/`를 명시적으로 포함하지 말 것 (중복 라우팅 방지)

### v1.3 변경사항 (2025-11-08)
- 데이터베이스 & 스키마 관리 섹션 추가
- schema.sql과 PHP 쿼리 일치 원칙 명시
- 커스텀 ID 생성 패턴 (CYYYYMMDDNNNN) 가이드 추가
- 스키마 변경 워크플로우 및 체크리스트 추가
- 더미 데이터 생성 규칙 (30개 이상) 명시
- 일반적인 오류 패턴 및 해결 방법 추가
- $response['result'] = false 누락 주의사항 추가

### v1.2 변경사항 (2025-11-08)
- AJAX 동적 로드 페이지에서 `window.pageName` 사용 필수화 (var 대신)
- onclick 인라인 이벤트 + `window.functionName` 패턴 추가
- FormData 필드명 매핑 객체 패턴 추가
- AJAX 필터 응답 HTML 구조 일치 가이드라인 추가
- PHP Warning 방지를 위한 암호화 필드 매핑 패턴 추가
