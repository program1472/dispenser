# 문제 해결 (Troubleshooting)

> FAQ, 일반적인 오류 패턴, 디버깅 팁

---

## 목차

1. [FAQ](#faq)
2. [일반적인 오류 패턴](#일반적인-오류-패턴)
3. [데이터베이스 오류](#데이터베이스-오류)
4. [AJAX 및 라우팅 오류](#ajax-및-라우팅-오류)
5. [폼 및 데이터 전송 오류](#폼-및-데이터-전송-오류)
6. [디버깅 팁](#디버깅-팁)

---

## FAQ

### Q1. 어떤 파일을 먼저 읽어야 하나요?

**A**: [quick-reference.md](./quick-reference.md)를 먼저 읽으세요. 핵심 규칙이 모두 요약되어 있습니다.

**추가 추천**:
1. [01-quick-start.md](./01-quick-start.md) - 5분 안에 시작하기
2. [architecture.md](./architecture.md) - 전체 구조 이해
3. 해당 포털 문서 (예: [portals/vendor.md](./portals/vendor.md))

---

### Q2. 특정 포털 개발 시 어떤 문서를 봐야 하나요?

**A**: `portals/` 폴더의 해당 포털 문서를 보세요.

- **벤더**: [portals/vendor.md](./portals/vendor.md)
- **고객**: [portals/customer.md](./portals/customer.md)
- **루시드**: [portals/lucid.md](./portals/lucid.md)
- **영업사원**: [portals/sales.md](./portals/sales.md)

---

### Q3. 계산식이 맞는지 확인하려면?

**A**: [policies.md](./policies.md)를 참조하세요. 모든 금액/계산 정책이 정리되어 있습니다.

**주요 정책**:
- 구독료: **29,700원/월**
- 벤더 커미션: **매출 × 40%**
- 벤더 인센티브: **매출 × 5%**
- 루시드 배분: **콘텐츠 단가 × 50%** (고객 수정 요청 건만)
- 영업사원 판매: **90,000원/대** → 15,000원 × 6회 분할
- 영업사원 리뉴얼: **30,000원** (기본) / **40,000원** (연속)

---

### Q4. DB 작업 중 FK 오류가 발생해요

**A**: [database.md](./database.md)의 "Foreign Key 관리" 섹션을 확인하세요.

**일반적인 해결 방법**:

```sql
-- 1. FK 체크 비활성화
SET FOREIGN_KEY_CHECKS = 0;

-- 2. 자식 → 부모 순서로 드롭
DROP TABLE IF EXISTS user_extra;
DROP TABLE IF EXISTS audit_log;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS sites;
DROP TABLE IF EXISTS customers;
DROP TABLE IF EXISTS vendors;
DROP TABLE IF EXISTS roles;

-- 3. FK 체크 재활성화
SET FOREIGN_KEY_CHECKS = 1;

-- 4. 부모 → 자식 순서로 재생성
-- schema.sql 실행
```

**체크리스트**:
- [ ] PK와 FK 컬럼의 타입/크기가 정확히 일치하는가?
- [ ] BIGINT 사용 시 양쪽 모두 UNSIGNED인가?
- [ ] FK 컬럼에 인덱스가 있는가?

---

### Q5. 상태 배지 색상을 모르겠어요

**A**: [06-ui-components.md](./06-ui-components.md)의 "상태 배지" 섹션을 확인하세요.

**빠른 참조**:
- **ACTIVE**: 초록 (`badge-active`)
- **WARNING**: 노랑 (`badge-warning`)
- **GRACE**: 주황 (`badge-grace`)
- **TERMINATED**: 회색 (`badge-terminated`)
- **PLANNED**: 회색 (`badge-planned`)
- **DUE**: 파랑 (`badge-due`)
- **PAID**: 초록 (`badge-paid`)
- **OPEN**: 파랑 (`badge-open`)
- **IN_PROGRESS**: 주황 (`badge-progress`)
- **DONE**: 초록 (`badge-done`)

---

### Q6. AJAX 요청은 어떻게 처리하나요?

**A**: **모든 AJAX 요청은 용도에 따라 다른 URL을 사용합니다.**

**1) 페이지 초기 로드 (index.php 경유)**:
```javascript
// loadPage() 함수
url: "<?= SRC ?>/";
// 전체 페이지 HTML을 반환받을 때
```

**2) 서버와의 모든 데이터 통신 (_ajax_.php 경유)**:
```javascript
// updateAjaxContent() 함수
url: "<?= SRC ?>/" + window.pageName;
// CRUD 작업, 필터링, 검색 등 모든 데이터 통신
```

**중요**:
- 절대 `/_ajax_.php/`를 명시적으로 포함하지 말 것 (중복 라우팅 방지)
- 스크립트 상단에 `window.pageName` 선언 필수

자세한 내용은 [03-coding-standards.md](./03-coding-standards.md) 섹션 "AJAX 라우팅 규칙" 참조

---

### Q7. 버튼이 동작하지 않아요

**A**: 다음을 확인하세요:

**1. 암호화 키 사용 오류**: 액션 값은 문자열 그대로 전달

```javascript
// 잘못됨
data['<?= encryptValue('action') ?>'] = '<?= encryptValue('get_item') ?>';

// 올바름
data['<?= encryptValue('action') ?>'] = 'get_item';
```

**2. AJAX URL**: `url: "<?= SRC ?>/" + window.pageName` 사용

**3. window.pageName 설정**: 스크립트 상단에 선언 필수

```javascript
window.pageName = '<?= encryptValue(date('Y-m-d') . '/my_page') ?>';
```

**4. 모달 CSS**: 모달이 표시되지 않으면 CSS가 누락된 것

**5. exit 누락**: AJAX 응답 후 반드시 `Finish()` 호출

```php
$response['result'] = true;
$response['msg'] = '성공';
Finish();  // 필수!
```

---

## 일반적인 오류 패턴

### 패턴 1: 로그아웃이 자동으로 발생

**증상**: 페이지 이동 시 또는 AJAX 요청 후 갑자기 로그아웃됨

**원인**:
1. 날짜 토큰 불일치 (어제 날짜 토큰 사용)
2. 암복호화 실패
3. DB 오류로 인한 예외 발생

**해결 방법**:

```php
// 올바른 토큰 생성 (항상 오늘 날짜 사용)
$token = encryptValue(date('Y-m-d') . '/dashboard');

// 날짜 검증
$decrypted = decryptValue($token);
list($date, $pageName) = explode('/', $decrypted);

if ($date !== date('Y-m-d')) {
    // 로그아웃 처리
    require 'logout.php';
    exit();
}
```

**디버깅**:
1. `_ajax_.php`에서 `$decrypted` 값 확인
2. 날짜가 오늘과 일치하는지 확인
3. DB 오류 로그 확인

---

### 패턴 2: 데이터가 하나만 전송됨 (폼 제출 시)

**증상**: 폼 저장 시 POST 데이터가 하나만 전송되고 페이지가 리로드됨

**원인**: form 자동 제출로 인한 페이지 리로드

**해결 방법**:

```html
<!-- 잘못된 예시 -->
<form id="frmCustomer">
  <button class="btn primary" onclick="saveCustomer()">저장</button>
</form>
<!-- type이 없으면 기본값 submit으로 form 제출됨 -->

<!-- 올바른 예시 -->
<form id="frmCustomer" onsubmit="return false;">
  <button type="button" class="btn primary" onclick="saveCustomer()">저장</button>
</form>
```

**체크리스트**:
- [ ] form 태그에 `onsubmit="return false;"` 추가
- [ ] 버튼 type을 `type="button"`으로 설정
- [ ] 모든 input/select/textarea에 `name` 속성 추가
- [ ] `name` 속성이 fieldMap의 key와 일치하는지 확인

---

### 패턴 3: AJAX 필터 결과가 초기 로드와 다르게 표시됨

**증상**: 필터 조회 후 테이블 스타일이 깨짐 (굵은 글씨, 배지 등 사라짐)

**원인**: AJAX 응답 HTML이 초기 로드 HTML과 구조가 다름

**해결 방법**:

```php
// 초기 로드 HTML
<td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
<td><?= number_format($row['count']) ?></td>
<td><span class="badge badge-active">ACTIVE</span></td>

// AJAX 응답 HTML (동일하게 작성)
$html .= '<td><strong>' . htmlspecialchars($row['name']) . '</strong></td>';
$html .= '<td>' . number_format($row['count']) . '</td>';
$html .= '<td><span class="badge badge-active">ACTIVE</span></td>';
```

**체크리스트**:
- [ ] `<strong>`, 배지, `number_format()` 등 모든 스타일 요소 포함
- [ ] colspan 숫자 정확히 일치
- [ ] 초기 로드 HTML과 완전히 동일한 구조 사용

---

### 패턴 4: 탭 페이지에서 버튼이 동작하지 않음

**증상**: AJAX로 로드된 탭 콘텐츠에서 버튼 클릭 시 아무 반응 없음

**원인**:
1. `var pageName` 사용 (스코프 문제)
2. `addEventListener` 사용 (동적 로드된 요소에 미적용)

**해결 방법**:

```javascript
// 잘못된 방법
var pageName = '...';  // 전역 스코프 접근 불가

document.getElementById('btnFilter').addEventListener('click', function() {
  // AJAX 로드 후 이 코드가 실행되지 않음
});

// 올바른 방법
window.pageName = '<?= encryptValue(date('Y-m-d') . '/my_page') ?>';

// onclick 인라인 이벤트 + window 함수
<button id="btnFilter" onclick="filterItems()">조회</button>

window.filterItems = function() {
  // 전역 함수로 선언
  const data = {};
  data['<?= encryptValue('action') ?>'] = 'filter_items';
  updateAjaxContent(data, callback);
};
```

**체크리스트**:
- [ ] `window.pageName` 사용 (var 대신)
- [ ] onclick 인라인 이벤트 사용
- [ ] 모든 함수를 `window.functionName = function() {...}` 형식으로 선언

---

### 패턴 5: FormData에서 일부 필드가 누락됨

**증상**: FormData로 수집 시 일부 필드가 전송되지 않음

**원인**:
1. `name` 속성 누락
2. fieldMap의 key와 `name` 속성 불일치

**해결 방법**:

```html
<!-- 잘못된 예시 -->
<input type="text" id="email" class="form-control">
<!-- name 속성 없음 -->

<!-- 올바른 예시 -->
<input type="text" id="email" name="email" class="form-control">
<!-- name 속성 추가 -->
```

```javascript
// fieldMap과 name 속성 일치 확인
const fieldMap = {
  'vendor_id': '<?= encryptValue('vendor_id') ?>'
};

<select id="vendorId" name="vendor_id">  <!-- name="vendor_id" 일치 -->
```

---

## 데이터베이스 오류

### 오류 1: Field doesn't have a default value

**증상**: `Field 'customer_id' doesn't have a default value`

**원인**: 필수 컬럼 누락

**해결 방법**:

```php
// 잘못된 예시
INSERT INTO customers (name) VALUES ('홍길동');
// → customer_id 누락

// 올바른 예시
$customerId = 'C' . date('Ymd') . '0001';
INSERT INTO customers (customer_id, name) VALUES ('{$customerId}', '홍길동');
```

**체크리스트**:
- [ ] schema.sql에서 필수 컬럼 확인
- [ ] 커스텀 ID 생성 로직 추가
- [ ] INSERT 문에 모든 필수 컬럼 포함

---

### 오류 2: Duplicate entry

**증상**: `Duplicate entry 'hong@example.com' for key 'email'`

**원인**: UNIQUE 컬럼에 중복 값 삽입

**해결 방법**:

```php
// 삽입 전 중복 체크
$email = mysqli_real_escape_string($con, $_POST['email']);
$sql = "SELECT COUNT(*) as cnt FROM users WHERE email = '{$email}'";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);

if ($row['cnt'] > 0) {
    $response['result'] = false;
    $response['error'] = ['msg' => '이미 사용 중인 이메일입니다.', 'code' => 400];
    Finish();
}
```

**사용자 친화적 에러 메시지 사용**:

```php
} catch (Exception $e) {
    mysqli_rollback($con);
    $response['result'] = false;

    // 공통 함수로 에러 메시지 변환
    $errorMsg = getFriendlyErrorMessage($e->getMessage());
    $response['error'] = ['msg' => $errorMsg, 'code' => 500];
}
```

---

### 오류 3: Cannot delete or update a parent row

**증상**: `Cannot delete or update a parent row: a foreign key constraint fails`

**원인**: 다른 테이블에서 참조 중인 레코드 삭제 시도

**해결 방법**:

```php
// 삭제 전 참조 확인
$vendorId = mysqli_real_escape_string($con, $_POST['vendor_id']);

$sql = "SELECT COUNT(*) as cnt FROM users WHERE vendor_id = '{$vendorId}'";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);

if ($row['cnt'] > 0) {
    $response['result'] = false;
    $response['error'] = ['msg' => '다른 데이터에서 사용 중이므로 삭제할 수 없습니다.', 'code' => 400];
    Finish();
}
```

---

### 오류 4: schema.sql과 PHP 쿼리 불일치

**증상**:
- SQL 오류 발생
- 세션 로그아웃
- 데이터 저장 실패

**원인**: schema.sql의 컬럼 구조와 PHP INSERT/UPDATE 쿼리가 일치하지 않음

**해결 방법**:

```sql
-- schema.sql
CREATE TABLE `customers` (
  `customer_id` varchar(20) NOT NULL COMMENT 'CYYYYMMDDNNNN 형식',
  `name` varchar(100) NOT NULL,
  -- ...
  PRIMARY KEY (`customer_id`)
);
```

```php
// PHP 코드 - schema.sql과 일치
$customerId = 'C' . date('Ymd') . str_pad($seq, 4, '0', STR_PAD_LEFT);
$name = mysqli_real_escape_string($con, $_POST['name']);

$sql = "INSERT INTO customers (customer_id, name, created_at)
        VALUES ('{$customerId}', '{$name}', NOW())";
```

**체크리스트**:
- [ ] schema.sql에서 테이블 구조 확인
- [ ] 모든 NOT NULL 컬럼이 INSERT에 포함되어 있는지 확인
- [ ] 커스텀 ID (CYYYYMMDDNNNN) 생성 로직 추가
- [ ] `$response['result'] = false` 누락 여부 확인

---

## AJAX 및 라우팅 오류

### 오류 1: AJAX 요청 후 응답 없음

**증상**: updateAjaxContent 호출 후 아무 반응 없음

**원인**:
1. `window.pageName` 미설정
2. URL 형식 오류
3. PHP에서 `Finish()` 누락

**해결 방법**:

```javascript
// 1. window.pageName 설정 확인
window.pageName = '<?= encryptValue(date('Y-m-d') . '/my_page') ?>';

// 2. URL 형식 확인
updateAjaxContent(data, callback);
// updateAjaxContent 내부에서 자동으로 "<?= SRC ?>/" + window.pageName 사용
```

```php
// 3. PHP에서 Finish() 호출
case 'get_data':
    // ... 로직 처리
    $response['result'] = true;
    $response['items'] = $rows;
    Finish();  // 필수!
    break;
```

---

### 오류 2: 탭 로드 시 스크립트가 실행되지 않음

**증상**: AJAX로 로드된 탭 콘텐츠의 스크립트가 동작하지 않음

**원인**: jQuery의 `.html()` 메서드는 `<script>` 태그를 실행하지 않음

**해결 방법**:

`js.php`의 `loadTabContent` 함수 사용 (자동으로 스크립트 실행):

```javascript
loadTabContent(this, token, '#tab-content', '#sec-page');
```

또는 수동으로 스크립트 파싱:

```javascript
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
      console.error('스크립트 실행 오류:', e);
    }
  }
});
```

---

### 오류 3: 중복 라우팅 발생

**증상**: `/_ajax_.php/_ajax_.php/...` 형태의 URL 호출

**원인**: URL에 `/_ajax_.php/`를 명시적으로 포함

**해결 방법**:

```javascript
// 잘못된 방법
url: "<?= SRC ?>/_ajax_.php/" + pageName;  // 중복!

// 올바른 방법
url: "<?= SRC ?>/" + window.pageName;  // .htaccess가 자동으로 _ajax_.php로 라우팅
```

---

## 폼 및 데이터 전송 오류

### 오류 1: 폼 데이터가 전송되지 않음

**증상**: FormData를 사용했지만 서버에서 데이터를 받지 못함

**원인**:
1. `name` 속성 누락
2. fieldMap과 불일치

**해결 방법**: [패턴 5](#패턴-5-formdata에서-일부-필드가-누락됨) 참조

---

### 오류 2: 페이지 리로드됨 (폼 제출 시)

**증상**: 저장 버튼 클릭 시 페이지가 리로드됨

**원인**: form 자동 제출

**해결 방법**: [패턴 2](#패턴-2-데이터가-하나만-전송됨-폼-제출-시) 참조

---

## 디버깅 팁

### 1. PHP 디버깅

**에러 로그 확인**:
```php
// php.ini 설정
error_reporting = E_ALL
display_errors = On
log_errors = On
error_log = "C:/php/logs/php_errors.log"
```

**변수 출력**:
```php
error_log(print_r($_POST, true));  // 에러 로그에 기록
echo '<pre>' . print_r($response, true) . '</pre>';  // 화면 출력
```

**DB 쿼리 디버깅**:
```php
$sql = "SELECT * FROM users WHERE id = ?";
error_log("SQL: " . $sql);

if (!$result) {
    error_log("MySQL Error: " . mysqli_error($con));
}
```

### 2. JavaScript 디버깅

**콘솔 로그**:
```javascript
console.log('Data:', data);
console.log('Response:', response);
console.error('Error:', error);
```

**네트워크 탭 확인**:
1. 브라우저 개발자 도구 열기 (F12)
2. Network 탭 선택
3. AJAX 요청 확인
4. Request/Response 확인

**브레이크포인트 설정**:
```javascript
debugger;  // 실행 중단점
```

### 3. MySQL 디버깅

**테이블 구조 확인**:
```sql
SHOW CREATE TABLE users;
SHOW COLUMNS FROM users;
```

**FK 확인**:
```sql
SHOW INDEX FROM users;
SELECT * FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_NAME = 'users';
```

**쿼리 실행 계획**:
```sql
EXPLAIN SELECT * FROM users WHERE email = 'hong@example.com';
```

### 4. 일반적인 디버깅 체크리스트

**페이지가 로드되지 않을 때**:
- [ ] PHP 문법 오류 확인 (에러 로그)
- [ ] `common.php` 로드 확인
- [ ] 토큰 날짜 일치 확인
- [ ] DB 연결 확인

**AJAX가 동작하지 않을 때**:
- [ ] `window.pageName` 설정 확인
- [ ] Network 탭에서 요청/응답 확인
- [ ] PHP에서 `Finish()` 호출 확인
- [ ] `$response['result']` 설정 확인

**데이터가 저장되지 않을 때**:
- [ ] FormData + fieldMap 패턴 사용 확인
- [ ] `name` 속성 확인
- [ ] schema.sql과 쿼리 일치 확인
- [ ] 트랜잭션 커밋 확인

**버튼이 동작하지 않을 때**:
- [ ] onclick 이벤트 확인
- [ ] 함수명 오타 확인
- [ ] `window.functionName` 형식 확인
- [ ] 콘솔 에러 확인

---

**마지막 업데이트**: 2025-11-12

**문서 작성자**: Claude Code Team
