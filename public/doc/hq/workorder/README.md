# 작업지시서 PDF/프린트 시스템

작업지시서를 HTML 프린트 뷰로 출력하는 시스템입니다.

## 📁 파일 구조

```
public/doc/hq/workorder/
├── wo_list.php           # 작업지시서 목록 및 관리
├── wo_print_simple.php   # HTML 프린트 뷰 (권장)
├── wo_print.php          # PDF 출력 (mpdf 필요)
└── README.md             # 본 문서
```

## 🚀 사용 방법

### 1. 작업지시서 목록 페이지

**URL**: `/doc/hq/workorder/wo_list.php`

**기능**:
- 작업지시서 목록 조회
- 필터: 상태, 항목타입, 검색어
- "출력" 버튼 클릭 → 프린트 뷰 새 창 열기
- CSV 내보내기

### 2. HTML 프린트 뷰 (권장)

**URL**: `/doc/hq/workorder/wo_print_simple.php?id=[작업지시서ID]`

**특징**:
- 외부 라이브러리 불필요
- 브라우저 프린트 기능 사용 (Ctrl+P 또는 화면 상단 프린트 버튼)
- A4 용지 최적화
- 화면 및 인쇄 스타일 분리

**사용법**:
1. 새 창이 열리면 "🖨️ 프린트" 버튼 클릭 또는 Ctrl+P
2. 프린터 선택 후 인쇄
3. PDF로 저장하려면 프린터 대신 "PDF로 저장" 선택

### 3. PDF 출력 (mpdf 사용)

**URL**: `/doc/hq/workorder/wo_print.php?id=[작업지시서ID]`

**특징**:
- 서버에서 PDF 파일 생성
- 저장 경로: `/files/workorder/YY/WOYYYYMMDDNNNN.pdf`
- mpdf 라이브러리 필요

**설치 (mpdf 사용 시)**:
```bash
cd /path/to/dispenser
composer require mpdf/mpdf
```

## 📋 작업지시서 번호 규칙

```
형식: WO + YYYYMMDD + NNNN

예시:
WO202511070001 - 2025년 11월 7일 첫 번째 작업지시서
WO202511070002 - 2025년 11월 7일 두 번째 작업지시서
```

## 💾 파일 저장 경로

PDF 파일 (wo_print.php 사용 시):
```
files/
└── workorder/
    ├── 25/                    # 2025년
    │   ├── WO202511070001.pdf
    │   ├── WO202511070002.pdf
    │   └── ...
    └── 26/                    # 2026년
        └── ...
```

## 🗄️ 데이터베이스

### work_orders 테이블에 pdf_path 컬럼 추가

```sql
-- 마이그레이션 실행
mysql -u root -p dispenser < migrations/add_pdf_path_to_work_orders.sql
```

또는 직접 실행:
```sql
ALTER TABLE work_orders
ADD COLUMN IF NOT EXISTS pdf_path VARCHAR(255) DEFAULT NULL COMMENT 'PDF 파일 경로';

CREATE INDEX IF NOT EXISTS idx_pdf_path ON work_orders(pdf_path);
```

## 🎨 프린트 템플릿 구성

작업지시서에 포함된 정보:
- **헤더**: 작업지시서 제목, 번호, 발행일자
- **고객 정보**: 고객명, ID, 연락처, 이메일, 배송주소
- **작업 내용**: 항목구분, 품목명, 수량
- **특이사항**: 비고/메모
- **서명란**: 작성자/확인자 서명
- **푸터**: 발행일시, 시스템 정보

## 🔧 커스터마이징

### 템플릿 수정

`wo_print_simple.php`의 HTML/CSS 섹션을 수정:

```php
// 스타일 변경
<style>
    .header h1 {
        color: #1976d2;  // 헤더 색상 변경
        font-size: 28pt; // 폰트 크기 조정
    }
    // ... 기타 스타일
</style>

// HTML 구조 변경
<div class="info-section">
    // 섹션 추가/제거/수정
</div>
```

### 자동 프린트

페이지 로드 시 자동으로 프린트 대화상자 표시:

```javascript
// wo_print_simple.php 하단 스크립트 주석 해제
window.addEventListener('load', function() {
    setTimeout(function() {
        window.print();
    }, 500);
});
```

## ⚙️ 시스템 요구사항

### HTML 프린트 뷰 (wo_print_simple.php)
- PHP 7.4 이상
- 모던 브라우저 (Chrome, Firefox, Edge, Safari)

### PDF 출력 (wo_print.php)
- PHP 7.4 이상
- Composer
- mpdf/mpdf ^8.2
- GD 또는 Imagick 확장 (이미지 처리 시)

## 📝 사용 예시

### PHP에서 호출

```php
// 작업지시서 프린트 페이지로 리다이렉트
$workOrderId = 123;
header("Location: /doc/hq/workorder/wo_print_simple.php?id=" . $workOrderId);
exit;
```

### JavaScript에서 호출

```javascript
// 새 창에서 프린트 뷰 열기
function printWorkOrder(workOrderId) {
    const url = '/doc/hq/workorder/wo_print_simple.php?id=' + workOrderId;
    window.open(url, '_blank', 'width=1000,height=900,scrollbars=yes');
}

// 사용
printWorkOrder(123);
```

## 🐛 트러블슈팅

### 프린트 시 레이아웃이 깨짐
- 브라우저 프린트 설정에서 "배경 그래픽" 옵션 활성화
- 용지 크기를 A4로 설정
- 여백을 "기본값" 또는 "없음"으로 설정

### PDF 저장이 안됨
1. `files/workorder` 디렉토리 생성 확인
2. 디렉토리 쓰기 권한 확인 (755 이상)
3. PHP error_log 확인

### mpdf 오류
```bash
# Composer 재설치
composer install --no-dev

# mpdf 단독 설치
composer require mpdf/mpdf
```

## 📞 지원

문제 발생 시:
1. PHP error_log 확인
2. 브라우저 개발자 도구 콘솔 확인
3. 데이터베이스 연결 확인

## 📄 라이선스

본 시스템은 Dispenser 프로젝트의 일부입니다.
