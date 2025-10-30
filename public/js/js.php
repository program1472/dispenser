<script>

	/**
	 * 로그를 출력하는 함수입니다.
	 * PHP 상수 IS_DEBUG가 true일 때만 console.log를 실행합니다.
	 * 
	 * @param {...any} args - 출력할 로그의 내용들
	 */
	function log(...args) {
		<?php if (defined('IS_DEBUG') && IS_DEBUG): ?>
			console.log(...args);
		<?php endif; ?>
	}

	/// <summary>
	/// 주어진 메뉴명을 기반으로 AJAX 요청을 보내 페이지를 로드하는 함수
	/// </summary>
	/// <param name="menuName">로드할 메뉴명 (쿼리스트링 포함 가능)</param>
	function loadPage(el, menuName) {
	  // ▼ el 없이 '토큰'만 넘겨도 동작하게 인자 정규화
	  if (typeof el === 'string' && menuName === undefined) {
		menuName = el;  // 첫 인자가 토큰
		el = null;
	  }

	  // el이 없으면 토큰으로 a 태그를 찾아서 active
	  if (!el && typeof menuName === 'string') {
		const tabs = document.getElementById('tabs');
		if (tabs) {
		  // data-token으로 먼저 찾고, 없으면 onclick 문자열에서 토큰 검색
		  el = tabs.querySelector(`a[data-token="${menuName}"]`) ||
			   Array.from(tabs.querySelectorAll('a')).find(a => (a.getAttribute('onclick')||'').includes(menuName));
		}
	  }

	  if (el) {
		  // 탭 active 초기화
		  document.querySelectorAll('#tabs a.active').forEach(a => a.classList.remove('active'));
		  el.classList.add('active');
	  }

	  // ----- 파라미터 구성 (암호화된 키는 bracket notation으로) -----
	  const data = {};
	  // ⚠️ JS 식별자 규칙에 안 맞을 수 있으므로 반드시 [] 사용
	  data['<?= encryptValue('menuName') ?>'] = (function(){
		if (typeof menuName === 'string' && menuName.includes('?')) {
		  return menuName.split('?')[0];
		}
		return menuName;
	  })();

	  // menuName에 쿼리 있으면 분리해서 data에 추가
	  if (typeof menuName === 'string' && menuName.includes('?')) {
		const paramString = menuName.split('?')[1];
		const params = new URLSearchParams(paramString);
		for (const [key, value] of params.entries()) {
		  data[key] = value;
		}
	  }

	  // ----- AJAX 로드 -----
	  $.ajax({
		type: "POST",
		url: "#",          // 규약: 모든 요청은 _ajax_.php 경유
		dataType: "html",
		data: data,
		cache: false
	  }).done(function(response){
		$('#content').html(response);
	  }).fail(function(xhr, status, error){
		console.warn('AJAX 오류:', error);
		$('#content').html('<p>페이지를 불러올 수 없습니다.</p>');
	  });
	}

	/// <summary>
	/// AJAX 요청을 보내고 JSON 응답을 받아 처리하는 함수
	/// </summary>
	/// <param name="data">전송할 폼 데이터 (FormData 또는 일반 객체)</param>
	/// <param name="callback">요청 성공 시 호출할 콜백 함수</param>
	function updateAjaxContent(data, callback, isClose = true) {
	  const isFormData = data instanceof FormData;

	  $.ajax({
		url: "<?= SRC ?>/" + pageName,
		type: "POST",
		data: data,
		dataType: "json",
		processData: !isFormData,  // FormData면 false, 일반 객체면 true
		contentType: isFormData ? false : 'application/x-www-form-urlencoded; charset=UTF-8', // FormData면 false
		success: function(response) {
			if (typeof callback === 'function'){
				callback(response);
			} else {
				if (response.result) {
					if (isClose) closePopup();
					alert('완료!!');
				} else {
					let msg = '알 수 없는 오류가 발생했습니다.';
					if (response.error) msg = response.error.msg || msg;
					alert(msg);
					console.warn('updateAjaxContent error payload:', response);
				}
			}
		},
		error: function(xhr, status, error) {
		  alert('에러 발생: ' + error);
		}
	  });
	}

</script>