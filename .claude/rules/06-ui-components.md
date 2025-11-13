# UI 컴포넌트 (UI Components)

> 레이아웃, CSS, JavaScript 패턴, 날짜 필터 프리셋

---

## 목차

1. [CSS 로드 순서](#css-로드-순서)
2. [표준 페이지 레이아웃](#표준-페이지-레이아웃)
3. [CSS 클래스 규칙](#css-클래스-규칙)
4. [공통 JavaScript 패턴](#공통-javascript-패턴)
5. [상태 배지](#상태-배지)
6. [날짜 필터 프리셋](#날짜-필터-프리셋)
7. [드롭다운 메뉴](#드롭다운-메뉴)
8. [모달 팝업](#모달-팝업)
9. [반응형 규칙](#반응형-규칙)

---

## CSS 로드 순서

### 로드 순서

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

## 표준 페이지 레이아웃

### Single Page 구조

**모든 포털(HQ, Vendor, Customer, Lucid)의 모든 페이지에 동일하게 적용됩니다.**

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
window.pageName = '<?= encryptValue(date('Y-m-d') . '/{page_name}') ?>';

(function() {
  // 이벤트 핸들러
})();
</script>
```

### Tab Page 구조

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
                  onclick="loadTabContent(this, '<?= $tab1Token ?>', '#{name}-tab-content', '#sec-{페이지명}')">
            탭1
          </button>
          <button class="tab-btn-inline" data-token="<?= $tab2Token ?>"
                  onclick="loadTabContent(this, '<?= $tab2Token ?>', '#{name}-tab-content', '#sec-{페이지명}')">
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
// 탭 로드 함수는 js.php의 loadTabContent를 사용

// 페이지 로드 시 첫 번째 탭 자동 로드
setTimeout(function() {
  const firstTab = document.querySelector('#sec-{페이지명} .tab-btn-inline.active');
  if (firstTab) {
    const token = firstTab.getAttribute('data-token');
    loadTabContent(firstTab, token, '#{name}-tab-content', '#sec-{페이지명}');
  }
}, 100);
</script>
```

---

## CSS 클래스 규칙

### 레이아웃 클래스

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

### 그리드 클래스

```css
.grid-2            /* 2단 그리드 (1fr 1fr @980px+) */
.grid-3            /* 3단 그리드 (1fr -> 2fr@768px -> 3fr@1200px) */
.row               /* Flexbox 행 (gap: 8px) */
.filter-row        /* 필터 행 (gap: 10px, wrap) */
```

### 폼 클래스

```css
.form-control      /* Input/Select 기본 */
.input-w-150       /* 너비 150px */
.input-w-200       /* 너비 200px */
.form-group        /* 폼 그룹 (margin-bottom) */
```

### 버튼 클래스

```css
.btn               /* 기본 버튼 */
.btn.primary       /* 주요 버튼 (green) */
.btn-sm            /* 작은 버튼 */
.btn-edit          /* 수정 버튼 */
.btn-delete        /* 삭제 버튼 */
```

### 테이블 클래스

```css
.table-wrap        /* 테이블 래퍼 (overflow-x: auto) */
.table             /* 테이블 기본 스타일 */
.table-text-center /* 테이블 가운데 정렬 */
```

### 배지 클래스

```css
.badge             /* 배지 기본 */
.badge-success     /* 성공 (green) */
.badge-warning     /* 경고 (yellow) */
.badge-danger      /* 위험 (red) */
.badge-info        /* 정보 (blue) */
.badge-secondary   /* 보조 (gray) */
```

### 모달 클래스

```css
.modal             /* 모달 오버레이 */
.modal-content     /* 모달 콘텐츠 박스 */
.modal-header      /* 모달 헤더 */
.modal-body        /* 모달 본문 */
.modal-footer      /* 모달 푸터 */
.modal-close       /* 모달 닫기 버튼 */
```

### 탭 클래스

```css
.tab-nav-inline    /* 인라인 탭 네비게이션 */
.tab-btn-inline    /* 인라인 탭 버튼 */
.tab-btn-inline.active /* 활성화된 탭 */
```

---

## 공통 JavaScript 패턴

### 필터 & 검색

```javascript
// 필터 조회 (페이지 리로드 방식)
document.getElementById('btnFilter').addEventListener('click', function() {
  const filter1 = document.getElementById('filter{Name}').value;
  const search = document.getElementById('search{Name}').value;
  const params = new URLSearchParams();
  if (filter1) params.append('filter1', filter1);
  if (search) params.append('search', search);
  window.location.href = '?' + params.toString();
});

// 엔터키로 검색
document.getElementById('search{Name}').addEventListener('keypress', function(e) {
  if (e.key === 'Enter') {
    document.getElementById('btnFilter').click();
  }
});

// 필터 변경 시 자동 조회
document.getElementById('filter{Name}').addEventListener('change', function() {
  document.getElementById('btnFilter').click();
});
```

### CSV 내보내기

```javascript
document.getElementById('btnExportCsv').addEventListener('click', function() {
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

### 전체 선택

```javascript
document.getElementById('chkAll').addEventListener('change', function() {
  const checkboxes = document.querySelectorAll('#tbl{Name} tbody input[type="checkbox"]');
  checkboxes.forEach(cb => cb.checked = this.checked);
});
```

### 모달 열기/닫기

```javascript
// 모달 열기 - 추가
document.getElementById('btnAdd{Name}').addEventListener('click', function() {
  document.getElementById('formTitle').textContent = '{항목} 추가';
  document.getElementById('{name}Form').reset();
  document.getElementById('{id}Field').value = '';
  document.getElementById('modal{Name}Form').style.display = 'flex';
});

// 모달 열기 - 수정
document.querySelectorAll('.btn-edit').forEach(function(btn) {
  btn.addEventListener('click', function() {
    document.getElementById('formTitle').textContent = '{항목} 수정';
    document.getElementById('{id}Field').value = this.getAttribute('data-{id}');
    document.getElementById('{name}Field').value = this.getAttribute('data-{name}');
    // ... 기타 필드
    document.getElementById('modal{Name}Form').style.display = 'flex';
  });
});

// 모달 닫기
document.querySelectorAll('.modal-close').forEach(function(btn) {
  btn.addEventListener('click', function() {
    this.closest('.modal').style.display = 'none';
  });
});

// ESC 키로 모달 닫기
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    document.querySelectorAll('.modal').forEach(function(modal) {
      modal.style.display = 'none';
    });
  }
});
```

### AJAX 저장

```javascript
document.getElementById('btnSave{Name}').addEventListener('click', function() {
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

---

## 상태 배지

### 공통 상태 정의

#### 계약 상태 (Contract Status)

| 상태 | 의미 | 배지 색상 | CSS 클래스 |
|------|------|-----------|------------|
| ACTIVE | 정상 활성 | 초록 | badge-active |
| WARNING | 경고 (결제 지연 등) | 노랑 | badge-warning |
| GRACE | 유예 기간 | 주황 | badge-grace |
| TERMINATED | 종료 | 회색 | badge-terminated |

#### 지급 상태 (Payment Status)

| 상태 | 의미 | 배지 색상 | CSS 클래스 |
|------|------|-----------|------------|
| PLANNED | 지급 예정 | 회색 | badge-planned |
| DUE | 지급 대기 중 | 파랑 | badge-due |
| PAID | 지급 완료 | 초록 | badge-paid |

#### 티켓 상태 (Ticket Status)

| 상태 | 의미 | 배지 색상 | CSS 클래스 |
|------|------|-----------|------------|
| OPEN | 접수 | 파랑 | badge-open |
| IN_PROGRESS | 처리 중 | 주황 | badge-progress |
| RESOLVED | 완료 | 초록 | badge-done |

#### 작업지시서 상태 (Work Order Status)

| 상태 | 의미 | 배지 색상 | CSS 클래스 |
|------|------|-----------|------------|
| OPEN | 대기 | 파랑 | badge-open |
| IN_PROGRESS | 진행 중 | 주황 | badge-progress |
| DONE | 완료 | 초록 | badge-done |

#### 배송 상태 (Shipping Status)

| 상태 | 의미 | 배지 색상 | CSS 클래스 |
|------|------|-----------|------------|
| REQUESTED | 요청 | 회색 | badge-secondary |
| CONFIRMED | 확인 | 파랑 | badge-info |
| SHIPPED | 배송 중 | 주황 | badge-warning |
| DELIVERED | 배송 완료 | 초록 | badge-success |

### HTML 예시

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

### CSS 스타일

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

## 날짜 필터 프리셋

### 개요

날짜 필터가 있는 모든 페이지에서 사용자가 빠르게 날짜 범위를 설정할 수 있도록 프리셋 버튼을 제공합니다.

**지원 프리셋**:
- 오늘
- 금주 (이번 주 월요일 ~ 오늘)
- 전주 (지난 주 월요일 ~ 일요일)
- 당월 (이번 달 1일 ~ 오늘)
- 전월 (지난 달 1일 ~ 말일)
- 최근1개월 (30일 전 ~ 오늘)

### 구현 방법

#### 1. 날짜 입력 필드 준비

날짜 입력 필드는 반드시 다음 ID를 가져야 합니다:
- `startDate` 또는 `startDate{접미사}`
- `endDate` 또는 `endDate{접미사}`

```html
<input type="date" id="startDate" class="form-control">
<input type="date" id="endDate" class="form-control">
```

**접미사가 있는 경우** (같은 페이지에 여러 날짜 필터가 있을 때):
```html
<!-- 첫 번째 필터 -->
<input type="date" id="startDate" class="form-control">
<input type="date" id="endDate" class="form-control">

<!-- 두 번째 필터 (접미사: "2") -->
<input type="date" id="startDate2" class="form-control">
<input type="date" id="endDate2" class="form-control">
```

#### 2. 프리셋 버튼 추가

날짜 입력 필드 바로 옆에 프리셋 버튼을 추가합니다:

```html
<div class="date-preset-buttons">
  <button type="button" class="btn-preset" onclick="setDate('today')">오늘</button>
  <button type="button" class="btn-preset" onclick="setDate('thisWeek')">금주</button>
  <button type="button" class="btn-preset" onclick="setDate('prevWeek')">전주</button>
  <button type="button" class="btn-preset" onclick="setDate('thisMonth')">당월</button>
  <button type="button" class="btn-preset" onclick="setDate('prevMonth')">전월</button>
  <button type="button" class="btn-preset" onclick="setDate('30days')">최근1개월</button>
</div>
```

**접미사가 있는 경우**:
```html
<div class="date-preset-buttons">
  <button type="button" class="btn-preset" onclick="setDate('today', '2')">오늘</button>
  <button type="button" class="btn-preset" onclick="setDate('thisWeek', '2')">금주</button>
  <!-- ... 나머지 버튼 ... -->
</div>
```

#### 3. 스타일링 (선택사항)

프리셋 버튼의 기본 스타일:

```css
.date-preset-buttons {
  display: flex;
  gap: 5px;
  margin-left: 10px;
}

.btn-preset {
  padding: 6px 12px;
  font-size: 12px;
  background: #f0f0f0;
  border: 1px solid #ccc;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-preset:hover {
  background: #e0e0e0;
  border-color: #999;
}

.btn-preset:active {
  background: #d0d0d0;
}
```

### 완전한 예시

#### 예시 1: 기본 필터 영역

```html
<div class="filter-area">
  <div class="row">
    <label>기간:</label>
    <input type="date" id="startDate" class="form-control" style="max-width:150px">
    <span>~</span>
    <input type="date" id="endDate" class="form-control" style="max-width:150px">

    <!-- 프리셋 버튼 -->
    <div class="date-preset-buttons">
      <button type="button" class="btn-preset" onclick="setDate('today')">오늘</button>
      <button type="button" class="btn-preset" onclick="setDate('thisWeek')">금주</button>
      <button type="button" class="btn-preset" onclick="setDate('prevWeek')">전주</button>
      <button type="button" class="btn-preset" onclick="setDate('thisMonth')">당월</button>
      <button type="button" class="btn-preset" onclick="setDate('prevMonth')">전월</button>
      <button type="button" class="btn-preset" onclick="setDate('30days')">최근1개월</button>
    </div>

    <button id="btnSearch" class="btn">조회</button>
  </div>
</div>
```

#### 예시 2: card-hd 영역에 통합된 필터

```html
<div class="card">
  <div class="card-hd">
    <div style="display:flex; justify-content:space-between; align-items:center; width:100%;">
      <h3>실적 조회</h3>

      <div style="display:flex; gap:10px; align-items:center;">
        <input type="date" id="startDate" class="form-control" style="max-width:150px">
        <span>~</span>
        <input type="date" id="endDate" class="form-control" style="max-width:150px">

        <div class="date-preset-buttons">
          <button type="button" class="btn-preset" onclick="setDate('today')">오늘</button>
          <button type="button" class="btn-preset" onclick="setDate('thisWeek')">금주</button>
          <button type="button" class="btn-preset" onclick="setDate('prevWeek')">전주</button>
          <button type="button" class="btn-preset" onclick="setDate('thisMonth')">당월</button>
          <button type="button" class="btn-preset" onclick="setDate('prevMonth')">전월</button>
          <button type="button" class="btn-preset" onclick="setDate('30days')">최근1개월</button>
        </div>

        <button id="btnSearch" class="btn">조회</button>
      </div>
    </div>
  </div>

  <div class="card-body">
    <!-- 컨텐츠 영역 -->
  </div>
</div>
```

### setDate() 함수 상세

#### 함수 시그니처
```javascript
setDate(type, pid = '')
```

#### 파라미터
- **type** (string, required): 날짜 프리셋 타입
  - `'today'`: 오늘부터 오늘까지
  - `'thisWeek'`: 이번 주 월요일부터 오늘까지
  - `'prevWeek'`: 지난 주 월요일부터 일요일까지
  - `'thisMonth'`: 이번 달 1일부터 오늘까지
  - `'prevMonth'`: 지난 달 1일부터 말일까지
  - `'30days'`: 30일 전부터 오늘까지
  - `'week'`: 7일 전부터 오늘까지 (구형)

- **pid** (string, optional): 날짜 입력 필드 ID 접미사
  - 기본값: `''` (빈 문자열)
  - 여러 날짜 필터가 있을 때 사용

#### 동작 원리
1. 선택한 프리셋 타입에 따라 시작일과 종료일을 계산
2. `startDate{pid}` 입력 필드에 시작일 설정
3. `endDate{pid}` 입력 필드에 종료일 설정
4. 날짜 형식: `YYYY-MM-DD` (HTML5 date input 표준)

#### 사용 예시
```javascript
// 기본 사용 (startDate, endDate)
setDate('today');        // 오늘
setDate('thisMonth');    // 이번 달

// 접미사 사용 (startDate2, endDate2)
setDate('prevWeek', '2');  // 지난 주
setDate('30days', '2');    // 최근 30일
```

### 체크리스트

새로운 페이지에 날짜 필터를 추가할 때:

- [ ] 날짜 입력 필드 ID가 `startDate`/`endDate` 형식인가?
- [ ] 여러 날짜 필터가 있다면 접미사를 사용했는가?
- [ ] 프리셋 버튼을 날짜 필드 옆에 추가했는가?
- [ ] 프리셋 버튼의 `onclick` 핸들러가 올바른 접미사를 전달하는가?
- [ ] 스타일이 다른 페이지와 일관성 있는가?

### 적용 대상 페이지

날짜 필터가 있어 프리셋 버튼을 추가해야 할 페이지:

**HQ 포털**:
- `dashboard.php` - 대시보드 통계
- `customer_perf.php` - 고객 실적
- `vendor_perf.php` - 벤더 실적
- `sales_perf.php` - 영업 실적
- `hq_perf.php` - HQ 실적
- `shipping_history.php` - 출고 히스토리
- `work_orders.php` - 작업지시서 목록

**Vendor 포털**:
- `dashboard.php` - 대시보드
- `settlement.php` - 정산 내역
- `work_orders.php` - 작업지시서

**Customer 포털**:
- `dashboard.php` - 대시보드
- `shipping.php` - 배송 정보
- `billing.php` - 결제 내역

**Lucid 포털**:
- `settlement.php` - 정산 내역

---

## 드롭다운 메뉴

### 구조

```html
<div class="dropdown">
  <a class="dropdown-toggle">실적</a>
  <div class="dropdown-menu">
    <a onclick="loadPage(this, '<?= $token1 ?>')">벤더</a>
    <a onclick="loadPage(this, '<?= $token2 ?>')">영업사원</a>
    <a onclick="loadPage(this, '<?= $token3 ?>')">본사</a>
  </div>
</div>
```

### JavaScript 동작

```javascript
// 드롭다운 토글
document.querySelectorAll('.dropdown-toggle').forEach(function(toggle) {
  toggle.addEventListener('click', function(e) {
    e.preventDefault();
    const dropdown = this.closest('.dropdown');
    dropdown.classList.toggle('open');
  });
});

// 외부 클릭 시 닫기
document.addEventListener('click', function(e) {
  if (!e.target.closest('.dropdown')) {
    document.querySelectorAll('.dropdown.open').forEach(function(dropdown) {
      dropdown.classList.remove('open');
    });
  }
});
```

---

## 모달 팝업

### 구조

```html
<div id="detailModal" class="modal" style="display:none;">
  <div class="modal-content">
    <div class="modal-header">
      <h3>상세 정보</h3>
      <button class="modal-close">&times;</button>
    </div>
    <div class="modal-body">
      <!-- 팝업 내용 -->
    </div>
    <div class="modal-footer">
      <button class="modal-close">닫기</button>
    </div>
  </div>
</div>
```

### JavaScript 동작

```javascript
// 모달 열기
function openModal(modalId) {
  document.getElementById(modalId).style.display = 'flex';
}

// 모달 닫기
function closeModal(modalId) {
  document.getElementById(modalId).style.display = 'none';
}

// ESC 키로 닫기
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => modal.style.display = 'none');
  }
});

// 모달 외부 클릭 시 닫기 (선택사항)
document.querySelectorAll('.modal').forEach(function(modal) {
  modal.addEventListener('click', function(e) {
    if (e.target === this) {
      this.style.display = 'none';
    }
  });
});
```

---

## 반응형 규칙

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

**마지막 업데이트**: 2025-11-12

**문서 작성자**: Claude Code Team
