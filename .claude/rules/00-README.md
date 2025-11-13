# 디스펜서 영업 관리 시스템 — 개발 규칙 문서

> 모든 포털(HQ, 벤더, 고객, 루시드, 영업사원)에 적용되는 개발·운영 규칙 통합 문서

**기준 버전**: v2.0 (2025-11-12)

---

## 📚 문서 구조

이 문서 세트는 **모듈화된 규칙 문서**로 구성되어 있으며, 필요한 부분만 빠르게 참조할 수 있습니다.

```
.claude/rules/
├── 00-README.md                    # 이 파일 (문서 네비게이션)
├── 01-quick-start.md               # 빠른 시작 가이드
├── 02-architecture.md              # 프로젝트 구조 및 아키텍처
├── 03-development-guide.md         # 개발 가이드 (코딩 규약, 보안)
├── 04-api-reference.md             # API 참조 (암복호화, 함수)
├── 05-database.md                  # DB 스키마 및 규약
├── 06-ui-components.md             # UI 컴포넌트 & CSS 가이드
├── 07-business-policies.md         # 정책 및 계산식
└── 08-troubleshooting.md           # 문제 해결 & FAQ

프로젝트 루트:
└── schema.sql                   # 데이터베이스 스키마 정의 (모든 작업 시 필수 참조)
```

---

## 📖 문서별 상세 안내

### [01-quick-start.md](./01-quick-start.md)
**처음 시작하는 개발자를 위한 빠른 시작 가이드**
- 개발 환경 설정부터 첫 번째 페이지 작성까지 단계별 안내
- 최소한으로 알아야 할 핵심 규칙만 요약

### [02-architecture.md](./02-architecture.md)
**프로젝트 전체 아키텍처**
- 전체 디렉토리 구조 및 핵심 파일 역할
- 요청 흐름 상세 (페이지 로드, AJAX)
- 보안 및 인증 체계

### [03-coding-standards.md](./03-coding-standards.md)
**개발 가이드 및 코딩 규약**
- 코딩 규약 (응답 포맷, 트랜잭션, 검증)
- 보안 규칙 (암복호화, 입력값 검증)
- 페이징 시스템 (공통 페이징, GROUP BY 처리)
- 공통 기능 (CSV, 검색, 정렬, 팝업)

### [04-api-reference.md](./04-api-reference.md)
**API 참조 및 함수 라이브러리**
- 암복호화 함수 구현 및 사용법
- 공통 함수 라이브러리 (MySQLi, 날짜, 파일 등)
- JavaScript 유틸리티 함수

### [05-database.md](./05-database.md)
**DB 스키마 및 규약**
- 핵심 테이블 구조 및 Foreign Key 관리
- 식별자 생성 규칙 (VYYYYMMDDNNNN 등)
- 스키마 변경 워크플로우 및 커스텀 ID 생성 규칙
- 트랜잭션 사용법

### [06-ui-components.md](./06-ui-components.md)
**UI 컴포넌트 & CSS 가이드**
- CSS 로드 순서 및 공통 UI 요소
- 상태 배지 및 반응형 지원
- 페이지 레이아웃 템플릿

### [07-business-policies.md](./07-business-policies.md)
**정책 및 계산식**
- 구독료, 콘텐츠 가격
- 벤더 커미션/인센티브 (40%, 5%)
- 루시드 배분 (50%)
- 영업사원 인센티브 (판매, 리뉴얼)
- KPI 공식 (40%+25%+20%+15%)
- 시리얼 생성 규칙

### [08-troubleshooting.md](./08-troubleshooting.md)
**문제 해결 및 FAQ**
- 자주 발생하는 오류 패턴 및 해결 방법
- 버튼이 동작하지 않을 때 대처법
- DB/FK 오류 해결 가이드

---

## 🚀 상황별 읽기 순서

### 1️⃣ 처음 시작하는 개발자
**읽어야 할 순서**:
1. [01-quick-start.md](./01-quick-start.md) — 빠른 시작 가이드
2. [02-architecture.md](./02-architecture.md) — 전체 구조 이해
3. [03-development-guide.md](./03-development-guide.md) — 개발 규약 숙지

### 2️⃣ 특정 포털 개발 시
**예시: 벤더 포털 개발**
1. [01-quick-start.md](./01-quick-start.md) — 공통 규칙 확인
2. [03-development-guide.md](./03-development-guide.md) — 코딩 규약
3. [07-business-policies.md](./07-business-policies.md) — 커미션/인센티브 계산식

### 3️⃣ DB 작업 시
1. **[../../../schema.sql](../../../schema.sql)** — 실제 DB 스키마 정의 (필수)
2. [05-database.md](./05-database.md) — 스키마 규칙 및 FK 관리
3. [04-api-reference.md](./04-api-reference.md) — DB 헬퍼 함수

### 4️⃣ UI/UX 작업 시
1. [06-ui-components.md](./06-ui-components.md) — UI 공통 규칙
2. [03-development-guide.md](./03-development-guide.md) — 상태 배지, CSS 로드 순서

### 5️⃣ 오류 발생 시
1. [08-troubleshooting.md](./08-troubleshooting.md) — 문제 해결 가이드
2. 해당 주제별 문서 참조

---

## 🔍 주제별 빠른 찾기

### 파일 구조 확인
→ [02-architecture.md](./02-architecture.md) 섹션 0

### DB 테이블 구조
→ **[../../../schema.sql](../../../schema.sql)** (실제 스키마 정의)
→ [05-database.md](./05-database.md) 섹션 1 (규칙 및 가이드)

### 암복호화 사용법
→ [04-api-reference.md](./04-api-reference.md) 섹션 "암복호화 함수"

### 메뉴 ID 규칙
→ [01-quick-start.md](./01-quick-start.md) 섹션 "메뉴 ID 규칙"

### 상태 배지
→ [06-ui-components.md](./06-ui-components.md) 섹션 "공통 상태 정의"

### 커미션/인센티브 계산
→ [07-business-policies.md](./07-business-policies.md) 섹션 "벤더 정책" / "영업사원 인센티브"

### KPI 공식
→ [07-business-policies.md](./07-business-policies.md) 섹션 "KPI 공식"

### 페이징 시스템
→ [03-coding-standards.md](./03-coding-standards.md) 섹션 "페이징 시스템"
→ `docs/PAGINATION_IMPLEMENTATION.md` (상세 구현 가이드)

### 문제 해결
→ [08-troubleshooting.md](./08-troubleshooting.md)

---

## 💡 개발 프로세스

### 신규 페이지 개발 시

1. **준비**:
   - [01-quick-start.md](./01-quick-start.md) 읽기
   - 해당 포털의 정책 확인 ([07-business-policies.md](./07-business-policies.md))

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
   - [03-development-guide.md](./03-development-guide.md) 하단 "코드 작성 체크리스트"

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

## 🔄 문서 업데이트 정책

### 문서 수정 시
1. 해당 `.md` 파일만 수정
2. 마지막 업데이트 날짜 기록
3. 변경사항을 Git 커밋 메시지에 명시

### 새로운 규칙 추가 시
1. 적절한 문서에 추가
2. [01-quick-start.md](./01-quick-start.md)에 요약 추가 (핵심이면)
3. 00-README.md (이 파일)의 "주제별 빠른 찾기" 업데이트

---

## 🎯 문서 활용 팁

### Claude Code 사용 시
```
사용자: "벤더 포털 정산 기능 만들어줘"
Claude: [자동으로 관련 문서 읽고 작업]
```

### 명시적 참조
```
사용자: ".claude/rules/07-business-policies.md 규칙에 따라 정산 탭 만들어"
```

### 빠른 질문
```
사용자: "01-quick-start.md에서 커미션 비율 알려줘"
```

---

## 📞 문의 및 피드백

문서 오류, 개선 제안, 질문이 있으면:
- GitHub Issue 등록
- 팀 채팅 채널에 공유
- 담당자에게 직접 문의

---

## 📝 버전 히스토리

### v2.0 (2025-11-12)
**Phase 2: 문서 구조 개편**
- README.md → 00-README.md로 변경
- FAQ 섹션 제거 (08-troubleshooting.md로 이동)
- 상세 예시 코드 제거
- 새로운 파일 구조 (00~08) 소개로 업데이트
- 각 파일의 용도를 1~2문장으로 간결하게 설명
- 상황별 읽기 순서 업데이트

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

**개선 사항**:
- 코드 구조 단순화 (별도 API 파일 불필요)
- 페이지별 독립성 향상
- 파일 관리 용이성 증대

### v1.1 (2025-11-06)
**초기 통합 지침 작성**:
- 프로젝트 구조 및 아키텍처 정의
- 포털별 상세 규칙 수립
- 공통 규약 및 보안 정책 수립

---

**마지막 업데이트**: 2025-11-12 (v2.0)

**문서 작성자**: Claude Code Team
