# 디스펜서 영업 관리 시스템 — 개발 규칙 문서

> 모든 포털(HQ, 벤더, 고객, 루시드, 영업사원)에 적용되는 개발·운영 규칙 통합 문서

**기준 버전**: v1.3 (2025-11-08)

---

## 📚 문서 구조

이 문서 세트는 **모듈화된 규칙 문서**로 구성되어 있으며, 필요한 부분만 빠르게 참조할 수 있습니다.

```
.claude/rules/
├── README.md                    # 이 파일 (문서 네비게이션)
├── quick-reference.md           # ⭐ 빠른 참조 (가장 자주 사용)
├── architecture.md              # 프로젝트 구조 및 아키텍처
├── database.md                  # DB 스키마 및 규약
├── common-rules.md              # 공통 코딩 규약 & UI 가이드
├── policies.md                  # 정책 및 계산식 (가격/커미션/인센티브/KPI)
└── portals/                     # 포털별 상세 규칙
    ├── vendor.md                # 벤더 포털
    ├── customer.md              # 고객 포털
    ├── lucid.md                 # 루시드 포털
    └── sales.md                 # 영업사원 포털

프로젝트 루트:
└── schema.sql                   # ⭐ 데이터베이스 스키마 정의 (모든 작업 시 필수 참조)
```

---

## 🚀 빠른 시작

### 1️⃣ 처음 시작하는 개발자

**읽어야 할 순서**:
1. [quick-reference.md](./quick-reference.md) — 핵심 규칙 요약
2. [architecture.md](./architecture.md) — 전체 구조 이해
3. 해당 포털 문서 (예: [portals/vendor.md](./portals/vendor.md))

### 2️⃣ 특정 포털 개발 시

**예시: 벤더 포털 개발**
1. [quick-reference.md](./quick-reference.md) — 공통 규칙 확인
2. [portals/vendor.md](./portals/vendor.md) — 벤더 포털 규칙
3. [policies.md](./policies.md) — 커미션/인센티브 계산식

### 3️⃣ DB 작업 시

1. **[schema.sql](../schema.sql)** — 실제 DB 스키마 정의 (필수)
2. [database.md](./database.md) — 스키마 및 FK 규칙
3. [quick-reference.md](./quick-reference.md) — 필수 함수

### 4️⃣ UI/UX 작업 시

1. [common-rules.md](./common-rules.md) — UI 공통 규칙
2. [quick-reference.md](./quick-reference.md) — 상태 배지, CSS 로드 순서

---

## 📖 문서별 상세 안내

### ⭐ [quick-reference.md](./quick-reference.md)
**가장 자주 참조하는 문서**

**내용**:
- 핵심 파일 구조
- 필수 함수 & 변수
- 요청 흐름
- 표준 응답 포맷
- 주요 정책 값
- 개발 체크리스트

**사용 시기**: 개발 중 수시로 참조

---

### 🏗️ [architecture.md](./architecture.md)
**프로젝트 전체 아키텍처**

**내용**:
- 전체 디렉토리 구조
- 핵심 파일 역할 (index.php, _ajax_.php, common.php 등)
- 요청 흐름 상세 (페이지 로드, AJAX)
- 보안 및 인증 체계
- CSS 및 UI 구조

**사용 시기**: 신규 입문, 구조 파악 필요 시

---

### 🗄️ [database.md](./database.md)
**DB 스키마 및 규약**

**내용**:
- 핵심 테이블 구조 (users, vendors, customers 등)
- 식별자 생성 규칙 (VYYYYMMDDNNNN 등)
- Foreign Key 관리
- 스키마 호환성 전략
- 트랜잭션 사용법

**사용 시기**: DB 작업, 테이블 생성/수정 시

**중요**: 실제 DB 스키마는 **[schema.sql](../schema.sql)** 파일을 참조하세요!

---

### 📏 [common-rules.md](./common-rules.md)
**공통 코딩 규약 & UI 가이드**

**내용**:
- 코딩 규약 (응답 포맷, 트랜잭션, 검증)
- 보안 규칙 (암복호화, 입력값 검증)
- UI/UX 공통 규칙 (CSS 로드, 반응형)
- 상태 정의 (계약, 지급, 티켓, 작업지시)
- 공통 기능 (CSV, 검색, 정렬, 팝업)

**사용 시기**: 모든 페이지 개발 시 필수 참조

---

### 💰 [policies.md](./policies.md)
**정책 및 계산식**

**내용**:
- 구독료 (29,700원/월)
- 콘텐츠 가격 (Basic~Premium)
- 벤더 커미션/인센티브 (40%, 5%)
- 루시드 배분 (50%)
- 영업사원 인센티브 (판매, 리뉴얼)
- KPI 공식 (40%+25%+20%+15%)
- 시리얼 생성 규칙

**사용 시기**: 금액 계산, 정책 적용 시

---

### 🏢 포털별 문서

#### [portals/vendor.md](./portals/vendor.md)
**벤더 포털 규칙**
- 탭 구조 (대시보드, 고객관리, 작업지시서, 정산 등)
- 커미션/인센티브 정책
- 작업지시서 조회 전용 규칙

#### [portals/customer.md](./portals/customer.md)
**고객 포털 규칙**
- 탭 구조 (기기관리, 콘텐츠/향 라이브러리, 결제/구독 등)
- 구독 정책 (29,700원/월)
- 향 공급 규칙 (2개월 주기)

#### [portals/lucid.md](./portals/lucid.md)
**루시드 포털 규칙**
- 탭 구조 (콘텐츠 등록, 수정요청, 정산 등)
- 배분 정책 (50%, 고객 수정 요청 건만)
- 신규 배지 규칙 (30일)

#### [portals/sales.md](./portals/sales.md)
**영업사원 포털 규칙**
- 탭 구조 (고객관리, 리뉴얼, 인센티브, KPI 등)
- 인센티브 정책 (판매 90,000원 분할, 리뉴얼 30,000~40,000원)
- KPI 공식 (40%+25%+20%+15%)

---

## 🔍 주제별 빠른 찾기

### 파일 구조 확인
→ [architecture.md](./architecture.md) 섹션 0

### DB 테이블 구조
→ **[schema.sql](../schema.sql)** (실제 스키마 정의)
→ [database.md](./database.md) 섹션 1 (규칙 및 가이드)

### AJAX 요청 처리 규칙 ⭐ 업데이트 (v1.3)
→ **[../public/지침.txt](../../public/지침.txt)** 섹션 0.3
→ 모든 AJAX 요청은 페이지 내부에서 처리
→ 별도 API 파일 생성 금지
→ `url: window.location.pathname` 패턴 사용
→ 암호화 키 사용 주의사항

### 암복호화 사용법
→ [quick-reference.md](./quick-reference.md) 섹션 "필수 함수"

### 메뉴 ID 규칙
→ [quick-reference.md](./quick-reference.md) 섹션 "메뉴 ID 규칙"

### 상태 배지
→ [common-rules.md](./common-rules.md) 섹션 "공통 상태 정의"

### 커미션/인센티브 계산
→ [policies.md](./policies.md) 섹션 "벤더 정책" / "영업사원 인센티브"

### KPI 공식
→ [policies.md](./policies.md) 섹션 "KPI 공식"

### 포털별 탭 구조
→ [portals/*.md](./portals/) 각 포털 문서

---

## 💡 개발 프로세스

### 신규 페이지 개발 시

1. **준비**:
   - [quick-reference.md](./quick-reference.md) 읽기
   - 해당 포털 문서 확인 (예: [portals/vendor.md](./portals/vendor.md))

2. **개발**:
   - `inc/common.php` 로드
   - 표준 응답 포맷 사용 (`Finish()`)
   - 암복호화 적용
   - 더미데이터 20건 이상

3. **검증**:
   - 문법 오류 체크
   - 계산식 정책 기준 확인
   - 권한 체크
   - AJAX 동작 확인

4. **체크리스트**:
   - [common-rules.md](./common-rules.md) 하단 "코드 작성 체크리스트"

---

## 📋 개발 체크리스트

### 공통 (모든 페이지)
- [ ] `inc/common.php` 로드
- [ ] 암복호화 처리 적용
- [ ] 표준 응답 포맷 (`Finish()`)
- [ ] 더미데이터 20건 이상
- [ ] CSV 내보내기
- [ ] 상태 배지
- [ ] 공통 CSS 로드

### 벤더 포털
- [ ] 커미션 40%, 인센티브 5%
- [ ] 작업지시서 조회 전용
- [ ] 정산 지급일 익월 15일

### 고객 포털
- [ ] 구독료 29,700원
- [ ] 향 공급 2개월 주기
- [ ] 무료 프린팅 6회

### 루시드 포털
- [ ] 배분율 50%
- [ ] 고객 수정 요청 건만 배분
- [ ] 신규 30일간 "NEW" 배지

### 영업사원 포털
- [ ] 판매 인센티브 15,000원 × 6회
- [ ] 리뉴얼 30,000원/40,000원
- [ ] KPI 공식 (40%+25%+20%+15%)

---

## 🆘 문제 해결

### Q1. 어떤 파일을 먼저 읽어야 하나요?
**A**: [quick-reference.md](./quick-reference.md)를 먼저 읽으세요. 핵심 규칙이 모두 요약되어 있습니다.

### Q2. 특정 포털 개발 시 어떤 문서를 봐야 하나요?
**A**: `portals/` 폴더의 해당 포털 문서를 보세요.
- 벤더: [portals/vendor.md](./portals/vendor.md)
- 고객: [portals/customer.md](./portals/customer.md)
- 루시드: [portals/lucid.md](./portals/lucid.md)
- 영업사원: [portals/sales.md](./portals/sales.md)

### Q3. 계산식이 맞는지 확인하려면?
**A**: [policies.md](./policies.md)를 참조하세요. 모든 금액/계산 정책이 정리되어 있습니다.

### Q4. DB 작업 중 FK 오류가 발생해요
**A**: [database.md](./database.md)의 "Foreign Key 관리" 섹션을 확인하세요.

### Q5. 상태 배지 색상을 모르겠어요
**A**: [common-rules.md](./common-rules.md)의 "공통 상태 정의" 섹션을 확인하세요.

### Q6. AJAX 요청은 어떻게 처리하나요? ⭐ 업데이트 (v1.3)
**A**: **모든 AJAX 요청은 페이지 내부에서 처리합니다.** 별도의 API 파일을 만들지 않습니다.

**페이지 구조**:
```php
<?php
// AJAX 요청 처리 (페이지 최상단)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST[encryptValue('action')])) {
    header('Content-Type: application/json; charset=utf-8');

    $action = $_POST[encryptValue('action')];

    switch ($action) {
        case 'get_item':
            // 처리 로직
            echo json_encode(['result' => true, 'data' => $row]);
            exit;
        // ... 기타 액션
    }
}

// 일반 페이지 로드 로직
// ... HTML 렌더링
?>
```

**AJAX 호출**:
```javascript
$.ajax({
    type: 'POST',
    url: window.location.pathname,  // 현재 페이지 자신에게 요청
    data: data,
    dataType: 'json'
});
```

자세한 내용은 [../public/지침.txt](../../public/지침.txt) 섹션 0.3 참조

### Q7. 버튼이 동작하지 않아요 ⭐ 신규 (v1.2)
**A**: 다음을 확인하세요:
1. **암호화 키 사용 오류**: 액션 값은 문자열 그대로 전달
   ```javascript
   // ❌ 잘못됨
   data['<?= encryptValue('action') ?>'] = '<?= encryptValue('get_item') ?>';

   // ✅ 올바름
   data['<?= encryptValue('action') ?>'] = 'get_item';
   ```
2. **AJAX URL**: `url: window.location.pathname` 사용 (~~API 파일 경로 아님~~)
3. **모달 CSS**: 모달이 표시되지 않으면 CSS가 누락된 것
4. **exit 누락**: AJAX 응답 후 반드시 `exit` 호출

---

## 🔄 문서 업데이트 정책

### 문서 수정 시
1. 해당 `.md` 파일만 수정
2. 마지막 업데이트 날짜 기록
3. 변경사항을 Git 커밋 메시지에 명시

### 새로운 규칙 추가 시
1. 적절한 문서에 추가 (공통이면 [common-rules.md](./common-rules.md))
2. [quick-reference.md](./quick-reference.md)에 요약 추가 (핵심이면)
3. README.md (이 파일)의 "주제별 빠른 찾기" 업데이트

---

## 🎯 문서 활용 팁

### Claude Code 사용 시
```
사용자: "벤더 포털 정산 기능 만들어줘"
Claude: [자동으로 portals/vendor.md와 policies.md 읽고 작업]
```

### 명시적 참조
```
사용자: ".claude/rules/portals/vendor.md 규칙에 따라 정산 탭 만들어"
```

### 빠른 질문
```
사용자: "quick-reference.md에서 커미션 비율 알려줘"
```

---

## 📞 문의 및 피드백

문서 오류, 개선 제안, 질문이 있으면:
- GitHub Issue 등록
- 팀 채팅 채널에 공유
- 담당자에게 직접 문의

---

## 📝 버전 히스토리

### v1.3 (2025-11-08)
**아키텍처 변경**:
- AJAX 요청 처리 규칙 전면 개편
- 모든 AJAX 로직을 페이지 내부로 통합
- 별도 API 파일 생성 금지
- `url: window.location.pathname` 패턴 사용

**삭제된 디렉토리/파일**:
- `public/api/` 디렉토리 전체 삭제
- `public/api/vendor_api.php` 삭제
- `public/api/sales_rep_api.php` 삭제

**수정된 파일**:
- `public/doc/hq/vendor_mgmt.php`: 페이지 내 AJAX 처리 통합
- `public/doc/hq/sales_rep_mgmt.php`: 페이지 내 AJAX 처리 통합
- `public/지침.txt`: 섹션 0.3 전면 개편 (v1.2 → v1.3)

**개선 사항**:
- 코드 구조 단순화 (별도 API 파일 불필요)
- 페이지별 독립성 향상
- 파일 관리 용이성 증대

### v1.2 (2025-11-08)
**추가된 내용 (이후 v1.3에서 철회됨)**:
- API 파일 사용 규칙 추가 (→ v1.3에서 페이지 내 처리로 변경)
- 암호화 키 사용 주의사항
- 버튼 동작 트러블슈팅 가이드

### v1.1 (2025-11-06)
**초기 통합 지침 작성**:
- 프로젝트 구조 및 아키텍처 정의
- 포털별 상세 규칙 수립
- 공통 규약 및 보안 정책 수립

---

**마지막 업데이트**: 2025-11-08 (v1.3)

**문서 작성자**: Claude Code Team
