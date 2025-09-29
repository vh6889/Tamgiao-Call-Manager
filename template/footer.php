			</div>
		</main>
		<?php 
		$query_approved = $_db->query("select id,update_time,user_call,user_ship,status,is_noti from `core_orders` where `status`='approved' and `is_noti`=0 order by id desc ")->fetch_array();					
		foreach($query_approved as $noti_approved){
			$now = date('Y-m-d');			
			$date = date('Y-m-d',($noti_approved['update_time']+7*24*60*60)); 						
			if(strtotime($now) == strtotime($date)){
				$notifi_title = '<strong class="trigger green lighten-2 text-white">Thông báo chăm sóc khách hàng</strong>';
				$notifi_text = '<strong>Đơn hàng: <a target="_blank" href="?route=editOrder&id='.$noti_approved['id'].'"><span class="text-success">#'.$noti_approved['id'].'</span></a> vừa được yêu cầu chăm sóc khách hàng</strong>.';
				addNotification($notifi_title,$notifi_text,$noti_approved['user_call']);  
				addNotification($notifi_title,$notifi_text,$noti_approved['user_ship']);  
				$data_notifi_support = [
					'is_noti' => 1
				];
				createOrUpdate('core_orders',$noti_approved['id'],$data_notifi_support);
			}
		}
	?> 
		<!-- Main layout -->

		<!-- SCRIPTS -->

		<!-- Bootstrap tooltips -->
		<script type="text/javascript" src="template/assets/js/popper.min.js"></script>
		<!-- Bootstrap core JavaScript -->
		<script type="text/javascript" src="template/assets/js/bootstrap.js"></script>
		<!-- MDB core JavaScript -->
		<script type="text/javascript" src="template/assets/js/mdb.js"></script>
		<script type="text/javascript" src="template/assets/js/custom.js?t=<?=time();?>"></script>
		<!--Custom scripts-->
		<script>
			// SideNav Initialization
			$(".button-collapse").sideNav({
				breakpoint: 992
			});

			var container = document.querySelector('.custom-scrollbar');
			Ps.initialize(container, {
			wheelSpeed: 2,
			wheelPropagation: true,
			minScrollbarLength: 20
			});


			var $nav = '<?=_e($_nav);?>',
				$nav_li = '<?=_e($_nav_li);?>';
			$(document).ready(function(){
				if($nav){
					$('[role='+$nav+']').addClass("active");
					$('[role='+$nav+'] > a').addClass("active");
					$('[role='+$nav+'] > .collapsible-body').show();		
				}

				if($nav_li)
					$('[role='+$nav+'] li[data-item='+$nav_li+']').addClass("current-menu-item");
			});


			var delay = (function () {
				var timer = 0;
				return function (callback, ms) {
					clearTimeout(timer);
					timer = setTimeout(callback, ms);
				};
			})();

			$('#searchAll').keyup(function () {
				delay(function () {
					Suggest();
				}, 500);
			});

			function Suggest() {
				var keyword = $.trim($('#searchAll').val());
				if (keyword != "") {
					$('#result_box').html("<span id='loader'></span>");
					$('#result_box').css('display', 'block');
					$.ajax({
						type: "POST",
						url: "ajax.php?act=search",
						data: {keyword:keyword},
						success: function (message) {
							if (message != "") {
								$('#result_box').html(message);
							} else {
								$('#result_box').html('');
								$('#result_box').css('display', 'none');
							}
						}
					});
				} else {
					$('#result_box').html('');
					$('#result_box').css('display', 'none');
				}
			}

			function setCookie(name,value,days) {
			    var expires = "";
			    if (days) {
			        var date = new Date();
			        date.setTime(date.getTime() + (days*24*60*60*1000));
			        expires = "; expires=" + date.toUTCString();
			    }
			    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
			}

		</script>		
	</body>
    <!-- Ajax Loader -->
    <div class="ajax-loader">
        <div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
    </div>
</html>