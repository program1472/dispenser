-- Dispenser Database Schema
-- 지침 문서 및 실제 사용 분석 기반 데이터베이스 스키마

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
    `name` VARCHAR(100) NOT NULL COMMENT '벤더명',
    `company_name` VARCHAR(100) COMMENT '회사명 (name과 동일할 수 있음)',
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
    `user_id` BIGINT UNSIGNED NULL COMMENT '연결된 사용자 ID (users.user_id)',
    `vendor_id` VARCHAR(20) NULL COMMENT '소속 벤더 ID (vendors.vendor_id)',
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
    `customer_id` VARCHAR(20) NOT NULL COMMENT '고객 ID (CYYYYMMDDNNNN)',
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
    INDEX `idx_is_active` (`is_active`),
    INDEX `idx_created_at` (`created_at`)
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
-- 8. dev_requests 테이블 (개발 요청)
-- ==========================================
CREATE TABLE IF NOT EXISTS `dev_requests` (
    `uid` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `status` VARCHAR(20) NOT NULL DEFAULT '접수중' COMMENT '상태: 접수중, 확인중, 진행중, 보류, 완료, 반려, 취소, 재요청, 업체확인요청',
    `title` VARCHAR(200) NOT NULL COMMENT '제목',
    `content` TEXT NOT NULL COMMENT '내용',
    `priority` VARCHAR(20) DEFAULT '보통' COMMENT '우선순위: 낮음, 보통, 높음, 긴급',
    `category` VARCHAR(50) COMMENT '카테고리',
    `requester_id` BIGINT UNSIGNED NOT NULL COMMENT '요청자 ID (users.user_id)',
    `due_at` DATETIME COMMENT '목표 완료일',
    `attachment_count` INT DEFAULT 0 COMMENT '첨부 파일 수',
    `progress` INT DEFAULT 0 COMMENT '진행률 (0-100%)',
    `registered_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '등록일',
    `received_at` TIMESTAMP NULL COMMENT '접수일',
    `started_at` TIMESTAMP NULL COMMENT '시작일',
    `completed_at` TIMESTAMP NULL COMMENT '완료일',
    `rejected_at` TIMESTAMP NULL COMMENT '반려일',
    `canceled_at` TIMESTAMP NULL COMMENT '취소일',
    `reopened_at` TIMESTAMP NULL COMMENT '재요청일',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '마지막 수정시간',
    `deleted_at` TIMESTAMP NULL COMMENT '삭제일 (Soft Delete)',
    INDEX `idx_status` (`status`),
    INDEX `idx_requester_id` (`requester_id`),
    INDEX `idx_priority` (`priority`),
    INDEX `idx_category` (`category`),
    INDEX `idx_registered_at` (`registered_at`),
    INDEX `idx_deleted_at` (`deleted_at`),
    INDEX `idx_uid_desc` (`uid` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='개발 요청 테이블';

-- ==========================================
-- 9. dev_request_files 테이블 (첨부 파일)
-- ==========================================
CREATE TABLE IF NOT EXISTS `dev_request_files` (
    `file_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uid` BIGINT UNSIGNED NOT NULL COMMENT '요청 ID (dev_requests.uid)',
    `original_name` VARCHAR(255) NOT NULL COMMENT '원본 파일명',
    `stored_name` VARCHAR(255) NOT NULL COMMENT '저장된 파일명',
    `mime` VARCHAR(100) COMMENT 'MIME 타입',
    `size` BIGINT UNSIGNED COMMENT '파일 크기 (bytes)',
    `uploaded_by` BIGINT UNSIGNED COMMENT '업로드자 ID (users.user_id)',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '업로드 시간',
    INDEX `idx_uid` (`uid`),
    INDEX `idx_file_id` (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='개발 요청 첨부 파일 테이블';

-- ==========================================
-- 10. dev_request_comments 테이블 (코멘트)
-- ==========================================
CREATE TABLE IF NOT EXISTS `dev_request_comments` (
    `comment_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uid` BIGINT UNSIGNED NOT NULL COMMENT '요청 ID (dev_requests.uid)',
    `author_id` BIGINT UNSIGNED NULL COMMENT '작성자 ID (users.user_id)',
    `author_name` VARCHAR(100) NULL COMMENT '작성자 이름',
    `body` TEXT NOT NULL COMMENT '코멘트 내용',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '작성 시간',
    INDEX `idx_uid_created` (`uid`, `created_at`),
    INDEX `idx_comment_id_desc` (`comment_id` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='개발 요청 코멘트 테이블';

-- ==========================================
-- 11. dev_request_status_log 테이블 (상태 변경 로그)
-- ==========================================
CREATE TABLE IF NOT EXISTS `dev_request_status_log` (
    `log_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uid` BIGINT UNSIGNED NOT NULL COMMENT '요청 ID (dev_requests.uid)',
    `from_status` VARCHAR(20) COMMENT '변경 전 상태',
    `to_status` VARCHAR(20) NOT NULL COMMENT '변경 후 상태',
    `changed_by` BIGINT UNSIGNED NOT NULL COMMENT '변경자 ID (users.user_id)',
    `note` TEXT COMMENT '변경 사유/노트',
    `changed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '변경 시간',
    INDEX `idx_uid` (`uid`),
    INDEX `idx_log_id_desc` (`log_id` DESC),
    INDEX `idx_uid_log_id` (`uid`, `log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='개발 요청 상태 변경 로그 테이블';

-- ==========================================
-- Foreign Key 설정
-- ==========================================

-- users 테이블 FK
ALTER TABLE `users`
    ADD CONSTRAINT `fk_users_role_id`
    FOREIGN KEY (`role_id`) REFERENCES `roles`(`role_id`)
    ON DELETE RESTRICT ON UPDATE CASCADE;

-- user_extra 테이블 FK
ALTER TABLE `user_extra`
    ADD CONSTRAINT `fk_user_extra_user_id`
    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`)
    ON DELETE CASCADE ON UPDATE CASCADE;

-- customers 테이블 FK
ALTER TABLE `customers`
    ADD CONSTRAINT `fk_customers_user_id`
    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`)
    ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `customers`
    ADD CONSTRAINT `fk_customers_vendor_id`
    FOREIGN KEY (`vendor_id`) REFERENCES `vendors`(`vendor_id`)
    ON DELETE SET NULL ON UPDATE CASCADE;

-- sites 테이블 FK
ALTER TABLE `sites`
    ADD CONSTRAINT `fk_sites_customer_id`
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`customer_id`)
    ON DELETE CASCADE ON UPDATE CASCADE;

-- audit_log 테이블 FK
ALTER TABLE `audit_log`
    ADD CONSTRAINT `fk_audit_log_actor_user_id`
    FOREIGN KEY (`actor_user_id`) REFERENCES `users`(`user_id`)
    ON DELETE SET NULL ON UPDATE CASCADE;

-- dev_requests 테이블 FK
ALTER TABLE `dev_requests`
    ADD CONSTRAINT `fk_dev_requests_requester_id`
    FOREIGN KEY (`requester_id`) REFERENCES `users`(`user_id`)
    ON DELETE RESTRICT ON UPDATE CASCADE;

-- dev_request_files 테이블 FK
ALTER TABLE `dev_request_files`
    ADD CONSTRAINT `fk_dev_request_files_uid`
    FOREIGN KEY (`uid`) REFERENCES `dev_requests`(`uid`)
    ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `dev_request_files`
    ADD CONSTRAINT `fk_dev_request_files_uploaded_by`
    FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`user_id`)
    ON DELETE SET NULL ON UPDATE CASCADE;

-- dev_request_comments 테이블 FK
ALTER TABLE `dev_request_comments`
    ADD CONSTRAINT `fk_dev_request_comments_uid`
    FOREIGN KEY (`uid`) REFERENCES `dev_requests`(`uid`)
    ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `dev_request_comments`
    ADD CONSTRAINT `fk_dev_request_comments_author_id`
    FOREIGN KEY (`author_id`) REFERENCES `users`(`user_id`)
    ON DELETE SET NULL ON UPDATE CASCADE;

-- dev_request_status_log 테이블 FK
ALTER TABLE `dev_request_status_log`
    ADD CONSTRAINT `fk_dev_request_status_log_uid`
    FOREIGN KEY (`uid`) REFERENCES `dev_requests`(`uid`)
    ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `dev_request_status_log`
    ADD CONSTRAINT `fk_dev_request_status_log_changed_by`
    FOREIGN KEY (`changed_by`) REFERENCES `users`(`user_id`)
    ON DELETE RESTRICT ON UPDATE CASCADE;

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

-- ==========================================
-- 샘플 데이터 (선택사항)
-- ==========================================

-- 샘플 벤더 데이터
INSERT IGNORE INTO `vendors` (`vendor_id`, `name`, `company_name`, `is_active`) VALUES
('V20250101', '샘플 벤더', '샘플 벤더 주식회사', 1);

-- 샘플 고객 데이터
-- INSERT IGNORE INTO `customers` (`company_name`, `is_active`) VALUES
-- ('샘플 고객사', 1);
