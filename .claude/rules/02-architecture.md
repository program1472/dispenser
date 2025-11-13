# 프로젝트 구조 및 아키텍처

> 디스펜서 영업 관리 시스템의 전체 아키텍처 및 핵심 파일 구조

---

## 📂 전체 디렉토리 구조

```
public/
├── index.php                    # 메인 진입점 (로그인 체크 + 초기 라우팅)
├── _ajax_.php                   # AJAX 요청 중계 허브 (암복호화 처리)
├── login.php / logout.php       # 인증 처리
├── member.php                   # 회원가입
├── common.php                   # DB 설정 (dbconfig.php 참조)
├── dbconfig.php                 # DB 연결 설정
│
├── inc/                         # 공통 모듈
│   ├── common.php               # 전역 상수, 함수, DB 연결 초기화
│   ├── menus.php                # 포털별 메뉴 구조 정의 (HQ/VENDOR/CUSTOMER/LUCID)
│   ├── topArea.php              # 헤더 + 메뉴 렌더링
│   ├── bottomArea.php           # 하단 공통 영역
│   └── functions/               # 공통 함수 라이브러리
│       ├── ende.php             # 암복호화 (encryptValue, decryptValue, decryptArrayRecursive)
│       ├── MySQLi.php           # DB 헬퍼 함수
│       ├── functions.php        # 범용 유틸리티
│       ├── JsonHalper.php       # JSON 처리
│       ├── SENDMAIL.php         # 이메일 발송
│       ├── date.php             # 날짜 유틸리티
│       ├── file.php             # 파일 처리
│       ├── error.php            # 에러 핸들링
│       └── permission.php       # 권한 검증
│
├── doc/                         # 포털별 페이지 디렉토리
│   ├── hq/                      # HQ (본사) 포털 페이지
│   │   ├── dashboard.php
│   │   ├── vendor_perf.php
│   │   ├── sales_perf.php
│   │   ├── hq_perf.php
│   │   ├── customer_perf.php
│   │   ├── customer_mgmt.php
│   │   ├── new_content.php
│   │   ├── work_orders.php
│   │   ├── shipping_labels.php
│   │   ├── invoices.php
│   │   ├── policy.php
│   │   ├── help.php
│   │   ├── dev.php
│   │   └── bottomArea.php       # HQ 하단 영역
│   │
│   ├── vendor/                  # 벤더 포털 페이지
│   │   ├── dashboard.php
│   │   ├── customer_mgmt.php
│   │   ├── work_orders.php
│   │   ├── billing.php
│   │   ├── settlement.php
│   │   ├── tickets.php
│   │   ├── new_content.php
│   │   ├── product_purchase.php
│   │   ├── inventory_serials.php
│   │   ├── notifications.php
│   │   └── bottomArea.php
│   │
│   ├── customer/                # 고객 포털 페이지
│   │   ├── dashboard.php
│   │   ├── device_mgmt.php
│   │   ├── content_lib.php
│   │   ├── scent_lib.php
│   │   ├── billing.php
│   │   ├── help.php
│   │   └── bottomArea.php
│   │
│   └── lucid/                   # 루시드 (협력사) 포털 페이지
│       ├── dashboard.php
│       ├── content_new.php
│       ├── edit_requests.php
│       ├── content_library.php
│       ├── tag_mgmt.php
│       ├── settlement.php
│       └── bottomArea.php
│
├── css/                         # 스타일시트
│   ├── style.css                # 공통 기본 스타일
│   ├── tem.css                  # 템플릿 스타일
│   ├── header.css               # 헤더/메뉴 스타일 (드롭다운 포함)
│   ├── hq.css                   # HQ 포털 전용
│   ├── vendor.css               # 벤더 포털 전용
│   ├── customer.css             # 고객 포털 전용
│   ├── lucid.css                # 루시드 포털 전용
│   └── xForm.css                # 폼 스타일
│
└── js/                          # JavaScript
    ├── js.php                   # 공통 JavaScript (loadPage, updateAjaxContent, 드롭다운 등)
    └── x.js                     # 추가 유틸리티
```

---

## 🔑 핵심 파일 역할 및 기능

### ■ index.php (메인 진입점)

**역할**: 초기 로드 및 라우팅 시작점

- 로그인 체크 (`$mb_id` 존재 여부)
- 미로그인 시 `login.php` 로드
- GET 파라미터 암복호화 처리
- POST 요청 시 `doc/{role}/{menuName}.php` 직접 로드
- `inc/topArea.php` 포함 (헤더/메뉴 렌더링)
- 최종적으로 `loadPage()` JavaScript 호출

**주요 로직**:
```php
// 1. 로그인 체크
if (!$mb_id) { require "login.php"; exit(); }

// 2. POST 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['menuName'])) {
    $menuName = decryptValue($_POST['menuName']);
    $filePath = DOC_ROOT."/doc/{$roleName}/{$menuName}.php";
    if (file_exists($filePath)) {
        require $filePath;
        exit();
    }
}

// 3. 헤더 렌더링
require "inc/topArea.php";

// 4. 초기 페이지 로드
echo "<script>loadPage('".$defaultToken."');</script>";
```

---

### ■ _ajax_.php (AJAX 중계 허브)

**역할**: 모든 AJAX 요청의 중앙 처리기

- 로그인 검증
- 요청 URI 파싱 및 토큰 복호화
- `$_GET`, `$_POST` 복호화 (`decryptArrayRecursive`)
- `doc/{role}/{menuName}.php` 파일 로드
- JSON 응답 반환 (`Finish()`)

**주요 로직**:
```php
header('Content-Type: application/json');
require_once "inc/common.php";

// 로그인 체크
if (is_null($mb_id)) { require 'logout.php'; exit(); }

// URI 파싱 및 토큰 복호화
$uri = $_SERVER['REQUEST_URI'];
$lastSegment = end(explode('/', trim($path, '/')));
$decrypted = decryptValue($lastSegment);
$segments = explode('/', $decrypted);

// 날짜 검증
if ($segments[0] !== $today) { exit(); }

// 파일 로드
$menuName = end($segments);
$targetFile = DOC_ROOT."/doc/{$roleName}/{$menuName}.php";
if (file_exists($targetFile)) {
    require $targetFile;
} else {
    $response['error'] = sendError(403, true);
    Finish();
}
```

---

### ■ inc/common.php (전역 초기화)

**역할**: 시스템 전체의 기반 설정

- 타임존 설정 (`Asia/Seoul`)
- 경로 상수 정의 (ROOT, DOC_ROOT, INC_ROOT, JS_ROOT, CSS_ROOT 등)
- 함수 파일 로드 (ende.php, MySQLi.php, functions.php 등)
- DB 연결 초기화 (`$con`)
- 전역 `$response` 배열 초기화
- 세션 변수 설정 (`$mb_id`, `$mb_role`, `$roleName`)

**주요 상수**:
```php
define('HOST', $scheme . '://' . $host);
define('ROOT', $DOCROOT);
define('DOC_ROOT', ROOT . SRC);
define('INC_ROOT', DOC_ROOT . '/inc');
define('FUNCTIONS_ROOT', INC_ROOT . '/functions');
define('JS_ROOT', DOC_ROOT . '/js');
define('CSS_ROOT', DOC_ROOT . '/css');
```

---

### ■ inc/menus.php (메뉴 구조 정의)

**역할**: 포털별 메뉴 데이터 제공

- 배열 형태로 각 포털의 메뉴 정의
- 서브메뉴 지원 (`sub` 배열)
- 메뉴 ID, 이름, 경로, 활성화 여부 관리

**구조**:
```php
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
    // ...
  ],
  "vendor" => [ /* ... */ ],
  "customer" => [ /* ... */ ],
  "lucid" => [ /* ... */ ]
];
```

---

### ■ inc/topArea.php (헤더/메뉴 렌더링)

**역할**: HTML 헤더와 네비게이션 메뉴 생성

- DOCTYPE, meta 태그, CSS 링크 출력
- 포털별 제목 및 브랜드명 설정
- `$menus[$role]` 배열 기반 메뉴 렌더링
- 서브메뉴가 있는 경우 드롭다운 구조 생성
- 캐시버스터 적용 (`filemtime` 기반)

**메뉴 렌더링 로직**:
```php
foreach ($menus[$role] as $item) {
  $id = $item['id'];
  $name = $item['name'];
  $path = $item['path'];
  $sub = $item['sub'] ?? null;

  if (!empty($sub) && is_array($sub)) {
    // 드롭다운 메뉴
    echo '<div class="dropdown">';
    echo '  <a class="dropdown-toggle">'.$name.'</a>';
    echo '  <div class="dropdown-menu">';
    foreach ($sub as $subItem) {
      // 서브메뉴 항목 렌더링
    }
    echo '  </div>';
    echo '</div>';
  } else {
    // 단일 메뉴
    echo '<a data-t="'.$id.'" onclick="loadPage(this, \''.encryptValue($today.'/'.$path).'\');">'.$name.'</a>';
  }
}
```

---

### ■ js/js.php (공통 JavaScript)

**역할**: 프론트엔드 핵심 기능 제공

**주요 함수**:

#### loadPage(el, menuName)
- AJAX 페이지 로드
- 토큰 복호화 없이 암호화된 토큰 그대로 전송
- 탭 active 상태 관리
- 드롭다운 부모 active 처리

```javascript
function loadPage(el, menuName) {
  // el 없이 토큰만 전달 가능
  if (typeof el === 'string' && menuName === undefined) {
    menuName = el;
    el = null;
  }

  // 탭 active 초기화
  document.querySelectorAll('#tabs a.active').forEach(a => a.classList.remove('active'));
  document.querySelectorAll('#tabs .dropdown.active').forEach(d => d.classList.remove('active'));

  // 클릭된 요소 active 추가
  if (el) {
    el.classList.add('active');
    const parentDropdown = el.closest('.dropdown');
    if (parentDropdown) parentDropdown.classList.add('active');
  }

  // AJAX 요청
  const data = {};
  data['<?= encryptValue('menuName') ?>'] = menuName;

  $.ajax({
    type: "POST",
    url: "#",
    dataType: "html",
    data: data,
    cache: false
  }).done(function(response){
    $('#content').html(response);
  });
}
```

#### updateAjaxContent(data, callback)
- JSON 응답 처리
- 성공/실패 분기

#### 드롭다운 메뉴 이벤트
- 클릭/외부 클릭 처리

---

### ■ inc/functions/ende.php (암복호화)

**역할**: 보안 토큰 및 데이터 암복호화

암복호화 함수의 상세 구현은 **[04-api-reference.md](./04-api-reference.md)** 문서를 참조하세요.

**주요 함수**:
- `encryptValue($value)` - 단일 값 암호화
- `decryptValue($value)` - 단일 값 복호화
- `decryptArrayRecursive($array)` - 배열 재귀 복호화

---

## 🔄 요청 흐름 (Request Flow)

### [페이지 최초 로드]
```
1. 사용자 → index.php 접근
2. index.php: 로그인 체크
3. index.php: inc/topArea.php 포함 (헤더/메뉴 렌더링)
4. index.php: loadPage(암호화토큰) 호출
5. JavaScript: POST → index.php (menuName 전송)
6. index.php: doc/{role}/{menuName}.php 로드
7. HTML 응답 → #content 영역에 삽입
```

### [메뉴 클릭 시]
```
1. 사용자 → 메뉴 클릭 (onclick="loadPage(this, 암호화토큰)")
2. JavaScript: loadPage() 실행
3. AJAX POST → # (실제로는 index.php로 처리됨)
4. index.php: menuName 복호화 → doc/{role}/{menuName}.php 로드
5. HTML 응답 → #content 영역에 삽입
```

### [AJAX 요청 (데이터 처리)]
```
1. 사용자 → 폼 제출 또는 버튼 클릭
2. JavaScript: updateAjaxContent(data, callback) 호출
3. AJAX POST → /{암호화토큰}
4. _ajax_.php: 토큰 복호화 → doc/{role}/{menuName}.php 로드
5. PHP: 비즈니스 로직 처리 → JSON 응답 (Finish())
6. JavaScript: callback 함수 실행 또는 alert
```

---

## 🔐 보안 및 인증 체계

### 토큰 기반 라우팅
- 모든 페이지 경로는 **암호화된 토큰** 형태로 전송
- 토큰 형식: `encryptValue("YYYY-MM-DD/menuName")`
- 날짜 검증: 요청 날짜가 오늘(`$today`)과 일치해야 함
- 복호화 실패 또는 날짜 불일치 시 로그아웃 처리

### 세션 기반 인증
- `$_SESSION['user']`: 사용자 정보 저장
- `$_SESSION['role']`: 현재 포털 역할 (hq/vendor/customer/lucid)
- `$mb_id`: 사용자 ID (로그인 여부 확인)
- `$mb_role`: 사용자 역할 코드

### 권한 검증
- 각 포털 디렉토리(`doc/hq`, `doc/vendor` 등)는 역할별로 분리
- `_ajax_.php`에서 `$roleName` 기반으로 파일 경로 결정
- 권한 없는 포털 접근 시 403 에러

---

## 🎨 CSS 및 UI 구조

CSS 및 UI 관련 상세 내용은 **[06-ui-components.md](./06-ui-components.md)** 문서를 참조하세요.

**주요 항목**:
- CSS 로드 순서
- 공통 UI 요소
- 반응형 지원
- 페이지 레이아웃 템플릿

---

## 📋 포털 구분

| 포털 | 역할 코드 | 디렉토리 | 주요 기능 |
|------|----------|---------|-----------|
| **HQ** | hq | doc/hq/ | 본사 관리, 정책·정산·출고·시리얼 관리, KPI 대시보드 |
| **VENDOR** | vendor | doc/vendor/ | 벤더 실적, 고객 관리, 인센티브·정산 관리, 티켓 연동 |
| **CUSTOMER** | customer | doc/customer/ | 기기·콘텐츠·향 관리, 주문/결제, 구독 및 라이브러리 |
| **LUCID** | lucid | doc/lucid/ | 협력사 콘텐츠 등록/수정, 루시드 배분·정산, 태그관리 |

---

**마지막 업데이트**: 2025-11-12 (v2.0)

### v2.0 변경사항 (2025-11-12)
- architecture.md → 02-architecture.md로 파일명 변경
- "CSS 및 UI 구조" 섹션 삭제 (06-ui-components.md로 이동)
- 암복호화 함수 구현 부분 삭제 (04-api-reference.md로 이동)
- 삭제된 섹션 위치에 다른 파일로의 링크 추가
