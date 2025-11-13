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
		  document.querySelectorAll('#tabs .dropdown.active').forEach(d => d.classList.remove('active'));

		  // 클릭된 요소에 active 추가
		  el.classList.add('active');

		  // 드롭다운 메뉴 아이템인 경우 부모 드롭다운도 active
		  const parentDropdown = el.closest('.dropdown');
		  if (parentDropdown) {
			  parentDropdown.classList.add('active');
		  }
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
		url: "<?= SRC ?>/",  // index.php로
		dataType: "json",
		data: data,
		cache: false
	  }).done(function(response){
		// 응답 검증 및 오류 처리
		if (!response) {
		  console.error('응답이 없습니다.');
		  $('#content').html('<div class="alert alert-danger">페이지를 불러올 수 없습니다.</div>');
		  return;
		}

		// 오류 응답 처리
		if (response.result === 'error') {
		  console.error('서버 오류:', response.msg || response.error?.msg || '알 수 없는 오류');
		  const errorMsg = response.msg || response.error?.msg || '페이지를 불러올 수 없습니다.';
		  $('#content').html(`<div class="alert alert-danger">${errorMsg}</div>`);
		  return;
		}

		// 정상 응답 처리
		if (response.result === 'ok' && response.html) {
		  // HTML을 직접 삽입 (jQuery가 자동으로 스크립트 실행)
		  const $content = $('#content');
		  $content.html(response.html);

		  // jQuery가 처리하지 못한 스크립트를 수동으로 실행
		  $content.find('script').each(function() {
			const script = document.createElement('script');

			if (this.src) {
			  // 외부 스크립트
			  script.src = this.src;
			  script.async = false;
			} else {
			  // 인라인 스크립트
			  script.text = this.text || this.textContent || this.innerHTML;
			}

			// 속성 복사
			if (this.type) script.type = this.type;
			if (this.id) script.id = this.id;

			// 기존 스크립트 제거 후 새로 추가
			this.parentNode.replaceChild(script, this);
		  });
		} else {
		  console.error('응답 형식이 올바르지 않습니다:', response);
		  $('#content').html('<div class="alert alert-warning">페이지 형식이 올바르지 않습니다.</div>');
		}
	  }).fail(function(xhr, status, error){
		console.error('AJAX 통신 오류:', status, error);
		console.error('응답 내용:', xhr.responseText);
		$('#content').html(`<div class="alert alert-danger">페이지를 불러올 수 없습니다.<br>오류: ${error}</div>`);
	  });
	}

	/// <summary>
	/// 공통 탭 로드 함수 - 모든 탭 페이지에서 사용
	/// </summary>
	/// <param name="btnElement">클릭된 탭 버튼 요소</param>
	/// <param name="encryptedToken">암호화된 페이지 토큰</param>
	/// <param name="containerSelector">탭 컨텐츠를 표시할 컨테이너 선택자</param>
	/// <param name="tabButtonsSelector">탭 버튼들의 부모 선택자 (active 클래스 토글용)</param>
	window.loadTabContent = function(btnElement, encryptedToken, containerSelector, tabButtonsSelector) {
	  // 로딩 중복 방지
	  const contentArea = document.querySelector(containerSelector);
	  if (!contentArea) {
		console.error('컨테이너를 찾을 수 없습니다:', containerSelector);
		return;
	  }

	  // 이미 로딩 중이면 중단
	  if (contentArea.dataset.loading === 'true') {
		console.log('이미 로딩 중입니다.');
		return;
	  }
	  contentArea.dataset.loading = 'true';

	  // 모든 탭 버튼 비활성화
	  document.querySelectorAll(`${tabButtonsSelector} .tab-btn-inline`).forEach(btn => {
		btn.classList.remove('active');
	  });

	  // 클릭된 탭 활성화
	  if (btnElement && btnElement.classList) {
		btnElement.classList.add('active');
	  }

	  // 로딩 표시
	  contentArea.innerHTML = '<div class="table-text-center text-muted"><p>로딩 중...</p></div>';

	  // AJAX로 페이지 로드
	  const data = {};
	  data['<?= encryptValue('menuName') ?>'] = encryptedToken;

	  $.ajax({
		type: "POST",
		url: "<?= SRC ?>/",  // index.php로
		dataType: "json",
		data: data,
		cache: false
	  }).done(function(response){
		// 로딩 플래그 제거
		contentArea.dataset.loading = 'false';

		// 응답 검증 및 오류 처리
		if (!response) {
		  console.error('응답이 없습니다.');
		  contentArea.innerHTML = '<div class="table-text-center text-danger"><p>페이지를 불러올 수 없습니다.</p></div>';
		  return;
		}

		// 오류 응답 처리
		if (response.result === 'error') {
		  console.error('서버 오류:', response.msg || response.error?.msg || '알 수 없는 오류');
		  const errorMsg = response.msg || response.error?.msg || '페이지를 불러올 수 없습니다.';
		  contentArea.innerHTML = `<div class="table-text-center text-danger"><p>${errorMsg}</p></div>`;
		  return;
		}

		// 정상 응답 처리
		if (response.result === 'ok' && response.html) {
		  // HTML을 직접 삽입
		  contentArea.innerHTML = response.html;

		  // 스크립트를 수동으로 실행
		  contentArea.querySelectorAll('script').forEach(function(oldScript) {
			const newScript = document.createElement('script');

			if (oldScript.src) {
			  // 외부 스크립트
			  newScript.src = oldScript.src;
			  newScript.async = false;
			} else {
			  // 인라인 스크립트
			  newScript.text = oldScript.text || oldScript.textContent || oldScript.innerHTML;
			}

			// 속성 복사
			if (oldScript.type) newScript.type = oldScript.type;
			if (oldScript.id) newScript.id = oldScript.id;

			// 기존 스크립트 제거 후 새로 추가
			oldScript.parentNode.replaceChild(newScript, oldScript);
		  });
		} else {
		  console.error('응답 형식이 올바르지 않습니다:', response);
		  contentArea.innerHTML = '<div class="table-text-center text-warning"><p>페이지 형식이 올바르지 않습니다.</p></div>';
		}
	  }).fail(function(xhr, status, error){
		// 로딩 플래그 제거
		contentArea.dataset.loading = 'false';

		console.error('AJAX 통신 오류:', status, error);
		console.error('응답 내용:', xhr.responseText);
		contentArea.innerHTML = `<div class="table-text-center text-danger"><p>페이지를 불러올 수 없습니다.<br>오류: ${error}</p></div>`;
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
		url: "<?= SRC ?>/" + window.pageName,  // .htaccess가 _ajax_.php로 라우팅
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

	// 드롭다운 메뉴 토글 (클릭 방식)
	document.addEventListener('DOMContentLoaded', function() {
		// 드롭다운 토글 클릭 시
		document.querySelectorAll('.dropdown-toggle').forEach(function(toggle) {
			toggle.addEventListener('click', function(e) {
				e.stopPropagation();
				const dropdown = this.closest('.dropdown');
				const isOpen = dropdown.classList.contains('open');

				// 다른 드롭다운 닫기
				document.querySelectorAll('.dropdown.open').forEach(function(d) {
					d.classList.remove('open');
				});

				// 현재 드롭다운 토글
				if (!isOpen) {
					dropdown.classList.add('open');
				}
			});
		});

		// 외부 클릭 시 드롭다운 닫기
		document.addEventListener('click', function() {
			document.querySelectorAll('.dropdown.open').forEach(function(d) {
				d.classList.remove('open');
			});
		});

		// 드롭다운 메뉴 내부 클릭 시 전파 중단
		document.querySelectorAll('.dropdown-menu').forEach(function(menu) {
			menu.addEventListener('click', function(e) {
				e.stopPropagation();
			});
		});
	});

	/// <summary>
	/// 지정된 기간 타입에 따라 시작일과 종료일을 설정하여 input 요소에 값을 할당하는 함수
	/// </summary>
	/// <param name="type">설정할 기간 타입 (예: 'today', 'week', 'thisMonth' 등)</param>
	function setDate(type, pid = '') {
		const today = new Date();
		let start = new Date();
		let end = new Date();

		/// <summary>
		/// 특정 연도와 월의 마지막 날짜를 반환
		/// </summary>
		/// <param name="year">연도</param>
		/// <param name="month">월 (0부터 시작)</param>
		/// <returns>마지막 날짜를 가진 Date 객체</returns>
		const getLastDateOfMonth = (year, month) => new Date(year, month + 1, 0);

		switch (type) {
			case 'today':
				// 오늘부터 오늘까지 (start와 end가 오늘)
				break;

			case 'yesterday':
				// 어제부터 어제까지
				start = new Date(today);
				end = new Date(today);
				start.setDate(start.getDate() - 1);
				end.setDate(end.getDate() - 1);
				break;

			case 'week':
				// 7일 전부터 오늘까지
				start = new Date(today);
				start.setDate(start.getDate() - 7);
				break;

			case 'thisWeek': {
				// 이번 주 월요일부터 오늘까지
				const dayOfWeek = today.getDay();
				const monday = new Date(today);
				// 일요일(0)인 경우 6일 전, 그 외는 (dayOfWeek - 1)일 전이 월요일
				monday.setDate(today.getDate() - (dayOfWeek === 0 ? 6 : dayOfWeek - 1));
				start = monday;
				end = today;
				break;
			}

			case 'prevWeek': {
				// 지난 주 월요일부터 일요일까지
				const dayOfWeek = today.getDay();
				const lastSunday = new Date(today);
				// 지난 주 일요일
				lastSunday.setDate(today.getDate() - (dayOfWeek === 0 ? 0 : dayOfWeek));
				const lastMonday = new Date(lastSunday);
				lastMonday.setDate(lastSunday.getDate() - 6);
				start = lastMonday;
				end = lastSunday;
				break;
			}

			case '30days':
				// 30일 전부터 오늘까지
				start = new Date(today);
				start.setDate(start.getDate() - 30);
				break;

			case 'thisMonth':
				// 이번 달 1일부터 오늘까지
				start = new Date(today.getFullYear(), today.getMonth(), 1);
				end = today;
				break;

			case 'prevMonth': {
				// 이전 달 1일부터 그 달의 마지막 날까지
				const prevMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
				start = new Date(prevMonth.getFullYear(), prevMonth.getMonth(), 1);
				end = getLastDateOfMonth(prevMonth.getFullYear(), prevMonth.getMonth());
				break;
			}

			case '3months':
			case '6months':
			case '1year':
			case '3year':
			case '5year':
			case '10year':
			case '15year':
			case '20year': {
				// 해당 기간만큼 과거부터 이전 달 말일까지
				const monthOffset = {
					'3months': 3,
					'6months': 6,
					'1year': 12,
					'3year': 36,
					'5year': 60,
					'10year': 120,
					'15year': 180,
					'20year': 240
				}[type];

				const prevMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
				end = getLastDateOfMonth(prevMonth.getFullYear(), prevMonth.getMonth());
				start = new Date(prevMonth.getFullYear(), prevMonth.getMonth() - (monthOffset - 1), 1);
				break;
			}
		}

		// 요소가 없는 경우 오류 방지용 체크 추가
		const startElem = document.getElementById('startDate' + pid);
		const endElem = document.getElementById('endDate' + pid);
		if (startElem) startElem.value = formatDate(start);
		if (endElem) endElem.value = formatDate(end);
	}

	/**
	 * 공용 페이징 시스템
	 * Common Pagination System
	 */
	window.changePage = function(elem, atValue) {
		const pageNum = elem.getAttribute('data-p');
		const encryptedSearch = elem.getAttribute('data-e');
		const targetId = elem.getAttribute('data-id');

		if (!pageNum || !atValue) {
			console.error('페이징 파라미터가 없습니다:', { pageNum, atValue });
			return;
		}

		// FormData 생성
		const formData = new FormData();
		formData.append('p', pageNum);
		formData.append('at', atValue);

		if (encryptedSearch) {
			formData.append('e', encryptedSearch);
		}

		// 현재 페이지의 검색 폼에서 추가 파라미터 수집
		const searchArea = elem.closest('.tab-page, .wrap, .container')?.querySelector('.filter-toolbar, .search-bar');
		if (searchArea) {
			const inputs = searchArea.querySelectorAll('input, select, textarea');
			inputs.forEach(input => {
				if (input.name && input.value && input.type !== 'button' && input.type !== 'submit') {
					// 체크박스/라디오는 체크된 것만
					if ((input.type === 'checkbox' || input.type === 'radio')) {
						if (input.checked) {
							formData.append(input.name, input.value);
						}
					} else {
						formData.append(input.name, input.value);
					}
				}
			});
		}

		// updateAjaxContent 함수 사용
		updateAjaxContent(formData, function(data) {
			if (data.result) {
				// 테이블 업데이트
				if (targetId && data.html) {
					const tbody = document.querySelector(targetId);
					if (tbody) {
						tbody.innerHTML = data.html;
					} else {
						console.warn('타겟 요소를 찾을 수 없습니다:', targetId);
					}
				}

				// 페이징 업데이트
				if (data.pagination) {
					// targetId를 사용하여 고정된 .paging 컨테이너 찾기
					const pagingContainer = targetId ? document.querySelector('.paging[data-id="' + targetId + '"]') : null;
					if (pagingContainer) {
						pagingContainer.innerHTML = data.pagination;
					}
				}

				// 스크롤을 테이블 상단으로 이동
				if (targetId) {
					const target = document.querySelector(targetId);
					if (target) {
						const tableTop = target.closest('.table-scroll, .table-wrap, table');
						if (tableTop) {
							tableTop.scrollIntoView({ behavior: 'smooth', block: 'start' });
						}
					}
				}
			} else {
				console.error('페이징 요청 실패:', data.error || data);
				if (data.error && data.error.msg) {
					alert('오류: ' + data.error.msg);
				}
			}
		});
	};

	/**
	 * 페이지 로드 시 페이징 초기화
	 */
	document.addEventListener('DOMContentLoaded', function() {
		console.log('공용 페이징 시스템 초기화됨');

		// 모든 .pagination 요소에 이벤트 위임 설정 (동적 생성 지원)
		document.addEventListener('click', function(e) {
			const link = e.target.closest('.pagination a, .paging a');
			if (link && link.hasAttribute('data-p')) {
				e.preventDefault();

				const atValue = link.closest('[data-at]')?.getAttribute('data-at') ||
							   link.getAttribute('onclick')?.match(/'([^']+)'/)?.[1];

				if (atValue) {
					changePage(link, atValue);
				} else {
					console.warn('atValue를 찾을 수 없습니다. onclick 속성을 확인하세요.');
				}
			}
		});
	});

</script>