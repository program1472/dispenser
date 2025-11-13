# 정책 및 계산식

> 모든 포털에서 사용되는 가격, 커미션, 인센티브, KPI 정책

---

## 💰 기본 금액 정책

### 정기 구독료
- **29,700원/월**
- 포함 내역:
  - 기기 렌탈
  - 오일 6개/년 (2개월마다 자동 공급)
  - 무료 프린팅 6회/년

---

## 📦 콘텐츠 가격

| 등급 | 가격 |
|------|------|
| Basic | 11,000원 |
| Standard | 22,000원 |
| Deluxe | 110,000원 |
| Premium | 220,000원 |

**적용**: 모든 포털 공통 (고객, 루시드, 벤더, HQ)

---

## 🏢 벤더 정책

### 커미션
- **비율**: 유료 매출 × 40%
- **대상**: 구독료, 유료 콘텐츠, 향 추가 구매 등
- **제외**: 무료 서비스, 프로모션, 할인 금액

```php
$vendorCommission = $paidSales * 0.40;
```

### 인센티브
- **비율**: 유료 매출 × 5%
- **대상**: 유료 매출 전체
- **조건**: 목표 달성 또는 특정 실적 기준 충족 시

```php
$vendorIncentive = $paidSales * 0.05;
```

### 지급 정책
- **지급일**: 익월 15일
- **지급 상태**: PLANNED → DUE → PAID
- **정산 기준**: 전월 1일~말일 완료된 결제 건
- **지급 방식**: 계좌 이체

### 예시 계산
```
전월 유료 매출: 10,000,000원
커미션 (40%): 4,000,000원
인센티브 (5%): 500,000원
총 지급액: 4,500,000원
지급일: 익월 15일
```

---

## 🎨 루시드 정책

### 배분율
- **기본 배분율**: 콘텐츠 단가 × 50%
- **조건**: 고객이 수정 요청한 경우에만 배분
- **제외**: 단순 프린트/복제 요청

```php
$lucidShare = $contentPrice * 0.50;
```

### 정산 대상 판정
```php
$isEligible = (
    $isCollaboration === true &&
    $hasEditRequest === true &&
    $status === 'DONE' &&
    $month === $previousMonth
);
```

### 지급 정책
- **지급일**: 익월 15일
- **지급 상태**: PLANNED → DUE → PAID
- **정산 기준**: 전월 DONE 완료 건

**참고**: 배분율은 HQ 정책센터에서 변경 가능

---

## 👔 영업사원 인센티브

### 판매 인센티브
- **총액**: 90,000원/대
- **지급 방식**: 15,000원 × 6회 분할 지급
- **조건**: 계약 유지 시에만 지급
- **해지 시**: 잔여 미도래분 자동 소멸
- **지급일**: 익월 15일

```php
// 월별 분할 지급액
$monthlySalesIncentive = $salesCount * 15000;

// 총 지급액 (6개월)
$totalSalesIncentive = 90000;
```

### 리뉴얼 인센티브
- **기본**: 30,000원
- **연속 리뉴얼**: 40,000원
- **지급 시기**: 결제 확인 후 익월 15일 지급

```php
$renewalIncentive = $isConsecutive ? 40000 : 30000;
```

### 예상 수입 계산
```php
$monthlyExpectedIncome =
    ($salesCount * 15000) +           // 분할 예정
    ($renewalCount * $renewalAmount) + // 리뉴얼 예정
    $activityFee;                      // 활동비
```

---

## 📊 KPI 공식 (영업사원)

### KPI 구성 비율
```
KPI = 판매(40%) + 유지(25%) + 리뉴얼(20%) + 보고(15%)
```

### 세부 계산식

**판매율**:
```javascript
salesRate = (thisMonthNewSales / targetSales) * 100;
```

**유지율**:
```javascript
retentionRate = (activeCustomers / totalCustomers) * 100;
```

**리뉴얼율**:
```javascript
renewalRate = (renewedCustomers / renewalTargets) * 100;
```

**보고율**:
```javascript
reportRate = (submittedReports / targetReports) * 100;
```

### KPI 점수 계산
```javascript
KPI점수 = (
    (salesRate * 0.4) +
    (retentionRate * 0.25) +
    (renewalRate * 0.2) +
    (reportRate * 0.15)
) * 100;
```

### KPI 배지 기준
- **80% 이상**: ACHIEVED (초록)
- **60~79%**: NORMAL (파랑)
- **60% 미만**: WARNING (노랑)

---

## 🔄 향 공급 정책

### 공급 주기
- **2개월마다** 자동 공급

### 공급 내역
- 무상 6종 제공
- 첫 선택 1개
- 나머지 랜덤

### 다음 배송일 계산
```javascript
nextDeliveryDate = new Date(installDate);
nextDeliveryDate.setMonth(nextDeliveryDate.getMonth() + 2);
```

---

## 🖨️ 무료 프린팅 정책

### 연간 무료 횟수
- **6회/년** (구독 포함)

### 잔여 횟수 계산
```javascript
remainingFreePrinting = 6 - usedCount;
```

---

## 🔢 시리얼 생성 규칙

### 형식
```
YYWW + LOT + 범위
```

### 예시
```
2511 + A + 0001~0100 → 2511A0001 ~ 2511A0100
```

### 생성 로직 (PHP)
```php
function generateSerial($lotNumber, $rangeStart, $rangeEnd) {
    $year = date('y');
    $week = date('W');
    $prefix = $year . $week . $lotNumber;

    $serials = [];
    for ($i = $rangeStart; $i <= $rangeEnd; $i++) {
        $serials[] = $prefix . str_pad($i, 4, '0', STR_PAD_LEFT);
    }

    return $serials;
}

// 사용 예시
$serials = generateSerial('A', 1, 100); // 2511A0001 ~ 2511A0100
```

---

## 📅 지급일 자동 계산

### 공통 지급일
- **익월 15일**

### 계산 로직 (JavaScript)
```javascript
function calculatePaymentDate(settlementMonth) {
    const paymentDate = new Date(settlementMonth);
    paymentDate.setMonth(paymentDate.getMonth() + 1);
    paymentDate.setDate(15);
    return paymentDate;
}

// 예시
const settlementMonth = new Date('2025-10-01');
const paymentDate = calculatePaymentDate(settlementMonth); // 2025-11-15
```

---

## 🎯 마감 임박 알림

### 기준
- **D-3 이하** 강조 표시

### 계산 로직
```javascript
function getDaysLeft(deadline) {
    const today = new Date();
    const daysLeft = Math.floor((deadline - today) / (1000 * 60 * 60 * 24));
    return daysLeft;
}

function isUrgent(deadline) {
    return getDaysLeft(deadline) <= 3;
}
```

---

## 🆕 신규 항목 배지

### 기준
- **등록 후 30일간** "NEW" 배지 표시

### 계산 로직
```javascript
function isNew(registeredDate) {
    const today = new Date();
    const daysSinceRegistered = Math.floor((today - registeredDate) / (1000 * 60 * 60 * 24));
    return daysSinceRegistered <= 30;
}
```

---

## 📐 구독 상태 자동 전환

### 상태 흐름
```
ACTIVE → WARNING → GRACE → TERMINATED
```

### 전환 규칙

**ACTIVE → WARNING**:
- 결제 지연 발생

**WARNING → GRACE**:
- 결제 실패

**GRACE → TERMINATED**:
- 유예 기간 후에도 미결제

### 구현 예시 (PHP)
```php
function updateSubscriptionStatus($customerId, $paymentStatus) {
    global $con;

    $status = 'ACTIVE';

    if ($paymentStatus === 'DELAYED') {
        $status = 'WARNING';
    } elseif ($paymentStatus === 'FAILED') {
        $status = 'GRACE';
    } elseif ($paymentStatus === 'GRACE_EXPIRED') {
        $status = 'TERMINATED';
    }

    $sql = "UPDATE subscriptions SET status = ? WHERE customer_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ss", $status, $customerId);
    $stmt->execute();

    return $status;
}
```

---

## 📋 정책 업데이트 가이드

### 정책 변경 시 체크리스트

- [ ] HQ 정책센터에서 정책 값 수정
- [ ] 변경사항 로그 기록
- [ ] 모든 포털에 자동 반영 확인
- [ ] 계산식이 올바르게 적용되는지 검증
- [ ] 변경 전후 비교 테스트

### 정책 우선순위

1. **HQ 정책센터 값** (최우선)
2. `policy_appendix.txt` (참조)
3. 코드 내 하드코딩 값 (최후)

**중요**: 가능한 HQ 정책센터에서 관리하고, 코드는 정책을 읽어오는 방식으로 구현

---

**마지막 업데이트**: 2025-11-06 (v1.1)
