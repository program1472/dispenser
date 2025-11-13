# 날짜 필터 프리셋 버튼 사용 가이드

> 필터 영역에 날짜 프리셋 버튼을 추가하여 사용자 편의성을 높이는 가이드

---

## 📋 개요

날짜 필터가 있는 모든 페이지에서 사용자가 빠르게 날짜 범위를 설정할 수 있도록 프리셋 버튼을 제공합니다.

**지원 프리셋**:
- 오늘
- 금주 (이번 주 월요일 ~ 오늘)
- 전주 (지난 주 월요일 ~ 일요일)
- 당월 (이번 달 1일 ~ 오늘)
- 전월 (지난 달 1일 ~ 말일)
- 최근1개월 (30일 전 ~ 오늘)

---

## 🔧 구현 방법

### 1. 날짜 입력 필드 준비

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

### 2. 프리셋 버튼 추가

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

### 3. 스타일링 (선택사항)

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

---

## 📝 완전한 예시

### 예시 1: 기본 필터 영역

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

<style>
.filter-area {
  background: #f5f5f5;
  padding: 15px;
  border-radius: 8px;
  margin-bottom: 20px;
}

.filter-area .row {
  display: flex;
  gap: 10px;
  align-items: center;
}

.date-preset-buttons {
  display: flex;
  gap: 5px;
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
</style>
```

### 예시 2: 여러 날짜 필터가 있는 경우

```html
<div class="filter-area">
  <!-- 첫 번째 날짜 필터 (생성일) -->
  <div class="row">
    <label>생성일:</label>
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
  </div>

  <!-- 두 번째 날짜 필터 (수정일) -->
  <div class="row">
    <label>수정일:</label>
    <input type="date" id="startDate2" class="form-control" style="max-width:150px">
    <span>~</span>
    <input type="date" id="endDate2" class="form-control" style="max-width:150px">

    <div class="date-preset-buttons">
      <button type="button" class="btn-preset" onclick="setDate('today', '2')">오늘</button>
      <button type="button" class="btn-preset" onclick="setDate('thisWeek', '2')">금주</button>
      <button type="button" class="btn-preset" onclick="setDate('prevWeek', '2')">전주</button>
      <button type="button" class="btn-preset" onclick="setDate('thisMonth', '2')">당월</button>
      <button type="button" class="btn-preset" onclick="setDate('prevMonth', '2')">전월</button>
      <button type="button" class="btn-preset" onclick="setDate('30days', '2')">최근1개월</button>
    </div>
  </div>

  <button id="btnSearch" class="btn">조회</button>
</div>
```

### 예시 3: card-hd 영역에 통합된 필터

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

---

## 🎯 setDate() 함수 상세

### 함수 시그니처
```javascript
setDate(type, pid = '')
```

### 파라미터
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

### 동작 원리
1. 선택한 프리셋 타입에 따라 시작일과 종료일을 계산
2. `startDate{pid}` 입력 필드에 시작일 설정
3. `endDate{pid}` 입력 필드에 종료일 설정
4. 날짜 형식: `YYYY-MM-DD` (HTML5 date input 표준)

### 사용 예시
```javascript
// 기본 사용 (startDate, endDate)
setDate('today');        // 오늘
setDate('thisMonth');    // 이번 달

// 접미사 사용 (startDate2, endDate2)
setDate('prevWeek', '2');  // 지난 주
setDate('30days', '2');    // 최근 30일
```

---

## 📌 체크리스트

새로운 페이지에 날짜 필터를 추가할 때:

- [ ] 날짜 입력 필드 ID가 `startDate`/`endDate` 형식인가?
- [ ] 여러 날짜 필터가 있다면 접미사를 사용했는가?
- [ ] 프리셋 버튼을 날짜 필드 옆에 추가했는가?
- [ ] 프리셋 버튼의 `onclick` 핸들러가 올바른 접미사를 전달하는가?
- [ ] 스타일이 다른 페이지와 일관성 있는가?

---

## 🔍 적용 대상 페이지

날짜 필터가 있어 프리셋 버튼을 추가해야 할 페이지:

### HQ 포털
- [ ] `dashboard.php` - 대시보드 통계
- [ ] `customer_perf.php` - 고객 실적
- [ ] `vendor_perf.php` - 벤더 실적
- [ ] `sales_perf.php` - 영업 실적
- [ ] `hq_perf.php` - HQ 실적
- [ ] `shipping_history.php` - 출고 히스토리
- [ ] `work_orders.php` - 작업지시서 목록

### Vendor 포털
- [ ] `dashboard.php` - 대시보드
- [ ] `settlement.php` - 정산 내역
- [ ] `work_orders.php` - 작업지시서

### Customer 포털
- [ ] `dashboard.php` - 대시보드
- [ ] `shipping.php` - 배송 정보
- [ ] `billing.php` - 결제 내역

### Lucid 포털
- [ ] `settlement.php` - 정산 내역

---

## 💡 팁

1. **일관성 유지**: 모든 페이지에서 동일한 순서로 버튼 배치
   - 순서: 오늘 → 금주 → 전주 → 당월 → 전월 → 최근1개월

2. **레이아웃 최적화**:
   - 프리셋 버튼이 많을 경우 두 줄로 나눌 수 있음
   - 모바일 환경에서는 드롭다운으로 변경 고려

3. **초기값 설정**:
   - 페이지 로드 시 기본 날짜 범위를 설정하려면:
   ```javascript
   document.addEventListener('DOMContentLoaded', function() {
     setDate('thisMonth'); // 기본값: 이번 달
   });
   ```

4. **접근성**:
   - 버튼에 `type="button"` 속성 추가 (폼 제출 방지)
   - 키보드 탐색 가능하도록 `tabindex` 고려

---

## ⚠️ 주의사항

1. **필수 요소**: `js.php`가 페이지에 포함되어 있어야 `setDate()` 함수 사용 가능
   - 대부분의 페이지는 `inc/common.php`에서 자동으로 포함됨

2. **날짜 형식**: HTML5 `<input type="date">`만 지원
   - 다른 날짜 선택기 라이브러리 사용 시 별도 구현 필요

3. **브라우저 호환성**:
   - IE에서는 date input 타입 미지원
   - 폴백 UI 고려 필요 (현재는 모던 브라우저만 지원)

4. **시간대**:
   - 모든 날짜는 로컬 시간대 기준
   - 서버 시간대와 다를 수 있음 주의
