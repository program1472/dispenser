-- ============================================================
-- 향기 디스펜서 구독 서비스 시스템 - 더미 데이터
-- ============================================================
-- 생성일: 2025-11-10
-- 주의: FK 의존성 순서대로 실행 필요
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- 1. 역할(Role) - 6개
-- ============================================================
INSERT INTO `roles` (`role_name`, `display_name`, `description`, `is_active`) VALUES
('SUPER_ADMIN', '최고관리자', '시스템 전체 관리 권한', TRUE),
('HQ_ADMIN', '본사 관리자', '본사 운영 및 정책 관리', TRUE),
('VENDOR', '밴더', '판매 파트너 권한', TRUE),
('SALES_REP', '영업사원', '고객 관리 및 영업 활동', TRUE),
('CUSTOMER', '구독 고객', '서비스 이용 고객', TRUE),
('LUCID', '루시드', '콘텐츠 디자인 제작', TRUE);

-- ============================================================
-- 2. 사용자(User) - 50개
-- ============================================================

-- 최고관리자 (1명)
INSERT INTO `users` (`role_id`, `email`, `password_hash`, `name`, `phone`, `is_active`) VALUES
(1, 'admin@all2green.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '김최고관리자', '010-1000-0001', TRUE);

-- 본사 관리자 (5명)
INSERT INTO `users` (`role_id`, `email`, `password_hash`, `name`, `phone`, `is_active`) VALUES
(2, 'hq1@all2green.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '박본사관리', '010-1000-0002', TRUE),
(2, 'hq2@all2green.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '이운영팀장', '010-1000-0003', TRUE),
(2, 'hq3@all2green.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '최정산담당', '010-1000-0004', TRUE),
(2, 'hq4@all2green.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '정물류담당', '010-1000-0005', TRUE),
(2, 'hq5@all2green.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '한고객지원', '010-1000-0006', TRUE);

-- 밴더 (10명)
INSERT INTO `users` (`role_id`, `email`, `password_hash`, `name`, `phone`, `is_active`) VALUES
(3, 'vendor1@partner.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '김서울밴더', '010-2000-0001', TRUE),
(3, 'vendor2@partner.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '이경기밴더', '010-2000-0002', TRUE),
(3, 'vendor3@partner.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '박부산밴더', '010-2000-0003', TRUE),
(3, 'vendor4@partner.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '최대구밴더', '010-2000-0004', TRUE),
(3, 'vendor5@partner.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '정광주밴더', '010-2000-0005', TRUE),
(3, 'vendor6@partner.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '한대전밴더', '010-2000-0006', TRUE),
(3, 'vendor7@partner.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '윤인천밴더', '010-2000-0007', TRUE),
(3, 'vendor8@partner.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '강울산밴더', '010-2000-0008', TRUE),
(3, 'vendor9@partner.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '조제주밴더', '010-2000-0009', TRUE),
(3, 'vendor10@partner.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '백강원밴더', '010-2000-0010', TRUE);

-- 영업사원 (10명)
INSERT INTO `users` (`role_id`, `email`, `password_hash`, `name`, `phone`, `is_active`) VALUES
(4, 'sales1@all2green.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '신영업1팀', '010-3000-0001', TRUE),
(4, 'sales2@all2green.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '유영업2팀', '010-3000-0002', TRUE),
(4, 'sales3@all2green.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '노영업3팀', '010-3000-0003', TRUE),
(4, 'sales4@all2green.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '하영업4팀', '010-3000-0004', TRUE),
(4, 'sales5@all2green.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '홍영업5팀', '010-3000-0005', TRUE),
(4, 'sales6@partner.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '표밴더영업1', '010-3000-0006', TRUE),
(4, 'sales7@partner.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '고밴더영업2', '010-3000-0007', TRUE),
(4, 'sales8@partner.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '권밴더영업3', '010-3000-0008', TRUE),
(4, 'sales9@partner.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '석밴더영업4', '010-3000-0009', TRUE),
(4, 'sales10@partner.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '선밴더영업5', '010-3000-0010', TRUE);

-- 루시드 (4명)
INSERT INTO `users` (`role_id`, `email`, `password_hash`, `name`, `phone`, `is_active`) VALUES
(6, 'lucid1@luciddesign.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '안디자이너', '010-5000-0001', TRUE),
(6, 'lucid2@luciddesign.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '배그래픽', '010-5000-0002', TRUE),
(6, 'lucid3@luciddesign.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '임아트팀', '010-5000-0003', TRUE),
(6, 'lucid4@luciddesign.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '방크리에이터', '010-5000-0004', TRUE);

-- 고객 (20명)
INSERT INTO `users` (`role_id`, `email`, `password_hash`, `name`, `phone`, `is_active`) VALUES
(5, 'customer1@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '손담당자1', '010-4000-0001', TRUE),
(5, 'customer2@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '양담당자2', '010-4000-0002', TRUE),
(5, 'customer3@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '변담당자3', '010-4000-0003', TRUE),
(5, 'customer4@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '황담당자4', '010-4000-0004', TRUE),
(5, 'customer5@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '서담당자5', '010-4000-0005', TRUE),
(5, 'customer6@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '전담당자6', '010-4000-0006', TRUE),
(5, 'customer7@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '탁담당자7', '010-4000-0007', TRUE),
(5, 'customer8@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '피담당자8', '010-4000-0008', TRUE),
(5, 'customer9@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '명담당자9', '010-4000-0009', TRUE),
(5, 'customer10@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '기담당자10', '010-4000-0010', TRUE),
(5, 'customer11@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '목담당자11', '010-4000-0011', TRUE),
(5, 'customer12@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '장담당자12', '010-4000-0012', TRUE),
(5, 'customer13@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '육담당자13', '010-4000-0013', TRUE),
(5, 'customer14@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '추담당자14', '010-4000-0014', TRUE),
(5, 'customer15@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '구담당자15', '010-4000-0015', TRUE),
(5, 'customer16@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '나담당자16', '010-4000-0016', TRUE),
(5, 'customer17@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '두담당자17', '010-4000-0017', TRUE),
(5, 'customer18@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '류담당자18', '010-4000-0018', TRUE),
(5, 'customer19@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '봉담당자19', '010-4000-0019', TRUE),
(5, 'customer20@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '사담당자20', '010-4000-0020', TRUE);

-- ============================================================
-- 3. 밴더(Vendor) - 10개
-- ============================================================
INSERT INTO `vendors` (`user_id`, `company_name`, `business_number`, `ceo_name`, `business_type`, `business_category`, `address`, `bank_name`, `account_number`, `account_holder`, `commission_rate`, `incentive_rate`, `contract_start_date`, `contract_end_date`, `is_active`, `created_by`) VALUES
(7, '서울향기유통(주)', '1234567890', '김서울밴더', '도매', '향수 및 방향제 도매', '서울특별시 강남구 테헤란로 123', '국민은행', '123-456789-01-001', '김서울밴더', 40.00, 5.00, '2024-01-01', '2025-12-31', TRUE, 1),
(8, '경기프래그런스', '2234567890', '이경기밴더', '도매', '생활용품 도매', '경기도 성남시 분당구 판교로 234', '신한은행', '223-456789-01-002', '이경기밴더', 40.00, 5.00, '2024-01-15', '2025-12-31', TRUE, 1),
(9, '부산아로마상사', '3234567890', '박부산밴더', '도매', '향료 도매', '부산광역시 해운대구 센텀로 345', '우리은행', '323-456789-01-003', '박부산밴더', 40.00, 5.00, '2024-02-01', '2025-12-31', TRUE, 1),
(10, '대구센트코리아', '4234567890', '최대구밴더', '도매', '방향제 도매', '대구광역시 수성구 달구벌대로 456', 'IBK기업은행', '423-456789-01-004', '최대구밴더', 40.00, 5.00, '2024-02-15', '2025-12-31', TRUE, 1),
(11, '광주향기나라', '5234567890', '정광주밴더', '도매', '생활향수 도매', '광주광역시 서구 상무대로 567', '하나은행', '523-456789-01-005', '정광주밴더', 40.00, 5.00, '2024-03-01', '2025-12-31', TRUE, 1),
(12, '대전프리미엄센트', '6234567890', '한대전밴더', '도매', '고급향수 도매', '대전광역시 유성구 대학로 678', '농협은행', '623-456789-01-006', '한대전밴더', 42.00, 6.00, '2024-03-15', '2025-12-31', TRUE, 1),
(13, '인천향기마케팅', '7234567890', '윤인천밴더', '도매', '향료 및 방향제', '인천광역시 연수구 송도동 789', '새마을금고', '723-456789-01-007', '윤인천밴더', 40.00, 5.00, '2024-04-01', '2025-12-31', TRUE, 1),
(14, '울산아로마존', '8234567890', '강울산밴더', '도매', '향수 도매', '울산광역시 남구 삼산로 890', '수협은행', '823-456789-01-008', '강울산밴더', 40.00, 5.00, '2024-04-15', '2025-12-31', TRUE, 1),
(15, '제주향기바람', '9234567890', '조제주밴더', '도매', '천연향료 도매', '제주특별자치도 제주시 중앙로 901', '제주은행', '923-456789-01-009', '조제주밴더', 45.00, 7.00, '2024-05-01', '2025-12-31', TRUE, 1),
(16, '강원힐링센트', '0234567890', '백강원밴더', '도매', '웰빙향수 도매', '강원도 춘천시 중앙로 012', '신협', '023-456789-01-010', '백강원밴더', 40.00, 5.00, '2024-05-15', '2025-12-31', TRUE, 1);

-- ============================================================
-- 4. 고객(Customer) - 30개
-- ============================================================
INSERT INTO `customers` (`user_id`, `vendor_id`, `company_name`, `business_number`, `ceo_name`, `business_type`, `business_category`, `address`, `payment_method`, `bank_name`, `account_number`, `card_number_masked`, `billing_key`, `is_active`, `created_by`) VALUES
(21, 7, '스타벅스코리아(주)', '1112223330', '손담당자1', '서비스업', '커피전문점', '서울특별시 중구 을지로 100', 'CARD', NULL, NULL, '****-****-****-1234', 'billing_key_001', TRUE, 7),
(22, 7, '롯데호텔서울', '1112223331', '양담당자2', '숙박업', '특급호텔', '서울특별시 중구 소공로 30', 'CMS', '신한은행', '100-123-456789', NULL, NULL, TRUE, 7),
(23, 7, '신세계백화점 강남점', '1112223332', '변담당자3', '소매업', '백화점', '서울특별시 서초구 신반포로 176', 'CARD', NULL, NULL, '****-****-****-5678', 'billing_key_002', TRUE, 7),
(24, 8, '현대백화점 판교점', '1112223333', '황담당자4', '소매업', '백화점', '경기도 성남시 분당구 판교역로 146', 'CARD', NULL, NULL, '****-****-****-9012', 'billing_key_003', TRUE, 8),
(25, 8, '(주)올투그린', '1112223334', '서담당자5', '제조업', 'IT기기', '경기도 용인시 기흥구 용구대로 2377', 'CMS', '국민은행', '100-234-567890', NULL, NULL, TRUE, 8),
(26, 8, '삼성전자 수원사옥', '1112223335', '전담당자6', '제조업', '전자기기', '경기도 수원시 영통구 삼성로 129', 'CARD', NULL, NULL, '****-****-****-3456', 'billing_key_004', TRUE, 8),
(27, 9, '해운대그랜드호텔', '1112223336', '탁담당자7', '숙박업', '호텔', '부산광역시 해운대구 중동 1411-23', 'CARD', NULL, NULL, '****-****-****-7890', 'billing_key_005', TRUE, 9),
(28, 9, '파라다이스호텔부산', '1112223337', '피담당자8', '숙박업', '특급호텔', '부산광역시 해운대구 중동 1408-5', 'CMS', '우리은행', '200-345-678901', NULL, NULL, TRUE, 9),
(29, 9, '롯데백화점 부산본점', '1112223338', '명담당자9', '소매업', '백화점', '부산광역시 부산진구 가야대로 772', 'CARD', NULL, NULL, '****-****-****-1357', 'billing_key_006', TRUE, 9),
(30, 10, '대구신세계백화점', '1112223339', '기담당자10', '소매업', '백화점', '대구광역시 동구 동부로 149', 'CARD', NULL, NULL, '****-****-****-2468', 'billing_key_007', TRUE, 10),
(31, 10, '인터불고호텔대구', '1112223340', '목담당자11', '숙박업', '호텔', '대구광역시 중구 동성로 141', 'CMS', 'IBK기업은행', '300-456-789012', NULL, NULL, TRUE, 10),
(32, 10, '롯데시네마대구', '1112223341', '장담당자12', '서비스업', '영화관', '대구광역시 동구 팔공로 177', 'CARD', NULL, NULL, '****-****-****-3691', 'billing_key_008', TRUE, 10),
(33, 11, '광주신세계백화점', '1112223342', '육담당자13', '소매업', '백화점', '광주광역시 서구 무진대로 932', 'CARD', NULL, NULL, '****-****-****-4702', 'billing_key_009', TRUE, 11),
(34, 11, '김대중컨벤션센터', '1112223343', '추담당자14', '서비스업', '컨벤션센터', '광주광역시 서구 상무누리로 30', 'CMS', '하나은행', '400-567-890123', NULL, NULL, TRUE, 11),
(35, 11, '조선대학교병원', '1112223344', '구담당자15', '의료업', '종합병원', '광주광역시 동구 필문대로 365', 'CARD', NULL, NULL, '****-****-****-5813', 'billing_key_010', TRUE, 11),
(36, 12, '대전컨벤션센터', '1112223345', '나담당자16', '서비스업', '컨벤션센터', '대전광역시 유성구 엑스포로 107', 'CARD', NULL, NULL, '****-****-****-6924', 'billing_key_011', TRUE, 12),
(37, 12, '한국과학기술원(KAIST)', '1112223346', '두담당자17', '교육업', '대학교', '대전광역시 유성구 대학로 291', 'CMS', '농협은행', '500-678-901234', NULL, NULL, TRUE, 12),
(38, 12, '갤러리아타임월드', '1112223347', '류담당자18', '소매업', '백화점', '대전광역시 서구 대덕대로 211', 'CARD', NULL, NULL, '****-****-****-7035', 'billing_key_012', TRUE, 12),
(39, 13, '인천국제공항 제1터미널', '1112223348', '봉담당자19', '교통업', '공항', '인천광역시 중구 공항로 272', 'CARD', NULL, NULL, '****-****-****-8146', 'billing_key_013', TRUE, 13),
(40, 13, '파라다이스시티', '1112223349', '사담당자20', '숙박업', '복합리조트', '인천광역시 중구 영종해안남로 321', 'CMS', '새마을금고', '600-789-012345', NULL, NULL, TRUE, 13),
(21, NULL, 'CGV여의도', '1112223350', '손담당자1', '서비스업', '영화관', '서울특별시 영등포구 의사당대로 83', 'CARD', NULL, NULL, '****-****-****-9257', 'billing_key_014', TRUE, 1),
(22, NULL, 'LG디스플레이본사', '1112223351', '양담당자2', '제조업', '디스플레이', '서울특별시 영등포구 여의대로 128', 'CMS', '신한은행', '700-890-123456', NULL, NULL, TRUE, 1),
(23, NULL, 'SK텔레콤타워', '1112223352', '변담당자3', '통신업', '이동통신', '서울특별시 중구 을지로 65', 'CARD', NULL, NULL, '****-****-****-0368', 'billing_key_015', TRUE, 1),
(24, 14, '현대자동차 울산공장', '1112223353', '황담당자4', '제조업', '자동차', '울산광역시 북구 연암로 700', 'CARD', NULL, NULL, '****-****-****-1479', 'billing_key_016', TRUE, 14),
(25, 14, '롯데호텔울산', '1112223354', '서담당자5', '숙박업', '호텔', '울산광역시 남구 삼산로 282', 'CMS', '수협은행', '800-901-234567', NULL, NULL, TRUE, 14),
(26, 15, '제주국제공항', '1112223355', '전담당자6', '교통업', '공항', '제주특별자치도 제주시 공항로 2', 'CARD', NULL, NULL, '****-****-****-2580', 'billing_key_017', TRUE, 15),
(27, 15, '제주신라호텔', '1112223356', '탁담당자7', '숙박업', '특급호텔', '제주특별자치도 서귀포시 중문관광로 72', 'CMS', '제주은행', '900-012-345678', NULL, NULL, TRUE, 15),
(28, 15, '한라산국립공원센터', '1112223357', '피담당자8', '관광업', '국립공원', '제주특별자치도 제주시 1100로 2070-61', 'CARD', NULL, NULL, '****-****-****-3691', 'billing_key_018', TRUE, 15),
(29, 16, '강원대학교춘천캠퍼스', '1112223358', '명담당자9', '교육업', '대학교', '강원도 춘천시 강원대학길 1', 'CMS', '신협', '101-123-456789', NULL, NULL, TRUE, 16),
(30, 16, '평창알펜시아리조트', '1112223359', '기담당자10', '숙박업', '스키리조트', '강원도 평창군 대관령면 솔봉로 325', 'CARD', NULL, NULL, '****-****-****-4702', 'billing_key_019', TRUE, 16);

-- ============================================================
-- 5. 고객 현장(Site) - 50개
-- ============================================================
INSERT INTO `customer_sites` (`customer_id`, `site_name`, `address`, `contact_name`, `contact_phone`, `notes`, `is_active`, `created_by`) VALUES
-- 스타벅스코리아 (5개 지점)
(1, '스타벅스 강남점', '서울특별시 강남구 테헤란로 427', '박점장', '02-1234-5001', '1층 로비 중앙', TRUE, 7),
(1, '스타벅스 여의도점', '서울특별시 영등포구 여의대로 108', '이점장', '02-1234-5002', '1층 입구 오른쪽', TRUE, 7),
(1, '스타벅스 홍대점', '서울특별시 마포구 양화로 160', '최점장', '02-1234-5003', '2층 계단 옆', TRUE, 7),
(1, '스타벅스 명동점', '서울특별시 중구 명동길 52', '정점장', '02-1234-5004', '1층 카운터 뒤', TRUE, 7),
(1, '스타벅스 코엑스점', '서울특별시 강남구 영동대로 513', '한점장', '02-1234-5005', 'B1층 중앙홀', TRUE, 7),

-- 롯데호텔서울 (3개 구역)
(2, '롯데호텔 로비', '서울특별시 중구 소공로 30', '김로비매니저', '02-2222-1001', '메인 로비 안내데스크 옆', TRUE, 7),
(2, '롯데호텔 연회장', '서울특별시 중구 소공로 30', '신연회팀장', '02-2222-1002', '2층 연회장 입구', TRUE, 7),
(2, '롯데호텔 비즈니스센터', '서울특별시 중구 소공로 30', '유비즈니스매니저', '02-2222-1003', '3층 비즈니스센터 내부', TRUE, 7),

-- 신세계백화점 강남점 (4개 층)
(3, '신세계 강남 1층', '서울특별시 서초구 신반포로 176', '노팀장', '02-3333-1001', '1층 화장품 코너 중앙', TRUE, 7),
(3, '신세계 강남 지하1층', '서울특별시 서초구 신반포로 176', '하매니저', '02-3333-1002', 'B1층 식품관 입구', TRUE, 7),
(3, '신세계 강남 6층', '서울특별시 서초구 신반포로 176', '홍매니저', '02-3333-1003', '6층 레스토랑가', TRUE, 7),
(3, '신세계 강남 주차장', '서울특별시 서초구 신반포로 176', '표관리팀', '02-3333-1004', '지하 주차장 입구', TRUE, 7),

-- 현대백화점 판교점 (3개 구역)
(4, '현대 판교 본관 1층', '경기도 성남시 분당구 판교역로 146', '고매니저', '031-4444-1001', '본관 1층 정문', TRUE, 8),
(4, '현대 판교 식품관', '경기도 성남시 분당구 판교역로 146', '권팀장', '031-4444-1002', '식품관 중앙 통로', TRUE, 8),
(4, '현대 판교 주차장', '경기도 성남시 분당구 판교역로 146', '석관리자', '031-4444-1003', '지하 주차장', TRUE, 8),

-- 올투그린 (2개 현장)
(5, '올투그린 본사', '경기도 용인시 기흥구 용구대로 2377', '선총무팀장', '031-5555-1001', '1층 로비', TRUE, 8),
(5, '올투그린 R&D센터', '경기도 용인시 기흥구 용구대로 2377', '안연구소장', '031-5555-1002', '연구동 1층', TRUE, 8),

-- 삼성전자 수원사옥 (3개 건물)
(6, '삼성 수원 제1공장', '경기도 수원시 영통구 삼성로 129', '배관리부장', '031-6666-1001', '공장동 로비', TRUE, 8),
(6, '삼성 수원 본관', '경기도 수원시 영통구 삼성로 129', '임본부장', '031-6666-1002', '본관 로비', TRUE, 8),
(6, '삼성 수원 식당', '경기도 수원시 영통구 삼성로 129', '방급식팀장', '031-6666-1003', '구내식당 입구', TRUE, 8),

-- 해운대그랜드호텔 (2개)
(7, '해운대그랜드 로비', '부산광역시 해운대구 중동 1411-23', '손지배인', '051-7777-1001', '메인 로비', TRUE, 9),
(7, '해운대그랜드 연회장', '부산광역시 해운대구 중동 1411-23', '양연회팀', '051-7777-1002', '2층 연회장', TRUE, 9),

-- 파라다이스호텔부산 (2개)
(8, '파라다이스부산 로비', '부산광역시 해운대구 중동 1408-5', '변로비매니저', '051-8888-1001', '1층 로비', TRUE, 9),
(8, '파라다이스부산 스파', '부산광역시 해운대구 중동 1408-5', '황스파팀장', '051-8888-1002', '지하1층 스파', TRUE, 9),

-- 롯데백화점 부산본점 (3개)
(9, '롯데 부산 1층', '부산광역시 부산진구 가야대로 772', '서매니저', '051-9999-1001', '1층 명품관', TRUE, 9),
(9, '롯데 부산 식품관', '부산광역시 부산진구 가야대로 772', '전팀장', '051-9999-1002', '지하1층 식품관', TRUE, 9),
(9, '롯데 부산 주차장', '부산광역시 부산진구 가야대로 772', '탁관리자', '051-9999-1003', '주차장 입구', TRUE, 9),

-- 대구신세계백화점 (2개)
(10, '신세계 대구 1층', '대구광역시 동구 동부로 149', '피매니저', '053-1000-1001', '1층 중앙홀', TRUE, 10),
(10, '신세계 대구 식품관', '대구광역시 동구 동부로 149', '명팀장', '053-1000-1002', '지하 식품관', TRUE, 10),

-- 나머지 고객들 각 1개씩
(11, '인터불고호텔 로비', '대구광역시 중구 동성로 141', '기로비매니저', '053-1100-1001', '1층 로비', TRUE, 10),
(12, '롯데시네마 대구점', '대구광역시 동구 팔공로 177', '목매니저', '053-1200-1001', '1층 입구', TRUE, 10),
(13, '광주신세계 1층', '광주광역시 서구 무진대로 932', '장팀장', '062-1300-1001', '1층 중앙', TRUE, 11),
(14, '김대중센터 로비', '광주광역시 서구 상무누리로 30', '육관리자', '062-1400-1001', '로비', TRUE, 11),
(15, '조선대병원 로비', '광주광역시 동구 필문대로 365', '추팀장', '062-1500-1001', '본관 로비', TRUE, 11),
(16, '대전컨벤션 로비', '대전광역시 유성구 엑스포로 107', '구매니저', '042-1600-1001', '중앙 로비', TRUE, 12),
(17, 'KAIST 본관', '대전광역시 유성구 대학로 291', '나총무팀', '042-1700-1001', '본관 1층', TRUE, 12),
(18, '갤러리아 대전점', '대전광역시 서구 대덕대로 211', '두매니저', '042-1800-1001', '1층 입구', TRUE, 12),
(19, '인천공항 T1', '인천광역시 중구 공항로 272', '류관리팀', '032-1900-1001', '출국장', TRUE, 13),
(20, '파라다이스시티 로비', '인천광역시 중구 영종해안남로 321', '봉지배인', '032-2000-1001', '호텔 로비', TRUE, 13);

-- ============================================================
-- 6. 카테고리(Category) - 4단계 구조, 30개
-- ============================================================

-- Level 1: 최상위 분류 (5개)
INSERT INTO `categories` (`parent_id`, `level`, `category_name`, `slug`, `description`, `display_order`, `is_active`, `created_by`) VALUES
(NULL, 1, '향 계열', 'scents', '향 카트리지 최상위 분류', 1, TRUE, 1),
(NULL, 1, '콘텐츠 유형', 'contents', '콘텐츠 최상위 분류', 2, TRUE, 1),
(NULL, 1, '디스펜서 타입', 'devices', '디스펜서 기기 분류', 3, TRUE, 1),
(NULL, 1, '부자재 종류', 'parts', '교체 부품 분류', 4, TRUE, 1),
(NULL, 1, '악세사리', 'accessories', '추가 상품 분류', 5, TRUE, 1);

-- Level 2: 향 계열 하위 (4개)
INSERT INTO `categories` (`parent_id`, `level`, `category_name`, `slug`, `description`, `display_order`, `is_active`, `created_by`) VALUES
(1, 2, 'Woody', 'woody', '우디 계열 향', 1, TRUE, 1),
(1, 2, 'Floral', 'floral', '플로랄 계열 향', 2, TRUE, 1),
(1, 2, 'Fruity', 'fruity', '프루티 계열 향', 3, TRUE, 1),
(1, 2, 'Green & Herb', 'green-herb', '그린 & 허브 계열', 4, TRUE, 1);

-- Level 3: Woody 하위 (3개)
INSERT INTO `categories` (`parent_id`, `level`, `category_name`, `slug`, `description`, `display_order`, `is_active`, `created_by`) VALUES
(6, 3, 'Pine (소나무)', 'pine', '소나무 향', 1, TRUE, 1),
(6, 3, 'Cedar (시더)', 'cedar', '시더우드 향', 2, TRUE, 1),
(6, 3, 'Sandalwood (샌달우드)', 'sandalwood', '샌달우드 향', 3, TRUE, 1);

-- Level 4: Pine 하위 (3개 - 강도별)
INSERT INTO `categories` (`parent_id`, `level`, `category_name`, `slug`, `description`, `display_order`, `is_active`, `created_by`) VALUES
(10, 4, '약함', 'pine-light', '은은한 소나무 향', 1, TRUE, 1),
(10, 4, '보통', 'pine-medium', '적당한 소나무 향', 2, TRUE, 1),
(10, 4, '강함', 'pine-strong', '진한 소나무 향', 3, TRUE, 1);

-- Level 3: Floral 하위 (2개)
INSERT INTO `categories` (`parent_id`, `level`, `category_name`, `slug`, `description`, `display_order`, `is_active`, `created_by`) VALUES
(7, 3, 'Lavender (라벤더)', 'lavender', '라벤더 향', 1, TRUE, 1),
(7, 3, 'Rose (장미)', 'rose', '장미 향', 2, TRUE, 1);

-- Level 4: Lavender 하위 (2개)
INSERT INTO `categories` (`parent_id`, `level`, `category_name`, `slug`, `description`, `display_order`, `is_active`, `created_by`) VALUES
(17, 4, '프렌치 라벤더', 'lavender-french', '프렌치 라벤더', 1, TRUE, 1),
(17, 4, '잉글리시 라벤더', 'lavender-english', '잉글리시 라벤더', 2, TRUE, 1);

-- Level 2: 콘텐츠 유형 하위 (3개)
INSERT INTO `categories` (`parent_id`, `level`, `category_name`, `slug`, `description`, `display_order`, `is_active`, `created_by`) VALUES
(2, 2, '계절별', 'seasonal', '계절 테마 콘텐츠', 1, TRUE, 1),
(2, 2, '테마별', 'themed', '특별 테마 콘텐츠', 2, TRUE, 1),
(2, 2, '프로모션', 'promotional', '홍보용 콘텐츠', 3, TRUE, 1);

-- Level 3: 계절별 하위 (4개)
INSERT INTO `categories` (`parent_id`, `level`, `category_name`, `slug`, `description`, `display_order`, `is_active`, `created_by`) VALUES
(22, 3, '봄', 'spring', '봄 시즌 콘텐츠', 1, TRUE, 1),
(22, 3, '여름', 'summer', '여름 시즌 콘텐츠', 2, TRUE, 1),
(22, 3, '가을', 'autumn', '가을 시즌 콘텐츠', 3, TRUE, 1),
(22, 3, '겨울', 'winter', '겨울 시즌 콘텐츠', 4, TRUE, 1);

-- Level 4: 봄 하위 (2개)
INSERT INTO `categories` (`parent_id`, `level`, `category_name`, `slug`, `description`, `display_order`, `is_active`, `created_by`) VALUES
(26, 4, '벚꽃', 'cherry-blossom', '벚꽃 테마', 1, TRUE, 1),
(26, 4, '새싹', 'sprout', '새싹 테마', 2, TRUE, 1);

-- Level 2: 디스펜서 타입 하위 (2개)
INSERT INTO `categories` (`parent_id`, `level`, `category_name`, `slug`, `description`, `display_order`, `is_active`, `created_by`) VALUES
(3, 2, '스탠드형', 'stand-type', '바닥 설치형', 1, TRUE, 1),
(3, 2, '벽부착형', 'wall-mount', '벽 부착형', 2, TRUE, 1);

-- 총 30개 카테고리 완성

-- ============================================================
-- 7. 태그(Tag) - 30개
-- ============================================================
INSERT INTO `tags` (`tag_name`, `slug`, `tag_type`, `color`, `description`, `is_active`) VALUES
-- 공통 태그 (10개)
('신상품', 'new-arrival', 'GENERAL', '#FF6B6B', '신규 출시 상품', TRUE),
('베스트셀러', 'best-seller', 'GENERAL', '#4ECDC4', '인기 상품', TRUE),
('한정판', 'limited-edition', 'GENERAL', '#95E1D3', '기간 한정', TRUE),
('프리미엄', 'premium', 'GENERAL', '#F38181', '고급 제품', TRUE),
('가성비', 'value', 'GENERAL', '#AA96DA', '가성비 좋은', TRUE),
('추천', 'recommended', 'GENERAL', '#FCBAD3', '추천 제품', TRUE),
('인기', 'popular', 'GENERAL', '#FFFFD2', '인기 제품', TRUE),
('세트상품', 'bundle', 'GENERAL', '#A8D8EA', '세트 구성', TRUE),
('이벤트', 'event', 'GENERAL', '#FFD166', '이벤트 상품', TRUE),
('품절임박', 'low-stock', 'GENERAL', '#EF476F', '재고 부족', TRUE),

-- 향 전용 태그 (10개)
('친환경', 'eco-friendly', 'SCENT', '#06D6A0', '천연 성분', TRUE),
('알러지프리', 'allergen-free', 'SCENT', '#118AB2', '알러지 유발 물질 없음', TRUE),
('강추천', 'highly-recommended', 'SCENT', '#073B4C', '강력 추천 향', TRUE),
('은은한', 'subtle', 'SCENT', '#FFD6BA', '은은한 향', TRUE),
('진한', 'strong', 'SCENT', '#E76F51', '진한 향', TRUE),
('상쾌한', 'refreshing', 'SCENT', '#2A9D8F', '상쾌한 느낌', TRUE),
('편안한', 'relaxing', 'SCENT', '#264653', '편안한 느낌', TRUE),
('고급스러운', 'luxurious', 'SCENT', '#E9C46A', '고급스러운 향', TRUE),
('자연향', 'natural', 'SCENT', '#F4A261', '자연 그대로', TRUE),
('계절향', 'seasonal', 'SCENT', '#E76F51', '계절 특별 향', TRUE),

-- 콘텐츠 전용 태그 (10개)
('미니멀', 'minimal', 'CONTENT', '#000000', '미니멀 디자인', TRUE),
('컬러풀', 'colorful', 'CONTENT', '#FF6B6B', '화려한 색상', TRUE),
('모던', 'modern', 'CONTENT', '#4ECDC4', '현대적 디자인', TRUE),
('클래식', 'classic', 'CONTENT', '#556B2F', '클래식 스타일', TRUE),
('감성적', 'emotional', 'CONTENT', '#DDA15E', '감성적 분위기', TRUE),
('비즈니스', 'business', 'CONTENT', '#283618', '비즈니스용', TRUE),
('축제', 'festival', 'CONTENT', '#BC6C25', '축제 분위기', TRUE),
('힐링', 'healing', 'CONTENT', '#FEFAE0', '힐링 테마', TRUE),
('레트로', 'retro', 'CONTENT', '#DDA15E', '레트로 스타일', TRUE),
('트렌디', 'trendy', 'CONTENT', '#BC6C25', '최신 트렌드', TRUE);

-- ============================================================
-- 8. 디스펜서(Device) 마스터 - 10개
-- ============================================================
INSERT INTO `devices` (`model_name`, `manufacturer`, `category_id`, `specifications`, `image_url`, `manual_url`, `is_active`, `created_by`) VALUES
('AllGreen Air Pro S1', 'All2Green Co.', 31, '{"크기":"30x30x150cm","무게":"3.5kg","전력":"220V 50/60Hz","용량":"200ml"}', 'https://cdn.all2green.com/devices/s1.jpg', 'https://cdn.all2green.com/manuals/s1.pdf', TRUE, 1),
('AllGreen Air Pro S2', 'All2Green Co.', 31, '{"크기":"35x35x160cm","무게":"4.0kg","전력":"220V 50/60Hz","용량":"300ml"}', 'https://cdn.all2green.com/devices/s2.jpg', 'https://cdn.all2green.com/manuals/s2.pdf', TRUE, 1),
('AllGreen Air Slim W1', 'All2Green Co.', 32, '{"크기":"20x10x40cm","무게":"1.5kg","전력":"220V 50/60Hz","용량":"100ml"}', 'https://cdn.all2green.com/devices/w1.jpg', 'https://cdn.all2green.com/manuals/w1.pdf', TRUE, 1),
('AllGreen Air Slim W2', 'All2Green Co.', 32, '{"크기":"25x12x45cm","무게":"1.8kg","전력":"220V 50/60Hz","용량":"150ml"}', 'https://cdn.all2green.com/devices/w2.jpg', 'https://cdn.all2green.com/manuals/w2.pdf', TRUE, 1),
('AllGreen Air Premium P1', 'All2Green Co.', 31, '{"크기":"40x40x180cm","무게":"5.0kg","전력":"220V 50/60Hz","용량":"500ml","IoT":"WiFi"}', 'https://cdn.all2green.com/devices/p1.jpg', 'https://cdn.all2green.com/manuals/p1.pdf', TRUE, 1),
('AllGreen Air Basic B1', 'All2Green Co.', 31, '{"크기":"28x28x140cm","무게":"3.0kg","전력":"220V 50/60Hz","용량":"150ml"}', 'https://cdn.all2green.com/devices/b1.jpg', 'https://cdn.all2green.com/manuals/b1.pdf', TRUE, 1),
('AllGreen Air Compact C1', 'All2Green Co.', 32, '{"크기":"18x8x35cm","무게":"1.2kg","전력":"220V 50/60Hz","용량":"80ml"}', 'https://cdn.all2green.com/devices/c1.jpg', 'https://cdn.all2green.com/manuals/c1.pdf', TRUE, 1),
('AllGreen Air Smart SM1', 'All2Green Co.', 31, '{"크기":"32x32x155cm","무게":"3.8kg","전력":"220V 50/60Hz","용량":"250ml","IoT":"WiFi,Bluetooth"}', 'https://cdn.all2green.com/devices/sm1.jpg', 'https://cdn.all2green.com/manuals/sm1.pdf', TRUE, 1),
('AllGreen Air Mini M1', 'All2Green Co.', 32, '{"크기":"15x8x30cm","무게":"1.0kg","전력":"USB 5V","용량":"50ml"}', 'https://cdn.all2green.com/devices/m1.jpg', 'https://cdn.all2green.com/manuals/m1.pdf', TRUE, 1),
('AllGreen Air Deluxe D1', 'All2Green Co.', 31, '{"크기":"38x38x170cm","무게":"4.5kg","전력":"220V 50/60Hz","용량":"400ml","IoT":"WiFi"}', 'https://cdn.all2green.com/devices/d1.jpg', 'https://cdn.all2green.com/manuals/d1.pdf', TRUE, 1);

-- ============================================================
-- 9. 디스펜서 시리얼(Serial) - 50개
-- ============================================================
INSERT INTO `device_serials` (`device_id`, `serial_number`, `qr_code`, `manufacture_date`, `import_date`, `status`, `created_by`) VALUES
-- AllGreen Air Pro S1 (10개)
(1, 'AG-S1-2024-0001', 'QR-AG-S1-2024-0001', '2024-01-10', '2024-01-20', 'ASSIGNED', 1),
(1, 'AG-S1-2024-0002', 'QR-AG-S1-2024-0002', '2024-01-10', '2024-01-20', 'ASSIGNED', 1),
(1, 'AG-S1-2024-0003', 'QR-AG-S1-2024-0003', '2024-01-10', '2024-01-20', 'ASSIGNED', 1),
(1, 'AG-S1-2024-0004', 'QR-AG-S1-2024-0004', '2024-01-15', '2024-01-25', 'ASSIGNED', 1),
(1, 'AG-S1-2024-0005', 'QR-AG-S1-2024-0005', '2024-01-15', '2024-01-25', 'ASSIGNED', 1),
(1, 'AG-S1-2024-0006', 'QR-AG-S1-2024-0006', '2024-02-01', '2024-02-10', 'ASSIGNED', 1),
(1, 'AG-S1-2024-0007', 'QR-AG-S1-2024-0007', '2024-02-01', '2024-02-10', 'ASSIGNED', 1),
(1, 'AG-S1-2024-0008', 'QR-AG-S1-2024-0008', '2024-02-15', '2024-02-25', 'ASSIGNED', 1),
(1, 'AG-S1-2024-0009', 'QR-AG-S1-2024-0009', '2024-02-15', '2024-02-25', 'AVAILABLE', 1),
(1, 'AG-S1-2024-0010', 'QR-AG-S1-2024-0010', '2024-03-01', '2024-03-10', 'AVAILABLE', 1),

-- AllGreen Air Pro S2 (10개)
(2, 'AG-S2-2024-0001', 'QR-AG-S2-2024-0001', '2024-01-20', '2024-02-01', 'ASSIGNED', 1),
(2, 'AG-S2-2024-0002', 'QR-AG-S2-2024-0002', '2024-01-20', '2024-02-01', 'ASSIGNED', 1),
(2, 'AG-S2-2024-0003', 'QR-AG-S2-2024-0003', '2024-02-05', '2024-02-15', 'ASSIGNED', 1),
(2, 'AG-S2-2024-0004', 'QR-AG-S2-2024-0004', '2024-02-05', '2024-02-15', 'ASSIGNED', 1),
(2, 'AG-S2-2024-0005', 'QR-AG-S2-2024-0005', '2024-02-20', '2024-03-01', 'ASSIGNED', 1),
(2, 'AG-S2-2024-0006', 'QR-AG-S2-2024-0006', '2024-02-20', '2024-03-01', 'ASSIGNED', 1),
(2, 'AG-S2-2024-0007', 'QR-AG-S2-2024-0007', '2024-03-05', '2024-03-15', 'ASSIGNED', 1),
(2, 'AG-S2-2024-0008', 'QR-AG-S2-2024-0008', '2024-03-05', '2024-03-15', 'AVAILABLE', 1),
(2, 'AG-S2-2024-0009', 'QR-AG-S2-2024-0009', '2024-03-20', '2024-03-30', 'AVAILABLE', 1),
(2, 'AG-S2-2024-0010', 'QR-AG-S2-2024-0010', '2024-03-20', '2024-03-30', 'AVAILABLE', 1),

-- AllGreen Air Slim W1 (10개)
(3, 'AG-W1-2024-0001', 'QR-AG-W1-2024-0001', '2024-02-10', '2024-02-20', 'ASSIGNED', 1),
(3, 'AG-W1-2024-0002', 'QR-AG-W1-2024-0002', '2024-02-10', '2024-02-20', 'ASSIGNED', 1),
(3, 'AG-W1-2024-0003', 'QR-AG-W1-2024-0003', '2024-02-25', '2024-03-05', 'ASSIGNED', 1),
(3, 'AG-W1-2024-0004', 'QR-AG-W1-2024-0004', '2024-02-25', '2024-03-05', 'ASSIGNED', 1),
(3, 'AG-W1-2024-0005', 'QR-AG-W1-2024-0005', '2024-03-10', '2024-03-20', 'ASSIGNED', 1),
(3, 'AG-W1-2024-0006', 'QR-AG-W1-2024-0006', '2024-03-10', '2024-03-20', 'ASSIGNED', 1),
(3, 'AG-W1-2024-0007', 'QR-AG-W1-2024-0007', '2024-03-25', '2024-04-05', 'ASSIGNED', 1),
(3, 'AG-W1-2024-0008', 'QR-AG-W1-2024-0008', '2024-03-25', '2024-04-05', 'ASSIGNED', 1),
(3, 'AG-W1-2024-0009', 'QR-AG-W1-2024-0009', '2024-04-10', '2024-04-20', 'AVAILABLE', 1),
(3, 'AG-W1-2024-0010', 'QR-AG-W1-2024-0010', '2024-04-10', '2024-04-20', 'AVAILABLE', 1),

-- AllGreen Air Premium P1 (10개)
(5, 'AG-P1-2024-0001', 'QR-AG-P1-2024-0001', '2024-03-15', '2024-03-25', 'ASSIGNED', 1),
(5, 'AG-P1-2024-0002', 'QR-AG-P1-2024-0002', '2024-03-15', '2024-03-25', 'ASSIGNED', 1),
(5, 'AG-P1-2024-0003', 'QR-AG-P1-2024-0003', '2024-04-01', '2024-04-10', 'ASSIGNED', 1),
(5, 'AG-P1-2024-0004', 'QR-AG-P1-2024-0004', '2024-04-01', '2024-04-10', 'ASSIGNED', 1),
(5, 'AG-P1-2024-0005', 'QR-AG-P1-2024-0005', '2024-04-15', '2024-04-25', 'ASSIGNED', 1),
(5, 'AG-P1-2024-0006', 'QR-AG-P1-2024-0006', '2024-04-15', '2024-04-25', 'ASSIGNED', 1),
(5, 'AG-P1-2024-0007', 'QR-AG-P1-2024-0007', '2024-05-01', '2024-05-10', 'ASSIGNED', 1),
(5, 'AG-P1-2024-0008', 'QR-AG-P1-2024-0008', '2024-05-01', '2024-05-10', 'ASSIGNED', 1),
(5, 'AG-P1-2024-0009', 'QR-AG-P1-2024-0009', '2024-05-15', '2024-05-25', 'AVAILABLE', 1),
(5, 'AG-P1-2024-0010', 'QR-AG-P1-2024-0010', '2024-05-15', '2024-05-25', 'AVAILABLE', 1),

-- AllGreen Air Basic B1 (10개)
(6, 'AG-B1-2024-0001', 'QR-AG-B1-2024-0001', '2024-01-25', '2024-02-05', 'ASSIGNED', 1),
(6, 'AG-B1-2024-0002', 'QR-AG-B1-2024-0002', '2024-01-25', '2024-02-05', 'ASSIGNED', 1),
(6, 'AG-B1-2024-0003', 'QR-AG-B1-2024-0003', '2024-02-10', '2024-02-20', 'ASSIGNED', 1),
(6, 'AG-B1-2024-0004', 'QR-AG-B1-2024-0004', '2024-02-10', '2024-02-20', 'ASSIGNED', 1),
(6, 'AG-B1-2024-0005', 'QR-AG-B1-2024-0005', '2024-02-25', '2024-03-05', 'ASSIGNED', 1),
(6, 'AG-B1-2024-0006', 'QR-AG-B1-2024-0006', '2024-02-25', '2024-03-05', 'ASSIGNED', 1),
(6, 'AG-B1-2024-0007', 'QR-AG-B1-2024-0007', '2024-03-10', '2024-03-20', 'ASSIGNED', 1),
(6, 'AG-B1-2024-0008', 'QR-AG-B1-2024-0008', '2024-03-10', '2024-03-20', 'ASSIGNED', 1),
(6, 'AG-B1-2024-0009', 'QR-AG-B1-2024-0009', '2024-03-25', '2024-04-05', 'AVAILABLE', 1),
(6, 'AG-B1-2024-0010', 'QR-AG-B1-2024-0010', '2024-03-25', '2024-04-05', 'AVAILABLE', 1);

-- ============================================================
-- 10. 디스펜서 배정(Assignment) - 40개
-- ============================================================
INSERT INTO `device_assignments` (`serial_id`, `customer_id`, `site_id`, `assigned_date`, `installation_location`, `status`, `created_by`) VALUES
-- 스타벅스 (5개 지점에 각 1대씩)
(1, 1, 1, '2024-02-01', '1층 로비 중앙 카운터 옆', 'ACTIVE', 7),
(2, 1, 2, '2024-02-01', '1층 입구 오른쪽 벽면', 'ACTIVE', 7),
(3, 1, 3, '2024-02-05', '2층 계단 상단 왼쪽', 'ACTIVE', 7),
(4, 1, 4, '2024-02-05', '1층 카운터 뒤 중앙', 'ACTIVE', 7),
(5, 1, 5, '2024-02-10', 'B1층 중앙홀 기둥 옆', 'ACTIVE', 7),

-- 롯데호텔서울 (3개 구역)
(6, 2, 6, '2024-02-15', '메인 로비 안내데스크 우측', 'ACTIVE', 7),
(7, 2, 7, '2024-02-15', '2층 연회장 입구 좌측', 'ACTIVE', 7),
(8, 2, 8, '2024-02-20', '3층 비즈니스센터 입구', 'ACTIVE', 7),

-- 신세계백화점 강남점 (4개 층)
(11, 3, 9, '2024-02-25', '1층 화장품 코너 중앙 기둥', 'ACTIVE', 7),
(12, 3, 10, '2024-02-25', 'B1층 식품관 입구 우측', 'ACTIVE', 7),
(13, 3, 11, '2024-03-01', '6층 레스토랑가 중앙홀', 'ACTIVE', 7),
(14, 3, 12, '2024-03-01', '지하 주차장 엘리베이터 앞', 'ACTIVE', 7),

-- 현대백화점 판교점 (3개 구역)
(15, 4, 13, '2024-03-05', '본관 1층 정문 중앙', 'ACTIVE', 8),
(16, 4, 14, '2024-03-05', '식품관 중앙 통로', 'ACTIVE', 8),
(17, 4, 15, '2024-03-10', '지하 주차장 입구', 'ACTIVE', 8),

-- 올투그린 (2개 현장)
(21, 5, 16, '2024-03-15', '본사 1층 로비 중앙', 'ACTIVE', 8),
(22, 5, 17, '2024-03-15', 'R&D센터 연구동 1층 입구', 'ACTIVE', 8),

-- 삼성전자 수원사옥 (3개 건물)
(23, 6, 18, '2024-03-20', '제1공장 로비 중앙', 'ACTIVE', 8),
(24, 6, 19, '2024-03-20', '본관 로비 안내데스크 옆', 'ACTIVE', 8),
(25, 6, 20, '2024-03-25', '구내식당 입구 우측', 'ACTIVE', 8),

-- 해운대그랜드호텔 (2개)
(31, 7, 21, '2024-04-01', '메인 로비 중앙홀', 'ACTIVE', 9),
(32, 7, 22, '2024-04-01', '2층 연회장 입구', 'ACTIVE', 9),

-- 파라다이스호텔부산 (2개)
(33, 8, 23, '2024-04-05', '1층 로비 안내데스크 앞', 'ACTIVE', 9),
(34, 8, 24, '2024-04-05', '지하1층 스파 입구', 'ACTIVE', 9),

-- 롯데백화점 부산본점 (3개)
(35, 9, 25, '2024-04-10', '1층 명품관 중앙', 'ACTIVE', 9),
(36, 9, 26, '2024-04-10', '지하1층 식품관 입구', 'ACTIVE', 9),
(37, 9, 27, '2024-04-15', '주차장 입구 좌측', 'ACTIVE', 9),

-- 대구신세계백화점 (2개)
(41, 10, 28, '2024-04-20', '1층 중앙홀 기둥 옆', 'ACTIVE', 10),
(42, 10, 29, '2024-04-20', '지하 식품관 중앙 통로', 'ACTIVE', 10),

-- 나머지 고객들
(43, 11, 30, '2024-04-25', '호텔 1층 로비 중앙', 'ACTIVE', 10),
(44, 12, 31, '2024-04-30', '영화관 1층 입구 우측', 'ACTIVE', 10),
(45, 13, 32, '2024-05-05', '백화점 1층 중앙 홀', 'ACTIVE', 11),
(46, 14, 33, '2024-05-10', '컨벤션센터 로비 중앙', 'ACTIVE', 11),
(47, 15, 34, '2024-05-15', '병원 본관 로비 안내데스크 옆', 'ACTIVE', 11),
(48, 16, 35, '2024-05-20', '컨벤션센터 중앙 로비', 'ACTIVE', 12),
(21, 17, 36, '2024-05-25', 'KAIST 본관 1층 중앙홀', 'ACTIVE', 12),
(22, 18, 37, '2024-05-30', '백화점 1층 입구', 'ACTIVE', 12),
(23, 19, 38, '2024-06-01', '공항 출국장 중앙', 'ACTIVE', 13),
(24, 20, 39, '2024-06-05', '호텔 로비 중앙홀', 'ACTIVE', 13);

-- ============================================================
-- 11. 향 카트리지(Scent) - 30개
-- ============================================================
INSERT INTO `scents` (`category_id`, `scent_name`, `scent_family`, `description`, `capacity_ml`, `price`, `image_url`, `ingredients`, `is_allergen_free`, `is_eco_friendly`, `stock_quantity`, `is_active`, `created_by`) VALUES
-- Woody 계열 (10개)
(13, '소나무 피톤치드 약함', 'Woody', '은은한 소나무 향으로 산속의 편안함을 느낄 수 있습니다', 50, 35000, 'https://cdn.all2green.com/scents/pine-light.jpg', '소나무 정유, 피톤치드 추출물', TRUE, TRUE, 150, TRUE, 1),
(14, '소나무 피톤치드 보통', 'Woody', '적당한 소나무 향으로 자연의 활력을 선사합니다', 50, 37000, 'https://cdn.all2green.com/scents/pine-medium.jpg', '소나무 정유, 피톤치드 추출물', TRUE, TRUE, 200, TRUE, 1),
(15, '소나무 피톤치드 강함', 'Woody', '진한 소나무 향으로 강렬한 숲의 기운을 느낄 수 있습니다', 50, 39000, 'https://cdn.all2green.com/scents/pine-strong.jpg', '소나무 정유, 피톤치드 추출물', TRUE, TRUE, 180, TRUE, 1),
(11, '시더우드 클래식', 'Woody', '고급스러운 시더우드 향', 50, 42000, 'https://cdn.all2green.com/scents/cedar-classic.jpg', '시더우드 정유, 천연 향료', TRUE, TRUE, 120, TRUE, 1),
(11, '시더우드 모던', 'Woody', '현대적으로 재해석한 시더우드', 50, 45000, 'https://cdn.all2green.com/scents/cedar-modern.jpg', '시더우드 정유, 시트러스 노트', TRUE, TRUE, 100, TRUE, 1),
(12, '샌달우드 프리미엄', 'Woody', '명품 샌달우드의 깊고 풍부한 향', 50, 55000, 'https://cdn.all2green.com/scents/sandalwood-premium.jpg', '인도산 샌달우드 정유', TRUE, TRUE, 80, TRUE, 1),
(12, '샌달우드 스탠다드', 'Woody', '편안한 샌달우드 향', 50, 48000, 'https://cdn.all2green.com/scents/sandalwood-standard.jpg', '샌달우드 정유, 천연 향료', TRUE, TRUE, 110, TRUE, 1),
(10, '파인 앤 머스크', 'Woody', '소나무와 머스크의 조화', 50, 40000, 'https://cdn.all2green.com/scents/pine-musk.jpg', '소나무 정유, 화이트 머스크', TRUE, FALSE, 130, TRUE, 1),
(11, '시더 앤 민트', 'Woody', '시원한 민트가 더해진 시더우드', 50, 43000, 'https://cdn.all2green.com/scents/cedar-mint.jpg', '시더우드, 페퍼민트 정유', TRUE, TRUE, 95, TRUE, 1),
(12, '샌달우드 로즈', 'Woody', '우아한 장미가 어우러진 샌달우드', 50, 52000, 'https://cdn.all2green.com/scents/sandalwood-rose.jpg', '샌달우드, 불가리안 로즈', TRUE, TRUE, 75, TRUE, 1),

-- Floral 계열 (10개)
(19, '프렌치 라벤더 퓨어', 'Floral', '프로방스의 순수한 라벤더 향', 50, 38000, 'https://cdn.all2green.com/scents/lavender-french.jpg', '프렌치 라벤더 정유', TRUE, TRUE, 170, TRUE, 1),
(20, '잉글리시 라벤더 클래식', 'Floral', '영국식 전통 라벤더 향', 50, 40000, 'https://cdn.all2green.com/scents/lavender-english.jpg', '잉글리시 라벤더 정유', TRUE, TRUE, 160, TRUE, 1),
(18, '로즈 가든', 'Floral', '만개한 장미 정원의 향기', 50, 47000, 'https://cdn.all2green.com/scents/rose-garden.jpg', '불가리안 로즈, 다마스크 로즈', TRUE, TRUE, 90, TRUE, 1),
(18, '화이트 로즈', 'Floral', '청순한 화이트 로즈 향', 50, 45000, 'https://cdn.all2green.com/scents/rose-white.jpg', '화이트 로즈 정유', TRUE, TRUE, 100, TRUE, 1),
(17, '라벤더 앤 카모마일', 'Floral', '편안한 휴식을 위한 블렌드', 50, 42000, 'https://cdn.all2green.com/scents/lavender-chamomile.jpg', '라벤더, 카모마일 정유', TRUE, TRUE, 140, TRUE, 1),
(17, '라벤더 민트', 'Floral', '시원하고 상쾌한 라벤더', 50, 41000, 'https://cdn.all2green.com/scents/lavender-mint.jpg', '라벤더, 페퍼민트', TRUE, TRUE, 125, TRUE, 1),
(18, '로즈 앤 오렌지', 'Floral', '달콤한 장미와 상큼한 오렌지', 50, 46000, 'https://cdn.all2green.com/scents/rose-orange.jpg', '로즈, 스위트 오렌지', TRUE, TRUE, 85, TRUE, 1),
(19, '라벤더 바닐라', 'Floral', '포근한 바닐라가 어우러진 라벤더', 50, 44000, 'https://cdn.all2green.com/scents/lavender-vanilla.jpg', '라벤더, 바닐라 앱솔루트', TRUE, FALSE, 105, TRUE, 1),
(20, '잉글리시 가든', 'Floral', '영국식 정원의 다채로운 꽃향기', 50, 48000, 'https://cdn.all2green.com/scents/english-garden.jpg', '라벤더, 로즈, 제라늄', TRUE, TRUE, 95, TRUE, 1),
(17, '프로방스 블렌드', 'Floral', '프로방스 허브 정원의 향', 50, 43000, 'https://cdn.all2green.com/scents/provence-blend.jpg', '라벤더, 로즈마리, 타임', TRUE, TRUE, 110, TRUE, 1),

-- Fruity & Green 계열 (10개)
(8, '시트러스 프레시', 'Fruity', '상쾌한 시트러스의 활력', 50, 36000, 'https://cdn.all2green.com/scents/citrus-fresh.jpg', '오렌지, 레몬, 그레이프프루트', TRUE, TRUE, 190, TRUE, 1),
(8, '레몬 그라스', 'Green & Herb', '상큼한 레몬그라스 향', 50, 34000, 'https://cdn.all2green.com/scents/lemongrass.jpg', '레몬그라스 정유', TRUE, TRUE, 200, TRUE, 1),
(9, '그린 티', 'Green & Herb', '은은한 녹차의 향', 50, 37000, 'https://cdn.all2green.com/scents/green-tea.jpg', '녹차 추출물, 천연 향료', TRUE, TRUE, 150, TRUE, 1),
(9, '유칼립투스 민트', 'Green & Herb', '시원한 유칼립투스와 민트', 50, 35000, 'https://cdn.all2green.com/scents/eucalyptus-mint.jpg', '유칼립투스, 페퍼민트', TRUE, TRUE, 160, TRUE, 1),
(8, '베르가못 얼그레이', 'Fruity', '우아한 베르가못 향', 50, 39000, 'https://cdn.all2green.com/scents/bergamot.jpg', '베르가못 정유', TRUE, TRUE, 140, TRUE, 1),
(9, '로즈마리 허브', 'Green & Herb', '상쾌한 로즈마리 향', 50, 33000, 'https://cdn.all2green.com/scents/rosemary.jpg', '로즈마리 정유', TRUE, TRUE, 170, TRUE, 1),
(8, '오렌지 블라썸', 'Fruity', '달콤한 오렌지 꽃향기', 50, 38000, 'https://cdn.all2green.com/scents/orange-blossom.jpg', '오렌지 블라썸 정유', TRUE, TRUE, 130, TRUE, 1),
(9, '바질 민트', 'Green & Herb', '허브 정원의 신선함', 50, 32000, 'https://cdn.all2green.com/scents/basil-mint.jpg', '스위트 바질, 민트', TRUE, TRUE, 145, TRUE, 1),
(8, '자몽 자스민', 'Fruity', '상큼함과 우아함의 조화', 50, 41000, 'https://cdn.all2green.com/scents/grapefruit-jasmine.jpg', '자몽, 자스민', TRUE, TRUE, 115, TRUE, 1),
(9, '그린 포레스트', 'Green & Herb', '초록 숲의 생명력', 50, 36000, 'https://cdn.all2green.com/scents/green-forest.jpg', '사이프러스, 주니퍼베리, 파인', TRUE, TRUE, 155, TRUE, 1);

-- ============================================================
-- 12. 콘텐츠(Content) - 30개
-- ============================================================
INSERT INTO `contents` (`category_id`, `content_title`, `description`, `template_type`, `image_url`, `thumbnail_url`, `file_url`, `size`, `owner_type`, `is_free`, `is_active`, `created_by`) VALUES
-- 계절별 - 봄 (5개)
(28, '벚꽃 만개', '벚꽃이 만개한 봄날의 설렘', 'SEASONAL', 'https://cdn.all2green.com/contents/cherry-full.jpg', 'https://cdn.all2green.com/contents/thumb/cherry-full.jpg', 'https://cdn.all2green.com/contents/pdf/cherry-full.pdf', 'A4', 'COMPANY', TRUE, TRUE, 1),
(28, '봄날의 소풍', '봄나들이의 즐거움', 'SEASONAL', 'https://cdn.all2green.com/contents/spring-picnic.jpg', 'https://cdn.all2green.com/contents/thumb/spring-picnic.jpg', 'https://cdn.all2green.com/contents/pdf/spring-picnic.pdf', 'A4', 'COMPANY', TRUE, TRUE, 1),
(29, '새싹의 희망', '새로운 시작의 메시지', 'SEASONAL', 'https://cdn.all2green.com/contents/sprout-hope.jpg', 'https://cdn.all2green.com/contents/thumb/sprout-hope.jpg', 'https://cdn.all2green.com/contents/pdf/sprout-hope.pdf', 'A4', 'COMPANY', TRUE, TRUE, 1),
(28, '벚꽃 엔딩', '벚꽃 눈송이가 흩날리는 순간', 'SEASONAL', 'https://cdn.all2green.com/contents/cherry-ending.jpg', 'https://cdn.all2green.com/contents/thumb/cherry-ending.jpg', 'https://cdn.all2green.com/contents/pdf/cherry-ending.pdf', 'A4', 'COMPANY', TRUE, TRUE, 1),
(29, '봄의 향기', '봄바람에 실려오는 꽃향기', 'SEASONAL', 'https://cdn.all2green.com/contents/spring-scent.jpg', 'https://cdn.all2green.com/contents/thumb/spring-scent.jpg', 'https://cdn.all2green.com/contents/pdf/spring-scent.pdf', 'A4', 'COMPANY', TRUE, TRUE, 1),

-- 계절별 - 여름 (5개)
(27, '여름 바캉스', '시원한 여름 휴가의 설렘', 'SEASONAL', 'https://cdn.all2green.com/contents/summer-vacation.jpg', 'https://cdn.all2green.com/contents/thumb/summer-vacation.jpg', 'https://cdn.all2green.com/contents/pdf/summer-vacation.pdf', 'A4', 'COMPANY', TRUE, TRUE, 1),
(27, '열대야 청량', '무더위를 식혀주는 시원함', 'SEASONAL', 'https://cdn.all2green.com/contents/tropical-night.jpg', 'https://cdn.all2green.com/contents/thumb/tropical-night.jpg', 'https://cdn.all2green.com/contents/pdf/tropical-night.pdf', 'A4', 'COMPANY', TRUE, TRUE, 1),
(27, '해변의 추억', '파도 소리와 함께하는 여름', 'SEASONAL', 'https://cdn.all2green.com/contents/beach-memory.jpg', 'https://cdn.all2green.com/contents/thumb/beach-memory.jpg', 'https://cdn.all2green.com/contents/pdf/beach-memory.pdf', 'A4', 'COMPANY', TRUE, TRUE, 1),
(27, '여름밤 축제', '불꽃놀이와 함께하는 축제', 'SEASONAL', 'https://cdn.all2green.com/contents/summer-festival.jpg', 'https://cdn.all2green.com/contents/thumb/summer-festival.jpg', 'https://cdn.all2green.com/contents/pdf/summer-festival.pdf', 'A4', 'COMPANY', TRUE, TRUE, 1),
(27, '트로피컬 파라다이스', '열대 과일의 싱그러움', 'SEASONAL', 'https://cdn.all2green.com/contents/tropical-paradise.jpg', 'https://cdn.all2green.com/contents/thumb/tropical-paradise.jpg', 'https://cdn.all2green.com/contents/pdf/tropical-paradise.pdf', 'A4', 'COMPANY', TRUE, TRUE, 1),

-- 계절별 - 가을 (5개)
(26, '단풍 물결', '가을 단풍의 아름다움', 'SEASONAL', 'https://cdn.all2green.com/contents/autumn-leaves.jpg', 'https://cdn.all2green.com/contents/thumb/autumn-leaves.jpg', 'https://cdn.all2green.com/contents/pdf/autumn-leaves.pdf', 'A4', 'COMPANY', TRUE, TRUE, 1),
(26, '풍요로운 수확', '가을 수확의 기쁨', 'SEASONAL', 'https://cdn.all2green.com/contents/harvest-season.jpg', 'https://cdn.all2green.com/contents/thumb/harvest-season.jpg', 'https://cdn.all2green.com/contents/pdf/harvest-season.pdf', 'A4', 'COMPANY', TRUE, TRUE, 1),
(26, '가을 햇살', '따뜻한 가을 햇살', 'SEASONAL', 'https://cdn.all2green.com/contents/autumn-sunshine.jpg', 'https://cdn.all2green.com/contents/thumb/autumn-sunshine.jpg', 'https://cdn.all2green.com/contents/pdf/autumn-sunshine.pdf', 'A4', 'COMPANY', TRUE, TRUE, 1),
(26, '코스모스 물결', '코스모스가 흐드러진 가을', 'SEASONAL', 'https://cdn.all2green.com/contents/cosmos-wave.jpg', 'https://cdn.all2green.com/contents/thumb/cosmos-wave.jpg', 'https://cdn.all2green.com/contents/pdf/cosmos-wave.pdf', 'A4', 'COMPANY', TRUE, TRUE, 1),
(26, '가을 감성', '낙엽 지는 감성적인 가을', 'SEASONAL', 'https://cdn.all2green.com/contents/autumn-mood.jpg', 'https://cdn.all2green.com/contents/thumb/autumn-mood.jpg', 'https://cdn.all2green.com/contents/pdf/autumn-mood.pdf', 'A4', 'COMPANY', TRUE, TRUE, 1),

-- 계절별 - 겨울 (5개)
(25, '크리스마스 캐럴', '따뜻한 크리스마스 감성', 'SEASONAL', 'https://cdn.all2green.com/contents/christmas-carol.jpg', 'https://cdn.all2green.com/contents/thumb/christmas-carol.jpg', 'https://cdn.all2green.com/contents/pdf/christmas-carol.pdf', 'A4', 'COMPANY', TRUE, TRUE, 1),
(25, '새해 복 많이', '새해 인사 메시지', 'SEASONAL', 'https://cdn.all2green.com/contents/happy-new-year.jpg', 'https://cdn.all2green.com/contents/thumb/happy-new-year.jpg', 'https://cdn.all2green.com/contents/pdf/happy-new-year.pdf', 'A4', 'COMPANY', TRUE, TRUE, 1),
(25, '화이트 윈터', '순백의 겨울 풍경', 'SEASONAL', 'https://cdn.all2green.com/contents/white-winter.jpg', 'https://cdn.all2green.com/contents/thumb/white-winter.jpg', 'https://cdn.all2green.com/contents/pdf/white-winter.pdf', 'A4', 'COMPANY', TRUE, TRUE, 1),
(25, '따뜻한 겨울', '포근함이 느껴지는 겨울', 'SEASONAL', 'https://cdn.all2green.com/contents/warm-winter.jpg', 'https://cdn.all2green.com/contents/thumb/warm-winter.jpg', 'https://cdn.all2green.com/contents/pdf/warm-winter.pdf', 'A4', 'COMPANY', TRUE, TRUE, 1),
(25, '겨울 휴식', '휴식이 필요한 겨울', 'SEASONAL', 'https://cdn.all2green.com/contents/winter-rest.jpg', 'https://cdn.all2green.com/contents/thumb/winter-rest.jpg', 'https://cdn.all2green.com/contents/pdf/winter-rest.pdf', 'A4', 'COMPANY', TRUE, TRUE, 1),

-- 테마별 (5개)
(23, '비즈니스 프로페셔널', '전문적인 비즈니스 공간', 'BASIC', 'https://cdn.all2green.com/contents/business-pro.jpg', 'https://cdn.all2green.com/contents/thumb/business-pro.jpg', 'https://cdn.all2green.com/contents/pdf/business-pro.pdf', 'A4', 'COMPANY', TRUE, TRUE, 1),
(23, '힐링 스파', '편안한 휴식 공간', 'BASIC', 'https://cdn.all2green.com/contents/healing-spa.jpg', 'https://cdn.all2green.com/contents/thumb/healing-spa.jpg', 'https://cdn.all2green.com/contents/pdf/healing-spa.pdf', 'A4', 'COMPANY', TRUE, TRUE, 1),
(23, '럭셔리 라이프', '고급스러운 분위기', 'BASIC', 'https://cdn.all2green.com/contents/luxury-life.jpg', 'https://cdn.all2green.com/contents/thumb/luxury-life.jpg', 'https://cdn.all2green.com/contents/pdf/luxury-life.pdf', 'A4', 'COMPANY', TRUE, TRUE, 1),
(23, '내추럴 라이프', '자연 친화적 공간', 'BASIC', 'https://cdn.all2green.com/contents/natural-life.jpg', 'https://cdn.all2green.com/contents/thumb/natural-life.jpg', 'https://cdn.all2green.com/contents/pdf/natural-life.pdf', 'A4', 'COMPANY', TRUE, TRUE, 1),
(23, '모던 미니멀', '현대적이고 심플한 디자인', 'BASIC', 'https://cdn.all2green.com/contents/modern-minimal.jpg', 'https://cdn.all2green.com/contents/thumb/modern-minimal.jpg', 'https://cdn.all2green.com/contents/pdf/modern-minimal.pdf', 'A4', 'COMPANY', TRUE, TRUE, 1),

-- 프로모션 (5개)
(24, '그랜드 오픈', '신규 오픈 축하 메시지', 'PROMOTIONAL', 'https://cdn.all2green.com/contents/grand-opening.jpg', 'https://cdn.all2green.com/contents/thumb/grand-opening.jpg', 'https://cdn.all2green.com/contents/pdf/grand-opening.pdf', 'A4', 'COMPANY', TRUE, TRUE, 1),
(24, '주년 기념', '기념일 축하 디자인', 'PROMOTIONAL', 'https://cdn.all2green.com/contents/anniversary.jpg', 'https://cdn.all2green.com/contents/thumb/anniversary.jpg', 'https://cdn.all2green.com/contents/pdf/anniversary.pdf', 'A4', 'COMPANY', TRUE, TRUE, 1),
(24, '감사 인사', '고객 감사 메시지', 'PROMOTIONAL', 'https://cdn.all2green.com/contents/thank-you.jpg', 'https://cdn.all2green.com/contents/thumb/thank-you.jpg', 'https://cdn.all2green.com/contents/pdf/thank-you.pdf', 'A4', 'COMPANY', TRUE, TRUE, 1),
(24, '특별 할인', '프로모션 안내', 'PROMOTIONAL', 'https://cdn.all2green.com/contents/special-sale.jpg', 'https://cdn.all2green.com/contents/thumb/special-sale.jpg', 'https://cdn.all2green.com/contents/pdf/special-sale.pdf', 'A4', 'COMPANY', TRUE, TRUE, 1),
(24, '환경보호', '친환경 메시지', 'PROMOTIONAL', 'https://cdn.all2green.com/contents/eco-message.jpg', 'https://cdn.all2green.com/contents/thumb/eco-message.jpg', 'https://cdn.all2green.com/contents/pdf/eco-message.pdf', 'A4', 'COMPANY', TRUE, TRUE, 1);

-- ============================================================
-- 13. 구독(Subscription) - 30개
-- ============================================================
INSERT INTO `subscriptions` (`customer_id`, `site_id`, `subscription_number`, `start_date`, `end_date`, `status`, `monthly_fee`, `cycle_months`, `billing_day`, `next_cycle_date`, `created_by`) VALUES
(1, 1, 'SUB-2024-0001', '2024-02-01', '2025-01-31', 'ACTIVE', 29700.00, 2, 1, '2025-12-01', 7),
(1, 2, 'SUB-2024-0002', '2024-02-01', '2025-01-31', 'ACTIVE', 29700.00, 2, 1, '2025-12-01', 7),
(1, 3, 'SUB-2024-0003', '2024-02-05', '2025-02-04', 'ACTIVE', 29700.00, 2, 5, '2025-12-05', 7),
(1, 4, 'SUB-2024-0004', '2024-02-05', '2025-02-04', 'ACTIVE', 29700.00, 2, 5, '2025-12-05', 7),
(1, 5, 'SUB-2024-0005', '2024-02-10', '2025-02-09', 'ACTIVE', 29700.00, 2, 10, '2025-12-10', 7),
(2, 6, 'SUB-2024-0006', '2024-02-15', '2025-02-14', 'ACTIVE', 29700.00, 2, 15, '2025-12-15', 7),
(2, 7, 'SUB-2024-0007', '2024-02-15', '2025-02-14', 'ACTIVE', 29700.00, 2, 15, '2025-12-15', 7),
(2, 8, 'SUB-2024-0008', '2024-02-20', '2025-02-19', 'ACTIVE', 29700.00, 2, 20, '2025-12-20', 7),
(3, 9, 'SUB-2024-0009', '2024-02-25', '2025-02-24', 'ACTIVE', 29700.00, 2, 25, '2025-12-25', 7),
(3, 10, 'SUB-2024-0010', '2024-02-25', '2025-02-24', 'ACTIVE', 29700.00, 2, 25, '2025-12-25', 7),
(3, 11, 'SUB-2024-0011', '2024-03-01', '2025-02-28', 'ACTIVE', 29700.00, 2, 1, '2025-11-01', 7),
(3, 12, 'SUB-2024-0012', '2024-03-01', '2025-02-28', 'ACTIVE', 29700.00, 2, 1, '2025-11-01', 7),
(4, 13, 'SUB-2024-0013', '2024-03-05', '2025-03-04', 'ACTIVE', 29700.00, 2, 5, '2025-11-05', 8),
(4, 14, 'SUB-2024-0014', '2024-03-05', '2025-03-04', 'ACTIVE', 29700.00, 2, 5, '2025-11-05', 8),
(4, 15, 'SUB-2024-0015', '2024-03-10', '2025-03-09', 'ACTIVE', 29700.00, 2, 10, '2025-11-10', 8),
(5, 16, 'SUB-2024-0016', '2024-03-15', '2025-03-14', 'ACTIVE', 29700.00, 2, 15, '2025-11-15', 8),
(5, 17, 'SUB-2024-0017', '2024-03-15', '2025-03-14', 'ACTIVE', 29700.00, 2, 15, '2025-11-15', 8),
(6, 18, 'SUB-2024-0018', '2024-03-20', '2025-03-19', 'ACTIVE', 29700.00, 2, 20, '2025-11-20', 8),
(6, 19, 'SUB-2024-0019', '2024-03-20', '2025-03-19', 'ACTIVE', 29700.00, 2, 20, '2025-11-20', 8),
(6, 20, 'SUB-2024-0020', '2024-03-25', '2025-03-24', 'ACTIVE', 29700.00, 2, 25, '2025-11-25', 8),
(7, 21, 'SUB-2024-0021', '2024-04-01', '2025-03-31', 'ACTIVE', 29700.00, 2, 1, '2025-12-01', 9),
(7, 22, 'SUB-2024-0022', '2024-04-01', '2025-03-31', 'ACTIVE', 29700.00, 2, 1, '2025-12-01', 9),
(8, 23, 'SUB-2024-0023', '2024-04-05', '2025-04-04', 'ACTIVE', 29700.00, 2, 5, '2025-12-05', 9),
(8, 24, 'SUB-2024-0024', '2024-04-05', '2025-04-04', 'ACTIVE', 29700.00, 2, 5, '2025-12-05', 9),
(9, 25, 'SUB-2024-0025', '2024-04-10', '2025-04-09', 'ACTIVE', 29700.00, 2, 10, '2025-12-10', 9),
(9, 26, 'SUB-2024-0026', '2024-04-10', '2025-04-09', 'ACTIVE', 29700.00, 2, 10, '2025-12-10', 9),
(9, 27, 'SUB-2024-0027', '2024-04-15', '2025-04-14', 'ACTIVE', 29700.00, 2, 15, '2025-12-15', 9),
(10, 28, 'SUB-2024-0028', '2024-04-20', '2025-04-19', 'ACTIVE', 29700.00, 2, 20, '2025-12-20', 10),
(10, 29, 'SUB-2024-0029', '2024-04-20', '2025-04-19', 'ACTIVE', 29700.00, 2, 20, '2025-12-20', 10),
(11, 30, 'SUB-2024-0030', '2024-04-25', '2025-04-24', 'ACTIVE', 29700.00, 2, 25, '2025-12-25', 10);

-- ============================================================
-- (나머지 테이블 데이터는 계속...)
-- 파일 크기 제한으로 인해 일부만 표시합니다.
-- 전체 SQL은 별도로 분할하여 제공하겠습니다.
-- ============================================================

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- 더미 데이터 생성 완료 (Part 1)
-- ============================================================
