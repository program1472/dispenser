# 향기 디스펜서 시스템 - DB 설치 및 더미 데이터 가이드

## 📋 실행 순서

### 1단계: 데이터베이스 스키마 생성
```bash
mysql -u root -p your_database_name < database_schema.sql
```

### 2단계: 더미 데이터 Part 1 실행
```bash
mysql -u root -p your_database_name < dummy_data.sql
```

### 3단계: 더미 데이터 Part 2 실행
```bash
mysql -u root -p your_database_name < dummy_data_part2.sql
```

## 📊 생성되는 더미 데이터 요약

### 기본 마스터 데이터
- ✅ **역할(Roles)**: 6개 - SUPER_ADMIN, HQ_ADMIN, VENDOR, SALES_REP, CUSTOMER, LUCID
- ✅ **사용자(Users)**: 50개 (관리자 6명, 밴더 10명, 영업사원 10명, 고객 20명, 루시드 4명)
- ✅ **카테고리(Categories)**: 30개 (4단계 계층 구조)
- ✅ **태그(Tags)**: 30개 (공통 10개, 향 전용 10개, 콘텐츠 전용 10개)

### 조직 및 회원 데이터
- ✅ **밴더(Vendors)**: 10개 (전국 주요 도시별)
- ✅ **고객(Customers)**: 30개 (스타벅스, 롯데호텔, 신세계 등 실제 기업명)
- ✅ **고객 현장(Customer Sites)**: 50개 (각 고객별 여러 현장)
- ✅ **담당자 배정(Account Assignments)**: 30개

### 상품 데이터
- ✅ **디스펜서 모델(Devices)**: 10개
- ✅ **디스펜서 시리얼(Device Serials)**: 50개
- ✅ **디스펜서 배정(Device Assignments)**: 40개
- ✅ **향 카트리지(Scents)**: 30개 (Woody, Floral, Fruity, Green 계열)
- ✅ **콘텐츠(Contents)**: 30개 (계절별, 테마별, 프로모션)
- ✅ **태그 매핑(Tag Map)**: 50개

### 구독 데이터
- ✅ **구독(Subscriptions)**: 30개
- ✅ **구독 항목(Subscription Items)**: 60개
- ✅ **구독 주기(Subscription Cycles)**: 36개 (샘플)

### 운영 데이터
- ✅ **작업지시서(Work Orders)**: 30개
- ✅ **청구서(Invoices)**: 30개
- ✅ **청구서 항목(Invoice Items)**: 30개
- ✅ **결제 트랜잭션(Payment Transactions)**: 30개
- ✅ **정산(Settlements)**: 30개 (밴더/영업사원)

### 고객 서비스 데이터
- ✅ **콘텐츠 수정 요청(Content Requests)**: 10개
- ✅ **티켓(Tickets)**: 20개
- ✅ **알림(Notifications)**: 30개

### 시스템 설정
- ✅ **시스템 설정(Settings)**: 20개

## 🎯 데이터 특징

### 현실적인 데이터
- 실제 한국 기업명 사용 (스타벅스, 롯데호텔, 신세계백화점 등)
- 실제 지역별 밴더 분포 (서울, 부산, 대구, 광주, 대전 등)
- 2024년 2월~6월 기간의 시계열 데이터
- 완료/진행중/대기 등 다양한 상태값

### 연관관계 완벽 구현
- FK 참조 무결성 유지
- 구독 → 주기 → 작업지시서 → 청구서 → 결제 → 정산 흐름
- 고객 → 현장 → 디스펜서 배정 → 사용 이력
- 콘텐츠 요청 → 루시드 배정 → 시안 제작 → 승인

### 비즈니스 룰 반영
- 월 구독료: 29,700원
- 밴더 커미션: 40%, 인센티브: 5%
- 영업사원 판매 인센티브: 90,000원 (15,000원씩 6회 분할)
- 루시드 배분율: 50%
- 콘텐츠 수정 가격: 5,000원(프린팅) ~ 50,000원(프리미엄)

## 🔍 데이터 확인 쿼리

### 고객별 구독 현황
```sql
SELECT 
    c.company_name,
    COUNT(s.subscription_id) as 구독수,
    SUM(s.monthly_fee) as 월총금액
FROM customers c
LEFT JOIN subscriptions s ON c.customer_id = s.customer_id
WHERE s.status = 'ACTIVE'
GROUP BY c.customer_id
ORDER BY 구독수 DESC
LIMIT 10;
```

### 향 카트리지 인기 순위
```sql
SELECT 
    sc.scent_name,
    sc.scent_family,
    COUNT(si.item_id) as 사용횟수,
    sc.price
FROM scents sc
LEFT JOIN subscription_items si ON sc.scent_id = si.item_id_ref AND si.item_type = 'SCENT'
GROUP BY sc.scent_id
ORDER BY 사용횟수 DESC
LIMIT 10;
```

### 밴더별 정산 현황
```sql
SELECT 
    v.company_name,
    COUNT(s.settlement_id) as 정산횟수,
    SUM(s.total_amount) as 총정산금액,
    AVG(s.commission_amount) as 평균커미션
FROM vendors v
LEFT JOIN settlements s ON v.vendor_id = s.target_vendor_id
WHERE s.status = 'PAID'
GROUP BY v.vendor_id
ORDER BY 총정산금액 DESC;
```

### 콘텐츠 카테고리별 분포
```sql
SELECT 
    c1.category_name as 대분류,
    c2.category_name as 중분류,
    COUNT(co.content_id) as 콘텐츠수
FROM categories c1
LEFT JOIN categories c2 ON c1.category_id = c2.parent_id
LEFT JOIN contents co ON c2.category_id = co.category_id
WHERE c1.level = 1 AND c1.category_name = '콘텐츠 유형'
GROUP BY c1.category_id, c2.category_id
ORDER BY 콘텐츠수 DESC;
```

### 티켓 상태별 현황
```sql
SELECT 
    category as 카테고리,
    status as 상태,
    priority as 우선순위,
    COUNT(*) as 건수
FROM tickets
GROUP BY category, status, priority
ORDER BY 
    FIELD(priority, 'URGENT', 'HIGH', 'NORMAL', 'LOW'),
    FIELD(status, 'OPEN', 'IN_PROGRESS', 'ON_HOLD', 'RESOLVED', 'CLOSED');
```

## ⚠️ 주의사항

1. **실행 순서 준수**: 반드시 schema → part1 → part2 순서로 실행
2. **FK 제약조건**: Part 1 없이 Part 2를 실행하면 FK 오류 발생
3. **데이터베이스 초기화**: 기존 데이터가 있다면 DROP DATABASE 후 재생성 권장
4. **문자셋**: UTF8MB4 사용으로 이모지 지원
5. **비밀번호**: 모든 사용자 비밀번호는 'password' (bcrypt 해시됨)

## 🚀 빠른 시작 (한 줄 명령어)

```bash
# 전체 실행
mysql -u root -p your_database_name < database_schema.sql && \
mysql -u root -p your_database_name < dummy_data.sql && \
mysql -u root -p your_database_name < dummy_data_part2.sql

# 실행 확인
mysql -u root -p your_database_name -e "
SELECT 
    '사용자' as 구분, COUNT(*) as 개수 FROM users
UNION ALL SELECT '고객', COUNT(*) FROM customers
UNION ALL SELECT '구독', COUNT(*) FROM subscriptions
UNION ALL SELECT '향', COUNT(*) FROM scents
UNION ALL SELECT '콘텐츠', COUNT(*) FROM contents
UNION ALL SELECT '청구서', COUNT(*) FROM invoices
UNION ALL SELECT '티켓', COUNT(*) FROM tickets;
"
```

## 📝 추가 작업 필요 항목

Part 2에서 샘플로만 제공된 항목들:
- **구독 주기(Subscription Cycles)**: 총 180개 필요 (현재 36개만 샘플)
- **작업지시서 항목(Work Order Items)**: 각 작업지시서마다 2~3개씩
- **출고/배송(Shipments)**: 각 작업지시서마다 1개씩
- **콘텐츠 리비전(Content Revisions)**: 수정 요청별 시안 버전
- **재고(Inventory)**: 상품별 현재 재고
- **로그 테이블들**: device_logs, content_changes, scent_changes, audit_logs

이들은 필요 시 추가 생성 가능합니다.

---

**생성일**: 2025-11-10  
**버전**: 1.0  
**작성자**: All2Green Development Team
