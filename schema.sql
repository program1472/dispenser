-- Database Backup
-- Generated: 2025-11-12 15:46:38

DROP TABLE IF EXISTS `accessories`;
CREATE TABLE `accessories` (
  `accessory_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '악세사리 ID (PK)',
  `category_id` int(11) DEFAULT NULL COMMENT '카테고리 ID (FK -> categories)',
  `device_id` int(11) DEFAULT NULL COMMENT 'ȣȯ ?????̽? ID (FK -> devices, NULL: ????)',
  `accessory_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '악세사리명',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '설명',
  `price` decimal(10,2) DEFAULT NULL COMMENT '가격 (원)',
  `image_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '이미지 URL',
  `stock_quantity` int(11) DEFAULT 0 COMMENT '재고 수량',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '활성 상태',
  `deleted_at` datetime DEFAULT NULL COMMENT '삭제일시 (Soft Delete)',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '수정일시',
  `created_by` int(11) DEFAULT NULL COMMENT '생성자 user_id',
  `updated_by` int(11) DEFAULT NULL COMMENT '수정자 user_id',
  PRIMARY KEY (`accessory_id`),
  KEY `idx_category` (`category_id`),
  KEY `idx_active` (`is_active`),
  KEY `idx_deleted` (`deleted_at`),
  KEY `idx_device` (`device_id`),
  CONSTRAINT `accessories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL,
  CONSTRAINT `accessories_ibfk_2` FOREIGN KEY (`device_id`) REFERENCES `devices` (`device_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='악세사리(Accessory) 테이블 - 디스펜서 관련 추가 상품 (거치대, 케이스 등)';

INSERT INTO `accessories` VALUES("1","53","","무선 리모컨","리모컨","45000.00","/images/remote.jpg","150","1","","2025-11-11 18:56:14","2025-11-11 19:57:40","1","");
INSERT INTO `accessories` VALUES("2","52","","전원 어댑터 15W","15W 어댑터","18000.00","/images/adapter.jpg","300","1","","2025-11-11 18:56:14","2025-11-11 19:57:40","1","");
INSERT INTO `accessories` VALUES("3","48","","벽걸이 브라켓","벽걸이용","28000.00","/images/bracket.jpg","180","1","","2025-11-11 18:56:14","2025-11-11 19:57:40","1","");
INSERT INTO `accessories` VALUES("4","48","18","프리미엄 메탈 거치대","고급 알루미늄 재질의 안정적인 거치대","45000.00","https://alltogreen.com/web/product/medium/202501/d4a31af4eb1c5174a45ffa051d4f1ad4.jpg","120","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("5","48","19","회전형 데스크 스탠드","360도 회전 가능한 데스크용 스탠드","38000.00","https://alltogreen.com/web/product/medium/202501/b57b181bc0620701f54de728964d3bf9.jpg","85","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("6","48","","벽걸이 브라켓 프로","모든 모델 호환 벽걸이 브라켓","28000.00","https://alltogreen.com/web/product/medium/202501/eb8089d27b66ab05b640e15ae9080924.jpg","200","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("7","48","20","각도조절 스탠드","5단계 각도 조절 가능","32000.00","https://alltogreen.com/web/product/medium/202501/d4a31af4eb1c5174a45ffa051d4f1ad4.jpg","95","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("8","48","26","미니 데스크 스탠드","소형 디바이스 전용 미니 스탠드","22000.00","https://alltogreen.com/web/product/medium/202501/b57b181bc0620701f54de728964d3bf9.jpg","150","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("9","49","18","실리콘 보호 케이스","충격 방지 실리콘 소재","15000.00","https://alltogreen.com/web/product/medium/202501/eb8089d27b66ab05b640e15ae9080924.jpg","300","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("10","49","21","가죽 프리미엄 케이스","고급 천연가죽 케이스","55000.00","https://alltogreen.com/web/product/medium/202501/d4a31af4eb1c5174a45ffa051d4f1ad4.jpg","60","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("11","49","","방수 보호 커버","생활방수 기능 범용 커버","18000.00","https://alltogreen.com/web/product/medium/202501/b57b181bc0620701f54de728964d3bf9.jpg","180","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("12","49","23","투명 하드 케이스","스크래치 방지 투명 케이스","12000.00","https://alltogreen.com/web/product/medium/202501/eb8089d27b66ab05b640e15ae9080924.jpg","250","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("13","49","24","패브릭 소프트 케이스","부드러운 패브릭 소재","20000.00","https://alltogreen.com/web/product/medium/202501/d4a31af4eb1c5174a45ffa051d4f1ad4.jpg","140","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("14","50","18","HEPA 고성능 필터","미세먼지 99.9% 차단","25000.00","https://alltogreen.com/web/product/medium/202501/b57b181bc0620701f54de728964d3bf9.jpg","200","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("15","50","19","활성탄 탈취 필터","냄새 제거 활성탄 필터","18000.00","https://alltogreen.com/web/product/medium/202501/eb8089d27b66ab05b640e15ae9080924.jpg","180","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("16","50","","항균 필터 (3개입)","항균 처리된 범용 필터 세트","35000.00","https://alltogreen.com/web/product/medium/202501/d4a31af4eb1c5174a45ffa051d4f1ad4.jpg","150","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("17","50","21","프리미엄 복합 필터","HEPA+활성탄 복합 필터","42000.00","https://alltogreen.com/web/product/medium/202501/b57b181bc0620701f54de728964d3bf9.jpg","90","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("18","50","20","교체용 기본 필터","표준 교체용 필터","15000.00","https://alltogreen.com/web/product/medium/202501/eb8089d27b66ab05b640e15ae9080924.jpg","280","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("19","51","18","USB-C 고속충전 케이블 1m","60W 고속충전 지원","12000.00","https://alltogreen.com/web/product/medium/202501/d4a31af4eb1c5174a45ffa051d4f1ad4.jpg","350","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("20","51","","USB-C 케이블 2m","길이 2m 범용 케이블","15000.00","https://alltogreen.com/web/product/medium/202501/b57b181bc0620701f54de728964d3bf9.jpg","280","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("21","51","22","USB-A to C 케이블","호환성 높은 A to C","9000.00","https://alltogreen.com/web/product/medium/202501/eb8089d27b66ab05b640e15ae9080924.jpg","400","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("22","51","23","USB 마그네틱 케이블","자석 탈착 방식","18000.00","https://alltogreen.com/web/product/medium/202501/d4a31af4eb1c5174a45ffa051d4f1ad4.jpg","200","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("23","51","","꼬임방지 케이블 3m","꼬임 방지 설계 긴 케이블","20000.00","https://alltogreen.com/web/product/medium/202501/b57b181bc0620701f54de728964d3bf9.jpg","150","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("24","52","","전원 어댑터 15W","표준 15W 어댑터","18000.00","https://alltogreen.com/web/product/medium/202501/eb8089d27b66ab05b640e15ae9080924.jpg","300","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("25","52","21","고속충전 어댑터 30W","30W PD 고속충전","28000.00","https://alltogreen.com/web/product/medium/202501/d4a31af4eb1c5174a45ffa051d4f1ad4.jpg","180","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("26","52","","듀얼포트 어댑터 45W","USB-C 2포트 동시충전","38000.00","https://alltogreen.com/web/product/medium/202501/b57b181bc0620701f54de728964d3bf9.jpg","120","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("27","52","","여행용 멀티 어댑터","해외 겸용 멀티 어댑터","32000.00","https://alltogreen.com/web/product/medium/202501/eb8089d27b66ab05b640e15ae9080924.jpg","95","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("28","52","24","차량용 시거잭 어댑터","차량 시거잭 12V 충전","22000.00","https://alltogreen.com/web/product/medium/202501/d4a31af4eb1c5174a45ffa051d4f1ad4.jpg","160","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("29","53","","무선 리모컨","공용 블루투스 리모컨","45000.00","https://alltogreen.com/web/product/medium/202501/b57b181bc0620701f54de728964d3bf9.jpg","150","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("30","53","21","스마트 리모컨 프로","앱 연동 스마트 리모컨","68000.00","https://alltogreen.com/web/product/medium/202501/eb8089d27b66ab05b640e15ae9080924.jpg","80","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("31","53","18","IR 리모컨 베이직","적외선 방식 기본 리모컨","35000.00","https://alltogreen.com/web/product/medium/202501/d4a31af4eb1c5174a45ffa051d4f1ad4.jpg","180","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("32","53","25","음성인식 리모컨","AI 음성인식 지원","85000.00","https://alltogreen.com/web/product/medium/202501/b57b181bc0620701f54de728964d3bf9.jpg","60","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("33","53","26","미니 리모컨","소형 디바이스용 미니 리모컨","28000.00","https://alltogreen.com/web/product/medium/202501/eb8089d27b66ab05b640e15ae9080924.jpg","200","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("34","54","","청소용 브러시 세트","디바이스 청소 도구 세트","15000.00","https://alltogreen.com/web/product/medium/202501/d4a31af4eb1c5174a45ffa051d4f1ad4.jpg","250","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("35","54","","향수 리필 카트리지 (5개입)","교체용 향수 카트리지","45000.00","https://alltogreen.com/web/product/medium/202501/b57b181bc0620701f54de728964d3bf9.jpg","300","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("36","54","20","LED 무드등 모듈","분위기 조명 LED 모듈","32000.00","https://alltogreen.com/web/product/medium/202501/eb8089d27b66ab05b640e15ae9080924.jpg","140","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("37","54","","소음 감소 패드","진동/소음 방지 패드","12000.00","https://alltogreen.com/web/product/medium/202501/d4a31af4eb1c5174a45ffa051d4f1ad4.jpg","280","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("38","54","","스티커 데코 세트","디바이스 꾸미기 스티커","8000.00","https://alltogreen.com/web/product/medium/202501/b57b181bc0620701f54de728964d3bf9.jpg","500","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("39","54","26","휴대용 파우치","소형 디바이스용 파우치","18000.00","https://alltogreen.com/web/product/medium/202501/eb8089d27b66ab05b640e15ae9080924.jpg","220","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");
INSERT INTO `accessories` VALUES("40","54","","전용 드라이버 세트","분해/조립용 정밀 드라이버","25000.00","https://alltogreen.com/web/product/medium/202501/d4a31af4eb1c5174a45ffa051d4f1ad4.jpg","150","1","","2025-11-11 20:10:00","2025-11-11 20:10:00","1","1");

DROP TABLE IF EXISTS `account_assignments`;
CREATE TABLE `account_assignments` (
  `assignment_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '배정 ID (PK)',
  `customer_id` int(11) DEFAULT NULL,
  `sales_user_id` int(11) NOT NULL COMMENT '담당 영업사원 user_id (FK -> users)',
  `vendor_id` int(11) DEFAULT NULL COMMENT '담당 밴더 ID (FK -> vendors, NULL: 본사 직관리)',
  `assigned_date` date NOT NULL COMMENT '배정일',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '활성 상태 (TRUE: 현재 담당, FALSE: 담당 해제)',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '배정 메모',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '수정일시',
  `created_by` int(11) DEFAULT NULL COMMENT '생성자 user_id',
  `updated_by` int(11) DEFAULT NULL COMMENT '수정자 user_id',
  PRIMARY KEY (`assignment_id`),
  KEY `idx_customer` (`customer_id`),
  KEY `idx_sales` (`sales_user_id`),
  KEY `idx_vendor` (`vendor_id`),
  KEY `idx_assignments_active` (`sales_user_id`,`is_active`),
  CONSTRAINT `account_assignments_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE,
  CONSTRAINT `account_assignments_ibfk_2` FOREIGN KEY (`sales_user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `account_assignments_ibfk_3` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`vendor_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='담당자 배정(Account Assignment) 테이블 - 영업사원의 고객 담당 배정 정보';

INSERT INTO `account_assignments` VALUES("32","1","17","1","2024-01-20","0","","2025-11-10 08:31:00","2025-11-10 12:23:18","1","0");
INSERT INTO `account_assignments` VALUES("33","2","17","1","2024-01-25","1","","2025-11-10 08:31:00","2025-11-10 08:31:00","1","0");
INSERT INTO `account_assignments` VALUES("34","3","17","1","2024-02-01","1","","2025-11-10 08:31:00","2025-11-10 08:31:00","1","0");
INSERT INTO `account_assignments` VALUES("35","4","18","2","2024-02-10","1","","2025-11-10 08:31:00","2025-11-10 08:31:00","2","0");
INSERT INTO `account_assignments` VALUES("36","5","18","2","2024-02-15","1","","2025-11-10 08:31:00","2025-11-10 08:31:00","2","0");
INSERT INTO `account_assignments` VALUES("37","6","18","2","2024-02-20","1","","2025-11-10 08:31:00","2025-11-10 08:31:00","2","0");
INSERT INTO `account_assignments` VALUES("38","7","19","3","2024-03-01","1","","2025-11-10 08:31:00","2025-11-10 08:31:00","3","0");
INSERT INTO `account_assignments` VALUES("39","8","19","3","2024-03-05","1","","2025-11-10 08:31:00","2025-11-10 08:31:00","3","0");
INSERT INTO `account_assignments` VALUES("40","9","19","3","2024-03-10","1","","2025-11-10 08:31:00","2025-11-10 08:31:00","3","0");
INSERT INTO `account_assignments` VALUES("41","10","20","4","2024-03-15","1","","2025-11-10 08:31:00","2025-11-10 08:31:00","4","0");
INSERT INTO `account_assignments` VALUES("42","11","20","4","2024-03-20","1","","2025-11-10 08:31:00","2025-11-10 08:31:00","4","0");
INSERT INTO `account_assignments` VALUES("43","12","20","4","2024-03-25","1","","2025-11-10 08:31:00","2025-11-10 08:31:00","4","0");
INSERT INTO `account_assignments` VALUES("44","13","21","5","2024-04-01","1","","2025-11-10 08:31:00","2025-11-10 08:31:00","5","0");
INSERT INTO `account_assignments` VALUES("45","14","21","5","2024-04-05","1","","2025-11-10 08:31:00","2025-11-10 08:31:00","5","0");
INSERT INTO `account_assignments` VALUES("46","15","21","5","2024-04-10","1","","2025-11-10 08:31:00","2025-11-10 08:31:00","5","0");
INSERT INTO `account_assignments` VALUES("47","16","22","6","2024-04-15","1","","2025-11-10 08:31:00","2025-11-10 08:31:00","6","0");
INSERT INTO `account_assignments` VALUES("48","17","22","6","2024-04-20","1","","2025-11-10 08:31:00","2025-11-10 08:31:00","6","0");
INSERT INTO `account_assignments` VALUES("49","18","22","6","2024-04-25","1","","2025-11-10 08:31:00","2025-11-10 08:31:00","6","0");
INSERT INTO `account_assignments` VALUES("50","19","23","7","2024-05-01","1","","2025-11-10 08:31:00","2025-11-10 08:31:00","7","0");
INSERT INTO `account_assignments` VALUES("51","20","23","7","2024-05-05","1","","2025-11-10 08:31:00","2025-11-10 08:31:00","7","0");
INSERT INTO `account_assignments` VALUES("52","21","6","0","2024-05-10","1","","2025-11-10 08:31:00","2025-11-10 08:31:00","1","0");
INSERT INTO `account_assignments` VALUES("53","22","6","0","2024-05-15","1","","2025-11-10 08:31:00","2025-11-10 08:31:00","1","0");
INSERT INTO `account_assignments` VALUES("54","23","7","0","2024-05-20","1","","2025-11-10 08:31:00","2025-11-10 08:31:00","1","0");
INSERT INTO `account_assignments` VALUES("55","24","24","8","2024-05-25","1","","2025-11-10 08:31:00","2025-11-10 08:31:00","8","0");
INSERT INTO `account_assignments` VALUES("56","25","24","8","2024-05-30","1","","2025-11-10 08:31:00","2025-11-10 08:31:00","8","0");
INSERT INTO `account_assignments` VALUES("57","26","25","9","2024-06-01","1","","2025-11-10 08:31:00","2025-11-10 08:31:00","9","0");
INSERT INTO `account_assignments` VALUES("58","27","25","9","2024-06-05","1","","2025-11-10 08:31:00","2025-11-10 08:31:00","9","0");
INSERT INTO `account_assignments` VALUES("59","28","25","9","2024-06-10","1","","2025-11-10 08:31:00","2025-11-10 08:31:00","9","0");
INSERT INTO `account_assignments` VALUES("60","29","26","10","2024-06-15","1","","2025-11-10 08:31:00","2025-11-10 08:31:00","10","0");
INSERT INTO `account_assignments` VALUES("61","30","26","10","2024-06-20","1","","2025-11-10 08:31:00","2025-11-10 08:31:00","10","0");
INSERT INTO `account_assignments` VALUES("67","0","56","9","2025-11-10","1","","2025-11-10 11:18:08","2025-11-10 11:18:08","0","0");
INSERT INTO `account_assignments` VALUES("68","1","26","10","2025-11-10","1","","2025-11-10 12:23:18","2025-11-10 12:23:18","0","0");
INSERT INTO `account_assignments` VALUES("77","","79","18","2025-11-12","1","","2025-11-12 02:07:36","2025-11-12 02:07:36","","");
INSERT INTO `account_assignments` VALUES("78","","80","18","2025-11-12","1","","2025-11-12 02:07:36","2025-11-12 02:07:36","","");
INSERT INTO `account_assignments` VALUES("79","","81","19","2025-11-12","1","","2025-11-12 02:07:36","2025-11-12 02:07:36","","");
INSERT INTO `account_assignments` VALUES("80","","82","19","2025-11-12","1","","2025-11-12 02:07:36","2025-11-12 02:07:36","","");

DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
  `log_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '로그 ID (PK)',
  `user_id` int(11) DEFAULT NULL COMMENT '작업자 user_id (FK -> users)',
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '액션 (LOGIN, LOGOUT, CREATE, UPDATE, DELETE 등)',
  `table_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '대상 테이블명',
  `record_id` int(11) DEFAULT NULL COMMENT '대상 레코드 ID',
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '변경 전 값 (JSON)',
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '변경 후 값 (JSON)',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'IP 주소 (IPv4/IPv6)',
  `user_agent` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'User Agent',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '로그 생성 일시',
  PRIMARY KEY (`log_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_action` (`action`),
  KEY `idx_table` (`table_name`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='감사 로그(Audit Log) 테이블 - 시스템 내 모든 중요 액션 기록';


DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '카테고리 ID (PK)',
  `parent_id` int(11) DEFAULT NULL COMMENT '상위 카테고리 ID (FK -> categories, NULL: 최상위)',
  `level` tinyint(4) NOT NULL COMMENT '카테고리 레벨 (1: 최상위, 2: 중분류, 3: 소분류, 4: 세부분류)',
  `category_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '카테고리명',
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'URL 슬러그 (영문, 소문자)',
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '카테고리 설명',
  `image_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '카테고리 이미지 URL',
  `display_order` int(11) DEFAULT 0 COMMENT '정렬 순서 (작을수록 우선)',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '활성 상태',
  `deleted_at` datetime DEFAULT NULL COMMENT '삭제일시 (Soft Delete)',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '수정일시',
  `created_by` int(11) DEFAULT NULL COMMENT '생성자 user_id',
  `updated_by` int(11) DEFAULT NULL COMMENT '수정자 user_id',
  PRIMARY KEY (`category_id`),
  KEY `idx_parent` (`parent_id`),
  KEY `idx_level` (`level`),
  KEY `idx_slug` (`slug`),
  KEY `idx_active` (`is_active`),
  KEY `idx_deleted` (`deleted_at`),
  CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='카테고리(Category) 테이블 - 4단계 계층 구조로 상품 분류 (콘텐츠, 향, 기기 등 모든 상품에 적용)';

INSERT INTO `categories` VALUES("1","0","1","향 계열","scents","향 카트리지 최상위 분류","","1","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `categories` VALUES("2","0","1","콘텐츠 유형","contents","콘텐츠 최상위 분류","","2","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `categories` VALUES("3","0","1","디스펜서 타입","devices","디스펜서 기기 분류","","3","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `categories` VALUES("4","0","1","부자재 종류","parts","교체 부품 분류","","4","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `categories` VALUES("5","0","1","악세사리","accessories","추가 상품 분류","","5","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `categories` VALUES("6","1","2","Woody","woody","우디 계열 향","","1","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `categories` VALUES("7","1","2","Floral","floral","플로랄 계열 향","","2","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `categories` VALUES("8","1","2","Fruity","fruity","프루티 계열 향","","3","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `categories` VALUES("9","1","2","Green & Herb","green-herb","그린 & 허브 계열","","4","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `categories` VALUES("10","6","3","Pine (소나무)","pine","소나무 향","","1","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `categories` VALUES("11","6","3","Cedar (시더)","cedar","시더우드 향","","2","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `categories` VALUES("12","6","3","Sandalwood (샌달우드)","sandalwood","샌달우드 향","","3","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `categories` VALUES("13","10","4","약함","pine-light","은은한 소나무 향","","1","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `categories` VALUES("14","10","4","보통","pine-medium","적당한 소나무 향","","2","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `categories` VALUES("15","10","4","강함","pine-strong","진한 소나무 향","","3","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `categories` VALUES("16","7","3","Lavender (라벤더)","lavender","라벤더 향","","1","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `categories` VALUES("17","7","3","Rose (장미)","rose","장미 향","","2","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `categories` VALUES("18","17","4","프렌치 라벤더","lavender-french","프렌치 라벤더","","1","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `categories` VALUES("19","17","4","잉글리시 라벤더","lavender-english","잉글리시 라벤더","","2","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `categories` VALUES("20","2","2","계절별","seasonal","계절 테마 콘텐츠","","1","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `categories` VALUES("21","2","2","테마별","themed","특별 테마 콘텐츠","","2","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `categories` VALUES("22","2","2","프로모션","promotional","홍보용 콘텐츠","","3","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `categories` VALUES("23","22","3","봄","spring","봄 시즌 콘텐츠","","1","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `categories` VALUES("24","22","3","여름","summer","여름 시즌 콘텐츠","","2","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `categories` VALUES("25","22","3","가을","autumn","가을 시즌 콘텐츠","","3","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `categories` VALUES("26","22","3","겨울","winter","겨울 시즌 콘텐츠","","4","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `categories` VALUES("27","26","4","벚꽃","cherry-blossom","벚꽃 테마","","1","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `categories` VALUES("28","26","4","새싹","sprout","새싹 테마","","2","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `categories` VALUES("29","3","2","스탠드형","stand-type","바닥 설치형","","1","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `categories` VALUES("30","3","2","벽부착형","wall-mount","벽 부착형","","2","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `categories` VALUES("31","21","0","유치원","","","","0","1","","2025-11-11 19:43:17","2025-11-11 19:43:17","","");
INSERT INTO `categories` VALUES("32","21","0","피부관리실","","","","0","1","","2025-11-11 19:43:17","2025-11-11 19:43:17","","");
INSERT INTO `categories` VALUES("33","21","0","호텔","","","","0","1","","2025-11-11 19:43:17","2025-11-11 19:43:17","","");
INSERT INTO `categories` VALUES("34","21","0","골프장","","","","0","1","","2025-11-11 19:43:17","2025-11-11 19:43:17","","");
INSERT INTO `categories` VALUES("35","21","0","회사","","","","0","1","","2025-11-11 19:43:17","2025-11-11 19:43:17","","");
INSERT INTO `categories` VALUES("36","21","0","웨딩홀","","","","0","1","","2025-11-11 19:43:17","2025-11-11 19:43:17","","");
INSERT INTO `categories` VALUES("37","21","0","학교","","","","0","1","","2025-11-11 19:43:17","2025-11-11 19:43:17","","");
INSERT INTO `categories` VALUES("38","21","0","ART","","","","0","1","","2025-11-11 19:43:17","2025-11-11 19:43:17","","");
INSERT INTO `categories` VALUES("39","21","0","피트니스","","","","0","1","","2025-11-11 19:43:17","2025-11-11 19:43:17","","");
INSERT INTO `categories` VALUES("40","21","0","병원/약국","","","","0","1","","2025-11-11 19:43:17","2025-11-11 19:43:17","","");
INSERT INTO `categories` VALUES("48","5","0","거치대/스탠드","","","","0","1","","2025-11-11 19:55:33","2025-11-11 19:55:33","","");
INSERT INTO `categories` VALUES("49","5","0","케이스/보호커버","","","","0","1","","2025-11-11 19:55:33","2025-11-11 19:55:33","","");
INSERT INTO `categories` VALUES("50","5","0","필터","","","","0","1","","2025-11-11 19:55:33","2025-11-11 19:55:33","","");
INSERT INTO `categories` VALUES("51","5","0","USB케이블","","","","0","1","","2025-11-11 19:55:33","2025-11-11 19:55:33","","");
INSERT INTO `categories` VALUES("52","5","0","어댑터","","","","0","1","","2025-11-11 19:55:33","2025-11-11 19:55:33","","");
INSERT INTO `categories` VALUES("53","5","0","리모컨","","","","0","1","","2025-11-11 19:55:33","2025-11-11 19:55:33","","");
INSERT INTO `categories` VALUES("54","5","0","기타 악세사리","","","","0","1","","2025-11-11 19:55:33","2025-11-11 19:55:33","","");
INSERT INTO `categories` VALUES("55","1","0","Citrus","","","","0","1","","2025-11-12 01:36:21","2025-11-12 01:36:21","","");
INSERT INTO `categories` VALUES("64","4","0","센서류","","","","1","1","","2025-11-12 02:18:36","2025-11-12 02:18:36","","");
INSERT INTO `categories` VALUES("65","4","0","케이블/전선","","","","2","1","","2025-11-12 02:18:36","2025-11-12 02:18:36","","");
INSERT INTO `categories` VALUES("66","4","0","필터/소모품","","","","3","1","","2025-11-12 02:18:36","2025-11-12 02:18:36","","");
INSERT INTO `categories` VALUES("67","4","0","기타 부품","","","","4","1","","2025-11-12 02:18:36","2025-11-12 02:18:36","","");

DROP TABLE IF EXISTS `content_changes`;
CREATE TABLE `content_changes` (
  `change_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '교체 ID (PK)',
  `assignment_id` int(11) NOT NULL COMMENT '디스펜서 배정 ID (FK -> device_assignments)',
  `old_content_id` int(11) DEFAULT NULL COMMENT '이전 콘텐츠 ID (FK -> contents)',
  `new_content_id` int(11) NOT NULL COMMENT '새 콘텐츠 ID (FK -> contents)',
  `change_date` date NOT NULL COMMENT '교체일',
  `changed_by` int(11) DEFAULT NULL COMMENT '교체자 user_id',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '교체 메모',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  PRIMARY KEY (`change_id`),
  KEY `idx_assignment` (`assignment_id`),
  KEY `idx_old_content` (`old_content_id`),
  KEY `idx_new_content` (`new_content_id`),
  KEY `idx_change_date` (`change_date`),
  CONSTRAINT `content_changes_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `device_assignments` (`assignment_id`) ON DELETE CASCADE,
  CONSTRAINT `content_changes_ibfk_2` FOREIGN KEY (`old_content_id`) REFERENCES `contents` (`content_id`) ON DELETE SET NULL,
  CONSTRAINT `content_changes_ibfk_3` FOREIGN KEY (`new_content_id`) REFERENCES `contents` (`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='콘텐츠 교체 이력(Content Change) 테이블 - 디스펜서 콘텐츠 교체 히스토리';


DROP TABLE IF EXISTS `content_requests`;
CREATE TABLE `content_requests` (
  `request_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '요청 ID (PK)',
  `request_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '요청 번호 (고유, 예: CR-2025-0001)',
  `customer_id` int(11) NOT NULL COMMENT '고객 ID (FK -> customers)',
  `base_content_id` int(11) DEFAULT NULL COMMENT '기본 콘텐츠 ID (FK -> contents, NULL: 완전 신규)',
  `customization_level` enum('PRINTING','BASIC','DELUXE','PREMIUM') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '커스터마이징 등급 (PRINTING: 문구만, BASIC: 이미지+문구, DELUXE: 부분 재디자인, PREMIUM: 완전 신규)',
  `price` decimal(10,2) NOT NULL COMMENT '수정 비용 (원)',
  `request_detail` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '수정 요청 내용',
  `reference_files` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '참고 파일 URL 목록 (JSON 배열)',
  `assigned_lucid_user_id` int(11) DEFAULT NULL COMMENT '배정된 루시드 user_id (FK -> users)',
  `status` enum('PENDING','ASSIGNED','IN_PROGRESS','REVIEW','REVISION','APPROVED','COMPLETED','CANCELLED') COLLATE utf8mb4_unicode_ci DEFAULT 'PENDING' COMMENT '상태 (PENDING: 배정 대기, ASSIGNED: 루시드 배정됨, IN_PROGRESS: 작업중, REVIEW: 고객 검토, REVISION: 재수정, APPROVED: 승인됨, COMPLETED: 완료, CANCELLED: 취소)',
  `revision_count` tinyint(4) DEFAULT 0 COMMENT '재수정 요청 횟수',
  `max_revision` tinyint(4) DEFAULT 2 COMMENT '최대 재수정 허용 횟수',
  `due_date` date DEFAULT NULL COMMENT '마감일',
  `approved_date` datetime DEFAULT NULL COMMENT '고객 승인 일시',
  `completed_date` datetime DEFAULT NULL COMMENT '작업 완료 일시',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '수정일시',
  `created_by` int(11) DEFAULT NULL COMMENT '생성자 user_id',
  `updated_by` int(11) DEFAULT NULL COMMENT '수정자 user_id',
  PRIMARY KEY (`request_id`),
  UNIQUE KEY `request_number` (`request_number`),
  KEY `idx_customer` (`customer_id`),
  KEY `idx_base_content` (`base_content_id`),
  KEY `idx_lucid` (`assigned_lucid_user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_due_date` (`due_date`),
  CONSTRAINT `content_requests_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  CONSTRAINT `content_requests_ibfk_2` FOREIGN KEY (`base_content_id`) REFERENCES `contents` (`content_id`) ON DELETE SET NULL,
  CONSTRAINT `content_requests_ibfk_3` FOREIGN KEY (`assigned_lucid_user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='콘텐츠 수정 요청(Content Request) 테이블 - 고객의 콘텐츠 커스터마이징 요청';


DROP TABLE IF EXISTS `content_revisions`;
CREATE TABLE `content_revisions` (
  `revision_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '리비전 ID (PK)',
  `request_id` int(11) NOT NULL COMMENT '요청 ID (FK -> content_requests)',
  `revision_number` tinyint(4) NOT NULL COMMENT '리비전 버전 (1, 2, 3 ...)',
  `file_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '시안 파일 URL',
  `thumbnail_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '썸네일 URL',
  `lucid_notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '루시드 작업 메모',
  `customer_feedback` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '고객 피드백',
  `feedback_date` datetime DEFAULT NULL COMMENT '피드백 입력 일시',
  `is_approved` tinyint(1) DEFAULT 0 COMMENT '승인 여부 (TRUE: 승인, FALSE: 재수정 필요)',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시 (시안 업로드 일시)',
  PRIMARY KEY (`revision_id`),
  KEY `idx_request` (`request_id`),
  CONSTRAINT `content_revisions_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `content_requests` (`request_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='콘텐츠 리비전(Content Revision) 테이블 - 콘텐츠 수정 작업의 버전별 시안 및 피드백';


DROP TABLE IF EXISTS `contents`;
CREATE TABLE `contents` (
  `content_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '콘텐츠 ID (PK)',
  `category_id` int(11) DEFAULT NULL COMMENT '카테고리 ID (FK -> categories, 4단계 분류)',
  `content_title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '콘텐츠 제목',
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '콘텐츠 설명',
  `template_type` enum('BASIC','SEASONAL','PROMOTIONAL','CUSTOM') COLLATE utf8mb4_unicode_ci DEFAULT 'BASIC' COMMENT '템플릿 유형 (BASIC: 기본, SEASONAL: 계절, PROMOTIONAL: 홍보, CUSTOM: 맞춤)',
  `image_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '콘텐츠 이미지 URL',
  `thumbnail_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '썸네일 이미지 URL',
  `file_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '인쇄용 파일 URL (AI, PDF 등)',
  `size` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '크기 (예: A4, A5, 210x297mm)',
  `owner_type` enum('COMPANY','CUSTOMER','LUCID') COLLATE utf8mb4_unicode_ci DEFAULT 'COMPANY' COMMENT '소유자 타입 (COMPANY: 본사 공용, CUSTOMER: 고객 전용, LUCID: 루시드 제작)',
  `owner_id` int(11) DEFAULT NULL COMMENT '소유자 ID (owner_type이 CUSTOMER면 customer_id, LUCID면 user_id)',
  `is_free` tinyint(1) DEFAULT 1 COMMENT '무료 제공 여부 (TRUE: 기본 무료, FALSE: 유료)',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '활성 상태',
  `view_count` int(11) DEFAULT 0 COMMENT '조회수',
  `deleted_at` datetime DEFAULT NULL COMMENT '삭제일시 (Soft Delete)',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '수정일시',
  `created_by` int(11) DEFAULT NULL COMMENT '생성자 user_id',
  `updated_by` int(11) DEFAULT NULL COMMENT '수정자 user_id',
  PRIMARY KEY (`content_id`),
  KEY `idx_category` (`category_id`),
  KEY `idx_owner` (`owner_type`,`owner_id`),
  KEY `idx_active` (`is_active`),
  KEY `idx_deleted` (`deleted_at`),
  CONSTRAINT `contents_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=224 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='콘텐츠(Content) 테이블 - 디스펜서 인쇄물 콘텐츠 정보';

INSERT INTO `contents` VALUES("133","31","한글놀이","유치원 교육용 한글놀이 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202511/90da2db6ce30960004d7e291499b3b1e.jpg","","","A5","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("134","31","옐로카","유치원 교육용 옐로카 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202511/a47f2aa14eb85e8731fc6cc7b45068db.jpg","","","10x15cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("135","31","숫자놀이","유치원 교육용 숫자놀이 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202511/8d0bb85171ce93ce97863fde7e655213.jpg","","","8x12cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("136","31","무지개 심볼","유치원 교육용 무지개 심볼 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202511/feab419108d275cf3290dbd6a323689c.jpg","","","A4","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("137","31","동물친구들","유치원 교육용 동물친구들 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202511/db7aab0784728ef7449bd545fdb480b2.jpg","","","A5","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("138","32","화이트플라워","피부관리실용 화이트플라워 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202511/1c88fa52fde0074cd644bc0e7aa4ff67.jpg","","","10x15cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("139","32","페이스케어","피부관리실용 페이스케어 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202511/4a56b026f71869259de48cebf29d244a.jpg","","","8x12cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("140","32","캔들세트","피부관리실용 캔들세트 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202511/d6e6a3865437d760412fd3bb60037a57.jpg","","","A4","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("141","32","스톤캔들","피부관리실용 스톤캔들 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202511/7053f8c169ee628503f409c8abfc7c23.jpg","","","A5","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("142","32","바디타이포그라피","피부관리실용 바디타이포그라피 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202511/0268f11351478de9e875025fd0144a56.jpg","","","10x15cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("143","32","릴랙스 타이포그라피","피부관리실용 릴랙스 타이포그라피 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202511/423e296f0e87e2be2b36a5537655bfd3.jpg","","","8x12cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("144","32","라인드로잉","피부관리실용 라인드로잉 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202511/1de578d7a7a4218fed424c97e7e3a112.jpg","","","A4","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("145","33","화이트플라워","호텔용 화이트플라워 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202511/089f18e336f97e53492a830edc675dd0.jpg","","","A5","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("146","33","핑크플라워","호텔용 핑크플라워 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202511/165e2716d5972601ee03c03482364f14.jpg","","","10x15cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("147","33","소프트패턴","호텔용 소프트패턴 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202511/78ce7e8de66479a4f91c6159ca9c2923.jpg","","","8x12cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("148","33","모던아트2","호텔용 모던아트2 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202511/d6b04001bf5386d095e5bfb9866ca6e0.jpg","","","A4","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("149","33","모던아트","호텔용 모던아트 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202511/4aee695ab4a74e59c30b59f1428b39af.jpg","","","A5","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("150","33","레드웨이브","호텔용 레드웨이브 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202511/5a0e606bb1794ffa222604c919edd9f0.jpg","","","10x15cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("151","33","골드웨이브","호텔용 골드웨이브 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202511/ba91195468d99ee30e3b17f50f92cf7f.jpg","","","8x12cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("152","34","필드볼","골프장용 필드볼 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202511/8779df86146cd28a3e29293e7fd5cddd.jpg","","","A4","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("153","34","골프 타이포그라피","골프장용 골프 타이포그라피 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202511/bfd21588c21d133dfc8a67c03140d995.jpg","","","A5","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("154","34","클럽샷","골프장용 클럽샷 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202511/2c56a7aadd875a2f481b63af9116ce4f.jpg","","","10x15cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("155","34","카트 일러스트","골프장용 카트 일러스트 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202511/6b316ee609bcb2bb40ce93837cdcc8e5.jpg","","","8x12cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("156","34","스윙 일러스트","골프장용 스윙 일러스트 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202511/6b6d94ebb69450f9b63750cbb2c21635.jpg","","","A4","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("157","34","모던플라워","골프장용 모던플라워 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202511/d092a89b7f31c8a9820f9430ebf41a1b.jpg","","","A5","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("158","34","그린필드","골프장용 그린필드 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202511/2d0fd27a1e66dab23dd06a1030f8b358.jpg","","","10x15cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("159","35","띵크심플 타이포그래픽","회사용 띵크심플 타이포그래픽 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202511/90a1c7dff5923b50aee097b26e3f935e.jpg","","","8x12cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("160","35","크리에이트밸류 타이포그래픽","회사용 크리에이트밸류 타이포그래픽 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202511/354dc9e4d25091bf71eba3392d9b533e.jpg","","","A4","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("161","35","아이디어노트","회사용 아이디어노트 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202511/99635f3e05ed3d25f2e925c746bf8768.jpg","","","A5","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("162","35","미니멀그래픽2","회사용 미니멀그래픽2 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202511/6c79dc47060f36ca6ac18e6c682c53f4.jpg","","","10x15cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("163","35","가드닝플라워","회사용 가드닝플라워 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202511/952b99f23991890229a20745d061255c.jpg","","","8x12cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("164","35","미니멀그래픽","회사용 미니멀그래픽 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202511/5b891593caea68a8083ec26f1a7cbd62.jpg","","","A4","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("165","36","웨딩링","웨딩홀용 웨딩링 컨텐츠","SEASONAL","//alltogreen.com/web/product/medium/202511/4ca31fb1eb6694450d65407abdb88dc4.jpg","","","A5","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("166","36","샴페인플라워","웨딩홀용 샴페인플라워 컨텐츠","SEASONAL","//alltogreen.com/web/product/medium/202511/447f10e1d22c3ba4d0aa1829e3367f06.jpg","","","10x15cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("167","36","블루플라워","웨딩홀용 블루플라워 컨텐츠","SEASONAL","//alltogreen.com/web/product/medium/202511/d8398c685e4974a47bed61d8c4ef2935.jpg","","","8x12cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("168","36","로즈월","웨딩홀용 로즈월 컨텐츠","SEASONAL","//alltogreen.com/web/product/medium/202511/0f3204bc229689257320cfb1a9cafd70.jpg","","","A4","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("169","36","로맨틱캔들","웨딩홀용 로맨틱캔들 컨텐츠","SEASONAL","//alltogreen.com/web/product/medium/202511/f834d3ab315a07e3d9c80cdd3a7c218f.jpg","","","A5","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("170","36","로맨틱블룸","웨딩홀용 로맨틱블룸 컨텐츠","SEASONAL","//alltogreen.com/web/product/medium/202511/a7fce96913f99f5f6e7bd3fcf738ebb8.jpg","","","10x15cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("171","36","가든아치","웨딩홀용 가든아치 컨텐츠","SEASONAL","//alltogreen.com/web/product/medium/202511/479d85746e669699f32565338fd70121.jpg","","","8x12cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("172","37","음식은 먹을 만큼만","학교용 음식은 먹을 만큼만 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202509/7ce9d3aeb6fda73d34ea1ead3f86c7c1.png","","","A4","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("173","37","올바르게 학교 기물 사용","학교용 올바르게 학교 기물 사용 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202509/637bd3eabd8a6334ef48d3f56334f7a0.png","","","A5","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("174","37","쓰레기를 아무곳에나","학교용 쓰레기를 아무곳에나 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202509/0f652a577e1723e9788be61e3fba942f.png","","","10x15cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("175","37","실내화를 신고 등·하교를 하지 않아요","학교용 실내화를 신고 등·하교를 하지 않아요 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202509/2d28f3d3a1c69491475862856e889f0d.png","","","8x12cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("176","37","등교 시간은 학교와 우리의 약속","학교용 등교 시간은 학교와 우리의 약속 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202509/3081425bfb98a655f9f59afcf18af698.png","","","A4","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("177","37","나는 친구의 방어자 입니다","학교용 나는 친구의 방어자 입니다 컨텐츠","BASIC","//alltogreen.com/web/product/medium/202509/ba7fbe30469bded9bc431f6971fd0aca.png","","","A5","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("178","38","Cliff Walk at Pourville 1882 - 모네","모네의 Cliff Walk at Pourville 1882","CUSTOM","//alltogreen.com/web/product/medium/202509/659d04091ec8c29b2865883fd407d2e8.png","","","10x15cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("179","31","Improvisation No. 30 (Cannons) 1913","Improvisation No. 30 (Cannons) 1913","BASIC","//alltogreen.com/web/product/medium/202509/e2f3ec801ca7e14e8c147afabe10507a.png","","","8x12cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("180","38","Irises 1914 - 모네","모네의 Irises 1914","CUSTOM","//alltogreen.com/web/product/medium/202509/221332d20ca0ecba9aa8d3fbc3523b8a.png","","","A4","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("181","31","Landscape with Two Poplars 1912","Landscape with Two Poplars 1912","BASIC","//alltogreen.com/web/product/medium/202509/f85ca8393dc3bf9648b9547933057996.png","","","A5","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("182","31","Pastoral Landscape with Ruins 1664","Pastoral Landscape with Ruins 1664","BASIC","//alltogreen.com/web/product/medium/202509/ab57ada7d53500e20b5d228ab0286464.png","","","10x15cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("183","38","Self-Portrait 1887 - 빈센트 반 고흐","빈센트 반 고흐의 Self-Portrait 1887","CUSTOM","//alltogreen.com/web/product/medium/202509/05d6dfac12a3cfd5bf8c2c690dd2594c.png","","","8x12cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("184","31","Still Life with Geranium 1906","Still Life with Geranium 1906","BASIC","//alltogreen.com/web/product/medium/202509/6b7747da26db396ee85f5a996e153899.png","","","A4","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("185","31","Still Life—Strawberries, Nuts, &c. 1822","Still Life—Strawberries, Nuts, &c. 1822","BASIC","//alltogreen.com/web/product/medium/202509/f27f90efbebca67fa46440379e49dea4.png","","","A5","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("186","38","The Basket of Apples 1893 - Paul Cezanne","Paul Cezanne의 The Basket of Apples 1893","CUSTOM","//alltogreen.com/web/product/medium/202509/863b56d3462213e9876a6cd13979c970.png","","","10x15cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("187","31","The Fountain, Villa Torlonia, Frascati, Italy 1907","The Fountain, Villa Torlonia, Frascati, Italy 1907","BASIC","//alltogreen.com/web/product/medium/202509/68597521056fa8c2deb22c3bc212ab48.png","","","8x12cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("188","31","Woman at Her Toilette 1875","Woman at Her Toilette 1875","BASIC","//alltogreen.com/web/product/medium/202509/7850445ac3270f31d0ff43bd4d2acf8e.png","","","A4","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("189","31","Woman at the Piano 1875","Woman at the Piano 1875","BASIC","//alltogreen.com/web/product/medium/202509/cc7d8cfa12b743f304ad0afad4ac4960.png","","","A5","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("190","31","Woman Reading 1880","Woman Reading 1880","BASIC","//alltogreen.com/web/product/medium/202509/9120bf4af50db7581d422cfa603f4295.png","","","10x15cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("191","31","파리 거리 비오는 날 1877","파리 거리 비오는 날 1877","BASIC","//alltogreen.com/web/product/medium/202509/82bae1220eac063096f9276afe627e82.png","","","8x12cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("192","34","퍼팅 라인 밟지 않기","골프장 에티켓 - 퍼팅 라인 밟지 않기","BASIC","//alltogreen.com/web/product/medium/202509/2d2a01c1142a55453c7f704c02b1ccfa.png","","","A4","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("193","34","예의와 배려는 라운딩의 기초상식","골프장 에티켓 - 예의와 배려는 라운딩의 기초상식","BASIC","//alltogreen.com/web/product/medium/202509/7ea8e12ea4129a6f9d5adae514128369.png","","","A5","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("194","34","연습 스윙 적당하게","골프장 에티켓 - 연습 스윙 적당하게","BASIC","//alltogreen.com/web/product/medium/202509/125e4aa6a5d21938c3a904a5d69ebaca.png","","","10x15cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("195","34","스윙하기 전 사람 있는지 확인","골프장 에티켓 - 스윙하기 전 사람 있는지 확인","BASIC","//alltogreen.com/web/product/medium/202509/c25f5c594658d985be583deeb05c7c7c.png","","","8x12cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("196","34","동반자 스윙 시 정숙","골프장 에티켓 - 동반자 스윙 시 정숙","BASIC","//alltogreen.com/web/product/medium/202509/26a6caf78fdb96e161cee3235cee6772.png","","","A4","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("197","34","그린에서 뛰지 않기","골프장 에티켓 - 그린에서 뛰지 않기","BASIC","//alltogreen.com/web/product/medium/202509/89a747093c72beb7f518301cbc3584e3.png","","","A5","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("198","34","골프에 티켓 지켜주세요","골프장 에티켓 - 골프에 티켓 지켜주세요","BASIC","//alltogreen.com/web/product/medium/202509/f6f140dfafa94df61c33c2537d4ea6bc.png","","","10x15cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("199","34","골프 카트로 카트라이더를","골프장 에티켓 - 골프 카트로 카트라이더를","BASIC","//alltogreen.com/web/product/medium/202509/629e593d73f476b082a0f1b6ebb69b9d.png","","","8x12cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("200","35","회의 중에 폰은 안녕","회사 에티켓 - 회의 중에 폰은 안녕","BASIC","//alltogreen.com/web/product/medium/202509/625ed5a12923fc26200afffe728e9eba.png","","","A4","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("201","35","팀워크 없는 팀은 그냥 워크","회사 에티켓 - 팀워크 없는 팀은 그냥 워크","BASIC","//alltogreen.com/web/product/medium/202509/6361387d34849b6f5d7b8322628866e6.png","","","A5","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("202","35","퇴근할 때 전원 끄기","회사 에티켓 - 퇴근할 때 전원 끄기","BASIC","//alltogreen.com/web/product/medium/202509/dee654db98da151f654a071e3f2105eb.png","","","10x15cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("203","35","퇴근 시간에 업무 멈춰","회사 에티켓 - 퇴근 시간에 업무 멈춰","BASIC","//alltogreen.com/web/product/medium/202509/de6ededbac2957d075295f480784e466.png","","","8x12cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("204","35","일도 성과도 제대로 터트리자","회사 에티켓 - 일도 성과도 제대로 터트리자","BASIC","//alltogreen.com/web/product/medium/202509/4c35ef9c5f0fd2830fb04ceb1eac7255.png","","","A4","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("205","35","오늘도 힘내요","회사 에티켓 - 오늘도 힘내요","BASIC","//alltogreen.com/web/product/medium/202509/d68879e3c0878474adf9780d3e13767d.png","","","A5","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("206","35","업무의 우선 순위 체크하기","회사 에티켓 - 업무의 우선 순위 체크하기","BASIC","//alltogreen.com/web/product/medium/202509/aaea91ce77f48f09b0c3658c0078364f.png","","","10x15cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("207","35","계단을 밟아야 계단 위에 올라설 수 있다_터키 격언","회사 에티켓 - 계단을 밟아야 계단 위에 올라설 수 있다_터키 격언","BASIC","//alltogreen.com/web/product/medium/202509/198686ddc5ae1530535747422c37ff09.png","","","8x12cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("208","39","휴지는 필요한 만큼만","피트니스 에티켓 - 휴지는 필요한 만큼만","BASIC","//alltogreen.com/web/product/medium/202509/29382533b6d36d4c0dfa501ad8697fb1.png","","","A4","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("209","39","준비운동은 필수 운동","피트니스 에티켓 - 준비운동은 필수 운동","BASIC","//alltogreen.com/web/product/medium/202509/4191ae47022afc55ed5d86de3331ab87.png","","","A5","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("210","39","운동기구는 모두의 것","피트니스 에티켓 - 운동기구는 모두의 것","BASIC","//alltogreen.com/web/product/medium/202509/1cd33ca020a73a4b4b351c36238d1fb2.png","","","10x15cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("211","39","쓸만큼만 가져가기","피트니스 에티켓 - 쓸만큼만 가져가기","BASIC","//alltogreen.com/web/product/medium/202509/b59344ce6dbcfef135506dfe7aa4c63c.png","","","8x12cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("212","39","사용 후 제자리에","피트니스 에티켓 - 사용 후 제자리에","BASIC","//alltogreen.com/web/product/medium/202509/456b40411ed5fe0ee45c45886a573b1f.png","","","A4","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("213","39","벤치프레스에서 딴짓하기 금지","피트니스 에티켓 - 벤치프레스에서 딴짓하기 금지","BASIC","//alltogreen.com/web/product/medium/202509/21125ee4e3e7f638a8c799e172e3cdab.png","","","A5","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("214","39","무리하면 다쳐요","피트니스 에티켓 - 무리하면 다쳐요","BASIC","//alltogreen.com/web/product/medium/202509/fe1cf0eb687274b3ee3628d051948a6f.png","","","10x15cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("215","39","기구 살살 내려놓기","피트니스 에티켓 - 기구 살살 내려놓기","BASIC","//alltogreen.com/web/product/medium/202509/03e6f3ea71a222d580d335d6fce0cf5f.png","","","8x12cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("216","40","30초 이상 손씻기","병원/약국 에티켓 - 30초 이상 손씻기","BASIC","//alltogreen.com/web/product/medium/202509/67139b90ab086a192a2252c96125e20e.png","","","A4","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("217","40","폐의 약품은 약국으로","병원/약국 에티켓 - 폐의 약품은 약국으로","BASIC","//alltogreen.com/web/product/medium/202509/369dacf1790b21984dc681383a3606f4.png","","","A5","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("218","40","임신 알레르기는 미리 알려주세요","병원/약국 에티켓 - 임신 알레르기는 미리 알려주세요","BASIC","//alltogreen.com/web/product/medium/202509/fe8bd9229b47dc10460be36514c861e2.png","","","10x15cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("219","40","의사의 진찰에 경청해 주세요","병원/약국 에티켓 - 의사의 진찰에 경청해 주세요","BASIC","//alltogreen.com/web/product/medium/202509/df6d083a665528e9dfe655666cd2d15d.png","","","8x12cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("220","40","예약 시간 준수 모두를 위한 배려","병원/약국 에티켓 - 예약 시간 준수 모두를 위한 배려","BASIC","//alltogreen.com/web/product/medium/202509/2b3a5de5025519b16c4231b084d00a6c.png","","","A4","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("221","40","물 마시기 건강의 시작","병원/약국 에티켓 - 물 마시기 건강의 시작","BASIC","//alltogreen.com/web/product/medium/202509/77a6e647564d50ee4d3dbd821bf2f5ce.png","","","A5","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("222","40","기침이 있을 땐 마스크 착용","병원/약국 에티켓 - 기침이 있을 땐 마스크 착용","BASIC","//alltogreen.com/web/product/medium/202509/e666914a0749d37b50589796d8ac0932.png","","","10x15cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","1");
INSERT INTO `contents` VALUES("223","40","건강 인간의 권리","병원/약국 에티켓 - 건강 인간의 권리","BASIC","//alltogreen.com/web/product/medium/202509/0ba4b4ba75a1f8896bea7d8010256127.png","","","8x12cm","COMPANY","","1","1","0","","2025-11-11 19:22:23","2025-11-12 11:29:03","1","2");

DROP TABLE IF EXISTS `customer_sites`;
CREATE TABLE `customer_sites` (
  `site_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '현장 ID (PK)',
  `customer_id` int(11) NOT NULL COMMENT '고객 ID (FK -> customers)',
  `site_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '현장명 (예: 본사 1층 로비, 강남점)',
  `address` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '설치 주소',
  `contact_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '현장 담당자 이름',
  `contact_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '현장 담당자 연락처',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '특이사항 (설치 위치, 주의사항 등)',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '활성 상태',
  `deleted_at` datetime DEFAULT NULL COMMENT '삭제일시 (Soft Delete)',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '수정일시',
  `created_by` int(11) DEFAULT NULL COMMENT '생성자 user_id',
  `updated_by` int(11) DEFAULT NULL COMMENT '수정자 user_id',
  PRIMARY KEY (`site_id`),
  KEY `idx_customer` (`customer_id`),
  KEY `idx_active` (`is_active`),
  KEY `idx_deleted` (`deleted_at`),
  CONSTRAINT `customer_sites_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='고객 현장(Site) 테이블 - 고객의 디스펜서 설치 현장 정보 (1고객 N현장 가능)';

INSERT INTO `customer_sites` VALUES("1","1","스타벅스 강남점","서울특별시 강남구 테헤란로 427","박점장","02-1234-5001","1층 로비 중앙","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `customer_sites` VALUES("2","1","스타벅스 여의도점","서울특별시 영등포구 여의대로 108","이점장","02-1234-5002","1층 입구 오른쪽","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `customer_sites` VALUES("3","1","스타벅스 홍대점","서울특별시 마포구 양화로 160","최점장","02-1234-5003","2층 계단 옆","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `customer_sites` VALUES("4","1","스타벅스 명동점","서울특별시 중구 명동길 52","정점장","02-1234-5004","1층 카운터 뒤","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `customer_sites` VALUES("5","1","스타벅스 코엑스점","서울특별시 강남구 영동대로 513","한점장","02-1234-5005","B1층 중앙홀","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `customer_sites` VALUES("6","2","롯데호텔 로비","서울특별시 중구 소공로 30","김로비매니저","02-2222-1001","메인 로비 안내데스크 옆","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `customer_sites` VALUES("7","2","롯데호텔 연회장","서울특별시 중구 소공로 30","신연회팀장","02-2222-1002","2층 연회장 입구","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `customer_sites` VALUES("8","2","롯데호텔 비즈니스센터","서울특별시 중구 소공로 30","유비즈니스매니저","02-2222-1003","3층 비즈니스센터 내부","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `customer_sites` VALUES("9","3","신세계 강남 1층","서울특별시 서초구 신반포로 176","노팀장","02-3333-1001","1층 화장품 코너 중앙","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `customer_sites` VALUES("10","3","신세계 강남 지하1층","서울특별시 서초구 신반포로 176","하매니저","02-3333-1002","B1층 식품관 입구","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `customer_sites` VALUES("11","3","신세계 강남 6층","서울특별시 서초구 신반포로 176","홍매니저","02-3333-1003","6층 레스토랑가","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `customer_sites` VALUES("12","3","신세계 강남 주차장","서울특별시 서초구 신반포로 176","표관리팀","02-3333-1004","지하 주차장 입구","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `customer_sites` VALUES("13","4","현대 판교 본관 1층","경기도 성남시 분당구 판교역로 146","고매니저","031-4444-1001","본관 1층 정문","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","8","0");
INSERT INTO `customer_sites` VALUES("14","4","현대 판교 식품관","경기도 성남시 분당구 판교역로 146","권팀장","031-4444-1002","식품관 중앙 통로","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","8","0");
INSERT INTO `customer_sites` VALUES("15","4","현대 판교 주차장","경기도 성남시 분당구 판교역로 146","석관리자","031-4444-1003","지하 주차장","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","8","0");
INSERT INTO `customer_sites` VALUES("16","5","올투그린 본사","경기도 용인시 기흥구 용구대로 2377","선총무팀장","031-5555-1001","1층 로비","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","8","0");
INSERT INTO `customer_sites` VALUES("17","5","올투그린 R&D센터","경기도 용인시 기흥구 용구대로 2377","안연구소장","031-5555-1002","연구동 1층","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","8","0");
INSERT INTO `customer_sites` VALUES("18","6","삼성 수원 제1공장","경기도 수원시 영통구 삼성로 129","배관리부장","031-6666-1001","공장동 로비","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","8","0");
INSERT INTO `customer_sites` VALUES("19","6","삼성 수원 본관","경기도 수원시 영통구 삼성로 129","임본부장","031-6666-1002","본관 로비","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","8","0");
INSERT INTO `customer_sites` VALUES("20","6","삼성 수원 식당","경기도 수원시 영통구 삼성로 129","방급식팀장","031-6666-1003","구내식당 입구","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","8","0");
INSERT INTO `customer_sites` VALUES("21","7","해운대그랜드 로비","부산광역시 해운대구 중동 1411-23","손지배인","051-7777-1001","메인 로비","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","9","0");
INSERT INTO `customer_sites` VALUES("22","7","해운대그랜드 연회장","부산광역시 해운대구 중동 1411-23","양연회팀","051-7777-1002","2층 연회장","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","9","0");
INSERT INTO `customer_sites` VALUES("23","8","파라다이스부산 로비","부산광역시 해운대구 중동 1408-5","변로비매니저","051-8888-1001","1층 로비","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","9","0");
INSERT INTO `customer_sites` VALUES("24","8","파라다이스부산 스파","부산광역시 해운대구 중동 1408-5","황스파팀장","051-8888-1002","지하1층 스파","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","9","0");
INSERT INTO `customer_sites` VALUES("25","9","롯데 부산 1층","부산광역시 부산진구 가야대로 772","서매니저","051-9999-1001","1층 명품관","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","9","0");
INSERT INTO `customer_sites` VALUES("26","9","롯데 부산 식품관","부산광역시 부산진구 가야대로 772","전팀장","051-9999-1002","지하1층 식품관","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","9","0");
INSERT INTO `customer_sites` VALUES("27","9","롯데 부산 주차장","부산광역시 부산진구 가야대로 772","탁관리자","051-9999-1003","주차장 입구","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","9","0");
INSERT INTO `customer_sites` VALUES("28","10","신세계 대구 1층","대구광역시 동구 동부로 149","피매니저","053-1000-1001","1층 중앙홀","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","10","0");
INSERT INTO `customer_sites` VALUES("29","10","신세계 대구 식품관","대구광역시 동구 동부로 149","명팀장","053-1000-1002","지하 식품관","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","10","0");
INSERT INTO `customer_sites` VALUES("30","11","인터불고호텔 로비","대구광역시 중구 동성로 141","기로비매니저","053-1100-1001","1층 로비","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","10","0");
INSERT INTO `customer_sites` VALUES("31","12","롯데시네마 대구점","대구광역시 동구 팔공로 177","목매니저","053-1200-1001","1층 입구","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","10","0");
INSERT INTO `customer_sites` VALUES("32","13","광주신세계 1층","광주광역시 서구 무진대로 932","장팀장","062-1300-1001","1층 중앙","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","11","0");
INSERT INTO `customer_sites` VALUES("33","14","김대중센터 로비","광주광역시 서구 상무누리로 30","육관리자","062-1400-1001","로비","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","11","0");
INSERT INTO `customer_sites` VALUES("34","15","조선대병원 로비","광주광역시 동구 필문대로 365","추팀장","062-1500-1001","본관 로비","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","11","0");
INSERT INTO `customer_sites` VALUES("35","16","대전컨벤션 로비","대전광역시 유성구 엑스포로 107","구매니저","042-1600-1001","중앙 로비","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","12","0");
INSERT INTO `customer_sites` VALUES("36","17","KAIST 본관","대전광역시 유성구 대학로 291","나총무팀","042-1700-1001","본관 1층","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","12","0");
INSERT INTO `customer_sites` VALUES("37","18","갤러리아 대전점","대전광역시 서구 대덕대로 211","두매니저","042-1800-1001","1층 입구","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","12","0");
INSERT INTO `customer_sites` VALUES("38","19","인천공항 T1","인천광역시 중구 공항로 272","류관리팀","032-1900-1001","출국장","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","13","0");
INSERT INTO `customer_sites` VALUES("39","20","파라다이스시티 로비","인천광역시 중구 영종해안남로 321","봉지배인","032-2000-1001","호텔 로비","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","13","0");
INSERT INTO `customer_sites` VALUES("40","31","??????","?????? ??????","???Ŵ???","010-1111-1111","","1","","2025-11-12 02:15:32","2025-11-12 02:15:32","","");
INSERT INTO `customer_sites` VALUES("41","32","?ؿ?????","?λ??? ?ؿ??뱸","?̽???","010-2222-2222","","1","","2025-11-12 02:15:32","2025-11-12 02:15:32","","");
INSERT INTO `customer_sites` VALUES("42","33","????","?뱸?? ??????","??????","010-3333-3333","","1","","2025-11-12 02:15:32","2025-11-12 02:15:32","","");
INSERT INTO `customer_sites` VALUES("43","31","??????","????","???Ŵ???","010-1111-1111","","1","","2025-11-12 02:16:16","2025-11-12 02:16:16","","");
INSERT INTO `customer_sites` VALUES("44","32","?ؿ?????","?λ?","?̽???","010-2222-2222","","1","","2025-11-12 02:16:16","2025-11-12 02:16:16","","");
INSERT INTO `customer_sites` VALUES("45","33","????","?뱸","??????","010-3333-3333","","1","","2025-11-12 02:16:16","2025-11-12 02:16:16","","");

DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '고객 ID (PK)',
  `user_id` int(11) NOT NULL COMMENT '사용자 ID (FK -> users, 고객 로그인 계정)',
  `vendor_id` int(11) DEFAULT NULL COMMENT '소속 밴더 ID (FK -> vendors, NULL: 직거래)',
  `company_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '회사명',
  `business_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '사업자등록번호',
  `ceo_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '대표자명',
  `business_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '업태',
  `business_category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '업종',
  `address` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '본사 주소',
  `payment_method` enum('CMS','CARD','TRANSFER','ONE_TIME') COLLATE utf8mb4_unicode_ci DEFAULT 'CARD',
  `bank_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '은행명 (CMS인 경우)',
  `account_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '계좌번호 (CMS인 경우)',
  `card_number_masked` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '카드번호 마스킹 (CARD인 경우, 예: ****-****-****-1234)',
  `billing_key` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '결제 키 (PG사 빌링키)',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '활성 상태',
  `deleted_at` datetime DEFAULT NULL COMMENT '삭제일시 (Soft Delete)',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '수정일시',
  `created_by` int(11) DEFAULT NULL COMMENT '생성자 user_id',
  `updated_by` int(11) DEFAULT NULL COMMENT '수정자 user_id',
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_vendor` (`vendor_id`),
  KEY `idx_active` (`is_active`),
  KEY `idx_deleted` (`deleted_at`),
  CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `customers_ibfk_2` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`vendor_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='고객(Customer) 테이블 - 구독 서비스 이용 고객 정보';

INSERT INTO `customers` VALUES("1","21","10","스타벅스코리아(주)","1112223330","손담당자1","서비스업","커피전문점","서울특별시 중구 을지로 100","ONE_TIME","","","****-****-****-1234","billing_key_001","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 12:23:18","7","0");
INSERT INTO `customers` VALUES("2","22","7","롯데호텔서울","1112223331","양담당자2","숙박업","특급호텔","서울특별시 중구 소공로 30","CMS","신한은행","100-123-456789","","","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `customers` VALUES("3","23","7","신세계백화점 강남점","1112223332","변담당자3","소매업","백화점","서울특별시 서초구 신반포로 176","ONE_TIME","","","****-****-****-5678","billing_key_002","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 12:04:37","7","0");
INSERT INTO `customers` VALUES("4","24","8","현대백화점 판교점","1112223333","황담당자4","소매업","백화점","경기도 성남시 분당구 판교역로 146","CARD","","","****-****-****-9012","billing_key_003","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","8","0");
INSERT INTO `customers` VALUES("5","25","8","(주)올투그린","1112223334","서담당자5","제조업","IT기기","경기도 용인시 기흥구 용구대로 2377","CMS","국민은행","100-234-567890","","","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","8","0");
INSERT INTO `customers` VALUES("6","26","8","삼성전자 수원사옥","1112223335","전담당자6","제조업","전자기기","경기도 수원시 영통구 삼성로 129","ONE_TIME","","","****-****-****-3456","billing_key_004","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 12:04:37","8","0");
INSERT INTO `customers` VALUES("7","27","9","해운대그랜드호텔","1112223336","탁담당자7","숙박업","호텔","부산광역시 해운대구 중동 1411-23","ONE_TIME","","","****-****-****-7890","billing_key_005","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 12:04:37","9","0");
INSERT INTO `customers` VALUES("8","28","9","파라다이스호텔부산","1112223337","피담당자8","숙박업","특급호텔","부산광역시 해운대구 중동 1408-5","CMS","우리은행","200-345-678901","","","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","9","0");
INSERT INTO `customers` VALUES("9","29","9","롯데백화점 부산본점","1112223338","명담당자9","소매업","백화점","부산광역시 부산진구 가야대로 772","ONE_TIME","","","****-****-****-1357","billing_key_006","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 12:04:37","9","0");
INSERT INTO `customers` VALUES("10","30","10","대구신세계백화점","1112223339","기담당자10","소매업","백화점","대구광역시 동구 동부로 149","ONE_TIME","","","****-****-****-2468","billing_key_007","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 12:04:37","10","0");
INSERT INTO `customers` VALUES("11","31","10","인터불고호텔대구","1112223340","목담당자11","숙박업","호텔","대구광역시 중구 동성로 141","CMS","IBK기업은행","300-456-789012","","","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","10","0");
INSERT INTO `customers` VALUES("12","32","10","롯데시네마대구","1112223341","장담당자12","서비스업","영화관","대구광역시 동구 팔공로 177","ONE_TIME","","","****-****-****-3691","billing_key_008","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 12:04:37","10","0");
INSERT INTO `customers` VALUES("13","33","11","광주신세계백화점","1112223342","육담당자13","소매업","백화점","광주광역시 서구 무진대로 932","ONE_TIME","","","****-****-****-4702","billing_key_009","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 12:04:37","11","0");
INSERT INTO `customers` VALUES("14","34","11","김대중컨벤션센터","1112223343","추담당자14","서비스업","컨벤션센터","광주광역시 서구 상무누리로 30","TRANSFER","하나은행","400-567-890123","","","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 12:04:37","11","0");
INSERT INTO `customers` VALUES("15","35","11","조선대학교병원","1112223344","구담당자15","의료업","종합병원","광주광역시 동구 필문대로 365","ONE_TIME","","","****-****-****-5813","billing_key_010","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 12:04:37","11","0");
INSERT INTO `customers` VALUES("16","36","","대전컨벤션센터","1112223345","나담당자16","서비스업","컨벤션센터","대전광역시 유성구 엑스포로 107","CARD","","","****-****-****-6924","billing_key_011","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","12","0");
INSERT INTO `customers` VALUES("17","37","","한국과학기술원(KAIST)","1112223346","두담당자17","교육업","대학교","대전광역시 유성구 대학로 291","CMS","농협은행","500-678-901234","","","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","12","0");
INSERT INTO `customers` VALUES("18","38","","갤러리아타임월드","1112223347","류담당자18","소매업","백화점","대전광역시 서구 대덕대로 211","CARD","","","****-****-****-7035","billing_key_012","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","12","0");
INSERT INTO `customers` VALUES("19","39","","인천국제공항 제1터미널","1112223348","봉담당자19","교통업","공항","인천광역시 중구 공항로 272","CARD","","","****-****-****-8146","billing_key_013","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","13","0");
INSERT INTO `customers` VALUES("20","40","","파라다이스시티","1112223349","사담당자20","숙박업","복합리조트","인천광역시 중구 영종해안남로 321","CMS","새마을금고","600-789-012345","","","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","13","0");
INSERT INTO `customers` VALUES("21","41","0","CGV여의도","1112223350","손담당자1","서비스업","영화관","서울특별시 영등포구 의사당대로 83","CARD","","","****-****-****-9257","billing_key_014","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `customers` VALUES("22","42","0","LG디스플레이본사","1112223351","양담당자2","제조업","디스플레이","서울특별시 영등포구 여의대로 128","TRANSFER","신한은행","700-890-123456","","","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 12:04:37","1","0");
INSERT INTO `customers` VALUES("23","43","0","SK텔레콤타워","1112223352","변담당자3","통신업","이동통신","서울특별시 중구 을지로 65","CARD","","","****-****-****-0368","billing_key_015","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `customers` VALUES("24","44","","현대자동차 울산공장","1112223353","황담당자4","제조업","자동차","울산광역시 북구 연암로 700","CARD","","","****-****-****-1479","billing_key_016","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","14","0");
INSERT INTO `customers` VALUES("25","45","","롯데호텔울산","1112223354","서담당자5","숙박업","호텔","울산광역시 남구 삼산로 282","CMS","수협은행","800-901-234567","","","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","14","0");
INSERT INTO `customers` VALUES("26","46","","제주국제공항","1112223355","전담당자6","교통업","공항","제주특별자치도 제주시 공항로 2","CARD","","","****-****-****-2580","billing_key_017","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","15","0");
INSERT INTO `customers` VALUES("27","47","","제주신라호텔","1112223356","탁담당자7","숙박업","특급호텔","제주특별자치도 서귀포시 중문관광로 72","CMS","제주은행","900-012-345678","","","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","15","0");
INSERT INTO `customers` VALUES("28","48","","한라산국립공원센터","1112223357","피담당자8","관광업","국립공원","제주특별자치도 제주시 1100로 2070-61","CARD","","","****-****-****-3691","billing_key_018","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","15","0");
INSERT INTO `customers` VALUES("29","49","","강원대학교춘천캠퍼스","1112223358","명담당자9","교육업","대학교","강원도 춘천시 강원대학길 1","CMS","신협","101-123-456789","","","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","16","0");
INSERT INTO `customers` VALUES("30","50","","평창알펜시아리조트","1112223359","기담당자10","숙박업","스키리조트","강원도 평창군 대관령면 솔봉로 325","CARD","","","****-****-****-4702","billing_key_019","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","16","0");
INSERT INTO `customers` VALUES("31","83","1","(주)서울카페","111-11-11111","김사장","","","서울시 강남구 테헤란로 100","CARD","","","","","1","","2024-10-01 00:00:00","2025-11-12 02:11:45","","");
INSERT INTO `customers` VALUES("32","84","1","부산식당","222-22-22222","이사장","","","부산시 해운대구 해운대로 200","CMS","","","","","1","","2024-10-15 00:00:00","2025-11-12 02:11:45","","");
INSERT INTO `customers` VALUES("33","85","2","대구병원","333-33-33333","박원장","","","대구시 수성구 동대구로 300","CARD","","","","","1","","2024-11-01 00:00:00","2025-11-12 02:11:45","","");
INSERT INTO `customers` VALUES("34","86","2","인천호텔","444-44-44444","최대표","","","인천시 중구 공항로 400","TRANSFER","","","","","1","","2024-11-05 00:00:00","2025-11-12 02:11:45","","");
INSERT INTO `customers` VALUES("35","87","3","광주백화점","555-55-55555","정사장","","","광주시 서구 상무대로 500","CARD","","","","","1","","2024-11-10 00:00:00","2025-11-12 02:11:45","","");
INSERT INTO `customers` VALUES("36","88","1","대전학원","666-66-66666","강원장","","","대전시 유성구 대학로 600","CMS","","","","","1","","2024-09-01 00:00:00","2025-11-12 02:11:45","","");
INSERT INTO `customers` VALUES("37","89","2","울산공장","777-77-77777","조대표","","","울산시 남구 산업로 700","CARD","","","","","1","","2024-09-15 00:00:00","2025-11-12 02:11:45","","");
INSERT INTO `customers` VALUES("38","90","3","제주리조트","888-88-88888","한사장","","","제주시 노형동 관광로 800","TRANSFER","","","","","1","","2024-08-01 00:00:00","2025-11-12 02:11:45","","");
INSERT INTO `customers` VALUES("39","91","1","경기마트","999-99-99999","송대표","","","경기도 수원시 영통구 900","CARD","","","","","1","","2024-08-20 00:00:00","2025-11-12 02:11:45","","");
INSERT INTO `customers` VALUES("40","92","2","강원스키장","000-00-00000","유사장","","","강원도 평창군 스키로 1000","CMS","","","","","1","","2024-07-15 00:00:00","2025-11-12 02:11:45","","");
INSERT INTO `customers` VALUES("41","93","1","(??)????ī??","111-11-11111","??????","","","","CARD","","","","","1","","2024-10-01 00:00:00","2025-11-12 02:12:36","","");
INSERT INTO `customers` VALUES("42","94","1","?λ??Ĵ?","222-22-22222","?̻???","","","","CARD","","","","","1","","2024-10-15 00:00:00","2025-11-12 02:12:36","","");
INSERT INTO `customers` VALUES("43","95","2","?뱸????","333-33-33333","?ڿ???","","","","CARD","","","","","1","","2024-11-01 00:00:00","2025-11-12 02:12:36","","");

DROP TABLE IF EXISTS `device_assignments`;
CREATE TABLE `device_assignments` (
  `assignment_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '배정 ID (PK)',
  `serial_id` int(11) NOT NULL COMMENT '시리얼 ID (FK -> device_serials)',
  `customer_id` int(11) NOT NULL COMMENT '고객 ID (FK -> customers)',
  `site_id` int(11) DEFAULT NULL COMMENT '현장 ID (FK -> customer_sites, NULL: 본사)',
  `assigned_date` date NOT NULL COMMENT '배정일 (설치일)',
  `returned_date` date DEFAULT NULL COMMENT '회수일',
  `installation_location` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '설치 위치 (상세, 예: 1층 로비 입구 왼쪽)',
  `status` enum('ACTIVE','RETURNED','REPLACED') COLLATE utf8mb4_unicode_ci DEFAULT 'ACTIVE' COMMENT '배정 상태 (ACTIVE: 사용중, RETURNED: 회수됨, REPLACED: 교체됨)',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '설치 메모',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '수정일시',
  `created_by` int(11) DEFAULT NULL COMMENT '생성자 user_id',
  `updated_by` int(11) DEFAULT NULL COMMENT '수정자 user_id',
  PRIMARY KEY (`assignment_id`),
  KEY `idx_serial` (`serial_id`),
  KEY `idx_customer` (`customer_id`),
  KEY `idx_site` (`site_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `device_assignments_ibfk_1` FOREIGN KEY (`serial_id`) REFERENCES `device_serials` (`serial_id`),
  CONSTRAINT `device_assignments_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  CONSTRAINT `device_assignments_ibfk_3` FOREIGN KEY (`site_id`) REFERENCES `customer_sites` (`site_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='디스펜서 배정(Assignment) 테이블 - 디스펜서 기기를 고객 현장에 배정한 이력';

INSERT INTO `device_assignments` VALUES("1","1","1","1","2024-02-01","0000-00-00","1층 로비 중앙 카운터 옆","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `device_assignments` VALUES("2","2","1","2","2024-02-01","0000-00-00","1층 입구 오른쪽 벽면","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `device_assignments` VALUES("3","3","1","3","2024-02-05","0000-00-00","2층 계단 상단 왼쪽","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `device_assignments` VALUES("4","4","1","4","2024-02-05","0000-00-00","1층 카운터 뒤 중앙","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `device_assignments` VALUES("5","5","1","5","2024-02-10","0000-00-00","B1층 중앙홀 기둥 옆","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `device_assignments` VALUES("6","6","2","6","2024-02-15","0000-00-00","메인 로비 안내데스크 우측","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `device_assignments` VALUES("7","7","2","7","2024-02-15","0000-00-00","2층 연회장 입구 좌측","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `device_assignments` VALUES("8","8","2","8","2024-02-20","0000-00-00","3층 비즈니스센터 입구","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `device_assignments` VALUES("9","11","3","9","2024-02-25","0000-00-00","1층 화장품 코너 중앙 기둥","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `device_assignments` VALUES("10","12","3","10","2024-02-25","0000-00-00","B1층 식품관 입구 우측","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `device_assignments` VALUES("11","13","3","11","2024-03-01","0000-00-00","6층 레스토랑가 중앙홀","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `device_assignments` VALUES("12","14","3","12","2024-03-01","0000-00-00","지하 주차장 엘리베이터 앞","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `device_assignments` VALUES("13","15","4","13","2024-03-05","0000-00-00","본관 1층 정문 중앙","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","8","0");
INSERT INTO `device_assignments` VALUES("14","16","4","14","2024-03-05","0000-00-00","식품관 중앙 통로","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","8","0");
INSERT INTO `device_assignments` VALUES("15","17","4","15","2024-03-10","0000-00-00","지하 주차장 입구","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","8","0");
INSERT INTO `device_assignments` VALUES("16","21","5","16","2024-03-15","0000-00-00","본사 1층 로비 중앙","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","8","0");
INSERT INTO `device_assignments` VALUES("17","22","5","17","2024-03-15","0000-00-00","R&D센터 연구동 1층 입구","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","8","0");
INSERT INTO `device_assignments` VALUES("18","23","6","18","2024-03-20","0000-00-00","제1공장 로비 중앙","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","8","0");
INSERT INTO `device_assignments` VALUES("19","24","6","19","2024-03-20","0000-00-00","본관 로비 안내데스크 옆","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","8","0");
INSERT INTO `device_assignments` VALUES("20","25","6","20","2024-03-25","0000-00-00","구내식당 입구 우측","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","8","0");
INSERT INTO `device_assignments` VALUES("21","31","7","21","2024-04-01","0000-00-00","메인 로비 중앙홀","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","9","0");
INSERT INTO `device_assignments` VALUES("22","32","7","22","2024-04-01","0000-00-00","2층 연회장 입구","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","9","0");
INSERT INTO `device_assignments` VALUES("23","33","8","23","2024-04-05","0000-00-00","1층 로비 안내데스크 앞","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","9","0");
INSERT INTO `device_assignments` VALUES("24","34","8","24","2024-04-05","0000-00-00","지하1층 스파 입구","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","9","0");
INSERT INTO `device_assignments` VALUES("25","35","9","25","2024-04-10","0000-00-00","1층 명품관 중앙","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","9","0");
INSERT INTO `device_assignments` VALUES("26","36","9","26","2024-04-10","0000-00-00","지하1층 식품관 입구","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","9","0");
INSERT INTO `device_assignments` VALUES("27","37","9","27","2024-04-15","0000-00-00","주차장 입구 좌측","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","9","0");
INSERT INTO `device_assignments` VALUES("28","41","10","28","2024-04-20","0000-00-00","1층 중앙홀 기둥 옆","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","10","0");
INSERT INTO `device_assignments` VALUES("29","42","10","29","2024-04-20","0000-00-00","지하 식품관 중앙 통로","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","10","0");
INSERT INTO `device_assignments` VALUES("30","43","11","30","2024-04-25","0000-00-00","호텔 1층 로비 중앙","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","10","0");
INSERT INTO `device_assignments` VALUES("31","44","12","31","2024-04-30","0000-00-00","영화관 1층 입구 우측","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","10","0");
INSERT INTO `device_assignments` VALUES("32","45","13","32","2024-05-05","0000-00-00","백화점 1층 중앙 홀","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","11","0");
INSERT INTO `device_assignments` VALUES("33","46","14","33","2024-05-10","0000-00-00","컨벤션센터 로비 중앙","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","11","0");
INSERT INTO `device_assignments` VALUES("34","47","15","34","2024-05-15","0000-00-00","병원 본관 로비 안내데스크 옆","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","11","0");
INSERT INTO `device_assignments` VALUES("35","48","16","35","2024-05-20","0000-00-00","컨벤션센터 중앙 로비","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","12","0");
INSERT INTO `device_assignments` VALUES("36","21","17","36","2024-05-25","0000-00-00","KAIST 본관 1층 중앙홀","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","12","0");
INSERT INTO `device_assignments` VALUES("37","22","18","37","2024-05-30","0000-00-00","백화점 1층 입구","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","12","0");
INSERT INTO `device_assignments` VALUES("38","23","19","38","2024-06-01","0000-00-00","공항 출국장 중앙","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","13","0");
INSERT INTO `device_assignments` VALUES("39","24","20","39","2024-06-05","0000-00-00","호텔 로비 중앙홀","ACTIVE","","2025-11-10 08:27:56","2025-11-10 08:27:56","13","0");

DROP TABLE IF EXISTS `device_logs`;
CREATE TABLE `device_logs` (
  `log_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '로그 ID (PK)',
  `serial_id` int(11) NOT NULL COMMENT '시리얼 ID (FK -> device_serials)',
  `log_type` enum('ONLINE','OFFLINE','ERROR','MAINTENANCE','RESET') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '로그 유형 (ONLINE: 온라인됨, OFFLINE: 오프라인됨, ERROR: 오류, MAINTENANCE: 유지보수, RESET: 초기화)',
  `message` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '로그 메시지',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '추가 메타데이터 (JSON)',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '로그 발생 일시',
  PRIMARY KEY (`log_id`),
  KEY `idx_serial` (`serial_id`),
  KEY `idx_type` (`log_type`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `device_logs_ibfk_1` FOREIGN KEY (`serial_id`) REFERENCES `device_serials` (`serial_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='디스펜서 로그(Device Log) 테이블 - 기기 상태 변화 및 이벤트 로그';


DROP TABLE IF EXISTS `device_serials`;
CREATE TABLE `device_serials` (
  `serial_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '시리얼 ID (PK)',
  `device_id` int(11) NOT NULL COMMENT '디스펜서 ID (FK -> devices)',
  `serial_number` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '시리얼 번호 (고유, 중복 불가)',
  `qr_code` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'QR 코드 데이터',
  `manufacture_date` date DEFAULT NULL COMMENT '제조일',
  `import_date` date DEFAULT NULL COMMENT '입고일',
  `status` enum('AVAILABLE','ASSIGNED','MAINTENANCE','RETIRED','DISPOSED') COLLATE utf8mb4_unicode_ci DEFAULT 'AVAILABLE' COMMENT '상태 (AVAILABLE: 재고, ASSIGNED: 배정됨, MAINTENANCE: 수리중, RETIRED: 회수됨, DISPOSED: 폐기)',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '특이사항',
  `deleted_at` datetime DEFAULT NULL COMMENT '삭제일시 (Soft Delete)',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '수정일시',
  `created_by` int(11) DEFAULT NULL COMMENT '생성자 user_id',
  `updated_by` int(11) DEFAULT NULL COMMENT '수정자 user_id',
  PRIMARY KEY (`serial_id`),
  UNIQUE KEY `serial_number` (`serial_number`),
  KEY `idx_device` (`device_id`),
  KEY `idx_status` (`status`),
  KEY `idx_deleted` (`deleted_at`),
  CONSTRAINT `device_serials_ibfk_1` FOREIGN KEY (`device_id`) REFERENCES `devices` (`device_id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='디스펜서 시리얼(Serial) 테이블 - 개별 디스펜서 기기 정보 (시리얼 번호 단위 관리)';

INSERT INTO `device_serials` VALUES("1","1","AG-S1-2024-0001","QR-AG-S1-2024-0001","2024-01-10","2024-01-20","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("2","1","AG-S1-2024-0002","QR-AG-S1-2024-0002","2024-01-10","2024-01-20","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("3","1","AG-S1-2024-0003","QR-AG-S1-2024-0003","2024-01-10","2024-01-20","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("4","1","AG-S1-2024-0004","QR-AG-S1-2024-0004","2024-01-15","2024-01-25","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("5","1","AG-S1-2024-0005","QR-AG-S1-2024-0005","2024-01-15","2024-01-25","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("6","1","AG-S1-2024-0006","QR-AG-S1-2024-0006","2024-02-01","2024-02-10","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("7","1","AG-S1-2024-0007","QR-AG-S1-2024-0007","2024-02-01","2024-02-10","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("8","1","AG-S1-2024-0008","QR-AG-S1-2024-0008","2024-02-15","2024-02-25","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("9","1","AG-S1-2024-0009","QR-AG-S1-2024-0009","2024-02-15","2024-02-25","AVAILABLE","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("10","1","AG-S1-2024-0010","QR-AG-S1-2024-0010","2024-03-01","2024-03-10","AVAILABLE","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("11","2","AG-S2-2024-0001","QR-AG-S2-2024-0001","2024-01-20","2024-02-01","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("12","2","AG-S2-2024-0002","QR-AG-S2-2024-0002","2024-01-20","2024-02-01","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("13","2","AG-S2-2024-0003","QR-AG-S2-2024-0003","2024-02-05","2024-02-15","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("14","2","AG-S2-2024-0004","QR-AG-S2-2024-0004","2024-02-05","2024-02-15","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("15","2","AG-S2-2024-0005","QR-AG-S2-2024-0005","2024-02-20","2024-03-01","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("16","2","AG-S2-2024-0006","QR-AG-S2-2024-0006","2024-02-20","2024-03-01","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("17","2","AG-S2-2024-0007","QR-AG-S2-2024-0007","2024-03-05","2024-03-15","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("18","2","AG-S2-2024-0008","QR-AG-S2-2024-0008","2024-03-05","2024-03-15","AVAILABLE","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("19","2","AG-S2-2024-0009","QR-AG-S2-2024-0009","2024-03-20","2024-03-30","AVAILABLE","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("20","2","AG-S2-2024-0010","QR-AG-S2-2024-0010","2024-03-20","2024-03-30","AVAILABLE","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("21","3","AG-W1-2024-0001","QR-AG-W1-2024-0001","2024-02-10","2024-02-20","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("22","3","AG-W1-2024-0002","QR-AG-W1-2024-0002","2024-02-10","2024-02-20","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("23","3","AG-W1-2024-0003","QR-AG-W1-2024-0003","2024-02-25","2024-03-05","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("24","3","AG-W1-2024-0004","QR-AG-W1-2024-0004","2024-02-25","2024-03-05","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("25","3","AG-W1-2024-0005","QR-AG-W1-2024-0005","2024-03-10","2024-03-20","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("26","3","AG-W1-2024-0006","QR-AG-W1-2024-0006","2024-03-10","2024-03-20","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("27","3","AG-W1-2024-0007","QR-AG-W1-2024-0007","2024-03-25","2024-04-05","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("28","3","AG-W1-2024-0008","QR-AG-W1-2024-0008","2024-03-25","2024-04-05","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("29","3","AG-W1-2024-0009","QR-AG-W1-2024-0009","2024-04-10","2024-04-20","AVAILABLE","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("30","3","AG-W1-2024-0010","QR-AG-W1-2024-0010","2024-04-10","2024-04-20","AVAILABLE","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("31","5","AG-P1-2024-0001","QR-AG-P1-2024-0001","2024-03-15","2024-03-25","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("32","5","AG-P1-2024-0002","QR-AG-P1-2024-0002","2024-03-15","2024-03-25","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("33","5","AG-P1-2024-0003","QR-AG-P1-2024-0003","2024-04-01","2024-04-10","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("34","5","AG-P1-2024-0004","QR-AG-P1-2024-0004","2024-04-01","2024-04-10","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("35","5","AG-P1-2024-0005","QR-AG-P1-2024-0005","2024-04-15","2024-04-25","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("36","5","AG-P1-2024-0006","QR-AG-P1-2024-0006","2024-04-15","2024-04-25","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("37","5","AG-P1-2024-0007","QR-AG-P1-2024-0007","2024-05-01","2024-05-10","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("38","5","AG-P1-2024-0008","QR-AG-P1-2024-0008","2024-05-01","2024-05-10","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("39","5","AG-P1-2024-0009","QR-AG-P1-2024-0009","2024-05-15","2024-05-25","AVAILABLE","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("40","5","AG-P1-2024-0010","QR-AG-P1-2024-0010","2024-05-15","2024-05-25","AVAILABLE","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("41","6","AG-B1-2024-0001","QR-AG-B1-2024-0001","2024-01-25","2024-02-05","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("42","6","AG-B1-2024-0002","QR-AG-B1-2024-0002","2024-01-25","2024-02-05","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("43","6","AG-B1-2024-0003","QR-AG-B1-2024-0003","2024-02-10","2024-02-20","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("44","6","AG-B1-2024-0004","QR-AG-B1-2024-0004","2024-02-10","2024-02-20","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("45","6","AG-B1-2024-0005","QR-AG-B1-2024-0005","2024-02-25","2024-03-05","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("46","6","AG-B1-2024-0006","QR-AG-B1-2024-0006","2024-02-25","2024-03-05","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("47","6","AG-B1-2024-0007","QR-AG-B1-2024-0007","2024-03-10","2024-03-20","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("48","6","AG-B1-2024-0008","QR-AG-B1-2024-0008","2024-03-10","2024-03-20","ASSIGNED","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("49","6","AG-B1-2024-0009","QR-AG-B1-2024-0009","2024-03-25","2024-04-05","AVAILABLE","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `device_serials` VALUES("50","6","AG-B1-2024-0010","QR-AG-B1-2024-0010","2024-03-25","2024-04-05","AVAILABLE","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");

DROP TABLE IF EXISTS `devices`;
CREATE TABLE `devices` (
  `device_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '디스펜서 ID (PK)',
  `model_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '모델명',
  `manufacturer` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '제조사',
  `category_id` int(11) DEFAULT NULL COMMENT '카테고리 ID (FK -> categories)',
  `specifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '사양 정보 (JSON, 예: {크기, 무게, 전력})',
  `image_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '제품 이미지 URL',
  `manual_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '매뉴얼 URL',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '활성 상태',
  `deleted_at` datetime DEFAULT NULL COMMENT '삭제일시 (Soft Delete)',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '수정일시',
  `created_by` int(11) DEFAULT NULL COMMENT '생성자 user_id',
  `updated_by` int(11) DEFAULT NULL COMMENT '수정자 user_id',
  PRIMARY KEY (`device_id`),
  KEY `idx_category` (`category_id`),
  KEY `idx_active` (`is_active`),
  KEY `idx_deleted` (`deleted_at`),
  CONSTRAINT `devices_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='디스펜서(Device) 마스터 테이블 - 디스펜서 모델 정보';

INSERT INTO `devices` VALUES("1","AllGreen Air Pro S1","All2Green Co.","31","{\"크기\":\"30x30x150cm\",\"무게\":\"3.5kg\",\"전력\":\"220V 50/60Hz\",\"용량\":\"200ml\"}","https://alltogreen.com/web/product/medium/202501/d4a31af4eb1c5174a45ffa051d4f1ad4.jpg","https://cdn.all2green.com/manuals/s1.pdf","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-11 18:21:36","1","0");
INSERT INTO `devices` VALUES("2","AllGreen Air Pro S2","All2Green Co.","31","{\"크기\":\"35x35x160cm\",\"무게\":\"4.0kg\",\"전력\":\"220V 50/60Hz\",\"용량\":\"300ml\"}","https://alltogreen.com/web/product/medium/202501/b57b181bc0620701f54de728964d3bf9.jpg","https://cdn.all2green.com/manuals/s2.pdf","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-11 18:22:16","1","0");
INSERT INTO `devices` VALUES("3","AllGreen Air Slim W1","All2Green Co.","32","{\"크기\":\"20x10x40cm\",\"무게\":\"1.5kg\",\"전력\":\"220V 50/60Hz\",\"용량\":\"100ml\"}","https://alltogreen.com/web/product/medium/202501/eb8089d27b66ab05b640e15ae9080924.jpg","https://cdn.all2green.com/manuals/w1.pdf","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-11 18:22:43","1","0");
INSERT INTO `devices` VALUES("4","AllGreen Air Slim W2","All2Green Co.","32","{\"크기\":\"25x12x45cm\",\"무게\":\"1.8kg\",\"전력\":\"220V 50/60Hz\",\"용량\":\"150ml\"}","https://alltogreen.com/web/product/medium/202501/d4a31af4eb1c5174a45ffa051d4f1ad4.jpg","https://cdn.all2green.com/manuals/w2.pdf","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-11 18:21:56","1","0");
INSERT INTO `devices` VALUES("5","AllGreen Air Premium P1","All2Green Co.","31","{\"크기\":\"40x40x180cm\",\"무게\":\"5.0kg\",\"전력\":\"220V 50/60Hz\",\"용량\":\"500ml\",\"IoT\":\"WiFi\"}","https://alltogreen.com/web/product/medium/202501/b57b181bc0620701f54de728964d3bf9.jpg","https://cdn.all2green.com/manuals/p1.pdf","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-11 18:22:30","1","0");
INSERT INTO `devices` VALUES("6","AllGreen Air Basic B1","All2Green Co.","31","{\"크기\":\"28x28x140cm\",\"무게\":\"3.0kg\",\"전력\":\"220V 50/60Hz\",\"용량\":\"150ml\"}","https://alltogreen.com/web/product/medium/202501/eb8089d27b66ab05b640e15ae9080924.jpg","https://cdn.all2green.com/manuals/b1.pdf","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-11 18:22:47","1","0");
INSERT INTO `devices` VALUES("7","AllGreen Air Compact C1","All2Green Co.","32","{\"크기\":\"18x8x35cm\",\"무게\":\"1.2kg\",\"전력\":\"220V 50/60Hz\",\"용량\":\"80ml\"}","https://alltogreen.com/web/product/medium/202501/d4a31af4eb1c5174a45ffa051d4f1ad4.jpg","https://cdn.all2green.com/manuals/c1.pdf","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-11 18:22:01","1","0");
INSERT INTO `devices` VALUES("8","AllGreen Air Smart SM1","All2Green Co.","31","{\"크기\":\"32x32x155cm\",\"무게\":\"3.8kg\",\"전력\":\"220V 50/60Hz\",\"용량\":\"250ml\",\"IoT\":\"WiFi,Bluetooth\"}","https://alltogreen.com/web/product/medium/202501/b57b181bc0620701f54de728964d3bf9.jpg","https://cdn.all2green.com/manuals/sm1.pdf","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-11 18:22:34","1","0");
INSERT INTO `devices` VALUES("9","AllGreen Air Mini M1","All2Green Co.","32","{\"크기\":\"15x8x30cm\",\"무게\":\"1.0kg\",\"전력\":\"USB 5V\",\"용량\":\"50ml\"}","https://alltogreen.com/web/product/medium/202501/eb8089d27b66ab05b640e15ae9080924.jpg","https://cdn.all2green.com/manuals/m1.pdf","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-11 18:22:50","1","0");
INSERT INTO `devices` VALUES("10","AllGreen Air Deluxe D1","All2Green Co.","31","{\"크기\":\"38x38x170cm\",\"무게\":\"4.5kg\",\"전력\":\"220V 50/60Hz\",\"용량\":\"400ml\",\"IoT\":\"WiFi\"}","https://alltogreen.com/web/product/medium/202501/d4a31af4eb1c5174a45ffa051d4f1ad4.jpg","https://cdn.all2green.com/manuals/d1.pdf","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-11 18:22:07","1","0");
INSERT INTO `devices` VALUES("11","디스펜서 프리미엄 X1","SCENT","1","크기: 350×250×450mm, 용량: 500ml, 소비전력: 15W","/images/devices/premium-x1.jpg","/manuals/premium-x1.pdf","1","0000-00-00 00:00:00","2025-11-11 18:36:48","2025-11-11 18:36:48","1","0");
INSERT INTO `devices` VALUES("12","디스펜서 프리미엄 X2","SCENT","1","크기: 380×280×480mm, 용량: 800ml, 소비전력: 20W","/images/devices/premium-x2.jpg","/manuals/premium-x2.pdf","1","0000-00-00 00:00:00","2025-11-11 18:36:48","2025-11-11 18:36:48","1","0");
INSERT INTO `devices` VALUES("13","디스펜서 루시드 L1","SCENT","1","크기: 400×320×500mm, 용량: 1200ml, AI","/images/devices/lucid-l1.jpg","/manuals/lucid-l1.pdf","1","0000-00-00 00:00:00","2025-11-11 18:36:48","2025-11-11 18:36:48","1","0");
INSERT INTO `devices` VALUES("14","디스펜서 스탠다드 S1","SCENT","1","크기: 280×200×380mm, 용량: 400ml, 소비전력: 12W","/images/devices/standard-s1.jpg","/manuals/standard-s1.pdf","1","0000-00-00 00:00:00","2025-11-11 18:36:48","2025-11-11 18:36:48","1","0");
INSERT INTO `devices` VALUES("15","디스펜서 미니 M1","SCENT","1","크기: 200×150×280mm, 용량: 200ml, 소비전력: 8W","/images/devices/mini-m1.jpg","/manuals/mini-m1.pdf","1","0000-00-00 00:00:00","2025-11-11 18:36:48","2025-11-11 18:36:48","1","0");
INSERT INTO `devices` VALUES("18","LS-100","루시드","29","{\"크기\": \"300x200x500mm\", \"무게\": \"2.5kg\", \"전력\": \"12V/1A\", \"용량\": \"100ml\"}","https://alltogreen.com/web/product/medium/202501/d4a31af4eb1c5174a45ffa051d4f1ad4.jpg","","1","","2025-11-11 19:57:00","2025-11-11 20:07:31","1","1");
INSERT INTO `devices` VALUES("19","LS-200","루시드","29","{\"크기\": \"350x220x550mm\", \"무게\": \"3.0kg\", \"전력\": \"12V/2A\", \"용량\": \"200ml\"}","https://alltogreen.com/web/product/medium/202501/b57b181bc0620701f54de728964d3bf9.jpg","","1","","2025-11-11 19:57:00","2025-11-11 20:07:31","1","1");
INSERT INTO `devices` VALUES("20","LS-300 프리미엄","루시드","29","{\"크기\": \"400x250x600mm\", \"무게\": \"3.5kg\", \"전력\": \"24V/2A\", \"용량\": \"300ml\"}","https://alltogreen.com/web/product/medium/202501/eb8089d27b66ab05b640e15ae9080924.jpg","","1","","2025-11-11 19:57:00","2025-11-11 20:07:31","1","1");
INSERT INTO `devices` VALUES("21","LS-500 프로","루시드","29","{\"크기\": \"450x300x650mm\", \"무게\": \"4.0kg\", \"전력\": \"24V/3A\", \"용량\": \"500ml\"}","https://alltogreen.com/web/product/medium/202501/d4a31af4eb1c5174a45ffa051d4f1ad4.jpg","","1","","2025-11-11 19:57:00","2025-11-11 20:07:31","1","1");
INSERT INTO `devices` VALUES("22","SS-100 베이직","삼성전자","29","{\"크기\": \"280x180x480mm\", \"무게\": \"2.2kg\", \"전력\": \"12V/1A\", \"용량\": \"100ml\"}","https://alltogreen.com/web/product/medium/202501/b57b181bc0620701f54de728964d3bf9.jpg","","1","","2025-11-11 19:57:00","2025-11-11 20:07:31","1","1");
INSERT INTO `devices` VALUES("23","SS-250 스마트","삼성전자","29","{\"크기\": \"350x220x550mm\", \"무게\": \"3.2kg\", \"전력\": \"12V/2A\", \"용량\": \"250ml\", \"WiFi\": \"지원\"}","https://alltogreen.com/web/product/medium/202501/eb8089d27b66ab05b640e15ae9080924.jpg","","1","","2025-11-11 19:57:00","2025-11-11 20:07:31","1","1");
INSERT INTO `devices` VALUES("24","LG-A100","LG전자","29","{\"크기\": \"300x200x500mm\", \"무게\": \"2.4kg\", \"전력\": \"12V/1A\", \"용량\": \"100ml\"}","https://alltogreen.com/web/product/medium/202501/d4a31af4eb1c5174a45ffa051d4f1ad4.jpg","","1","","2025-11-11 19:57:00","2025-11-11 20:07:31","1","1");
INSERT INTO `devices` VALUES("25","LG-A200 AI","LG전자","29","{\"크기\": \"380x240x580mm\", \"무게\": \"3.5kg\", \"전력\": \"24V/2A\", \"용량\": \"200ml\", \"AI\": \"지원\"}","https://alltogreen.com/web/product/medium/202501/b57b181bc0620701f54de728964d3bf9.jpg","","1","","2025-11-11 19:57:00","2025-11-11 20:07:31","1","1");
INSERT INTO `devices` VALUES("26","LS-W50","루시드","30","{\"크기\": \"200x100x300mm\", \"무게\": \"1.2kg\", \"전력\": \"12V/0.5A\", \"용량\": \"50ml\"}","https://alltogreen.com/web/product/medium/202501/eb8089d27b66ab05b640e15ae9080924.jpg","","1","","2025-11-11 19:57:00","2025-11-11 20:07:31","1","1");
INSERT INTO `devices` VALUES("27","LS-W100","루시드","30","{\"크기\": \"250x120x350mm\", \"무게\": \"1.5kg\", \"전력\": \"12V/1A\", \"용량\": \"100ml\"}","https://alltogreen.com/web/product/medium/202501/d4a31af4eb1c5174a45ffa051d4f1ad4.jpg","","1","","2025-11-11 19:57:00","2025-11-11 20:07:31","1","1");
INSERT INTO `devices` VALUES("28","LS-W200 슬림","루시드","30","{\"크기\": \"280x130x380mm\", \"무게\": \"1.8kg\", \"전력\": \"12V/1A\", \"용량\": \"200ml\"}","https://alltogreen.com/web/product/medium/202501/b57b181bc0620701f54de728964d3bf9.jpg","","1","","2025-11-11 19:57:00","2025-11-11 20:07:31","1","1");
INSERT INTO `devices` VALUES("29","SS-W100","삼성전자","30","{\"크기\": \"240x110x340mm\", \"무게\": \"1.4kg\", \"전력\": \"12V/0.5A\", \"용량\": \"100ml\"}","https://alltogreen.com/web/product/medium/202501/eb8089d27b66ab05b640e15ae9080924.jpg","","1","","2025-11-11 19:57:00","2025-11-11 20:07:31","1","1");
INSERT INTO `devices` VALUES("30","SS-W150 디지털","삼성전자","30","{\"크기\": \"260x120x360mm\", \"무게\": \"1.6kg\", \"전력\": \"12V/1A\", \"용량\": \"150ml\", \"디스플레이\": \"LCD\"}","https://alltogreen.com/web/product/medium/202501/d4a31af4eb1c5174a45ffa051d4f1ad4.jpg","","1","","2025-11-11 19:57:00","2025-11-11 20:07:31","1","1");
INSERT INTO `devices` VALUES("31","LG-W80","LG전자","30","{\"크기\": \"220x100x320mm\", \"무게\": \"1.3kg\", \"전력\": \"12V/0.5A\", \"용량\": \"80ml\"}","https://alltogreen.com/web/product/medium/202501/b57b181bc0620701f54de728964d3bf9.jpg","","1","","2025-11-11 19:57:00","2025-11-11 20:07:31","1","1");
INSERT INTO `devices` VALUES("32","LG-W120 에어케어","LG전자","30","{\"크기\": \"250x115x350mm\", \"무게\": \"1.5kg\", \"전력\": \"12V/1A\", \"용량\": \"120ml\", \"공기청정\": \"지원\"}","https://alltogreen.com/web/product/medium/202501/eb8089d27b66ab05b640e15ae9080924.jpg","","1","","2025-11-11 19:57:00","2025-11-11 20:07:31","1","1");

DROP TABLE IF EXISTS `inventory`;
CREATE TABLE `inventory` (
  `inventory_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '재고 ID (PK)',
  `item_type` enum('DEVICE','SCENT','CONTENT','PART','ACCESSORY') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '상품 타입',
  `item_id_ref` int(11) NOT NULL COMMENT '상품 참조 ID',
  `location` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '보관 위치 (창고명, 구역 등)',
  `quantity` int(11) NOT NULL DEFAULT 0 COMMENT '현재 재고 수량',
  `reserved_quantity` int(11) DEFAULT 0 COMMENT '예약된 수량 (출고 예정)',
  `minimum_stock` int(11) DEFAULT 0 COMMENT '안전 재고 수량 (알림 기준)',
  `last_stocked_date` date DEFAULT NULL COMMENT '최근 입고일',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '수정일시',
  PRIMARY KEY (`inventory_id`),
  UNIQUE KEY `unique_item` (`item_type`,`item_id_ref`,`location`),
  KEY `idx_item` (`item_type`,`item_id_ref`),
  KEY `idx_inventory_item` (`item_type`,`item_id_ref`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='재고(Inventory) 테이블 - 상품별 현재 재고 수량 관리';


DROP TABLE IF EXISTS `inventory_transactions`;
CREATE TABLE `inventory_transactions` (
  `transaction_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '트랜잭션 ID (PK)',
  `inventory_id` int(11) NOT NULL COMMENT '재고 ID (FK -> inventory)',
  `transaction_type` enum('IN','OUT','ADJUSTMENT','RETURN') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '트랜잭션 유형 (IN: 입고, OUT: 출고, ADJUSTMENT: 재고조정, RETURN: 반품)',
  `quantity` int(11) NOT NULL COMMENT '수량 (양수: 증가, 음수: 감소)',
  `reference_type` enum('WORK_ORDER','SHIPMENT','PURCHASE','MANUAL') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '참조 유형',
  `reference_id` int(11) DEFAULT NULL COMMENT '참조 ID (work_order_id, shipment_id 등)',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '트랜잭션 메모',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  `created_by` int(11) DEFAULT NULL COMMENT '생성자 user_id',
  PRIMARY KEY (`transaction_id`),
  KEY `idx_inventory` (`inventory_id`),
  KEY `idx_reference` (`reference_type`,`reference_id`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `inventory_transactions_ibfk_1` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`inventory_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='재고 트랜잭션(Inventory Transaction) 테이블 - 재고 입출고 이력';


DROP TABLE IF EXISTS `invoice_items`;
CREATE TABLE `invoice_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '청구 항목 ID (PK)',
  `invoice_id` int(11) NOT NULL COMMENT '청구서 ID (FK -> invoices)',
  `item_type` enum('SUBSCRIPTION_FEE','CONTENT_CUSTOM','SCENT','PART','ACCESSORY','OTHER') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '항목 타입 (SUBSCRIPTION_FEE: 구독료, CONTENT_CUSTOM: 콘텐츠 수정, SCENT: 향 추가구매, PART: 부자재, ACCESSORY: 악세사리, OTHER: 기타)',
  `item_id_ref` int(11) DEFAULT NULL COMMENT '항목 참조 ID (content_request_id, scent_id 등)',
  `description` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '항목 설명',
  `quantity` int(11) NOT NULL DEFAULT 1 COMMENT '수량',
  `unit_price` decimal(10,2) NOT NULL COMMENT '단가 (원)',
  `amount` decimal(10,2) NOT NULL COMMENT '금액 (원, quantity × unit_price)',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  PRIMARY KEY (`item_id`),
  KEY `idx_invoice` (`invoice_id`),
  KEY `idx_item_ref` (`item_type`,`item_id_ref`),
  CONSTRAINT `invoice_items_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`invoice_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='청구서 항목(Invoice Item) 테이블 - 청구서 내 세부 항목';


DROP TABLE IF EXISTS `invoices`;
CREATE TABLE `invoices` (
  `invoice_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '청구서 ID (PK)',
  `invoice_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '청구서 번호 (고유, 예: INV-2025-0001)',
  `customer_id` int(11) NOT NULL COMMENT '고객 ID (FK -> customers)',
  `subscription_id` int(11) DEFAULT NULL COMMENT '구독 ID (FK -> subscriptions, NULL: 단품 주문)',
  `cycle_id` int(11) DEFAULT NULL COMMENT '주기 ID (FK -> subscription_cycles)',
  `invoice_date` date NOT NULL COMMENT '청구일',
  `due_date` date NOT NULL COMMENT '납기일',
  `total_amount` decimal(10,2) NOT NULL COMMENT '총 청구 금액 (원)',
  `tax_amount` decimal(10,2) DEFAULT 0.00 COMMENT '세금 (원)',
  `discount_amount` decimal(10,2) DEFAULT 0.00 COMMENT '할인 금액 (원)',
  `grand_total` decimal(10,2) NOT NULL COMMENT '최종 청구 금액 (원)',
  `status` enum('PENDING','PAID','OVERDUE','CANCELLED') COLLATE utf8mb4_unicode_ci DEFAULT 'PENDING' COMMENT '청구 상태 (PENDING: 미결제, PAID: 결제완료, OVERDUE: 연체, CANCELLED: 취소)',
  `paid_date` datetime DEFAULT NULL COMMENT '결제 완료 일시',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '청구 메모',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '수정일시',
  `created_by` int(11) DEFAULT NULL COMMENT '생성자 user_id',
  `updated_by` int(11) DEFAULT NULL COMMENT '수정자 user_id',
  PRIMARY KEY (`invoice_id`),
  UNIQUE KEY `invoice_number` (`invoice_number`),
  KEY `idx_customer` (`customer_id`),
  KEY `idx_subscription` (`subscription_id`),
  KEY `idx_cycle` (`cycle_id`),
  KEY `idx_invoice_date` (`invoice_date`),
  KEY `idx_status` (`status`),
  KEY `idx_invoice_customer_status` (`customer_id`,`status`),
  CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  CONSTRAINT `invoices_ibfk_2` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`subscription_id`) ON DELETE SET NULL,
  CONSTRAINT `invoices_ibfk_3` FOREIGN KEY (`cycle_id`) REFERENCES `subscription_cycles` (`cycle_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='청구서(Invoice) 테이블 - 고객 대상 청구 정보';


DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '알림 ID (PK)',
  `user_id` int(11) NOT NULL COMMENT '수신자 user_id (FK -> users)',
  `notification_type` enum('SYSTEM','ORDER','PAYMENT','SHIPMENT','TICKET','REMINDER') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '알림 유형 (SYSTEM: 시스템, ORDER: 주문, PAYMENT: 결제, SHIPMENT: 배송, TICKET: 티켓, REMINDER: 리마인더)',
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '알림 제목',
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '알림 내용',
  `link_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '링크 URL (클릭 시 이동)',
  `is_read` tinyint(1) DEFAULT 0 COMMENT '읽음 여부',
  `read_at` datetime DEFAULT NULL COMMENT '읽은 일시',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  PRIMARY KEY (`notification_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_is_read` (`is_read`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='알림(Notification) 테이블 - 사용자별 알림 메시지';


DROP TABLE IF EXISTS `parts`;
CREATE TABLE `parts` (
  `part_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '부자재 ID (PK)',
  `category_id` int(11) DEFAULT NULL COMMENT '카테고리 ID (FK -> categories)',
  `part_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '부자재명',
  `image_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '?????? ?̹??? URL',
  `part_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '부품 번호',
  `compatible_device_id` int(11) DEFAULT NULL COMMENT '호환 디스펜서 ID (FK -> devices, NULL: 공용)',
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '설명',
  `price` decimal(10,2) DEFAULT NULL COMMENT '가격 (원)',
  `warranty_type` enum('FREE','PAID') COLLATE utf8mb4_unicode_ci DEFAULT 'FREE' COMMENT '보증 유형 (FREE: 무상, PAID: 유상)',
  `stock_quantity` int(11) DEFAULT 0 COMMENT '재고 수량',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '활성 상태',
  `deleted_at` datetime DEFAULT NULL COMMENT '삭제일시 (Soft Delete)',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '수정일시',
  `created_by` int(11) DEFAULT NULL COMMENT '생성자 user_id',
  `updated_by` int(11) DEFAULT NULL COMMENT '수정자 user_id',
  PRIMARY KEY (`part_id`),
  KEY `idx_category` (`category_id`),
  KEY `idx_device` (`compatible_device_id`),
  KEY `idx_active` (`is_active`),
  KEY `idx_deleted` (`deleted_at`),
  CONSTRAINT `parts_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL,
  CONSTRAINT `parts_ibfk_2` FOREIGN KEY (`compatible_device_id`) REFERENCES `devices` (`device_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='부자재(Part) 테이블 - 디스펜서 유지보수용 교체 부품';

INSERT INTO `parts` VALUES("1","11","초음파 진동자 15W","","PUMP-UV-15W-001","0","15W 초음파 진동자","45000.00","PAID","200","1","0000-00-00 00:00:00","2025-11-11 18:36:48","2025-11-11 18:36:48","1","0");
INSERT INTO `parts` VALUES("2","11","펌프 모터 스탠다드","","MOTOR-STD-001","0","스탠다드 펌프 모터","38000.00","PAID","220","1","0000-00-00 00:00:00","2025-11-11 18:36:48","2025-11-11 18:36:48","1","0");
INSERT INTO `parts` VALUES("3","12","레벨 센서","","SENSOR-LVL-001","0","액체 레벨 센서","18000.00","FREE","300","1","0000-00-00 00:00:00","2025-11-11 18:36:48","2025-11-11 18:36:48","1","0");
INSERT INTO `parts` VALUES("4","13","메인 제어보드 v1.0","","PCB-MAIN-V10","0","메인 제어보드 v1.0","85000.00","PAID","150","1","0000-00-00 00:00:00","2025-11-11 18:36:48","2025-11-11 18:36:48","1","0");
INSERT INTO `parts` VALUES("5","14","LCD 디스플레이 2.4인치","","DISP-LCD-24","0","2.4인치 컬러 LCD","32000.00","PAID","180","1","0000-00-00 00:00:00","2025-11-11 18:36:48","2025-11-11 18:36:48","1","0");
INSERT INTO `parts` VALUES("38","64","온도 센서","https://alltogreen.com/web/product/medium/202501/d4a31af4eb1c5174a45ffa051d4f1ad4.jpg","SENSOR-TEMP-001","","온도 측정 센서","20000.00","FREE","150","1","","2025-11-12 11:59:16","2025-11-12 11:59:16","1","");
INSERT INTO `parts` VALUES("39","64","진동 센서","https://alltogreen.com/web/product/medium/202501/b57b181bc0620701f54de728964d3bf9.jpg","SENSOR-VIB-001","","진동 감지 센서","18000.00","FREE","200","1","","2025-11-12 11:59:16","2025-11-12 11:59:16","1","");
INSERT INTO `parts` VALUES("40","64","적외선 센서","https://alltogreen.com/web/product/medium/202501/eb8089d27b66ab05b640e15ae9080924.jpg","SENSOR-IR-001","","IR 센서 모듈","22000.00","FREE","150","1","","2025-11-12 11:59:16","2025-11-12 11:59:16","1","");
INSERT INTO `parts` VALUES("41","64","습도 센서","https://alltogreen.com/web/product/medium/202501/d4a31af4eb1c5174a45ffa051d4f1ad4.jpg","SENSOR-HUM-001","","습도 측정 센서","25000.00","FREE","180","1","","2025-11-12 11:59:16","2025-11-12 11:59:16","1","");
INSERT INTO `parts` VALUES("42","65","전원 케이블 1.5m","https://alltogreen.com/web/product/medium/202501/b57b181bc0620701f54de728964d3bf9.jpg","CABLE-PWR-15","","1.5미터 전원 케이블","8000.00","FREE","500","1","","2025-11-12 11:59:16","2025-11-12 11:59:16","1","");
INSERT INTO `parts` VALUES("43","65","전원 케이블 3m","https://alltogreen.com/web/product/medium/202501/eb8089d27b66ab05b640e15ae9080924.jpg","CABLE-PWR-30","","3미터 전원 케이블","12000.00","FREE","450","1","","2025-11-12 11:59:16","2025-11-12 11:59:16","1","");
INSERT INTO `parts` VALUES("44","65","데이터 케이블 1m","https://alltogreen.com/web/product/medium/202501/d4a31af4eb1c5174a45ffa051d4f1ad4.jpg","CABLE-DATA-10","","USB 데이터 케이블","6000.00","FREE","600","1","","2025-11-12 11:59:16","2025-11-12 11:59:16","1","");
INSERT INTO `parts` VALUES("45","66","향 필터 A타입","https://alltogreen.com/web/product/medium/202501/b57b181bc0620701f54de728964d3bf9.jpg","FILTER-A-001","","향 확산 필터","8000.00","PAID","800","1","","2025-11-12 11:59:16","2025-11-12 11:59:16","1","");
INSERT INTO `parts` VALUES("46","66","향 필터 B타입","https://alltogreen.com/web/product/medium/202501/eb8089d27b66ab05b640e15ae9080924.jpg","FILTER-B-001","","향 확산 필터 고급형","12000.00","PAID","600","1","","2025-11-12 11:59:16","2025-11-12 11:59:16","1","");
INSERT INTO `parts` VALUES("47","66","공기 필터","https://alltogreen.com/web/product/medium/202501/d4a31af4eb1c5174a45ffa051d4f1ad4.jpg","FILTER-AIR-001","","공기 정화 필터","15000.00","PAID","400","1","","2025-11-12 11:59:16","2025-11-12 11:59:16","1","");
INSERT INTO `parts` VALUES("48","67","LCD 디스플레이","https://alltogreen.com/web/product/medium/202501/b57b181bc0620701f54de728964d3bf9.jpg","LCD-001","","2.4인치 LCD 화면","35000.00","PAID","80","1","","2025-11-12 11:59:16","2025-11-12 11:59:16","1","");
INSERT INTO `parts` VALUES("49","67","메인 보드","https://alltogreen.com/web/product/medium/202501/eb8089d27b66ab05b640e15ae9080924.jpg","BOARD-MAIN-001","","메인 컨트롤 보드","85000.00","PAID","50","1","","2025-11-12 11:59:16","2025-11-12 11:59:16","1","");
INSERT INTO `parts` VALUES("50","67","펌프 모터","https://alltogreen.com/web/product/medium/202501/d4a31af4eb1c5174a45ffa051d4f1ad4.jpg","MOTOR-PUMP-001","","향 분사 펌프","45000.00","PAID","120","1","","2025-11-12 11:59:16","2025-11-12 11:59:16","1","");
INSERT INTO `parts` VALUES("51","67","팬 모터","https://alltogreen.com/web/product/medium/202501/b57b181bc0620701f54de728964d3bf9.jpg","MOTOR-FAN-001","","공기 순환 팬","28000.00","FREE","200","1","","2025-11-12 11:59:16","2025-11-12 11:59:16","1","");
INSERT INTO `parts` VALUES("52","67","스위치 버튼","https://alltogreen.com/web/product/medium/202501/eb8089d27b66ab05b640e15ae9080924.jpg","SWITCH-BTN-001","","전원 스위치","3000.00","FREE","1000","1","","2025-11-12 11:59:16","2025-11-12 11:59:16","1","");
INSERT INTO `parts` VALUES("53","67","LED 모듈","https://alltogreen.com/web/product/medium/202501/d4a31af4eb1c5174a45ffa051d4f1ad4.jpg","LED-MOD-001","","RGB LED 모듈","12000.00","FREE","350","1","","2025-11-12 11:59:16","2025-11-12 11:59:16","1","");

DROP TABLE IF EXISTS `payment_transactions`;
CREATE TABLE `payment_transactions` (
  `transaction_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '트랜잭션 ID (PK)',
  `invoice_id` int(11) NOT NULL COMMENT '청구서 ID (FK -> invoices)',
  `transaction_number` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '거래 번호 (PG사 제공)',
  `payment_method` enum('CMS','CARD','TRANSFER','OTHER') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '결제 수단',
  `amount` decimal(10,2) NOT NULL COMMENT '결제 금액 (원)',
  `status` enum('PENDING','SUCCESS','FAILED','REFUNDED') COLLATE utf8mb4_unicode_ci DEFAULT 'PENDING' COMMENT '트랜잭션 상태 (PENDING: 처리중, SUCCESS: 성공, FAILED: 실패, REFUNDED: 환불됨)',
  `pg_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'PG사 응답 데이터 (JSON)',
  `error_message` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '오류 메시지 (실패 시)',
  `transaction_date` datetime NOT NULL COMMENT '거래 일시',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  PRIMARY KEY (`transaction_id`),
  UNIQUE KEY `transaction_number` (`transaction_number`),
  KEY `idx_invoice` (`invoice_id`),
  KEY `idx_status` (`status`),
  KEY `idx_transaction_date` (`transaction_date`),
  CONSTRAINT `payment_transactions_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='결제 트랜잭션(Payment Transaction) 테이블 - 결제 거래 상세 이력';


DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '역할 ID (PK)',
  `role_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '역할명 (SUPER_ADMIN, HQ_ADMIN, VENDOR, SALES_REP, CUSTOMER, LUCID)',
  `display_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '화면 표시명',
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '역할 설명',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '활성 상태 (TRUE: 사용, FALSE: 비활성)',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '수정일시',
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='역할(Role) 정의 테이블 - 시스템 내 사용자 역할을 정의';

INSERT INTO `roles` VALUES("1","HQ","최고관리자","시스템 전체 관리 권한","1","2025-11-10 08:27:56","2025-11-10 09:17:51");
INSERT INTO `roles` VALUES("2","HQ_ADMIN","본사 관리자","본사 운영 및 정책 관리","1","2025-11-10 08:27:56","2025-11-10 08:27:56");
INSERT INTO `roles` VALUES("3","VENDOR","밴더","판매 파트너 권한","1","2025-11-10 08:27:56","2025-11-10 08:27:56");
INSERT INTO `roles` VALUES("4","SALES_REP","영업사원","고객 관리 및 영업 활동","1","2025-11-10 08:27:56","2025-11-10 08:27:56");
INSERT INTO `roles` VALUES("5","CUSTOMER","구독 고객","서비스 이용 고객","1","2025-11-10 08:27:56","2025-11-10 08:27:56");
INSERT INTO `roles` VALUES("6","LUCID","루시드","콘텐츠 디자인 제작","1","2025-11-10 08:27:56","2025-11-10 08:27:56");

DROP TABLE IF EXISTS `scent_changes`;
CREATE TABLE `scent_changes` (
  `change_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '교체 ID (PK)',
  `assignment_id` int(11) NOT NULL COMMENT '디스펜서 배정 ID (FK -> device_assignments)',
  `old_scent_id` int(11) DEFAULT NULL COMMENT '이전 향 ID (FK -> scents)',
  `new_scent_id` int(11) NOT NULL COMMENT '새 향 ID (FK -> scents)',
  `change_date` date NOT NULL COMMENT '교체일',
  `changed_by` int(11) DEFAULT NULL COMMENT '교체자 user_id',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '교체 메모',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  PRIMARY KEY (`change_id`),
  KEY `idx_assignment` (`assignment_id`),
  KEY `idx_old_scent` (`old_scent_id`),
  KEY `idx_new_scent` (`new_scent_id`),
  KEY `idx_change_date` (`change_date`),
  CONSTRAINT `scent_changes_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `device_assignments` (`assignment_id`) ON DELETE CASCADE,
  CONSTRAINT `scent_changes_ibfk_2` FOREIGN KEY (`old_scent_id`) REFERENCES `scents` (`scent_id`) ON DELETE SET NULL,
  CONSTRAINT `scent_changes_ibfk_3` FOREIGN KEY (`new_scent_id`) REFERENCES `scents` (`scent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='향 교체 이력(Scent Change) 테이블 - 디스펜서 향 카트리지 교체 히스토리';


DROP TABLE IF EXISTS `scents`;
CREATE TABLE `scents` (
  `scent_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '향 ID (PK)',
  `category_id` int(11) DEFAULT NULL COMMENT '카테고리 ID (FK -> categories, 4단계 분류: 계열 > 향명 > 특성 > 강도)',
  `scent_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '향 이름',
  `scent_family` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '향 계열 (Woody, Floral, Fruity, Green 등)',
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '향 설명',
  `capacity_ml` int(11) DEFAULT NULL COMMENT '용량 (ml)',
  `price` decimal(10,2) DEFAULT NULL COMMENT '단품 가격 (원)',
  `image_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '향 이미지 URL',
  `ingredients` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '주요 성분',
  `is_allergen_free` tinyint(1) DEFAULT 0 COMMENT '알러지프리 여부',
  `is_eco_friendly` tinyint(1) DEFAULT 0 COMMENT '친환경 인증 여부',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '활성 상태',
  `stock_quantity` int(11) DEFAULT 0 COMMENT '재고 수량',
  `view_count` int(11) DEFAULT 0 COMMENT '조회수',
  `deleted_at` datetime DEFAULT NULL COMMENT '삭제일시 (Soft Delete)',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '수정일시',
  `created_by` int(11) DEFAULT NULL COMMENT '생성자 user_id',
  `updated_by` int(11) DEFAULT NULL COMMENT '수정자 user_id',
  PRIMARY KEY (`scent_id`),
  KEY `idx_category` (`category_id`),
  KEY `idx_family` (`scent_family`),
  KEY `idx_active` (`is_active`),
  KEY `idx_deleted` (`deleted_at`),
  CONSTRAINT `scents_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=401 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='향 카트리지(Scent) 테이블 - 디스펜서 향 오일 정보';

INSERT INTO `scents` VALUES("1","1","롬브르단로","Green&Herb","딥디크 롬브르단로 타입","100","39000.00","http://oilpick.co.kr/MiniERP/oil/image/롬브르단로(573A).png","Top: BERGAMOT, MANDARIN, CASSIS, BLACK CURRANT\nMiddle: ROSE, BLACK CURRANT LEAF\nBase: AMBER, MUSK","0","0","1","100","0","","2025-11-12 12:50:57","2025-11-12 12:50:57","1","");
INSERT INTO `scents` VALUES("2","1","센스","Floral","멘디니 디퓨저 - 피오레 / 딥디크 오데썽 타입","100","55000.00","http://oilpick.co.kr/MiniERP/oil/image/AT SENSE 2970A-센스.png","Top: BASIL, BERGAMOT, LEMON, BITTER ORANGE, JUNIPER BERRY, LAVENDER\nMiddle: JASMINE, ROSE, MUGUET, ORANGE BLOSSOM\nBase: MUSK, CEDARWOOD, CIVET, AMBER","0","0","1","100","0","","2025-11-12 12:50:57","2025-11-12 12:50:57","1","");
INSERT INTO `scents` VALUES("3","1","꽃집","Floral","더향 보니타 2번","100","37000.00","http://oilpick.co.kr/MiniERP/oil/image/FLOWER 8491A-꽃집.png","Top: GREEN, BERGAMOT, EUCALYPTUS, PINE, PETITGRAIN\nMiddle: MUGUET, ROSE, JASMINE, DAISY\nBase: CEDARWOOD","0","0","1","100","0","","2025-11-12 12:50:57","2025-11-12 12:50:57","1","");
INSERT INTO `scents` VALUES("4","1","오리엔탈","Woody&Spicy","더향 보니타 5번","100","43000.00","http://oilpick.co.kr/MiniERP/oil/image/AT ORIENTAL-5156A-오리엔탈.png","Top: LEMON, BLACK PEPPER, GALBANUM, OZONE, ORANGE, LAVANDIN\nMiddle: CARDAMON, PINEAPPLE, JASMINE, VIOLET\nBase: VANILLA, CARAMEL, MUSK, AMBER, CEDAR, PATCHOULI","0","0","1","100","0","","2025-11-12 12:50:57","2025-11-12 12:50:57","1","");
INSERT INTO `scents` VALUES("5","1","피트니스","Green&Herb","","100","40000.00","http://oilpick.co.kr/MiniERP/oil/image/피트니스(9976A).png","Top: ORANGE, LIME, HERB, ROSEMARY\nMiddle: ROSE, JASMINE, MUGUET\nBase: TONKA, CEDARWOOD, SANDALWOOD","0","0","1","100","0","","2025-11-12 12:50:57","2025-11-12 12:50:57","1","");
INSERT INTO `scents` VALUES("6","1","소바쥬","Woody&Spicy","디올 소바주 타입","100","39000.00","http://oilpick.co.kr/MiniERP/oil/image/AT SAUVAGE 594A-소바쥬.png","Top: BERGAMOT, GALBANUM, MANDARIN, AROMATIC\nMiddle: LAVENDER, OZONIC, GERANIUM, ELEMI, WHITE FLORAL\nBase: AMBER, CISTUS LABDANUM, TONKA, MUSK","0","0","1","100","0","","2025-11-12 12:50:57","2025-11-12 12:50:57","1","");
INSERT INTO `scents` VALUES("7","1","블랙베리","Fruity","조말론 블랙 베리&베이 타입","100","40000.00","http://oilpick.co.kr/MiniERP/oil/image/BLACK BERRY-2510A-블랙베리.png","Top: CASSIS, CITRUS, GREEN, BAY LEAF, BLACKBERRY\nMiddle: WHITE LILY, JASMINE\nBase: MUSK, SANDALWOOD, VETIVER, CEDARWOOD","0","0","1","100","0","","2025-11-12 12:50:57","2025-11-12 12:50:57","1","");
INSERT INTO `scents` VALUES("8","1","무화과","Fruity","조말론 와일드 피그&카시스 타입","100","42000.00","http://oilpick.co.kr/MiniERP/oil/image/무화과(4519A).png","Top: GREEN, FIG\nMiddle: PINE, JASMINE, CYCLAMEN, HYACINTH\nBase: CEDAR, AMBER, COCONUT","0","0","1","100","0","","2025-11-12 12:50:57","2025-11-12 12:50:57","1","");
INSERT INTO `scents` VALUES("9","1","리브르","Floral","입생로랑 리브르 타입","100","50000.00","http://oilpick.co.kr/MiniERP/oil/image/리브르(612B).png","Top: MANDARIN, LEMON, PINEAPPLE, GRAPEFRUIT\nMiddle: CASSIS, OZONIC, JASMIN, NEROLI, ROSE\nBase: AMBER, TONKA, MUSK, ORRIS, VANILLA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("10","1","가브리엘","Floral","샤넬 가브리엘 오드 빠르펭 타입","100","50000.00","http://oilpick.co.kr/MiniERP/oil/image/가브리엘(388B).png","Top: BERGAMOT, MANDARIN, GRAPEFRUIT, BLACK CURRANT, PINK PEPPER, OZONE\nMiddle: ROSE, YLANG, JASMINE SAMBAC, TUBEROSE, ORANGE BLOSSOM, ORRIS\nBase: MUSK, SANDAL, PATCHOULI, AMBER, ANIMALIC","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("11","1","러브","Floral","끌로에 러브 타입","100","41000.00","http://oilpick.co.kr/MiniERP/oil/image/러브(427A).png","Top: PINK PEPPER, VIOLET LEAF, BERGAMOT, GALBANUM, ORANGE FLOWER\nMiddle: IRIS, LILAC, HYACINTH, VIOLET, HELIOTROPE, JASMINE\nBase: CEDAR, MUSK, AMBER, SANDAL, TONKA BEAN, VANILLA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("12","1","블루밍 부케","Floral","디올 블루밍 부케 타입","100","55000.00","http://oilpick.co.kr/MiniERP/oil/image/블루밍부케(756B).png","Top: BERGAMOT, MANDARIN, PEACH, APRICOT\nMiddle: ROSE, JASMINE, MUGUET, ORANGE FLOWER, PEONY\nBase: MUSK, AMBER, PATCHOULI","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("13","1","피톤치드","Woody&Spicy","","100","37000.00","http://oilpick.co.kr/MiniERP/oil/image/피톤치드(275A).png","Top: PINE, CAMPHOR, EUCALYPTUS\nMiddle: PINE, LIME, ROSEMARY\nBase: MUSK, MOSS","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("14","1","런던 우먼","Floral","버버리 런던 우먼 타입","100","70000.00","http://oilpick.co.kr/MiniERP/oil/image/LONDON-1556P-런던우먼.png","Top: ORANGE FLOWER, OZONE, GREEN, GRAPEFRUIT, MANDARIN\nMiddle: TUBEROSE, JASMINE, ROSE, PEONY, HONEYSUCKLE\nBase: MUSK, PATCHOULI, CEDARWOOD, SANDALWOOD, MOSS","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("15","1","NO.5","Floral","샤넬 NO.5 타입","100","65000.00","http://oilpick.co.kr/MiniERP/oil/image/NO.5(9000P).png","Top: ALDEHYDE, LEMON, BERGAMOT, YUZU\nMiddle: JASMINE, ROSE, YLANG YLANG, IRIS, MUGUET\nBase: MUSK, AMBER, PATCHOULI, SANDALWOOD, TONKA, VANILLA, MOSS","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("16","1","코코 마드모아젤","Floral","샤넬 코코 마드모아젤 타입","100","63000.00","http://oilpick.co.kr/MiniERP/oil/image/코코 마드모아젤(1431P).png","Top: BERGAMOT, PINK PEPPER, OZONE, JUNIPERBERRY, BLOOD ORANGE\nMiddle: ROSE, JASMINE, FREESIA, NEROLI, PATCHOULI\nBase: MUSK, VANILLA, MOSS, AMBER, SANDALWOOD, VETIVER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("17","1","피톤치드","Woody&Spicy","","100","37000.00","http://oilpick.co.kr/MiniERP/oil/image/피톤치드(8749A).png","Top: PINE, CAMPHOR, EUCALYPTUS, BERGAMOT, GREEN\nMiddle: PINE, LIME, ROSEMARY, DAISY\nBase: MUSK, MOSS, CEDARWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("18","1","그레이프","Fruity","","100","35000.00","http://oilpick.co.kr/MiniERP/oil/image/GRAPE 483A-그레이프.png","Top: GREEN GRAPE, LEMON\nMiddle: GRAPE, CHERRY\nBase: MUSK, GRAPE, CHERRY","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("19","1","포레스트","Woody&Spicy","","100","90000.00","http://oilpick.co.kr/MiniERP/oil/image/FOREST 7735A-포레스트.png","Top: LEMON, ORANGE, BERGAMOT, EUCALYPTUS, ROSEMARY, PINE, MINT\nMiddle: ROSE, FREESIA\nBase: CEDARWOOD ATLAS, CEDARWOOD VIRGINIA, SANDALWOOD, PATCHOULI","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("20","1","오에도","Citrus","딥디크 오에도 타입","100","46000.00","http://oilpick.co.kr/MiniERP/oil/image/오에도(9160A).png","Top: LIME, MANDARIN, ORANGE, LEMON AND YUZU\nMiddle: THYME, JASMINE\nBase: PATCHOULI","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("21","1","시트러스버베나","Citrus","","100","35000.00","http://oilpick.co.kr/MiniERP/oil/image/시트러스버베나(7860A).png","Top: MANDARIN, LEMON, LIME, ORANGE, VERBENA\nMiddle: VERBENA, FRESH FLORAL, MINT\nBase: PATCHOULI, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("22","1","우드세이지 앤 씨솔트","Woody&Spicy","조말론 우드세이지 앤 씨솔트 타입","100","49000.00","http://oilpick.co.kr/MiniERP/oil/image/우드세이지 앤 씨솔트(965A).png","Top: GRAPEFRUIT, BERGAMOT, MANDARIN, SEA SALT, SEAWEED\nMiddle: MUGUET, ROSE, ORANGE FLOWER, SAGE\nBase: AMBER, MUSK, CEDARWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("23","1","화이트머스크","","","100","34000.00","http://oilpick.co.kr/MiniERP/oil/image/화이트머스크(545A).png","Top: ORANGE, GALBANUM\nMiddle: IRIS, NARCISSUS, ROSE, MAGNOLIA, JASMINE\nBase: MUSK, AMBER, SANDALWOOD, VETIVER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("24","1","화이트 자스민 앤 민트","Floral","","100","45000.00","http://oilpick.co.kr/MiniERP/oil/image/화이트 자스민 앤 민트(6255A).png","Top: MINT, BERGAMOT, CHAMOMILE, CORIANDER, CARDAMOM, BLACK CURRANT\nMiddle: ROSE, JASMINE, ORANGE BLOSSOM, LILY-OF-THE-VALLEY, YLANG\nBase: TEA, VETIVER, CEDAR, MUSK, PLUM, GUAIAC WOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("25","1","웨딩데이","Floral","","100","48000.00","http://oilpick.co.kr/MiniERP/oil/image/웨딩데이(315A).png","Top: FREESIA, CYCLAMEN, PETITGRAIN, YLANG YLANG\nMiddle: FREESIA, JASMINE, MUGUET, HYACINTH, ROSE, LILAC\nBase: MUSK, CEDAR, PEACH","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("26","1","시크릿 위시","Fruity","","100","44000.00","http://oilpick.co.kr/MiniERP/oil/image/시크릿위시(5260A).png","Top: LEMON, MELON, PEACH, TAGETES\nMiddle: PINEAPPLE, BLACK CURRANT, FREESIA, ORCHID\nBase: SANDALWOOD, CEDARWOOD, MUSK, AMBER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("27","1","씨솔트 앤 유자","Citrus","","100","37000.00","http://oilpick.co.kr/MiniERP/oil/image/씨솔트 앤 유자(671A).png","Top: YUZU, ALDEHYDE, LIME, GRAPEFRUIT, ORANGE, BLACK CURRANT\nMiddle: LEMONGRASS, JASMINE, ROSE, APPLE, MIXED FRUITY\nBase: JASMINE, VANILLA, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("28","1","피오니","Floral","","100","40000.00","http://oilpick.co.kr/MiniERP/oil/image/피오니(1790A).jpg","Top: PEONY, GREEN, ORANGE, OZONE, FRUITY\nMiddle: LILY OF THE VALLEY, ROSE, JASMINE, FRUITY\nBase: SANDALWOOD, MUSK, PEACH","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("29","1","수선화","Floral","","100","38000.00","http://oilpick.co.kr/MiniERP/oil/image/수선화(335A).png","Top: GRAPEFRUIT, OZONE, GREEN APPLE\nMiddle: JASMINE, MUGUET, ROSE, FREESIA, GERANIUM\nBase: MUSK, AMBER, HELIOTROPE","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("30","1","메리미","Floral","","100","40000.00","http://oilpick.co.kr/MiniERP/oil/image/메리미(9060A).png","Top: BITTER ORANGE, APPLE, GREEN, LEMON, JASMINE\nMiddle: JASMINE, MAGNOLIA, FREESIA, FRUITY, MUGUET\nBase: AMBER, CEDAR, TONKA, SANDAL, MOSS, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("31","1","라일락","Floral","","100","37000.00","http://oilpick.co.kr/MiniERP/oil/image/라일락(1172A).png","Top: CASSIS, LILAC\nMiddle: ROSE, LILAC, JASMINE, LILY, VIOLET\nBase: MUSK, AMBER, PATCHOULI","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("32","1","가든 스위트피","Fruity","","100","40000.00","http://oilpick.co.kr/MiniERP/oil/image/가든 스위트피 145A.png","Top: GREEN, ORANGE, LEMON, CYCLAMEN\nMiddle: LILY, JASMINE, VIOLET, PINEAPPLE\nBase: MUSK, CARAMEL, RASPBERRY, PEACH","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("33","1","잉글리쉬페어 앤 프리지아","Fruity","조말론 잉글리쉬 페어&프리지아 타입","100","39000.00","http://oilpick.co.kr/MiniERP/oil/image/잉글리쉬페어 앤 프리지아(1383A).png","Top: MELON, PEAR\nMiddle: FREESIA, ROSE\nBase: MUSK, PATCHOULI, RHUBURB, AMBER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("34","1","다우니에이프릴","Musk","","100","35000.00","http://oilpick.co.kr/MiniERP/oil/image/다우니에이프릴(134A).png","Top: BERGAMOT, ALDEHYDE, FRUITY, WORMWOOD\nMiddle: MUGUET, JASMINE, ORANGE FLOWER, TUBEROSE, VIOLET, ROSE\nBase: CEDAR, AMBER, VETIVER, TONKA, VANILLA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("35","1","체리 블러썸","Floral","","100","42000.00","http://oilpick.co.kr/MiniERP/oil/image/체리 블러썸(2A).png","Top: MIXED FRUITY, OZONE, GREEN\nMiddle: LILY OF THE VALLEY, JASMINE, YLANG YLANG\nBase: MUSK, HELIOTROPE, SANDAL, VANILLA, CEDARWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("36","1","블랙라즈베리앤 바닐라","Fruity","","100","44000.00","http://oilpick.co.kr/MiniERP/oil/image/블랙라즈베리앤 바닐라(7560A).png","Top: BLACK CURRANT, BLUEBERRY, ORANGE, PEAR, BERGAMOT\nMiddle: ORRIS ROOT, JASMINE, OSMANTHUS\nBase: VANILLA ORCHID, SANDALWOOD, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("37","1","블랙체리","Fruity","","100","55000.00","http://oilpick.co.kr/MiniERP/oil/image/ALL TO DREAM-65A-블랙체리.png","Top: CHERRY, STRAWBERRY, RASPBERRY, ORANGE, ALMOND\nMiddle: CHERRY, JASMINE, STRAWBERRY\nBase: VANILLA, CARAMEL, PEACH, HELIOTROPE","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("38","1","믹스베리","Fruity","","100","36000.00","http://oilpick.co.kr/MiniERP/oil/image/믹스베리(135A).png","Top: MIXED BERRIES, GRAPEFRUIT\nMiddle: ROSE, APPLE, STRAWBERRY\nBase: PEACH, RASPBERRY","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("39","1","바하마 브리즈","Fruity","","100","37000.00","http://oilpick.co.kr/MiniERP/oil/image/바하마브리즈(863A).png","Top: EXOTIC GRUITS, PINEAPLLE, PASSIONFRUIT, GRAPEFRUIT\nMiddle: MANGO, PEACH, TROPICAL FRUITS\nBase: MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("40","1","베이비파우더","Musk","","100","35000.00","http://oilpick.co.kr/MiniERP/oil/image/베이비파우더(5590A).png","Top: BERGAMOT, YLANG YLANG, ALDEHYDE\nMiddle: ROSE, LILY OF THE VALLEY, JASMINE, TONKA\nBase: MUSK, SANDALWOOD, VANILLA, HELIOTROPE","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("41","1","아카시아","Floral","","100","39000.00","http://oilpick.co.kr/MiniERP/oil/image/아카시아(847A).png","Top: GREEN APPLE, ORANGE, GRAPEFRUIT, GREEN\nMiddle: ACACIA, JASMINE, MUGUET, VIOLET, NEROLI, TUBEROSE\nBase: MUSK, AMBER, CEDARWOOD, HELIOTROPE","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("42","1","플로랄존","Floral","","100","63000.00","http://oilpick.co.kr/MiniERP/oil/image/플로랄존(60A).jpg","Top: OZONE\nMiddle: LILY, LILAC, WHITE ROSE, JASMIN, MUGUET\nBase: WHITE MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("43","1","화이트자스민","Floral","","100","58000.00","http://oilpick.co.kr/MiniERP/oil/image/화이트자스민(40A).png","Top: BERGAMOT, CHAMOMILE, MINT, CARDAMOM, BLACK CURRANT, CORIANDER\nMiddle: JASMIN, ORANGE BLOSSOM, ROSE, YLANG YLANG, MUGUET\nBase: MUSK, VETIVER, CEDARWOOD, GUAIAC WOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("44","1","밀키브레스","Floral","","100","37000.00","http://oilpick.co.kr/MiniERP/oil/image/밀키브레스(55A).jpg","Top: ALDEHYDE\nMiddle: ROSE, JASMIN, SWEET PEA\nBase: WHITE MUSK, VANILLA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("45","1","다우니프레쉬","Musk","","100","38000.00","http://oilpick.co.kr/MiniERP/oil/image/다우니프레쉬(64A).png","Top: BERGAMOT, ALDEHYDE, FRUITY, WORMWOOD\nMiddle: MUGUET, JASMINE, ORANGE FLOWER, TUBEROSE, VIOLET, ROSE\nBase: CEDAR, AMBER, VETIVER, TONKA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("46","1","코튼블라썸","Musk","","100","43000.00","http://oilpick.co.kr/MiniERP/oil/image/코튼블라썸(62A).png","Top: GREEN, BERGAMOT, TANGERINE, LEMON, ALMOND\nMiddle: VIOLET, JASMINE, ROSE, MUGUET, ORANGE FLOWER\nBase: VANILLA, SANDALWOOD, MUSK, CEDARWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("47","1","그린애플","Fruity","","100","27000.00","http://oilpick.co.kr/MiniERP/oil/image/그린애플(51A).jpg","Top: GREEN APPLE, PEAR\nMiddle: APPLE JUICE, MINT\nBase: JASMIN","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("48","1","블루오션","Aqua","","100","44000.00","http://oilpick.co.kr/MiniERP/oil/image/블루오션 81A.png","Top: MANDARIN, BERGAMOT, ALDEHYDE, OZONE, LEMON, SPEARMINT\nMiddle: MUGUET, JASMINE, FRUITY, CARDAMON, GERANIUM, NUTMEG\nBase: MUSK, PATCHOULI, MOSS, VANILLA, SANDAL, AMBER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("49","1","시나펌킨","Fruity","","100","38000.00","http://oilpick.co.kr/MiniERP/oil/image/시나펌킨(45A).png","Top: PUMPKIN, CINNAMON, GINGER, COFFEE\nMiddle: CINNAMON, CLOVE BUD, APPLE, FLORAL\nBase: PEACH, VANILLA, CARAMEL, CEDAR, POWDERY","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("50","1","타임리스","Fruity","","100","36000.00","http://oilpick.co.kr/MiniERP/oil/image/타임리스(456A).jpg","Top: APPLE, PLUM, CITRUS\nMiddle: JASMIN, MUGUET, PEONY\nBase: MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("51","1","쿨미","Citrus","","100","48000.00","http://oilpick.co.kr/MiniERP/oil/image/쿨미(1Z).jpg","Top: BERGAMOT, LAVENDER, LAVANDIN, THYME, ROSEMARY\nMiddle: JASMINE, PINEAPPLE, LILY OF THE VALLEY, ORANGE FLOWER,GERANIUM, LAVENDER\nBase: MUSK, CEDAR, MOSS, SANDALWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("52","1","실버마운틴워터","Aqua","","100","40000.00","http://oilpick.co.kr/MiniERP/oil/image/실버마운틴워터(39CA).jpg.png","Top: GREEN, ORANGE, FRUITY\nMiddle: APPLE, FREESIA, MUGUET, JASMINE, ROSE\nBase: MUSK, VETIVER, MOSS, VANILLA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("53","1","쿨워터","Aqua","","100","48000.00","http://oilpick.co.kr/MiniERP/oil/image/쿨워터(5CA).jpg","Top: BERGAMOT, PETITGRAIN, LEMON, LAVANDIN, CORIANDER\nMiddle: CLARY SAGE, GERANIUM, LILY OF THE VALLEY, JASMINE\nBase: SANDALWOOD, CEDAR, AMBER, MOSS, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("54","1","헤이즐넛","Sweet","","100","22000.00","http://oilpick.co.kr/MiniERP/oil/image/헤이즐넛(73A).jpg","Top: HAZELNUT\nMiddle: HAZELNUT\nBase: VANILLA, CARAMEL","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("55","1","썸머","Fruity","","100","65000.00","http://oilpick.co.kr/MiniERP/oil/image/썸머(2A).jpg","Top: LEMON, LIME, KIWI\nMiddle: MUGUET, BLUEBERRY, CACTUS\nBase: VETIVER, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("56","1","엘더플라워앤그레이프프룻","Fruity","","100","28000.00","http://oilpick.co.kr/MiniERP/oil/image/엘더플라워앤그레이프프룻(3A).jpg","Top: GRAPEFRUIT, ORANGE, OZONE\nMiddle: NEROLI, ROSE, JASMIN\nBase: MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("57","1","헬로엔젤","Fruity","","100","37000.00","http://oilpick.co.kr/MiniERP/oil/image/헬로엔젤(30A).jpg","Top: MELON, STRAWBERRY, CITRUS\nMiddle: SWEET PEA, MUGUET, ROSE\nBase: VANILLA, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("58","1","오로즈","Floral","딥디크 오로즈 타입","100","93000.00","http://oilpick.co.kr/MiniERP/oil/image/오로즈(2370W).jpg","Top: LITCHI, CASSIS, BERGAMOT\nMiddle: ROSE, GERANIUM, JASMINE\nBase: MUSK, CEDARWOOD, AMBER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("59","1","알로에베라","Floral","","100","45000.00","http://oilpick.co.kr/MiniERP/oil/image/알로에베라(1252B).jpg","Top: ALOE, CUCUMBER, PEAR, LEMON, LEAFY GREEN\nMiddle: MUGUET, FREESIA, ROSE\nBase: MUSK, AMBER, WOODY","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("60","1","베이비파우더해피","Musk","","100","40000.00","http://oilpick.co.kr/MiniERP/oil/image/베이비파우더해피(784B).png","Top: BERGAMOT, ORANGE, YLANG YLANG, ALDEHYDE\nMiddle: ROSE, MUGUET, JASMINE, GERANIUM\nBase: MUSK, SANDALWOOD, VANILLA, TONKA BEAN","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("61","1","고스트","Woody&Spicy","바이레도 모하비 고스트 타입","100","130000.00","http://oilpick.co.kr/MiniERP/oil/image/고스트(1207W).jpg","Top: AMBRETTE, SAPODILLA\nMiddle: MAGNOLIA, VIOLET, SANDALWOOD\nBase: AMBERGRIS, CEDARWOOD, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("62","1","바이레도 발다","Citrus","바이레도 발다프리크 타입","100","130000.00","http://oilpick.co.kr/MiniERP/oil/image/바이레도-발다(1215W).jpg","Top: AMALFI LEMON, BERGAMOT, NEROLI, AFRICAN MARIGOLD, BUCCHU\nMiddle: VIOLET, CYCLAMEN, JASMINE\nBase: VETIVER, BLACK AMBER, MUSK, CEDARWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("63","1","사쿠라","Floral","","100","125000.00","http://oilpick.co.kr/MiniERP/oil/image/사쿠라(1132W).png","Top: BERGAMOT, MANDARIN ORANGE\nMiddle: ROSE, CHERRY BLOSSOM, MIMOSA\nBase: WOODY NOTE, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("64","1","유주쥬스","Citrus","","100","50000.00","http://oilpick.co.kr/MiniERP/oil/image/유주쥬스(54A).jpg","Top: YUZU, ORANGE FRUITY\nMiddle: YUZU, ORANGE JUICE\nBase: YUZU, ORANGE","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("65","1","블랙우드","Woody&Spicy","","100","115000.00","http://oilpick.co.kr/MiniERP/oil/image/블랙우드(1240W).jpg","Top: SICHUAN PEPPER, AGARWOOD, SANDALWOOD\nMiddle: AGARWOOD, SANDALWOOD, VETIVER, CARDAMOM\nBase: WOODY, TONKA BEAN, VANILLA, AMBER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("66","1","우디머스크라벤더","Floral","","100","112000.00","http://oilpick.co.kr/MiniERP/oil/image/우디머스크라벤더(112W).jpg","Top: LAVENDER\nMiddle: WISTERIA FLOWER, HELIOTROPE\nBase: MOSS, WOODY, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("67","1","차콜","Green&Herb","","100","85000.00","http://oilpick.co.kr/MiniERP/oil/image/차콜(615W).jpg","Top: BERGAMOT, LAVENDER, EUCALYPTUS, ROSEMARY, PINE, TEA TREE\nMiddle: FREESIA, MUGUET\nBase: CEDARWOOD, BALSAM","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("68","1","허","Fruity","버버리 허 타입","100","88000.00","http://oilpick.co.kr/MiniERP/oil/image/허(1078W).jpg","Top: LITCHI, BLACK CURRANT, MANDARINE ORANGE\nMiddle: JASMINE, RICE BASMATI, ROSE\nBase: MUSK, SANDALWOOD, AMBER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("69","1","샹스","Floral","샤넬 샹스 타입","100","84000.00","http://oilpick.co.kr/MiniERP/oil/image/샹스(280W).jpg","Top: GREEN, LEMON, PEAR, GRAPEFRUIT, BLACK PEPPER\nMiddle: JASMINE, VIOLET, YLANG YLANG, FREESIA, HYACINTH\nBase: MUSK, CEDARWOOD, SANDALWOOD, AMBER, IRIS","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("70","1","베르가못","Citrus","르라보 베르가못 타입","100","52000.00","http://oilpick.co.kr/MiniERP/oil/image/베르가못(18A).jpg","Top: BERGAMOT, LIME, ORANGE\nMiddle: BERGAMOT, LITSEA CUBEBA, PALMAROSA\nBase: BALSAMIC","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("71","1","블랑","Floral","바이레도 블랑쉐 타입","100","53000.00","http://oilpick.co.kr/MiniERP/oil/image/블랑(1092W).jpg","Top: PINK PEPPER, ROSE, ALDEHYDES\nMiddle: ORANGE FLOWER, VIOLET, PEONY\nBase: SANDALWOOD, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("72","1","시트우드","Woody&Spicy","","100","80000.00","http://oilpick.co.kr/MiniERP/oil/image/시트우드(433W).jpg","Top: APPLE, CEDAR, BELL FLOWER, LEMON\nMiddle: AMBER, MUSK, CEDARWOOD\nBase: WHITE ROSE, BAMBOO, JASMINE, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("73","1","라튤립","Floral","바이레도 라튤립 타입","100","55000.00","http://oilpick.co.kr/MiniERP/oil/image/라튤립(899W).jpg","Top: GREEN NOTES, CYCLAMEN, RHUBURB\nMiddle: PINK TULIP, MUGUET, FREESIA\nBase: WOODY NOTE, MUSK, MOSS","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("74","1","자몽","Citrus","","100","54000.00","http://oilpick.co.kr/MiniERP/oil/image/자몽(433CA).jpg","Top: GRAPEFRUIT, MIXED BERRIES, BERGAMOT\nMiddle: ROSE, APPLE, STRAWBERRY, PINEAPPLE\nBase: PEACH, RASPBERRY","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("75","1","나르시소","Floral","","100","54000.00","http://oilpick.co.kr/MiniERP/oil/image/나르시소(660A).jpg","Top: MANDARIN, GREEN, PETITGRAIN\nMiddle: MUGUET, NEROLI, FREESIA, ROSE, OSMANTHUS\nBase: MUSK, SANDALWOOD, PATCHOULI, VANILLA, AMBER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("76","1","카니발","Fruity","","100","52000.00","http://oilpick.co.kr/MiniERP/oil/image/카니발(8270A).jpg","Top: GREEN, DEWBERRY, CASSIS, GRAPEFRUIT, MANDARIN, ORANGE\nMiddle: FREESIA, OZONE, JASMINE, APPLE, RASPBERRY, ROSE\nBase: MOSS, MUSK, SANDAL, RASPBERRY","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("77","1","오렌지","Citrus","","100","46000.00","http://oilpick.co.kr/MiniERP/oil/image/오렌지(325CA).jpg","Top: ORANGE JUICE, LEMON\nMiddle: ORANGE BLOSSOM, FRUITY\nBase: ORANGE","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("78","1","크롬","Woody&Spicy","","100","42000.00","http://oilpick.co.kr/MiniERP/oil/image/크롬(3270A).jpg","Top: LEMON, ORANGE, CARDAMOM, PETITGRAIN, SAGE, SEA NOTES\nMiddle: ROSEMARY, LILY-OF-THE-VALLEY, JASMINE, ROSE\nBase: MUSK, VETIVER, SANDALWOOD, ROSEWOOD, OAKMOSS","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("79","1","시트로넬라&레몬","Citrus","","100","54000.00","http://oilpick.co.kr/MiniERP/oil/image/시트로넬라&레몬(3Z).jpg","Top: LEMON, LEMONGRASS, LIME, ORANGE, VERBENA\nMiddle: LEMON, BERGAMOT, ALDEHYDE\nBase: LEMON","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("80","1","그린허브","Green&Herb","","100","40000.00","http://oilpick.co.kr/MiniERP/oil/image/그린허브(1276A).jpg","Top: ORANGE, LIME, HERB\nMiddle: ROSE, JASMINE, MUGUET\nBase: TONKA, CEDARWOOD, SANDALWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("81","1","시트러스릴리","Floral","","100","44000.00","http://oilpick.co.kr/MiniERP/oil/image/CITRUS LILY 49A-시트러스릴리.png","Top: GREEN, LEMON, GRAPEFRUIT, BERGAMOT, ROSEMARY, LAVENDER\nMiddle: YLANG YLANG, MUGUET, JASMINE, ROSE, LEMONGRASS, CLARY SAGE, JASMINE, OZONE\nBase: VANILLA, FLORAL, MUSK, CEDAR, PATCHOULI, VETIVER, TONKA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("82","1","그린네롤리","Green&Herb","더향 프래그런스 오일 - Vert / 워커힐호텔 향","100","45000.00","http://oilpick.co.kr/MiniERP/oil/image/AT GREEN 1277A-그린네롤리.png","Top: BERGAMOT, ORANGE, GREEN\nMiddle: ROSE, ORANGE FLOWER, LAVANDIN\nBase: SANDALWOOD, MOSS, LABDANUM","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("83","1","코코마드","Citrus","샤넬 코코마드 모아젤 타입","100","40000.00","http://oilpick.co.kr/MiniERP/oil/image/코코마드(1031A).jpg","Top: BERGAMOT, PINK PEPPER, JUNIPERBERRY, BLOOD ORANGE\nMiddle: ROSE, JASMINE, FREESIA, NEROLI, PATCHOULI\nBase: MUSK, VANILLA, MOSS, AMBER, SANDALWOOD, VETIVER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("84","1","모링가","Floral","","100","41000.00","http://oilpick.co.kr/MiniERP/oil/image/모링가(78A).jpg","Top: ACACIA, MANDARIN ORANGE, FRUITY NOTE\nMiddle: YLANG YLANG, GARDENIA, LILY, ROSE, JASMIN\nBase: MUSK, AMBER, TONKA BEAN, WOODY","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("85","1","넥타린","Fruity","","100","38000.00","http://oilpick.co.kr/MiniERP/oil/image/넥타린(674A).jpg","Top: GREEN NOTE, BLACK QURRANT, PETITGRAIN\nMiddle: NECTARINE, BLACK LOCUST, PLUM\nBase: VETIVER, PEACH, MUSK, AMBER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("86","1","선샤인","Floral","","100","32000.00","http://oilpick.co.kr/MiniERP/oil/image/선샤인(642A).jpg","Top: GREEN, OZONE, TAGETE, CHAMOMILE, PEACH, MANDARIN\nMiddle: ROSE, JASMINE, MUGUET, CYCLAMEN, FRUITY, CASSIS, JASMINE\nBase: SANDALWOOD, MOSS, VANILLA, CEDARWOOD, VETIVER, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("87","1","코지","Woody&Spicy","","100","50000.00","http://oilpick.co.kr/MiniERP/oil/image/COZY 328A-코지.png","Top: FREESIA, ORANGE, LEMON\nMiddle: FREESIA, JASMINE, MUGUET, ROSE, HYACINTH, ORANGE BLOSSOM\nBase: MUSK, AMBER, CEDARWOOD, VANILLA, HONEY","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("88","1","에이큐피오니","Floral","","100","46000.00","http://oilpick.co.kr/MiniERP/oil/image/에이큐피오니(417A).jpg","Top: PEONY, GREEN, ORANGE, OZONE, FRUITY\nMiddle: LILY OF THE VALLEY, ROSE, JASMINE, FRUITY\nBase: SANDALWOOD, MUSK, PEACH","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("89","1","바닐라 슈가","Sweet","","100","65000.00","http://oilpick.co.kr/MiniERP/oil/image/바닐라-슈가(877A).jpg","Top: VANILLA\nMiddle: VANILLA, JASMINE, MUGUET\nBase: MUSK, AMBER, VANILLA, SANDALWOOD, COCONUT","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("90","1","롤리타씨","Floral","","100","52000.00","http://oilpick.co.kr/MiniERP/oil/image/롤리타씨(676A).jpg","Top: BERGAMOT, MANDARIN\nMiddle: PINK PEPPER, SWEET PEA, HELIOTROPE\nBase: PATCHOULI, AMBER, TONKA BEAN, ELEMI","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("91","1","로즈","Floral","","100","22000.00","http://oilpick.co.kr/MiniERP/oil/image/로즈(6751A).jpg","Top: ROSE, GERANIUM, GREEN\nMiddle: ROSE, MUGUET, VIOLET\nBase: MUSK, SANDALWOOD, AMBER, CEDARWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("92","1","랄프","Fruity","","100","53000.00","http://oilpick.co.kr/MiniERP/oil/image/랄프(485W).jpg","Top: GERANIUM, MANDARIN, CORIANDER, RASPBERRY, GREEN APPLE\nMiddle: FREESIA, JASMINE, ROSE, YLANG YLANG, MUGUET\nBase: SANDALWOOD, MUSK, VANILLA, AMBER, PEACH","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("93","1","자스민민트","Floral","","100","55000.00","http://oilpick.co.kr/MiniERP/oil/image/자스민민트(2Z).jpg","Top: PEPPERMINT, BERGAMOT, MANDARIN, TAGETE, GRAPEFRUIT\nMiddle: LILY, JASMINE, HYACINTH, ROSE, NARCISSE, ROSEMARY, MINT\nBase: MUSK, MOSS, FRUITY, HERBAL","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("94","1","플라워솝","Floral","","100","35000.00","http://oilpick.co.kr/MiniERP/oil/image/플라워솝(586A).jpg","Top: ALDEHYDE, ROSE\nMiddle: ROSE, JASMINE, MUGUET\nBase: WHITE MUSK, SANDALWOOD, TONKA BEAN","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("95","1","일랑","Floral","","100","38000.00","http://oilpick.co.kr/MiniERP/oil/image/일랑(1182A).jpg","Top: LEMON, GREEN, NEROLI, ORANGE\nMiddle: ORANGE FLOWER, ROSE, YLANG, OSMANTUS, JASMIN\nBase: ORANGE FLOWER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("96","1","카시스릴리","Floral","","100","39000.00","http://oilpick.co.kr/MiniERP/oil/image/카시스릴리(9A).jpg","Top: CASSIS, SPICY NOTE\nMiddle: LILY, HYACINTH, MUGUET\nBase: MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("97","1","로드","Fruity","","100","75000.00","http://oilpick.co.kr/MiniERP/oil/image/로드(69A).png","Top: MELON, PEAR\nMiddle: ROSE, FREESIA\nBase: RHUBARB, AMBER, PATCHOULI, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("98","1","블루베리 머스크","Fruity","","100","52000.00","http://oilpick.co.kr/MiniERP/oil/image/블루베리-머스크(4A).jpg","Top: PEAR, RASPBERRY, STRAWBERRY, BLUEBERRY\nMiddle: CYCLAMEN, PEONY, MAGNOLIA\nBase: VANILLA, TONKA BEAN, SANDALWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("99","1","그레이프프루츠 앤 베리","Fruity","","100","57000.00","http://oilpick.co.kr/MiniERP/oil/image/그레이프프루츠-앤-베리(3A).jpg","Top: GRAPEFRUIT, ORANGE\nMiddle: JASMIN, LOTUS, RASPBERRY\nBase: PEACH, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("100","1","슈가베리","Fruity","","100","46000.00","http://oilpick.co.kr/MiniERP/oil/image/슈가베리(2A).jpg","Top: LIME, LEMON, GRAPEFRUIT\nMiddle: RASPBERRY, LYCHEE, FREESIA, LOTUS\nBase: TONKA BEAN, AMBER, SANDALWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("101","1","웨딩무드","Floral","","100","50000.00","http://oilpick.co.kr/MiniERP/oil/image/웨딩무드(1A).jpg","Top: STRAWBERRY, PEACH, ORANGE\nMiddle: ROSE, FREESIA, JASMINE, MAGNOLIA\nBase: AMBER, CEDARWOOD, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("102","1","실키우드","Woody&Spicy","","100","91000.00","http://oilpick.co.kr/MiniERP/oil/image/실키우드(68A).jpg","Top: ROSE, CYPRESS\nMiddle: SANDALWOOD, CEDARWOOD\nBase: AMBER, SPICY, WHITE MUSK, ROSEWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("103","1","아티산 존바바토스","Floral","","100","35000.00","http://oilpick.co.kr/MiniERP/oil/image/아티산-존바바토스(6021A).jpg","Top: ORANGE, LEMON, BERGAMOT, CLEMENTINE, TANGERINE,\nMiddle: ORANGE BLOSSOM, JASMINE, MUGUET, LAVENDER\nBase: AMBER, MUSK, WOOD, MOSS","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("104","1","체리블라썸","Floral","","100","39000.00","http://oilpick.co.kr/MiniERP/oil/image/체리블라썸(57A).jpg","Top: PEACH, PEONY\nMiddle: MUGUET, JASMINE, YLANG YLANG\nBase: MUSK, VANILLA, WOOD, HELIOTROPE","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("105","1","로즈마리","Green&Herb","","100","57000.00","http://oilpick.co.kr/MiniERP/oil/image/로즈마리(92CA).jpg","Top: EUCALYPTUS, PINE, ROSEMARY\nMiddle: PINE, ROSEMARY\nBase: PINE, SAGE","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("106","1","스파아로마","Green&Herb","","100","31000.00","http://oilpick.co.kr/MiniERP/oil/image/스파아로마(1070A).jpg","Top: BERGAMOT, LEMON BALM\nMiddle: ARTEMISIA, ROSEMARY, LAVENDER\nBase: SANDALWOOD, CEDAR","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("107","1","마그놀리아","Floral","클로에 오드퍼퓸","100","44000.00","http://oilpick.co.kr/MiniERP/oil/image/마그놀리아(19A).jpg","Top: LITCHI, PEONY, FREESIA\nMiddle: MAGNOLIA, ROSE, MUGUET\nBase: CEDARWOOD, AMBER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("108","1","이솝휠","Woody&Spicy","더향 프레그런스 오일 - Ardeur / 이솝 휠 타입","100","46000.00","http://oilpick.co.kr/MiniERP/oil/image/AT HWYL-6968A-휠.png","Top: THYME, ORANGE, CLOVE, GINGER\nMiddle: CYPRESS, GERANIUM\nBase: VETIVER, OLIBANUM, CEDARWOOD, AMBER, PATCHOULI","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("109","1","화이트플로랄","Floral","","100","44000.00","http://oilpick.co.kr/MiniERP/oil/image/화이트플로랄(21A).jpg","Top: ALDEHYDE\nMiddle: MAGNOLIA, ROSE, WHITE JASMIN, LILY\nBase: MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("110","1","오션릴리","Aqua","바디샵 아쿠아릴리","100","44000.00","http://oilpick.co.kr/MiniERP/oil/image/오션릴리 29A.png","Top: GREEN APPLE, TANGERINE, WATER MELON, BAMBOO\nMiddle: ROSE, MUGUET, VIOLET, LILY\nBase: MUSK, CEDARWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("111","1","폴링","Green&Herb","","100","40000.00","http://oilpick.co.kr/MiniERP/oil/image/폴링(53A).jpg","Top: MINT, EUCALYPTUS\nMiddle: CHAMOMILE, LAVENDER, LAUREL LEAF\nBase: WOODY","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("112","1","아이스블루민트","Citrus","","100","50000.00","http://oilpick.co.kr/MiniERP/oil/image/아이스블루민트(3CA).jpg","Top: LAVANDIN, LAVENDER, MINT\nMiddle: LAVENDER, FRUITY, ROSEMARY, MINT\nBase: PATCHOULI, HERBAL","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("113","1","대나무","Green&Herb","더향 프래그런스 오일 - Bambous","100","44000.00","http://oilpick.co.kr/MiniERP/oil/image/BAMBOO 82CA-대나무.png","Top: BERGAMOT, ORANGE, LEMON, OZONIC, YUZU\nMiddle: JASMINE, MUGUET, LAVANDIN, GERANIUM, BLACKCURRANT\nBase: MUSK, SANDALWOOD, CEDARWOOD, AMBER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("114","1","라벤더","Floral","","100","43000.00","http://oilpick.co.kr/MiniERP/oil/image/라벤더(24CA).jpg","Top: LEMON, BERGAMOT, LAVENDER\nMiddle: LAVENDER, LAVANDIN\nBase: LAVENDER, LAVANDIN","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("115","1","프레쉬","Green&Herb","","100","48000.00","http://oilpick.co.kr/MiniERP/oil/image/프레쉬(11A).jpg","Top: YUZU, GALBANUM, EUCALYPTUS, CASSIS, BERGAMOT\nMiddle: JASMIN, CINNAMON, ROSEMARY, ORANGE FLOWER\nBase: MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("116","1","라벤더미스트","Floral","","100","33000.00","http://oilpick.co.kr/MiniERP/oil/image/라벤더미스트(17A).jpg","Top: MARINE NOTE, EUCALYPTUS\nMiddle: LAVENDER, ROSEMARY\nBase: MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("117","1","그린빌","Green&Herb","","100","37000.00","http://oilpick.co.kr/MiniERP/oil/image/그린빌(24A).jpg","Top: LIME, MINT, BERGAMOT\nMiddle: JASMIN, ORANGE FLOWER, ORCHID, ROSE\nBase: MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("118","1","민트폴","Green&Herb","","100","40000.00","http://oilpick.co.kr/MiniERP/oil/image/민트폴(41A).jpg","Top: PEPPERMINT\nMiddle: PEPPERMINT\nBase: PEPPERMINT","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("119","1","캐모마일라벤더","Floral","","100","41000.00","http://oilpick.co.kr/MiniERP/oil/image/캐모마일라벤더(26A).jpg","Top: EUCALYPTUS\nMiddle: LAVENDER, CHAMOMILE, ROSEMARY\nBase: MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("120","1","그린티","Green&Herb","엘리자베스아덴/그린티","100","50000.00","http://oilpick.co.kr/MiniERP/oil/image/그린티(43A).jpg","Top: LEMON, BERGAMOT, ORANGE, MINT\nMiddle: GREEN TEA, FENNEL, CARNATION, JASMIN\nBase: AMBER, MUSK, OAKMOSS","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("121","1","레몬","Citrus","","100","44000.00","http://oilpick.co.kr/MiniERP/oil/image/LEMON 75CA-레몬.png","Top: LEMON, LIME, ORANGE\nMiddle: LEMON, GREEN NOTE\nBase: LEMONGRASS, LEMON","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("122","1","라임앤허브","Green&Herb","","100","58000.00","http://oilpick.co.kr/MiniERP/oil/image/라임앤허브(34A).jpg","Top: LIME, BERGAMOT\nMiddle: BASIL, LILAC, THYME, IRIS\nBase: VETIVER, PATCHOULI","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("123","1","쿨시트러스릴리","Floral","","100","50000.00","http://oilpick.co.kr/MiniERP/oil/image/쿨시트러스릴리(36A).jpg","Top: MANDARIN, ORANGE, CASSIS\nMiddle: LIME, JASMIN, OZONE, LILY\nBase: MUSK, WOODY, PEACH","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("124","1","익스트림","Floral","","100","50000.00","http://oilpick.co.kr/MiniERP/oil/image/익스트림(32A).jpg","Top: LEMON, BERGAMOT\nMiddle: JASMIN, TUBEROSE\nBase: VANILLA, LEATHER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("125","1","프레쉬리치","Fruity","","100","58000.00","http://oilpick.co.kr/MiniERP/oil/image/프레쉬리치(45A).jpg","Top: GRAPEFRUIT, LEMON, LIME BLOSSOM\nMiddle: LITCHI, PEONY, FREESIA\nBase: SANDALWOOD, AMBER, TONKA BEAN","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("126","1","어트렉티브유","Floral","","100","44000.00","http://oilpick.co.kr/MiniERP/oil/image/어트렉티브유(47A).jpg","Top: GRAPEFRUIT, PEAR, PINK PEPPER\nMiddle: ROSE, GERANIUM, PEONY\nBase: VANILLA, TONKA BEAN, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("127","1","오로라","Floral","","100","60000.00","http://oilpick.co.kr/MiniERP/oil/image/ORORA 42A-오로라.png","Top: STRAWBERRY, PEAR, RASPBERRY\nMiddle: CYCLAMEN, PEONY, MAGNOLIA\nBase: SANDALWOOD, TONKA BEAN, VANILLA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("128","1","시크릿리버","Floral","","100","62000.00","http://oilpick.co.kr/MiniERP/oil/image/시크릿리버(50A).jpg","Top: CARROT, MANGO, TOMATO, GRAPEFRUIT\nMiddle: LOTUS, HYACINTH, PEONY, ORANGE\nBase: LABDANUM, IRIS, MUSK, INCENSE","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("129","1","엔젤푸드","Sweet","","100","30000.00","http://oilpick.co.kr/MiniERP/oil/image/ANGELFOOD 56A-엔젤푸드.png","Top: COOKIE\nMiddle: ALMOND, COOKIE, OAT MEAL\nBase: VANILLA, SUGAR","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("130","1","데이지가든","Floral","","100","46000.00","http://oilpick.co.kr/MiniERP/oil/image/DAISYGARDEN 48A-데이지가든.png","Top: APPLE, DAISY, CASSIS, GRAPEFRUIT\nMiddle: VIOLET, JASMIN, GARDENIA, ROSE, FREESIA\nBase: MUSK, VANILLA, CEDARWOOD, SANDALWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("131","1","가드니아","Floral","","100","49000.00","http://oilpick.co.kr/MiniERP/oil/image/가드니아(33A).jpg","Top: CYPRESS, ELEMI, CITRUS LABDANUM\nMiddle: PATCHOULI, OLIBANUM, GUAIACWOOD\nBase: BENZOIN, CEDAR, VETIVER, MUSK, SANDAL, AMBER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("132","1","코튼두베","Floral","","100","38000.00","http://oilpick.co.kr/MiniERP/oil/image/코튼두베(35A).jpg","Top: LITCHI, ALDEHYDE\nMiddle: MAGNOLIA, ROSE, LILY\nBase: MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("133","1","코코넛","Fruity","","100","35000.00","http://oilpick.co.kr/MiniERP/oil/image/COCONUT 52A-코코넛.png","Top: BLACKBERRY, STRAWBERRY, COCONUT, BERGAMOT\nMiddle: ROSE, JASMINE, CYCLAMEN\nBase: VANILLA, CARAMEL, PEACH, MUSK, CEDAR","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("134","1","롤리타","Floral","","100","60000.00","http://oilpick.co.kr/MiniERP/oil/image/롤리타(458A).jpg","Top: VIOLET, ANISE\nMiddle: CHERRY, ORRIS, IRIS, LICORICE\nBase: PRALINE, WHITE MUSK, TONKA BEAN, VANILLA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("135","1","러브스펠","Fruity","","100","39000.00","http://oilpick.co.kr/MiniERP/oil/image/LOVESPELL 70A-러브스펠.png","Top: TAMARINE, ALDEHYDE, LEMON, BERGAMOT, ORANGE\nMiddle: FRUITY, CITRUS, PEACH, STRAWBERRY\nBase: MUSK, PEACH","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("136","1","트로피컬","Fruity","","100","39000.00","http://oilpick.co.kr/MiniERP/oil/image/트로피컬(651A).jpg","Top: ORANGE, LEMON, LIME, BERRY MIX, WATERY\nMiddle: ROSE, OZONE\nBase: MUSK, PEACH","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("137","1","퍼시픽파라다이스","Citrus","","100","50000.00","http://oilpick.co.kr/MiniERP/oil/image/퍼시픽파라다이스(1CA).png","Top: MANDARIN, LIME, GRAPEFRUIT, OZONE\nMiddle: PRUNE, ROSE, JASMINE, APPLE, FREESIA, VIOLET\nBase: MUSK, CEDARWOOD, AMBER, SANDAL, CARAMEL, COCONUT","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("138","1","피치","Fruity","","100","40000.00","http://oilpick.co.kr/MiniERP/oil/image/피치(87CA).png","Top: PEACH, APPLE, APRICOT\nMiddle: PEACH, JASMINE, MUGUET\nBase: PEACH, RASPBERRY, MUSK, VANILLA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("139","1","바나나","Fruity","","100","25000.00","http://oilpick.co.kr/MiniERP/oil/image/바나나(359CA).jpg","Top: BANANA\nMiddle: BANANA\nBase: BANANA, VANILLA, CARAMEL","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("140","1","블랙베리","Fruity","","100","39000.00","http://oilpick.co.kr/MiniERP/oil/image/ALL TO DREAM-7A-블랙베리.png","Top: OLIVE, BLACKBERRY\nMiddle: BLACKBERRY, NEROLI, JASMIN, ROSE\nBase: MUSK, HELIOTROPE","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("141","1","스프링뉴스","Fruity","","100","60000.00","http://oilpick.co.kr/MiniERP/oil/image/스프링뉴스(59A).jpg","Top: MELON, APPLE, PEACH\nMiddle: JASMIN, ROSE\nBase: MUSK, AMBERGRIS","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("142","1","쿨프루츠","Fruity","","100","33000.00","http://oilpick.co.kr/MiniERP/oil/image/쿨프루츠(13A).jpg","Top: LIME, MINT, PEACH\nMiddle: STRAWBERRY, RASPBERRY\nBase: SUGAR, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("143","1","슈가드페어","Fruity","","100","39000.00","http://oilpick.co.kr/MiniERP/oil/image/슈가드페어(10A).jpg","Top: PEAR FRUIT\nMiddle: PEAR JUICE\nBase: SUGAR","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("144","1","리치피치","Fruity","","100","39000.00","http://oilpick.co.kr/MiniERP/oil/image/리치피치(53A).jpg","Top: PEACH FRUIT\nMiddle: PEACH JUICE\nBase: PECH SWEET","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("145","1","체리붐","Fruity","","100","33000.00","http://oilpick.co.kr/MiniERP/oil/image/체리붐(15A).jpg","Top: CHERRY, PEACH\nMiddle: CHERRY\nBase: VANILLA, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("146","1","마일드피치","Fruity","","100","32000.00","http://oilpick.co.kr/MiniERP/oil/image/마일드피치(14A).jpg","Top: PEACH, CITRUS\nMiddle: PEACH, SUGAR\nBase: PEACH, VANILLA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("147","1","카시스로즈","Floral","","100","63000.00","http://oilpick.co.kr/MiniERP/oil/image/카시스로즈(58A).jpg","Top: BERGAMOT, MANDARIN ORANGE, CASSIS, BLACK CURRANT\nMiddle: ROSE, BLACK CURRANT LEAF\nBase: AMBER, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("148","1","스노우베리","Fruity","","100","36000.00","http://oilpick.co.kr/MiniERP/oil/image/스노우베리(38A).jpg","Top: MINT, APRICOT\nMiddle: STRAWBERRY, RASPBERRY, LITCHI, ACAIBERRY\nBase: MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("149","1","포메그라네이트","Fruity","","100","39000.00","http://oilpick.co.kr/MiniERP/oil/image/포메그라네이트(16A).jpg","Top: GRAPEFRUIT, ORANGE, POMEGRANATE\nMiddle: BLACK CURRANT, ROSE, PEACH, STRAWBERRY\nBase: VANILLA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("150","1","레드애플","Fruity","","100","24000.00","http://oilpick.co.kr/MiniERP/oil/image/레드애플(52A).jpg","Top: RED APPLE\nMiddle: RED APPLE, JASMINE\nBase: PEACH, VANILLA, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("151","1","브리즈","Citrus","","100","40000.00","http://oilpick.co.kr/MiniERP/oil/image/AT DREAM 20A-브리즈.png","Top: APPLE, GREEN, CYCLAMEN, LEMON, PEAR\nMiddle: OZONE, VIOLET, MELON, PEONY, MUGUET\nBase: PEACH, CEDAR, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("152","1","레몬버베나","Citrus","","100","51000.00","http://oilpick.co.kr/MiniERP/oil/image/레몬버베나(46A).jpg","Top: MANDARIN, LEMON, LIME, ORANGE\nMiddle: VERBENA, FLORAL\nBase: MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("153","1","해피타임","Citrus","","100","47000.00","http://oilpick.co.kr/MiniERP/oil/image/해피타임(44A).jpg","Top: ORANGE, MANDARIN, BERGAMOT, APPLE, GRAPEFRUIT\nMiddle: ROSE, MUGUET, FREESIA, ORCHID\nBase: MUSK, AMBER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("154","1","포레스트","Green&Herb","","100","37000.00","http://oilpick.co.kr/MiniERP/oil/image/포레스트(646A).jpg","Top: BERGAMOT, PINE NEEDLE, ROSEMARY, GERANIUM, GALBANUM\nMiddle: JASMINE, MUGUET, PATCHOULI, CINNAMON, HYACINTH, OZONE\nBase: AMBER, MUSK, VETIVER, CEDAR, CIVET, TONKA BEAN","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("155","1","실버스타","Woody&Spicy","","100","39000.00","http://oilpick.co.kr/MiniERP/oil/image/실버스타(655A).jpg","Top: MANDARIN\nMiddle: CAMELLIA, LICORICE, JASMIN\nBase: INCENSE, AMBER, WOODY","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("156","1","마이케빈","Woody&Spicy","","100","62000.00","http://oilpick.co.kr/MiniERP/oil/image/마이케빈(39A).jpg","Top: PINK PEPPER, CARDAMOM, OLIBANUM, BERGAMOT, ELEMI\nMiddle: BLACK TEA, SAFFRON, JUNIPERBERRY\nBase: AMBERGRIS, MUSK, VANILLA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("157","1","클린코튼","Musk","더향 보니타 1번","100","46000.00","http://oilpick.co.kr/MiniERP/oil/image/COTTON 779CA-클린코튼.png","Top: LEMON, LEAFY GREEN, OZONE\nMiddle: ORANGE FLOWER, LILY OF THE VALLEY\nBase: SANDALWOOD, AMBER, VETIVER, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("158","1","베이비화이트","Floral","","100","44000.00","http://oilpick.co.kr/MiniERP/oil/image/베이비화이트 77CA.png","Top: BERGAMOT, YLANG YLANG, ALDEHYDE\nMiddle: ROSE, LILY OF THE VALLEY, JASMINE\nBase: SANDALWOOD, VANILLA, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("159","1","앰버릴리","Floral","","100","38000.00","http://oilpick.co.kr/MiniERP/oil/image/AMBER LILY 57A-앰버릴리.png","Top: GREEN NOTE\nMiddle: LILY, JASMIN, CARNATION\nBase: AMBER, WOODY","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("160","1","부케","Floral","","100","32000.00","http://oilpick.co.kr/MiniERP/oil/image/부케 333A.png","Top: LAVANDIN, GREEN, GALBANUM\nMiddle: MUGUET, JASMINE, ROSE\nBase: FLORAL","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("161","1","바질","Citrus","더향 보니타 4번 / 조말론 라임바질만다린 타입","100","41000.00","http://oilpick.co.kr/MiniERP/oil/image/AT BASIL 4481A-바질.png","Top: LEMON, LIME, THYME, SPEARMINT, MANDARIN, ROSEMARY\nMiddle: CARAWAY, LILAC, BASIL, PATCHOULI, FLORAL\nBase: LEATHER, VETIVER, MOSS, CEDARWOOD, SANDALWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("162","1","라임바질","Citrus","조말론 라임바질&만다린 타입 / 64CA 개선 향","100","55000.00","http://oilpick.co.kr/MiniERP/oil/image/라임바질(6378CA).jpg","Top: LEMON VERBENA, LEMON, LIME, BERGAMOT, MANDARINE, ROSEMARY\nMiddle: MUGUET, IRIS, LILAC, BASIL\nBase: VETIVER, PATCHOULI, COCONUT, SANDALWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("163","1","베르가못","Citrus","멘디니 디퓨저 - 프레스코 / 르라보 베르가못 타입","100","40000.00","http://oilpick.co.kr/MiniERP/oil/image/AT BERGAMOT 451A-베르가못.png","Top: BERGAMOT, LEMON, GRAPEFRUIT, ALDEHYDIC\nMiddle: OZONE, FLORAL, PINE\nBase: MUSK, AMBER, CEDARWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("164","1","따듯한 우디","Woody&Spicy","르라보 떼누아","100","54000.00","http://oilpick.co.kr/MiniERP/oil/image/WARM WOOD 3256P-따듯한 우디.png","Top: FIG, BAY, BERGAMOT, NUTMEG\nMiddle: CEDARWOOD, VETIVER, MUSK\nBase: TOBACCO, HAY, VANILLA, SANDALWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("165","1","따듯한 우디","Woody&Spicy","","100","60000.00","http://oilpick.co.kr/MiniERP/oil/image/WARM WOOD 2965P-따듯한 우디.png","Top: PATCHOULI, CEDARWOOD\nMiddle: PATCHOULI, CEDARWOOD\nBase: PATCHOULI, CEDARWOOD, SANDALWOOD, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("166","1","시나센스","Woody&Spicy","","100","53000.00","http://oilpick.co.kr/MiniERP/oil/image/WARM WOOD 5305P-시나센스.png","Top: CINNAMON, INCENSE\nMiddle: ROSE, FREESIA\nBase: PATCHOULI, SANDALWOOD, COUMARIN","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("167","1","스펠온유","Floral","루이비통 스펠온유 타입","100","50000.00","http://oilpick.co.kr/MiniERP/oil/image/스펠온유(7566P).jpg","Top: VIOLET, IRIS, GREEN NOTES\nMiddle: ROSE, IRISM JASMINE\nBase: WHITE MUSK, ACACIA, PEACH","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("168","1","오리엔탈 우드","Woody&Spicy","","100","40000.00","http://oilpick.co.kr/MiniERP/oil/image/오리엔탈 우드(1863A).png","Top: LEMON, BERGAMOT, ORANGE, BLACK PEPPER, GALBANUM, ANIS\nMiddle: CARDAMON, PINEAPPLE, JASMINE, VIOLET,LAVENDER\nBase: VANILLA, CARAMEL, MUSK, AMBER, CEDAR, PATCHOULI, VETIVER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("169","1","체리","Fruity","","100","43000.00","http://oilpick.co.kr/MiniERP/oil/image/체리(6545A).jpg","Top: CHERRY, ORANGE, ALMOND, WINEY\nMiddle: STRAWBERRY, ALMOND, BLACKCURRANT\nBase: VANILLA, CARAMEL, RASPBERRY, PEACH, HELIOTROPE","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("170","1","아사이베리","Fruity","","100","37000.00","http://oilpick.co.kr/MiniERP/oil/image/아사이베리(1466A).jpg","Top: ACAIBERRY, CRANBERRY, BLUEBERRY, FRUITY\nMiddle: ACAIBERRY, MIXED BERRIES, FRUITY, MULBERRY\nBase: STRAWBERRY, VANILLA, PEACH, DEWBERRY","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("171","1","코튼","Musk","","100","37000.00","http://oilpick.co.kr/MiniERP/oil/image/코튼(1508A).jpg","Top: LEMON, LEAFY GREEN, OZONE\nMiddle: ORANGE FLOWER, LILY OF THE VALLEY\nBase: SANDALWOOD, AMBER, VETIVER, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("172","1","라벤더","Floral","","100","37000.00","http://oilpick.co.kr//MiniERP/oil/image/195.jpg","Top: BERGAMOT, ROSEMARY, LEMON\nMiddle: ROSE, MUGUET, LAVENDER, LAVANDIN\nBase: MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("173","1","루나","Floral","펜할리곤스 루나 타입","100","72000.00","http://oilpick.co.kr/MiniERP/oil/image/루나(1581P).jpg","Top: BERGAMOT, LEMON, MANDARIN\nMiddle: LAVENDER, GERANIUM, ROSE, ORANGE BLOSSOM\nBase: AMBER, MUSK, MOSSY","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("174","1","로투스","Floral","조말론 피그&로투스 플라워 타입","100","75000.00","http://oilpick.co.kr/MiniERP/oil/image/로투스(1589P).jpg","Top: BERGAMOT, ANISEED, PEPPERY, GRAPEFRUIT\nMiddle: ROSE, JASMIN, MUGUET, NEROLI, GERANIUM\nBase: CEDARWOOD, MUSK, TONKA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("175","1","누아르","Woody&Spicy","","100","110000.00","http://oilpick.co.kr/MiniERP/oil/image/누아르(9544P).jpg","Top: PEAR, APPLE, BERGAMOT JUNIPERBERRY\nMiddle: JASMINE, MUGUET\nBase: AMBER, MUSK, MOSS, VETIVER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("176","1","오데썽","Citrus","딥디크 오데썽 타입","100","70000.00","http://oilpick.co.kr/MiniERP/oil/image/오데썽 1579P.png","Top: BERGAMOT, LEMON, ORANGE, PEPPERMINT, GALBANUM\nMiddle: NEROLI, JASMINE, ANGELICA, LAVENDER\nBase: AMBER, MUSK, ELEM","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("177","1","필로시코스","Fruity","딥디크 필로시코스 타입","100","110000.00","http://oilpick.co.kr/MiniERP/oil/image/필로시코스(696P).jpg","Top: GREEN, CYPRESS, FIG, MANDARIN, GALBANUM\nMiddle: TUBEROSE, COCONUT, OSMANTHUS, CYCLAMEN, ROSE\nBase: AMBER, CEDAR, TONKA, BENZOIN","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("178","1","리타","Floral","롤리타 렘피카 타입","100","85000.00","http://oilpick.co.kr/MiniERP/oil/image/리타(1310P).jpg","Top: BERGAMOT, RUM, ANISEED\nMiddle: MUGUET, FREESIA, JASMINE, CARDAMON\nBase: MUSK, SANDAL, VANILLA, CARAMEL, TONKA, VETIVER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("179","1","상탈","Woody&Spicy","르라보 상탈 타입","100","130000.00","http://oilpick.co.kr/MiniERP/oil/image/상탈(8279P).jpg","Top: SANDALWOOD, NAGARMOTHA, CARDAMON, ROSEMARY, BLACK PEPPER\nMiddle: SANDALWOOD, VIOLET, IRIS, VIRGINIA CEDAR, GURJUN\nBase: SANDALWOOD, AMBER, MUSK, LEATHER, OLIBANUM","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("180","1","파이어플레이스","Woody&Spicy","메종 마르지엘라 파이어플레이스 타입","100","90000.00","http://oilpick.co.kr/MiniERP/oil/image/파이어플레이스(1582P).jpg","Top: PINK PEPPER, MANDARIN, CORIANDER\nMiddle: ROSE, JASMINE, ORANGE FLOWER\nBase: AMBER, CEDARWOOD, VANILLA, MUSK, BENZOIN, TONKA, PATCHOULI","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("181","1","테싯","Woody&Spicy","이솝 테싯 타입","100","110000.00","http://oilpick.co.kr/MiniERP/oil/image/테싯(5565P).jpg","Top: BERGAMOT, ORANGE, BASIL\nMiddle: SPEARMINT, ROSEMARY, FENNEL\nBase: VETIVER, AMBER, CEDAR, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("182","1","프루티 플로럴","Floral","","100","42000.00","http://oilpick.co.kr/MiniERP/oil/image/프루티-플로럴(1570A).jpg","Top: RED APPLE, LEMON, GREEN, PEACH, TEA ROSE\nMiddle: LOTUS, CASSIS, ROSE, JASMINE, GERANIUM\nBase: VANILLA, SANDAL, CEDAR, HELIOTROPE, MUSK, MOSS","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("183","1","코코넛 라임","Fruity","","100","42000.00","http://oilpick.co.kr/MiniERP/oil/image/코코넛-라임(1529A).jpg","Top: ORANGE, LEMON, LIME, MANDARIN\nMiddle: JASMINE, PINEAPPLE, OZONE, MUGUET\nBase: SANDALWOOD, COCONUT, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("184","1","자몽","Fruity","","100","76000.00","http://oilpick.co.kr/MiniERP/oil/image/자몽(9233P).jpg","Top: GRAPEFRUIT, ORANGE, GREEN, APPLE\nMiddle: GRAPEFRUIT, PINEAPPLE, MUGUET\nBase: MUSK, RHUBARB","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("185","1","베르테","Citrus","에르메스 오 도랑쥬 베르테 타입","100","90000.00","http://oilpick.co.kr/MiniERP/oil/image/베르테(5714P).jpg","Top: ORANGE, MANDARIN ORANGE, BERGAMOT, LEMON, MINT, CASSIS\nMiddle: ORANGE BLOSSOM, JASMINE\nBase: PATCHOULI, OAKMOSS","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("186","1","베르가못","Citrus","르라보 베르가못22 타입","100","78000.00","http://oilpick.co.kr/MiniERP/oil/image/베르가못(1251P).jpg","Top: BERGAMOT, LEMON, GRAPEFRUIT, ALDEHYDIC\nMiddle: OZONE, FLORAL, PINE\nBase: MUSK, AMBER, CEDARWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("187","1","파인","Woody&Spicy","","100","37000.00","http://oilpick.co.kr/MiniERP/oil/image/파인(275A).jpg","Top: PINE, CAMPHOR, EUCALYPTUS\nMiddle: PINE, LIME, ROSEMARY\nBase: MUSK, MOSS","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("188","1","금목서","Floral","","100","40000.00","http://oilpick.co.kr//MiniERP/oil/image/215.jpg","Top: GREEN APPLE, OSMANTHUS\nMiddle: JASMINE, TUBEROSE, FRUITY, ROSE, OSMANTHUS\nBase: MUSK, CEDAR, PEACH, VETIVER, VANILLA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("189","1","프루티","Fruity","","100","43000.00","http://oilpick.co.kr/MiniERP/oil/image/프루티(7498A).jpg","Top: GRAPEFRUIT, BLACK PEPPER, CORIANDER, OZONE, SAFFRON, CLARY SAGE\nMiddle: NUTMEG, ROSE, VIOLET LEAF, APPLE, CINNAMON BARK, CARDAMON\nBase: VANILLA, AMBER, PATCHOULI, OUD, MUSK, TONKA BEAN","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("190","1","라레","Floral","탬버린즈 라레 타입","100","53000.00","http://oilpick.co.kr/MiniERP/oil/image/LALE 6099P-라레.png","Top: APPLE, BERGAMOT, GREEN\nMiddle: ROSE, JASMIN, VIOLET\nBase: AMBER, MUSK, CEDARWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("191","1","인플로레센스","Floral","바이레도 인플로레센스 타입","100","65000.00","http://oilpick.co.kr/MiniERP/oil/image/인플로레센스(5012P).jpg","Top: FREESIA, ROSE\nMiddle: MUGUET, MAGNOLIA\nBase: JASMINE, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("192","1","오하이 가든","Floral","대니멕켄지 오하이 가든 타입","100","53000.00","http://oilpick.co.kr/MiniERP/oil/image/오하이-가든(3970P).jpg","Top: MANDARIN, BITTER ORANGE, LEMON, GINGER, GREEN LEAF\nMiddle: ROSE, DAISY, NEROLI, HELIOTROPE, VIOLET\nBase: WOODY, MOSS, MUSK, AMBER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("193","1","와일드 체리","Fruity","만세라 와일드 체리 타입","100","83000.00","http://oilpick.co.kr/MiniERP/oil/image/와일드-체리(5306P).jpg","Top: BERGAMOT, LEMON, CHERRY\nMiddle: ROSE, JASMINE, CHERRY\nBase: VANILLA, AMBER, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("194","1","무루","Fruity","프루티 플로럴 타입","100","35000.00","http://oilpick.co.kr/MiniERP/oil/image/무루(1480P).jpg","Top: GREEN, CITRUS,FRUITY\nMiddle: PEACH, JASMINE, MUGUET\nBase: CEDARWOOD,SWEET,MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("195","1","프리지아","Floral","산타마리아 노벨라 프리지아 타입","100","55000.00","http://oilpick.co.kr/MiniERP/oil/image/프리지아(5066P).jpg","Top: FREESIA\nMiddle: VIOLET, ROSE\nBase: IRIS, MUSK, PATCHOULI","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("196","1","자연스러운 플로럴","Floral","더향 프래그런스 오일 - Rosier","100","36000.00","http://oilpick.co.kr/MiniERP/oil/image/T ROSE 5751A-자연스러운 플로럴.png","Top: GREEN, CYCLAMEN, RHUBARB\nMiddle: ROSE, GERANIUM, MUGUET, TULIP\nBase: MUSK, SANDALWOOD, AMBER, CEDARWOOD, VETIVER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("197","1","자연스러운 플로럴","Floral","","100","37000.00","http://oilpick.co.kr/MiniERP/oil/image/자연스러운-플로럴(5672A).jpg","Top: GREEN, HONEYSUKLE, GALBANUM, EUCALYPTUS, PINE\nMiddle: LILAC, ROSE, JASMINE, DAISY\nBase: MUSK, AMBER, VANILLA, HELIOTROPE","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("198","1","자연스러운 플로럴","Citrus","","100","36000.00","http://oilpick.co.kr/MiniERP/oil/image/자연스러운 플로럴(3267A).jpg","Top: LEMON, TANGERINE, GRAPEFRUIT, ORANGE, GREEN\nMiddle: JASMINE, ORANGE FLOWER, NEROLI, GARDENIA, LILY\nBase: CEDARWOOD, VETIVER, MUSK, AMBER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("199","1","자연스러운 플로럴","Floral","더향 프래그런스 오일 - Fleurs","100","40000.00","http://oilpick.co.kr/MiniERP/oil/image/G FLORAL 4949A-자연스러운 플로럴.png","Top: LAVANDIN, GREEN, GALBANUM, FIG\nMiddle: JASMINE, ROSE, HYACINTH, CYCLAMEN\nBase: CEDARWOOD, AMBER, COCONUT","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("200","1","시트러스","Citrus","** 뽀로로 제안용 **","100","40000.00","http://oilpick.co.kr/MiniERP/oil/image/CITRUS 9433A-시트러스.png","Top: BERGAMOT, LEMON, ARTEMISIA\nMiddle: PINE, ROSEMARY, TEA TREE, SAGE\nBase: CEDARWOOD, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("201","1","자몽","Fruity","** 뽀로로 제안용 **","100","40000.00","http://oilpick.co.kr/MiniERP/oil/image/GRAPEFRUIT 5033A-자몽.png","Top: GRAPEFRUIT, ORANGE, GREEN, APPLE\nMiddle: GRAPEFRUIT, PINEAPPLE, MUGUET\nBase: MUSK, RHUBARB","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("202","1","체리","Fruity","** 뽀로로 제안용 **","100","38000.00","http://oilpick.co.kr/MiniERP/oil/image/CHERRY 5045A-체리.png","Top: CHERRY, ORANGE, ALMOND, WINEY\nMiddle: STRAWBERRY, ALMOND, BLACKCURRANT\nBase: VANILLA, CARAMEL, RASPBERRY, PEACH, HELIOTROPE","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("203","1","프루티","Fruity","** 뽀로로 제안용 **","100","42000.00","http://oilpick.co.kr/MiniERP/oil/image/PARTY 767A-프루티.png","Top: PINEAPPLE, LEMON, MELON, COCONUT\nMiddle: JASMINE, CASSIS, PEAR, FREESIA\nBase: AMBER, MUSK, VETIVER, MOSS, CARAMEL","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("204","1","마이웨이","Citrus","** 뽀로로 제안용 ** / 조지오 아르마니 마이웨이","100","41000.00","http://oilpick.co.kr/MiniERP/oil/image/WAY 1555A-마이웨이.png","Top: BERGAMOT, MANDARIN, CASSIS, PASSIONFRUIT, GREEN\nMiddle: JASMINE, ORNAGE FLOWER, YLANG YLANG\nBase: AMBER, MUSK, VANILLA, GOURMANDE","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("205","1","발다","Citrus","바이레도 발다 프리크 타입","100","92000.00","http://oilpick.co.kr/MiniERP/oil/image/발다 4435P.png","Top: BERGAMOT, LEMON, ORANGE, BLACK CURRANT, TAGETE\nMiddle: VIOLET, JASMINE, NEROLI, OSMANTHUS, CARAMEL\nBase: AMBER, VETIVER, TONKA BEAN, SANDALWOOD, MUSK, CEDARWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("206","1","지오","Aqua","조지오 아르마니 아쿠아 디 지오 타입","100","84000.00","http://oilpick.co.kr/MiniERP/oil/image/지오(5547P).jpg","Top: LIME, LEMON, BERGAMOT, JASMINE, ORANGE, MANDARIN, NEROLI\nMiddle: SEA NOTE, JASMINE, PEACH, FREESIA, ROSEMARY, CYCLAMEN,\nBase: WHITE MUSK, CEDARWOOD, PATCHOULI, AMBER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("207","1","태싯","Citrus","이솝 테싯 타입","100","65000.00","http://oilpick.co.kr/MiniERP/oil/image/태싯(7420P).jpg","Top: YUZU, LEMON, TANGERINE, GRAPEFRUIT, EUCALYPTUS, ANISE\nMiddle: BASIL, ORCHID, LILY, CLOVE, CYPRESS, YLANG YLANG\nBase: VETIVER, CEDARWOOD, SANDALWOOD, AMBER, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("208","1","코튼허그","Musk","포맨트 코튼 허그 타입","100","62000.00","http://oilpick.co.kr/MiniERP/oil/image/코튼허그(8001P).jpg","Top: MUGUET, COTTON FLOWER\nMiddle: ROSE, JASMINE, ORANGE FLOWER\nBase: PATCHOULI, CEDARWOOD, MUSK, MOSS","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("209","1","스타","Floral","몽블랑 스타워커 타입","100","70000.00","http://oilpick.co.kr/MiniERP/oil/image/스타(4166P).jpg","Top: BERGAMOT, HERBAL, GINGER, PEPPER, OZONE, LAVANDIN\nMiddle: JASMINE, MUGUET, FRUITY, ROSE, NUTMEG\nBase: MUSK, VETIVER, AMBER, CEDAR, MOSS","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("210","1","코튼","Musk","클린 웜코튼 타입","100","60000.00","http://oilpick.co.kr/MiniERP/oil/image/코튼(4975P).jpg","Top: LEMON, BERGAMOT, GRAPEFRUIT, LIME, LEMON VERBENA\nMiddle: ORANGE BLOSSOM, LILY, ORCHID, VIOLET\nBase: MUSK, AMBER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("211","1","페이지","Woody&Spicy","교보문고향","100","60000.00","http://oilpick.co.kr/MiniERP/oil/image/페이지(3170A).jpg","Top: LEMON, ORANGE, PINEAPPLE, EUCALYPTUS, ROSEMARY, PINE, MINT\nMiddle: FREESIA, MUGUET, JASMINE, ROSE\nBase: PATCHOULI, VANILLA, BALSAM, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("212","1","어진","Woody&Spicy","","100","55000.00","http://oilpick.co.kr/MiniERP/oil/image/EOJIN 5845P-어진.png","Top: LEMON, ORANGE, BLACK PEPPER, GALBANUM, GINGER, CINNAMON\nMiddle: CARDAMON, LAVANDIN, JASMINE, VIOLET\nBase: VANILLA, CARAMEL, MUSK, SANDALWOOD, CEDARWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("213","1","페이지","Woody&Spicy","교보문고향","100","90000.00","http://oilpick.co.kr/MiniERP/oil/image/페이지(6121A).png","Top: LEMON, ORANGE, BERGAMOT, EUCALYPTUS, ROSEMARY, MINT\nMiddle: ROSE, FREESIA, PINE\nBase: CEDARWOOD, SANDALWOOD, PATCHOULI","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("214","1","로파피에","Floral","딥디크 로라피에 타입","100","40000.00","http://oilpick.co.kr/MiniERP/oil/image/PAPIER 4216P-로파피에.png","Top: LILAC, MIMOSA, SESAME, CLOVE\nMiddle: HELIOTROPE, MUGUET\nBase: MUSK, AMBER, WOODY","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("215","1","팜므","Fruity","베르사체 에로스 뿌르 팜므 타입","100","50000.00","http://oilpick.co.kr/MiniERP/oil/image/에로스(5916P).jpg","Top: BERGAMOT, GRAPEFRUIT, POMEGRANATE, CASSIS, OZONE\nMiddle: PEONY, JASMINE SAMBAC, ROSE, RASPBERRY, VIOLET\nBase: AMBER, MUSK, SANDAL, CEDAR, MOSS, PEACH","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("216","1","카르미나","Floral","크로드 카르미나 타입","100","50000.00","http://oilpick.co.kr/MiniERP/oil/image/카르미나(5906P).jpg","Top: BLACK CHERRY, SAFFRON, PINK PEPPER\nMiddle: MAY ROSE, PEONY, VIOLET\nBase: MUSK, MYRHH, FRANKINCENSE","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("217","1","사봉","Musk","시로 사봉 타입","100","47000.00","http://oilpick.co.kr/MiniERP/oil/image/사봉(6126P).jpg","Top: LEMON, APPLE\nMiddle: CASSIS, MUGUET, ROSE\nBase: MUSK, SANDAL, VANILLA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("218","1","로즈잼","Citrus","러쉬 로즈잼 타입","100","39000.00","http://oilpick.co.kr/MiniERP/oil/image/로즈잼(8364A).jpg","Top: LEMON, ROSE\nMiddle: ROSE, GERANIUM\nBase: TONKA BEAN, RASPBERRY","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("219","1","정원","Floral","안산대 시그니처향 후보1","100","38000.00","http://oilpick.co.kr/MiniERP/oil/image/GARDEN 8664A-정원.png","Top: MANDARINE, GINGER, MINT, GREEN\nMiddle: ROSE, HYACINTH, MUGUET, JASMINE\nBase: MUSK, SANDALWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("220","1","애플프루","Fruity","안산대 시그니처향 후보3","100","37000.00","http://oilpick.co.kr/MiniERP/oil/image/DAWN 8409A-애플프루.png","Top: APPLE, GREEN, ORANGE, PEAR\nMiddle: ROSE, JASMINE, VANILLA, VIOLET, FRUITY\nBase: PEACH, CEDAR, MOSS, MUSK, HELIOTROPE, CARAMEL","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("221","1","새벽정원","Green&Herb","안산대 시그니처향 후보4 & 더향 누아 향","100","36000.00","http://oilpick.co.kr/MiniERP/oil/image/DAWN 7754A-새벽정원.png","Top: GREEM BAMBOO, EUCALYPTUS, FRUITY, ORANGE\nMiddle: ROSE, MUGUET, JASMINE, OZONE\nBase: ROSE, MUGUET, JASMINE, OZONE","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("222","1","나무","Woody&Spicy","안산대 시그니처향 후보5","100","38000.00","http://oilpick.co.kr/MiniERP/oil/image/WOOD 5633A-나무.png","Top: CARDAMOM, ELEMI, BERGAMOT, GREEN TEA\nMiddle: ROSE, JASMINE, MUGUET\nBase: CEDARWOOD, SANDALWOOD, VANILLA, LEATHER, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("223","1","나무","Floral","안산대 시그니처향 후보6","100","39000.00","http://oilpick.co.kr/MiniERP/oil/image/WOOD 6532A-나무.png","Top: BERGAMOT, GREEN\nMiddle: JASMINE, VIOLET, MUGUET\nBase: CEDARWOOD, PATCHOULI, AMBER, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("224","1","목욕하는 여인","Citrus","불리 목욕하는 여인 타입","100","80000.00","http://oilpick.co.kr/MiniERP/oil/image/목욕하는-여인(7223P).jpg","Top: BERGAMOT, CITRONELLA, NEROLI, POWDERY NOTE\nMiddle: LAVENDER, VIOLET, ROSE, GERANIUM\nBase: INCENSE, PATCHOULI, IRIS","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("225","1","그로세이","Green&Herb","불리 그로세이 타입 / 안산대 시그니처향 후보2","100","40000.00","http://oilpick.co.kr/MiniERP/oil/image/GROSEILLE-4640A-그로세이.jpg","Top: TOMATO, CASSIS, LEMON, EUCALYPTUS, ROSEMARY, PEPPERMINT, MARINE, GREEN\nMiddle: JASMINE, ROSE, FREESIA\nBase: SANDALWOOD, CEDARWOOD, AMBER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("226","1","아카시아","Floral","더향 프래그런스 오일 - Acacia","100","35000.00","http://oilpick.co.kr/MiniERP/oil/image/ACACIA 1412A-아카시아.png","Top: IRIS, GRAPEFRUIT, GREEN\nMiddle: ACACIA, JASMINE, MUGUET, VIOLET, ROSE, TUBEROSE\nBase: MUSK, AMBER, HELIOTROPE, CEDAR, TONKA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("227","1","가든","Floral","더향 프래그런스 오일 - Nombre","100","40000.00","http://oilpick.co.kr/MiniERP/oil/image/GARDEN 1129A-가든.png","Top: MANDARINE, ORANGE BITTER, GINGER, MINT, GREEN\nMiddle: ROSE, HYACINTH, MUGUET, JASMINE\nBase: MUSK, SANDALWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("228","1","태싯","Citrus","더향 프래그런스 오일 - Tacit / 이솝 테싯 타입","100","41000.00","http://oilpick.co.kr/MiniERP/oil/image/TACIT 5447A-태싯.png","Top: YUZA, LEMON, GRAPEFRUIT, ORANGE, BASIL, EUCALYPTUS, MINT\nMiddle: MUGUET, ROSE, ROSEMARY, CLOVE\nBase: CEDARWOOD, SANDALWOOD, AMBER, VETIVER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("229","1","히노끼","Woody&Spicy","더향 프래그런스 오일 - Vieux /  르라보 히노끼 타입","100","43000.00","http://oilpick.co.kr/MiniERP/oil/image/HINOKI 1840A-히노끼.png","Top: BERGAMOT, AVOCADO, HINOKI\nMiddle: SANDALWOOD, CEDARWOOD, HINOKI\nBase: VETIVER, SANDALWOOD, CEDARWOOD, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("230","1","비자","Floral","더향 프래그런스 오일 - Vija","100","45000.00","http://oilpick.co.kr/MiniERP/oil/image/VIJA 1252B-비자.png","Top: GREEN, ORANGE, PEAR, MINT, VIJA\nMiddle: ROSE, JASMINE, VIOLET\nBase: CEDARWOOD, SANDALWOOD, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("231","1","상탈","Woody&Spicy","르라보 상탈33 타입","100","40000.00","http://oilpick.co.kr/MiniERP/oil/image/상탈(3797A).jpg","Top: CARDAMON, CARROT SEED, ROSEMARY, BLACK PEPPER, MELON\nMiddle: NEROLI, MUGUET, VIOLET, OLIBANUM\nBase: SANDALWOOD, CEDARWOOD, AMBER, MUSK, PATCHOULI, VANILLA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("232","1","오르페옹","Woody&Spicy","딥디크 오르페옹 타입","100","40000.00","http://oilpick.co.kr/MiniERP/oil/image/오르페옹(3984A).jpg","Top: JUNIPERBERRY\nMiddle: JASMINE, INCENSE\nBase: POWDERY, CEDARWOOD, TONKA BEAN, VANILLA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("233","1","어스","Citrus","폴로 어스 타입","100","53000.00","http://oilpick.co.kr/MiniERP/oil/image/어스(5434P).jpg","Top: NEROLI, PETITGRAIN, BERGAMOT, CITRON, PEPPERMINT\nMiddle: ORANGE BLOSSM, GERANIUM, YLANG, LAVENDER, ROSE\nBase: VETIVER, MUSK, CEDAR","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("234","1","어스","Citrus","폴로 어스 타입","100","230000.00","http://oilpick.co.kr/MiniERP/oil/image/H EARTH 3048P-어스.png","Top: NEROLI, PETITGRAIN, BERGAMOT, CITRON, PEPPERMINT\nMiddle: ORANGE BLOSSM, GERANIUM, YLANG, LAVENDER, ROSE\nBase: VETIVER, MUSK, CEDAR","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("235","1","한국","Woody&Spicy","한국적인 향","100","34000.00","http://oilpick.co.kr/MiniERP/oil/image/P KOREA 2672A-한국.png","Top: PINE, CAMPHOR, EUCALYPTUS, ORANGE, LIME, HERB\nMiddle: PINE, LIME, ROSEMARY, ROSE\nBase: MUSK, MOSS, CEDARWOOD, SANDALWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("236","1","한국","Woody&Spicy","한국적인 향 / 나드 컨셉","100","45000.00","http://oilpick.co.kr/MiniERP/oil/image/한국(6933A).jpg","Top: THYME, PINE, BLACK PEPPER\nMiddle: CEDARWOOD, VIOLET, ROSE, CLOVE\nBase: VETIVER, PATCHOULI, AMBER, SANDALWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("237","1","레몬 로즈","Floral","","100","73000.00","http://oilpick.co.kr/MiniERP/oil/image/한국(5584A).jpg","Top: LEMON, BERGAMOT, ORANGE\nMiddle: LILY, MUGUET, ROSE\nBase: MUSK, AMBER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("238","1","인레","Floral","메모 인레 타입","100","70000.00","http://oilpick.co.kr/MiniERP/oil/image/INLE 7699P-인레.png","Top: BERGAMOT, ARTEMISIA, MINT\nMiddle: OSMANTHUS, JASMINE, MATE\nBase: IRIS, MUSK, CEDARWOOD, PEACH","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("239","1","프랑킨센스","Woody&Spicy","프랑켄센스 타입","100","130000.00","http://oilpick.co.kr/MiniERP/oil/image/A FRANKINCENSE 84P-프랑킨센스.png","Top: OLIBANUM, ORANGE, LAVANDIN\nMiddle: PATCHOULI, SANDALWOOD, CINNAMON\nBase: CEDARWOOD, TONKA, VETIVER, MUSK, VANILLA, AMBER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("240","1","프랑킨센스","Woody&Spicy","프랑켄센스 타입","100","46000.00","http://oilpick.co.kr/MiniERP/oil/image/B FRANKINCENSE 844A-프랑킨센스.png","Top: OLIBANUM, ORANGE\nMiddle: PATCHOULI, SANDALWOOD, CINNAMON\nBase: CEDARWOOD, TONKA, VETIVER, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("241","1","터메릭","Woody&Spicy","안산대 시그니처향 후보 7","100","46000.00","http://oilpick.co.kr/MiniERP/oil/image/TURMERIC 7441B-터메릭.png","Top: ALDEHYDES, MYRRH, ARTEMISIA, CLOVER, BERGAMOT, GARDENIA\nMiddle: PATCHOULI, SAGE, JASMINE, CARDAMOM, ORRIS ROOT\nBase: LEATHER, OAKMOSS, MUSK, VETIVER, SANDALWOOD, AMBER, COCONUT","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("242","1","트와일라잇","Floral","","100","38000.00","http://oilpick.co.kr/MiniERP/oil/image/트와일라잇(469B).jpg","Top: LAVENDER, HONEY\nMiddle: LAVENDER, YLANG YLANG\nBase: TONKA BEAN, MUSK, BENZOIN","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("243","1","라군","Floral","에르메스 운자르뎅 수르 라 라군 타입","100","44000.00","http://oilpick.co.kr/MiniERP/oil/image/운자르뎅(3530B).jpg","Top: SEA NOTES\nMiddle: MAGNOLIA, LILY\nBase: WOODY NOTES","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("244","1","나드","Woody&Spicy","나드 컨셉","100","40000.00","http://oilpick.co.kr/MiniERP/oil/image/나드(439A).jpg","Top: AGARWOOD, SAFFRON, PEPPER, MYRRH, BIRCH\nMiddle: JASMINE, MUGUET, PETITGRAIN, COSTUS, GUAIACWOOD\nBase: AMBER, MUSK, OUDH, PATCHOULI, LABDANUM, VANILLA, TONKA BEAN","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("245","1","프랑킨센스 에센셜","Woody&Spicy","기한 : 개봉 후 1년 / 냉장보관 / 100g 발주 가능","100","1300000.00","","Top: FRANKINCENSE\nMiddle: FRANKINCENSE\nBase: FRANKINCENSE","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("246","1","우디","Woody&Spicy","안산대 PICK - 6532A 개선 샘플","100","42000.00","http://oilpick.co.kr/MiniERP/oil/image/A WOOD 1202A-우디.png","Top: BERGAMOT, GREEN, BASIL, EUCALYPTUS, MINT, YUZA\nMiddle: JASMINE, VIOLET, MUGUET, ROSEMARY, CLOVE\nBase: CEDARWOOD, PATCHOULI, AMBER, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("247","1","우디플로럴","Floral","안산대 PICK - 6532A 개선 샘플","100","40000.00","http://oilpick.co.kr/MiniERP/oil/image/B WOOD 6915A-우디플로럴.png","Top: BERGAMOT, GREEN, CLOVE\nMiddle: JASMINE, VIOLET, MUGUET\nBase: SANDALWOOD, CEDARWOOD, PATCHOULI, AMBER, MUSK, TONKA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("248","1","로즈마리","Green&Herb","","100","50000.00","http://oilpick.co.kr/MiniERP/oil/image/로즈마리(14A).jpg","Top: ROSEMARY, PINE\nMiddle: LAVENDER, TEA TREE\nBase: SAGE, MARJORAM","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("249","1","라벤더","Floral","","100","42000.00","http://oilpick.co.kr/MiniERP/oil/image/라벤더(220A).jpg","Top: ROSEMARY, PEPPERMINT\nMiddle: FREESIA, LAVENDER, LAVANDIN\nBase: MUSK, TONKA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("250","1","라벤더","Floral","","100","31000.00","http://oilpick.co.kr/MiniERP/oil/image/라벤더(95A).jpg","Top: PEACH, ROSEMARY, SPEARMINT, THYMOL\nMiddle: LAVANDIN, LAVENDER, ORANGE FLOWER, JASMINE, ROSE\nBase: AMBER, MUSK, VANILLA, VETIVER, PATCHOULI, SANDALWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("251","1","자스민","Floral","","100","30000.00","http://oilpick.co.kr/MiniERP/oil/image/자스민(142A).jpg","Top: JASMINE, PEACH, APPLE\nMiddle: JASMINE, ORANGE FLOWER, ROSE\nBase: JASMINE, MUSK, CEDARWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("252","1","자스민","Floral","","100","33000.00","http://oilpick.co.kr/MiniERP/oil/image/자스민(325A).jpg","Top: JASMINE, GREEN, SPICY\nMiddle: JASMINE, YLANG YLANG, ORANGE FLOWER\nBase: MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("253","1","라일락","Floral","","100","43000.00","http://oilpick.co.kr/MiniERP/oil/image/라일락(721A).jpg","Top: GREEN, HONEYSUKLE, GALBANUM\nMiddle: LILAC, ROSE, JASMINE, MUGUET\nBase: MUSK, AMBER, VANILLA, HELIOTROPE","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("254","1","라일락","Floral","","100","43000.00","http://oilpick.co.kr/MiniERP/oil/image/라일락(8670A).jpg","Top: ANGELICA, GALBANUM\nMiddle: LILAC, JASMINE, ORANGE BLOSSOM, HONEYSUCKLE\nBase: MUSK, AMBER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("255","1","베리 판타지","Fruity","강릉영동대 시그니처향 후보1","100","40000.00","http://oilpick.co.kr/MiniERP/oil/image/FANTASY 5578A-베리 판타지.png","Top: ACAIBERRY, CRANBERRY, BLUEBERRY, CARROT SEED\nMiddle: MIXED BERRIES, MULBERRY, ROSE\nBase: MIXED BERRIES, MULBERRY, ROSE","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("256","1","당근2","Fruity","강릉영동대 시그니처향 후보2","100","48000.00","http://oilpick.co.kr/MiniERP/oil/image/JELLY 7640A-당근2.png","Top: ORANGE, FRUITY, GREEN, CARROT SEED\nMiddle: ORANGE, FRUITY, FLORAL\nBase: VANILLA, PEACH","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("257","1","시트러스파티","Citrus","강릉영동대 시그니처향 후보3","100","42000.00","http://oilpick.co.kr/MiniERP/oil/image/CUTE 578A-시트러스파티.png","Top: MANDARIN, LIME, GRAPEFRUIT, OZONE, CARROT SEED\nMiddle: PRUNE, ROSE, JASMINE, APPLE, FREESIA, VIOLET\nBase: MUSK, CEDARWOOD, AMBER, SANDAL, CARAMEL, COCONUT","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("258","1","탐타오","Woody&Spicy","딥디크 탐타오 타입","100","40000.00","http://oilpick.co.kr/MiniERP/oil/image/탐타오(493A).jpg","Top: LIME, GINGER, CORIANDER, SANDALWOOD\nMiddle: SANDALWOOD, CEDARWOOD\nBase: MUSK, VANILLA, AMBERWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("259","1","페루","Floral","불리 페루 헬리오트로프 타입","100","65000.00","http://oilpick.co.kr/MiniERP/oil/image/페루(916B).jpg","Top: BERGAMOT\nMiddle: VIOLET, HELIOTROPE\nBase: SANDALWOOD, TONKA BEAN, MUSK, EBONY, LEATHER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("260","1","파이어플레이스","Woody&Spicy","메종마르지엘라 파이어 플레이스 타입","100","66000.00","http://oilpick.co.kr/MiniERP/oil/image/파이어플레이스(66B).jpg","Top: CLOVE, PINK PEPPER, ORANGE BLOSSOM\nMiddle: OUD, JUNIPER BERRY, NUTTY NOTES\nBase: VANILLA, BALSAM, CASHMERAN","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("261","1","오르페옹","Woody&Spicy","딥디크 오르페옹 타입","100","65000.00","http://oilpick.co.kr/MiniERP/oil/image/ORPHEON 598B-오르페옹.png","Top: JUNIPERBERRY\nMiddle: JASMINE, INCENSE\nBase: POWDERY, CEDARWOOD, TONKA BEAN, VANILLA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("262","1","오렌즈마말레이드1","Fruity","유니온픽쳐스 / 아티스트 \\","100","2024.00","","Top: / 향기 제안용\nMiddle: ORANGE, POMELO\nBase: ORANGE, ROSE","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("263","1","오렌즈마말레이드2","Fruity","유니온픽쳐스 / 아티스트 \\","100","2024.00","","Top: / 향기 제안용\nMiddle: ORANGE, POMELO, LEMON\nBase: ORANGE, ROSE","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("264","1","오렌지1","Fruity","유니온픽쳐스 / 아티스트 \\","100","2024.00","","Top: / 향기 제안용\nMiddle: ORANGE JUICE\nBase: ORANGE JUICE, FRUITY","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("265","1","오렌지2","Fruity","유니온픽쳐스 / 아티스트 \\","100","2024.00","http://oilpick.co.kr/MiniERP/oil/coa/FRESH ORANGE-7541B COA_241230-01.jpg","Top: / 향기 제안용\nMiddle: ORANGE, LEMON, MANDARIN, GRAPEFRUIT, RHUBARB\nBase: ROSE, JASMINE","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("266","1","가을 거리","Woody&Spicy","유니온픽쳐스 / 아티스트 \\","100","2024.00","http://oilpick.co.kr/MiniERP/oil/coa/FALL-5034B COA_241224-01.jpg","Top: / 향기 제안용\nMiddle: BERGAMOT, ORANGE, ELEMI, NUTMEG, TEA\nBase: VIOLET, JASMINE, MAGNOLAN, ROSE","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("267","1","가을2","Woody&Spicy","유니온픽쳐스 / 아티스트 \\","100","2024.00","http://oilpick.co.kr/MiniERP/oil/coa/AUTUMN-653B COA_241218-01.jpg","Top: / 향기 제안용\nMiddle: BERGAMOT, ROSEMARY, PEPPER\nBase: LAVENDER, CARDAMON, VIOLET","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("268","1","빈티지1","Floral","유니온픽쳐스 / 아티스트 \\","100","2024.00","","Top: / 향기 제안용\nMiddle: EUCALYPTUS, LEMON, ROSEMARY\nBase: ROSE, FREESIA, JASMINE","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("269","1","빈티지2","Citrus","유니온픽쳐스 / 아티스트 \\","100","2024.00","http://oilpick.co.kr/MiniERP/oil/coa/SECOND HAND-444B COA_250108-01.jpg","Top: / 향기 제안용\nMiddle: TANGERINE, GRAPEFRUIT\nBase: PIMENTO, NUTMEG, JASMINE, ROSEMARY","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("270","1","세드라","Woody&Spicy","조말론 인센스 앤 세드라 타입","100","42000.00","http://oilpick.co.kr/MiniERP/oil/image/세드라(433A).jpg","Top: CYPRESS, ELEMI, LABDANUM\nMiddle: PATCHOULI, OLIBANUM, GUAIACWOOD\nBase: BENZOIN, CEDAR, VETIVER, MUSK, SANDAL, AMBER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("271","1","블루","Woody&Spicy","블루드 샤넬 타입","100","71000.00","http://oilpick.co.kr/MiniERP/oil/image/BLUE 5057P-블루.png","Top: CARDAMOM, SANDALWOOD\nMiddle: GINGER, JUNIPER, GALANGA\nBase: TOBACCO BLOSSOM, GREEN LEAVES, TEAK WOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("272","1","블루드샤넬","Citrus","블루드 샤넬 타입","100","50000.00","http://oilpick.co.kr/MiniERP/oil/image/BLUE 8572P-블루드샤넬.png","Top: BERGAMOT, MANDARIN, GRAPEFRUIT, GALBANUM, WATERMELON, APPLE\nMiddle: GERANIUM, VIOLET, OLIBANUM, CARDAMON, PEPPER, ANISEED\nBase: AMBER, MUSK, SANDALWOOD, MOSS, PATCHOULI, LABDANUM","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("273","1","스포츠","Citrus","","100","70000.00","http://oilpick.co.kr/MiniERP/oil/image/SPORTS 1465P-스포츠.png","Top: MANDARIN, BERGAMOT, GRAPEFRUIT, LAVANDIN, BLACK PEPPER\nMiddle: MUGUET, JASMINE, GERANIUM, CARDAMOM\nBase: MUSK, SANDALWOOD, AMBER, PATCHOULI, VANILLA, TONKA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("274","1","푸르츠젤리","Fruity","","100","41000.00","http://oilpick.co.kr/MiniERP/oil/image/JELLY 7406A-푸르츠젤리.png","","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("275","1","마린오키드","Citrus","","100","45000.00","http://oilpick.co.kr/MiniERP/oil/image/MARINE ORCHID 69A-마린오키드.png","","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("276","1","아쿠아키스","Aqua","빅토리아 시크릿 아쿠아 키스 타입","100","45000.00","http://oilpick.co.kr/MiniERP/oil/image/AQUA KISS 533A-아쿠아키스.png","","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("277","1","퓨어","Floral","나르시소 로드리게즈 퓨어 머스크 포 허 타입","100","80000.00","http://oilpick.co.kr/MiniERP/oil/image/퓨어(1636P).jpg","Top: MUSK\nMiddle: ORANGE BLOSSOM, JASMINE, YLANG YLANG\nBase: CASHMERAN","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("278","1","셀로판","Floral","세르주루텐 뉘 드 셀로판 타입","100","200000.00","http://oilpick.co.kr/MiniERP/oil/image/CELLOPHAN 1634P-셀로판.png","","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("279","1","플로럴 우디","Woody&Spicy","","100","35000.00","http://oilpick.co.kr/MiniERP/oil/image/우디(8120A).jpg","Top: GREEN, ORANGE\nMiddle: JASMINE, ROSE, MUGUET\nBase: SANDALWOOD, CEDARWOOD, SWEET","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("280","1","풀","Green&Herb","","100","45000.00","http://oilpick.co.kr/MiniERP/oil/image/풀(7537A).jpg","Top: GREEN, GRASS, PETITGRAIN\nMiddle: GALBANUM\nBase: TONKA BEAN, WOODY","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("281","1","가든","Green&Herb","","100","40000.00","http://oilpick.co.kr/MiniERP/oil/image/가든(160A).jpg","Top: PEPPERMINT, SPEARMINT, GREEN\nMiddle: JASMINE, APPLE, PINE\nBase: MUSK, CEDARWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("282","1","꽃밭","Floral","","100","35000.00","http://oilpick.co.kr/MiniERP/oil/image/꽃집(8049A).jpg","Top: GREEN, BERGAMOT, EUCALYPTUS, PINE\nMiddle: MUGUET, ROSE, JASMINE, DAISY\nBase: CEDARWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("283","1","다우니 에이프릴 프레쉬","Floral","","100","35000.00","http://oilpick.co.kr/MiniERP/oil/image/다우니-에이프릴-프레쉬(1569A).jpg","Top: BERGAMOT, PEACH, PEAR, GREEN, CYCLAMEN\nMiddle: ROSE, JASMINE, MUGUET, VIOLET, ORANGE FLOWER\nBase: SANDALWOOD, CEDARWOOD, AMBER, VANILLA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("284","1","파인 유칼립투스","Woody&Spicy","","100","43000.00","http://oilpick.co.kr/MiniERP/oil/image/파인-유칼립투스(6584A).jpg","Top: PINE NEEDLE, CAMPHOR, GREEN, ROSEMARY, ARMOISE\nMiddle: PINE, CINNAMON\nBase: SANDAL, TONKA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("285","1","꽃","Floral","","100","37000.00","http://oilpick.co.kr/MiniERP/oil/image/꽃(8500A).jpg","Top: GREEN, BERGAMOT, EUCALYPTUS, PINE, PETITGRAIN\nMiddle: MUGUET, ROSE, JASMINE, DAISY\nBase: CEDARWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("286","1","오리엔탈","Woody&Spicy","","100","43000.00","http://oilpick.co.kr/MiniERP/oil/image/오리엔탈(5157A).jpg","Top: LEMON, BLACK PEPPER, GALBANUM, OZONE, ORANGE, LAVANDIN\nMiddle: CARDAMON, PINEAPPLE, JASMINE, VIOLET\nBase: VANILLA, CARAMEL, MUSK, AMBER, CEDAR, PATCHOULI","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("287","1","우디","Woody&Spicy","","100","41000.00","http://oilpick.co.kr/MiniERP/oil/image/우디(5273A).jpg","Top: ALDEHYDE, MANDARIN, PINE NEEDLE, ORANGE\nMiddle: LAVENDER, FREESIA, ELEMI\nBase: CEDARWOOD, SANDALWOOD, VETIVER, PATCHOULI","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("288","1","튤립","Floral","LATULIP AG - 899W GALAXOLID 개선 샘플","100","55000.00","http://oilpick.co.kr/MiniERP/oil/image/LA TULIP 1059W-튤립.jpg","Top: GREEN NOTES, CYCLAMEN, RHUBURB\nMiddle: PINK TULIP, MUGUET, FREESIA\nBase: WOODY NOTE, MUSK, MOSS","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("289","1","페이지","Woody&Spicy","PAGE-3170A IPM개선 샘플","100","28500.00","http://oilpick.co.kr/MiniERP/oil/image/PAGE 1730A-페이지.png","Top: LEMON, ORANGE, BERGAMOT, EUCALYPTUS, ROSEMARY, MINT\nMiddle: ROSE, FREESIA, PINE\nBase: CEDARWOOD, SANDALWOOD, PATCHOULI","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("290","1","휠","Woody&Spicy","이솝 휠 타입","100","46000.00","http://oilpick.co.kr/MiniERP/oil/image/이솝-휠(6710A).jpg","Top: THYME, ORANGE, CLOVE, GINGER\nMiddle: CYPRESS, GERANIUM\nBase: VETIVER, OLIBANUM, CEDARWOOD, AMBER, PATCHOULI","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("291","1","우드","Woody&Spicy","톰포드 오드우드 타입","100","42000.00","http://oilpick.co.kr/MiniERP/oil/image/우드(687B).jpg","Top: AGARWOOD, SAFFRON, PEPPER, MYRRH, BIRCH\nMiddle: JASMINE, MUGUET, PETITGRAIN, COSTUS, GUAIACWOOD\nBase: AMBER, MUSK, OUDH, PATCHOULI, LABDANUM, VANILLA, TONKA BEAN","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("292","1","멜론","Fruity","","100","35000.00","http://oilpick.co.kr/MiniERP/oil/image/멜론(1564B).jpg","Top: MELON, HONEYDEW, BANANA, GREEN\nMiddle: MELON, HONEYDEW\nBase: VANILLA CARAMEL, PEACH","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("293","1","화이트 티","Fruity","시로 화이트 티 타입","100","45000.00","http://oilpick.co.kr/MiniERP/oil/image/화이트-티(615B).png","Top: LEMON, GRAPEFRUIT\nMiddle: JASMINE, ROSE\nBase: MUSK, AMBER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("294","1","프루티 릴리","Floral","시로 화이트 릴리 타입","100","42000.00","http://oilpick.co.kr/MiniERP/oil/image/화이트 릴리 815B.png","Top: FRUITY, BERGAMOT\nMiddle: MUGUET, CASSIS, PRUNE\nBase: SANDALWOOD, MUSK, MOSS","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("295","1","영로즈","Floral","바이레도 영 로즈","100","280000.00","http://oilpick.co.kr/MiniERP/oil/image/영로즈(1643P).jpg","Top: PEPPER, AMBRETTE\nMiddle: ROSE, IRIS\nBase: MUSK, AMBER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("296","1","로즈31","Floral","르라보 로즈31 타입","100","65000.00","http://oilpick.co.kr/MiniERP/oil/image/로즈31(788P).jpg","Top: ROSE, CUMIN, BERGAMOT, MANDARIN, NUTMEG, PINK PEPPER\nMiddle: ROSE, JASMINE, CYPRESS, OLIBANUM\nBase: AMBER, MUSK, CEDARWOOD, OUD, GUAIAC WOOD, VETIVER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("297","1","베이19","Green&Herb","르라보 베이 19타입","100","66000.00","http://oilpick.co.kr/MiniERP/oil/image/베이19(592P).jpg","Top: JUNIPER BERRIES, GREEN LEAVES\nMiddle: OZONIC NOTES\nBase: PATCHOULI, MUSK, AMBROXAN","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("298","1","네롤리36","Floral","르라보 네롤리36타입","100","66000.00","http://oilpick.co.kr/MiniERP/oil/image/네롤리36(5255P).jpg","Top: ALDEHYDE, ORANGE\nMiddle: JASMINE, ORANGE BLOSSOM, ROSE\nBase: MUSK, VANILLA, TONKA BEAN","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("299","1","베티버46","Woody&Spicy","르라보 베티버 46 타입","100","75000.00","http://oilpick.co.kr/MiniERP/oil/image/베티버46(3285P).jpg","Top: BERGAMOT, PEPPER, CLOVE\nMiddle: LABDANUM\nBase: OLIBANUM, VETIVER, GUAIAC WOOD, CEDAR, AMBER, VANILLA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("300","1","발다프리크","Woody&Spicy","바이레도 발다프리크 타입","100","76000.00","http://oilpick.co.kr/MiniERP/oil/image/발다프리크(353P).jpg","Top: BERGAMOT, LEMON, ORANGE, BLACK CURRANT, TAGETE\nMiddle: VIOLET, JASMINE, NEROLI, OSMANTHUS, CARAMEL\nBase: AMBER, VETIVER, TONKA BEAN, SANDALWOOD, MUSK, CEDARWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("301","1","집시워터","Woody&Spicy","바이레도 집시워터 타입","100","85000.00","http://oilpick.co.kr/MiniERP/oil/image/집시워터(412P).jpg","Top: GREEN PEAR, VIOLET\nMiddle: MAGNOLIA, SANDALWOOD\nBase: AMBER, CEDAR, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("302","1","모하비 고스트","Woody&Spicy","바이레도 모하비 고스트 타입","100","68000.00","http://oilpick.co.kr/MiniERP/oil/image/모하비-고스트(741P).jpg","Top: GREEN PEAR, VIOLET\nMiddle: MAGNOLIA, SANDALWOOD\nBase: AMBER, CEDARWOOD, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("303","1","노맨즈 오브 로즈랜드","Floral","바이레도 노맨즈 오브 로즈랜드 타입","100","72000.00","http://oilpick.co.kr/MiniERP/oil/image/노맨즈-오브-로즈랜드(317P).jpg","Top: ROSE, RASPBERRY, PINK PEPPER\nMiddle: ROSE, CYPRESS\nBase: AMBER, OUD, CEDARWOOD, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("304","1","미르토","Aqua","아쿠아 디 파르마 미르토 타입","100","76000.00","http://oilpick.co.kr/MiniERP/oil/image/미르토(678P).jpg","Top: LEMON, MYRTLE, ORANGE\nMiddle: JUNIPERBERRY, ROSE, CASSIS\nBase: AMBER, CEDARWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("305","1","유자","Citrus","아쿠아 디 파르마 유자 타입","100","67000.00","http://oilpick.co.kr/MiniERP/oil/image/유자(798P).jpg","Top: YUZU, BERGAMOT, PEPPER\nMiddle: LOTUS, MIMOSA, VIOLET LEAF, JASMINE\nBase: MUSK, SANDALWOOD, LICORICE","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("306","1","콜로니아","Citrus","아쿠아 디 파르마 콜로니아 타입","100","80000.00","http://oilpick.co.kr/MiniERP/oil/image/콜로니아(125P).jpg","Top: BERGAMOT, ORANGE, LEMON, LITSEA, ROSEMARY\nMiddle: LAVENDER, ROSE, PETITGRAIN, YLANG, CLOVE, MUGUET\nBase: PATCHOULI, VETIVER, MOSS, TONKA, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("307","1","아란치아 디 카프리","Citrus","아쿠아 디 파르마 아란치아 디 카프리 타입","100","70000.00","","Top: ORANGE, LEMON, BERGAMOT, MANDARIN\nMiddle: PETITGRAIN, CARDAMOM\nBase: MUSK, AMBER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("308","1","라튤립","Floral","바이레도 라튤립 타입","100","40000.00","http://oilpick.co.kr/MiniERP/oil/image/라튤립(1454P).jpg","Top: GREEN, CYCLAMEN, RHUBARB\nMiddle: TULIP, MUGUET, FREESIA\nBase: MUSK, CEDARWOOD, SANDALWOOD, VETIVER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("309","1","피코 디 아말피 타입","Citrus","아쿠아 디 파르마 피코 디 아말피 타입","100","60000.00","http://oilpick.co.kr/MiniERP/oil/image/피코-디-아말피-타입(725P).jpg","Top: BERGAMOT, GRAPEFRUIT, LEMON, CITRON\nMiddle: FIG, JASMINE, PINK PEPPER\nBase: FIG TREE, CEDARWOOD, BENZOIN","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("310","1","비치워크","Aqua","메종마르지엘라 비치워크 타입","100","72000.00","http://oilpick.co.kr/MiniERP/oil/image/비치워크(2918P).jpg","Top: LEMON, PINK PEPPER, BERGAMOT, AROMATIC\nMiddle: WHITE FLORAL, NEROLI, HELIOTROPE, OZONE\nBase: SANDALWOOD, VANILLA, MUSK, AMBER, BENZOIN SIAM","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("311","1","필로시코스","Fruity","딥디크 필로시코스 타입","100","88000.00","http://oilpick.co.kr/MiniERP/oil/image/필로시코스(30P).jpg","Top: GREEN, CYPRESS, FIG, MANDARIN, GALBANUM\nMiddle: TUBEROSE, COCONUT, OSMANTHUS, CYCLAMEN, ROSE\nBase: AMBER, CEDAR, TONKA, BENZOIN","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("312","1","탐다오","Woody&Spicy","딥디크 탐다오 타입","100","73000.00","http://oilpick.co.kr/MiniERP/oil/image/탐다오(439P).jpg","Top: LIME, GINGER, CORIANDER, SANDALWOOD\nMiddle: SANDALWOOD, CEDARWOOD\nBase: MUSK, VANILLA, AMBERWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("313","1","레이지 선데이 모닝","Musk","메종 마르지엘라 레이지 선데이 모닝 타입","100","67000.00","http://oilpick.co.kr/MiniERP/oil/image/레이지-선데이-모닝(8325P).jpg","Top: ALDEHYDE, WHITE LILY, PEAR\nMiddle: ROSE, ORANGE BLOSSOM, IRIS\nBase: MUSK, AMBER, PATCHOULI","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("314","1","플레르드뽀","Floral","딥디크 플레르 드뽀 타입","100","79000.00","http://oilpick.co.kr/MiniERP/oil/image/플레르드뽀(348P).jpg","Top: ALDEHYDES, PINK PEPPER, ANGELICA, BERGAMOT\nMiddle: IRIS, MUGUET, ROSE\nBase: MUSK, AMBER, SANDALWOOD, LEATHER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("315","1","웬 더 레인 스탑스","Green&Herb","메종 마르지엘라 웬 더 레인 스탑스 타입","100","67000.00","http://oilpick.co.kr/MiniERP/oil/image/웬-더-레인-스탑스(7540P).jpg","Top: GREEN, BERGAMOT, PINK PEPPER\nMiddle: WATERY NOTES, RAIN NOTES, TUKISH ROSE, JASMINE\nBase: PINE TREE, MOSS, PATCHOULI","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("316","1","도손","Woody&Spicy","딥디크 도손 타입","100","78000.00","http://oilpick.co.kr/MiniERP/oil/image/도손(5122P).jpg","Top: TUBEROSE, PINK PEPPER, PETITGRAIN\nMiddle: ROSE, JASMINE, ORANGE FLOWER\nBase: MUSK, SANDALWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("317","1","세일링 데이","Aqua","메종 마르지엘라 세일링 데이 타입","100","65000.00","http://oilpick.co.kr/MiniERP/oil/image/세일링-데이(35P).jpg","Top: SEA NOTES, ALDEHYDES, CORIANDER, RED PEPPER\nMiddle: JUNIPER, IRIS, ROSE\nBase: AMBERGRIS, CEDAR, AMBERWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("318","1","롬브르단로","Green&Herb","딥디크 롬브르단로 타입","100","67000.00","http://oilpick.co.kr/MiniERP/oil/image/롬브르단로(373P).jpg","Top: GREEN, CASSIS, BERGAMOT, MANDARIN, CLOVE\nMiddle: ROSE, GERANIUM, NEROLI, BLACK CURRANT\nBase: MUSK, AMBER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("319","1","프랑킨센스 에센셜","Woody&Spicy","","100","1000000.00","http://oilpick.co.kr/MiniERP/oil/image/프랑킨센스-에센셜.jpg","Top: FRANKINCENSE\nMiddle: FRANKINCENSE\nBase: FRANKINCENSE","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("320","1","오로즈","Floral","딥디크 오로즈 타입","100","75000.00","http://oilpick.co.kr/MiniERP/oil/image/오로즈(33P).jpg","Top: BEGAMOT, CASSIS, PETTIGRAIN\nMiddle: MUGUET, ROSE BULGARIAN, GERANIUM, JASMINE\nBase: MUSK, SANDALWOOD, CADARWOOD, AMBER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("321","1","상탈33","Woody&Spicy","르라보 상탈 33 타입","100","65000.00","http://oilpick.co.kr/MiniERP/oil/image/상탈33(597P).jpg","Top: CARDAMON, CARROT SEED, ROSEMARY, BLACK PEPPER, MELON\nMiddle: NEROLI, MUGUET, VIOLET, OLIBANUM\nBase: SANDALWOOD, CEDARWOOD, AMBER, MUSK, PATCHOULI, VANILLA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("322","1","블루벨","Floral","조말론 와일드 블루벨 타입","100","68000.00","http://oilpick.co.kr/MiniERP/oil/image/블루벨(41P).jpg","Top: OZONIC, GREEN, MELON\nMiddle: JASMINE, NEROLI, MUGUET\nBase: MUSK, AMBER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("323","1","떼누아29","Woody&Spicy","르라보 떼누아 29 타입","100","67000.00","http://oilpick.co.kr/MiniERP/oil/image/떼누아29(323P).jpg","Top: BERGAMOT, BAY LEAF, FIG\nMiddle: JASMINE, ROSE, HAY, CEDARWOOD\nBase: PATCHOULI, CEDARWOOD, AMBER, OLIBANUM, VETIVER, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("324","1","얼그레이&큐컴버","Green&Herb","조말론 얼그레이 & 큐컴버 타입","100","66000.00","http://oilpick.co.kr/MiniERP/oil/image/얼그레이&큐컴버(582P).jpg","Top: BERGAMOT, GRAPEFRUIT, ORANGE, CUCUMBER, GREEN TEA\nMiddle: JASMINE, VIOLET, ROSE, ANGELICA\nBase: MUSK, AMBER, CEDARWOOD, VANILLA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("325","1","어나더13","Woody&Spicy","르라보 어나더 13 타입","100","100000.00","http://oilpick.co.kr/MiniERP/oil/image/어나더13(5330P).jpg","Top: PEAR, APPLE, BERGAMOT\nMiddle: AMBER, JASMINE, MUGUET\nBase: MUSK, AMBER, MOSS","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("326","1","우드세이지&씨솔트","Woody&Spicy","조말론 우드세이지&씨솔트 타입","100","73000.00","http://oilpick.co.kr/MiniERP/oil/image/우드세이지&씨솔트(7149P).jpg","Top: BERGAMOT, GRAPEFRUIT, CLARY SAGE, MANDARIN, GREEN\nMiddle: SAGE, ORANGE FLOWER, VIOLET, MUGUET, SEA WEED\nBase: PATCHOULI, AMBER, MUSK, CEDARWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("327","1","베르가못22","Citrus","르라보 베르가못 22 타입","100","52000.00","http://oilpick.co.kr/MiniERP/oil/image/베르가못22(51P).jpg","Top: BERGAMOT, LEMON, GRAPEFRUIT, ALDEHYDIC\nMiddle: ORANGE BLOSSOM, PINE, PETITGRAIN\nBase: MUSK, AMBER, CEDARWOOD, VETIVER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("328","1","블랙베리&베이","Fruity","조말론 블랙베리&베이 타입","100","57000.00","http://oilpick.co.kr/MiniERP/oil/image/블랙베리&베이(314P).jpg","Top: CASSIS, CITRUS, GREEN, BAY LEAF, BLACKBERRY, PEAR\nMiddle: WHITE LILY, JASMINE\nBase: MUSK, SANDALWOOD, VETIVER, CEDARWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("329","1","넥타린블라썸&허니","Floral","조말론 넥타린블라썬&허니 타입","100","40000.00","http://oilpick.co.kr/MiniERP/oil/image/넥타린블라썸&허니(786P).jpg","Top: PETITGRAIN, LEMON, GREEN, BLACK CURRANT, NECTARINE\nMiddle: LILY, WHITE FLORAL, REDCURRANT, PLUM\nBase: PEACH, MUSK, VETIVER, CEDARWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("330","1","옐로우 히비스커스","Floral","조말론 옐로우 히브스커스 타입","100","69000.00","http://oilpick.co.kr/MiniERP/oil/image/옐로우-히비스커스(375P).jpg","Top: LIME, LEMON\nMiddle: JASMIN SAMBAC, ROSE\nBase: MUSK, BENZOIN","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("331","1","피오니&블러쉬 스웨이드","Floral","조말론 피오니 & 블러쉬 스웨이드 타입","100","56000.00","http://oilpick.co.kr/MiniERP/oil/image/피오니&블러쉬-스웨이드(12P).jpg","Top: APPEL, LEMON, CASSIS, HONEY\nMiddle: PEONY, ROSE, JASMINE, VIOLET, CARNATION\nBase: MUSK, AMBER, PATCHOULI, VANILLA, SUEDE","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("332","1","블랙시더&주니퍼","Woody&Spicy","조말론 블랙시더&주니퍼 타입","100","72000.00","http://oilpick.co.kr/MiniERP/oil/image/블랙시더&주니퍼(58P).jpg","Top: CUMIN, CEDARWOOD, SICHUAN PEPPER, CITRUS, LABDANUM\nMiddle: JUNIPER BERRY, ROSE, YLANG YLANG, CLAY SAGE, NUTMEG\nBase: MUSK, SANDALWOOD, AMBER, VANILLA, CEDARWOOD, MOSS","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("333","1","오렌지 블러썸","Floral","조말론 오렌지블라썸 타입","100","65000.00","http://oilpick.co.kr/MiniERP/oil/image/오렌지블러썸.jpg","Top: JASMINE, YLANG YLANG, GREEN NOTES\nMiddle: JASMINE, YLANG YLANG, ROSE, ORANGE FLOWER\nBase: PATCHOULI, VETIVER, PEACH, SANDAL, MUSK, MOSS","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("334","1","인센스&세드랏","Woody&Spicy","조말론 인센스&세드랏 타입","100","69000.00","http://oilpick.co.kr/MiniERP/oil/image/인센스&세드랏(511P).jpg","Top: CYPRESS, ELEMI, CITRUS LABDANUM\nMiddle: PATCHOULI, OLIBANUM, GUAIACWOOD\nBase: BENZOIN, CEDAR, VETIVER, MUSK, SANDAL, AMBER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("335","1","슈퍼시더","Woody&Spicy","바이레도 슈퍼시더 타입","100","90000.00","http://oilpick.co.kr/MiniERP/oil/image/슈퍼시더(5106P).jpg","Top: ROSE\nMiddle: VIRGINIAN CEDAR\nBase: VETIVER, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("336","1","코코느와르","Woody&Spicy","샤넬 코코 느와르 타입","100","65000.00","http://oilpick.co.kr/MiniERP/oil/image/코코느와르(5355P).jpg","Top: BERGAMOT, LEMON ITALIAN, GRAPEFRUIT, TAGETE\nMiddle: NARCISSE, FRUITY, ROSE, PATCHOULI, GERAMIUM, JASMINE\nBase: TONKA BEAN, OLIBANUM, SANDAL, MUSK, CIVET, VANILLA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("337","1","NO.5","Floral","샤넬 NO.5 타입","100","68000.00","http://oilpick.co.kr/MiniERP/oil/image/NO.5(9P).jpg","Top: ALDEHYDE, LEMON, BERGAMOT, YUZU\nMiddle: JASMINE, ROSE, YLANG YLANG, IRIS, MUGUET\nBase: MUSK, AMBER, PATCHOULI, SANDALWOOD, TONKA, VANILLA, MOSS","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("338","1","샹스오땅드르","Floral","샤넬 샹스 오 땅드르","100","77000.00","http://oilpick.co.kr/MiniERP/oil/image/샹스오땅드르(7153P).jpg","Top: QUINCE, GRAPEFRUIT\nMiddle: ROSE, JASMINE\nBase: WHITE MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("339","1","블루드샤넬","Citrus","블루드샤넬 타입","100","66000.00","http://oilpick.co.kr/MiniERP/oil/image/블루드샤넬(523P).jpg","Top: BERGAMOT, PETITGRAIN, BLACK PEPPER, MANDARIN, ORANGE, GRAPEFRUIT\nMiddle: ANISEED, PATCHOULI, NEROLI, NUTMEG, APPLE, JASMINE\nBase: AMBER, VETIVER, MOSS, LABDANUM, SANDALWOOD, CEDARWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("340","1","소바쥬","Woody&Spicy","디올 소바쥬 타입","100","63000.00","http://oilpick.co.kr/MiniERP/oil/image/소바쥬(946P).jpg","Top: BERGAMOT, GALBANUM, MANDARIN, AROMATIC\nMiddle: LAVENDER, OZONIC, GERANIUM, ELEMI, WHITE FLORAL\nBase: AMBER, CISTUS LABDANUM, TONKA, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("341","1","블루밍 부케","Floral","디올 블루밍 부케 타입","100","77000.00","http://oilpick.co.kr/MiniERP/oil/image/블루밍-부케(654P).jpg","Top: MANDARIN, BERGAMOT, MUGUET, GREEN, HERBAL\nMiddle: ROSE, JASMINE, MUGUET, DEWBERRY, ORANGE FLOWER, PEONY\nBase: MUSK, PATCHOULI, AMBER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("342","1","옴므","Woody&Spicy","디올 옴므 타입","100","60000.00","http://oilpick.co.kr/MiniERP/oil/image/옴므(3515P).jpg","Top: IRIS, ORANGE\nMiddle: LEATHER, ROSE\nBase: SANDALWOOD, AMBRETTE, AGARWOOD, CEDARWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("343","1","미스","Floral","미스 디올 타입","100","67000.00","http://oilpick.co.kr/MiniERP/oil/image/미스(5181P).jpg","Top: MANDARIN, BERGAMOT, ORANGE, PEAR, PINK PEPPER\nMiddle: ROSE, JASMINE, MUGUET, YLANG YLANG\nBase: PATCHOULI, CEDARWOOD, SANDALWOOD, VANILLA, AMBER, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("344","1","SPA1","Green&Herb","","100","47000.00","http://oilpick.co.kr/MiniERP/oil/image/SPA1(840A).jpg","Top: ORANGE, BLACK CURRANT, PETITGRAIN\nMiddle: LAVENDER, ORANGE BLOSSOM,\nBase: MUSK, VANILLA, AMBER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("345","1","SPA2","Green&Herb","호텔스파 컨셉 추천 샘플","100","47000.00","http://oilpick.co.kr/MiniERP/oil/image/SPA2(841A).jpg","Top: ORANGE, BERGAMOT\nMiddle: ROSE, JASMINE, YLANG YLANG,\nBase: MUSK, PATCHOULI, VANILLA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("346","1","SPA3","Green&Herb","호텔스파 컨셉 추천 샘플","100","47000.00","http://oilpick.co.kr/MiniERP/oil/image/SPA3(843A).jpg","Top: CHERRY, PLUM\nMiddle: ROSE, JASMINE, CLOVE\nBase: MUSK, WOODY, VANILLA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("347","1","GOLF1","Green&Herb","골프장 컨셉 추천 샘플","100","47000.00","http://oilpick.co.kr/MiniERP/oil/image/GOLF1(405A).jpg","Top: HINOKI\nMiddle: HINOKI\nBase: CEDARWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("348","1","GOLF2","Green&Herb","골프장 컨셉 추천 샘플","100","47000.00","http://oilpick.co.kr/MiniERP/oil/image/GOLF2(616A).jpg","Top: LEMON, APPLE\nMiddle: ROSE, JASMINE, BAMBOO\nBase: MUSK, CEDARWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("349","1","GOLF3","Green&Herb","골프장 컨셉 추천 샘플","100","47000.00","http://oilpick.co.kr/MiniERP/oil/image/GOLF3(55A).jpg","Top: ARMOISE, EUCALYPTOL, LIME, MINT\nMiddle: ROSE, JASMINE, MUGUET\nBase: MUSK, WOODY, AMBER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("350","1","원추리","Floral","원추리 꽃 컨셉 추천 샘플","100","47000.00","http://oilpick.co.kr/MiniERP/oil/image/AT YELLOW 539A-원추리.png","Top: GREEN NOTES\nMiddle: LILY, LILY OF THE VALLEY, RAPE BLOSSOM\nBase: MUSK, WOODY","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("351","1","씨솔트유자","Citrus","박앤웍스 공급","100","46000.00","","","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("352","1","프레쉬맨","Musk","박앤웍스 공급","100","57000.00","","","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("353","1","라임바질만다린","Citrus","박앤웍스 공급","100","100000.00","","","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("354","1","화이트쟈스민민트","Green&Herb","박앤웍스 공급","100","79000.00","","","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("355","1","다크체리","Fruity","","100","38000.00","http://oilpick.co.kr/MiniERP/oil/image/DARK CHERRY 1199A-다크체리.png","","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("356","1","블랙체리2","Fruity","","100","43000.00","http://oilpick.co.kr/MiniERP/oil/image/BLACK CHERRY 869A-블랙체리2.png","","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("357","1","로즈","Floral","","100","40000.00","http://oilpick.co.kr/MiniERP/oil/image/ROSE EVELYN 1526B-로즈.png","Top: ROSE, GREEN, FRUITY\nMiddle: ROSE, GERANIUM, MUGUET\nBase: SANDALWOOD, AMBER, MUSK, CEDARWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("358","1","퓨어 시덕션","Fruity","빅토리아시크릿 퓨어 시덕션 타입","100","42000.00","http://oilpick.co.kr/MiniERP/oil/image/퓨어-시덕션(1509B).jpg.png","Top: LEMON, ORANGE, MELON, GREEN, APPLE, BANANA\nMiddle: JASMINE, MUGUET, STRAWBERRY, PLUM\nBase: MUSK, CARAMEL, PEACH","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("359","1","머스크","Musk","","100","35000.00","http://oilpick.co.kr/MiniERP/oil/image/머스크(515B).jpg","Top: ALDEHYDE, ROSE\nMiddle: MUSK, ROSE, MUGUET, POWDERY\nBase: MUSK, SANDALWOOD, VANILLA, TONKA, CEDARWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("360","1","플로럴","Floral","","100","46000.00","http://oilpick.co.kr/MiniERP/oil/image/플로랄(6159B).jpg.png","Top: GREEN, MANDARIN, PINK PEPPER, CASSIS\nMiddle: ROSE, JASMINE, MUGUET, FREESIA, VIOLET, YLANG YLANG\nBase: MUSK, SANDALWOOD, AMBER, VANILLA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("361","1","비누","Musk","","100","42000.00","http://oilpick.co.kr/MiniERP/oil/image/비누(8147B).jpg.png","Top: ALDEHYDE, GERANIUM, ROSE\nMiddle: MUGUET, ROSE, JASMINE, YLANG YLANG\nBase: MUSK, CEDARWOOD, SANDALWOOD, PATCHOULI, VANILLA, TONKA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("362","1","플로라 고저스","Floral","구찌 플로라 고저스 가드니아 타입","100","43000.00","http://oilpick.co.kr/MiniERP/oil/image/플로라-고저스(5162B).jpg.png","Top: PEAR, MANDARIN, PRUNE\nMiddle: GARDENIA, JASMINE\nBase: MUSK, SANDALWOOD, PATCHOULI","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("363","1","이돌 르 파워크림","Floral","랑콤 이돌 르 파워크림타입","100","45000.00","http://oilpick.co.kr/MiniERP/oil/image/이돌 르 파워크림(1565B).jpg","Top: GREEN, BERGAMOT, PEAR\nMiddle: ROSE, JASMINE, CASSIS\nBase: MUSK, AMBER, VANILLA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("364","1","썬키","Fruity","","100","48000.00","http://oilpick.co.kr/MiniERP/oil/image/썬키(359B).jpg.png","Top: MANDARIN, BERGAMOT, CASSIS\nMiddle: JASMINE, ROSE\nBase: MUSK, AMBER, SANDALWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("365","1","프리지아","Floral","","100","45000.00","http://oilpick.co.kr/MiniERP/oil/image/프리지아(7160B).jpg.png","Top: GREEN, FREESIA, APPLE\nMiddle: JASMINE, FREESIA, MUGUET, ROSE\nBase: MUSK, SANDALWOOD","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("366","1","클로에","Floral","클로에 컨셉 추천샘플","100","68000.00","","","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("367","1","아디나","Floral","아디나 컨셉 추천 샘플","100","60000.00","","","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("368","1","마커스","Woody&Spicy","마커스 컨셉 추천 샘플","100","75000.00","","","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("369","1","츠바메","Floral","츠바메 컨셉 추천 샘플","100","70000.00","","","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("370","1","화이트 자스민&민트","Floral","박앤웍스 공급","100","36000.00","","","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("371","1","라임 바질&만다린","Citrus","박앤웍스 공급","100","36000.00","","","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("372","1","프렌치 라벤더","Floral","","100","37000.00","http://oilpick.co.kr/MiniERP/oil/image/프렌치 라벤더(2370A).jpg.png","Top: LAVENDER, LAVANDIN\nMiddle: PINEAPPLE, ROSE, MUGUET\nBase: MOSS, PATCHOULI, TONKA, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("373","1","프레시맨","Citrus","박앤웍스 공급","100","37000.00","","","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("374","1","씨솔트유자","Citrus","박앤웍스 공급","100","38000.00","","","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("375","1","씨솔트유자","Citrus","박앤웍스 공급","100","38000.00","","","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("376","1","블랑쉬","Floral","바이레도 블랑쉬 타입","100","62000.00","http://oilpick.co.kr/MiniERP/oil/image/블랑쉬 29P.png","Top: ALDEHYDE, PINK PEPPER, OZONE, CYCLAMEN\nMiddle: ROSE, MAGNOLIA, PEONY, VIOLET, JASMINE, MUGUET\nBase: MUSK, SANDALWOOD, VANILLA","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("377","1","옴브레 레더","Woody&Spicy","톰포드 옴브레 레더 타입","100","42000.00","http://oilpick.co.kr/MiniERP/oil/image/OMBRE LEATHER-1583P_대표이미지.jpg","Top: PINK PEPPER, CARDAMOM, SAFFRON, CORIANDER, RASPBERRY\nMiddle: JASMINE, ORANGE FLOWER, ORRIS, SUEDE, VIOLET, LEATHER\nBase: AMBER, MOSS, PATCHOULI, VANILLA, CEDARWOOD, MUSK","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("378","1","제로","Woody&Spicy","꼼데가르송 제로 타입","100","42000.00","http://oilpick.co.kr/MiniERP/oil/image/ZERO-5846P_대표이미지.jpg","Top: BERGAMOT, APPLE, GRAPEFRUIT\nMiddle: GERANIUM, ROSE, CORIANDER\nBase: AMBER, MUSK, CEDARWOOD, VETIVER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("379","1","길티블랙","Woody&Spicy","구찌 길티블랙 맨 타입","100","43000.00","http://oilpick.co.kr/MiniERP/oil/image/GUILTY-3524P_대표이미지.jpg","Top: PINK PEPPER, BERGAMOT, GREEN, LAVENDER, MANDARIN\nMiddle: JASMINE, ORANGE BLOSSOM, JUNIPER BERRY, CARDAMOM\nBase: MUSK, SANDALWOOD, CEDARWOOD, PATCHOULI, AMBER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("380","1","J 숲타입","Woody&Spicy","","100","33000.00","http://oilpick.co.kr/MiniERP/oil/image/J FOREST-250826AF_대표이미지.jpg","Top: EUCALYPTUS, PINE, BERGAMOT\nMiddle: CEDARWOOD, PATCHOULI, CINNAMON\nBase: CEDARWOOD, VETIVER, TONKA BEAN","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("381","1","레인 포레스트","Green&Herb","","100","31000.00","http://oilpick.co.kr/MiniERP/oil/image/RAIN FOREST-250826AF_대표이미지.jpg","Top: GREEN CYCLAMEN, PINEAPPLE, OZONE\nMiddle: JASMINE, VIOLET, LILY OF THE VALLEY, ROSE, HYACINTH\nBase: AMBER, CEDARWOOD, PEACH","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("382","1","클로브","Citrus","","100","46000.00","http://oilpick.co.kr/MiniERP/oil/image/CLOVE 9321P-클로브.png","Top: BERGAMOT, GRAPEFRUIT, POMEGRANATE, CASSIS, OZONE\nMiddle: PEONY, JASMINE SAMBAC, ROSE, RASPBERRY, VIOLET\nBase: AMBER, MUSK, SANDAL, CEDAR, MOSS, PEACH","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("383","1","라벤더","Floral","","100","40000.00","http://oilpick.co.kr/MiniERP/oil/image/LAVNEDER 4593A-라벤더.png","Top: LAVENDER, LAVANDIN, EUCALYPTUS\nMiddle: LAVENDER, LAVANDIN\nBase: LAVENDER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("384","1","아티산","Citrus","","100","75000.00","http://oilpick.co.kr/MiniERP/oil/image/ARTISAN 7330P-아티산.png","Top: ORANGE, LEMON, BERGAMOT, CLEMENTINE, TANGERINE,\nMiddle: ORANGE BLOSSOM, JASMINE, MUGUET, LAVENDER\nBase: AMBER, MUSK, WOOD, MOSS","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("385","1","SM FREESIA 250915P 노벨라 프리지아","Floral","","100","50000.00","http://oilpick.co.kr/MiniERP/oil/image/SM FREESIA-250915P-노벨라 프리지아.jpg","","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("386","1","OSMANTHUS 250915 금목서 타입","Floral","","100","57000.00","http://oilpick.co.kr/MiniERP/oil/image/OSMANTHUS-250915P- 금목서 타입.jpg","","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("387","1","MAISON SEVEN 250915P 커정 724","Floral","","100","54000.00","http://oilpick.co.kr/MiniERP/oil/image/MAISON SEVEN-250915P- 커정 724.jpg","","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("388","1","밤쉘","Floral","","100","48000.00","http://oilpick.co.kr//MiniERP/oil/image/458.jpg","","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("389","1","","","","100","0.00","","","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("390","1","","","","100","0.00","","","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("391","1","화이트티","Floral","지속방출형","100","32000.00","","","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("392","1","001맨","Woody&Spicy","로에베 001맨 타입","100","48000.00","http://oilpick.co.kr/MiniERP/oil/image/001맨(3165B).jpg.png","Top: BERGAMOT, MANDARIN, CARROT SEED\nMiddle: JASMINE, ROSE, CARDAMOM, GINGER\nBase: PATCHOULI, VETIVER, AMBER","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("393","1","","","","100","0.00","","","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("394","1","ㅇㅇㅇ","","","100","0.00","","","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("395","1","ㅇㅇ","","","100","0.00","","","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("396","1","ㅇㅇ","","","100","0.00","","","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("397","1","ㅇㅇ","","","100","0.00","","","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("398","1","포레스트 베일","Floral","","100","35000.00","","Top: Eucalyptol, Methyl Salicylate, Linalool\nMiddle: Hedione, Geraniol, Phenethyl Alcohol\nBase: Iso E Super, Cedarwood, Benzyl Benzoate","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("399","1","화이트티","Floral","지속방출형","100","30000.00","","","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");
INSERT INTO `scents` VALUES("400","1","피치 타입","Fruity","","100","35000.00","","","0","0","1","100","0","","2025-11-12 12:50:58","2025-11-12 12:50:58","1","");

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '설정 ID (PK)',
  `setting_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '설정 키 (고유, 예: commission_rate, monthly_fee)',
  `setting_value` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '설정 값',
  `setting_type` enum('STRING','INTEGER','FLOAT','BOOLEAN','JSON') COLLATE utf8mb4_unicode_ci DEFAULT 'STRING' COMMENT '설정 값 타입',
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '설정 설명',
  `is_editable` tinyint(1) DEFAULT 1 COMMENT '수정 가능 여부',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '수정일시',
  `updated_by` int(11) DEFAULT NULL COMMENT '수정자 user_id',
  PRIMARY KEY (`setting_id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='시스템 설정(Setting) 테이블 - 시스템 전역 설정 값';


DROP TABLE IF EXISTS `settlements`;
CREATE TABLE `settlements` (
  `settlement_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '정산 ID (PK)',
  `settlement_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '정산 번호 (고유, 예: SET-2025-0001)',
  `settlement_type` enum('VENDOR','SALES_REP','LUCID') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '정산 대상 타입 (VENDOR: 밴더, SALES_REP: 영업사원, LUCID: 루시드)',
  `target_user_id` int(11) DEFAULT NULL COMMENT '정산 대상 user_id (FK -> users)',
  `target_vendor_id` int(11) DEFAULT NULL COMMENT '정산 대상 vendor_id (FK -> vendors, 밴더인 경우)',
  `settlement_month` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '정산 월 (YYYY-MM 형식)',
  `total_sales` decimal(10,2) DEFAULT 0.00 COMMENT '총 매출 (원)',
  `commission_amount` decimal(10,2) DEFAULT 0.00 COMMENT '커미션 금액 (원)',
  `incentive_amount` decimal(10,2) DEFAULT 0.00 COMMENT '인센티브 금액 (원)',
  `total_amount` decimal(10,2) NOT NULL COMMENT '총 정산 금액 (원)',
  `status` enum('PENDING','CALCULATED','PAID','CANCELLED') COLLATE utf8mb4_unicode_ci DEFAULT 'PENDING' COMMENT '정산 상태 (PENDING: 대기, CALCULATED: 계산완료, PAID: 지급완료, CANCELLED: 취소)',
  `calculated_date` date DEFAULT NULL COMMENT '정산 계산일',
  `payment_date` date DEFAULT NULL COMMENT '정산 지급일',
  `bank_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '지급 은행',
  `account_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '계좌번호',
  `account_holder` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '예금주',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '정산 메모',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '수정일시',
  `created_by` int(11) DEFAULT NULL COMMENT '생성자 user_id',
  `updated_by` int(11) DEFAULT NULL COMMENT '수정자 user_id',
  PRIMARY KEY (`settlement_id`),
  UNIQUE KEY `settlement_number` (`settlement_number`),
  KEY `idx_target_user` (`target_user_id`),
  KEY `idx_target_vendor` (`target_vendor_id`),
  KEY `idx_type` (`settlement_type`),
  KEY `idx_month` (`settlement_month`),
  KEY `idx_status` (`status`),
  KEY `idx_settlement_type_month` (`settlement_type`,`settlement_month`),
  CONSTRAINT `settlements_ibfk_1` FOREIGN KEY (`target_user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `settlements_ibfk_2` FOREIGN KEY (`target_vendor_id`) REFERENCES `vendors` (`vendor_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='정산(Settlement) 테이블 - 밴더/영업사원/루시드 정산 정보';


DROP TABLE IF EXISTS `shipment_items`;
CREATE TABLE `shipment_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '배송 항목 ID (PK)',
  `shipment_id` int(11) NOT NULL COMMENT '배송 ID (FK -> shipments)',
  `work_order_item_id` int(11) NOT NULL COMMENT '작업지시서 항목 ID (FK -> work_order_items)',
  `quantity` int(11) NOT NULL DEFAULT 1 COMMENT '배송 수량',
  `serial_id` int(11) DEFAULT NULL COMMENT '디스펜서 시리얼 ID (FK -> device_serials, 기기인 경우)',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  PRIMARY KEY (`item_id`),
  KEY `idx_shipment` (`shipment_id`),
  KEY `idx_work_order_item` (`work_order_item_id`),
  KEY `idx_serial` (`serial_id`),
  CONSTRAINT `shipment_items_ibfk_1` FOREIGN KEY (`shipment_id`) REFERENCES `shipments` (`shipment_id`) ON DELETE CASCADE,
  CONSTRAINT `shipment_items_ibfk_2` FOREIGN KEY (`work_order_item_id`) REFERENCES `work_order_items` (`item_id`),
  CONSTRAINT `shipment_items_ibfk_3` FOREIGN KEY (`serial_id`) REFERENCES `device_serials` (`serial_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='배송 항목(Shipment Item) 테이블 - 배송에 포함된 상품 항목 상세';


DROP TABLE IF EXISTS `shipments`;
CREATE TABLE `shipments` (
  `shipment_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '배송 ID (PK)',
  `work_order_id` int(11) NOT NULL COMMENT '작업지시서 ID (FK -> work_orders)',
  `shipment_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '배송 번호 (고유, 예: SHP-2025-0001)',
  `customer_id` int(11) NOT NULL COMMENT '고객 ID (FK -> customers)',
  `site_id` int(11) DEFAULT NULL COMMENT '배송 현장 ID (FK -> customer_sites)',
  `recipient_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '수령인 이름',
  `recipient_phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '수령인 연락처',
  `shipping_address` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '배송 주소',
  `courier_company` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '택배사',
  `tracking_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '송장번호',
  `shipped_date` datetime DEFAULT NULL COMMENT '출고 일시',
  `delivered_date` datetime DEFAULT NULL COMMENT '배송 완료 일시',
  `status` enum('PENDING','SHIPPED','IN_TRANSIT','DELIVERED','FAILED','RETURNED') COLLATE utf8mb4_unicode_ci DEFAULT 'PENDING' COMMENT '배송 상태 (PENDING: 출고 대기, SHIPPED: 출고됨, IN_TRANSIT: 배송중, DELIVERED: 배송 완료, FAILED: 배송 실패, RETURNED: 반송)',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '배송 메모',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '수정일시',
  `created_by` int(11) DEFAULT NULL COMMENT '생성자 user_id',
  `updated_by` int(11) DEFAULT NULL COMMENT '수정자 user_id',
  PRIMARY KEY (`shipment_id`),
  UNIQUE KEY `shipment_number` (`shipment_number`),
  KEY `idx_work_order` (`work_order_id`),
  KEY `idx_customer` (`customer_id`),
  KEY `idx_site` (`site_id`),
  KEY `idx_status` (`status`),
  KEY `idx_shipped_date` (`shipped_date`),
  CONSTRAINT `shipments_ibfk_1` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`work_order_id`),
  CONSTRAINT `shipments_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  CONSTRAINT `shipments_ibfk_3` FOREIGN KEY (`site_id`) REFERENCES `customer_sites` (`site_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='출고/배송(Shipment) 테이블 - 작업지시서 기반 출고 및 배송 정보';

INSERT INTO `shipments` VALUES("1","1","SHIP-001","1","1","김매니저","010-1111-1111","서울시 강남구 테헤란로 123","CJ대한통운","1234567890","2025-11-12 00:00:00","","PENDING","","2025-11-12 02:25:19","2025-11-12 02:25:19","","");
INSERT INTO `shipments` VALUES("2","2","SHIP-002","2","2","이실장","010-2222-2222","부산시 해운대구 해운대로 456","로젠택배","9876543210","2025-11-12 00:00:00","","SHIPPED","","2025-11-12 02:25:19","2025-11-12 02:25:19","","");
INSERT INTO `shipments` VALUES("3","3","SHIP-003","3","3","박팀장","010-3333-3333","대구시 수성구 수성로 789","한진택배","","2025-11-12 00:00:00","","PENDING","","2025-11-12 02:25:19","2025-11-12 02:25:19","","");

DROP TABLE IF EXISTS `subscription_cycles`;
CREATE TABLE `subscription_cycles` (
  `cycle_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '주기 ID (PK)',
  `subscription_id` int(11) NOT NULL COMMENT '구독 ID (FK -> subscriptions)',
  `cycle_number` int(11) NOT NULL COMMENT '주기 회차 (1회차, 2회차 ...)',
  `cycle_start_date` date NOT NULL COMMENT '주기 시작일',
  `cycle_end_date` date NOT NULL COMMENT '주기 종료일',
  `shipment_due_date` date DEFAULT NULL COMMENT '배송 예정일',
  `status` enum('PENDING','PROCESSING','SHIPPED','COMPLETED','SKIPPED') COLLATE utf8mb4_unicode_ci DEFAULT 'PENDING' COMMENT '주기 상태 (PENDING: 대기, PROCESSING: 처리중, SHIPPED: 배송됨, COMPLETED: 완료, SKIPPED: 건너뜀)',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '수정일시',
  PRIMARY KEY (`cycle_id`),
  KEY `idx_subscription` (`subscription_id`),
  KEY `idx_cycle_start` (`cycle_start_date`),
  KEY `idx_status` (`status`),
  KEY `idx_cycle_subscription_status` (`subscription_id`,`status`),
  CONSTRAINT `subscription_cycles_ibfk_1` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`subscription_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='구독 주기(Subscription Cycle) 테이블 - 2개월마다 반복되는 구독 주기 정보';

INSERT INTO `subscription_cycles` VALUES("1","1","1","2024-02-01","2024-03-31","2024-02-01","COMPLETED","2025-11-10 08:28:30","2025-11-10 08:28:30");
INSERT INTO `subscription_cycles` VALUES("2","1","2","2024-04-01","2024-05-31","2024-04-01","COMPLETED","2025-11-10 08:28:30","2025-11-10 08:28:30");
INSERT INTO `subscription_cycles` VALUES("3","1","3","2024-06-01","2024-07-31","2024-06-01","COMPLETED","2025-11-10 08:28:30","2025-11-10 08:28:30");
INSERT INTO `subscription_cycles` VALUES("4","1","4","2024-08-01","2024-09-30","2024-08-01","COMPLETED","2025-11-10 08:28:30","2025-11-10 08:28:30");
INSERT INTO `subscription_cycles` VALUES("5","1","5","2024-10-01","2024-11-30","2024-10-01","COMPLETED","2025-11-10 08:28:30","2025-11-10 08:28:30");
INSERT INTO `subscription_cycles` VALUES("6","1","6","2024-12-01","2025-01-31","2024-12-01","PENDING","2025-11-10 08:28:30","2025-11-10 08:28:30");
INSERT INTO `subscription_cycles` VALUES("7","2","1","2024-02-01","2024-03-31","2024-02-01","COMPLETED","2025-11-10 08:28:30","2025-11-10 08:28:30");
INSERT INTO `subscription_cycles` VALUES("8","2","2","2024-04-01","2024-05-31","2024-04-01","COMPLETED","2025-11-10 08:28:30","2025-11-10 08:28:30");
INSERT INTO `subscription_cycles` VALUES("9","2","3","2024-06-01","2024-07-31","2024-06-01","COMPLETED","2025-11-10 08:28:30","2025-11-10 08:28:30");
INSERT INTO `subscription_cycles` VALUES("10","2","4","2024-08-01","2024-09-30","2024-08-01","COMPLETED","2025-11-10 08:28:30","2025-11-10 08:28:30");
INSERT INTO `subscription_cycles` VALUES("11","2","5","2024-10-01","2024-11-30","2024-10-01","COMPLETED","2025-11-10 08:28:30","2025-11-10 08:28:30");
INSERT INTO `subscription_cycles` VALUES("12","2","6","2024-12-01","2025-01-31","2024-12-01","PENDING","2025-11-10 08:28:30","2025-11-10 08:28:30");
INSERT INTO `subscription_cycles` VALUES("13","3","1","2024-02-05","2024-04-04","2024-02-05","COMPLETED","2025-11-10 08:28:30","2025-11-10 08:28:30");
INSERT INTO `subscription_cycles` VALUES("14","3","2","2024-04-05","2024-06-04","2024-04-05","COMPLETED","2025-11-10 08:28:30","2025-11-10 08:28:30");
INSERT INTO `subscription_cycles` VALUES("15","3","3","2024-06-05","2024-08-04","2024-06-05","COMPLETED","2025-11-10 08:28:30","2025-11-10 08:28:30");
INSERT INTO `subscription_cycles` VALUES("16","3","4","2024-08-05","2024-10-04","2024-08-05","COMPLETED","2025-11-10 08:28:30","2025-11-10 08:28:30");
INSERT INTO `subscription_cycles` VALUES("17","3","5","2024-10-05","2024-12-04","2024-10-05","COMPLETED","2025-11-10 08:28:30","2025-11-10 08:28:30");
INSERT INTO `subscription_cycles` VALUES("18","3","6","2024-12-05","2025-02-04","2024-12-05","PENDING","2025-11-10 08:28:30","2025-11-10 08:28:30");
INSERT INTO `subscription_cycles` VALUES("19","4","1","2024-02-05","2024-04-04","2024-02-05","COMPLETED","2025-11-10 08:28:30","2025-11-10 08:28:30");
INSERT INTO `subscription_cycles` VALUES("20","4","2","2024-04-05","2024-06-04","2024-04-05","COMPLETED","2025-11-10 08:28:30","2025-11-10 08:28:30");
INSERT INTO `subscription_cycles` VALUES("21","4","3","2024-06-05","2024-08-04","2024-06-05","COMPLETED","2025-11-10 08:28:30","2025-11-10 08:28:30");
INSERT INTO `subscription_cycles` VALUES("22","4","4","2024-08-05","2024-10-04","2024-08-05","COMPLETED","2025-11-10 08:28:30","2025-11-10 08:28:30");
INSERT INTO `subscription_cycles` VALUES("23","4","5","2024-10-05","2024-12-04","2024-10-05","COMPLETED","2025-11-10 08:28:30","2025-11-10 08:28:30");
INSERT INTO `subscription_cycles` VALUES("24","4","6","2024-12-05","2025-02-04","2024-12-05","PENDING","2025-11-10 08:28:30","2025-11-10 08:28:30");
INSERT INTO `subscription_cycles` VALUES("25","5","1","2024-02-10","2024-04-09","2024-02-10","COMPLETED","2025-11-10 08:28:30","2025-11-10 08:28:30");
INSERT INTO `subscription_cycles` VALUES("26","5","2","2024-04-10","2024-06-09","2024-04-10","COMPLETED","2025-11-10 08:28:30","2025-11-10 08:28:30");
INSERT INTO `subscription_cycles` VALUES("27","5","3","2024-06-10","2024-08-09","2024-06-10","COMPLETED","2025-11-10 08:28:30","2025-11-10 08:28:30");
INSERT INTO `subscription_cycles` VALUES("28","5","4","2024-08-10","2024-10-09","2024-08-10","COMPLETED","2025-11-10 08:28:30","2025-11-10 08:28:30");
INSERT INTO `subscription_cycles` VALUES("29","5","5","2024-10-10","2024-12-09","2024-10-10","COMPLETED","2025-11-10 08:28:30","2025-11-10 08:28:30");
INSERT INTO `subscription_cycles` VALUES("30","5","6","2024-12-10","2025-02-09","2024-12-10","PENDING","2025-11-10 08:28:30","2025-11-10 08:28:30");
INSERT INTO `subscription_cycles` VALUES("31","1","1","2024-02-01","2024-03-31","2024-02-01","COMPLETED","2025-11-10 08:29:18","2025-11-10 08:29:18");
INSERT INTO `subscription_cycles` VALUES("32","1","2","2024-04-01","2024-05-31","2024-04-01","COMPLETED","2025-11-10 08:29:18","2025-11-10 08:29:18");
INSERT INTO `subscription_cycles` VALUES("33","1","3","2024-06-01","2024-07-31","2024-06-01","COMPLETED","2025-11-10 08:29:18","2025-11-10 08:29:18");
INSERT INTO `subscription_cycles` VALUES("34","1","4","2024-08-01","2024-09-30","2024-08-01","COMPLETED","2025-11-10 08:29:18","2025-11-10 08:29:18");
INSERT INTO `subscription_cycles` VALUES("35","1","5","2024-10-01","2024-11-30","2024-10-01","COMPLETED","2025-11-10 08:29:18","2025-11-10 08:29:18");
INSERT INTO `subscription_cycles` VALUES("36","1","6","2024-12-01","2025-01-31","2024-12-01","PENDING","2025-11-10 08:29:18","2025-11-10 08:29:18");
INSERT INTO `subscription_cycles` VALUES("37","2","1","2024-02-01","2024-03-31","2024-02-01","COMPLETED","2025-11-10 08:29:18","2025-11-10 08:29:18");
INSERT INTO `subscription_cycles` VALUES("38","2","2","2024-04-01","2024-05-31","2024-04-01","COMPLETED","2025-11-10 08:29:18","2025-11-10 08:29:18");
INSERT INTO `subscription_cycles` VALUES("39","2","3","2024-06-01","2024-07-31","2024-06-01","COMPLETED","2025-11-10 08:29:18","2025-11-10 08:29:18");
INSERT INTO `subscription_cycles` VALUES("40","2","4","2024-08-01","2024-09-30","2024-08-01","COMPLETED","2025-11-10 08:29:18","2025-11-10 08:29:18");
INSERT INTO `subscription_cycles` VALUES("41","2","5","2024-10-01","2024-11-30","2024-10-01","COMPLETED","2025-11-10 08:29:18","2025-11-10 08:29:18");
INSERT INTO `subscription_cycles` VALUES("42","2","6","2024-12-01","2025-01-31","2024-12-01","PENDING","2025-11-10 08:29:18","2025-11-10 08:29:18");
INSERT INTO `subscription_cycles` VALUES("43","3","1","2024-02-05","2024-04-04","2024-02-05","COMPLETED","2025-11-10 08:29:18","2025-11-10 08:29:18");
INSERT INTO `subscription_cycles` VALUES("44","3","2","2024-04-05","2024-06-04","2024-04-05","COMPLETED","2025-11-10 08:29:18","2025-11-10 08:29:18");
INSERT INTO `subscription_cycles` VALUES("45","3","3","2024-06-05","2024-08-04","2024-06-05","COMPLETED","2025-11-10 08:29:18","2025-11-10 08:29:18");
INSERT INTO `subscription_cycles` VALUES("46","3","4","2024-08-05","2024-10-04","2024-08-05","COMPLETED","2025-11-10 08:29:18","2025-11-10 08:29:18");
INSERT INTO `subscription_cycles` VALUES("47","3","5","2024-10-05","2024-12-04","2024-10-05","COMPLETED","2025-11-10 08:29:18","2025-11-10 08:29:18");
INSERT INTO `subscription_cycles` VALUES("48","3","6","2024-12-05","2025-02-04","2024-12-05","PENDING","2025-11-10 08:29:18","2025-11-10 08:29:18");
INSERT INTO `subscription_cycles` VALUES("49","4","1","2024-02-05","2024-04-04","2024-02-05","COMPLETED","2025-11-10 08:29:18","2025-11-10 08:29:18");
INSERT INTO `subscription_cycles` VALUES("50","4","2","2024-04-05","2024-06-04","2024-04-05","COMPLETED","2025-11-10 08:29:18","2025-11-10 08:29:18");
INSERT INTO `subscription_cycles` VALUES("51","4","3","2024-06-05","2024-08-04","2024-06-05","COMPLETED","2025-11-10 08:29:18","2025-11-10 08:29:18");
INSERT INTO `subscription_cycles` VALUES("52","4","4","2024-08-05","2024-10-04","2024-08-05","COMPLETED","2025-11-10 08:29:18","2025-11-10 08:29:18");
INSERT INTO `subscription_cycles` VALUES("53","4","5","2024-10-05","2024-12-04","2024-10-05","COMPLETED","2025-11-10 08:29:18","2025-11-10 08:29:18");
INSERT INTO `subscription_cycles` VALUES("54","4","6","2024-12-05","2025-02-04","2024-12-05","PENDING","2025-11-10 08:29:18","2025-11-10 08:29:18");
INSERT INTO `subscription_cycles` VALUES("55","5","1","2024-02-10","2024-04-09","2024-02-10","COMPLETED","2025-11-10 08:29:18","2025-11-10 08:29:18");
INSERT INTO `subscription_cycles` VALUES("56","5","2","2024-04-10","2024-06-09","2024-04-10","COMPLETED","2025-11-10 08:29:18","2025-11-10 08:29:18");
INSERT INTO `subscription_cycles` VALUES("57","5","3","2024-06-10","2024-08-09","2024-06-10","COMPLETED","2025-11-10 08:29:18","2025-11-10 08:29:18");
INSERT INTO `subscription_cycles` VALUES("58","5","4","2024-08-10","2024-10-09","2024-08-10","COMPLETED","2025-11-10 08:29:18","2025-11-10 08:29:18");
INSERT INTO `subscription_cycles` VALUES("59","5","5","2024-10-10","2024-12-09","2024-10-10","COMPLETED","2025-11-10 08:29:18","2025-11-10 08:29:18");
INSERT INTO `subscription_cycles` VALUES("60","5","6","2024-12-10","2025-02-09","2024-12-10","PENDING","2025-11-10 08:29:18","2025-11-10 08:29:18");

DROP TABLE IF EXISTS `subscription_items`;
CREATE TABLE `subscription_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '구독 항목 ID (PK)',
  `subscription_id` int(11) NOT NULL COMMENT '구독 ID (FK -> subscriptions)',
  `item_type` enum('DEVICE','SCENT','CONTENT','PART','ACCESSORY') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '항목 타입 (DEVICE: 디스펜서, SCENT: 향, CONTENT: 콘텐츠, PART: 부자재, ACCESSORY: 악세사리)',
  `item_id_ref` int(11) NOT NULL COMMENT '항목 참조 ID (해당 상품 테이블의 PK)',
  `quantity` int(11) DEFAULT 1 COMMENT '수량',
  `is_recurring` tinyint(1) DEFAULT 1 COMMENT '정기 배송 여부 (TRUE: 주기마다 자동, FALSE: 1회만)',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  PRIMARY KEY (`item_id`),
  KEY `idx_subscription` (`subscription_id`),
  KEY `idx_item_ref` (`item_type`,`item_id_ref`),
  CONSTRAINT `subscription_items_ibfk_1` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`subscription_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=161 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='구독 항목(Subscription Item) 테이블 - 구독에 포함된 상품 항목';

INSERT INTO `subscription_items` VALUES("1","1","SCENT","1","1","1","2025-11-10 08:28:29");
INSERT INTO `subscription_items` VALUES("2","1","CONTENT","1","1","1","2025-11-10 08:28:29");
INSERT INTO `subscription_items` VALUES("3","2","SCENT","2","1","1","2025-11-10 08:28:29");
INSERT INTO `subscription_items` VALUES("4","2","CONTENT","2","1","1","2025-11-10 08:28:29");
INSERT INTO `subscription_items` VALUES("5","3","SCENT","3","1","1","2025-11-10 08:28:29");
INSERT INTO `subscription_items` VALUES("6","3","CONTENT","3","1","1","2025-11-10 08:28:29");
INSERT INTO `subscription_items` VALUES("7","4","SCENT","11","1","1","2025-11-10 08:28:29");
INSERT INTO `subscription_items` VALUES("8","4","CONTENT","6","1","1","2025-11-10 08:28:29");
INSERT INTO `subscription_items` VALUES("9","5","SCENT","12","1","1","2025-11-10 08:28:29");
INSERT INTO `subscription_items` VALUES("10","5","CONTENT","7","1","1","2025-11-10 08:28:29");
INSERT INTO `subscription_items` VALUES("11","6","SCENT","4","1","1","2025-11-10 08:28:29");
INSERT INTO `subscription_items` VALUES("12","6","CONTENT","11","1","1","2025-11-10 08:28:29");
INSERT INTO `subscription_items` VALUES("13","7","SCENT","5","1","1","2025-11-10 08:28:29");
INSERT INTO `subscription_items` VALUES("14","7","CONTENT","12","1","1","2025-11-10 08:28:29");
INSERT INTO `subscription_items` VALUES("15","8","SCENT","13","1","1","2025-11-10 08:28:29");
INSERT INTO `subscription_items` VALUES("16","8","CONTENT","16","1","1","2025-11-10 08:28:29");
INSERT INTO `subscription_items` VALUES("17","9","SCENT","14","1","1","2025-11-10 08:28:29");
INSERT INTO `subscription_items` VALUES("18","9","CONTENT","21","1","1","2025-11-10 08:28:29");
INSERT INTO `subscription_items` VALUES("19","10","SCENT","15","1","1","2025-11-10 08:28:29");
INSERT INTO `subscription_items` VALUES("20","10","CONTENT","22","1","1","2025-11-10 08:28:29");
INSERT INTO `subscription_items` VALUES("21","11","SCENT","6","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("22","11","CONTENT","26","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("23","12","SCENT","7","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("24","12","CONTENT","27","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("25","13","SCENT","16","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("26","13","CONTENT","4","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("27","14","SCENT","17","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("28","14","CONTENT","5","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("29","15","SCENT","18","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("30","15","CONTENT","8","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("31","16","SCENT","8","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("32","16","CONTENT","9","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("33","17","SCENT","9","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("34","17","CONTENT","10","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("35","18","SCENT","19","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("36","18","CONTENT","13","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("37","19","SCENT","20","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("38","19","CONTENT","14","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("39","20","SCENT","21","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("40","20","CONTENT","15","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("41","21","SCENT","10","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("42","21","CONTENT","17","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("43","22","SCENT","22","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("44","22","CONTENT","18","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("45","23","SCENT","23","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("46","23","CONTENT","19","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("47","24","SCENT","24","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("48","24","CONTENT","20","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("49","25","SCENT","25","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("50","25","CONTENT","23","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("51","26","SCENT","26","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("52","26","CONTENT","24","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("53","27","SCENT","27","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("54","27","CONTENT","25","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("55","28","SCENT","28","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("56","28","CONTENT","28","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("57","29","SCENT","29","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("58","29","CONTENT","29","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("59","30","SCENT","30","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("60","30","CONTENT","30","1","1","2025-11-10 08:28:30");
INSERT INTO `subscription_items` VALUES("61","1","SCENT","1","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("62","1","CONTENT","1","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("63","2","SCENT","2","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("64","2","CONTENT","2","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("65","3","SCENT","3","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("66","3","CONTENT","3","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("67","4","SCENT","11","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("68","4","CONTENT","6","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("69","5","SCENT","12","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("70","5","CONTENT","7","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("71","6","SCENT","4","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("72","6","CONTENT","11","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("73","7","SCENT","5","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("74","7","CONTENT","12","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("75","8","SCENT","13","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("76","8","CONTENT","16","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("77","9","SCENT","14","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("78","9","CONTENT","21","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("79","10","SCENT","15","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("80","10","CONTENT","22","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("81","11","SCENT","6","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("82","11","CONTENT","26","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("83","12","SCENT","7","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("84","12","CONTENT","27","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("85","13","SCENT","16","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("86","13","CONTENT","4","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("87","14","SCENT","17","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("88","14","CONTENT","5","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("89","15","SCENT","18","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("90","15","CONTENT","8","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("91","16","SCENT","8","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("92","16","CONTENT","9","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("93","17","SCENT","9","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("94","17","CONTENT","10","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("95","18","SCENT","19","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("96","18","CONTENT","13","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("97","19","SCENT","20","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("98","19","CONTENT","14","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("99","20","SCENT","21","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("100","20","CONTENT","15","1","1","2025-11-10 08:28:57");
INSERT INTO `subscription_items` VALUES("101","1","SCENT","1","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("102","1","CONTENT","1","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("103","2","SCENT","2","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("104","2","CONTENT","2","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("105","3","SCENT","3","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("106","3","CONTENT","3","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("107","4","SCENT","11","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("108","4","CONTENT","6","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("109","5","SCENT","12","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("110","5","CONTENT","7","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("111","6","SCENT","4","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("112","6","CONTENT","11","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("113","7","SCENT","5","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("114","7","CONTENT","12","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("115","8","SCENT","13","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("116","8","CONTENT","16","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("117","9","SCENT","14","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("118","9","CONTENT","21","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("119","10","SCENT","15","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("120","10","CONTENT","22","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("121","11","SCENT","6","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("122","11","CONTENT","26","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("123","12","SCENT","7","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("124","12","CONTENT","27","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("125","13","SCENT","16","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("126","13","CONTENT","4","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("127","14","SCENT","17","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("128","14","CONTENT","5","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("129","15","SCENT","18","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("130","15","CONTENT","8","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("131","16","SCENT","8","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("132","16","CONTENT","9","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("133","17","SCENT","9","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("134","17","CONTENT","10","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("135","18","SCENT","19","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("136","18","CONTENT","13","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("137","19","SCENT","20","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("138","19","CONTENT","14","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("139","20","SCENT","21","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("140","20","CONTENT","15","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("141","21","SCENT","10","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("142","21","CONTENT","17","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("143","22","SCENT","22","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("144","22","CONTENT","18","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("145","23","SCENT","23","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("146","23","CONTENT","19","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("147","24","SCENT","24","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("148","24","CONTENT","20","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("149","25","SCENT","25","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("150","25","CONTENT","23","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("151","26","SCENT","26","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("152","26","CONTENT","24","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("153","27","SCENT","27","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("154","27","CONTENT","25","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("155","28","SCENT","28","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("156","28","CONTENT","28","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("157","29","SCENT","29","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("158","29","CONTENT","29","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("159","30","SCENT","30","1","1","2025-11-10 08:29:18");
INSERT INTO `subscription_items` VALUES("160","30","CONTENT","30","1","1","2025-11-10 08:29:18");

DROP TABLE IF EXISTS `subscriptions`;
CREATE TABLE `subscriptions` (
  `subscription_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '구독 ID (PK)',
  `customer_id` int(11) NOT NULL COMMENT '고객 ID (FK -> customers)',
  `site_id` int(11) DEFAULT NULL COMMENT '설치 현장 ID (FK -> customer_sites)',
  `subscription_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '구독 번호 (고유, 예: SUB-2025-0001)',
  `start_date` date NOT NULL COMMENT '구독 시작일',
  `end_date` date DEFAULT NULL COMMENT '구독 종료일 (NULL: 무기한)',
  `status` enum('ACTIVE','PAUSED','CANCELLED','EXPIRED') COLLATE utf8mb4_unicode_ci DEFAULT 'ACTIVE' COMMENT '구독 상태 (ACTIVE: 진행중, PAUSED: 일시정지, CANCELLED: 해지, EXPIRED: 만료)',
  `monthly_fee` decimal(10,2) DEFAULT 29700.00 COMMENT '월 구독료 (원, 기본 29,700)',
  `cycle_months` tinyint(4) DEFAULT 2 COMMENT '배송 주기 (개월, 기본 2개월)',
  `billing_day` tinyint(4) DEFAULT 1 COMMENT '결제일 (1~31일)',
  `next_cycle_date` date DEFAULT NULL COMMENT '다음 배송 예정일',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '구독 메모',
  `deleted_at` datetime DEFAULT NULL COMMENT '삭제일시 (Soft Delete)',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '수정일시',
  `created_by` int(11) DEFAULT NULL COMMENT '생성자 user_id',
  `updated_by` int(11) DEFAULT NULL COMMENT '수정자 user_id',
  PRIMARY KEY (`subscription_id`),
  UNIQUE KEY `subscription_number` (`subscription_number`),
  KEY `idx_customer` (`customer_id`),
  KEY `idx_site` (`site_id`),
  KEY `idx_status` (`status`),
  KEY `idx_next_cycle` (`next_cycle_date`),
  KEY `idx_deleted` (`deleted_at`),
  KEY `idx_subscription_customer_status` (`customer_id`,`status`),
  CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  CONSTRAINT `subscriptions_ibfk_2` FOREIGN KEY (`site_id`) REFERENCES `customer_sites` (`site_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='구독(Subscription) 테이블 - 고객의 구독 서비스 마스터 정보';

INSERT INTO `subscriptions` VALUES("1","1","1","SUB-2024-0001","2024-02-01","2025-01-31","ACTIVE","29700.00","2","1","2025-12-01","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `subscriptions` VALUES("2","1","2","SUB-2024-0002","2024-02-01","2025-01-31","ACTIVE","29700.00","2","1","2025-12-01","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `subscriptions` VALUES("3","1","3","SUB-2024-0003","2024-02-05","2025-02-04","ACTIVE","29700.00","2","5","2025-12-05","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `subscriptions` VALUES("4","1","4","SUB-2024-0004","2024-02-05","2025-02-04","ACTIVE","29700.00","2","5","2025-12-05","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `subscriptions` VALUES("5","1","5","SUB-2024-0005","2024-02-10","2025-02-09","ACTIVE","29700.00","2","10","2025-12-10","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `subscriptions` VALUES("6","2","6","SUB-2024-0006","2024-02-15","2025-02-14","ACTIVE","29700.00","2","15","2025-12-15","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `subscriptions` VALUES("7","2","7","SUB-2024-0007","2024-02-15","2025-02-14","ACTIVE","29700.00","2","15","2025-12-15","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `subscriptions` VALUES("8","2","8","SUB-2024-0008","2024-02-20","2025-02-19","ACTIVE","29700.00","2","20","2025-12-20","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `subscriptions` VALUES("9","3","9","SUB-2024-0009","2024-02-25","2025-02-24","ACTIVE","29700.00","2","25","2025-12-25","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `subscriptions` VALUES("10","3","10","SUB-2024-0010","2024-02-25","2025-02-24","ACTIVE","29700.00","2","25","2025-12-25","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `subscriptions` VALUES("11","3","11","SUB-2024-0011","2024-03-01","2025-02-28","ACTIVE","29700.00","2","1","2025-11-01","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `subscriptions` VALUES("12","3","12","SUB-2024-0012","2024-03-01","2025-02-28","ACTIVE","29700.00","2","1","2025-11-01","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","7","0");
INSERT INTO `subscriptions` VALUES("13","4","13","SUB-2024-0013","2024-03-05","2025-03-04","ACTIVE","29700.00","2","5","2025-11-05","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","8","0");
INSERT INTO `subscriptions` VALUES("14","4","14","SUB-2024-0014","2024-03-05","2025-03-04","ACTIVE","29700.00","2","5","2025-11-05","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","8","0");
INSERT INTO `subscriptions` VALUES("15","4","15","SUB-2024-0015","2024-03-10","2025-03-09","ACTIVE","29700.00","2","10","2025-11-10","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","8","0");
INSERT INTO `subscriptions` VALUES("16","5","16","SUB-2024-0016","2024-03-15","2025-03-14","ACTIVE","29700.00","2","15","2025-11-15","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","8","0");
INSERT INTO `subscriptions` VALUES("17","5","17","SUB-2024-0017","2024-03-15","2025-03-14","ACTIVE","29700.00","2","15","2025-11-15","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","8","0");
INSERT INTO `subscriptions` VALUES("18","6","18","SUB-2024-0018","2024-03-20","2025-03-19","ACTIVE","29700.00","2","20","2025-11-20","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","8","0");
INSERT INTO `subscriptions` VALUES("19","6","19","SUB-2024-0019","2024-03-20","2025-03-19","ACTIVE","29700.00","2","20","2025-11-20","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","8","0");
INSERT INTO `subscriptions` VALUES("20","6","20","SUB-2024-0020","2024-03-25","2025-03-24","ACTIVE","29700.00","2","25","2025-11-25","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","8","0");
INSERT INTO `subscriptions` VALUES("21","7","21","SUB-2024-0021","2024-04-01","2025-03-31","ACTIVE","29700.00","2","1","2025-12-01","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","9","0");
INSERT INTO `subscriptions` VALUES("22","7","22","SUB-2024-0022","2024-04-01","2025-03-31","ACTIVE","29700.00","2","1","2025-12-01","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","9","0");
INSERT INTO `subscriptions` VALUES("23","8","23","SUB-2024-0023","2024-04-05","2025-04-04","ACTIVE","29700.00","2","5","2025-12-05","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","9","0");
INSERT INTO `subscriptions` VALUES("24","8","24","SUB-2024-0024","2024-04-05","2025-04-04","ACTIVE","29700.00","2","5","2025-12-05","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","9","0");
INSERT INTO `subscriptions` VALUES("25","9","25","SUB-2024-0025","2024-04-10","2025-04-09","ACTIVE","29700.00","2","10","2025-12-10","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","9","0");
INSERT INTO `subscriptions` VALUES("26","9","26","SUB-2024-0026","2024-04-10","2025-04-09","ACTIVE","29700.00","2","10","2025-12-10","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","9","0");
INSERT INTO `subscriptions` VALUES("27","9","27","SUB-2024-0027","2024-04-15","2025-04-14","ACTIVE","29700.00","2","15","2025-12-15","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","9","0");
INSERT INTO `subscriptions` VALUES("28","10","28","SUB-2024-0028","2024-04-20","2025-04-19","ACTIVE","29700.00","2","20","2025-12-20","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","10","0");
INSERT INTO `subscriptions` VALUES("29","10","29","SUB-2024-0029","2024-04-20","2025-04-19","ACTIVE","29700.00","2","20","2025-12-20","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","10","0");
INSERT INTO `subscriptions` VALUES("30","11","30","SUB-2024-0030","2024-04-25","2025-04-24","ACTIVE","29700.00","2","25","2025-12-25","","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","10","0");
INSERT INTO `subscriptions` VALUES("34","31","","SUB-2024-001","2024-10-01","2025-12-31","ACTIVE","500000.00","12","1","","","","2024-10-01 00:00:00","2025-11-12 02:13:47","","");
INSERT INTO `subscriptions` VALUES("35","32","","SUB-2024-002","2024-10-15","2025-11-30","ACTIVE","300000.00","12","1","","","","2024-10-15 00:00:00","2025-11-12 02:13:47","","");
INSERT INTO `subscriptions` VALUES("36","33","","SUB-2024-003","2024-11-01","2026-01-31","ACTIVE","400000.00","14","1","","","","2024-11-01 00:00:00","2025-11-12 02:13:47","","");
INSERT INTO `subscriptions` VALUES("37","34","","SUB-2024-004","2024-09-01","2025-12-31","ACTIVE","350000.00","12","1","","","","2024-09-01 00:00:00","2025-11-12 02:13:47","","");
INSERT INTO `subscriptions` VALUES("38","35","","SUB-2024-005","2024-08-01","2025-10-31","ACTIVE","450000.00","12","1","","","","2024-08-01 00:00:00","2025-11-12 02:13:47","","");
INSERT INTO `subscriptions` VALUES("39","31","","SUB-001","2024-10-01","2025-12-31","ACTIVE","500000.00","12","1","","","","2024-10-01 00:00:00","2025-11-12 02:14:24","","");
INSERT INTO `subscriptions` VALUES("40","32","","SUB-002","2024-10-15","2025-11-30","ACTIVE","300000.00","12","1","","","","2024-10-15 00:00:00","2025-11-12 02:14:24","","");
INSERT INTO `subscriptions` VALUES("41","33","","SUB-003","2024-11-01","2026-01-31","ACTIVE","400000.00","14","1","","","","2024-11-01 00:00:00","2025-11-12 02:14:24","","");

DROP TABLE IF EXISTS `tag_map`;
CREATE TABLE `tag_map` (
  `map_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '매핑 ID (PK)',
  `tag_id` int(11) NOT NULL COMMENT '태그 ID (FK -> tags)',
  `entity_type` enum('CONTENT','SCENT','DEVICE','PART','ACCESSORY') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '엔티티 타입 (CONTENT: 콘텐츠, SCENT: 향, DEVICE: 기기, PART: 부자재, ACCESSORY: 악세사리)',
  `entity_id` int(11) NOT NULL COMMENT '엔티티 ID (해당 상품 테이블의 PK)',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  PRIMARY KEY (`map_id`),
  UNIQUE KEY `unique_tag_entity` (`tag_id`,`entity_type`,`entity_id`),
  KEY `idx_entity` (`entity_type`,`entity_id`),
  CONSTRAINT `tag_map_ibfk_1` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`tag_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='태그 매핑(Tag Map) 테이블 - 태그와 상품 간 다대다 관계 매핑';

INSERT INTO `tag_map` VALUES("1","1","SCENT","1","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("2","11","SCENT","1","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("3","16","SCENT","1","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("4","20","SCENT","1","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("5","1","SCENT","2","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("6","13","SCENT","2","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("7","17","SCENT","2","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("8","2","SCENT","3","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("9","15","SCENT","3","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("10","17","SCENT","3","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("11","4","SCENT","6","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("12","11","SCENT","6","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("13","18","SCENT","6","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("14","2","SCENT","5","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("15","12","SCENT","5","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("16","17","SCENT","5","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("17","3","SCENT","7","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("18","13","SCENT","7","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("19","20","SCENT","7","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("20","6","SCENT","21","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("21","11","SCENT","21","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("22","16","SCENT","21","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("23","1","SCENT","22","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("24","12","SCENT","22","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("25","16","SCENT","22","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("26","5","SCENT","23","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("27","19","SCENT","23","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("28","2","SCENT","30","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("29","11","SCENT","30","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("30","19","SCENT","30","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("31","1","CONTENT","1","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("32","20","CONTENT","1","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("33","25","CONTENT","1","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("34","6","CONTENT","2","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("35","28","CONTENT","2","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("36","9","CONTENT","6","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("37","22","CONTENT","6","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("38","27","CONTENT","6","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("39","2","CONTENT","5","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("40","24","CONTENT","5","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("41","29","CONTENT","5","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("42","3","CONTENT","10","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("43","22","CONTENT","10","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("44","30","CONTENT","10","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("45","4","CONTENT","21","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("46","26","CONTENT","21","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("47","1","CONTENT","26","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("48","22","CONTENT","26","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("49","29","CONTENT","26","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("50","7","CONTENT","27","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("51","25","CONTENT","27","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("52","8","CONTENT","28","2025-11-10 08:31:17");
INSERT INTO `tag_map` VALUES("53","21","CONTENT","28","2025-11-10 08:31:17");

DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags` (
  `tag_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '태그 ID (PK)',
  `tag_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '태그명 (중복 불가)',
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'URL 슬러그',
  `tag_type` enum('CONTENT','SCENT','DEVICE','GENERAL') COLLATE utf8mb4_unicode_ci DEFAULT 'GENERAL' COMMENT '태그 유형 (CONTENT: 콘텐츠, SCENT: 향, DEVICE: 기기, GENERAL: 공통)',
  `color` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '태그 색상 (HEX, 예: #FF5733)',
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '태그 설명',
  `usage_count` int(11) DEFAULT 0 COMMENT '사용 횟수 (캐시용)',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '활성 상태',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '수정일시',
  PRIMARY KEY (`tag_id`),
  UNIQUE KEY `tag_name` (`tag_name`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_type` (`tag_type`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='태그(Tag) 테이블 - 상품 메타데이터 태그 마스터';

INSERT INTO `tags` VALUES("1","신상품","new-arrival","GENERAL","#FF6B6B","신규 출시 상품","0","1","2025-11-10 08:27:56","2025-11-10 08:27:56");
INSERT INTO `tags` VALUES("2","베스트셀러","best-seller","GENERAL","#4ECDC4","인기 상품","0","1","2025-11-10 08:27:56","2025-11-10 08:27:56");
INSERT INTO `tags` VALUES("3","한정판","limited-edition","GENERAL","#95E1D3","기간 한정","0","1","2025-11-10 08:27:56","2025-11-10 08:27:56");
INSERT INTO `tags` VALUES("4","프리미엄","premium","GENERAL","#F38181","고급 제품","0","1","2025-11-10 08:27:56","2025-11-10 08:27:56");
INSERT INTO `tags` VALUES("5","가성비","value","GENERAL","#AA96DA","가성비 좋은","0","1","2025-11-10 08:27:56","2025-11-10 08:27:56");
INSERT INTO `tags` VALUES("6","추천","recommended","GENERAL","#FCBAD3","추천 제품","0","1","2025-11-10 08:27:56","2025-11-10 08:27:56");
INSERT INTO `tags` VALUES("7","인기","popular","GENERAL","#FFFFD2","인기 제품","0","1","2025-11-10 08:27:56","2025-11-10 08:27:56");
INSERT INTO `tags` VALUES("8","세트상품","bundle","GENERAL","#A8D8EA","세트 구성","0","1","2025-11-10 08:27:56","2025-11-10 08:27:56");
INSERT INTO `tags` VALUES("9","이벤트","event","GENERAL","#FFD166","이벤트 상품","0","1","2025-11-10 08:27:56","2025-11-10 08:27:56");
INSERT INTO `tags` VALUES("10","품절임박","low-stock","GENERAL","#EF476F","재고 부족","0","1","2025-11-10 08:27:56","2025-11-10 08:27:56");
INSERT INTO `tags` VALUES("11","친환경","eco-friendly","SCENT","#06D6A0","천연 성분","0","1","2025-11-10 08:27:56","2025-11-10 08:27:56");
INSERT INTO `tags` VALUES("12","알러지프리","allergen-free","SCENT","#118AB2","알러지 유발 물질 없음","0","1","2025-11-10 08:27:56","2025-11-10 08:27:56");
INSERT INTO `tags` VALUES("13","강추천","highly-recommended","SCENT","#073B4C","강력 추천 향","0","1","2025-11-10 08:27:56","2025-11-10 08:27:56");
INSERT INTO `tags` VALUES("14","은은한","subtle","SCENT","#FFD6BA","은은한 향","0","1","2025-11-10 08:27:56","2025-11-10 08:27:56");
INSERT INTO `tags` VALUES("15","진한","strong","SCENT","#E76F51","진한 향","0","1","2025-11-10 08:27:56","2025-11-10 08:27:56");
INSERT INTO `tags` VALUES("16","상쾌한","refreshing","SCENT","#2A9D8F","상쾌한 느낌","0","1","2025-11-10 08:27:56","2025-11-10 08:27:56");
INSERT INTO `tags` VALUES("17","편안한","relaxing","SCENT","#264653","편안한 느낌","0","1","2025-11-10 08:27:56","2025-11-10 08:27:56");
INSERT INTO `tags` VALUES("18","고급스러운","luxurious","SCENT","#E9C46A","고급스러운 향","0","1","2025-11-10 08:27:56","2025-11-10 08:27:56");
INSERT INTO `tags` VALUES("19","자연향","natural","SCENT","#F4A261","자연 그대로","0","1","2025-11-10 08:27:56","2025-11-10 08:27:56");
INSERT INTO `tags` VALUES("20","계절향","seasonal","SCENT","#E76F51","계절 특별 향","0","1","2025-11-10 08:27:56","2025-11-10 08:27:56");
INSERT INTO `tags` VALUES("21","미니멀","minimal","CONTENT","#000000","미니멀 디자인","0","1","2025-11-10 08:27:56","2025-11-10 08:27:56");
INSERT INTO `tags` VALUES("22","컬러풀","colorful","CONTENT","#FF6B6B","화려한 색상","0","1","2025-11-10 08:27:56","2025-11-10 08:27:56");
INSERT INTO `tags` VALUES("23","모던","modern","CONTENT","#4ECDC4","현대적 디자인","0","1","2025-11-10 08:27:56","2025-11-10 08:27:56");
INSERT INTO `tags` VALUES("24","클래식","classic","CONTENT","#556B2F","클래식 스타일","0","1","2025-11-10 08:27:56","2025-11-10 08:27:56");
INSERT INTO `tags` VALUES("25","감성적","emotional","CONTENT","#DDA15E","감성적 분위기","0","1","2025-11-10 08:27:56","2025-11-10 08:27:56");
INSERT INTO `tags` VALUES("26","비즈니스","business","CONTENT","#283618","비즈니스용","0","1","2025-11-10 08:27:56","2025-11-10 08:27:56");
INSERT INTO `tags` VALUES("27","축제","festival","CONTENT","#BC6C25","축제 분위기","0","1","2025-11-10 08:27:56","2025-11-10 08:27:56");
INSERT INTO `tags` VALUES("28","힐링","healing","CONTENT","#FEFAE0","힐링 테마","0","1","2025-11-10 08:27:56","2025-11-10 08:27:56");
INSERT INTO `tags` VALUES("29","레트로","retro","CONTENT","#DDA15E","레트로 스타일","0","1","2025-11-10 08:27:56","2025-11-10 08:27:56");
INSERT INTO `tags` VALUES("30","트렌디","trendy","CONTENT","#BC6C25","최신 트렌드","0","1","2025-11-10 08:27:56","2025-11-10 08:27:56");

DROP TABLE IF EXISTS `ticket_comments`;
CREATE TABLE `ticket_comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '댓글 ID (PK)',
  `ticket_id` int(11) NOT NULL COMMENT '티켓 ID (FK -> tickets)',
  `user_id` int(11) NOT NULL COMMENT '작성자 user_id (FK -> users)',
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '댓글 내용',
  `is_internal` tinyint(1) DEFAULT 0 COMMENT '내부 메모 여부 (TRUE: 관리자만 보임, FALSE: 고객도 보임)',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  PRIMARY KEY (`comment_id`),
  KEY `idx_ticket` (`ticket_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `ticket_comments_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`ticket_id`) ON DELETE CASCADE,
  CONSTRAINT `ticket_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='티켓 댓글(Ticket Comment) 테이블 - 티켓 내 대화 및 메모';


DROP TABLE IF EXISTS `tickets`;
CREATE TABLE `tickets` (
  `ticket_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '티켓 ID (PK)',
  `ticket_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '티켓 번호 (고유, 예: T-2025-0001)',
  `customer_id` int(11) NOT NULL COMMENT '고객 ID (FK -> customers)',
  `category` enum('TECHNICAL','BILLING','DELIVERY','CONTENT','GENERAL') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '카테고리 (TECHNICAL: 기술지원, BILLING: 결제, DELIVERY: 배송, CONTENT: 콘텐츠, GENERAL: 일반)',
  `priority` enum('LOW','NORMAL','HIGH','URGENT') COLLATE utf8mb4_unicode_ci DEFAULT 'NORMAL' COMMENT '긴급도 (LOW: 낮음, NORMAL: 보통, HIGH: 높음, URGENT: 긴급)',
  `subject` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '제목',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '문의 내용',
  `status` enum('OPEN','IN_PROGRESS','ON_HOLD','RESOLVED','CLOSED') COLLATE utf8mb4_unicode_ci DEFAULT 'OPEN' COMMENT '상태 (OPEN: 접수, IN_PROGRESS: 처리중, ON_HOLD: 보류, RESOLVED: 해결됨, CLOSED: 종료)',
  `assigned_to` int(11) DEFAULT NULL COMMENT '담당자 user_id (FK -> users)',
  `resolved_date` datetime DEFAULT NULL COMMENT '해결 일시',
  `closed_date` datetime DEFAULT NULL COMMENT '종료 일시',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '수정일시',
  `created_by` int(11) DEFAULT NULL COMMENT '생성자 user_id',
  PRIMARY KEY (`ticket_id`),
  UNIQUE KEY `ticket_number` (`ticket_number`),
  KEY `idx_customer` (`customer_id`),
  KEY `idx_assigned_to` (`assigned_to`),
  KEY `idx_status` (`status`),
  KEY `idx_priority` (`priority`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='티켓(Ticket) 테이블 - 고객 문의 및 지원 요청 관리';

INSERT INTO `tickets` VALUES("7","TKT-001","31","TECHNICAL","HIGH","고장 문의","디스펜서 작동 안됨","OPEN","","","","2025-11-12 02:24:01","2025-11-12 02:24:01","");
INSERT INTO `tickets` VALUES("8","TKT-002","32","","","교체 요청","향 교체 필요","IN_PROGRESS","","","","2025-11-12 02:24:01","2025-11-12 02:24:01","");
INSERT INTO `tickets` VALUES("9","TKT-003","33","","LOW","설치 문의","2층 추가 설치","OPEN","","","","2025-11-12 02:24:01","2025-11-12 02:24:01","");

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '사용자 ID (PK)',
  `role_id` int(11) NOT NULL COMMENT '역할 ID (FK -> roles)',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '이메일 (로그인 ID, 중복 불가)',
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '비밀번호 해시 (bcrypt)',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '이름',
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '연락처 (- 포함, 예: 010-1234-5678)',
  `department` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_holder` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_id_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1 COMMENT '활성 상태 (TRUE: 사용, FALSE: 비활성)',
  `last_login_at` datetime DEFAULT NULL COMMENT '마지막 로그인 일시',
  `deleted_at` datetime DEFAULT NULL COMMENT '삭제일시 (Soft Delete, NULL: 미삭제)',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '수정일시',
  `created_by` int(11) DEFAULT NULL COMMENT '생성자 user_id',
  `updated_by` int(11) DEFAULT NULL COMMENT '수정자 user_id',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_role` (`role_id`),
  KEY `idx_deleted` (`deleted_at`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='사용자(User) 테이블 - 시스템 로그인 계정 정보';

INSERT INTO `users` VALUES("1","1","1211kkk@naver.com","$2y$10$A5SLZTqtf1YErcsI0togROaVLBCbUXekZX5CfDNEXWdrqmvMwZBgS","김최고관리자","010-1000-0001","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 09:01:05","0","0");
INSERT INTO `users` VALUES("2","2","program1472@naver.com","$2y$10$KlmTCFZH45p0BTY1m3OVauffbZuRk34mE4A9GSMm14hsccTlXzUZC","박본사관리","010-1000-0002","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 09:01:31","0","0");
INSERT INTO `users` VALUES("3","2","hq2@all2green.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","이운영팀장","010-1000-0003","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("4","2","hq3@all2green.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","최정산담당","010-1000-0004","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("5","2","hq4@all2green.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","정물류담당","010-1000-0005","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("6","2","hq5@all2green.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","한고객지원","010-1000-0006","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("7","3","vendor1@partner.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","김서울밴더","010-2000-0001","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("8","3","vendor2@partner.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","이경기밴더","010-2000-0002","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("9","3","vendor3@partner.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","박부산밴더","010-2000-0003","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("10","3","vendor4@partner.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","최대구밴더","010-2000-0004","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("11","3","vendor5@partner.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","정광주밴더","010-2000-0005","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 11:35:22","0","0");
INSERT INTO `users` VALUES("12","3","vendor6@partner.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","한대전밴더","010-2000-0006","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("13","3","vendor7@partner.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","윤인천밴더","010-2000-0007","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 11:33:11","0","0");
INSERT INTO `users` VALUES("14","3","vendor8@partner.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","강울산밴더","010-2000-0008","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 16:42:50","0","0");
INSERT INTO `users` VALUES("15","3","vendor9@partner.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","조제주밴더","010-2000-0009","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("16","3","vendor10@partner.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","백강원밴더","010-2000-0010","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("17","4","sales1@all2green.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","신영업1팀","010-3000-0001","마케팅","팀장","서울시 중구","새마을금고","215-75-22495","쌈지돈","212-78-22495","특이사항 없음","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 11:39:42","0","0");
INSERT INTO `users` VALUES("18","4","sales2@all2green.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","유영업2팀","010-3000-0002","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("19","4","sales3@all2green.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","노영업3팀","010-3000-0003","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("20","4","sales4@all2green.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","하영업4팀","010-3000-0004","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("21","4","sales5@all2green.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","홍영업5팀","010-3000-0005","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("22","4","sales6@partner.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","표밴더영업1","010-3000-0006","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("23","4","sales7@partner.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","고밴더영업2","010-3000-0007","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("24","4","sales8@partner.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","권밴더영업3","010-3000-0008","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("25","4","sales9@partner.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","석밴더영업4","010-3000-0009","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("26","4","sales10@partner.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","선밴더영업5","010-3000-0010","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("27","6","lucid1@luciddesign.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","안디자이너","010-5000-0001","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("28","6","lucid2@luciddesign.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","배그래픽","010-5000-0002","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("29","6","lucid3@luciddesign.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","임아트팀","010-5000-0003","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("30","6","lucid4@luciddesign.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","방크리에이터","010-5000-0004","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("31","5","customer1@company.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","손담당자1","010-4000-0001","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("32","5","customer2@company.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","양담당자2","010-4000-0002","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("33","5","customer3@company.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","변담당자3","010-4000-0003","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("34","5","customer4@company.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","황담당자4","010-4000-0004","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("35","5","customer5@company.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","서담당자5","010-4000-0005","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("36","5","customer6@company.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","전담당자6","010-4000-0006","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("37","5","customer7@company.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","탁담당자7","010-4000-0007","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("38","5","customer8@company.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","피담당자8","010-4000-0008","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("39","5","customer9@company.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","명담당자9","010-4000-0009","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("40","5","customer10@company.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","기담당자10","010-4000-0010","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("41","5","customer11@company.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","목담당자11","010-4000-0011","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("42","5","customer12@company.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","장담당자12","010-4000-0012","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("43","5","customer13@company.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","육담당자13","010-4000-0013","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("44","5","customer14@company.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","추담당자14","010-4000-0014","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("45","5","customer15@company.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","구담당자15","010-4000-0015","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("46","5","customer16@company.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","나담당자16","010-4000-0016","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("47","5","customer17@company.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","두담당자17","010-4000-0017","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("48","5","customer18@company.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","류담당자18","010-4000-0018","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("49","5","customer19@company.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","봉담당자19","010-4000-0019","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("50","5","customer20@company.com","$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi","사담당자20","010-4000-0020","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","0","0");
INSERT INTO `users` VALUES("56","4","1211ddd@naver.com","$2y$10$WPj828VeZMdBGyQH08a6muy0QyP.SUwhdvpi8d3DMZ.1xOfCa7HKm","dispenser","12341324","asgddasg","adgadsg","","adsgdasg","1325153","asdgadsg","gdfsg","","1","0000-00-00 00:00:00","2025-11-10 11:18:15","2025-11-10 11:13:11","2025-11-10 11:18:15","0","0");
INSERT INTO `users` VALUES("60","4","program14f72@naver.com","$2y$10$VKk.qoX1LeTEsgavRfg1ze84710v09nZ3.T96Rv9pNqR/n9MPP5xG","변희성","01052222318","4gdsg","gsdg","금강 펜테리움 2단지 203동 1401호","fsda","5342","gsdgs","dfsgsd","gsdg","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-11 17:52:36","2025-11-11 17:52:36","0","0");
INSERT INTO `users` VALUES("61","3","programa1472@naver.com","$2y$10$puuBGQTVarwzf0fJ5untse1oYs2krfNlh1NWKAIZYDIAfZXuIDLIm","dd","01052222318","","","","","","","","","1","0000-00-00 00:00:00","0000-00-00 00:00:00","2025-11-11 17:53:47","2025-11-11 17:53:47","0","0");
INSERT INTO `users` VALUES("76","3","vendor1@example.com","$2y$10$abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGH","(주)올투그린","02-1234-5678","","","","","","","","","1","","","2025-11-12 02:07:36","2025-11-12 02:07:36","","");
INSERT INTO `users` VALUES("77","3","vendor2@example.com","$2y$10$abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGH","(주)에코솔루션","02-2345-6789","","","","","","","","","1","","","2025-11-12 02:07:36","2025-11-12 02:07:36","","");
INSERT INTO `users` VALUES("78","3","vendor3@example.com","$2y$10$abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGH","(주)그린테크","02-3456-7890","","","","","","","","","1","","","2025-11-12 02:07:36","2025-11-12 02:07:36","","");
INSERT INTO `users` VALUES("79","4","sales1@example.com","$2y$10$abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGH","홍길동","010-1111-2222","영업팀","과장","","신한은행","110-987-654321","홍길동","","","1","","","2025-11-12 02:07:36","2025-11-12 02:07:36","","");
INSERT INTO `users` VALUES("80","4","sales2@example.com","$2y$10$abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGH","김영업","010-2222-3333","영업팀","대리","","국민은행","123-876-543210","김영업","","","1","","","2025-11-12 02:07:36","2025-11-12 02:07:36","","");
INSERT INTO `users` VALUES("81","4","sales3@example.com","$2y$10$abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGH","이세일즈","010-3333-4444","영업1팀","차장","","우리은행","1002-765-432109","이세일즈","","","1","","","2025-11-12 02:07:36","2025-11-12 02:07:36","","");
INSERT INTO `users` VALUES("82","4","sales4@example.com","$2y$10$abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGH","박판매","010-4444-5555","영업2팀","사원","","하나은행","123-654-321098","박판매","","","1","","","2025-11-12 02:07:36","2025-11-12 02:07:36","","");
INSERT INTO `users` VALUES("83","5","customer1@cafe.com","$2y$10$abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGH","김사장","010-1111-1111","","","","","","","","","1","","","2024-10-01 00:00:00","2025-11-12 02:11:45","","");
INSERT INTO `users` VALUES("84","5","customer2@food.com","$2y$10$abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGH","이사장","010-2222-2222","","","","","","","","","1","","","2024-10-15 00:00:00","2025-11-12 02:11:45","","");
INSERT INTO `users` VALUES("85","5","customer3@hospital.com","$2y$10$abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGH","박원장","010-3333-3333","","","","","","","","","1","","","2024-11-01 00:00:00","2025-11-12 02:11:45","","");
INSERT INTO `users` VALUES("86","5","customer4@hotel.com","$2y$10$abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGH","최대표","010-4444-4444","","","","","","","","","1","","","2024-11-05 00:00:00","2025-11-12 02:11:45","","");
INSERT INTO `users` VALUES("87","5","customer5@dept.com","$2y$10$abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGH","정사장","010-5555-5555","","","","","","","","","1","","","2024-11-10 00:00:00","2025-11-12 02:11:45","","");
INSERT INTO `users` VALUES("88","5","customer6@edu.com","$2y$10$abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGH","강원장","010-6666-6666","","","","","","","","","1","","","2024-09-01 00:00:00","2025-11-12 02:11:45","","");
INSERT INTO `users` VALUES("89","5","customer7@factory.com","$2y$10$abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGH","조대표","010-7777-7777","","","","","","","","","1","","","2024-09-15 00:00:00","2025-11-12 02:11:45","","");
INSERT INTO `users` VALUES("90","5","customer8@resort.com","$2y$10$abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGH","한사장","010-8888-8888","","","","","","","","","1","","","2024-08-01 00:00:00","2025-11-12 02:11:45","","");
INSERT INTO `users` VALUES("91","5","customer9@mart.com","$2y$10$abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGH","송대표","010-9999-9999","","","","","","","","","1","","","2024-08-20 00:00:00","2025-11-12 02:11:45","","");
INSERT INTO `users` VALUES("92","5","customer10@ski.com","$2y$10$abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGH","유사장","010-0000-0000","","","","","","","","","1","","","2024-07-15 00:00:00","2025-11-12 02:11:45","","");
INSERT INTO `users` VALUES("93","5","c1@cafe.com","$2y$10$abc","??????","010-1111-1111","","","","","","","","","1","","","2024-10-01 00:00:00","2025-11-12 02:12:36","","");
INSERT INTO `users` VALUES("94","5","c2@food.com","$2y$10$abc","?̻???","010-2222-2222","","","","","","","","","1","","","2024-10-15 00:00:00","2025-11-12 02:12:36","","");
INSERT INTO `users` VALUES("95","5","c3@hospital.com","$2y$10$abc","?ڿ???","010-3333-3333","","","","","","","","","1","","","2024-11-01 00:00:00","2025-11-12 02:12:36","","");

DROP TABLE IF EXISTS `vendors`;
CREATE TABLE `vendors` (
  `vendor_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '밴더 ID (PK)',
  `user_id` int(11) NOT NULL COMMENT '사용자 ID (FK -> users, 밴더 로그인 계정)',
  `company_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '회사명',
  `business_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '사업자등록번호 (- 제외, 10자리)',
  `ceo_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '대표자명',
  `business_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '업태',
  `business_category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '업종',
  `address` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '주소',
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '정산 은행명',
  `account_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '정산 계좌번호',
  `account_holder` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '예금주',
  `tax_id_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `commission_rate` decimal(5,2) DEFAULT 40.00 COMMENT '커미션율 (%, 기본 40.00)',
  `incentive_rate` decimal(5,2) DEFAULT 5.00 COMMENT '인센티브율 (%, 기본 5.00)',
  `contract_start_date` date DEFAULT NULL COMMENT '계약 시작일',
  `contract_end_date` date DEFAULT NULL COMMENT '계약 종료일',
  `contract_document_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '계약서 파일 URL',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1 COMMENT '활성 상태 (TRUE: 운영, FALSE: 계약종료/정지)',
  `deleted_at` datetime DEFAULT NULL COMMENT '삭제일시 (Soft Delete)',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '수정일시',
  `created_by` int(11) DEFAULT NULL COMMENT '생성자 user_id',
  `updated_by` int(11) DEFAULT NULL COMMENT '수정자 user_id',
  PRIMARY KEY (`vendor_id`),
  UNIQUE KEY `user_id` (`user_id`),
  UNIQUE KEY `business_number` (`business_number`),
  KEY `idx_user` (`user_id`),
  KEY `idx_active` (`is_active`),
  KEY `idx_deleted` (`deleted_at`),
  CONSTRAINT `vendors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='밴더(Vendor) 테이블 - 판매 파트너 정보 및 계약 조건';

INSERT INTO `vendors` VALUES("1","7","서울향기유통(주)","1234567890","김서울밴더","도매","향수 및 방향제 도매","서울특별시 강남구 테헤란로 123","","","","","국민은행","123-456789-01-001","김서울밴더","","40.00","5.00","2024-01-01","2025-12-31","","","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `vendors` VALUES("2","8","경기프래그런스","2234567890","이경기밴더","도매","생활용품 도매","경기도 성남시 분당구 판교로 234","","","","","신한은행","223-456789-01-002","이경기밴더","","40.00","5.00","2024-01-15","2025-12-31","","","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `vendors` VALUES("3","9","부산아로마상사","3234567890","박부산밴더","도매","향료 도매","부산광역시 해운대구 센텀로 345","","","","","우리은행","323-456789-01-003","박부산밴더","","40.00","5.00","2024-02-01","2025-12-31","","","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `vendors` VALUES("4","10","대구센트코리아","4234567890","최대구밴더","도매","방향제 도매","대구광역시 수성구 달구벌대로 456","","","","","IBK기업은행","423-456789-01-004","최대구밴더","","40.00","5.00","2024-02-15","2025-12-31","","","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `vendors` VALUES("5","11","광주향기나라","5234567890","","도매","생활향수 도매","광주광역시 서구 상무대로 567","","","","","하나은행","","","","40.00","5.00","2024-03-01","2025-12-31","","","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 11:35:22","1","0");
INSERT INTO `vendors` VALUES("6","12","대전프리미엄센트","6234567890","한대전밴더","도매","고급향수 도매","대전광역시 유성구 대학로 678","","","","","농협은행","623-456789-01-006","한대전밴더","","42.00","6.00","2024-03-15","2025-12-31","","","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `vendors` VALUES("7","13","인천향기마케팅","7234567890","나대표","도매","향료 및 방향제","인천광역시 연수구 송도동 789","02-1234-5678","홍지민","010-5678-9410","gdf@gsafg.com","새마을금고","215-75-22495","쌈지돈","212-78-22495","40.00","5.00","2024-04-01","2025-12-31","","특이사항 없음","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 11:33:11","1","0");
INSERT INTO `vendors` VALUES("8","14","울산아로마존","8234567890","강울산밴더","도매","향수 도매","울산광역시 남구 삼산로 890","","","홍지선","","수협은행","823-456789-01-008","강울산밴더","","40.00","5.00","2024-04-15","2025-12-31","","","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 16:42:50","1","0");
INSERT INTO `vendors` VALUES("9","15","제주향기바람","9234567890","조제주밴더","도매","천연향료 도매","제주특별자치도 제주시 중앙로 901","","","","","제주은행","923-456789-01-009","조제주밴더","","45.00","7.00","2024-05-01","2025-12-31","","","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `vendors` VALUES("10","16","강원힐링센트","0234567890","백강원밴더","도매","웰빙향수 도매","강원도 춘천시 중앙로 012","","","","","신협","023-456789-01-010","백강원밴더","","40.00","5.00","2024-05-15","2025-12-31","","","1","0000-00-00 00:00:00","2025-11-10 08:27:56","2025-11-10 08:27:56","1","0");
INSERT INTO `vendors` VALUES("11","61","tophost","tophost","a","","","금강 펜테리움 2단지 203동 1401호","","변희성","01052222318","programs1472@naver.com","fsda","5342","gsdgs","dfsgsd","40.00","5.00","0000-00-00","0000-00-00","","ddd","1","0000-00-00 00:00:00","2025-11-11 17:53:47","2025-11-11 17:53:47","0","0");
INSERT INTO `vendors` VALUES("18","76","(주)올투그린파트너스","123-45-67890","김대표","","","서울시 강남구 테헤란로 123","02-1234-5678","김담당","010-1234-5678","contact1@all2green.com","신한은행","110-123-456789","(주)올투그린파트너스","","40.00","5.00","","","","","1","","2025-11-12 02:07:36","2025-11-12 02:07:36","","");
INSERT INTO `vendors` VALUES("19","77","(주)에코솔루션","234-56-78901","이대표","","","서울시 서초구 서초대로 456","02-2345-6789","이담당","010-2345-6789","contact2@eco.com","국민은행","123-456-789012","(주)에코솔루션","","35.00","3.00","","","","","1","","2025-11-12 02:07:36","2025-11-12 02:07:36","","");
INSERT INTO `vendors` VALUES("20","78","(주)그린테크","345-67-89012","박대표","","","경기도 성남시 분당구 판교로 789","02-3456-7890","박담당","010-3456-7890","contact3@greentech.com","우리은행","1002-123-456789","(주)그린테크","","38.00","4.00","","","","","1","","2025-11-12 02:07:36","2025-11-12 02:07:36","","");

DROP TABLE IF EXISTS `work_order_items`;
CREATE TABLE `work_order_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '작업 항목 ID (PK)',
  `work_order_id` int(11) NOT NULL COMMENT '작업지시서 ID (FK -> work_orders)',
  `item_type` enum('DEVICE','SCENT','CONTENT','PART','ACCESSORY') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '항목 타입',
  `item_id_ref` int(11) NOT NULL COMMENT '항목 참조 ID',
  `quantity` int(11) NOT NULL DEFAULT 1 COMMENT '수량',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '항목별 메모',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  PRIMARY KEY (`item_id`),
  KEY `idx_work_order` (`work_order_id`),
  KEY `idx_item_ref` (`item_type`,`item_id_ref`),
  CONSTRAINT `work_order_items_ibfk_1` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`work_order_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='작업지시서 항목(Work Order Item) 테이블 - 작업지시서에 포함된 상품 항목';


DROP TABLE IF EXISTS `work_orders`;
CREATE TABLE `work_orders` (
  `work_order_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '작업지시서 ID (PK)',
  `order_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '작업지시서 번호 (고유, 예: WO-2025-0001)',
  `subscription_id` int(11) DEFAULT NULL COMMENT '연관 구독 ID (FK -> subscriptions, NULL: 단품 주문)',
  `cycle_id` int(11) DEFAULT NULL COMMENT '연관 주기 ID (FK -> subscription_cycles)',
  `customer_id` int(11) NOT NULL COMMENT '고객 ID (FK -> customers)',
  `order_type` enum('SUBSCRIPTION','CONTENT_CUSTOM','ADDITIONAL') COLLATE utf8mb4_unicode_ci DEFAULT 'SUBSCRIPTION' COMMENT '작업 유형 (SUBSCRIPTION: 정기 구독, CONTENT_CUSTOM: 콘텐츠 커스터마이징, ADDITIONAL: 추가 구매)',
  `status` enum('PENDING','IN_PROGRESS','PRINTING','READY','SHIPPED','COMPLETED','CANCELLED') COLLATE utf8mb4_unicode_ci DEFAULT 'PENDING' COMMENT '상태 (PENDING: 대기, IN_PROGRESS: 작업중, PRINTING: 프린팅중, READY: 출고 대기, SHIPPED: 배송됨, COMPLETED: 완료, CANCELLED: 취소)',
  `due_date` date DEFAULT NULL COMMENT '마감일',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '작업 메모',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '생성일시',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '수정일시',
  `created_by` int(11) DEFAULT NULL COMMENT '생성자 user_id',
  `updated_by` int(11) DEFAULT NULL COMMENT '수정자 user_id',
  PRIMARY KEY (`work_order_id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `idx_subscription` (`subscription_id`),
  KEY `idx_cycle` (`cycle_id`),
  KEY `idx_customer` (`customer_id`),
  KEY `idx_status` (`status`),
  KEY `idx_due_date` (`due_date`),
  CONSTRAINT `work_orders_ibfk_1` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`subscription_id`) ON DELETE SET NULL,
  CONSTRAINT `work_orders_ibfk_2` FOREIGN KEY (`cycle_id`) REFERENCES `subscription_cycles` (`cycle_id`) ON DELETE SET NULL,
  CONSTRAINT `work_orders_ibfk_3` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='작업지시서(Work Order) 테이블 - 구독 주기 또는 추가 주문에 대한 작업 지시';

INSERT INTO `work_orders` VALUES("1","WO-2024-0001","1","1","1","SUBSCRIPTION","COMPLETED","2024-02-01","","2025-11-10 08:31:17","2025-11-10 08:31:17","1","0");
INSERT INTO `work_orders` VALUES("2","WO-2024-0002","2","1","1","SUBSCRIPTION","COMPLETED","2024-02-01","","2025-11-10 08:31:17","2025-11-10 08:31:17","1","0");
INSERT INTO `work_orders` VALUES("3","WO-2024-0003","3","1","1","SUBSCRIPTION","COMPLETED","2024-02-05","","2025-11-10 08:31:17","2025-11-10 08:31:17","1","0");
INSERT INTO `work_orders` VALUES("4","WO-2024-0004","4","1","1","SUBSCRIPTION","COMPLETED","2024-02-05","","2025-11-10 08:31:17","2025-11-10 08:31:17","1","0");
INSERT INTO `work_orders` VALUES("5","WO-2024-0005","5","1","1","SUBSCRIPTION","COMPLETED","2024-02-10","","2025-11-10 08:31:17","2025-11-10 08:31:17","1","0");
INSERT INTO `work_orders` VALUES("6","WO-2024-0006","6","1","2","SUBSCRIPTION","COMPLETED","2024-02-15","","2025-11-10 08:31:17","2025-11-10 08:31:17","1","0");
INSERT INTO `work_orders` VALUES("7","WO-2024-0007","1","1","2","SUBSCRIPTION","COMPLETED","2024-02-15","","2025-11-10 08:31:17","2025-11-10 08:31:17","1","0");
INSERT INTO `work_orders` VALUES("8","WO-2024-0008","2","1","2","SUBSCRIPTION","COMPLETED","2024-02-20","","2025-11-10 08:31:17","2025-11-10 08:31:17","1","0");
INSERT INTO `work_orders` VALUES("9","WO-2024-0009","3","1","3","SUBSCRIPTION","COMPLETED","2024-02-25","","2025-11-10 08:31:17","2025-11-10 08:31:17","1","0");
INSERT INTO `work_orders` VALUES("10","WO-2024-0010","4","1","3","SUBSCRIPTION","COMPLETED","2024-02-25","","2025-11-10 08:31:17","2025-11-10 08:31:17","1","0");
INSERT INTO `work_orders` VALUES("11","WO-2024-0011","5","1","3","SUBSCRIPTION","COMPLETED","2024-03-01","","2025-11-10 08:31:17","2025-11-10 08:31:17","1","0");
INSERT INTO `work_orders` VALUES("12","WO-2024-0012","6","1","3","SUBSCRIPTION","COMPLETED","2024-03-01","","2025-11-10 08:31:17","2025-11-10 08:31:17","1","0");
INSERT INTO `work_orders` VALUES("13","WO-2024-0013","7","1","4","SUBSCRIPTION","COMPLETED","2024-03-05","","2025-11-10 08:31:17","2025-11-10 08:31:17","1","0");
INSERT INTO `work_orders` VALUES("14","WO-2024-0014","8","1","4","SUBSCRIPTION","COMPLETED","2024-03-05","","2025-11-10 08:31:17","2025-11-10 08:31:17","1","0");
INSERT INTO `work_orders` VALUES("15","WO-2024-0015","9","1","4","SUBSCRIPTION","COMPLETED","2024-03-10","","2025-11-10 08:31:17","2025-11-10 08:31:17","1","0");
INSERT INTO `work_orders` VALUES("16","WO-2024-0016","10","1","5","SUBSCRIPTION","COMPLETED","2024-03-15","","2025-11-10 08:31:17","2025-11-10 08:31:17","1","0");
INSERT INTO `work_orders` VALUES("17","WO-2024-0017","17","1","5","SUBSCRIPTION","COMPLETED","2024-03-15","","2025-11-10 08:31:17","2025-11-10 08:31:17","1","0");
INSERT INTO `work_orders` VALUES("18","WO-2024-0018","18","1","6","SUBSCRIPTION","COMPLETED","2024-03-20","","2025-11-10 08:31:17","2025-11-10 08:31:17","1","0");
INSERT INTO `work_orders` VALUES("19","WO-2024-0019","19","1","6","SUBSCRIPTION","COMPLETED","2024-03-20","","2025-11-10 08:31:17","2025-11-10 08:31:17","1","0");
INSERT INTO `work_orders` VALUES("20","WO-2024-0020","20","1","6","SUBSCRIPTION","COMPLETED","2024-03-25","","2025-11-10 08:31:17","2025-11-10 08:31:17","1","0");
INSERT INTO `work_orders` VALUES("21","WO-2024-0021","21","1","1","SUBSCRIPTION","COMPLETED","2024-04-01","","2025-11-10 08:31:17","2025-11-10 08:31:17","1","0");
INSERT INTO `work_orders` VALUES("22","WO-2024-0022","22","1","1","SUBSCRIPTION","COMPLETED","2024-04-01","","2025-11-10 08:31:17","2025-11-10 08:31:17","1","0");
INSERT INTO `work_orders` VALUES("23","WO-2024-0023","23","1","2","SUBSCRIPTION","COMPLETED","2024-04-05","","2025-11-10 08:31:17","2025-11-10 08:31:17","1","0");
INSERT INTO `work_orders` VALUES("24","WO-2024-0024","24","1","2","SUBSCRIPTION","COMPLETED","2024-04-05","","2025-11-10 08:31:17","2025-11-10 08:31:17","1","0");
INSERT INTO `work_orders` VALUES("25","WO-2024-0025","25","1","3","SUBSCRIPTION","COMPLETED","2024-04-10","","2025-11-10 08:31:17","2025-11-10 08:31:17","1","0");
INSERT INTO `work_orders` VALUES("26","WO-2024-0026","26","1","3","SUBSCRIPTION","COMPLETED","2024-04-10","","2025-11-10 08:31:17","2025-11-10 08:31:17","1","0");
INSERT INTO `work_orders` VALUES("27","WO-2024-0027","27","1","3","SUBSCRIPTION","COMPLETED","2024-04-15","","2025-11-10 08:31:17","2025-11-10 08:31:17","1","0");
INSERT INTO `work_orders` VALUES("28","WO-2024-0028","28","1","4","SUBSCRIPTION","COMPLETED","2024-04-20","","2025-11-10 08:31:17","2025-11-10 08:31:17","1","0");
INSERT INTO `work_orders` VALUES("29","WO-2024-0029","29","1","4","SUBSCRIPTION","COMPLETED","2024-04-20","","2025-11-10 08:31:17","2025-11-10 08:31:17","1","0");
INSERT INTO `work_orders` VALUES("30","WO-2024-0030","30","1","5","SUBSCRIPTION","COMPLETED","2024-04-25","","2025-11-10 08:31:17","2025-11-10 08:31:17","1","0");

