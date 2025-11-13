# 빠른 시작 가이드 (Quick Start)

> 5분 안에 디스펜서 프로젝트를 시작하는 방법

**읽는 시간**: 약 5분

---

## 목차

1. [개발 환경 세팅](#개발-환경-세팅)
2. [핵심 개념 3가지](#핵심-개념-3가지)
3. [첫 페이지 만들기](#첫-페이지-만들기)
4. [다음 단계](#다음-단계)

---

## 개발 환경 세팅

### 1. 데이터베이스 연결 확인

```bash
# MySQL 연결 테스트
"C:\php\server\MariaDB10\bin\mysql.exe" -u program1472 -p$gPfls1129 dispenser -e "SELECT COUNT(*) FROM users;"
```

### 2. 프로젝트 구조 이해

```
public/
├── index.php           # 메인 진입점 (로그인 체크 + 라우팅)
├── _ajax_.php          # AJAX 중계 허브
├── inc/
│   ├── common.php      # 전역 설정 (DB 연결, 상수 정의)
│   ├── menus.php       # 포털별 메뉴 구조
│   └── functions/
│       ├── ende.php    # 암복호화
│       └── MySQLi.php  # DB 헬퍼
├── doc/{role}/         # 포털별 페이지
│   ├── hq/
│   ├── vendor/
│   ├── customer/
│   └── lucid/
├── css/                # 공통 CSS
└── js/js.php           # 공통 JavaScript
```

### 3. 필수 파일 확인

모든 페이지는 `inc/common.php`를 통해 시작됩니다:

```php
<?php
// inc/common.php가 자동으로 로드되므로 별도 선언 불필요
// 이미 사용 가능한 전역 변수:
// - $con: MySQLi 연결
// - $mb_id: 로그인 사용자 ID
// - $mb_role: 사용자 역할 코드
// - $roleName: 포털명 (hq/vendor/customer/lucid)
// - $response: 표준 응답 배열
?>
```

---

## 핵심 개념 3가지

### 1. 토큰 기반 라우팅

모든 페이지 경로는 **암호화된 토큰**으로 전송됩니다.

```php
// 토큰 생성 (PHP)
$token = encryptValue(date('Y-m-d') . '/dashboard');
```

```javascript
// 페이지 로드 (JavaScript)
loadPage(this, '<?= $token ?>');
```

**날짜 검증**: 요청 날짜가 오늘(`$today`)과 일치해야 합니다.

### 2. AJAX 요청 흐름

**페이지 로드 (index.php 경유)**:
```
메뉴 클릭 → loadPage() → index.php → doc/{role}/{menuName}.php → HTML 응답
```

**데이터 통신 (_ajax_.php 경유)**:
```
버튼 클릭 → updateAjaxContent() → _ajax_.php → 비즈니스 로직 → JSON 응답
```

### 3. 표준 응답 포맷

```php
// 성공
$response['result'] = true;
$response['msg'] = '성공 메시지';
Finish();

// 실패
$response['result'] = false;
$response['error'] = ['msg' => '오류 메시지', 'code' => 400];
Finish();

// 데이터 반환
$response['result'] = true;
$response['item'] = $row;  // 단일 객체
Finish();

$response['result'] = true;
$response['items'] = $rows;  // 배열
Finish();

// HTML 반환 (필터/조회)
$response['result'] = true;
$response['html'] = '<tr>...</tr>';
Finish();
```

**중요**: `$response` 변수는 `common.php`에서 이미 전역으로 선언되어 있으므로 개별 페이지에서 초기화하지 않습니다.

**허용된 키**: `result`, `msg`, `html`, `item`, `items`, `error` (이 외 사용 금지)

---

## 첫 페이지 만들기

### 단계 1: 메뉴 등록

`inc/menus.php`에 메뉴 추가:

```php
$menus = [
  "hq" => [
    ["name" => "대시보드", "id" => "H01", "path" => "dashboard", "enabled" => true],
    ["name" => "내 페이지", "id" => "H10", "path" => "my_page", "enabled" => true],
    // ...
  ]
];
```

### 단계 2: 페이지 파일 생성

`doc/hq/my_page.php` 생성:

```php
<?php
// AJAX 요청 처리 (POST일 때)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['action'])) {
    header('Content-Type: application/json; charset=utf-8');

    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'get_data':
            // 데이터 조회
            $sql = "SELECT * FROM my_table LIMIT 10";
            $result = mysqli_query($con, $sql);
            $items = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $items[] = $row;
            }

            $response['result'] = true;
            $response['items'] = $items;
            Finish();
            break;

        case 'add_item':
            // 데이터 추가
            $name = mysqli_real_escape_string($con, $_POST['name'] ?? '');

            if (empty($name)) {
                $response['result'] = false;
                $response['error'] = ['msg' => '필수 항목을 입력해주세요.', 'code' => 400];
                Finish();
            }

            $sql = "INSERT INTO my_table (name, created_at) VALUES ('{$name}', NOW())";
            $result = mysqli_query($con, $sql);

            if ($result) {
                $response['result'] = true;
                $response['msg'] = '등록되었습니다.';
            } else {
                $response['result'] = false;
                $response['error'] = ['msg' => '등록 실패', 'code' => 500];
            }
            Finish();
            break;
    }

    // 알 수 없는 액션
    $response['result'] = false;
    $response['error'] = ['msg' => '지원하지 않는 요청입니다.', 'code' => 400];
    Finish();
}

// 페이지 HTML (GET일 때)
$sql = "SELECT * FROM my_table LIMIT 20";
$result = mysqli_query($con, $sql);
$items = [];
while ($row = mysqli_fetch_assoc($result)) {
    $items[] = $row;
}
?>

<div class="wrap">
  <section id="sec-my-page" class="card section-card-first">
    <div class="card-hd card-hd-wrap">
      <div class="card-hd-content">
        <div class="card-hd-title-area">
          <div class="card-ttl">내 페이지</div>
          <div class="card-sub">첫 번째 페이지입니다.</div>
        </div>
        <div class="row filter-row">
          <input type="text" id="searchKeyword" class="form-control input-w-200" placeholder="검색">
          <button id="btnFilter" class="btn primary">조회</button>
          <button id="btnAdd" class="btn primary">항목 추가</button>
        </div>
      </div>
    </div>
    <div class="card-bd">
      <div class="table-wrap">
        <table class="table" id="tblItems">
          <thead>
            <tr>
              <th>번호</th>
              <th>이름</th>
              <th>생성일</th>
              <th>관리</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $index => $item): ?>
            <tr>
              <td><?= $index + 1 ?></td>
              <td><strong><?= htmlspecialchars($item['name']) ?></strong></td>
              <td><?= $item['created_at'] ?></td>
              <td>
                <button class="btn-sm btn-edit" data-id="<?= $item['id'] ?>">수정</button>
                <button class="btn-sm btn-delete" data-id="<?= $item['id'] ?>">삭제</button>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($items)): ?>
            <tr>
              <td colspan="4" style="text-align:center;">데이터가 없습니다.</td>
            </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

<!-- 모달 -->
<div id="modalAddForm" class="modal" style="display:none">
  <div class="modal-content">
    <div class="modal-header">
      <h3 id="formTitle">항목 추가</h3>
      <button class="modal-close">&times;</button>
    </div>
    <div class="modal-body">
      <form id="frmItem" onsubmit="return false;">
        <div class="form-group">
          <label>이름 <span style="color:red;">*</span></label>
          <input type="text" id="itemName" name="name" class="form-control" required>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn modal-close">취소</button>
      <button id="btnSave" class="btn primary">저장</button>
    </div>
  </div>
</div>

<script>
// 페이지 이름 (AJAX 호출용)
window.pageName = '<?= encryptValue(date('Y-m-d') . '/my_page') ?>';

// 필터 조회
window.filterItems = function() {
  const searchKeyword = document.getElementById('searchKeyword').value || '';

  const data = {};
  data['<?= encryptValue('action') ?>'] = 'get_data';
  data['<?= encryptValue('search') ?>'] = searchKeyword;

  updateAjaxContent(data, function(response) {
    if (response.result && response.items) {
      // 테이블 갱신
      const tbody = document.querySelector('#tblItems tbody');
      tbody.innerHTML = '';

      if (response.items.length > 0) {
        response.items.forEach(function(item, index) {
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${index + 1}</td>
            <td><strong>${item.name}</strong></td>
            <td>${item.created_at}</td>
            <td>
              <button class="btn-sm btn-edit" data-id="${item.id}">수정</button>
              <button class="btn-sm btn-delete" data-id="${item.id}">삭제</button>
            </td>
          `;
          tbody.appendChild(tr);
        });
      } else {
        tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;">데이터가 없습니다.</td></tr>';
      }
    }
  });
};

// 항목 추가 버튼
document.getElementById('btnAdd').addEventListener('click', function() {
  document.getElementById('formTitle').textContent = '항목 추가';
  document.getElementById('frmItem').reset();
  document.getElementById('modalAddForm').style.display = 'flex';
});

// 저장 버튼
document.getElementById('btnSave').addEventListener('click', function() {
  const form = document.getElementById('frmItem');
  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  const formData = new FormData(form);
  const data = {};
  data['<?= encryptValue('action') ?>'] = 'add_item';

  const fieldMap = {
    'name': '<?= encryptValue('name') ?>'
  };

  for (let [key, value] of formData.entries()) {
    if (fieldMap[key]) {
      data[fieldMap[key]] = value;
    }
  }

  updateAjaxContent(data, function(response) {
    if (response.result) {
      alert(response.msg || '저장되었습니다.');
      document.getElementById('modalAddForm').style.display = 'none';
      location.reload();
    } else {
      alert(response.error?.msg || '저장에 실패했습니다.');
    }
  }, false);
});

// 모달 닫기
document.querySelectorAll('.modal-close').forEach(function(btn) {
  btn.addEventListener('click', function() {
    this.closest('.modal').style.display = 'none';
  });
});

// 조회 버튼
document.getElementById('btnFilter').addEventListener('click', filterItems);

// 검색창 엔터키
document.getElementById('searchKeyword').addEventListener('keypress', function(e) {
  if (e.key === 'Enter') {
    filterItems();
  }
});
</script>
```

### 단계 3: 페이지 확인

1. 로그인 후 HQ 포털 접속
2. 메뉴에서 "내 페이지" 클릭
3. 데이터 조회, 추가 동작 확인

---

## 다음 단계

### 더 자세히 알아보기

**아키텍처 이해**:
- [architecture.md](./architecture.md) - 전체 구조 및 요청 흐름

**코딩 규약**:
- [03-coding-standards.md](./03-coding-standards.md) - 코딩 표준 및 체크리스트

**API 참조**:
- [04-api-reference.md](./04-api-reference.md) - 필수 함수 및 응답 포맷

**UI 컴포넌트**:
- [06-ui-components.md](./06-ui-components.md) - 레이아웃, 모달, 날짜 필터

**문제 해결**:
- [08-troubleshooting.md](./08-troubleshooting.md) - FAQ 및 디버깅 팁

### 포털별 개발

- **HQ 포털**: [portals/hq.md](./portals/hq.md)
- **벤더 포털**: [portals/vendor.md](./portals/vendor.md)
- **고객 포털**: [portals/customer.md](./portals/customer.md)
- **루시드 포털**: [portals/lucid.md](./portals/lucid.md)

### 정책 및 계산식

- [policies.md](./policies.md) - 구독료, 커미션, 인센티브, KPI 공식

### 데이터베이스

- [database.md](./database.md) - DB 스키마 및 FK 관리
- [schema.sql](../../schema.sql) - 실제 DB 스키마 정의

---

## 체크리스트

신규 페이지 개발 시 확인사항:

- [ ] `inc/menus.php`에 메뉴 등록
- [ ] 페이지 파일 생성 (`doc/{role}/{menuName}.php`)
- [ ] POST 요청 처리 (AJAX 액션)
- [ ] GET 요청 처리 (HTML 렌더링)
- [ ] `window.pageName` 설정
- [ ] `updateAjaxContent()` 함수 사용
- [ ] 표준 응답 포맷 (`$response` + `Finish()`)
- [ ] FormData + fieldMap 패턴 사용
- [ ] 더미 데이터 20건 이상
- [ ] 모달 구조 (`modal` > `modal-content` > `modal-header/body/footer`)

---

**마지막 업데이트**: 2025-11-12

**문서 작성자**: Claude Code Team
