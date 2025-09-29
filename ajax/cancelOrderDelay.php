<?php

$_id = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : '';

$exp_id = explode(",", trim($_id,","));

if(isBanned())
	exit('{"status":403,"message":"Không thể hủy chọn đơn hàng khi đang bị cấm."}');

if(!isShipper())
	exit('{"status":403,"message":"Tài khoản của bạn không phải là shipper !"}');

$sql = "";
if(!isLeader())
	$sql = " `id` in ('".implode("','", $exp_id)."') and `status`='shipdelay' and `user_ship`='".$_user['id']."' ";
else {

	if(isAller()){
		$sql = " `id` in ('".implode("','", $exp_id)."') and `status`='shipdelay' and `user_ship`!='' ";
	} else {
		  $memGroup = memberGroup($_user['group']);
		  $user_ship = array();
		  foreach ($memGroup as $us)
		    $user_ship[] = $us['id'];
		$sql = " `id` in ('".implode("','", $exp_id)."') and `status`='shipdelay' and `user_ship` in ('".implode("','", $user_ship)."') ";		
	}

}

$orders = $_db->query("select * from `core_orders` where ".$sql)->fetch_array();	

if(!$orders)
	exit('{"status":403,"message":"Đơn hàng không tồn tại hoặc không thuộc quyền quản lí của bạn!"}');


if($_db->exec_query("update `core_orders` set `user_ship`='',`ship_time`='' where ".$sql)){
	exit('{"status":200,"message":"Hủy vận chuyển đơn hàng thành công."}');
} else 
	exit('{"status":403,"message":"Có lỗi xảy ra vui lòng thử lại sau."}');


	

?>