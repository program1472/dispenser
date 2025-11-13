<?php
/**
 * HQ 정책관리
 * 가격, 커미션, 인센티브, KPI 정책 관리
 */

// POST 처리 (정책 업데이트)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'update_policy') {
        $policyType = mysqli_real_escape_string($con, $_POST['policy_type']);
        $policyKey = mysqli_real_escape_string($con, $_POST['policy_key']);
        $policyValue = mysqli_real_escape_string($con, $_POST['policy_value']);

        // policies 테이블에 저장 (upsert)
        $sql = "INSERT INTO policies (policy_type, policy_key, policy_value, updated_at)
                VALUES (?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE
                policy_value = VALUES(policy_value),
                updated_at = NOW()";

        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'sss', $policyType, $policyKey, $policyValue);

        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['result' => true, 'msg' => '정책이 업데이트되었습니다.']);
        } else {
            echo json_encode(['result' => false, 'error' => ['msg' => '업데이트 실패: ' . mysqli_error($con)]]);
        }

        mysqli_stmt_close($stmt);
        mysqli_close($con);
        exit;
    }
}

// 현재 정책 값 조회
$policies = [];

// policies 테이블 존재 여부 확인
$tableCheck = mysqli_query($con, "SHOW TABLES LIKE 'policies'");
$tableExists = mysqli_num_rows($tableCheck) > 0;

if ($tableExists) {
    $sql = "SELECT policy_type, policy_key, policy_value, updated_at FROM policies ORDER BY policy_type, policy_key";
    $result = mysqli_query($con, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $policies[$row['policy_type']][$row['policy_key']] = $row;
        }
    }
}

// 기본값 설정 (DB에 없을 경우)
$defaultPolicies = [
    'subscription' => [
        'monthly_fee' => '29700',
    ],
    'content' => [
        'basic' => '11000',
        'standard' => '22000',
        'deluxe' => '110000',
        'premium' => '220000',
    ],
    'vendor' => [
        'commission_rate' => '40',
        'incentive_rate' => '5',
    ],
    'lucid' => [
        'share_rate' => '50',
    ],
    'sales' => [
        'sales_incentive_total' => '90000',
        'sales_incentive_monthly' => '15000',
        'renewal_basic' => '30000',
        'renewal_consecutive' => '40000',
    ],
];

// DB에 없으면 기본값 사용
foreach ($defaultPolicies as $type => $keys) {
    foreach ($keys as $key => $value) {
        if (!isset($policies[$type][$key])) {
            $policies[$type][$key] = [
                'policy_type' => $type,
                'policy_key' => $key,
                'policy_value' => $value,
                'updated_at' => null
            ];
        }
    }
}
?>

<div class="wrap">
  <!-- 구독료 정책 -->
  <section id="sec-subscription-policy" class="card section-card-first">
    <div class="card-hd card-hd-wrap">
      <div class="card-hd-content">
        <div class="card-hd-title-area">
          <div class="card-ttl">💰 구독료 정책</div>
          <div class="card-sub">월 정기 구독료</div>
        </div>
      </div>
    </div>
    <div class="card-bd">
      <div class="policy-grid">
        <div class="policy-item">
          <label>월 구독료</label>
          <div class="input-group">
            <span class="input-prefix">₩</span>
            <input type="number" class="form-control policy-input"
                   data-type="subscription" data-key="monthly_fee"
                   value="<?php echo $policies['subscription']['monthly_fee']['policy_value']; ?>">
            <span class="input-suffix">원</span>
            <button class="btn-sm btn-save" onclick="savePolicy('subscription', 'monthly_fee', this)">저장</button>
          </div>
          <small class="text-muted">포함: 기기 렌탈, 오일 6개/년, 무료 프린팅 6회/년</small>
        </div>
      </div>
    </div>
  </section>

  <!-- 콘텐츠 가격 정책 -->
  <section id="sec-content-policy" class="card">
    <div class="card-hd card-hd-wrap">
      <div class="card-hd-content">
        <div class="card-hd-title-area">
          <div class="card-ttl">📦 콘텐츠 가격 정책</div>
          <div class="card-sub">등급별 콘텐츠 가격</div>
        </div>
      </div>
    </div>
    <div class="card-bd">
      <div class="policy-grid">
        <div class="policy-item">
          <label>Basic</label>
          <div class="input-group">
            <span class="input-prefix">₩</span>
            <input type="number" class="form-control policy-input"
                   data-type="content" data-key="basic"
                   value="<?php echo $policies['content']['basic']['policy_value']; ?>">
            <span class="input-suffix">원</span>
            <button class="btn-sm btn-save" onclick="savePolicy('content', 'basic', this)">저장</button>
          </div>
        </div>

        <div class="policy-item">
          <label>Standard</label>
          <div class="input-group">
            <span class="input-prefix">₩</span>
            <input type="number" class="form-control policy-input"
                   data-type="content" data-key="standard"
                   value="<?php echo $policies['content']['standard']['policy_value']; ?>">
            <span class="input-suffix">원</span>
            <button class="btn-sm btn-save" onclick="savePolicy('content', 'standard', this)">저장</button>
          </div>
        </div>

        <div class="policy-item">
          <label>Deluxe</label>
          <div class="input-group">
            <span class="input-prefix">₩</span>
            <input type="number" class="form-control policy-input"
                   data-type="content" data-key="deluxe"
                   value="<?php echo $policies['content']['deluxe']['policy_value']; ?>">
            <span class="input-suffix">원</span>
            <button class="btn-sm btn-save" onclick="savePolicy('content', 'deluxe', this)">저장</button>
          </div>
        </div>

        <div class="policy-item">
          <label>Premium</label>
          <div class="input-group">
            <span class="input-prefix">₩</span>
            <input type="number" class="form-control policy-input"
                   data-type="content" data-key="premium"
                   value="<?php echo $policies['content']['premium']['policy_value']; ?>">
            <span class="input-suffix">원</span>
            <button class="btn-sm btn-save" onclick="savePolicy('content', 'premium', this)">저장</button>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- 벤더 정책 -->
  <section id="sec-vendor-policy" class="card">
    <div class="card-hd">
      <div style="display: flex; flex-direction: column; gap: 20px; flex: 1;">
        <div style="display: flex; align-items: center; gap: 12px;">
          <div class="card-ttl">🏢 벤더 정책</div>
          <div class="card-sub">커미션 및 인센티브 비율</div>
        </div>
      </div>
    </div>
    <div class="card-bd">
      <div class="policy-grid">
        <div class="policy-item">
          <label>커미션 비율</label>
          <div class="input-group">
            <input type="number" class="form-control policy-input"
                   data-type="vendor" data-key="commission_rate"
                   value="<?php echo $policies['vendor']['commission_rate']['policy_value']; ?>"
                   min="0" max="100" step="0.1">
            <span class="input-suffix">%</span>
            <button class="btn-sm btn-save" onclick="savePolicy('vendor', 'commission_rate', this)">저장</button>
          </div>
          <small class="text-muted">유료 매출 × 커미션 비율</small>
        </div>

        <div class="policy-item">
          <label>인센티브 비율</label>
          <div class="input-group">
            <input type="number" class="form-control policy-input"
                   data-type="vendor" data-key="incentive_rate"
                   value="<?php echo $policies['vendor']['incentive_rate']['policy_value']; ?>"
                   min="0" max="100" step="0.1">
            <span class="input-suffix">%</span>
            <button class="btn-sm btn-save" onclick="savePolicy('vendor', 'incentive_rate', this)">저장</button>
          </div>
          <small class="text-muted">유료 매출 × 인센티브 비율</small>
        </div>
      </div>

      <div class="info-box">
        <strong>정산 정책</strong>
        <ul>
          <li>지급일: 익월 15일</li>
          <li>지급 상태: PLANNED → DUE → PAID</li>
          <li>정산 기준: 전월 1일~말일 완료된 결제 건</li>
        </ul>
      </div>
    </div>
  </section>

  <!-- 루시드 정책 -->
  <section id="sec-lucid-policy" class="card">
    <div class="card-hd">
      <div style="display: flex; flex-direction: column; gap: 20px; flex: 1;">
        <div style="display: flex; align-items: center; gap: 12px;">
          <div class="card-ttl">🎨 루시드 정책</div>
          <div class="card-sub">콘텐츠 배분율</div>
        </div>
      </div>
    </div>
    <div class="card-bd">
      <div class="policy-grid">
        <div class="policy-item">
          <label>배분율</label>
          <div class="input-group">
            <input type="number" class="form-control policy-input"
                   data-type="lucid" data-key="share_rate"
                   value="<?php echo $policies['lucid']['share_rate']['policy_value']; ?>"
                   min="0" max="100" step="0.1">
            <span class="input-suffix">%</span>
            <button class="btn-sm btn-save" onclick="savePolicy('lucid', 'share_rate', this)">저장</button>
          </div>
          <small class="text-muted">콘텐츠 단가 × 배분율 (고객 수정 요청 시만)</small>
        </div>
      </div>

      <div class="info-box">
        <strong>정산 조건</strong>
        <ul>
          <li>고객이 수정 요청한 경우에만 배분</li>
          <li>단순 프린트/복제 요청은 제외</li>
          <li>지급일: 익월 15일</li>
        </ul>
      </div>
    </div>
  </section>

  <!-- 영업사원 인센티브 정책 -->
  <section id="sec-sales-policy" class="card">
    <div class="card-hd">
      <div style="display: flex; flex-direction: column; gap: 20px; flex: 1;">
        <div style="display: flex; align-items: center; gap: 12px;">
          <div class="card-ttl">👔 영업사원 인센티브 정책</div>
          <div class="card-sub">판매 및 리뉴얼 인센티브</div>
        </div>
      </div>
    </div>
    <div class="card-bd">
      <div class="policy-grid">
        <div class="policy-item">
          <label>판매 인센티브 총액</label>
          <div class="input-group">
            <span class="input-prefix">₩</span>
            <input type="number" class="form-control policy-input"
                   data-type="sales" data-key="sales_incentive_total"
                   value="<?php echo $policies['sales']['sales_incentive_total']['policy_value']; ?>">
            <span class="input-suffix">원/대</span>
            <button class="btn-sm btn-save" onclick="savePolicy('sales', 'sales_incentive_total', this)">저장</button>
          </div>
          <small class="text-muted">6회 분할 지급</small>
        </div>

        <div class="policy-item">
          <label>월 분할 지급액</label>
          <div class="input-group">
            <span class="input-prefix">₩</span>
            <input type="number" class="form-control policy-input"
                   data-type="sales" data-key="sales_incentive_monthly"
                   value="<?php echo $policies['sales']['sales_incentive_monthly']['policy_value']; ?>">
            <span class="input-suffix">원</span>
            <button class="btn-sm btn-save" onclick="savePolicy('sales', 'sales_incentive_monthly', this)">저장</button>
          </div>
          <small class="text-muted">매월 지급 금액</small>
        </div>

        <div class="policy-item">
          <label>리뉴얼 인센티브 (기본)</label>
          <div class="input-group">
            <span class="input-prefix">₩</span>
            <input type="number" class="form-control policy-input"
                   data-type="sales" data-key="renewal_basic"
                   value="<?php echo $policies['sales']['renewal_basic']['policy_value']; ?>">
            <span class="input-suffix">원</span>
            <button class="btn-sm btn-save" onclick="savePolicy('sales', 'renewal_basic', this)">저장</button>
          </div>
        </div>

        <div class="policy-item">
          <label>리뉴얼 인센티브 (연속)</label>
          <div class="input-group">
            <span class="input-prefix">₩</span>
            <input type="number" class="form-control policy-input"
                   data-type="sales" data-key="renewal_consecutive"
                   value="<?php echo $policies['sales']['renewal_consecutive']['policy_value']; ?>">
            <span class="input-suffix">원</span>
            <button class="btn-sm btn-save" onclick="savePolicy('sales', 'renewal_consecutive', this)">저장</button>
          </div>
        </div>
      </div>

      <div class="info-box">
        <strong>KPI 구성 비율</strong>
        <ul>
          <li>판매: 40%</li>
          <li>유지: 25%</li>
          <li>리뉴얼: 20%</li>
          <li>보고: 15%</li>
        </ul>
      </div>
    </div>
  </section>
</div>

<script>
// 정책 저장
function savePolicy(type, key, btnElement) {
  const input = btnElement.closest('.input-group').querySelector('.policy-input');
  const value = input.value;

  if (!value || value <= 0) {
    alert('올바른 값을 입력하세요.');
    return;
  }

  const formData = new FormData();
  formData.append('action', 'update_policy');
  formData.append('policy_type', type);
  formData.append('policy_key', key);
  formData.append('policy_value', value);

  btnElement.disabled = true;
  btnElement.textContent = '저장 중...';

  fetch(window.location.href, {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.result) {
      alert('정책이 업데이트되었습니다.');
      input.style.borderColor = '#28a745';
      setTimeout(() => {
        input.style.borderColor = '';
      }, 2000);
    } else {
      alert('오류: ' + (data.error?.msg || '알 수 없는 오류'));
    }
  })
  .catch(err => {
    alert('오류: ' + err.message);
  })
  .finally(() => {
    btnElement.disabled = false;
    btnElement.textContent = '저장';
  });
}

// 입력값 변경 감지
document.querySelectorAll('.policy-input').forEach(input => {
  input.addEventListener('change', function() {
    const saveBtn = this.closest('.input-group').querySelector('.btn-save');
    if (saveBtn) {
      saveBtn.style.backgroundColor = '#ff9800';
      saveBtn.textContent = '저장 필요';
    }
  });
});
</script>
