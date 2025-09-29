<?php

$_id = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : '';

$exp_id = explode(",", trim($_id,","));


if(!isCaller())
	exit('{"status":403,"message":"Tài khoản của bạn không phải là caller !"}');


$sql = "";
if(!isLeader())
	$sql = " `id` in ('".implode("','", $exp_id)."') and `status`='calling' and `user_call`='".$_user['id']."' ";
else {

	if(isAller()){
		$sql = " `id` in ('".implode("','", $exp_id)."') and `status`='calling' and `user_call`!='' ";
	} else {
		$sql = " `id` in ('".implode("','", $exp_id)."') and `status`='calling' and `group`='".$_user['group']."' ";		
	}

}

$orders = $_db->query("select * from `core_orders` where ".$sql)->fetch_array();	

if(!$orders)
	exit('{"status":403,"message":"Đơn hàng không tồn tại hoặc không thuộc quyền quản lí của bạn!"}');


if($_db->exec_query("update `core_orders` set `status`='uncheck',`user_call`='',`call_time`='' where ".$sql)){
	exit('{"status":200,"message":"Hủy gọi đơn hàng thành công."}');
} else 
	exit('{"status":403,"message":"Có lỗi xảy ra vui lòng thử lại sau."}');


	

?>