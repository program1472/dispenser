<?php
/**
 * 본사 직접 고객 더미 데이터 삽입 (UTF-8)
 */

$host = 'localhost';
$user = 'program1472';
$pass = '$gPfls1129';
$db = 'dispenser';

// UTF-8 연결
$con = mysqli_connect($host, $user, $pass, $db);
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($con, 'utf8mb4');
mysqli_query($con, "SET NAMES utf8mb4");

// 1. 본사 직접 고객 추가 (최근 6개월)
$customers = [
    ['user_id' => 80, 'company' => '인천국제공항면세점', 'ceo' => '최면세', 'category' => '소매업', 'date' => '2024-06-15 14:00:00'],
    ['user_id' => 81, 'company' => '광주문화전당', 'ceo' => '정문화', 'category' => '문화업', 'date' => '2024-07-20 11:30:00'],
    ['user_id' => 82, 'company' => '울산현대공장', 'ceo' => '박자동', 'category' => '제조업', 'date' => '2024-08-25 10:00:00'],
    ['user_id' => 76, 'company' => '서울중앙병원', 'ceo' => '김병원', 'category' => '의료업', 'date' => '2024-09-01 10:00:00'],
    ['user_id' => 77, 'company' => '부산해운대호텔', 'ceo' => '박호텔', 'category' => '숙박업', 'date' => '2024-10-01 11:00:00'],
    ['user_id' => 78, 'company' => '대구백화점', 'ceo' => '이백화', 'category' => '소매업', 'date' => '2024-11-01 09:00:00'],
];

echo "=== 본사 직접 고객 추가 시작 ===\n";

foreach ($customers as $cust) {
    $sql = "INSERT INTO customers (
        user_id, vendor_id, company_name, business_number, ceo_name,
        business_type, business_category, address, payment_method,
        is_active, created_at, updated_at, created_by, updated_by
    ) VALUES (
        {$cust['user_id']}, NULL, '{$cust['company']}', '000-00-00000', '{$cust['ceo']}',
        '법인사업자', '{$cust['category']}', '주소', 'CARD',
        1, '{$cust['date']}', NOW(), 2, 2
    )";

    if (mysqli_query($con, $sql)) {
        $customer_id = mysqli_insert_id($con);
        echo "✓ 고객 추가: {$cust['company']} (ID: {$customer_id})\n";

        // 사업장 추가
        $site_sql = "INSERT INTO customer_sites (customer_id, site_name, address, contact_name, contact_phone, created_at, updated_at, created_by, updated_by)
                     VALUES ({$customer_id}, '{$cust['company']} 본점', '주소', '담당자', '02-0000-0000', NOW(), NOW(), 2, 2)";
        mysqli_query($con, $site_sql);
        $site_id = mysqli_insert_id($con);

        // 구독 추가
        $monthly_fee = rand(500000, 900000);
        $sub_number = 'SUB-HQ-' . date('Y-m-d-His');
        $start_date = substr($cust['date'], 0, 10);
        $end_date = date('Y-m-d', strtotime($start_date . ' +1 year'));

        $sub_sql = "INSERT INTO subscriptions (
            customer_id, site_id, subscription_number, start_date, end_date,
            monthly_fee, billing_day, status, created_at, updated_at, created_by, updated_by
        ) VALUES (
            {$customer_id}, {$site_id}, '{$sub_number}', '{$start_date}', '{$end_date}',
            {$monthly_fee}, 1, 'ACTIVE', NOW(), NOW(), 2, 2
        )";

        if (mysqli_query($con, $sub_sql)) {
            echo "  → 구독 추가: {$monthly_fee}원/월\n";
        }
    } else {
        echo "✗ 오류: " . mysqli_error($con) . "\n";
    }
}

echo "\n=== 완료 ===\n";

// 결과 확인
$check_sql = "SELECT customer_id, company_name, ceo_name, DATE(created_at) as contract_date
              FROM customers
              WHERE vendor_id IS NULL AND deleted_at IS NULL
              ORDER BY created_at";
$result = mysqli_query($con, $check_sql);

echo "\n본사 직접 고객 목록:\n";
while ($row = mysqli_fetch_assoc($result)) {
    echo "- ID {$row['customer_id']}: {$row['company_name']} ({$row['ceo_name']}) - {$row['contract_date']}\n";
}

mysqli_close($con);
?>
