-- ============================================================
-- 향기 디스펜서 구독 서비스 시스템 - 데이터베이스 스키마
-- ============================================================
-- 작성일: 2025-11-10
-- DBMS: MySQL 8.0+
-- 문자셋: utf8mb4 (이모지 지원)
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- 1. 회원 및 권한 관리
-- ============================================================

-- 1.1 역할(Role) 테이블
CREATE TABLE `roles` (
  `role_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '역할 ID (PK)',
  `role_name` VARCHAR(50) NOT NULL UNIQUE COMMENT '역할명 (SUPER_ADMIN, HQ_ADMIN, VENDOR, SALES_REP, CUSTOMER, LUCID)',
  `display_name` VARCHAR(100) NOT NULL COMMENT '화면 표시명',
  `description` TEXT COMMENT '역할 설명',
  `is_active` BOOLEAN DEFAULT TRUE COMMENT '활성 상태 (TRUE: 사용, FALSE: 비활성)',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='역할(Role) 정의 테이블 - 시스템 내 사용자 역할을 정의';

-- 1.2 사용자(User) 테이블
CREATE TABLE `users` (
  `user_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '사용자 ID (PK)',
  `role_id` INT NOT NULL COMMENT '역할 ID (FK -> roles)',
  `email` VARCHAR(255) NOT NULL UNIQUE COMMENT '이메일 (로그인 ID, 중복 불가)',
  `password_hash` VARCHAR(255) NOT NULL COMMENT '비밀번호 해시 (bcrypt)',
  `name` VARCHAR(100) NOT NULL COMMENT '이름',
  `phone` VARCHAR(20) COMMENT '연락처 (- 포함, 예: 010-1234-5678)',
  `is_active` BOOLEAN DEFAULT TRUE COMMENT '활성 상태 (TRUE: 사용, FALSE: 비활성)',
  `last_login_at` DATETIME COMMENT '마지막 로그인 일시',
  `deleted_at` DATETIME COMMENT '삭제일시 (Soft Delete, NULL: 미삭제)',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',
  `created_by` INT COMMENT '생성자 user_id',
  `updated_by` INT COMMENT '수정자 user_id',
  INDEX `idx_role` (`role_id`),
  INDEX `idx_deleted` (`deleted_at`),
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`role_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='사용자(User) 테이블 - 시스템 로그인 계정 정보';

-- 1.3 밴더(Vendor) 테이블
CREATE TABLE `vendors` (
  `vendor_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '밴더 ID (PK)',
  `user_id` INT NOT NULL UNIQUE COMMENT '사용자 ID (FK -> users, 밴더 로그인 계정)',
  `company_name` VARCHAR(200) NOT NULL COMMENT '회사명',
  `business_number` VARCHAR(20) UNIQUE COMMENT '사업자등록번호 (- 제외, 10자리)',
  `ceo_name` VARCHAR(100) COMMENT '대표자명',
  `business_type` VARCHAR(100) COMMENT '업태',
  `business_category` VARCHAR(100) COMMENT '업종',
  `address` VARCHAR(500) COMMENT '주소',
  `bank_name` VARCHAR(100) COMMENT '정산 은행명',
  `account_number` VARCHAR(50) COMMENT '정산 계좌번호',
  `account_holder` VARCHAR(100) COMMENT '예금주',
  `commission_rate` DECIMAL(5,2) DEFAULT 40.00 COMMENT '커미션율 (%, 기본 40.00)',
  `incentive_rate` DECIMAL(5,2) DEFAULT 5.00 COMMENT '인센티브율 (%, 기본 5.00)',
  `contract_start_date` DATE COMMENT '계약 시작일',
  `contract_end_date` DATE COMMENT '계약 종료일',
  `contract_document_url` VARCHAR(500) COMMENT '계약서 파일 URL',
  `is_active` BOOLEAN DEFAULT TRUE COMMENT '활성 상태 (TRUE: 운영, FALSE: 계약종료/정지)',
  `deleted_at` DATETIME COMMENT '삭제일시 (Soft Delete)',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',
  `created_by` INT COMMENT '생성자 user_id',
  `updated_by` INT COMMENT '수정자 user_id',
  INDEX `idx_user` (`user_id`),
  INDEX `idx_active` (`is_active`),
  INDEX `idx_deleted` (`deleted_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='밴더(Vendor) 테이블 - 판매 파트너 정보 및 계약 조건';

-- 1.4 고객(Customer) 테이블
CREATE TABLE `customers` (
  `customer_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '고객 ID (PK)',
  `user_id` INT NOT NULL UNIQUE COMMENT '사용자 ID (FK -> users, 고객 로그인 계정)',
  `vendor_id` INT COMMENT '소속 밴더 ID (FK -> vendors, NULL: 직거래)',
  `company_name` VARCHAR(200) NOT NULL COMMENT '회사명',
  `business_number` VARCHAR(20) COMMENT '사업자등록번호',
  `ceo_name` VARCHAR(100) COMMENT '대표자명',
  `business_type` VARCHAR(100) COMMENT '업태',
  `business_category` VARCHAR(100) COMMENT '업종',
  `address` VARCHAR(500) COMMENT '본사 주소',
  `payment_method` ENUM('CMS', 'CARD', 'TRANSFER') DEFAULT 'CARD' COMMENT '결제 방법 (CMS: 자동이체, CARD: 신용카드, TRANSFER: 무통장입금)',
  `bank_name` VARCHAR(100) COMMENT '은행명 (CMS인 경우)',
  `account_number` VARCHAR(50) COMMENT '계좌번호 (CMS인 경우)',
  `card_number_masked` VARCHAR(20) COMMENT '카드번호 마스킹 (CARD인 경우, 예: ****-****-****-1234)',
  `billing_key` VARCHAR(100) COMMENT '결제 키 (PG사 빌링키)',
  `is_active` BOOLEAN DEFAULT TRUE COMMENT '활성 상태',
  `deleted_at` DATETIME COMMENT '삭제일시 (Soft Delete)',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',
  `created_by` INT COMMENT '생성자 user_id',
  `updated_by` INT COMMENT '수정자 user_id',
  INDEX `idx_user` (`user_id`),
  INDEX `idx_vendor` (`vendor_id`),
  INDEX `idx_active` (`is_active`),
  INDEX `idx_deleted` (`deleted_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE RESTRICT,
  FOREIGN KEY (`vendor_id`) REFERENCES `vendors`(`vendor_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='고객(Customer) 테이블 - 구독 서비스 이용 고객 정보';

-- 1.5 고객 현장(Site) 테이블
CREATE TABLE `customer_sites` (
  `site_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '현장 ID (PK)',
  `customer_id` INT NOT NULL COMMENT '고객 ID (FK -> customers)',
  `site_name` VARCHAR(200) NOT NULL COMMENT '현장명 (예: 본사 1층 로비, 강남점)',
  `address` VARCHAR(500) NOT NULL COMMENT '설치 주소',
  `contact_name` VARCHAR(100) COMMENT '현장 담당자 이름',
  `contact_phone` VARCHAR(20) COMMENT '현장 담당자 연락처',
  `notes` TEXT COMMENT '특이사항 (설치 위치, 주의사항 등)',
  `is_active` BOOLEAN DEFAULT TRUE COMMENT '활성 상태',
  `deleted_at` DATETIME COMMENT '삭제일시 (Soft Delete)',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',
  `created_by` INT COMMENT '생성자 user_id',
  `updated_by` INT COMMENT '수정자 user_id',
  INDEX `idx_customer` (`customer_id`),
  INDEX `idx_active` (`is_active`),
  INDEX `idx_deleted` (`deleted_at`),
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`customer_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='고객 현장(Site) 테이블 - 고객의 디스펜서 설치 현장 정보 (1고객 N현장 가능)';

-- 1.6 담당자 배정(Account Assignment) 테이블
CREATE TABLE `account_assignments` (
  `assignment_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '배정 ID (PK)',
  `customer_id` INT NOT NULL COMMENT '고객 ID (FK -> customers)',
  `sales_user_id` INT NOT NULL COMMENT '담당 영업사원 user_id (FK -> users)',
  `vendor_id` INT COMMENT '담당 밴더 ID (FK -> vendors, NULL: 본사 직관리)',
  `assigned_date` DATE NOT NULL COMMENT '배정일',
  `is_active` BOOLEAN DEFAULT TRUE COMMENT '활성 상태 (TRUE: 현재 담당, FALSE: 담당 해제)',
  `notes` TEXT COMMENT '배정 메모',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',
  `created_by` INT COMMENT '생성자 user_id',
  `updated_by` INT COMMENT '수정자 user_id',
  INDEX `idx_customer` (`customer_id`),
  INDEX `idx_sales` (`sales_user_id`),
  INDEX `idx_vendor` (`vendor_id`),
  INDEX `idx_assignments_active` (`sales_user_id`, `is_active`),
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`customer_id`) ON DELETE CASCADE,
  FOREIGN KEY (`sales_user_id`) REFERENCES `users`(`user_id`) ON DELETE RESTRICT,
  FOREIGN KEY (`vendor_id`) REFERENCES `vendors`(`vendor_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='담당자 배정(Account Assignment) 테이블 - 영업사원의 고객 담당 배정 정보';

-- ============================================================
-- 2. 카테고리 및 태그 시스템
-- ============================================================

-- 2.1 카테고리(Category) 테이블 - 4단계 계층 구조
CREATE TABLE `categories` (
  `category_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '카테고리 ID (PK)',
  `parent_id` INT COMMENT '상위 카테고리 ID (FK -> categories, NULL: 최상위)',
  `level` TINYINT NOT NULL COMMENT '카테고리 레벨 (1: 최상위, 2: 중분류, 3: 소분류, 4: 세부분류)',
  `category_name` VARCHAR(100) NOT NULL COMMENT '카테고리명',
  `slug` VARCHAR(100) NOT NULL COMMENT 'URL 슬러그 (영문, 소문자)',
  `description` TEXT COMMENT '카테고리 설명',
  `image_url` VARCHAR(500) COMMENT '카테고리 이미지 URL',
  `display_order` INT DEFAULT 0 COMMENT '정렬 순서 (작을수록 우선)',
  `is_active` BOOLEAN DEFAULT TRUE COMMENT '활성 상태',
  `deleted_at` DATETIME COMMENT '삭제일시 (Soft Delete)',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',
  `created_by` INT COMMENT '생성자 user_id',
  `updated_by` INT COMMENT '수정자 user_id',
  INDEX `idx_parent` (`parent_id`),
  INDEX `idx_level` (`level`),
  INDEX `idx_slug` (`slug`),
  INDEX `idx_active` (`is_active`),
  INDEX `idx_deleted` (`deleted_at`),
  FOREIGN KEY (`parent_id`) REFERENCES `categories`(`category_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='카테고리(Category) 테이블 - 4단계 계층 구조로 상품 분류 (콘텐츠, 향, 기기 등 모든 상품에 적용)';

-- 2.2 태그(Tag) 테이블
CREATE TABLE `tags` (
  `tag_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '태그 ID (PK)',
  `tag_name` VARCHAR(50) NOT NULL UNIQUE COMMENT '태그명 (중복 불가)',
  `slug` VARCHAR(50) NOT NULL UNIQUE COMMENT 'URL 슬러그',
  `tag_type` ENUM('CONTENT', 'SCENT', 'DEVICE', 'GENERAL') DEFAULT 'GENERAL' COMMENT '태그 유형 (CONTENT: 콘텐츠, SCENT: 향, DEVICE: 기기, GENERAL: 공통)',
  `color` VARCHAR(7) COMMENT '태그 색상 (HEX, 예: #FF5733)',
  `description` TEXT COMMENT '태그 설명',
  `usage_count` INT DEFAULT 0 COMMENT '사용 횟수 (캐시용)',
  `is_active` BOOLEAN DEFAULT TRUE COMMENT '활성 상태',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',
  INDEX `idx_type` (`tag_type`),
  INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='태그(Tag) 테이블 - 상품 메타데이터 태그 마스터';

-- 2.3 태그 매핑(Tag Map) 테이블 - 다대다 관계
CREATE TABLE `tag_map` (
  `map_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '매핑 ID (PK)',
  `tag_id` INT NOT NULL COMMENT '태그 ID (FK -> tags)',
  `entity_type` ENUM('CONTENT', 'SCENT', 'DEVICE', 'PART', 'ACCESSORY') NOT NULL COMMENT '엔티티 타입 (CONTENT: 콘텐츠, SCENT: 향, DEVICE: 기기, PART: 부자재, ACCESSORY: 악세사리)',
  `entity_id` INT NOT NULL COMMENT '엔티티 ID (해당 상품 테이블의 PK)',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  UNIQUE KEY `unique_tag_entity` (`tag_id`, `entity_type`, `entity_id`),
  INDEX `idx_entity` (`entity_type`, `entity_id`),
  FOREIGN KEY (`tag_id`) REFERENCES `tags`(`tag_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='태그 매핑(Tag Map) 테이블 - 태그와 상품 간 다대다 관계 매핑';

-- ============================================================
-- 3. 상품 관리
-- ============================================================

-- 3.1 디스펜서(Device) 마스터 테이블
CREATE TABLE `devices` (
  `device_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '디스펜서 ID (PK)',
  `model_name` VARCHAR(100) NOT NULL COMMENT '모델명',
  `manufacturer` VARCHAR(100) COMMENT '제조사',
  `category_id` INT COMMENT '카테고리 ID (FK -> categories)',
  `specifications` JSON COMMENT '사양 정보 (JSON, 예: {크기, 무게, 전력})',
  `image_url` VARCHAR(500) COMMENT '제품 이미지 URL',
  `manual_url` VARCHAR(500) COMMENT '매뉴얼 URL',
  `is_active` BOOLEAN DEFAULT TRUE COMMENT '활성 상태',
  `deleted_at` DATETIME COMMENT '삭제일시 (Soft Delete)',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',
  `created_by` INT COMMENT '생성자 user_id',
  `updated_by` INT COMMENT '수정자 user_id',
  INDEX `idx_category` (`category_id`),
  INDEX `idx_active` (`is_active`),
  INDEX `idx_deleted` (`deleted_at`),
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`category_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='디스펜서(Device) 마스터 테이블 - 디스펜서 모델 정보';

-- 3.2 디스펜서 시리얼(Serial) 테이블
CREATE TABLE `device_serials` (
  `serial_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '시리얼 ID (PK)',
  `device_id` INT NOT NULL COMMENT '디스펜서 ID (FK -> devices)',
  `serial_number` VARCHAR(100) NOT NULL UNIQUE COMMENT '시리얼 번호 (고유, 중복 불가)',
  `qr_code` VARCHAR(200) COMMENT 'QR 코드 데이터',
  `manufacture_date` DATE COMMENT '제조일',
  `import_date` DATE COMMENT '입고일',
  `status` ENUM('AVAILABLE', 'ASSIGNED', 'MAINTENANCE', 'RETIRED', 'DISPOSED') DEFAULT 'AVAILABLE' COMMENT '상태 (AVAILABLE: 재고, ASSIGNED: 배정됨, MAINTENANCE: 수리중, RETIRED: 회수됨, DISPOSED: 폐기)',
  `notes` TEXT COMMENT '특이사항',
  `deleted_at` DATETIME COMMENT '삭제일시 (Soft Delete)',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',
  `created_by` INT COMMENT '생성자 user_id',
  `updated_by` INT COMMENT '수정자 user_id',
  INDEX `idx_device` (`device_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_deleted` (`deleted_at`),
  FOREIGN KEY (`device_id`) REFERENCES `devices`(`device_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='디스펜서 시리얼(Serial) 테이블 - 개별 디스펜서 기기 정보 (시리얼 번호 단위 관리)';

-- 3.3 디스펜서 배정(Assignment) 테이블
CREATE TABLE `device_assignments` (
  `assignment_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '배정 ID (PK)',
  `serial_id` INT NOT NULL COMMENT '시리얼 ID (FK -> device_serials)',
  `customer_id` INT NOT NULL COMMENT '고객 ID (FK -> customers)',
  `site_id` INT COMMENT '현장 ID (FK -> customer_sites, NULL: 본사)',
  `assigned_date` DATE NOT NULL COMMENT '배정일 (설치일)',
  `returned_date` DATE COMMENT '회수일',
  `installation_location` VARCHAR(200) COMMENT '설치 위치 (상세, 예: 1층 로비 입구 왼쪽)',
  `status` ENUM('ACTIVE', 'RETURNED', 'REPLACED') DEFAULT 'ACTIVE' COMMENT '배정 상태 (ACTIVE: 사용중, RETURNED: 회수됨, REPLACED: 교체됨)',
  `notes` TEXT COMMENT '설치 메모',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',
  `created_by` INT COMMENT '생성자 user_id',
  `updated_by` INT COMMENT '수정자 user_id',
  INDEX `idx_serial` (`serial_id`),
  INDEX `idx_customer` (`customer_id`),
  INDEX `idx_site` (`site_id`),
  INDEX `idx_status` (`status`),
  FOREIGN KEY (`serial_id`) REFERENCES `device_serials`(`serial_id`) ON DELETE RESTRICT,
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`customer_id`) ON DELETE RESTRICT,
  FOREIGN KEY (`site_id`) REFERENCES `customer_sites`(`site_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='디스펜서 배정(Assignment) 테이블 - 디스펜서 기기를 고객 현장에 배정한 이력';

-- 3.4 콘텐츠(Content) 테이블
CREATE TABLE `contents` (
  `content_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '콘텐츠 ID (PK)',
  `category_id` INT COMMENT '카테고리 ID (FK -> categories, 4단계 분류)',
  `content_title` VARCHAR(200) NOT NULL COMMENT '콘텐츠 제목',
  `description` TEXT COMMENT '콘텐츠 설명',
  `template_type` ENUM('BASIC', 'SEASONAL', 'PROMOTIONAL', 'CUSTOM') DEFAULT 'BASIC' COMMENT '템플릿 유형 (BASIC: 기본, SEASONAL: 계절, PROMOTIONAL: 홍보, CUSTOM: 맞춤)',
  `image_url` VARCHAR(500) COMMENT '콘텐츠 이미지 URL',
  `thumbnail_url` VARCHAR(500) COMMENT '썸네일 이미지 URL',
  `file_url` VARCHAR(500) COMMENT '인쇄용 파일 URL (AI, PDF 등)',
  `size` VARCHAR(20) COMMENT '크기 (예: A4, A5, 210x297mm)',
  `owner_type` ENUM('COMPANY', 'CUSTOMER', 'LUCID') DEFAULT 'COMPANY' COMMENT '소유자 타입 (COMPANY: 본사 공용, CUSTOMER: 고객 전용, LUCID: 루시드 제작)',
  `owner_id` INT COMMENT '소유자 ID (owner_type이 CUSTOMER면 customer_id, LUCID면 user_id)',
  `is_free` BOOLEAN DEFAULT TRUE COMMENT '무료 제공 여부 (TRUE: 기본 무료, FALSE: 유료)',
  `is_active` BOOLEAN DEFAULT TRUE COMMENT '활성 상태',
  `view_count` INT DEFAULT 0 COMMENT '조회수',
  `deleted_at` DATETIME COMMENT '삭제일시 (Soft Delete)',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',
  `created_by` INT COMMENT '생성자 user_id',
  `updated_by` INT COMMENT '수정자 user_id',
  INDEX `idx_category` (`category_id`),
  INDEX `idx_owner` (`owner_type`, `owner_id`),
  INDEX `idx_active` (`is_active`),
  INDEX `idx_deleted` (`deleted_at`),
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`category_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='콘텐츠(Content) 테이블 - 디스펜서 인쇄물 콘텐츠 정보';

-- 3.5 향 카트리지(Scent) 테이블
CREATE TABLE `scents` (
  `scent_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '향 ID (PK)',
  `category_id` INT COMMENT '카테고리 ID (FK -> categories, 4단계 분류: 계열 > 향명 > 특성 > 강도)',
  `scent_name` VARCHAR(100) NOT NULL COMMENT '향 이름',
  `scent_family` VARCHAR(50) COMMENT '향 계열 (Woody, Floral, Fruity, Green 등)',
  `description` TEXT COMMENT '향 설명',
  `capacity_ml` INT COMMENT '용량 (ml)',
  `price` DECIMAL(10,2) COMMENT '단품 가격 (원)',
  `image_url` VARCHAR(500) COMMENT '향 이미지 URL',
  `ingredients` TEXT COMMENT '주요 성분',
  `is_allergen_free` BOOLEAN DEFAULT FALSE COMMENT '알러지프리 여부',
  `is_eco_friendly` BOOLEAN DEFAULT FALSE COMMENT '친환경 인증 여부',
  `is_active` BOOLEAN DEFAULT TRUE COMMENT '활성 상태',
  `stock_quantity` INT DEFAULT 0 COMMENT '재고 수량',
  `view_count` INT DEFAULT 0 COMMENT '조회수',
  `deleted_at` DATETIME COMMENT '삭제일시 (Soft Delete)',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',
  `created_by` INT COMMENT '생성자 user_id',
  `updated_by` INT COMMENT '수정자 user_id',
  INDEX `idx_category` (`category_id`),
  INDEX `idx_family` (`scent_family`),
  INDEX `idx_active` (`is_active`),
  INDEX `idx_deleted` (`deleted_at`),
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`category_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='향 카트리지(Scent) 테이블 - 디스펜서 향 오일 정보';

-- 3.6 부자재(Part) 테이블
CREATE TABLE `parts` (
  `part_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '부자재 ID (PK)',
  `category_id` INT COMMENT '카테고리 ID (FK -> categories)',
  `part_name` VARCHAR(100) NOT NULL COMMENT '부자재명',
  `part_number` VARCHAR(50) COMMENT '부품 번호',
  `compatible_device_id` INT COMMENT '호환 디스펜서 ID (FK -> devices, NULL: 공용)',
  `description` TEXT COMMENT '설명',
  `price` DECIMAL(10,2) COMMENT '가격 (원)',
  `warranty_type` ENUM('FREE', 'PAID') DEFAULT 'FREE' COMMENT '보증 유형 (FREE: 무상, PAID: 유상)',
  `stock_quantity` INT DEFAULT 0 COMMENT '재고 수량',
  `is_active` BOOLEAN DEFAULT TRUE COMMENT '활성 상태',
  `deleted_at` DATETIME COMMENT '삭제일시 (Soft Delete)',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',
  `created_by` INT COMMENT '생성자 user_id',
  `updated_by` INT COMMENT '수정자 user_id',
  INDEX `idx_category` (`category_id`),
  INDEX `idx_device` (`compatible_device_id`),
  INDEX `idx_active` (`is_active`),
  INDEX `idx_deleted` (`deleted_at`),
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`category_id`) ON DELETE SET NULL,
  FOREIGN KEY (`compatible_device_id`) REFERENCES `devices`(`device_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='부자재(Part) 테이블 - 디스펜서 유지보수용 교체 부품';

-- 3.7 악세사리(Accessory) 테이블
CREATE TABLE `accessories` (
  `accessory_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '악세사리 ID (PK)',
  `category_id` INT COMMENT '카테고리 ID (FK -> categories)',
  `accessory_name` VARCHAR(100) NOT NULL COMMENT '악세사리명',
  `description` TEXT COMMENT '설명',
  `price` DECIMAL(10,2) COMMENT '가격 (원)',
  `image_url` VARCHAR(500) COMMENT '이미지 URL',
  `stock_quantity` INT DEFAULT 0 COMMENT '재고 수량',
  `is_active` BOOLEAN DEFAULT TRUE COMMENT '활성 상태',
  `deleted_at` DATETIME COMMENT '삭제일시 (Soft Delete)',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',
  `created_by` INT COMMENT '생성자 user_id',
  `updated_by` INT COMMENT '수정자 user_id',
  INDEX `idx_category` (`category_id`),
  INDEX `idx_active` (`is_active`),
  INDEX `idx_deleted` (`deleted_at`),
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`category_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='악세사리(Accessory) 테이블 - 디스펜서 관련 추가 상품 (거치대, 케이스 등)';

-- ============================================================
-- 4. 구독 관리
-- ============================================================

-- 4.1 구독(Subscription) 테이블
CREATE TABLE `subscriptions` (
  `subscription_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '구독 ID (PK)',
  `customer_id` INT NOT NULL COMMENT '고객 ID (FK -> customers)',
  `site_id` INT COMMENT '설치 현장 ID (FK -> customer_sites)',
  `subscription_number` VARCHAR(50) NOT NULL UNIQUE COMMENT '구독 번호 (고유, 예: SUB-2025-0001)',
  `start_date` DATE NOT NULL COMMENT '구독 시작일',
  `end_date` DATE COMMENT '구독 종료일 (NULL: 무기한)',
  `status` ENUM('ACTIVE', 'PAUSED', 'CANCELLED', 'EXPIRED') DEFAULT 'ACTIVE' COMMENT '구독 상태 (ACTIVE: 진행중, PAUSED: 일시정지, CANCELLED: 해지, EXPIRED: 만료)',
  `monthly_fee` DECIMAL(10,2) DEFAULT 29700.00 COMMENT '월 구독료 (원, 기본 29,700)',
  `cycle_months` TINYINT DEFAULT 2 COMMENT '배송 주기 (개월, 기본 2개월)',
  `billing_day` TINYINT DEFAULT 1 COMMENT '결제일 (1~31일)',
  `next_cycle_date` DATE COMMENT '다음 배송 예정일',
  `notes` TEXT COMMENT '구독 메모',
  `deleted_at` DATETIME COMMENT '삭제일시 (Soft Delete)',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',
  `created_by` INT COMMENT '생성자 user_id',
  `updated_by` INT COMMENT '수정자 user_id',
  INDEX `idx_customer` (`customer_id`),
  INDEX `idx_site` (`site_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_next_cycle` (`next_cycle_date`),
  INDEX `idx_deleted` (`deleted_at`),
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`customer_id`) ON DELETE RESTRICT,
  FOREIGN KEY (`site_id`) REFERENCES `customer_sites`(`site_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='구독(Subscription) 테이블 - 고객의 구독 서비스 마스터 정보';

-- 4.2 구독 항목(Subscription Item) 테이블
CREATE TABLE `subscription_items` (
  `item_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '구독 항목 ID (PK)',
  `subscription_id` INT NOT NULL COMMENT '구독 ID (FK -> subscriptions)',
  `item_type` ENUM('DEVICE', 'SCENT', 'CONTENT', 'PART', 'ACCESSORY') NOT NULL COMMENT '항목 타입 (DEVICE: 디스펜서, SCENT: 향, CONTENT: 콘텐츠, PART: 부자재, ACCESSORY: 악세사리)',
  `item_id_ref` INT NOT NULL COMMENT '항목 참조 ID (해당 상품 테이블의 PK)',
  `quantity` INT DEFAULT 1 COMMENT '수량',
  `is_recurring` BOOLEAN DEFAULT TRUE COMMENT '정기 배송 여부 (TRUE: 주기마다 자동, FALSE: 1회만)',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  INDEX `idx_subscription` (`subscription_id`),
  INDEX `idx_item_ref` (`item_type`, `item_id_ref`),
  FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions`(`subscription_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='구독 항목(Subscription Item) 테이블 - 구독에 포함된 상품 항목';

-- 4.3 구독 주기(Subscription Cycle) 테이블
CREATE TABLE `subscription_cycles` (
  `cycle_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '주기 ID (PK)',
  `subscription_id` INT NOT NULL COMMENT '구독 ID (FK -> subscriptions)',
  `cycle_number` INT NOT NULL COMMENT '주기 회차 (1회차, 2회차 ...)',
  `cycle_start_date` DATE NOT NULL COMMENT '주기 시작일',
  `cycle_end_date` DATE NOT NULL COMMENT '주기 종료일',
  `shipment_due_date` DATE COMMENT '배송 예정일',
  `status` ENUM('PENDING', 'PROCESSING', 'SHIPPED', 'COMPLETED', 'SKIPPED') DEFAULT 'PENDING' COMMENT '주기 상태 (PENDING: 대기, PROCESSING: 처리중, SHIPPED: 배송됨, COMPLETED: 완료, SKIPPED: 건너뜀)',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',
  INDEX `idx_subscription` (`subscription_id`),
  INDEX `idx_cycle_start` (`cycle_start_date`),
  INDEX `idx_status` (`status`),
  FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions`(`subscription_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='구독 주기(Subscription Cycle) 테이블 - 2개월마다 반복되는 구독 주기 정보';

-- ============================================================
-- 5. 작업지시 및 출고/배송
-- ============================================================

-- 5.1 작업지시서(Work Order) 테이블
CREATE TABLE `work_orders` (
  `work_order_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '작업지시서 ID (PK)',
  `order_number` VARCHAR(50) NOT NULL UNIQUE COMMENT '작업지시서 번호 (고유, 예: WO-2025-0001)',
  `subscription_id` INT COMMENT '연관 구독 ID (FK -> subscriptions, NULL: 단품 주문)',
  `cycle_id` INT COMMENT '연관 주기 ID (FK -> subscription_cycles)',
  `customer_id` INT NOT NULL COMMENT '고객 ID (FK -> customers)',
  `order_type` ENUM('SUBSCRIPTION', 'CONTENT_CUSTOM', 'ADDITIONAL') DEFAULT 'SUBSCRIPTION' COMMENT '작업 유형 (SUBSCRIPTION: 정기 구독, CONTENT_CUSTOM: 콘텐츠 커스터마이징, ADDITIONAL: 추가 구매)',
  `status` ENUM('PENDING', 'IN_PROGRESS', 'PRINTING', 'READY', 'SHIPPED', 'COMPLETED', 'CANCELLED') DEFAULT 'PENDING' COMMENT '상태 (PENDING: 대기, IN_PROGRESS: 작업중, PRINTING: 프린팅중, READY: 출고 대기, SHIPPED: 배송됨, COMPLETED: 완료, CANCELLED: 취소)',
  `due_date` DATE COMMENT '마감일',
  `notes` TEXT COMMENT '작업 메모',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',
  `created_by` INT COMMENT '생성자 user_id',
  `updated_by` INT COMMENT '수정자 user_id',
  INDEX `idx_subscription` (`subscription_id`),
  INDEX `idx_cycle` (`cycle_id`),
  INDEX `idx_customer` (`customer_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_due_date` (`due_date`),
  FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions`(`subscription_id`) ON DELETE SET NULL,
  FOREIGN KEY (`cycle_id`) REFERENCES `subscription_cycles`(`cycle_id`) ON DELETE SET NULL,
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`customer_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='작업지시서(Work Order) 테이블 - 구독 주기 또는 추가 주문에 대한 작업 지시';

-- 5.2 작업지시서 항목(Work Order Item) 테이블
CREATE TABLE `work_order_items` (
  `item_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '작업 항목 ID (PK)',
  `work_order_id` INT NOT NULL COMMENT '작업지시서 ID (FK -> work_orders)',
  `item_type` ENUM('DEVICE', 'SCENT', 'CONTENT', 'PART', 'ACCESSORY') NOT NULL COMMENT '항목 타입',
  `item_id_ref` INT NOT NULL COMMENT '항목 참조 ID',
  `quantity` INT NOT NULL DEFAULT 1 COMMENT '수량',
  `notes` TEXT COMMENT '항목별 메모',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  INDEX `idx_work_order` (`work_order_id`),
  INDEX `idx_item_ref` (`item_type`, `item_id_ref`),
  FOREIGN KEY (`work_order_id`) REFERENCES `work_orders`(`work_order_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='작업지시서 항목(Work Order Item) 테이블 - 작업지시서에 포함된 상품 항목';

-- 5.3 출고/배송(Shipment) 테이블
CREATE TABLE `shipments` (
  `shipment_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '배송 ID (PK)',
  `work_order_id` INT NOT NULL COMMENT '작업지시서 ID (FK -> work_orders)',
  `shipment_number` VARCHAR(50) NOT NULL UNIQUE COMMENT '배송 번호 (고유, 예: SHP-2025-0001)',
  `customer_id` INT NOT NULL COMMENT '고객 ID (FK -> customers)',
  `site_id` INT COMMENT '배송 현장 ID (FK -> customer_sites)',
  `recipient_name` VARCHAR(100) NOT NULL COMMENT '수령인 이름',
  `recipient_phone` VARCHAR(20) NOT NULL COMMENT '수령인 연락처',
  `shipping_address` VARCHAR(500) NOT NULL COMMENT '배송 주소',
  `courier_company` VARCHAR(100) COMMENT '택배사',
  `tracking_number` VARCHAR(100) COMMENT '송장번호',
  `shipped_date` DATETIME COMMENT '출고 일시',
  `delivered_date` DATETIME COMMENT '배송 완료 일시',
  `status` ENUM('PENDING', 'SHIPPED', 'IN_TRANSIT', 'DELIVERED', 'FAILED', 'RETURNED') DEFAULT 'PENDING' COMMENT '배송 상태 (PENDING: 출고 대기, SHIPPED: 출고됨, IN_TRANSIT: 배송중, DELIVERED: 배송 완료, FAILED: 배송 실패, RETURNED: 반송)',
  `notes` TEXT COMMENT '배송 메모',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',
  `created_by` INT COMMENT '생성자 user_id',
  `updated_by` INT COMMENT '수정자 user_id',
  INDEX `idx_work_order` (`work_order_id`),
  INDEX `idx_customer` (`customer_id`),
  INDEX `idx_site` (`site_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_shipped_date` (`shipped_date`),
  FOREIGN KEY (`work_order_id`) REFERENCES `work_orders`(`work_order_id`) ON DELETE RESTRICT,
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`customer_id`) ON DELETE RESTRICT,
  FOREIGN KEY (`site_id`) REFERENCES `customer_sites`(`site_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='출고/배송(Shipment) 테이블 - 작업지시서 기반 출고 및 배송 정보';

-- 5.4 배송 항목(Shipment Item) 테이블
CREATE TABLE `shipment_items` (
  `item_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '배송 항목 ID (PK)',
  `shipment_id` INT NOT NULL COMMENT '배송 ID (FK -> shipments)',
  `work_order_item_id` INT NOT NULL COMMENT '작업지시서 항목 ID (FK -> work_order_items)',
  `quantity` INT NOT NULL DEFAULT 1 COMMENT '배송 수량',
  `serial_id` INT COMMENT '디스펜서 시리얼 ID (FK -> device_serials, 기기인 경우)',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  INDEX `idx_shipment` (`shipment_id`),
  INDEX `idx_work_order_item` (`work_order_item_id`),
  INDEX `idx_serial` (`serial_id`),
  FOREIGN KEY (`shipment_id`) REFERENCES `shipments`(`shipment_id`) ON DELETE CASCADE,
  FOREIGN KEY (`work_order_item_id`) REFERENCES `work_order_items`(`item_id`) ON DELETE RESTRICT,
  FOREIGN KEY (`serial_id`) REFERENCES `device_serials`(`serial_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='배송 항목(Shipment Item) 테이블 - 배송에 포함된 상품 항목 상세';

-- ============================================================
-- 6. 콘텐츠 수정 요청 관리
-- ============================================================

-- 6.1 콘텐츠 수정 요청(Content Request) 테이블
CREATE TABLE `content_requests` (
  `request_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '요청 ID (PK)',
  `request_number` VARCHAR(50) NOT NULL UNIQUE COMMENT '요청 번호 (고유, 예: CR-2025-0001)',
  `customer_id` INT NOT NULL COMMENT '고객 ID (FK -> customers)',
  `base_content_id` INT COMMENT '기본 콘텐츠 ID (FK -> contents, NULL: 완전 신규)',
  `customization_level` ENUM('PRINTING', 'BASIC', 'DELUXE', 'PREMIUM') NOT NULL COMMENT '커스터마이징 등급 (PRINTING: 문구만, BASIC: 이미지+문구, DELUXE: 부분 재디자인, PREMIUM: 완전 신규)',
  `price` DECIMAL(10,2) NOT NULL COMMENT '수정 비용 (원)',
  `request_detail` TEXT NOT NULL COMMENT '수정 요청 내용',
  `reference_files` JSON COMMENT '참고 파일 URL 목록 (JSON 배열)',
  `assigned_lucid_user_id` INT COMMENT '배정된 루시드 user_id (FK -> users)',
  `status` ENUM('PENDING', 'ASSIGNED', 'IN_PROGRESS', 'REVIEW', 'REVISION', 'APPROVED', 'COMPLETED', 'CANCELLED') DEFAULT 'PENDING' COMMENT '상태 (PENDING: 배정 대기, ASSIGNED: 루시드 배정됨, IN_PROGRESS: 작업중, REVIEW: 고객 검토, REVISION: 재수정, APPROVED: 승인됨, COMPLETED: 완료, CANCELLED: 취소)',
  `revision_count` TINYINT DEFAULT 0 COMMENT '재수정 요청 횟수',
  `max_revision` TINYINT DEFAULT 2 COMMENT '최대 재수정 허용 횟수',
  `due_date` DATE COMMENT '마감일',
  `approved_date` DATETIME COMMENT '고객 승인 일시',
  `completed_date` DATETIME COMMENT '작업 완료 일시',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',
  `created_by` INT COMMENT '생성자 user_id',
  `updated_by` INT COMMENT '수정자 user_id',
  INDEX `idx_customer` (`customer_id`),
  INDEX `idx_base_content` (`base_content_id`),
  INDEX `idx_lucid` (`assigned_lucid_user_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_due_date` (`due_date`),
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`customer_id`) ON DELETE RESTRICT,
  FOREIGN KEY (`base_content_id`) REFERENCES `contents`(`content_id`) ON DELETE SET NULL,
  FOREIGN KEY (`assigned_lucid_user_id`) REFERENCES `users`(`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='콘텐츠 수정 요청(Content Request) 테이블 - 고객의 콘텐츠 커스터마이징 요청';

-- 6.2 콘텐츠 리비전(Content Revision) 테이블
CREATE TABLE `content_revisions` (
  `revision_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '리비전 ID (PK)',
  `request_id` INT NOT NULL COMMENT '요청 ID (FK -> content_requests)',
  `revision_number` TINYINT NOT NULL COMMENT '리비전 버전 (1, 2, 3 ...)',
  `file_url` VARCHAR(500) COMMENT '시안 파일 URL',
  `thumbnail_url` VARCHAR(500) COMMENT '썸네일 URL',
  `lucid_notes` TEXT COMMENT '루시드 작업 메모',
  `customer_feedback` TEXT COMMENT '고객 피드백',
  `feedback_date` DATETIME COMMENT '피드백 입력 일시',
  `is_approved` BOOLEAN DEFAULT FALSE COMMENT '승인 여부 (TRUE: 승인, FALSE: 재수정 필요)',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시 (시안 업로드 일시)',
  INDEX `idx_request` (`request_id`),
  FOREIGN KEY (`request_id`) REFERENCES `content_requests`(`request_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='콘텐츠 리비전(Content Revision) 테이블 - 콘텐츠 수정 작업의 버전별 시안 및 피드백';

-- ============================================================
-- 7. 재고 관리
-- ============================================================

-- 7.1 재고(Inventory) 테이블
CREATE TABLE `inventory` (
  `inventory_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '재고 ID (PK)',
  `item_type` ENUM('DEVICE', 'SCENT', 'CONTENT', 'PART', 'ACCESSORY') NOT NULL COMMENT '상품 타입',
  `item_id_ref` INT NOT NULL COMMENT '상품 참조 ID',
  `location` VARCHAR(100) COMMENT '보관 위치 (창고명, 구역 등)',
  `quantity` INT NOT NULL DEFAULT 0 COMMENT '현재 재고 수량',
  `reserved_quantity` INT DEFAULT 0 COMMENT '예약된 수량 (출고 예정)',
  `minimum_stock` INT DEFAULT 0 COMMENT '안전 재고 수량 (알림 기준)',
  `last_stocked_date` DATE COMMENT '최근 입고일',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',
  UNIQUE KEY `unique_item` (`item_type`, `item_id_ref`, `location`),
  INDEX `idx_item` (`item_type`, `item_id_ref`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='재고(Inventory) 테이블 - 상품별 현재 재고 수량 관리';

-- 7.2 재고 트랜잭션(Inventory Transaction) 테이블
CREATE TABLE `inventory_transactions` (
  `transaction_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '트랜잭션 ID (PK)',
  `inventory_id` INT NOT NULL COMMENT '재고 ID (FK -> inventory)',
  `transaction_type` ENUM('IN', 'OUT', 'ADJUSTMENT', 'RETURN') NOT NULL COMMENT '트랜잭션 유형 (IN: 입고, OUT: 출고, ADJUSTMENT: 재고조정, RETURN: 반품)',
  `quantity` INT NOT NULL COMMENT '수량 (양수: 증가, 음수: 감소)',
  `reference_type` ENUM('WORK_ORDER', 'SHIPMENT', 'PURCHASE', 'MANUAL') COMMENT '참조 유형',
  `reference_id` INT COMMENT '참조 ID (work_order_id, shipment_id 등)',
  `notes` TEXT COMMENT '트랜잭션 메모',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  `created_by` INT COMMENT '생성자 user_id',
  INDEX `idx_inventory` (`inventory_id`),
  INDEX `idx_reference` (`reference_type`, `reference_id`),
  INDEX `idx_created_at` (`created_at`),
  FOREIGN KEY (`inventory_id`) REFERENCES `inventory`(`inventory_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='재고 트랜잭션(Inventory Transaction) 테이블 - 재고 입출고 이력';

-- ============================================================
-- 8. 청구/결제/정산
-- ============================================================

-- 8.1 청구서(Invoice) 테이블
CREATE TABLE `invoices` (
  `invoice_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '청구서 ID (PK)',
  `invoice_number` VARCHAR(50) NOT NULL UNIQUE COMMENT '청구서 번호 (고유, 예: INV-2025-0001)',
  `customer_id` INT NOT NULL COMMENT '고객 ID (FK -> customers)',
  `subscription_id` INT COMMENT '구독 ID (FK -> subscriptions, NULL: 단품 주문)',
  `cycle_id` INT COMMENT '주기 ID (FK -> subscription_cycles)',
  `invoice_date` DATE NOT NULL COMMENT '청구일',
  `due_date` DATE NOT NULL COMMENT '납기일',
  `total_amount` DECIMAL(10,2) NOT NULL COMMENT '총 청구 금액 (원)',
  `tax_amount` DECIMAL(10,2) DEFAULT 0.00 COMMENT '세금 (원)',
  `discount_amount` DECIMAL(10,2) DEFAULT 0.00 COMMENT '할인 금액 (원)',
  `grand_total` DECIMAL(10,2) NOT NULL COMMENT '최종 청구 금액 (원)',
  `status` ENUM('PENDING', 'PAID', 'OVERDUE', 'CANCELLED') DEFAULT 'PENDING' COMMENT '청구 상태 (PENDING: 미결제, PAID: 결제완료, OVERDUE: 연체, CANCELLED: 취소)',
  `paid_date` DATETIME COMMENT '결제 완료 일시',
  `notes` TEXT COMMENT '청구 메모',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',
  `created_by` INT COMMENT '생성자 user_id',
  `updated_by` INT COMMENT '수정자 user_id',
  INDEX `idx_customer` (`customer_id`),
  INDEX `idx_subscription` (`subscription_id`),
  INDEX `idx_cycle` (`cycle_id`),
  INDEX `idx_invoice_date` (`invoice_date`),
  INDEX `idx_status` (`status`),
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`customer_id`) ON DELETE RESTRICT,
  FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions`(`subscription_id`) ON DELETE SET NULL,
  FOREIGN KEY (`cycle_id`) REFERENCES `subscription_cycles`(`cycle_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='청구서(Invoice) 테이블 - 고객 대상 청구 정보';

-- 8.2 청구서 항목(Invoice Item) 테이블
CREATE TABLE `invoice_items` (
  `item_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '청구 항목 ID (PK)',
  `invoice_id` INT NOT NULL COMMENT '청구서 ID (FK -> invoices)',
  `item_type` ENUM('SUBSCRIPTION_FEE', 'CONTENT_CUSTOM', 'SCENT', 'PART', 'ACCESSORY', 'OTHER') NOT NULL COMMENT '항목 타입 (SUBSCRIPTION_FEE: 구독료, CONTENT_CUSTOM: 콘텐츠 수정, SCENT: 향 추가구매, PART: 부자재, ACCESSORY: 악세사리, OTHER: 기타)',
  `item_id_ref` INT COMMENT '항목 참조 ID (content_request_id, scent_id 등)',
  `description` VARCHAR(200) NOT NULL COMMENT '항목 설명',
  `quantity` INT NOT NULL DEFAULT 1 COMMENT '수량',
  `unit_price` DECIMAL(10,2) NOT NULL COMMENT '단가 (원)',
  `amount` DECIMAL(10,2) NOT NULL COMMENT '금액 (원, quantity × unit_price)',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  INDEX `idx_invoice` (`invoice_id`),
  INDEX `idx_item_ref` (`item_type`, `item_id_ref`),
  FOREIGN KEY (`invoice_id`) REFERENCES `invoices`(`invoice_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='청구서 항목(Invoice Item) 테이블 - 청구서 내 세부 항목';

-- 8.3 결제 트랜잭션(Payment Transaction) 테이블
CREATE TABLE `payment_transactions` (
  `transaction_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '트랜잭션 ID (PK)',
  `invoice_id` INT NOT NULL COMMENT '청구서 ID (FK -> invoices)',
  `transaction_number` VARCHAR(100) NOT NULL UNIQUE COMMENT '거래 번호 (PG사 제공)',
  `payment_method` ENUM('CMS', 'CARD', 'TRANSFER', 'OTHER') NOT NULL COMMENT '결제 수단',
  `amount` DECIMAL(10,2) NOT NULL COMMENT '결제 금액 (원)',
  `status` ENUM('PENDING', 'SUCCESS', 'FAILED', 'REFUNDED') DEFAULT 'PENDING' COMMENT '트랜잭션 상태 (PENDING: 처리중, SUCCESS: 성공, FAILED: 실패, REFUNDED: 환불됨)',
  `pg_response` JSON COMMENT 'PG사 응답 데이터 (JSON)',
  `error_message` TEXT COMMENT '오류 메시지 (실패 시)',
  `transaction_date` DATETIME NOT NULL COMMENT '거래 일시',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  INDEX `idx_invoice` (`invoice_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_transaction_date` (`transaction_date`),
  FOREIGN KEY (`invoice_id`) REFERENCES `invoices`(`invoice_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='결제 트랜잭션(Payment Transaction) 테이블 - 결제 거래 상세 이력';

-- 8.4 정산(Settlement) 테이블
CREATE TABLE `settlements` (
  `settlement_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '정산 ID (PK)',
  `settlement_number` VARCHAR(50) NOT NULL UNIQUE COMMENT '정산 번호 (고유, 예: SET-2025-0001)',
  `settlement_type` ENUM('VENDOR', 'SALES_REP', 'LUCID') NOT NULL COMMENT '정산 대상 타입 (VENDOR: 밴더, SALES_REP: 영업사원, LUCID: 루시드)',
  `target_user_id` INT COMMENT '정산 대상 user_id (FK -> users)',
  `target_vendor_id` INT COMMENT '정산 대상 vendor_id (FK -> vendors, 밴더인 경우)',
  `settlement_month` VARCHAR(7) NOT NULL COMMENT '정산 월 (YYYY-MM 형식)',
  `total_sales` DECIMAL(10,2) DEFAULT 0.00 COMMENT '총 매출 (원)',
  `commission_amount` DECIMAL(10,2) DEFAULT 0.00 COMMENT '커미션 금액 (원)',
  `incentive_amount` DECIMAL(10,2) DEFAULT 0.00 COMMENT '인센티브 금액 (원)',
  `total_amount` DECIMAL(10,2) NOT NULL COMMENT '총 정산 금액 (원)',
  `status` ENUM('PENDING', 'CALCULATED', 'PAID', 'CANCELLED') DEFAULT 'PENDING' COMMENT '정산 상태 (PENDING: 대기, CALCULATED: 계산완료, PAID: 지급완료, CANCELLED: 취소)',
  `calculated_date` DATE COMMENT '정산 계산일',
  `payment_date` DATE COMMENT '정산 지급일',
  `bank_name` VARCHAR(100) COMMENT '지급 은행',
  `account_number` VARCHAR(50) COMMENT '계좌번호',
  `account_holder` VARCHAR(100) COMMENT '예금주',
  `notes` TEXT COMMENT '정산 메모',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',
  `created_by` INT COMMENT '생성자 user_id',
  `updated_by` INT COMMENT '수정자 user_id',
  INDEX `idx_target_user` (`target_user_id`),
  INDEX `idx_target_vendor` (`target_vendor_id`),
  INDEX `idx_type` (`settlement_type`),
  INDEX `idx_month` (`settlement_month`),
  INDEX `idx_status` (`status`),
  FOREIGN KEY (`target_user_id`) REFERENCES `users`(`user_id`) ON DELETE SET NULL,
  FOREIGN KEY (`target_vendor_id`) REFERENCES `vendors`(`vendor_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='정산(Settlement) 테이블 - 밴더/영업사원/루시드 정산 정보';

-- ============================================================
-- 9. 로그 및 이력 관리
-- ============================================================

-- 9.1 디스펜서 로그(Device Log) 테이블
CREATE TABLE `device_logs` (
  `log_id` BIGINT AUTO_INCREMENT PRIMARY KEY COMMENT '로그 ID (PK)',
  `serial_id` INT NOT NULL COMMENT '시리얼 ID (FK -> device_serials)',
  `log_type` ENUM('ONLINE', 'OFFLINE', 'ERROR', 'MAINTENANCE', 'RESET') NOT NULL COMMENT '로그 유형 (ONLINE: 온라인됨, OFFLINE: 오프라인됨, ERROR: 오류, MAINTENANCE: 유지보수, RESET: 초기화)',
  `message` TEXT COMMENT '로그 메시지',
  `metadata` JSON COMMENT '추가 메타데이터 (JSON)',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '로그 발생 일시',
  INDEX `idx_serial` (`serial_id`),
  INDEX `idx_type` (`log_type`),
  INDEX `idx_created_at` (`created_at`),
  FOREIGN KEY (`serial_id`) REFERENCES `device_serials`(`serial_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='디스펜서 로그(Device Log) 테이블 - 기기 상태 변화 및 이벤트 로그';

-- 9.2 콘텐츠 교체 이력(Content Change) 테이블
CREATE TABLE `content_changes` (
  `change_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '교체 ID (PK)',
  `assignment_id` INT NOT NULL COMMENT '디스펜서 배정 ID (FK -> device_assignments)',
  `old_content_id` INT COMMENT '이전 콘텐츠 ID (FK -> contents)',
  `new_content_id` INT NOT NULL COMMENT '새 콘텐츠 ID (FK -> contents)',
  `change_date` DATE NOT NULL COMMENT '교체일',
  `changed_by` INT COMMENT '교체자 user_id',
  `notes` TEXT COMMENT '교체 메모',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  INDEX `idx_assignment` (`assignment_id`),
  INDEX `idx_old_content` (`old_content_id`),
  INDEX `idx_new_content` (`new_content_id`),
  INDEX `idx_change_date` (`change_date`),
  FOREIGN KEY (`assignment_id`) REFERENCES `device_assignments`(`assignment_id`) ON DELETE CASCADE,
  FOREIGN KEY (`old_content_id`) REFERENCES `contents`(`content_id`) ON DELETE SET NULL,
  FOREIGN KEY (`new_content_id`) REFERENCES `contents`(`content_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='콘텐츠 교체 이력(Content Change) 테이블 - 디스펜서 콘텐츠 교체 히스토리';

-- 9.3 향 교체 이력(Scent Change) 테이블
CREATE TABLE `scent_changes` (
  `change_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '교체 ID (PK)',
  `assignment_id` INT NOT NULL COMMENT '디스펜서 배정 ID (FK -> device_assignments)',
  `old_scent_id` INT COMMENT '이전 향 ID (FK -> scents)',
  `new_scent_id` INT NOT NULL COMMENT '새 향 ID (FK -> scents)',
  `change_date` DATE NOT NULL COMMENT '교체일',
  `changed_by` INT COMMENT '교체자 user_id',
  `notes` TEXT COMMENT '교체 메모',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  INDEX `idx_assignment` (`assignment_id`),
  INDEX `idx_old_scent` (`old_scent_id`),
  INDEX `idx_new_scent` (`new_scent_id`),
  INDEX `idx_change_date` (`change_date`),
  FOREIGN KEY (`assignment_id`) REFERENCES `device_assignments`(`assignment_id`) ON DELETE CASCADE,
  FOREIGN KEY (`old_scent_id`) REFERENCES `scents`(`scent_id`) ON DELETE SET NULL,
  FOREIGN KEY (`new_scent_id`) REFERENCES `scents`(`scent_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='향 교체 이력(Scent Change) 테이블 - 디스펜서 향 카트리지 교체 히스토리';

-- 9.4 감사 로그(Audit Log) 테이블
CREATE TABLE `audit_logs` (
  `log_id` BIGINT AUTO_INCREMENT PRIMARY KEY COMMENT '로그 ID (PK)',
  `user_id` INT COMMENT '작업자 user_id (FK -> users)',
  `action` VARCHAR(100) NOT NULL COMMENT '액션 (LOGIN, LOGOUT, CREATE, UPDATE, DELETE 등)',
  `table_name` VARCHAR(100) COMMENT '대상 테이블명',
  `record_id` INT COMMENT '대상 레코드 ID',
  `old_values` JSON COMMENT '변경 전 값 (JSON)',
  `new_values` JSON COMMENT '변경 후 값 (JSON)',
  `ip_address` VARCHAR(45) COMMENT 'IP 주소 (IPv4/IPv6)',
  `user_agent` VARCHAR(500) COMMENT 'User Agent',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '로그 생성 일시',
  INDEX `idx_user` (`user_id`),
  INDEX `idx_action` (`action`),
  INDEX `idx_table` (`table_name`),
  INDEX `idx_created_at` (`created_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='감사 로그(Audit Log) 테이블 - 시스템 내 모든 중요 액션 기록';

-- ============================================================
-- 10. 기타 (티켓, 알림, 설정)
-- ============================================================

-- 10.1 티켓(Ticket) 테이블
CREATE TABLE `tickets` (
  `ticket_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '티켓 ID (PK)',
  `ticket_number` VARCHAR(50) NOT NULL UNIQUE COMMENT '티켓 번호 (고유, 예: T-2025-0001)',
  `customer_id` INT NOT NULL COMMENT '고객 ID (FK -> customers)',
  `category` ENUM('TECHNICAL', 'BILLING', 'DELIVERY', 'CONTENT', 'GENERAL') NOT NULL COMMENT '카테고리 (TECHNICAL: 기술지원, BILLING: 결제, DELIVERY: 배송, CONTENT: 콘텐츠, GENERAL: 일반)',
  `priority` ENUM('LOW', 'NORMAL', 'HIGH', 'URGENT') DEFAULT 'NORMAL' COMMENT '긴급도 (LOW: 낮음, NORMAL: 보통, HIGH: 높음, URGENT: 긴급)',
  `subject` VARCHAR(200) NOT NULL COMMENT '제목',
  `description` TEXT NOT NULL COMMENT '문의 내용',
  `status` ENUM('OPEN', 'IN_PROGRESS', 'ON_HOLD', 'RESOLVED', 'CLOSED') DEFAULT 'OPEN' COMMENT '상태 (OPEN: 접수, IN_PROGRESS: 처리중, ON_HOLD: 보류, RESOLVED: 해결됨, CLOSED: 종료)',
  `assigned_to` INT COMMENT '담당자 user_id (FK -> users)',
  `resolved_date` DATETIME COMMENT '해결 일시',
  `closed_date` DATETIME COMMENT '종료 일시',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',
  `created_by` INT COMMENT '생성자 user_id',
  INDEX `idx_customer` (`customer_id`),
  INDEX `idx_assigned_to` (`assigned_to`),
  INDEX `idx_status` (`status`),
  INDEX `idx_priority` (`priority`),
  INDEX `idx_created_at` (`created_at`),
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`customer_id`) ON DELETE RESTRICT,
  FOREIGN KEY (`assigned_to`) REFERENCES `users`(`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='티켓(Ticket) 테이블 - 고객 문의 및 지원 요청 관리';

-- 10.2 티켓 댓글(Ticket Comment) 테이블
CREATE TABLE `ticket_comments` (
  `comment_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '댓글 ID (PK)',
  `ticket_id` INT NOT NULL COMMENT '티켓 ID (FK -> tickets)',
  `user_id` INT NOT NULL COMMENT '작성자 user_id (FK -> users)',
  `comment` TEXT NOT NULL COMMENT '댓글 내용',
  `is_internal` BOOLEAN DEFAULT FALSE COMMENT '내부 메모 여부 (TRUE: 관리자만 보임, FALSE: 고객도 보임)',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  INDEX `idx_ticket` (`ticket_id`),
  INDEX `idx_user` (`user_id`),
  INDEX `idx_created_at` (`created_at`),
  FOREIGN KEY (`ticket_id`) REFERENCES `tickets`(`ticket_id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='티켓 댓글(Ticket Comment) 테이블 - 티켓 내 대화 및 메모';

-- 10.3 알림(Notification) 테이블
CREATE TABLE `notifications` (
  `notification_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '알림 ID (PK)',
  `user_id` INT NOT NULL COMMENT '수신자 user_id (FK -> users)',
  `notification_type` ENUM('SYSTEM', 'ORDER', 'PAYMENT', 'SHIPMENT', 'TICKET', 'REMINDER') NOT NULL COMMENT '알림 유형 (SYSTEM: 시스템, ORDER: 주문, PAYMENT: 결제, SHIPMENT: 배송, TICKET: 티켓, REMINDER: 리마인더)',
  `title` VARCHAR(200) NOT NULL COMMENT '알림 제목',
  `message` TEXT NOT NULL COMMENT '알림 내용',
  `link_url` VARCHAR(500) COMMENT '링크 URL (클릭 시 이동)',
  `is_read` BOOLEAN DEFAULT FALSE COMMENT '읽음 여부',
  `read_at` DATETIME COMMENT '읽은 일시',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  INDEX `idx_user` (`user_id`),
  INDEX `idx_is_read` (`is_read`),
  INDEX `idx_created_at` (`created_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='알림(Notification) 테이블 - 사용자별 알림 메시지';

-- 10.4 시스템 설정(Setting) 테이블
CREATE TABLE `settings` (
  `setting_id` INT AUTO_INCREMENT PRIMARY KEY COMMENT '설정 ID (PK)',
  `setting_key` VARCHAR(100) NOT NULL UNIQUE COMMENT '설정 키 (고유, 예: commission_rate, monthly_fee)',
  `setting_value` TEXT NOT NULL COMMENT '설정 값',
  `setting_type` ENUM('STRING', 'INTEGER', 'FLOAT', 'BOOLEAN', 'JSON') DEFAULT 'STRING' COMMENT '설정 값 타입',
  `description` TEXT COMMENT '설정 설명',
  `is_editable` BOOLEAN DEFAULT TRUE COMMENT '수정 가능 여부',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',
  `updated_by` INT COMMENT '수정자 user_id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='시스템 설정(Setting) 테이블 - 시스템 전역 설정 값';

-- ============================================================
-- 11. 인덱스 최적화 (복합 인덱스 추가)
-- ============================================================

-- 구독 관련 복합 인덱스
CREATE INDEX idx_subscription_customer_status ON subscriptions(customer_id, status);
CREATE INDEX idx_cycle_subscription_status ON subscription_cycles(subscription_id, status);

-- 정산 관련 복합 인덱스
CREATE INDEX idx_settlement_type_month ON settlements(settlement_type, settlement_month);

-- 청구서 관련 복합 인덱스
CREATE INDEX idx_invoice_customer_status ON invoices(customer_id, status);

-- 재고 관련 복합 인덱스
CREATE INDEX idx_inventory_item ON inventory(item_type, item_id_ref);

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- 스키마 생성 완료
-- ============================================================
