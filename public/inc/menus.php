<?php

$menus = [
  "hq" => [
    ["name" => "대시보드", "id" => "H01", "path" => "dashboard", "enabled" => true],
    ["name" => "고객관리", "id" => "H02", "path" => "customer_mgmt_tab", "enabled" => true],
    ["name" => "제품관리", "id" => "H03", "path" => "product_mgmt", "enabled" => true],
    ["name" => "실적관리", "id" => "H04", "path" => "perf_mgmt_tab", "enabled" => true],
    ["name" => "출고관리", "id" => "H05", "path" => "shipping_mgmt_tab", "enabled" => true],
    ["name" => "정책관리", "id" => "H06", "path" => "policy_mgmt", "enabled" => true],
    ["name" => "구매발주", "id" => "H07", "path" => "purchase_order", "enabled" => true],
    ["name" => "신규콘텐츠", "id" => "H08", "path" => "new_content", "enabled" => true],
    ["name" => "고객 문의", "id" => "H09", "path" => "customer_tickets", "enabled" => true],
  ],
  "vendor" => [
    ["name" => "대시보드",       "id" => "V01", "path" => "dashboard",           "enabled" => true],
    ["name" => "고객관리",       "id" => "V02", "path" => "customer_mgmt",       "enabled" => true],
    ["name" => "작업지시서",     "id" => "V03", "path" => "work_orders",         "enabled" => true],
    ["name" => "Billing(구독)",  "id" => "V04", "path" => "billing",             "enabled" => true],
    ["name" => "정산",           "id" => "V05", "path" => "settlement",          "enabled" => true],
    ["name" => "티켓(문의)",     "id" => "V06", "path" => "tickets",             "enabled" => true],
    ["name" => "신규 향/콘텐츠", "id" => "V07", "path" => "new_content",         "enabled" => true],
    ["name" => "상품구매(할인)", "id" => "V08", "path" => "product_purchase",    "enabled" => true],
    ["name" => "재고/시리얼",    "id" => "V09", "path" => "inventory_serials",   "enabled" => true],
	["name" => "장바구니",        "id" => "V10", "path" => "cart",       "enabled" => true],
    ["name" => "알림센터",       "id" => "V11", "path" => "notifications",       "enabled" => true],
  ],
  "customer" => [
    ["name" => "대시보드",         "id" => "C01", "path" => "dashboard",     "enabled" => true],
    ["name" => "기기관리",         "id" => "C02", "path" => "device_mgmt",   "enabled" => true],
    ["name" => "콘텐츠라이브러리", "id" => "C03", "path" => "content_lib",   "enabled" => true],
    ["name" => "향라이브러리",     "id" => "C04", "path" => "scent_lib",     "enabled" => true],
	["name" => "배송정보",        "id" => "C05", "path" => "shipping",       "enabled" => true],
    ["name" => "결제/구독",        "id" => "C06", "path" => "billing",       "enabled" => true],
	["name" => "장바구니",        "id" => "C07", "path" => "cart",       "enabled" => true],
    ["name" => "도움",             "id" => "C08", "path" => "help",          "enabled" => true],
  ],
  "lucid" => [
	  ["name" => "대시보드",       "id" => "L01", "path" => "dashboard",        "enabled" => true],
	  ["name" => "신규콘텐츠 등록", "id" => "L02", "path" => "content_new",      "enabled" => true],
	  ["name" => "수정요청",       "id" => "L03", "path" => "edit_requests",     "enabled" => true],
	  ["name" => "라이브러리",     "id" => "L04", "path" => "content_library",   "enabled" => true],
	  ["name" => "태그관리",       "id" => "L05", "path" => "tag_mgmt",          "enabled" => true],
	  ["name" => "정산",           "id" => "L06", "path" => "settlement",        "enabled" => true],
  ],
];

?>
