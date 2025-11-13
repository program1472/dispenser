# 데이터베이스 규약

> DB 스키마, 식별자 규칙, FK 관리 정책

---

## 🔌 MySQL 연결 정보

### 로컬 개발 환경

- **MySQL 경로**: `C:\php\server\MariaDB10\bin\mysql.exe`
- **데이터베이스**: `dispenser`
- **사용자**: `program1472`
- **암호**: `$gPfls1129`

### 연결 예시

```bash
# CLI 접속
"C:\php\server\MariaDB10\bin\mysql.exe" -u program1472 -p$gPfls1129 dispenser

# 쿼리 실행
"C:\php\server\MariaDB10\bin\mysql.exe" -u program1472 -p$gPfls1129 dispenser -e "SELECT * FROM users LIMIT 5;"
```

### PHP MySQLi 연결

```php
$con = mysqli_connect(
    'localhost',
    'program1472',
    '$gPfls1129',
    'dispenser'
);

if (!$con) {
    die('Connection failed: ' . mysqli_connect_error());
}
```

---

## 🗄️ 핵심 테이블 구조

### roles
- **용도**: 사용자 역할 정의
- **컬럼**:
  - `id`: PK (AUTO_INCREMENT)
  - `code`: VARCHAR ('HQ', 'VENDOR', 'CUSTOMER')
  - `name`: VARCHAR (역할명)
- **비고**: 없으면 자동 시드

### users
- **PK**: `user_id` (BIGINT UNSIGNED AUTO_INCREMENT)
- **FK**:
  - `role_id` → roles.id
  - `vendor_id` (VARCHAR, 선택 연결용)
  - `customer_id` (VARCHAR, 선택 연결용)
- **주요 컬럼**:
  - `email`: VARCHAR (UNIQUE, 로그인용)
  - `login_id`: VARCHAR (선택, 로그인용)
  - `password`: VARCHAR (BCRYPT 해시)
  - `is_active`: TINYINT (0=비활성, 1=활성)
  - `last_login`: DATETIME
  - `created_at`: TIMESTAMP DEFAULT CURRENT_TIMESTAMP

### vendors
- **PK**: `vendor_id` (VARCHAR) — **VYYYYMMDDNNNN** 형식
- **주요 컬럼**:
  - `vendor_name`: VARCHAR
  - `contact_email`: VARCHAR
  - `contact_phone`: VARCHAR
  - `address`: TEXT
  - `created_at`: TIMESTAMP

### customers
- **PK**: `customer_id` (VARCHAR) — **CYYYYMMDDNNNN** 형식
- **주요 컬럼**:
  - `customer_name`: VARCHAR
  - `billing_contact`: JSON (연락처 정보)
  - `shipping_contact`: JSON (배송지 정보)
  - `created_at`: TIMESTAMP

### sites
- **PK**: `site_id` (VARCHAR) — **SYYYYMMDDNNNN** 형식
- **FK**: `customer_id` → customers.customer_id
- **주요 컬럼**:
  - `site_name`: VARCHAR (기본: '본점')
  - `address`: TEXT
  - `created_at`: TIMESTAMP

### user_extra
- **PK**: `id` (AUTO_INCREMENT)
- **FK**: `user_id` (BIGINT UNSIGNED) → users.user_id
- **주요 컬럼**:
  - `userid`: VARCHAR (추가 식별자)
  - 기타 확장 정보

### audit_log
- **용도**: 감사 로그 기록
- **주요 컬럼**:
  - `id`: PK (AUTO_INCREMENT)
  - `actor_user_id`: BIGINT UNSIGNED → users.user_id
  - `action`: VARCHAR (LOGIN, UPDATE, DELETE 등)
  - `target_table`: VARCHAR
  - `target_id`: VARCHAR
  - `old_value`: TEXT
  - `new_value`: TEXT
  - `ip_address`: VARCHAR
  - `created_at`: TIMESTAMP

---

## 🔑 식별자 생성 규칙

### 가변 ID 형식

| 테이블 | ID 형식 | 예시 |
|--------|---------|------|
| vendors | VYYYYMMDDNNNN | V202511060001 |
| customers | CYYYYMMDDNNNN | C202511060001 |
| sites | SYYYYMMDDNNNN | S202511060001 |

### 생성 로직 예시

```php
function generateVendorId($con) {
    $today = date('Ymd');
    $prefix = 'V' . $today;

    // 오늘 생성된 마지막 번호 조회
    $sql = "SELECT vendor_id FROM vendors
            WHERE vendor_id LIKE '{$prefix}%'
            ORDER BY vendor_id DESC LIMIT 1";
    $result = $con->query($sql);

    if ($result && $result->num_rows > 0) {
        $lastId = $result->fetch_assoc()['vendor_id'];
        $lastNum = (int)substr($lastId, -4);
        $newNum = $lastNum + 1;
    } else {
        $newNum = 1;
    }

    return $prefix . str_pad($newNum, 4, '0', STR_PAD_LEFT);
}
```

### 커스텀 ID 생성 공통 패턴

```php
// 고객 ID (CYYYYMMDDNNNN)
$customerId = 'C' . date('Ymd') . str_pad($seq, 4, '0', STR_PAD_LEFT);
// 예시: C202501080001, C202501080002

// 벤더 ID (VYYYYMMDDNNNN)
$vendorId = 'V' . date('Ymd') . str_pad($seq, 4, '0', STR_PAD_LEFT);
// 예시: V202501080001, V202501080002

// 공통 패턴
$today = date('Ymd');
$prefix = '{PREFIX}' . $today; // C/V/S 등
$lastIdSql = "SELECT {id_field} FROM {table} WHERE {id_field} LIKE '{$prefix}%' ORDER BY {id_field} DESC LIMIT 1";
$lastIdResult = mysqli_query($con, $lastIdSql);

if ($lastIdResult && mysqli_num_rows($lastIdResult) > 0) {
    $lastRow = mysqli_fetch_assoc($lastIdResult);
    $lastSeq = intval(substr($lastRow['{id_field}'], -4));
    $newSeq = str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);
} else {
    $newSeq = '0001';
}
$newId = $prefix . $newSeq;
```

---

## 🔗 Foreign Key 관리

### FK 재구성 순서

**문제 발생 시 (FK 타입 불일치, 인덱스 누락 등)**:

```sql
-- 1. FK 체크 비활성화
SET FOREIGN_KEY_CHECKS = 0;

-- 2. 자식 → 부모 순서로 드롭
DROP TABLE IF EXISTS user_extra;
DROP TABLE IF EXISTS audit_log;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS sites;
DROP TABLE IF EXISTS customers;
DROP TABLE IF EXISTS vendors;
DROP TABLE IF EXISTS roles;

-- 3. FK 체크 재활성화
SET FOREIGN_KEY_CHECKS = 1;

-- 4. 부모 → 자식 순서로 재생성
CREATE TABLE roles (...);
CREATE TABLE vendors (...);
CREATE TABLE customers (...);
CREATE TABLE users (...);
-- ...
```

### FK 컬럼 요구사항

- **타입 일치**: PK와 FK 컬럼의 타입/크기가 정확히 일치
- **UNSIGNED**: BIGINT 사용 시 양쪽 모두 UNSIGNED
- **NULL**: FK는 NULL 허용 또는 NOT NULL + DEFAULT 필요
- **인덱스**: FK 컬럼에는 자동으로 인덱스 생성되지만, 명시적으로 추가 권장

### FK 검증 쿼리

```sql
-- 테이블 구조 확인
SHOW CREATE TABLE users;

-- 인덱스 확인
SHOW INDEX FROM users;

-- FK 컬럼 타입 확인
SHOW COLUMNS FROM users WHERE Field = 'role_id';
SHOW COLUMNS FROM roles WHERE Field = 'id';
```

---

## 📊 스키마 호환성 전략

### 동적 INSERT/UPDATE 패턴

스키마 변경에 유연하게 대응하기 위해 **존재하는 컬럼만 처리**:

```php
function dynamicInsert($con, $table, $data) {
    // 테이블의 실제 컬럼 목록 조회
    $columnsResult = $con->query("SHOW COLUMNS FROM {$table}");
    $validColumns = [];
    while ($row = $columnsResult->fetch_assoc()) {
        $validColumns[] = $row['Field'];
    }

    // data에서 유효한 컬럼만 필터링
    $filteredData = array_filter($data, function($key) use ($validColumns) {
        return in_array($key, $validColumns);
    }, ARRAY_FILTER_USE_KEY);

    // INSERT 쿼리 생성
    $columns = implode(', ', array_keys($filteredData));
    $placeholders = implode(', ', array_fill(0, count($filteredData), '?'));

    $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
    $stmt = $con->prepare($sql);

    // 바인딩
    $types = str_repeat('s', count($filteredData)); // 간단히 모두 string
    $stmt->bind_param($types, ...array_values($filteredData));

    return $stmt->execute();
}
```

---

## 🔒 데이터 무결성 규칙

### 트랜잭션 사용

관련 엔터티를 함께 생성/갱신하는 작업은 **트랜잭션** 필수:

```php
// 예시: 벤더 + 사용자 동시 생성
$con->begin_transaction();

try {
    // 1. vendors 테이블에 삽입
    $vendorId = generateVendorId($con);
    $sql1 = "INSERT INTO vendors (vendor_id, vendor_name, ...) VALUES (?, ?, ...)";
    $stmt1 = $con->prepare($sql1);
    $stmt1->bind_param("ss...", $vendorId, $vendorName, ...);
    $stmt1->execute();

    // 2. users 테이블에 삽입
    $sql2 = "INSERT INTO users (email, password, role_id, vendor_id, ...) VALUES (?, ?, ?, ?, ...)";
    $stmt2 = $con->prepare($sql2);
    $stmt2->bind_param("ssis...", $email, $hashedPassword, $roleId, $vendorId, ...);
    $stmt2->execute();

    // 커밋
    $con->commit();
    $response = ['result' => 'ok', 'msg' => '벤더 및 사용자 생성 완료'];
} catch (Exception $e) {
    // 롤백
    $con->rollback();
    $response = ['result' => 'error', 'msg' => '생성 실패: ' . $e->getMessage()];
}

Finish();
```

### 공통 검증 항목

- **이메일**: 형식 검증 (`filter_var($email, FILTER_VALIDATE_EMAIL)`)
- **이메일 중복**: 가입 전 체크
- **필수값**: NULL 체크
- **권한**: 포털별 접근 권한 검증
- **ENUM/상태값**: 허용된 값만 저장

---

## 📝 테이블별 특수 규칙

### customers.billing_contact / shipping_contact (JSON)

```php
// 저장
$billingContact = json_encode([
    'name' => '담당자명',
    'phone' => '010-1234-5678',
    'email' => 'contact@example.com'
]);

$sql = "INSERT INTO customers (customer_id, customer_name, billing_contact)
        VALUES (?, ?, ?)";
$stmt = $con->prepare($sql);
$stmt->bind_param("sss", $customerId, $customerName, $billingContact);

// 조회
$result = $con->query("SELECT billing_contact FROM customers WHERE customer_id = 'C...'");
$row = $result->fetch_assoc();
$contact = json_decode($row['billing_contact'], true);
echo $contact['name']; // 담당자명
```

### audit_log (감사 로그)

- **로그 실패가 본처리를 막지 않도록 예외 삼킴**:

```php
try {
    // 본처리 (사용자 업데이트 등)
    $sql = "UPDATE users SET email = ? WHERE user_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("si", $newEmail, $userId);
    $stmt->execute();

    // 감사 로그 기록 (실패해도 무시)
    try {
        $logSql = "INSERT INTO audit_log (actor_user_id, action, target_table, target_id, old_value, new_value)
                   VALUES (?, 'UPDATE', 'users', ?, ?, ?)";
        $logStmt = $con->prepare($logSql);
        $logStmt->bind_param("iiss", $mb_id, $userId, $oldEmail, $newEmail);
        $logStmt->execute();
    } catch (Exception $logError) {
        // 로그 실패는 무시
        error_log("Audit log failed: " . $logError->getMessage());
    }

    $response = ['result' => 'ok', 'msg' => '업데이트 완료'];
} catch (Exception $e) {
    $response = ['result' => 'error', 'msg' => $e->getMessage()];
}

Finish();
```

---

## 🛠️ DB 헬퍼 함수 (MySQLi.php)

### 주요 함수 예시

```php
// 단일 값 조회
function fetchValue($con, $sql, $params = []) {
    $stmt = $con->prepare($sql);
    if ($params) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// 다중 행 조회
function fetchAll($con, $sql, $params = []) {
    $stmt = $con->prepare($sql);
    if ($params) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}
```

---

## 🗄️ 스키마 변경 워크플로우

**⚠️ IMPORTANT: schema.sql 변경 시 반드시 다음 순서를 따르세요**

### 1. schema.sql 수정
- 테이블 구조 변경 (ALTER TABLE)
- 새 테이블 추가 (CREATE TABLE)
- 인덱스/제약조건 추가

### 2. PHP 코드 업데이트
- INSERT/UPDATE 쿼리문을 schema.sql과 일치시키기
- 새 컬럼 추가 시 필수/선택 여부 확인
- 커스텀 ID 생성 로직 추가 (PRIMARY KEY가 VARCHAR인 경우)

### 3. 더미 데이터 업데이트 (필수!)
- `utility/generate_dummy_data.php` 수정
- 변경된 스키마에 맞춰 더미 데이터 생성 로직 수정
- 새 테이블 추가 시 최소 30개 더미 레코드 생성

### 4. 더미 데이터 재생성
```bash
# Windows (XAMPP 환경)
C:\AutoSet9\server\bin\php.exe utility/generate_dummy_data.php > dummy_data.sql

# 생성된 SQL 실행
mysql -u root -p dispenser < dummy_data.sql
```

### 5. 검증
- [ ] schema.sql과 PHP INSERT/UPDATE 쿼리 필드 일치 확인
- [ ] 더미 데이터 정상 삽입 확인
- [ ] CRUD 기능 정상 작동 확인

---

## 📋 스키마 일치 원칙

**⚠️ CRITICAL: schema.sql과 PHP 쿼리문은 반드시 일치해야 합니다**

```php
// ❌ Bad - schema.sql과 불일치
// schema.sql: customer_id VARCHAR(20) NOT NULL (PRIMARY KEY)
$sql = "INSERT INTO customers (name, email) VALUES (...)";
// → customer_id 누락으로 SQL 오류 발생, 세션 로그아웃 유발

// ✅ Good - schema.sql과 일치
// 1. customer_id 생성 로직 추가
$today = date('Ymd');
$prefix = 'C' . $today;
$lastIdSql = "SELECT customer_id FROM customers WHERE customer_id LIKE '{$prefix}%' ORDER BY customer_id DESC LIMIT 1";
$lastIdResult = mysqli_query($con, $lastIdSql);

if ($lastIdResult && mysqli_num_rows($lastIdResult) > 0) {
    $lastRow = mysqli_fetch_assoc($lastIdResult);
    $lastSeq = intval(substr($lastRow['customer_id'], -4));
    $newSeq = str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);
} else {
    $newSeq = '0001';
}
$customerId = $prefix . $newSeq; // CYYYYMMDDNNNN

// 2. INSERT 문에 customer_id 포함
$sql = "INSERT INTO customers (customer_id, name, email, ...)
        VALUES ('{$customerId}', ...)";
```

---

## 🎯 더미 데이터 생성 규칙

```php
// utility/generate_dummy_data.php 예시

// 1. 기존 데이터 삭제
echo "-- 기존 {테이블명} 데이터 삭제\n";
echo "DELETE FROM {테이블명};\n\n";

// 2. 30개 이상 더미 데이터 생성
$count = 30;
for ($i = 1; $i <= $count; $i++) {
    // 커스텀 ID 생성 (필요한 경우)
    $id = generateCustomId($i);

    // INSERT 쿼리 생성
    echo "INSERT INTO {테이블명} (field1, field2, ...) VALUES ";
    echo "('{$value1}', '{$value2}', ...);\n";
}

// 3. 완료 메시지
echo "\n-- ✓ {테이블명}: {$count}개 생성 완료\n\n";
```

---

## ⚠️ 일반적인 오류 패턴

```php
// ❌ 오류 1: 필수 컬럼 누락
// schema.sql: customer_id VARCHAR(20) NOT NULL
INSERT INTO customers (name) VALUES ('홍길동');
// → ERROR: Field 'customer_id' doesn't have a default value
// → 로그아웃 유발!

// ✅ 수정: 커스텀 ID 생성 후 포함
$customerId = 'C' . date('Ymd') . '0001';
INSERT INTO customers (customer_id, name) VALUES ('{$customerId}', '홍길동');

// ❌ 오류 2: 더미 데이터 스키마 불일치
// schema.sql: ALTER TABLE vendors ADD COLUMN tax_id_number VARCHAR(50)
// generate_dummy_data.php: (업데이트 안 함)
// → 더미 데이터에 tax_id_number 누락

// ✅ 수정: generate_dummy_data.php 업데이트
echo "INSERT INTO vendors (..., tax_id_number) VALUES (..., '123-45-67890');\n";

// ❌ 오류 3: $response['result'] = false 누락
if ($result) {
    $response['result'] = true;
} else {
    // $response['result'] = false; ← 누락!
    $response['error'] = ['msg' => '오류', 'code' => 500];
}
// → 프론트엔드에서 result 체크 시 undefined 오류

// ✅ 수정: 모든 에러 응답에 result = false 명시
if ($result) {
    $response['result'] = true;
} else {
    $response['result'] = false; // ← 필수!
    $response['error'] = ['msg' => '오류', 'code' => 500];
}
```

---

## 📋 스키마 변경 체크리스트

### 테이블 추가 시:
- [ ] schema.sql에 CREATE TABLE 추가
- [ ] generate_dummy_data.php에 30개 더미 데이터 생성 로직 추가
- [ ] CRUD PHP 파일 생성 (doc/{role}/{table}_mgmt.php)
- [ ] 메뉴에 등록 (inc/menus.php)
- [ ] 더미 데이터 재생성 실행

### 컬럼 추가/변경 시:
- [ ] schema.sql에 ALTER TABLE 추가
- [ ] 관련 PHP INSERT/UPDATE 쿼리 수정
- [ ] generate_dummy_data.php 업데이트
- [ ] 더미 데이터 재생성 실행
- [ ] 기존 데이터 마이그레이션 (필요 시)

### PRIMARY KEY 변경 시:
- [ ] AUTO_INCREMENT → VARCHAR: 커스텀 ID 생성 로직 추가
- [ ] VARCHAR → AUTO_INCREMENT: 기존 ID 매핑 테이블 생성 (필요 시)
- [ ] 외래 키 참조 테이블 모두 업데이트

---

## 🎯 실전 예시: customers 테이블 추가

### 1. schema.sql
```sql
CREATE TABLE `customers` (
  `customer_id` varchar(20) NOT NULL COMMENT 'CYYYYMMDDNNNN 형식',
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  -- ... 기타 컬럼
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 2. customer_list.php (CRUD)
```php
case 'add_customer':
    // 커스텀 ID 생성
    $today = date('Ymd');
    $prefix = 'C' . $today;
    $lastIdSql = "SELECT customer_id FROM customers WHERE customer_id LIKE '{$prefix}%' ORDER BY customer_id DESC LIMIT 1";
    $lastIdResult = mysqli_query($con, $lastIdSql);

    if ($lastIdResult && mysqli_num_rows($lastIdResult) > 0) {
        $lastRow = mysqli_fetch_assoc($lastIdResult);
        $lastSeq = intval(substr($lastRow['customer_id'], -4));
        $newSeq = str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);
    } else {
        $newSeq = '0001';
    }
    $customerId = $prefix . $newSeq;

    // INSERT 실행
    $sql = "INSERT INTO customers (customer_id, name, email, ...) VALUES ('{$customerId}', ...)";
    $result = query($sql);

    if ($result) {
        $response['result'] = true;
        $response['msg'] = '고객이 등록되었습니다.';
        $response['item'] = ['customer_id' => $customerId];
    } else {
        $response['result'] = false; // 필수!
        $response['error'] = ['msg' => '등록 실패', 'code' => 500];
    }
    Finish();
```

### 3. generate_dummy_data.php
```php
// 고객 더미 데이터 생성
echo "-- 3. Customers 더미 데이터\n";
echo "DELETE FROM customers;\n\n";

$customerCount = 30;
for ($i = 1; $i <= $customerCount; $i++) {
    $customerId = 'C20250108' . str_pad($i, 4, '0', STR_PAD_LEFT);
    $name = "고객{$i}";
    $email = "customer{$i}@example.com";
    // ... 기타 필드

    echo "INSERT INTO customers (customer_id, name, email, ...) ";
    echo "VALUES ('{$customerId}', '{$name}', '{$email}', ...);\n";
}

echo "\n-- ✓ Customers: {$customerCount}개 생성 완료\n\n";
```

### 4. 더미 데이터 실행
```bash
C:\AutoSet9\server\bin\php.exe utility/generate_dummy_data.php > dummy_data.sql
mysql -u root -p dispenser < dummy_data.sql
```

---

## 📋 DB 체크리스트

### 테이블 생성 시
- [ ] PK는 적절한 타입 (AUTO_INCREMENT 또는 VARCHAR)
- [ ] FK 컬럼은 부모 PK와 타입/크기 일치
- [ ] UNSIGNED 속성 일치
- [ ] 인덱스 추가 (FK, 검색 빈도 높은 컬럼)
- [ ] DEFAULT 값 설정 (created_at, is_active 등)
- [ ] 타임스탬프는 CURRENT_TIMESTAMP

### FK 추가 시
- [ ] 부모 테이블이 먼저 존재
- [ ] 부모 PK에 UNIQUE 또는 PRIMARY KEY 제약
- [ ] 자식 FK 컬럼에 인덱스 존재
- [ ] ON DELETE/ON UPDATE 액션 설정 (CASCADE, SET NULL 등)

### 데이터 삽입 시
- [ ] 필수 컬럼 누락 없음
- [ ] ENUM/상태값 검증
- [ ] 이메일 형식 검증
- [ ] 중복 체크 (UNIQUE 컬럼)
- [ ] 트랜잭션 사용 (관련 테이블 동시 삽입 시)

---

**마지막 업데이트**: 2025-11-12 (v2.0)

### v2.0 변경사항 (2025-11-12)
- database.md → 05-database.md로 파일명 변경
- quick-reference.md의 "데이터베이스 & 스키마 관리" 섹션 통합
- 스키마 변경 워크플로우 강화
- 커스텀 ID 생성 규칙 통합 및 명확화
- 중복 제거하며 통합
