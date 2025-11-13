# 고객 포털 규칙

> 고객 포털의 탭 구조, 구독 정책, 기기/콘텐츠/향 관리

---

## 🎯 포털 개요

**역할**: 고객은 디스펜서 기기를 설치하고 구독 서비스를 이용하며, 콘텐츠와 향을 관리

**디렉토리**: `doc/customer/`

**메뉴 ID**: `C01`, `C02`, `C03` ...

---

## 📑 탭 구조 및 기능

### 1. 대시보드 (dashboard.php)
**메뉴 ID**: C01

**전체 현황 요약**:
- 전체 구독 수
- 남은 무료 프린팅
- 배송 예정일
- 만료 예정일

**상태별 배지**: ACTIVE / WARNING / GRACE / TERMINATED

**자동 계산**:
```javascript
// 다음 배송 예정일
nextDeliveryDate = installDate + 2개월;

// 무료 잔여 프린팅 횟수
remainingFreePrinting = 6 - usedCount;
```

---

### 2. 기기관리 (device_mgmt.php)
**메뉴 ID**: C02

**표시 항목**:
- 기기명, 설치일, 장소
- 시리얼 (HQ 발급 기준: YYWW+LOT)
- 향, 콘텐츠
- 다음 배송일
- 무료 잔여 프린팅

**시리얼 관리**:
- HQ 발급 자동 연동
- 누락 시 고객 직접 입력 가능

**향 공급 규칙**:
- 설치일 기준 **2개월마다** 자동 공급
- 무상 6종 제공
- 첫 선택 1개, 나머지 랜덤

---

### 3. 콘텐츠 라이브러리 (content_lib.php)
**메뉴 ID**: C03

**표시 형식**: 썸네일 리스트형

**구분**:
- **상단**: 무상 콘텐츠 (6개)
- **하단**: 유료 콘텐츠

**신규 콘텐츠**: 30일간 "NEW" 배지

**유료 콘텐츠 가격**:
- Basic: 11,000원
- Standard: 22,000원
- Deluxe: 110,000원
- Premium: 220,000원

**기능**:
- 고객이 직접 신청·변경 가능
- HQ 승인 후 반영

---

### 4. 향 라이브러리 (scent_lib.php)
**메뉴 ID**: C04

**표시 항목**:
- 향 목록 및 카트리지 상태
- 잔여 개수
- 배송 예정일
- 향 변경 신청 기능

**공급 규칙**:
- **2개월 주기** 자동 공급
- 신규 향 "NEW" 배지 (30일 내 등록)

---

### 5. 주문 히스토리 (billing.php)
**메뉴 ID**: C05

**주문 내역**: 향/콘텐츠/기기/프린팅

**표시 항목**:
- 결제일
- 배송상태
- 금액
- 상태

**상태 흐름**:
```
REQUESTED → CONFIRMED → SHIPPED → DELIVERED
```

**기능**: CSV 내보내기

---

### 6. 결제/구독 (subscription.php)
**메뉴 ID**: C06

**정기구독료**: 29,700원/월
- 기기 포함
- 오일 6개/년
- 무료 프린팅 6회

**결제 상태**: PLANNED → DUE → PAID

**구독 상태**: ACTIVE / WARNING / GRACE / TERMINATED

**결제 수단**: 카드 자동결제

**결제 실패 시**: GRACE 전환

**자동 계산 항목**:
```javascript
// 다음 결제일
nextPaymentDate = lastPaymentDate + 1개월;

// 남은 기간
remainingDays = Math.floor((nextPaymentDate - today) / (1000 * 60 * 60 * 24));

// 남은 프린팅 횟수
remainingPrinting = 6 - usedCount;
```

**구독 해지**: 미도래 분 자동 소멸

---

### 7. 도움 (help.php)
**메뉴 ID**: C07

**기능**:
- FAQ
- 1:1 문의
- 정책 안내

**고객 요청 시**: HQ 티켓 자동 생성 (OPEN 상태)

**처리 진행상황**: 답변 조회 가능

---

## 💰 정책 및 가격

### 구독 정책
- **정기구독료**: 29,700원/월
- **향 공급 주기**: 2개월
- **무료 프린팅**: 연 6회

### 콘텐츠 단가
| 등급 | 가격 |
|------|------|
| Basic | 11,000원 |
| Standard | 22,000원 |
| Deluxe | 110,000원 |
| Premium | 220,000원 |

### 특별 규칙
- **루시드 협업 콘텐츠**: 고객 수정 요청 시만 루시드 배분 (50%)

---

## 📐 자동 계산 공식

```javascript
// 1. 남은 무료 프린팅
remainingFreePrinting = 6 - usedCount;

// 2. 다음 배송일 (설치일 기준 2개월 주기)
nextDeliveryDate = new Date(installDate);
nextDeliveryDate.setMonth(nextDeliveryDate.getMonth() + 2);

// 3. 구독 남은 기간
remainingDays = Math.floor((nextPaymentDate - today) / (1000 * 60 * 60 * 24));

// 4. 결제 상태 전환 (결제 실패 시)
if (paymentFailed) {
  subscriptionStatus = 'GRACE';
  // 유예 기간 후에도 미결제 시
  if (gracePeriodExpired) {
    subscriptionStatus = 'TERMINATED';
  }
}

// 5. 콘텐츠 가격 (정책 기준 자동 반영)
contentPrice = getPriceFromPolicy(contentTier); // policy_appendix.txt 참조
```

---

## 🏷️ 상태 배지

### 구독 상태
- **ACTIVE**: 초록
- **WARNING**: 노랑
- **GRACE**: 주황
- **TERMINATED**: 회색

### 신규 항목 표시
- 신규 항목은 30일간 "NEW" 배지 표시
- 계산식: `(오늘 - 등록일) ≤ 30일`

---

## 🔓 권한 및 접근

| 구분 | 권한 내용 |
|------|------------|
| 고객 | 본인 계정 및 사업장 데이터만 접근 가능 |
| HQ | 고객 데이터 전체 조회 가능 |
| 벤더/루시드/영업사원 | 고객 포털 접근 불가 |

---

## 🛠️ 개발 체크리스트

### 고객 포털 개발 시
- [ ] 구독료 29,700원 정확히 적용
- [ ] 향 공급 주기 2개월 자동 계산
- [ ] 무료 프린팅 6회 제한 확인
- [ ] 신규 항목 30일간 "NEW" 배지
- [ ] 본인 데이터만 조회 (권한 체크)
- [ ] 결제 실패 시 GRACE → TERMINATED 자동 전환
- [ ] 콘텐츠 가격 정책 연동
- [ ] 더미데이터 20건 이상

### 고객 직접 변경 가능 항목
- [ ] 콘텐츠 선택·변경
- [ ] 향 선택·변경
- [ ] 모든 변경사항 로그 자동 기록

---

**마지막 업데이트**: 2025-11-06 (v1.1)
