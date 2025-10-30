-- Dispenser Database Schema
-- 지침 문서 기반 데이터베이스 스키마

SET FOREIGN_KEY_CHECKS=0;

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

-- 기본 역할 데이터 삽입
INSERT IGNORE INTO `roles` (`code`, `name`, `description`) VALUES
('HQ', '본사', '본사 관리자'),
('VENDOR', '벤더', '벤더 관리자'),
('CUSTOMER', '고객', '고객 사용자');

-- ==========================================
-- 2. vendors 테이블 (벤더 정보)
-- ==========================================
CREATE TABLE IF NOT EXISTS `vendors` (
    `vendor_id` VARCHAR(20) PRIMARY KEY COMMENT 'VYYYYMMDDNNNN 형식',
    `company_name` VARCHAR(100) NOT NULL COMMENT '회사명',
    `business_number` VARCHAR(20) COMMENT '사업자번호',
    `representative` VARCHAR(50) COMMENT '대표자명',
    `email` VARCHAR(100) COMMENT '이메일',
    `phone` VARCHAR(20) COMMENT '전화번호',
    `address` TEXT COMMENT '주소',
    `is_active` TINYINT(1) DEFAULT 1 COMMENT '활성 상태',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_company_name` (`company_name`),
    INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='벤더 정보 테이블';

-- ==========================================
-- 3. customers 테이블 (고객 정보)
-- ==========================================
CREATE TABLE IF NOT EXISTS `customers` (
    `customer_id` VARCHAR(20) PRIMARY KEY COMMENT 'CYYYYMMDDNNNN 형식',
    `company_name` VARCHAR(100) NOT NULL COMMENT '회사명',
    `business_number` VARCHAR(20) COMMENT '사업자번호',
    `representative` VARCHAR(50) COMMENT '대표자명',
    `email` VARCHAR(100) COMMENT '이메일',
    `phone` VARCHAR(20) COMMENT '전화번호',
    `billing_contact` JSON COMMENT '청구 연락처 (JSON)',
    `shipping_contact` JSON COMMENT '배송 연락처 (JSON)',
    `is_active` TINYINT(1) DEFAULT 1 COMMENT '활성 상태',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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
    `vendor_id` VARCHAR(20) NULL COMMENT '벤더 ID (역할이 VENDOR일 경우)',
    `customer_id` VARCHAR(20) NULL COMMENT '고객 ID (역할이 CUSTOMER일 경우)',
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
    `extra_data` JSON COMMENT '추가 데이터 (JSON)',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_user_id` (`user_id`),
    INDEX `idx_userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='사용자 추가 정보 테이블';

-- ==========================================
-- 7. audit_log 테이블 (감사 로그)
-- ==========================================
CREATE TABLE IF NOT EXISTS `audit_log` (
    `log_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `actor_user_id` BIGINT UNSIGNED COMMENT '행위자 사용자 ID',
    `action` VARCHAR(50) NOT NULL COMMENT '행위 (LOGIN, CREATE, UPDATE, DELETE 등)',
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
-- Foreign Key 설정
-- ==========================================
ALTER TABLE `users`
    ADD CONSTRAINT `fk_users_role_id`
    FOREIGN KEY (`role_id`) REFERENCES `roles`(`role_id`)
    ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `user_extra`
    ADD CONSTRAINT `fk_user_extra_user_id`
    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`)
    ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `sites`
    ADD CONSTRAINT `fk_sites_customer_id`
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`customer_id`)
    ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `audit_log`
    ADD CONSTRAINT `fk_audit_log_actor_user_id`
    FOREIGN KEY (`actor_user_id`) REFERENCES `users`(`user_id`)
    ON DELETE SET NULL ON UPDATE CASCADE;

SET FOREIGN_KEY_CHECKS=1;

-- ==========================================
-- 기본 관리자 계정 생성 (비밀번호: admin123)
-- ==========================================
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
