<?php

$_id = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : '';

$exp_id = explode(",", trim($_id,","));

if(isBanned())
	exit('{"status":403,"message":"Không thể chọn đơn hàng khi đang bị cấm."}');

if(!isShipper())
	exit('{"status":403,"message":"Tài khoản của bạn không phải là shipper !"}');

$orders = $_db->query("select * from `core_orders` where `id` in ('".implode("','", $exp_id)."') and `status`='shipdelay' and `user_ship`='' ")->fetch_array();


if(!$orders)
	exit('{"status":403,"message":"Không tìm thấy đơn hàng!"}');

if($_db->exec_query("update `core_orders` set `user_ship`='".escape_string($_user['id'])."' where `id` in ('".implode("','", $exp_id)."') and `status`='shipdelay' and `user_ship`='' ")){
	exit('{"status":200,"message":"Nhận đơn hàng thành công."}');
} else 
	exit('{"status":403,"message":"Có vẻ như ai đó đã nhận đơn hàng này sớm hơn bạn."}');


	

?>