<?php
/**
 * 더미 데이터 생성 스크립트
 * 본사 > 고객관리 관련 테이블 더미 데이터 생성
 */

require_once __DIR__ . '/../dbconfig.php';

echo "=== 더미 데이터 생성 시작 ===\n\n";

// 1. Vendors 더미 데이터 30개 생성
echo "1. Vendors 더미 데이터 생성 중...\n";

$vendorNames = [
    ['디바이스테크', '(주)디바이스테크', '123-45-67890', '김철수', 'contact@devicetech.com', '02-1234-5678', '서울시 강남구 테헤란로 123', '이영희', '02-1234-5679', 'lee@devicetech.com', '신한은행', '110-123-456789', '김철수', '프리미엄 디바이스 전문'],
    ['스마트솔루션', '(주)스마트솔루션', '234-56-78901', '박민수', 'info@smartsol.co.kr', '02-2345-6789', '서울시 서초구 서초대로 456', '최지훈', '02-2345-6790', 'choi@smartsol.co.kr', '우리은행', '220-234-567890', '박민수', 'IoT 솔루션 제공'],
    ['퓨처테크놀로지', '퓨처테크놀로지(주)', '345-67-89012', '정수진', 'hello@futuretech.kr', '02-3456-7890', '경기도 성남시 분당구 판교역로 789', '강민주', '02-3456-7891', 'kang@futuretech.kr', '하나은행', '330-345-678901', '정수진', '미래형 기술 개발'],
    ['에코시스템즈', '(주)에코시스템즈', '456-78-90123', '이동훈', 'contact@ecosys.com', '031-4567-8901', '경기도 용인시 수지구 죽전로 101', '송하늘', '031-4567-8902', 'song@ecosys.com', '국민은행', '440-456-789012', '이동훈', '친환경 제품 전문'],
    ['글로벌네트웍스', '글로벌네트웍스(주)', '567-89-01234', '최윤아', 'info@globalnet.co.kr', '02-5678-9012', '서울시 영등포구 여의대로 202', '한서연', '02-5678-9013', 'han@globalnet.co.kr', '기업은행', '550-567-890123', '최윤아', '글로벌 네트워크 구축'],
    ['비전시스템', '(주)비전시스템', '678-90-12345', '장현우', 'hello@visionsys.kr', '02-6789-0123', '서울시 마포구 월드컵북로 303', '윤채원', '02-6789-0124', 'yoon@visionsys.kr', '농협은행', '660-678-901234', '장현우', '비전 인식 시스템'],
    ['넥스트제너레이션', '넥스트제너레이션(주)', '789-01-23456', '오지훈', 'contact@nextgen.com', '031-7890-1234', '경기도 화성시 동탄대로 404', '임도윤', '031-7890-1235', 'lim@nextgen.com', '신한은행', '770-789-012345', '오지훈', '차세대 기술 선도'],
    ['테크노파크', '(주)테크노파크', '890-12-34567', '신예진', 'info@technopark.co.kr', '02-8901-2345', '서울시 구로구 디지털로 505', '백시우', '02-8901-2346', 'baek@technopark.co.kr', '우리은행', '880-890-123456', '신예진', '첨단 기술 단지'],
    ['인노베이션랩', '인노베이션랩(주)', '901-23-45678', '허준서', 'hello@innovlab.kr', '031-9012-3456', '경기도 안양시 동안구 시민대로 606', '조아인', '031-9012-3457', 'cho@innovlab.kr', '하나은행', '990-901-234567', '허준서', '혁신 연구소'],
    ['디지털플랫폼', '(주)디지털플랫폼', '012-34-56789', '문지우', 'contact@digitalplatform.com', '02-0123-4567', '서울시 송파구 올림픽로 707', '서준혁', '02-0123-4568', 'seo@digitalplatform.com', '국민은행', '100-012-345678', '문지우', '디지털 플랫폼 구축'],
    ['클라우드서비스', '클라우드서비스(주)', '123-56-78902', '양수아', 'info@cloudservice.co.kr', '031-2345-6780', '경기도 수원시 영통구 광교로 808', '곽하준', '031-2345-6781', 'kwak@cloudservice.co.kr', '기업은행', '220-123-456790', '양수아', '클라우드 전문'],
    ['스마트팩토리', '(주)스마트팩토리', '234-67-89013', '강도현', 'hello@smartfactory.kr', '02-3456-7892', '서울시 금천구 가산디지털로 909', '남윤서', '02-3456-7893', 'nam@smartfactory.kr', '신한은행', '330-234-567902', '강도현', '스마트 공장 솔루션'],
    ['빅데이터랩', '빅데이터랩(주)', '345-78-90124', '노은우', 'contact@bigdatalab.com', '031-4567-8903', '경기도 광명시 광명로 1010', '배서진', '031-4567-8904', 'bae@bigdatalab.com', '우리은행', '440-345-678913', '노은우', '빅데이터 분석'],
    ['AI솔루션즈', '(주)AI솔루션즈', '456-89-01235', '류시윤', 'info@aisolutions.co.kr', '02-5678-9014', '서울시 관악구 관악로 1111', '손주원', '02-5678-9015', 'son@aisolutions.co.kr', '하나은행', '550-456-789024', '류시윤', 'AI 기반 솔루션'],
    ['로봇테크', '로봇테크(주)', '567-90-12346', '진하율', 'hello@robotech.kr', '031-6789-0125', '경기도 고양시 일산동구 중앙로 1212', '홍지안', '031-6789-0126', 'hong@robotech.kr', '국민은행', '660-567-890135', '진하율', '로봇 자동화'],
    ['보안시스템', '(주)보안시스템', '678-01-23457', '전서우', 'contact@securitysys.com', '02-7890-1236', '서울시 동대문구 천호대로 1313', '황예준', '02-7890-1237', 'hwang@securitysys.com', '기업은행', '770-678-901246', '전서우', '보안 전문'],
    ['모바일앱스', '모바일앱스(주)', '789-12-34568', '탁도윤', 'info@mobileapps.co.kr', '031-8901-2347', '경기도 부천시 원미구 부천로 1414', '차시은', '031-8901-2348', 'cha@mobileapps.co.kr', '신한은행', '880-789-012357', '탁도윤', '모바일 앱 개발'],
    ['웹솔루션', '(주)웹솔루션', '890-23-45679', '팽유준', 'hello@websolution.kr', '02-9012-3458', '서울시 노원구 노해로 1515', '편수현', '02-9012-3459', 'pyun@websolution.kr', '우리은행', '990-890-123468', '팽유준', '웹 솔루션 제공'],
    ['네트워크프로', '네트워크프로(주)', '901-34-56780', '표연우', 'contact@networkpro.com', '031-0123-4569', '경기도 안산시 단원구 광덕로 1616', '하지호', '031-0123-4570', 'ha@networkpro.com', '하나은행', '100-901-234579', '표연우', '네트워크 구축'],
    ['데이터센터', '(주)데이터센터', '012-45-67891', '피민준', 'info@datacenter.co.kr', '02-1234-5670', '서울시 강서구 공항대로 1717', '하서준', '02-1234-5671', 'hasj@datacenter.co.kr', '국민은행', '220-012-345690', '피민준', '데이터센터 운영'],
    ['소프트웨어하우스', '소프트웨어하우스(주)', '123-67-89903', '하예은', 'hello@swhouse.kr', '031-2345-6782', '경기도 평택시 평택로 1818', '허윤호', '031-2345-6783', 'heo@swhouse.kr', '기업은행', '330-123-456801', '하예은', '소프트웨어 개발'],
    ['하드웨어랩', '(주)하드웨어랩', '234-78-90014', '홍지우', 'contact@hwlab.com', '02-3456-7894', '서울시 양천구 목동로 1919', '호이준', '02-3456-7895', 'ho@hwlab.com', '신한은행', '440-234-567912', '홍지우', '하드웨어 설계'],
    ['미디어콘텐츠', '미디어콘텐츠(주)', '345-89-01225', '황시현', 'info@mediacontent.co.kr', '031-4567-8905', '경기도 오산시 오산로 2020', '환유진', '031-4567-8906', 'hwan@mediacontent.co.kr', '우리은행', '550-345-678023', '황시현', '미디어 제작'],
    ['게임스튜디오', '(주)게임스튜디오', '456-90-12336', '후서현', 'hello@gamestudio.kr', '02-5678-9016', '서울시 성북구 아리랑로 2121', '훙지원', '02-5678-9017', 'hung@gamestudio.kr', '하나은행', '660-456-789134', '후서현', '게임 개발'],
    ['VR/AR랩', 'VR/AR랩(주)', '567-01-23447', '흥예지', 'contact@vrarlab.com', '031-6789-0127', '경기도 군포시 군포로 2222', '희서아', '031-6789-0128', 'hee@vrarlab.com', '국민은행', '770-567-890245', '흥예지', 'VR/AR 콘텐츠'],
    ['3D프린팅', '(주)3D프린팅', '678-12-34558', '개하은', 'info@3dprinting.co.kr', '02-7890-1238', '서울시 도봉구 도봉로 2323', '경민서', '02-7890-1239', 'kyung@3dprinting.co.kr', '기업은행', '880-678-901356', '개하은', '3D프린팅 서비스'],
    ['드론테크', '드론테크(주)', '789-23-45669', '견지유', 'hello@dronetech.kr', '031-8901-2349', '경기도 의왕시 의왕로 2424', '계서율', '031-8901-2350', 'kye@dronetech.kr', '신한은행', '990-789-012467', '견지유', '드론 제작'],
    ['전기차부품', '(주)전기차부품', '890-34-56770', '고유하', 'contact@evparts.com', '02-9012-3460', '서울시 중랑구 망우로 2525', '곤하린', '02-9012-3461', 'gon@evparts.com', '우리은행', '100-890-123578', '고유하', '전기차 부품 제조'],
    ['태양광에너지', '태양광에너지(주)', '901-45-67881', '곽도하', 'info@solarenergy.co.kr', '031-0123-4571', '경기도 하남시 하남대로 2626', '관지윤', '031-0123-4572', 'kwan@solarenergy.co.kr', '하나은행', '220-901-234689', '곽도하', '태양광 발전'],
    ['스마트홈', '(주)스마트홈', '012-56-78992', '교은채', 'hello@smarthome.kr', '02-1234-5672', '서울시 강동구 천호대로 2727', '구서윤', '02-1234-5673', 'koo@smarthome.kr', '국민은행', '330-012-345791', '교은채', '스마트홈 시스템']
];

$vendorIds = [];
$vendorCount = 0;

foreach ($vendorNames as $idx => $vd) {
    $num = str_pad($idx + 1, 4, '0', STR_PAD_LEFT);
    $vendorId = 'V20251108' . $num;
    $vendorIds[] = $vendorId;

    $sql = "INSERT INTO vendors (vendor_id, name, company_name, business_number, representative, email, phone, address,
             contact_person, contact_phone, contact_email, bank_name, bank_account_number, bank_account_holder,
             tax_id_number, notes, is_active, created_at) VALUES
             ('$vendorId', '{$vd[0]}', '{$vd[1]}', '{$vd[2]}', '{$vd[3]}', '{$vd[4]}', '{$vd[5]}', '{$vd[6]}',
             '{$vd[7]}', '{$vd[8]}', '{$vd[9]}', '{$vd[10]}', '{$vd[11]}', '{$vd[12]}', '{$vd[2]}', '{$vd[13]}', 1, NOW())";

    if (mysqli_query($con, $sql)) {
        $vendorCount++;
    } else {
        echo "Error vendor $vendorId: " . mysqli_error($con) . "\n";
    }
}

echo "✓ Vendors: $vendorCount개 생성 완료\n\n";

// 2. SALES 역할 확인
$roleResult = mysqli_query($con, "SELECT role_id FROM roles WHERE code = 'SALES'");
if (!$roleResult || mysqli_num_rows($roleResult) == 0) {
    echo "✗ SALES 역할이 없습니다. roles 테이블을 확인하세요.\n";
    exit;
}
$roleRow = mysqli_fetch_assoc($roleResult);
$salesRoleId = $roleRow['role_id'];

// 2. Users (영업사원) 더미 데이터 30개 생성
echo "2. Users (영업사원) 더미 데이터 생성 중...\n";

$salesNames = ['김영업', '이판매', '박세일즈', '최마케팅', '정영업팀', '강수주', '윤판촉', '장영업자', '임세일', '한마케터',
               '오영업부', '서판매장', '신영업왕', '유세일즈맨', '조마케팅팀장', '전영업과장', '홍판매대리', '황영업사원', '허세일맨', '문마케터',
               '양영업부장', '강판매팀', '노세일즈', '류마케팅부', '진영업담당', '전판매왕', '탁세일즈직원', '팽마케팅자', '표영업맨', '피판매원'];

$userIds = [];
$userCount = 0;

foreach ($salesNames as $idx => $name) {
    $num = str_pad($idx + 1, 3, '0', STR_PAD_LEFT);
    $userid = 'sales' . $num;
    $email = $userid . '@example.com';
    $phone = '010-1001-' . str_pad($idx + 1, 4, '0', STR_PAD_LEFT);
    $vendorId = $vendorIds[$idx % count($vendorIds)]; // 벤더에 균등 분배

    $sql = "INSERT INTO users (role_id, vendor_id, customer_id, userid, email, password_hash, name, phone, is_active, created_at)
            VALUES ($salesRoleId, '$vendorId', NULL, '$userid', '$email', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '$name', '$phone', 1, NOW())";

    if (mysqli_query($con, $sql)) {
        $userIds[] = mysqli_insert_id($con);
        $userCount++;
    } else {
        echo "Error user $userid: " . mysqli_error($con) . "\n";
    }
}

echo "✓ Users (영업사원): $userCount개 생성 완료\n\n";

// 3. User_extra 더미 데이터
echo "3. User_extra 더미 데이터 생성 중...\n";

$extraCount = 0;
foreach ($userIds as $idx => $userId) {
    $department = ['영업1팀', '영업2팀', '마케팅팀', '판매팀'][$idx % 4];
    $position = ['사원', '대리', '과장', '차장', '부장'][$idx % 5];

    $sql = "INSERT INTO user_extra (user_id, department, position, address, bank_name, bank_account_number, bank_account_holder, created_at)
            VALUES ($userId, '$department', '$position', '서울시 강남구', '신한은행', '110-123-" . str_pad($idx, 6, '0', STR_PAD_LEFT) . "', '{$salesNames[$idx]}', NOW())";

    if (mysqli_query($con, $sql)) {
        $extraCount++;
    }
}

echo "✓ User_extra: $extraCount개 생성 완료\n\n";

// 4. Customers 더미 데이터 30개 생성
echo "4. Customers 더미 데이터 생성 중...\n";

$customerNames = [
    ['강남병원', '111-22-33444', '이병원', 'contact@gangnamhospital.com', '02-1111-2222', '서울시 강남구 강남대로 100', '김담당', '02-1111-2223', 'kim@gangnamhospital.com', 'CMS', '신한은행', '111-222-333444', '이병원', '주요 고객사'],
    ['서초약국', '222-33-44555', '박약국', 'info@seochopharm.com', '02-2222-3333', '서울시 서초구 서초대로 200', '이약사', '02-2222-3334', 'lee@seochopharm.com', 'ONE_TIME', NULL, NULL, NULL, NULL],
    ['판교클리닉', '333-44-55666', '정의사', 'hello@pangyoclinic.kr', '031-3333-4444', '경기도 성남시 분당구 판교역로 300', '최간호사', '031-3333-4445', 'choi@pangyoclinic.kr', 'CMS', '우리은행', '333-444-555666', '정의사', NULL],
    ['수지종합병원', '444-55-66777', '강병원장', 'contact@sujihosp.com', '031-4444-5555', '경기도 용인시 수지구 죽전로 400', '송원무과장', '031-4444-5556', 'song@sujihosp.com', 'CMS', '하나은행', '444-555-666777', '강병원장', 'VIP 고객'],
    ['여의도약국', '555-66-77888', '한약사', 'info@yeouipharm.co.kr', '02-5555-6666', '서울시 영등포구 여의대로 500', '황직원', '02-5555-6667', 'hwang@yeouipharm.co.kr', 'ONE_TIME', NULL, NULL, NULL, NULL],
    ['마포의원', '666-77-88999', '윤의원장', 'hello@mapoclinic.kr', '02-6666-7777', '서울시 마포구 월드컵북로 600', '임간호사', '02-6666-7778', 'lim@mapoclinic.kr', 'CMS', '국민은행', '666-777-888999', '윤의원장', NULL],
    ['동탄병원', '777-88-99000', '백병원', 'contact@dongtanhospital.com', '031-7777-8888', '경기도 화성시 동탄대로 700', '조행정팀', '031-7777-8889', 'cho@dongtanhospital.com', 'ONE_TIME', NULL, NULL, NULL, NULL],
    ['구로약국', '888-99-00111', '서약사장', 'info@guropharmacy.co.kr', '02-8888-9999', '서울시 구로구 디지털로 800', '백약사', '02-8888-9990', 'baek@guropharmacy.co.kr', 'CMS', '기업은행', '888-999-000111', '서약사장', NULL],
    ['안양클리닉', '999-00-11222', '허클리닉장', 'hello@anyangclinic.kr', '031-9999-0000', '경기도 안양시 동안구 시민대로 900', '강관리자', '031-9999-0001', 'kang@anyangclinic.kr', 'ONE_TIME', NULL, NULL, NULL, NULL],
    ['송파병원', '000-11-22333', '문병원장', 'contact@songpahospital.com', '02-0000-1111', '서울시 송파구 올림픽로 1000', '남의국', '02-0000-1112', 'nam@songpahospital.com', 'CMS', '신한은행', '000-111-222333', '문병원장', NULL],
    ['광교약국', '111-33-44555', '양약국장', 'info@gwanggyopharm.co.kr', '031-1111-2222', '경기도 수원시 영통구 광교로 1100', '곽직원', '031-1111-2223', 'kwak@gwanggyopharm.co.kr', 'CMS', '우리은행', '111-333-444555', '양약국장', NULL],
    ['가산병원', '222-44-55666', '강의사', 'hello@gasanhospital.kr', '02-2222-3334', '서울시 금천구 가산디지털로 1200', '노간호사', '02-2222-3335', 'noh@gasanhospital.kr', 'ONE_TIME', NULL, NULL, NULL, NULL],
    ['광명클리닉', '333-55-66777', '노박사', 'contact@gmclinic.com', '031-3333-4446', '경기도 광명시 광명로 1300', '류행정', '031-3333-4447', 'ryu@gmclinic.com', 'CMS', '하나은행', '333-555-666777', '노박사', NULL],
    ['관악약국', '444-66-77888', '류약사', 'info@gwanakpharm.co.kr', '02-4444-5557', '서울시 관악구 관악로 1400', '진사무장', '02-4444-5558', 'jin@gwanakpharm.co.kr', 'ONE_TIME', NULL, NULL, NULL, NULL],
    ['일산병원', '555-77-88999', '진병원장', 'hello@ilsanhospital.kr', '031-5555-6668', '경기도 고양시 일산동구 중앙로 1500', '전의무기록팀', '031-5555-6669', 'jeon@ilsanhospital.kr', 'CMS', '국민은행', '555-777-888999', '진병원장', 'VIP'],
    ['천호의원', '666-88-99000', '전의원장', 'contact@cheonhoclinic.com', '02-6666-7779', '서울시 동대문구 천호대로 1600', '탁관리자', '02-6666-7780', 'tak@cheonhoclinic.com', 'ONE_TIME', NULL, NULL, NULL, NULL],
    ['부천약국', '777-99-00111', '탁약사장', 'info@bucheonpharm.co.kr', '031-7777-8890', '경기도 부천시 원미구 부천로 1700', '팽약사', '031-7777-8891', 'pang@bucheonpharm.co.kr', 'CMS', '기업은행', '777-999-000111', '탁약사장', NULL],
    ['노원병원', '888-00-11222', '팽병원장', 'hello@nowonhospital.kr', '02-8888-9991', '서울시 노원구 노해로 1800', '표원무과', '02-8888-9992', 'pyo@nowonhospital.kr', 'CMS', '신한은행', '888-000-111222', '팽병원장', NULL],
    ['단원클리닉', '999-11-22333', '표원장', 'contact@danwonclinic.com', '031-9999-0002', '경기도 안산시 단원구 광덕로 1900', '피사무장', '031-9999-0003', 'pi@danwonclinic.com', 'ONE_TIME', NULL, NULL, NULL, NULL],
    ['공항약국', '000-22-33444', '피약사', 'info@airportpharm.co.kr', '02-0000-1113', '서울시 강서구 공항대로 2000', '하직원', '02-0000-1114', 'ha@airportpharm.co.kr', 'CMS', '우리은행', '000-222-333444', '피약사', NULL],
    ['평택병원', '111-44-55666', '하병원장', 'hello@pyeongtaekhospital.kr', '031-1111-2224', '경기도 평택시 평택로 2100', '허행정실', '031-1111-2225', 'heo@pyeongtaekhospital.kr', 'ONE_TIME', NULL, NULL, NULL, NULL],
    ['목동의원', '222-55-66777', '허의사', 'contact@mokdongclinic.com', '02-2222-3336', '서울시 양천구 목동로 2200', '호간호사', '02-2222-3337', 'ho@mokdongclinic.com', 'CMS', '하나은행', '222-555-666777', '허의사', NULL],
    ['오산약국', '333-66-77888', '호약사장', 'info@osanpharm.co.kr', '031-3333-4448', '경기도 오산시 오산로 2300', '환약사', '031-3333-4449', 'hwan@osanpharm.co.kr', 'CMS', '국민은행', '333-666-777888', '호약사장', NULL],
    ['성북병원', '444-77-88999', '환병원장', 'hello@seongbukhospital.kr', '02-4444-5559', '서울시 성북구 아리랑로 2400', '후원무팀', '02-4444-5560', 'hoo@seongbukhospital.kr', 'ONE_TIME', NULL, NULL, NULL, NULL],
    ['군포클리닉', '555-88-99000', '후의사', 'contact@gunpoclinic.com', '031-5555-6670', '경기도 군포시 군포로 2500', '흥관리자', '031-5555-6671', 'hong@gunpoclinic.com', 'CMS', '기업은행', '555-888-999000', '후의사', NULL],
    ['도봉약국', '666-99-00111', '흥약사장', 'info@dobongpharm.co.kr', '02-6666-7781', '서울시 도봉구 도봉로 2600', '희직원', '02-6666-7782', 'hee@dobongpharm.co.kr', 'ONE_TIME', NULL, NULL, NULL, NULL],
    ['의왕병원', '777-00-11222', '희병원장', 'hello@uiwanghospital.kr', '031-7777-8892', '경기도 의왕시 의왕로 2700', '개행정부', '031-7777-8893', 'gae@uiwanghospital.kr', 'CMS', '신한은행', '777-000-111222', '희병원장', NULL],
    ['중랑의원', '888-11-22333', '개의원장', 'contact@jungrangclinic.com', '02-8888-9993', '서울시 중랑구 망우로 2800', '경간호사', '02-8888-9994', 'kyung@jungrangclinic.com', 'CMS', '우리은행', '888-111-222333', '개의원장', NULL],
    ['하남약국', '999-22-33444', '경약사', 'info@hanampharm.co.kr', '031-9999-0004', '경기도 하남시 하남대로 2900', '견약사', '031-9999-0005', 'gyun@hanampharm.co.kr', 'ONE_TIME', NULL, NULL, NULL, NULL],
    ['강동병원', '000-33-44555', '견병원장', 'hello@gangdonghospital.kr', '02-0000-1115', '서울시 강동구 천호대로 3000', '고총무팀', '02-0000-1116', 'go@gangdonghospital.kr', 'CMS', '하나은행', '000-333-444555', '견병원장', 'VIP 고객사']
];

$customerCount = 0;

foreach ($customerNames as $idx => $cd) {
    $num = str_pad($idx + 1, 4, '0', STR_PAD_LEFT);
    $customerId = 'C20251108' . $num;
    $vendorId = $vendorIds[$idx % count($vendorIds)];
    $salesRepId = $userIds[$idx % count($userIds)];

    $bankName = ($cd[10] == NULL) ? 'NULL' : "'{$cd[10]}'";
    $bankAccount = ($cd[11] == NULL) ? 'NULL' : "'{$cd[11]}'";
    $bankHolder = ($cd[12] == NULL) ? 'NULL' : "'{$cd[12]}'";
    $notes = ($cd[13] == NULL) ? 'NULL' : "'{$cd[13]}'";

    $sql = "INSERT INTO customers (customer_id, user_id, vendor_id, sales_rep_id, name, company_name, business_number, representative,
             email, phone, address, payment_method, cms_bank_name, cms_account_number, cms_account_holder,
             contact_person, contact_phone, contact_email, notes, is_active, created_at) VALUES
             ('$customerId', NULL, '$vendorId', $salesRepId, '{$cd[0]}', '{$cd[0]}', '{$cd[1]}', '{$cd[2]}',
             '{$cd[3]}', '{$cd[4]}', '{$cd[5]}', '{$cd[9]}', $bankName, $bankAccount, $bankHolder,
             '{$cd[6]}', '{$cd[7]}', '{$cd[8]}', $notes, 1, NOW())";

    if (mysqli_query($con, $sql)) {
        $customerCount++;
    } else {
        echo "Error customer $customerId: " . mysqli_error($con) . "\n";
    }
}

echo "✓ Customers: $customerCount개 생성 완료\n\n";

echo "=== 더미 데이터 생성 완료 ===\n";
echo "총 생성: Vendors $vendorCount, Users $userCount, User_extra $extraCount, Customers $customerCount\n";

mysqli_close($con);
