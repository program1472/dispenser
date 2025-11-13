# 자주 발생하는 실수 및 금지 사항

> 반복적으로 발생하는 실수들을 방지하기 위한 체크리스트

**최종 업데이트**: 2025-01-13

---

## 1. JavaScript에서 PHP 함수 호출 금지

### ❌ 절대 금지
```javascript
// PHP 함수는 JavaScript에서 호출 불가!
const data = {};
data[encryptedKey] = encryptValue(value);  // ❌ 오류 발생!
```

### ✅ 올바른 방법
```javascript
// PHP에서 미리 암호화된 키 사용, 값은 그대로 전달
const data = {};
data['<?= encryptValue("start_date") ?>'] = startDate;  // ✅ 정상
```

**원칙**:
- `encryptValue()`, `decryptValue()` 등 PHP 함수는 **서버에서만 실행됨**
- JavaScript 코드에서는 **PHP에서 미리 암호화한 키**만 사용
- 값(value)은 평문 그대로 전달 (서버에서 암호화 처리)

---

## 2. 탭 페이지 필터 조회 처리

### ❌ 절대 금지
```javascript
// 탭 페이지에서 window.location.href 사용 금지!
document.getElementById('btnApplyFilter').addEventListener('click', function() {
  const params = new URLSearchParams();
  params.append('start_date', startDate);
  window.location.href = '/doc/hq/perf_all.php?' + params.toString();  // ❌ 홈으로 이동됨!
});
```

### ✅ 올바른 방법
```javascript
// updateAjaxContent() 사용
document.getElementById('btnApplyFilter')?.addEventListener('click', function() {
  const startDate = document.getElementById('startDate').value;
  const endDate = document.getElementById('endDate').value;

  const data = {};
  if (startDate) data['<?= encryptValue('start_date') ?>'] = startDate;
  if (endDate) data['<?= encryptValue('end_date') ?>'] = endDate;

  updateAjaxContent(data, function(response) {
    if (response.result === 'ok' && response.html) {
      const contentArea = document.querySelector('#탭콘텐츠영역ID');
      if (contentArea) {
        contentArea.innerHTML = response.html;
        // 스크립트 재실행
        contentArea.querySelectorAll('script').forEach(function(oldScript) {
          const newScript = document.createElement('script');
          if (oldScript.src) {
            newScript.src = oldScript.src;
          } else {
            newScript.text = oldScript.text || oldScript.textContent || oldScript.innerHTML;
          }
          oldScript.parentNode.replaceChild(newScript, oldScript);
        });
      }
    }
  }, false);
});
```

**원칙**:
- 탭 구조에서는 **절대 `window.location.href` 사용 금지**
- 반드시 `updateAjaxContent()` 함수 사용
- 페이지 전체가 아닌 **탭 콘텐츠만 교체**

---

## 3. 탭 페이지 필터 파라미터 처리

### ❌ 잘못된 방법
```php
<?php
// 탭 페이지에서 $_GET 사용 금지!
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');  // ❌
```

### ✅ 올바른 방법
```php
<?php
// 탭 페이지는 $_POST로 필터 받기
$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-01');  // ✅
$endDate = isset($_POST['end_date']) ? $_POST['end_date'] : date('Y-m-t');

// SQL Injection 방지
$startDate = mysqli_real_escape_string($con, $startDate);
$endDate = mysqli_real_escape_string($con, $endDate);
```

**원칙**:
- **탭 구조 페이지**: `$_POST`로 필터 파라미터 받기
- **독립 페이지**: `$_GET`으로 받기
- AJAX 요청은 항상 POST로 전달됨

---

## 4. 버튼 타입 명시 필수

### ❌ 잘못된 방법
```html
<!-- type 없으면 form submit으로 동작! -->
<button id="btnApplyFilter" class="btn primary">조회</button>  <!-- ❌ -->
```

### ✅ 올바른 방법
```html
<!-- type="button" 명시 필수 -->
<button type="button" id="btnApplyFilter" class="btn primary">조회</button>  <!-- ✅ -->
<button type="button" id="btnExportCsv" class="btn">CSV 내보내기</button>
```

**원칙**:
- 모든 클릭 이벤트 버튼에 **`type="button"` 필수**
- `type` 없으면 기본값이 `type="submit"`이 되어 form 제출됨
- submit 의도가 아니면 반드시 명시

---

## 5. 탭 페이지 필수 설정

### ❌ 누락하면 안 됨
```javascript
// window.pageName 없으면 AJAX 호출 실패!
document.getElementById('btnApplyFilter').addEventListener('click', function() {
  const data = {};
  updateAjaxContent(data, callback, false);  // ❌ pageName 없음!
});
```

### ✅ 올바른 방법
```javascript
<script>
// 페이지 이름 (AJAX 호출용) - 필수!
window.pageName = '<?= encryptValue($today . '/perf_all') ?>';

// 필터 적용
document.getElementById('btnApplyFilter')?.addEventListener('click', function() {
  const data = {};
  if (startDate) data['<?= encryptValue('start_date') ?>'] = startDate;

  updateAjaxContent(data, callback, false);  // ✅ window.pageName 자동 사용
});
</script>
```

**원칙**:
- 탭 페이지 스크립트 최상단에 **`window.pageName` 필수 설정**
- `updateAjaxContent()`가 이 값을 자동으로 사용
- 형식: `encryptValue($today . '/페이지명')`

---

## 6. 코드 일관성 체크리스트

탭 페이지에서 조회 버튼 구현 시 **반드시 확인**:

- [ ] PHP: `$_POST`로 필터 파라미터 받기 (❌ `$_GET` 금지)
- [ ] HTML: 버튼에 `type="button"` 명시
- [ ] JS: `window.pageName` 설정
- [ ] JS: `updateAjaxContent()` 사용 (❌ `window.location.href` 금지)
- [ ] JS: 암호화 키는 PHP에서 미리 처리, 값은 평문 전달
- [ ] JS: PHP 함수 호출 금지 (❌ `encryptValue(value)`)

---

## 7. 자주 하는 실수 요약

| 실수 | 이유 | 해결 |
|------|------|------|
| `encryptValue(value)` in JS | PHP 함수는 서버에서만 실행됨 | PHP에서 미리 암호화된 키 사용 |
| `window.location.href` in 탭 | 페이지 전체 이동, 탭 구조 깨짐 | `updateAjaxContent()` 사용 |
| `$_GET` in 탭 페이지 | AJAX는 POST로 전달됨 | `$_POST` 사용 |
| `type` 없는 버튼 | form submit 동작 | `type="button"` 명시 |
| `window.pageName` 누락 | AJAX 호출 실패 | 스크립트 최상단에 설정 |

---

## 8. 완전한 예제 코드

### PHP (페이지 상단)
```php
<?php
/**
 * HQ 실적관리 > 전체 매출 실적
 */

// 필터 파라미터 (탭 페이지는 $_POST)
$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-01');
$endDate = isset($_POST['end_date']) ? $_POST['end_date'] : date('Y-m-t');

// SQL Injection 방지
$startDate = mysqli_real_escape_string($con, $startDate);
$endDate = mysqli_real_escape_string($con, $endDate);

// 데이터 조회...
?>
```

### HTML (필터 영역)
```html
<div class="filter-toolbar">
  <div class="filter-group">
    <label>시작일</label>
    <input type="date" id="startDate" value="<?= htmlspecialchars($startDate) ?>">
  </div>
  <div class="filter-group">
    <label>종료일</label>
    <input type="date" id="endDate" value="<?= htmlspecialchars($endDate) ?>">
  </div>
  <!-- type="button" 필수! -->
  <button type="button" id="btnApplyFilter" class="btn primary">조회</button>
</div>
```

### JavaScript (스크립트 영역)
```javascript
<script>
// 페이지 이름 설정 (필수!)
window.pageName = '<?= encryptValue($today . '/perf_all') ?>';

// 필터 적용
document.getElementById('btnApplyFilter')?.addEventListener('click', function() {
  const startDate = document.getElementById('startDate').value;
  const endDate = document.getElementById('endDate').value;

  // 암호화된 키 사용, 값은 평문
  const data = {};
  if (startDate) data['<?= encryptValue('start_date') ?>'] = startDate;  // ✅
  if (endDate) data['<?= encryptValue('end_date') ?>'] = endDate;

  // updateAjaxContent 사용 (window.location.href 금지!)
  updateAjaxContent(data, function(response) {
    if (response.result === 'ok' && response.html) {
      const contentArea = document.querySelector('#perf-tab-content');
      if (contentArea) {
        contentArea.innerHTML = response.html;
        // 스크립트 재실행
        contentArea.querySelectorAll('script').forEach(function(oldScript) {
          const newScript = document.createElement('script');
          if (oldScript.src) {
            newScript.src = oldScript.src;
          } else {
            newScript.text = oldScript.text || oldScript.textContent || oldScript.innerHTML;
          }
          oldScript.parentNode.replaceChild(newScript, oldScript);
        });
      }
    }
  }, false);
});
</script>
```

---

## 9. 디버깅 팁

### 조회 버튼 클릭 시 홈으로 이동되는 경우
- `type="button"` 확인
- `window.location.href` 사용 여부 확인
- `updateAjaxContent()` 사용 확인

### "encryptValue is not defined" 오류
- JavaScript에서 `encryptValue(value)` 호출 확인
- PHP에서 미리 암호화된 키만 사용하도록 수정

### 필터가 적용되지 않는 경우
- `$_POST` vs `$_GET` 확인 (탭은 POST)
- `window.pageName` 설정 확인
- 암호화된 키 이름 확인

---

**중요**: 이 지침을 반드시 따르지 않으면 **같은 오류가 반복 발생**합니다!
