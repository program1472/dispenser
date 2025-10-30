<?php
	// 로그인 id의 연결된 `roles` 테이블의 code에 따라서 hq, vender, customer 폴더의 inc 폴더의 bottomArea.php 로딩
?>
		<footer class="small">© 2025 AllToGreen — <?= $_SESSION['role'] ?> Portal</footer>
		<div id="modal" class="modal" style="display: none;">
		  <div class="panel">
			<div class="panel-hd"><div id="modalTitle">요청 상세</div><div class="close" id="modalClose">✕</div></div>
			<div class="panel-bd" id="modalBody"></div>
		  </div>
		</div>
		<div class="toast-wrap" id="toasts"></div>
	</body>
</html>