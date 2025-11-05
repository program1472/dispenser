-- ==========================================
-- Dispenser Database Schema
-- 통합 데이터베이스 스키마 (전체 테이블 + 트리거 + 뷰 + 샘플 데이터)
-- ==========================================

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- ==========================================
-- 1. roles 테이블 (역할 정의)
-- ==========================================
CREATE TABLE IF NOT EXISTS `roles` (
    `role_id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(20) NOT NULL UNIQUE COMMENT 'HQ, VENDOR, CUSTOMER',
    `name` VARCHAR(50) NOT NULL COMMENT '역할명',
    `description` TEXT COMMENT '역할 설명',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='사용자 역할 테이블';

-- ==========================================
-- 2. vendors 테이블 (벤더 정보)
-- ==========================================
CREATE TABLE IF NOT EXISTS `vendors` (
    `vendor_id` VARCHAR(20) PRIMARY KEY COMMENT 'VYYYYMMDDNNNN 형식',
    `name` VARCHAR(100) NOT NULL COMMENT '벤더명',
    `company_name` VARCHAR(100) COMMENT '회사명',
    `business_number` VARCHAR(20) COMMENT '사업자번호',
    `representative` VARCHAR(50) COMMENT '대표자명',
    `email` VARCHAR(100) COMMENT '이메일',
    `phone` VARCHAR(20) COMMENT '전화번호',
    `address` TEXT COMMENT '주소',
    `is_active` TINYINT(1) DEFAULT 1 COMMENT '활성 상태',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_name` (`name`),
    INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='벤더 정보 테이블';

-- ==========================================
-- 3. customers 테이블 (고객 정보)
-- ==========================================
CREATE TABLE IF NOT EXISTS `customers` (
    `customer_id` VARCHAR(20) PRIMARY KEY COMMENT 'CYYYYMMDDNNNN 형식',
    `user_id` BIGINT UNSIGNED NULL COMMENT '연결된 사용자 ID',
    `vendor_id` VARCHAR(20) NULL COMMENT '소속 벤더 ID',
    `company_name` VARCHAR(100) NOT NULL COMMENT '회사명',
    `business_number` VARCHAR(20) COMMENT '사업자번호',
    `representative` VARCHAR(50) COMMENT '대표자명',
    `email` VARCHAR(100) COMMENT '이메일',
    `phone` VARCHAR(20) COMMENT '전화번호',
    `address` TEXT COMMENT '주소',
    `billing_contact` JSON COMMENT '청구 연락처',
    `shipping_contact` JSON COMMENT '배송 연락처',
    `contract_date` DATE COMMENT '계약일',
    `contract_end_date` DATE COMMENT '계약 종료일',
    `is_active` TINYINT(1) DEFAULT 1 COMMENT '활성 상태',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_vendor_id` (`vendor_id`),
    INDEX `idx_company_name` (`company_name`),
    INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='고객 정보 테이블';

-- ==========================================
-- 4. sites 테이블 (사업장 정보)
-- ==========================================
CREATE TABLE IF NOT EXISTS `sites` (
    `site_id` VARCHAR(20) PRIMARY KEY COMMENT 'SYYYYMMDDNNNN 형식',
    `customer_id` VARCHAR(20) NOT NULL COMMENT '고객 ID',
    `site_name` VARCHAR(100) NOT NULL COMMENT '사업장명',
    `address` TEXT COMMENT '주소',
    `contact_name` VARCHAR(50) COMMENT '담당자명',
    `contact_phone` VARCHAR(20) COMMENT '담당자 전화',
    `is_main` TINYINT(1) DEFAULT 0 COMMENT '본점 여부',
    `is_active` TINYINT(1) DEFAULT 1 COMMENT '활성 상태',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_customer_id` (`customer_id`),
    INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='사업장 정보 테이블';

-- ==========================================
-- 5. users 테이블 (사용자 정보)
-- ==========================================
CREATE TABLE IF NOT EXISTS `users` (
    `user_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `role_id` INT UNSIGNED NOT NULL COMMENT '역할 ID',
    `vendor_id` VARCHAR(20) NULL COMMENT '벤더 ID',
    `customer_id` VARCHAR(20) NULL COMMENT '고객 ID',
    `userid` VARCHAR(50) UNIQUE COMMENT '로그인 ID',
    `email` VARCHAR(100) NOT NULL UNIQUE COMMENT '이메일',
    `password` VARCHAR(255) NOT NULL COMMENT '비밀번호 (bcrypt)',
    `name` VARCHAR(50) NOT NULL COMMENT '이름',
    `phone` VARCHAR(20) COMMENT '전화번호',
    `is_active` TINYINT(1) DEFAULT 1 COMMENT '활성 상태',
    `last_login` TIMESTAMP NULL COMMENT '마지막 로그인',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_role_id` (`role_id`),
    INDEX `idx_vendor_id` (`vendor_id`),
    INDEX `idx_customer_id` (`customer_id`),
    INDEX `idx_email` (`email`),
    INDEX `idx_userid` (`userid`),
    INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='사용자 정보 테이블';

-- ==========================================
-- 6. user_extra 테이블 (사용자 추가 정보)
-- ==========================================
CREATE TABLE IF NOT EXISTS `user_extra` (
    `extra_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL COMMENT '사용자 ID',
    `userid` VARCHAR(50) COMMENT '추가 로그인 ID',
    `profile_image` VARCHAR(255) COMMENT '프로필 이미지 URL',
    `department` VARCHAR(50) COMMENT '부서',
    `position` VARCHAR(50) COMMENT '직급',
    `extra_data` JSON COMMENT '추가 데이터',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_user_id` (`user_id`),
    INDEX `idx_userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='사용자 추가 정보 테이블';

-- ==========================================
-- 7. user_consents 테이블 (사용자 동의 내역)
-- ==========================================
CREATE TABLE IF NOT EXISTS `user_consents` (
    `consent_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL COMMENT '사용자 ID',
    `consent_type` VARCHAR(50) NOT NULL COMMENT '동의 타입 (terms, privacy, marketing)',
    `consented` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '동의 여부',
    `consent_date` TIMESTAMP NULL COMMENT '동의 일시',
    `ip_address` VARCHAR(45) COMMENT '동의 시 IP',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_consent_type` (`consent_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='사용자 동의 내역 테이블';

-- ==========================================
-- 8. audit_log 테이블 (감사 로그)
-- ==========================================
CREATE TABLE IF NOT EXISTS `audit_log` (
    `log_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `actor_user_id` BIGINT UNSIGNED COMMENT '행위자 사용자 ID',
    `action` VARCHAR(50) NOT NULL COMMENT '행위',
    `entity_type` VARCHAR(50) COMMENT '대상 엔터티 타입',
    `entity_id` VARCHAR(50) COMMENT '대상 엔터티 ID',
    `description` TEXT COMMENT '상세 설명',
    `ip_address` VARCHAR(45) COMMENT 'IP 주소',
    `user_agent` TEXT COMMENT 'User Agent',
    `result` ENUM('SUCCESS', 'FAILURE') DEFAULT 'SUCCESS' COMMENT '결과',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_actor_user_id` (`actor_user_id`),
    INDEX `idx_action` (`action`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='감사 로그 테이블';

-- ==========================================
-- 9. products 테이블 (제품 정보)
-- ==========================================
CREATE TABLE IF NOT EXISTS `products` (
    `product_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `product_code` VARCHAR(50) UNIQUE NOT NULL COMMENT '제품 코드',
    `product_name` VARCHAR(100) NOT NULL COMMENT '제품명',
    `category` VARCHAR(50) COMMENT '카테고리 (디바이스, 오일, 부품 등)',
    `model` VARCHAR(100) COMMENT '모델명',
    `specification` TEXT COMMENT '제품 사양',
    `unit_price` DECIMAL(15,2) DEFAULT 0 COMMENT '단가',
    `is_active` TINYINT(1) DEFAULT 1 COMMENT '활성 상태',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_product_code` (`product_code`),
    INDEX `idx_category` (`category`),
    INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='제품 정보 테이블';

-- ==========================================
-- 10. scents 테이블 (향 정보)
-- ==========================================
CREATE TABLE IF NOT EXISTS `scents` (
    `scent_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `scent_code` VARCHAR(50) UNIQUE NOT NULL COMMENT '향 코드',
    `scent_name` VARCHAR(100) NOT NULL COMMENT '향 이름',
    `description` TEXT COMMENT '향 설명',
    `is_active` TINYINT(1) DEFAULT 1 COMMENT '활성 상태',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_scent_code` (`scent_code`),
    INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='향 정보 테이블';

-- ==========================================
-- 11. devices 테이블 (디바이스 정보)
-- ==========================================
CREATE TABLE IF NOT EXISTS `devices` (
    `device_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `serial` VARCHAR(50) UNIQUE COMMENT '시리얼 번호 (AP5-250001 형식)',
    `product_id` BIGINT UNSIGNED COMMENT '제품 ID',
    `customer_id` VARCHAR(20) COMMENT '고객 ID',
    `site_id` VARCHAR(20) COMMENT '사업장 ID',
    `status` ENUM('재고', '배송중', '설치완료', '가동중', '고장', '수리중', '회수', '폐기') DEFAULT '재고' COMMENT '디바이스 상태',
    `install_date` DATE COMMENT '설치일',
    `last_maintenance_date` DATE COMMENT '마지막 유지보수일',
    `next_maintenance_date` DATE COMMENT '다음 유지보수 예정일',
    `notes` TEXT COMMENT '비고',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_serial` (`serial`),
    INDEX `idx_customer_id` (`customer_id`),
    INDEX `idx_site_id` (`site_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='디바이스 정보 테이블';

-- ==========================================
-- 12. device_serials 테이블 (디바이스 시리얼 히스토리)
-- ==========================================
CREATE TABLE IF NOT EXISTS `device_serials` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `device_id` BIGINT UNSIGNED NOT NULL COMMENT '디바이스 ID',
    `old_serial` VARCHAR(50) COMMENT '이전 시리얼',
    `new_serial` VARCHAR(50) NOT NULL COMMENT '신규 시리얼',
    `changed_by` BIGINT UNSIGNED COMMENT '변경자 ID',
    `changed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '변경 일시',
    INDEX `idx_device_id` (`device_id`),
    INDEX `idx_old_serial` (`old_serial`),
    INDEX `idx_new_serial` (`new_serial`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='디바이스 시리얼 변경 히스토리';

-- ==========================================
-- 13. device_selections 테이블 (디바이스 향 선택)
-- ==========================================
CREATE TABLE IF NOT EXISTS `device_selections` (
    `selection_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `device_id` BIGINT UNSIGNED NOT NULL COMMENT '디바이스 ID',
    `scent_id` BIGINT UNSIGNED NOT NULL COMMENT '향 ID',
    `slot_number` INT NOT NULL COMMENT '슬롯 번호 (1-4)',
    `is_active` TINYINT(1) DEFAULT 1 COMMENT '활성 상태',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_device_id` (`device_id`),
    INDEX `idx_scent_id` (`scent_id`),
    UNIQUE KEY `uq_device_slot` (`device_id`, `slot_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='디바이스 향 선택 테이블';

-- ==========================================
-- 14. serial_history 테이블 (시리얼 상태 변경 히스토리)
-- ==========================================
CREATE TABLE IF NOT EXISTS `serial_history` (
    `history_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `device_id` BIGINT UNSIGNED NOT NULL COMMENT '디바이스 ID',
    `serial` VARCHAR(50) NOT NULL COMMENT '시리얼 번호',
    `old_status` VARCHAR(50) COMMENT '이전 상태',
    `new_status` VARCHAR(50) NOT NULL COMMENT '신규 상태',
    `changed_by` BIGINT UNSIGNED COMMENT '변경자 ID',
    `changed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '변경 일시',
    `notes` TEXT COMMENT '비고',
    INDEX `idx_device_id` (`device_id`),
    INDEX `idx_serial` (`serial`),
    INDEX `idx_changed_at` (`changed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='시리얼 상태 변경 히스토리';

-- ==========================================
-- 15. subscriptions 테이블 (구독 정보)
-- ==========================================
CREATE TABLE IF NOT EXISTS `subscriptions` (
    `subscription_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `customer_id` VARCHAR(20) NOT NULL COMMENT '고객 ID',
    `site_id` VARCHAR(20) COMMENT '사업장 ID',
    `device_id` BIGINT UNSIGNED COMMENT '디바이스 ID',
    `plan_type` VARCHAR(50) NOT NULL COMMENT '플랜 타입',
    `start_date` DATE NOT NULL COMMENT '시작일',
    `end_date` DATE COMMENT '종료일',
    `monthly_fee` DECIMAL(15,2) DEFAULT 0 COMMENT '월 구독료',
    `status` ENUM('활성', '일시중지', '취소', '만료') DEFAULT '활성' COMMENT '구독 상태',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_customer_id` (`customer_id`),
    INDEX `idx_site_id` (`site_id`),
    INDEX `idx_device_id` (`device_id`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='구독 정보 테이블';

-- ==========================================
-- 16. invoices 테이블 (송장 정보)
-- ==========================================
CREATE TABLE IF NOT EXISTS `invoices` (
    `invoice_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `invoice_number` VARCHAR(50) UNIQUE NOT NULL COMMENT '송장 번호',
    `customer_id` VARCHAR(20) NOT NULL COMMENT '고객 ID',
    `invoice_date` DATE NOT NULL COMMENT '송장 발행일',
    `due_date` DATE COMMENT '납기일',
    `total_amount` DECIMAL(15,2) DEFAULT 0 COMMENT '총 금액',
    `tax_amount` DECIMAL(15,2) DEFAULT 0 COMMENT '세액',
    `status` ENUM('발행', '발송', '결제대기', '결제완료', '연체', '취소') DEFAULT '발행' COMMENT '송장 상태',
    `notes` TEXT COMMENT '비고',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_invoice_number` (`invoice_number`),
    INDEX `idx_customer_id` (`customer_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_invoice_date` (`invoice_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='송장 정보 테이블';

-- ==========================================
-- 17. invoice_items 테이블 (송장 항목)
-- ==========================================
CREATE TABLE IF NOT EXISTS `invoice_items` (
    `item_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `invoice_id` BIGINT UNSIGNED NOT NULL COMMENT '송장 ID',
    `product_id` BIGINT UNSIGNED COMMENT '제품 ID',
    `description` VARCHAR(255) NOT NULL COMMENT '항목 설명',
    `quantity` DECIMAL(10,2) DEFAULT 1 COMMENT '수량',
    `unit_price` DECIMAL(15,2) DEFAULT 0 COMMENT '단가',
    `amount` DECIMAL(15,2) DEFAULT 0 COMMENT '금액',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_invoice_id` (`invoice_id`),
    INDEX `idx_product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='송장 항목 테이블';

-- ==========================================
-- 18. payments 테이블 (결제 정보)
-- ==========================================
CREATE TABLE IF NOT EXISTS `payments` (
    `payment_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `invoice_id` BIGINT UNSIGNED NOT NULL COMMENT '송장 ID',
    `payment_date` DATE NOT NULL COMMENT '결제일',
    `payment_amount` DECIMAL(15,2) NOT NULL COMMENT '결제 금액',
    `payment_method` VARCHAR(50) COMMENT '결제 방법 (계좌이체, 카드, 현금 등)',
    `transaction_id` VARCHAR(100) COMMENT '거래 ID',
    `notes` TEXT COMMENT '비고',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_invoice_id` (`invoice_id`),
    INDEX `idx_payment_date` (`payment_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='결제 정보 테이블';

-- ==========================================
-- 19. lots 테이블 (로트 정보)
-- ==========================================
CREATE TABLE IF NOT EXISTS `lots` (
    `lot_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `lot_number` VARCHAR(50) UNIQUE NOT NULL COMMENT '로트 번호',
    `product_id` BIGINT UNSIGNED NOT NULL COMMENT '제품 ID',
    `manufacture_date` DATE COMMENT '제조일',
    `expiry_date` DATE COMMENT '유효기한',
    `quantity` INT DEFAULT 0 COMMENT '수량',
    `status` ENUM('입고', '재고', '출고', '폐기') DEFAULT '재고' COMMENT '로트 상태',
    `notes` TEXT COMMENT '비고',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_lot_number` (`lot_number`),
    INDEX `idx_product_id` (`product_id`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='로트 정보 테이블';

-- ==========================================
-- 20. shipments 테이블 (배송 정보)
-- ==========================================
CREATE TABLE IF NOT EXISTS `shipments` (
    `shipment_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `shipment_number` VARCHAR(50) UNIQUE NOT NULL COMMENT '배송 번호',
    `customer_id` VARCHAR(20) NOT NULL COMMENT '고객 ID',
    `site_id` VARCHAR(20) COMMENT '사업장 ID',
    `shipment_date` DATE COMMENT '배송일',
    `delivery_date` DATE COMMENT '배달 완료일',
    `status` ENUM('준비중', '배송중', '배송완료', '배송실패', '반송') DEFAULT '준비중' COMMENT '배송 상태',
    `tracking_number` VARCHAR(100) COMMENT '운송장 번호',
    `courier` VARCHAR(50) COMMENT '택배사',
    `notes` TEXT COMMENT '비고',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_shipment_number` (`shipment_number`),
    INDEX `idx_customer_id` (`customer_id`),
    INDEX `idx_site_id` (`site_id`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='배송 정보 테이블';

-- ==========================================
-- 21. shipment_items 테이블 (배송 항목)
-- ==========================================
CREATE TABLE IF NOT EXISTS `shipment_items` (
    `item_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `shipment_id` BIGINT UNSIGNED NOT NULL COMMENT '배송 ID',
    `product_id` BIGINT UNSIGNED NOT NULL COMMENT '제품 ID',
    `lot_id` BIGINT UNSIGNED COMMENT '로트 ID',
    `quantity` INT NOT NULL COMMENT '수량',
    `notes` TEXT COMMENT '비고',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_shipment_id` (`shipment_id`),
    INDEX `idx_product_id` (`product_id`),
    INDEX `idx_lot_id` (`lot_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='배송 항목 테이블';

-- ==========================================
-- 22. work_orders 테이블 (작업 지시서)
-- ==========================================
CREATE TABLE IF NOT EXISTS `work_orders` (
    `work_order_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `work_order_number` VARCHAR(50) UNIQUE NOT NULL COMMENT '작업 지시 번호',
    `work_type` VARCHAR(50) NOT NULL COMMENT '작업 유형 (설치, 유지보수, 수리, 회수 등)',
    `customer_id` VARCHAR(20) NOT NULL COMMENT '고객 ID',
    `site_id` VARCHAR(20) COMMENT '사업장 ID',
    `device_id` BIGINT UNSIGNED COMMENT '디바이스 ID',
    `assigned_to` BIGINT UNSIGNED COMMENT '담당자 ID',
    `scheduled_date` DATE COMMENT '예정일',
    `completed_date` DATE COMMENT '완료일',
    `status` ENUM('접수', '배정', '진행중', '완료', '취소') DEFAULT '접수' COMMENT '작업 상태',
    `priority` ENUM('낮음', '보통', '높음', '긴급') DEFAULT '보통' COMMENT '우선순위',
    `description` TEXT COMMENT '작업 내용',
    `result` TEXT COMMENT '작업 결과',
    `notes` TEXT COMMENT '비고',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_work_order_number` (`work_order_number`),
    INDEX `idx_customer_id` (`customer_id`),
    INDEX `idx_site_id` (`site_id`),
    INDEX `idx_device_id` (`device_id`),
    INDEX `idx_assigned_to` (`assigned_to`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='작업 지시서 테이블';

-- ==========================================
-- 23. work_order_history 테이블 (작업 지시서 히스토리)
-- ==========================================
CREATE TABLE IF NOT EXISTS `work_order_history` (
    `history_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `work_order_id` BIGINT UNSIGNED NOT NULL COMMENT '작업 지시 ID',
    `old_status` VARCHAR(50) COMMENT '이전 상태',
    `new_status` VARCHAR(50) NOT NULL COMMENT '신규 상태',
    `changed_by` BIGINT UNSIGNED COMMENT '변경자 ID',
    `changed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '변경 일시',
    `notes` TEXT COMMENT '비고',
    INDEX `idx_work_order_id` (`work_order_id`),
    INDEX `idx_changed_at` (`changed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='작업 지시서 상태 변경 히스토리';

-- ==========================================
-- 24. rma_claims 테이블 (RMA/반품 클레임)
-- ==========================================
CREATE TABLE IF NOT EXISTS `rma_claims` (
    `rma_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `rma_number` VARCHAR(50) UNIQUE NOT NULL COMMENT 'RMA 번호',
    `customer_id` VARCHAR(20) NOT NULL COMMENT '고객 ID',
    `device_id` BIGINT UNSIGNED COMMENT '디바이스 ID',
    `claim_type` VARCHAR(50) NOT NULL COMMENT '클레임 유형 (반품, 교환, 수리)',
    `reason` TEXT NOT NULL COMMENT '사유',
    `status` ENUM('접수', '검토중', '승인', '반려', '처리중', '완료') DEFAULT '접수' COMMENT '상태',
    `received_date` DATE COMMENT '접수일',
    `resolved_date` DATE COMMENT '해결일',
    `resolution` TEXT COMMENT '해결 내용',
    `notes` TEXT COMMENT '비고',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_rma_number` (`rma_number`),
    INDEX `idx_customer_id` (`customer_id`),
    INDEX `idx_device_id` (`device_id`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='RMA/반품 클레임 테이블';

-- ==========================================
-- 25. settlements 테이블 (정산 정보)
-- ==========================================
CREATE TABLE IF NOT EXISTS `settlements` (
    `settlement_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `settlement_number` VARCHAR(50) UNIQUE NOT NULL COMMENT '정산 번호',
    `vendor_id` VARCHAR(20) COMMENT '벤더 ID',
    `customer_id` VARCHAR(20) COMMENT '고객 ID',
    `settlement_date` DATE NOT NULL COMMENT '정산일',
    `period_start` DATE NOT NULL COMMENT '정산 기간 시작',
    `period_end` DATE NOT NULL COMMENT '정산 기간 종료',
    `total_amount` DECIMAL(15,2) DEFAULT 0 COMMENT '정산 총액',
    `commission_rate` DECIMAL(5,2) DEFAULT 0 COMMENT '수수료율 (%)',
    `commission_amount` DECIMAL(15,2) DEFAULT 0 COMMENT '수수료',
    `net_amount` DECIMAL(15,2) DEFAULT 0 COMMENT '순액',
    `status` ENUM('임시저장', '확정', '지급완료') DEFAULT '임시저장' COMMENT '정산 상태',
    `notes` TEXT COMMENT '비고',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_settlement_number` (`settlement_number`),
    INDEX `idx_vendor_id` (`vendor_id`),
    INDEX `idx_customer_id` (`customer_id`),
    INDEX `idx_settlement_date` (`settlement_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='정산 정보 테이블';

-- ==========================================
-- 26. settlement_lines 테이블 (정산 항목)
-- ==========================================
CREATE TABLE IF NOT EXISTS `settlement_lines` (
    `line_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `settlement_id` BIGINT UNSIGNED NOT NULL COMMENT '정산 ID',
    `invoice_id` BIGINT UNSIGNED COMMENT '송장 ID',
    `description` VARCHAR(255) NOT NULL COMMENT '항목 설명',
    `amount` DECIMAL(15,2) DEFAULT 0 COMMENT '금액',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_settlement_id` (`settlement_id`),
    INDEX `idx_invoice_id` (`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='정산 항목 테이블';

-- ==========================================
-- 27. tickets 테이블 (고객 지원 티켓)
-- ==========================================
CREATE TABLE IF NOT EXISTS `tickets` (
    `ticket_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `ticket_number` VARCHAR(50) UNIQUE NOT NULL COMMENT '티켓 번호',
    `customer_id` VARCHAR(20) NOT NULL COMMENT '고객 ID',
    `requester_id` BIGINT UNSIGNED NOT NULL COMMENT '요청자 ID',
    `subject` VARCHAR(255) NOT NULL COMMENT '제목',
    `description` TEXT NOT NULL COMMENT '내용',
    `priority` ENUM('낮음', '보통', '높음', '긴급') DEFAULT '보통' COMMENT '우선순위',
    `status` ENUM('신규', '진행중', '대기', '해결', '완료', '취소') DEFAULT '신규' COMMENT '상태',
    `assigned_to` BIGINT UNSIGNED COMMENT '담당자 ID',
    `category` VARCHAR(50) COMMENT '카테고리',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_ticket_number` (`ticket_number`),
    INDEX `idx_customer_id` (`customer_id`),
    INDEX `idx_requester_id` (`requester_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_assigned_to` (`assigned_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='고객 지원 티켓 테이블';

-- ==========================================
-- 28. policy_center 테이블 (정책 센터)
-- ==========================================
CREATE TABLE IF NOT EXISTS `policy_center` (
    `policy_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `policy_type` VARCHAR(50) NOT NULL COMMENT '정책 유형',
    `policy_key` VARCHAR(100) UNIQUE NOT NULL COMMENT '정책 키',
    `policy_value` TEXT COMMENT '정책 값',
    `description` TEXT COMMENT '설명',
    `is_active` TINYINT(1) DEFAULT 1 COMMENT '활성 상태',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_policy_type` (`policy_type`),
    INDEX `idx_policy_key` (`policy_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='정책 센터 테이블';

-- ==========================================
-- 29. contents 테이블 (콘텐츠 관리)
-- ==========================================
CREATE TABLE IF NOT EXISTS `contents` (
    `content_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `content_type` VARCHAR(50) NOT NULL COMMENT '콘텐츠 유형 (공지사항, FAQ, 매뉴얼 등)',
    `title` VARCHAR(255) NOT NULL COMMENT '제목',
    `body` LONGTEXT COMMENT '내용',
    `author_id` BIGINT UNSIGNED COMMENT '작성자 ID',
    `is_published` TINYINT(1) DEFAULT 0 COMMENT '공개 여부',
    `published_at` TIMESTAMP NULL COMMENT '공개 일시',
    `view_count` INT DEFAULT 0 COMMENT '조회수',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_content_type` (`content_type`),
    INDEX `idx_author_id` (`author_id`),
    INDEX `idx_is_published` (`is_published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='콘텐츠 관리 테이블';

-- ==========================================
-- 30. content_assets 테이블 (콘텐츠 첨부파일)
-- ==========================================
CREATE TABLE IF NOT EXISTS `content_assets` (
    `asset_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `content_id` BIGINT UNSIGNED NOT NULL COMMENT '콘텐츠 ID',
    `file_name` VARCHAR(255) NOT NULL COMMENT '파일명',
    `file_path` VARCHAR(500) NOT NULL COMMENT '파일 경로',
    `file_type` VARCHAR(50) COMMENT '파일 유형',
    `file_size` BIGINT UNSIGNED COMMENT '파일 크기',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_content_id` (`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='콘텐츠 첨부파일 테이블';

-- ==========================================
-- 31. dev_requests 테이블 (개발 요청)
-- ==========================================
CREATE TABLE IF NOT EXISTS `dev_requests` (
    `uid` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `status` VARCHAR(20) NOT NULL DEFAULT '접수중' COMMENT '상태',
    `title` VARCHAR(200) NOT NULL COMMENT '제목',
    `content` TEXT NOT NULL COMMENT '내용',
    `priority` VARCHAR(20) DEFAULT '보통' COMMENT '우선순위',
    `category` VARCHAR(50) COMMENT '카테고리',
    `requester_id` BIGINT UNSIGNED NOT NULL COMMENT '요청자 ID',
    `due_at` DATETIME COMMENT '목표 완료일',
    `attachment_count` INT DEFAULT 0 COMMENT '첨부 파일 수',
    `progress` INT DEFAULT 0 COMMENT '진행률',
    `registered_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '등록일',
    `received_at` TIMESTAMP NULL COMMENT '접수일',
    `started_at` TIMESTAMP NULL COMMENT '시작일',
    `completed_at` TIMESTAMP NULL COMMENT '완료일',
    `rejected_at` TIMESTAMP NULL COMMENT '반려일',
    `canceled_at` TIMESTAMP NULL COMMENT '취소일',
    `reopened_at` TIMESTAMP NULL COMMENT '재요청일',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL COMMENT '삭제일',
    INDEX `idx_status` (`status`),
    INDEX `idx_requester_id` (`requester_id`),
    INDEX `idx_priority` (`priority`),
    INDEX `idx_deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='개발 요청 테이블';

-- ==========================================
-- 32. dev_request_files 테이블 (개발 요청 첨부파일)
-- ==========================================
CREATE TABLE IF NOT EXISTS `dev_request_files` (
    `file_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uid` BIGINT UNSIGNED NOT NULL COMMENT '요청 ID',
    `original_name` VARCHAR(255) NOT NULL COMMENT '원본 파일명',
    `stored_name` VARCHAR(255) NOT NULL COMMENT '저장된 파일명',
    `mime` VARCHAR(100) COMMENT 'MIME 타입',
    `size` BIGINT UNSIGNED COMMENT '파일 크기',
    `uploaded_by` BIGINT UNSIGNED COMMENT '업로드자 ID',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='개발 요청 첨부파일 테이블';

-- ==========================================
-- 33. dev_request_comments 테이블 (개발 요청 코멘트)
-- ==========================================
CREATE TABLE IF NOT EXISTS `dev_request_comments` (
    `comment_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uid` BIGINT UNSIGNED NOT NULL COMMENT '요청 ID',
    `author_id` BIGINT UNSIGNED NULL COMMENT '작성자 ID',
    `author_name` VARCHAR(100) NULL COMMENT '작성자 이름',
    `body` TEXT NOT NULL COMMENT '코멘트 내용',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='개발 요청 코멘트 테이블';

-- ==========================================
-- 34. dev_request_status_log 테이블 (개발 요청 상태 로그)
-- ==========================================
CREATE TABLE IF NOT EXISTS `dev_request_status_log` (
    `log_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uid` BIGINT UNSIGNED NOT NULL COMMENT '요청 ID',
    `from_status` VARCHAR(20) COMMENT '변경 전 상태',
    `to_status` VARCHAR(20) NOT NULL COMMENT '변경 후 상태',
    `changed_by` BIGINT UNSIGNED NOT NULL COMMENT '변경자 ID',
    `note` TEXT COMMENT '변경 사유',
    `changed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='개발 요청 상태 로그 테이블';

-- ==========================================
-- Foreign Key 설정
-- ==========================================

-- users FK
ALTER TABLE `users`
    ADD CONSTRAINT `fk_users_role_id`
    FOREIGN KEY (`role_id`) REFERENCES `roles`(`role_id`)
    ON DELETE RESTRICT ON UPDATE CASCADE;

-- user_extra FK
ALTER TABLE `user_extra`
    ADD CONSTRAINT `fk_user_extra_user_id`
    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`)
    ON DELETE CASCADE ON UPDATE CASCADE;

-- user_consents FK
ALTER TABLE `user_consents`
    ADD CONSTRAINT `fk_user_consents_user_id`
    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`)
    ON DELETE CASCADE ON UPDATE CASCADE;

-- customers FK
ALTER TABLE `customers`
    ADD CONSTRAINT `fk_customers_user_id`
    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_customers_vendor_id`
    FOREIGN KEY (`vendor_id`) REFERENCES `vendors`(`vendor_id`)
    ON DELETE SET NULL ON UPDATE CASCADE;

-- sites FK
ALTER TABLE `sites`
    ADD CONSTRAINT `fk_sites_customer_id`
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`customer_id`)
    ON DELETE CASCADE ON UPDATE CASCADE;

-- devices FK
ALTER TABLE `devices`
    ADD CONSTRAINT `fk_devices_product_id`
    FOREIGN KEY (`product_id`) REFERENCES `products`(`product_id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_devices_customer_id`
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`customer_id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_devices_site_id`
    FOREIGN KEY (`site_id`) REFERENCES `sites`(`site_id`)
    ON DELETE SET NULL ON UPDATE CASCADE;

-- device_serials FK
ALTER TABLE `device_serials`
    ADD CONSTRAINT `fk_device_serials_device_id`
    FOREIGN KEY (`device_id`) REFERENCES `devices`(`device_id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_device_serials_changed_by`
    FOREIGN KEY (`changed_by`) REFERENCES `users`(`user_id`)
    ON DELETE SET NULL ON UPDATE CASCADE;

-- device_selections FK
ALTER TABLE `device_selections`
    ADD CONSTRAINT `fk_device_selections_device_id`
    FOREIGN KEY (`device_id`) REFERENCES `devices`(`device_id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_device_selections_scent_id`
    FOREIGN KEY (`scent_id`) REFERENCES `scents`(`scent_id`)
    ON DELETE CASCADE ON UPDATE CASCADE;

-- serial_history FK
ALTER TABLE `serial_history`
    ADD CONSTRAINT `fk_serial_history_device_id`
    FOREIGN KEY (`device_id`) REFERENCES `devices`(`device_id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_serial_history_changed_by`
    FOREIGN KEY (`changed_by`) REFERENCES `users`(`user_id`)
    ON DELETE SET NULL ON UPDATE CASCADE;

-- subscriptions FK
ALTER TABLE `subscriptions`
    ADD CONSTRAINT `fk_subscriptions_customer_id`
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`customer_id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_subscriptions_site_id`
    FOREIGN KEY (`site_id`) REFERENCES `sites`(`site_id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_subscriptions_device_id`
    FOREIGN KEY (`device_id`) REFERENCES `devices`(`device_id`)
    ON DELETE SET NULL ON UPDATE CASCADE;

-- invoices FK
ALTER TABLE `invoices`
    ADD CONSTRAINT `fk_invoices_customer_id`
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`customer_id`)
    ON DELETE RESTRICT ON UPDATE CASCADE;

-- invoice_items FK
ALTER TABLE `invoice_items`
    ADD CONSTRAINT `fk_invoice_items_invoice_id`
    FOREIGN KEY (`invoice_id`) REFERENCES `invoices`(`invoice_id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_invoice_items_product_id`
    FOREIGN KEY (`product_id`) REFERENCES `products`(`product_id`)
    ON DELETE SET NULL ON UPDATE CASCADE;

-- payments FK
ALTER TABLE `payments`
    ADD CONSTRAINT `fk_payments_invoice_id`
    FOREIGN KEY (`invoice_id`) REFERENCES `invoices`(`invoice_id`)
    ON DELETE CASCADE ON UPDATE CASCADE;

-- lots FK
ALTER TABLE `lots`
    ADD CONSTRAINT `fk_lots_product_id`
    FOREIGN KEY (`product_id`) REFERENCES `products`(`product_id`)
    ON DELETE RESTRICT ON UPDATE CASCADE;

-- shipments FK
ALTER TABLE `shipments`
    ADD CONSTRAINT `fk_shipments_customer_id`
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`customer_id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_shipments_site_id`
    FOREIGN KEY (`site_id`) REFERENCES `sites`(`site_id`)
    ON DELETE SET NULL ON UPDATE CASCADE;

-- shipment_items FK
ALTER TABLE `shipment_items`
    ADD CONSTRAINT `fk_shipment_items_shipment_id`
    FOREIGN KEY (`shipment_id`) REFERENCES `shipments`(`shipment_id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_shipment_items_product_id`
    FOREIGN KEY (`product_id`) REFERENCES `products`(`product_id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_shipment_items_lot_id`
    FOREIGN KEY (`lot_id`) REFERENCES `lots`(`lot_id`)
    ON DELETE SET NULL ON UPDATE CASCADE;

-- work_orders FK
ALTER TABLE `work_orders`
    ADD CONSTRAINT `fk_work_orders_customer_id`
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`customer_id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_work_orders_site_id`
    FOREIGN KEY (`site_id`) REFERENCES `sites`(`site_id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_work_orders_device_id`
    FOREIGN KEY (`device_id`) REFERENCES `devices`(`device_id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_work_orders_assigned_to`
    FOREIGN KEY (`assigned_to`) REFERENCES `users`(`user_id`)
    ON DELETE SET NULL ON UPDATE CASCADE;

-- work_order_history FK
ALTER TABLE `work_order_history`
    ADD CONSTRAINT `fk_work_order_history_work_order_id`
    FOREIGN KEY (`work_order_id`) REFERENCES `work_orders`(`work_order_id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_work_order_history_changed_by`
    FOREIGN KEY (`changed_by`) REFERENCES `users`(`user_id`)
    ON DELETE SET NULL ON UPDATE CASCADE;

-- rma_claims FK
ALTER TABLE `rma_claims`
    ADD CONSTRAINT `fk_rma_claims_customer_id`
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`customer_id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_rma_claims_device_id`
    FOREIGN KEY (`device_id`) REFERENCES `devices`(`device_id`)
    ON DELETE SET NULL ON UPDATE CASCADE;

-- settlements FK
ALTER TABLE `settlements`
    ADD CONSTRAINT `fk_settlements_vendor_id`
    FOREIGN KEY (`vendor_id`) REFERENCES `vendors`(`vendor_id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_settlements_customer_id`
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`customer_id`)
    ON DELETE SET NULL ON UPDATE CASCADE;

-- settlement_lines FK
ALTER TABLE `settlement_lines`
    ADD CONSTRAINT `fk_settlement_lines_settlement_id`
    FOREIGN KEY (`settlement_id`) REFERENCES `settlements`(`settlement_id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_settlement_lines_invoice_id`
    FOREIGN KEY (`invoice_id`) REFERENCES `invoices`(`invoice_id`)
    ON DELETE SET NULL ON UPDATE CASCADE;

-- tickets FK
ALTER TABLE `tickets`
    ADD CONSTRAINT `fk_tickets_customer_id`
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`customer_id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_tickets_requester_id`
    FOREIGN KEY (`requester_id`) REFERENCES `users`(`user_id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_tickets_assigned_to`
    FOREIGN KEY (`assigned_to`) REFERENCES `users`(`user_id`)
    ON DELETE SET NULL ON UPDATE CASCADE;

-- contents FK
ALTER TABLE `contents`
    ADD CONSTRAINT `fk_contents_author_id`
    FOREIGN KEY (`author_id`) REFERENCES `users`(`user_id`)
    ON DELETE SET NULL ON UPDATE CASCADE;

-- content_assets FK
ALTER TABLE `content_assets`
    ADD CONSTRAINT `fk_content_assets_content_id`
    FOREIGN KEY (`content_id`) REFERENCES `contents`(`content_id`)
    ON DELETE CASCADE ON UPDATE CASCADE;

-- audit_log FK
ALTER TABLE `audit_log`
    ADD CONSTRAINT `fk_audit_log_actor_user_id`
    FOREIGN KEY (`actor_user_id`) REFERENCES `users`(`user_id`)
    ON DELETE SET NULL ON UPDATE CASCADE;

-- dev_requests FK
ALTER TABLE `dev_requests`
    ADD CONSTRAINT `fk_dev_requests_requester_id`
    FOREIGN KEY (`requester_id`) REFERENCES `users`(`user_id`)
    ON DELETE RESTRICT ON UPDATE CASCADE;

-- dev_request_files FK
ALTER TABLE `dev_request_files`
    ADD CONSTRAINT `fk_dev_request_files_uid`
    FOREIGN KEY (`uid`) REFERENCES `dev_requests`(`uid`)
    ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_dev_request_files_uploaded_by`
    FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`user_id`)
    ON DELETE SET NULL ON UPDATE CASCADE;

-- dev_request_comments FK
ALTER TABLE `dev_request_comments`
    ADD CONSTRAINT `fk_dev_request_comments_uid`
    FOREIGN KEY (`uid`) REFERENCES `dev_requests`(`uid`)
    ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_dev_request_comments_author_id`
    FOREIGN KEY (`author_id`) REFERENCES `users`(`user_id`)
    ON DELETE SET NULL ON UPDATE CASCADE;

-- dev_request_status_log FK
ALTER TABLE `dev_request_status_log`
    ADD CONSTRAINT `fk_dev_request_status_log_uid`
    FOREIGN KEY (`uid`) REFERENCES `dev_requests`(`uid`)
    ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_dev_request_status_log_changed_by`
    FOREIGN KEY (`changed_by`) REFERENCES `users`(`user_id`)
    ON DELETE RESTRICT ON UPDATE CASCADE;

SET FOREIGN_KEY_CHECKS=1;

-- ==========================================
-- 데이터베이스 트리거 (자동 상태 동기화)
-- ==========================================

DELIMITER $$

-- devices 트리거: INSERT 시 시리얼 히스토리 추가
DROP TRIGGER IF EXISTS `trg_devices_ai_sync_serial`$$
CREATE TRIGGER `trg_devices_ai_sync_serial`
AFTER INSERT ON `devices`
FOR EACH ROW
BEGIN
    IF NEW.serial IS NOT NULL THEN
        INSERT INTO device_serials (device_id, old_serial, new_serial, changed_at)
        VALUES (NEW.device_id, NULL, NEW.serial, NOW());
    END IF;
END$$

-- devices 트리거: UPDATE 시 시리얼 변경 히스토리 추가
DROP TRIGGER IF EXISTS `trg_devices_au_sync_serial`$$
CREATE TRIGGER `trg_devices_au_sync_serial`
AFTER UPDATE ON `devices`
FOR EACH ROW
BEGIN
    IF OLD.serial != NEW.serial OR (OLD.serial IS NULL AND NEW.serial IS NOT NULL) THEN
        INSERT INTO device_serials (device_id, old_serial, new_serial, changed_at)
        VALUES (NEW.device_id, OLD.serial, NEW.serial, NOW());
    END IF;
END$$

-- devices 트리거: DELETE 시 시리얼 히스토리 추가
DROP TRIGGER IF EXISTS `trg_devices_ad_sync_serial`$$
CREATE TRIGGER `trg_devices_ad_sync_serial`
AFTER DELETE ON `devices`
FOR EACH ROW
BEGIN
    IF OLD.serial IS NOT NULL THEN
        INSERT INTO device_serials (device_id, old_serial, new_serial, changed_at)
        VALUES (OLD.device_id, OLD.serial, NULL, NOW());
    END IF;
END$$

-- devices 트리거: UPDATE 시 상태 변경 히스토리 추가
DROP TRIGGER IF EXISTS `trg_devices_au_status_history`$$
CREATE TRIGGER `trg_devices_au_status_history`
AFTER UPDATE ON `devices`
FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO serial_history (device_id, serial, old_status, new_status, changed_at)
        VALUES (NEW.device_id, NEW.serial, OLD.status, NEW.status, NOW());
    END IF;
END$$

-- invoice_items 트리거: INSERT 시 총액 업데이트
DROP TRIGGER IF EXISTS `trg_invoice_items_ai`$$
CREATE TRIGGER `trg_invoice_items_ai`
AFTER INSERT ON `invoice_items`
FOR EACH ROW
BEGIN
    UPDATE invoices
    SET total_amount = (
        SELECT COALESCE(SUM(amount), 0)
        FROM invoice_items
        WHERE invoice_id = NEW.invoice_id
    ),
    tax_amount = (
        SELECT COALESCE(SUM(amount), 0) * 0.1
        FROM invoice_items
        WHERE invoice_id = NEW.invoice_id
    )
    WHERE invoice_id = NEW.invoice_id;
END$$

-- invoice_items 트리거: UPDATE 시 총액 업데이트
DROP TRIGGER IF EXISTS `trg_invoice_items_au`$$
CREATE TRIGGER `trg_invoice_items_au`
AFTER UPDATE ON `invoice_items`
FOR EACH ROW
BEGIN
    UPDATE invoices
    SET total_amount = (
        SELECT COALESCE(SUM(amount), 0)
        FROM invoice_items
        WHERE invoice_id = NEW.invoice_id
    ),
    tax_amount = (
        SELECT COALESCE(SUM(amount), 0) * 0.1
        FROM invoice_items
        WHERE invoice_id = NEW.invoice_id
    )
    WHERE invoice_id = NEW.invoice_id;
END$$

-- invoice_items 트리거: DELETE 시 총액 업데이트
DROP TRIGGER IF EXISTS `trg_invoice_items_ad`$$
CREATE TRIGGER `trg_invoice_items_ad`
AFTER DELETE ON `invoice_items`
FOR EACH ROW
BEGIN
    UPDATE invoices
    SET total_amount = (
        SELECT COALESCE(SUM(amount), 0)
        FROM invoice_items
        WHERE invoice_id = OLD.invoice_id
    ),
    tax_amount = (
        SELECT COALESCE(SUM(amount), 0) * 0.1
        FROM invoice_items
        WHERE invoice_id = OLD.invoice_id
    )
    WHERE invoice_id = OLD.invoice_id;
END$$

DELIMITER ;

-- ==========================================
-- 데이터베이스 뷰 (복잡한 쿼리 간소화)
-- ==========================================

-- 송장 결제 합계 뷰
DROP VIEW IF EXISTS `v_invoice_payment_totals`;
CREATE VIEW `v_invoice_payment_totals` AS
SELECT
    i.invoice_id,
    i.invoice_number,
    i.customer_id,
    i.total_amount,
    i.tax_amount,
    COALESCE(SUM(p.payment_amount), 0) AS paid_amount,
    (i.total_amount + i.tax_amount - COALESCE(SUM(p.payment_amount), 0)) AS balance
FROM invoices i
LEFT JOIN payments p ON i.invoice_id = p.invoice_id
GROUP BY i.invoice_id, i.invoice_number, i.customer_id, i.total_amount, i.tax_amount;

-- 다음 오일 배송 뷰
DROP VIEW IF EXISTS `v_next_oil_ship_view`;
CREATE VIEW `v_next_oil_ship_view` AS
SELECT
    d.device_id,
    d.serial,
    d.customer_id,
    c.company_name,
    d.site_id,
    s.site_name,
    d.last_maintenance_date,
    d.next_maintenance_date,
    DATEDIFF(d.next_maintenance_date, CURDATE()) AS days_until_maintenance
FROM devices d
LEFT JOIN customers c ON d.customer_id = c.customer_id
LEFT JOIN sites s ON d.site_id = s.site_id
WHERE d.status IN ('가동중', '설치완료')
AND d.next_maintenance_date IS NOT NULL
ORDER BY d.next_maintenance_date ASC;

-- ==========================================
-- 기본 데이터 삽입
-- ==========================================

-- 역할 데이터
INSERT IGNORE INTO `roles` (`code`, `name`, `description`) VALUES
('HQ', '본사', '본사 관리자'),
('VENDOR', '벤더', '벤더 관리자'),
('CUSTOMER', '고객', '고객 사용자');

-- 기본 관리자 계정 (비밀번호: admin123)
INSERT IGNORE INTO `users` (`role_id`, `userid`, `email`, `password`, `name`, `is_active`)
SELECT
    r.role_id,
    'admin',
    'admin@dispenser.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    '시스템 관리자',
    1
FROM `roles` r
WHERE r.code = 'HQ'
LIMIT 1;

-- ==========================================
-- 다양한 샘플 데이터
-- ==========================================

-- 벤더 샘플 데이터
INSERT IGNORE INTO `vendors` (`vendor_id`, `name`, `company_name`, `business_number`, `representative`, `email`, `phone`, `address`, `is_active`) VALUES
('V20250101001', '스마트솔루션', '스마트솔루션 주식회사', '123-45-67890', '김벤더', 'vendor1@smartsol.com', '02-1234-5678', '서울시 강남구 테헤란로 123', 1),
('V20250102001', '프리미엄디스펜서', '프리미엄디스펜서 주식회사', '234-56-78901', '이벤더', 'vendor2@premiumd.com', '02-2345-6789', '서울시 서초구 서초대로 456', 1),
('V20250103001', '향기나라', '향기나라 유한회사', '345-67-89012', '박향기', 'vendor3@향기.com', '02-3456-7890', '경기도 성남시 분당구 판교로 789', 1);

-- 고객 샘플 데이터
INSERT IGNORE INTO `customers` (`customer_id`, `vendor_id`, `company_name`, `business_number`, `representative`, `email`, `phone`, `address`, `contract_date`, `is_active`) VALUES
('C20250201001', 'V20250101001', '서울병원', '456-78-90123', '최병원', 'info@seoulhosp.com', '02-4567-8901', '서울시 강남구 논현로 321', '2025-02-01', 1),
('C20250202001', 'V20250101001', '부산호텔', '567-89-01234', '정호텔', 'contact@busanhotel.com', '051-567-8901', '부산시 해운대구 해운대로 654', '2025-02-02', 1),
('C20250203001', 'V20250102001', '대전빌딩', '678-90-12345', '강빌딩', 'admin@daejeonbld.com', '042-678-9012', '대전시 유성구 대학로 987', '2025-02-03', 1),
('C20250204001', 'V20250102001', '광주백화점', '789-01-23456', '오백화', 'info@gwangjudept.com', '062-789-0123', '광주시 동구 금남로 147', '2025-02-04', 1),
('C20250205001', 'V20250103001', '인천공항', '890-12-34567', '송공항', 'service@incheonair.com', '032-890-1234', '인천시 중구 공항로 258', '2025-02-05', 1);

-- 사업장 샘플 데이터
INSERT IGNORE INTO `sites` (`site_id`, `customer_id`, `site_name`, `address`, `contact_name`, `contact_phone`, `is_main`, `is_active`) VALUES
('S20250201001', 'C20250201001', '서울병원 본관', '서울시 강남구 논현로 321', '김담당', '02-4567-8901', 1, 1),
('S20250201002', 'C20250201001', '서울병원 별관', '서울시 강남구 논현로 322', '이담당', '02-4567-8902', 0, 1),
('S20250202001', 'C20250202001', '부산호텔 로비', '부산시 해운대구 해운대로 654', '박로비', '051-567-8901', 1, 1),
('S20250202002', 'C20250202001', '부산호텔 연회장', '부산시 해운대구 해운대로 655', '최연회', '051-567-8902', 0, 1),
('S20250203001', 'C20250203001', '대전빌딩 1층', '대전시 유성구 대학로 987', '정일층', '042-678-9012', 1, 1);

-- 사용자 샘플 데이터
INSERT IGNORE INTO `users` (`role_id`, `vendor_id`, `customer_id`, `userid`, `email`, `password`, `name`, `phone`, `is_active`) VALUES
((SELECT role_id FROM roles WHERE code='VENDOR'), 'V20250101001', NULL, 'vendor1', 'vendor1@smartsol.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '김벤더', '010-1234-5678', 1),
((SELECT role_id FROM roles WHERE code='VENDOR'), 'V20250102001', NULL, 'vendor2', 'vendor2@premiumd.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '이벤더', '010-2345-6789', 1),
((SELECT role_id FROM roles WHERE code='CUSTOMER'), NULL, 'C20250201001', 'customer1', 'info@seoulhosp.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '최병원', '010-4567-8901', 1),
((SELECT role_id FROM roles WHERE code='CUSTOMER'), NULL, 'C20250202001', 'customer2', 'contact@busanhotel.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '정호텔', '010-5678-9012', 1),
((SELECT role_id FROM roles WHERE code='HQ'), NULL, NULL, 'manager1', 'manager1@dispenser.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '김매니저', '010-1111-2222', 1);

-- 제품 샘플 데이터
INSERT IGNORE INTO `products` (`product_code`, `product_name`, `category`, `model`, `specification`, `unit_price`, `is_active`) VALUES
('DEV-AP5-001', 'AromaPro 5000', '디바이스', 'AP5-2025', '4슬롯 디스펜서, 최대 400ml 용량', 1500000.00, 1),
('OIL-LAV-001', '라벤더 오일', '오일', 'LAV-100', '100ml 프리미엄 라벤더 오일', 45000.00, 1),
('OIL-ROS-001', '로즈 오일', '오일', 'ROS-100', '100ml 프리미엄 로즈 오일', 55000.00, 1),
('OIL-CIT-001', '시트러스 오일', '오일', 'CIT-100', '100ml 프레시 시트러스 오일', 40000.00, 1),
('OIL-MIN-001', '민트 오일', '오일', 'MIN-100', '100ml 쿨 민트 오일', 38000.00, 1),
('PART-FIL-001', '교체용 필터', '부품', 'FIL-STD', '표준 필터 10개입', 25000.00, 1),
('PART-NOZ-001', '노즐 세트', '부품', 'NOZ-SET', '노즐 4개 세트', 18000.00, 1);

-- 향 샘플 데이터
INSERT IGNORE INTO `scents` (`scent_code`, `scent_name`, `description`, `is_active`) VALUES
('LAV', '라벤더', '편안하고 차분한 라벤더 향', 1),
('ROS', '로즈', '우아하고 화사한 장미 향', 1),
('CIT', '시트러스', '상쾌하고 활력있는 감귤 향', 1),
('MIN', '민트', '청량하고 시원한 박하 향', 1),
('SAN', '샌달우드', '깊고 따뜻한 백단향', 1),
('JAZ', '재스민', '달콤하고 관능적인 재스민 향', 1);

-- 디바이스 샘플 데이터
INSERT IGNORE INTO `devices` (`serial`, `product_id`, `customer_id`, `site_id`, `status`, `install_date`, `last_maintenance_date`, `next_maintenance_date`) VALUES
('AP5-250001', (SELECT product_id FROM products WHERE product_code='DEV-AP5-001'), 'C20250201001', 'S20250201001', '가동중', '2025-02-10', '2025-03-01', '2025-04-01'),
('AP5-250002', (SELECT product_id FROM products WHERE product_code='DEV-AP5-001'), 'C20250201001', 'S20250201002', '가동중', '2025-02-11', '2025-03-02', '2025-04-02'),
('AP5-250003', (SELECT product_id FROM products WHERE product_code='DEV-AP5-001'), 'C20250202001', 'S20250202001', '설치완료', '2025-02-15', NULL, '2025-03-15'),
('AP5-250004', (SELECT product_id FROM products WHERE product_code='DEV-AP5-001'), 'C20250202001', 'S20250202002', '가동중', '2025-02-16', '2025-03-05', '2025-04-05'),
('AP5-250005', (SELECT product_id FROM products WHERE product_code='DEV-AP5-001'), 'C20250203001', 'S20250203001', '가동중', '2025-02-20', '2025-03-10', '2025-04-10'),
('AP5-250006', (SELECT product_id FROM products WHERE product_code='DEV-AP5-001'), NULL, NULL, '재고', NULL, NULL, NULL),
('AP5-250007', (SELECT product_id FROM products WHERE product_code='DEV-AP5-001'), NULL, NULL, '재고', NULL, NULL, NULL);

-- 디바이스 향 선택 샘플 데이터
INSERT IGNORE INTO `device_selections` (`device_id`, `scent_id`, `slot_number`, `is_active`) VALUES
((SELECT device_id FROM devices WHERE serial='AP5-250001'), (SELECT scent_id FROM scents WHERE scent_code='LAV'), 1, 1),
((SELECT device_id FROM devices WHERE serial='AP5-250001'), (SELECT scent_id FROM scents WHERE scent_code='ROS'), 2, 1),
((SELECT device_id FROM devices WHERE serial='AP5-250001'), (SELECT scent_id FROM scents WHERE scent_code='CIT'), 3, 1),
((SELECT device_id FROM devices WHERE serial='AP5-250001'), (SELECT scent_id FROM scents WHERE scent_code='MIN'), 4, 1),
((SELECT device_id FROM devices WHERE serial='AP5-250002'), (SELECT scent_id FROM scents WHERE scent_code='LAV'), 1, 1),
((SELECT device_id FROM devices WHERE serial='AP5-250002'), (SELECT scent_id FROM scents WHERE scent_code='SAN'), 2, 1);

-- 구독 샘플 데이터
INSERT IGNORE INTO `subscriptions` (`customer_id`, `site_id`, `device_id`, `plan_type`, `start_date`, `end_date`, `monthly_fee`, `status`) VALUES
('C20250201001', 'S20250201001', (SELECT device_id FROM devices WHERE serial='AP5-250001'), '프리미엄', '2025-02-10', '2026-02-09', 250000.00, '활성'),
('C20250201001', 'S20250201002', (SELECT device_id FROM devices WHERE serial='AP5-250002'), '프리미엄', '2025-02-11', '2026-02-10', 250000.00, '활성'),
('C20250202001', 'S20250202001', (SELECT device_id FROM devices WHERE serial='AP5-250003'), '스탠다드', '2025-02-15', '2026-02-14', 200000.00, '활성'),
('C20250202001', 'S20250202002', (SELECT device_id FROM devices WHERE serial='AP5-250004'), '스탠다드', '2025-02-16', '2026-02-15', 200000.00, '활성'),
('C20250203001', 'S20250203001', (SELECT device_id FROM devices WHERE serial='AP5-250005'), '베이직', '2025-02-20', '2026-02-19', 150000.00, '활성');

-- 로트 샘플 데이터
INSERT IGNORE INTO `lots` (`lot_number`, `product_id`, `manufacture_date`, `expiry_date`, `quantity`, `status`) VALUES
('LOT-2025020101', (SELECT product_id FROM products WHERE product_code='OIL-LAV-001'), '2025-02-01', '2027-02-01', 500, '재고'),
('LOT-2025020102', (SELECT product_id FROM products WHERE product_code='OIL-ROS-001'), '2025-02-01', '2027-02-01', 300, '재고'),
('LOT-2025020103', (SELECT product_id FROM products WHERE product_code='OIL-CIT-001'), '2025-02-01', '2027-02-01', 400, '재고'),
('LOT-2025020104', (SELECT product_id FROM products WHERE product_code='OIL-MIN-001'), '2025-02-01', '2027-02-01', 350, '재고'),
('LOT-2025020201', (SELECT product_id FROM products WHERE product_code='PART-FIL-001'), '2025-02-02', '2028-02-02', 200, '재고');

-- 송장 샘플 데이터
INSERT IGNORE INTO `invoices` (`invoice_number`, `customer_id`, `invoice_date`, `due_date`, `status`) VALUES
('INV-2025030001', 'C20250201001', '2025-03-01', '2025-03-31', '발행'),
('INV-2025030002', 'C20250202001', '2025-03-01', '2025-03-31', '발행'),
('INV-2025030003', 'C20250203001', '2025-03-01', '2025-03-31', '결제완료');

-- 송장 항목 샘플 데이터 (트리거가 자동으로 총액 계산)
INSERT IGNORE INTO `invoice_items` (`invoice_id`, `product_id`, `description`, `quantity`, `unit_price`, `amount`) VALUES
((SELECT invoice_id FROM invoices WHERE invoice_number='INV-2025030001'), (SELECT product_id FROM products WHERE product_code='DEV-AP5-001'), '월 구독료 (2025-03)', 2, 250000.00, 500000.00),
((SELECT invoice_id FROM invoices WHERE invoice_number='INV-2025030001'), (SELECT product_id FROM products WHERE product_code='OIL-LAV-001'), '라벤더 오일 리필', 4, 45000.00, 180000.00),
((SELECT invoice_id FROM invoices WHERE invoice_number='INV-2025030002'), (SELECT product_id FROM products WHERE product_code='DEV-AP5-001'), '월 구독료 (2025-03)', 2, 200000.00, 400000.00),
((SELECT invoice_id FROM invoices WHERE invoice_number='INV-2025030003'), (SELECT product_id FROM products WHERE product_code='DEV-AP5-001'), '월 구독료 (2025-03)', 1, 150000.00, 150000.00),
((SELECT invoice_id FROM invoices WHERE invoice_number='INV-2025030003'), (SELECT product_id FROM products WHERE product_code='OIL-CIT-001'), '시트러스 오일 리필', 2, 40000.00, 80000.00);

-- 결제 샘플 데이터
INSERT IGNORE INTO `payments` (`invoice_id`, `payment_date`, `payment_amount`, `payment_method`, `transaction_id`) VALUES
((SELECT invoice_id FROM invoices WHERE invoice_number='INV-2025030003'), '2025-03-05', 253000.00, '계좌이체', 'TXN-20250305-001');

-- 배송 샘플 데이터
INSERT IGNORE INTO `shipments` (`shipment_number`, `customer_id`, `site_id`, `shipment_date`, `delivery_date`, `status`, `tracking_number`, `courier`) VALUES
('SHIP-2025030001', 'C20250201001', 'S20250201001', '2025-03-02', '2025-03-04', '배송완료', '123456789012', 'CJ대한통운'),
('SHIP-2025030002', 'C20250202001', 'S20250202001', '2025-03-03', NULL, '배송중', '234567890123', '한진택배'),
('SHIP-2025030003', 'C20250203001', 'S20250203001', '2025-03-05', '2025-03-07', '배송완료', '345678901234', '로젠택배');

-- 배송 항목 샘플 데이터
INSERT IGNORE INTO `shipment_items` (`shipment_id`, `product_id`, `lot_id`, `quantity`) VALUES
((SELECT shipment_id FROM shipments WHERE shipment_number='SHIP-2025030001'), (SELECT product_id FROM products WHERE product_code='OIL-LAV-001'), (SELECT lot_id FROM lots WHERE lot_number='LOT-2025020101'), 4),
((SELECT shipment_id FROM shipments WHERE shipment_number='SHIP-2025030002'), (SELECT product_id FROM products WHERE product_code='OIL-ROS-001'), (SELECT lot_id FROM lots WHERE lot_number='LOT-2025020102'), 2),
((SELECT shipment_id FROM shipments WHERE shipment_number='SHIP-2025030003'), (SELECT product_id FROM products WHERE product_code='OIL-CIT-001'), (SELECT lot_id FROM lots WHERE lot_number='LOT-2025020103'), 2);

-- 작업 지시서 샘플 데이터
INSERT IGNORE INTO `work_orders` (`work_order_number`, `work_type`, `customer_id`, `site_id`, `device_id`, `assigned_to`, `scheduled_date`, `completed_date`, `status`, `priority`, `description`, `result`) VALUES
('WO-2025030001', '설치', 'C20250201001', 'S20250201001', (SELECT device_id FROM devices WHERE serial='AP5-250001'), (SELECT user_id FROM users WHERE userid='manager1'), '2025-02-10', '2025-02-10', '완료', '보통', 'AromaPro 5000 설치', '정상 설치 완료'),
('WO-2025030002', '유지보수', 'C20250201001', 'S20250201001', (SELECT device_id FROM devices WHERE serial='AP5-250001'), (SELECT user_id FROM users WHERE userid='manager1'), '2025-03-01', '2025-03-01', '완료', '보통', '정기 유지보수 및 청소', '정상 점검 완료, 필터 교체'),
('WO-2025030003', '수리', 'C20250202001', 'S20250202001', (SELECT device_id FROM devices WHERE serial='AP5-250003'), (SELECT user_id FROM users WHERE userid='manager1'), '2025-03-10', NULL, '진행중', '높음', '노즐 막힘 현상', NULL);

-- 고객 지원 티켓 샘플 데이터
INSERT IGNORE INTO `tickets` (`ticket_number`, `customer_id`, `requester_id`, `subject`, `description`, `priority`, `status`, `assigned_to`, `category`) VALUES
('TKT-2025030001', 'C20250201001', (SELECT user_id FROM users WHERE userid='customer1'), '향이 약하게 나옵니다', '최근 일주일간 향이 평소보다 약하게 나오는 것 같습니다. 점검 부탁드립니다.', '보통', '해결', (SELECT user_id FROM users WHERE userid='manager1'), '기술지원'),
('TKT-2025030002', 'C20250202001', (SELECT user_id FROM users WHERE userid='customer2'), '오일 추가 주문', '라벤더 오일 10개 추가 주문하고 싶습니다.', '낮음', '완료', (SELECT user_id FROM users WHERE userid='vendor1'), '주문'),
('TKT-2025030003', 'C20250203001', (SELECT user_id FROM users WHERE userid='customer1'), '디바이스 이상 소음', '디바이스에서 이상한 소음이 발생합니다.', '높음', '진행중', (SELECT user_id FROM users WHERE userid='manager1'), '기술지원');

-- 정산 샘플 데이터
INSERT IGNORE INTO `settlements` (`settlement_number`, `vendor_id`, `settlement_date`, `period_start`, `period_end`, `total_amount`, `commission_rate`, `commission_amount`, `net_amount`, `status`) VALUES
('SET-2025030001', 'V20250101001', '2025-03-31', '2025-03-01', '2025-03-31', 1500000.00, 15.00, 225000.00, 1275000.00, '확정'),
('SET-2025030002', 'V20250102001', '2025-03-31', '2025-03-01', '2025-03-31', 800000.00, 15.00, 120000.00, 680000.00, '확정');

-- 정산 항목 샘플 데이터
INSERT IGNORE INTO `settlement_lines` (`settlement_id`, `invoice_id`, `description`, `amount`) VALUES
((SELECT settlement_id FROM settlements WHERE settlement_number='SET-2025030001'), (SELECT invoice_id FROM invoices WHERE invoice_number='INV-2025030001'), '서울병원 2025-03 구독료', 680000.00),
((SELECT settlement_id FROM settlements WHERE settlement_number='SET-2025030001'), (SELECT invoice_id FROM invoices WHERE invoice_number='INV-2025030002'), '부산호텔 2025-03 구독료', 440000.00);

-- RMA 클레임 샘플 데이터
INSERT IGNORE INTO `rma_claims` (`rma_number`, `customer_id`, `device_id`, `claim_type`, `reason`, `status`, `received_date`, `resolved_date`, `resolution`) VALUES
('RMA-2025030001', 'C20250201001', (SELECT device_id FROM devices WHERE serial='AP5-250002'), '수리', '슬롯2 작동 불량', '완료', '2025-03-05', '2025-03-08', '내부 센서 교체 후 정상 작동 확인'),
('RMA-2025030002', 'C20250202001', (SELECT device_id FROM devices WHERE serial='AP5-250004'), '교환', '초기 불량', '처리중', '2025-03-10', NULL, NULL);

-- 콘텐츠 샘플 데이터
INSERT IGNORE INTO `contents` (`content_type`, `title`, `body`, `author_id`, `is_published`, `published_at`, `view_count`) VALUES
('공지사항', '2025년 3월 정기 유지보수 안내', '고객 여러분께, 2025년 3월 정기 유지보수 일정을 안내드립니다...', (SELECT user_id FROM users WHERE userid='admin'), 1, '2025-03-01 09:00:00', 152),
('공지사항', '신제품 AromaPro 6000 출시 예정', 'AromaPro 시리즈의 신제품 6000 모델이 2025년 4월 출시 예정입니다...', (SELECT user_id FROM users WHERE userid='admin'), 1, '2025-03-15 10:00:00', 87),
('FAQ', '디바이스 청소 방법', 'Q: 디바이스를 어떻게 청소하나요? A: 부드러운 천에 중성세제를 묻혀...', (SELECT user_id FROM users WHERE userid='admin'), 1, '2025-02-01 08:00:00', 423);

-- 정책 센터 샘플 데이터
INSERT IGNORE INTO `policy_center` (`policy_type`, `policy_key`, `policy_value`, `description`, `is_active`) VALUES
('시스템', 'maintenance_interval_days', '30', '정기 유지보수 주기 (일)', 1),
('시스템', 'invoice_due_days', '30', '송장 납기일 (발행일로부터)', 1),
('비즈니스', 'commission_rate', '15.0', '기본 수수료율 (%)', 1),
('비즈니스', 'warranty_months', '12', '기본 보증 기간 (개월)', 1);

-- 개발 요청 샘플 데이터
INSERT IGNORE INTO `dev_requests` (`status`, `title`, `content`, `priority`, `category`, `requester_id`, `due_at`, `progress`) VALUES
('완료', '고객 대시보드 개선', '고객 포털에 실시간 디바이스 상태 표시 기능 추가', '높음', 'UI/UX', (SELECT user_id FROM users WHERE userid='manager1'), '2025-03-15 23:59:59', 100),
('진행중', '재고 관리 자동화', '로트 재고가 임계치 이하일 때 자동 알림 발송', '보통', '기능개선', (SELECT user_id FROM users WHERE userid='vendor1'), '2025-04-30 23:59:59', 45),
('접수중', '모바일 앱 개발', 'iOS/Android 고객용 모바일 앱 개발', '긴급', '신규개발', (SELECT user_id FROM users WHERE userid='admin'), '2025-06-30 23:59:59', 0);

-- 개발 요청 코멘트 샘플 데이터
INSERT IGNORE INTO `dev_request_comments` (`uid`, `author_id`, `author_name`, `body`) VALUES
(1, (SELECT user_id FROM users WHERE userid='admin'), '시스템 관리자', '대시보드 개선 작업이 완료되었습니다. 테스트 부탁드립니다.'),
(2, (SELECT user_id FROM users WHERE userid='vendor1'), '김벤더', '자동 알림 로직 구현 중입니다. 임계치는 몇 %로 설정할까요?'),
(2, (SELECT user_id FROM users WHERE userid='manager1'), '김매니저', '임계치는 20%로 설정해주세요.');

COMMIT;
