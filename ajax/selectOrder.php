<?php

$_id = isset($_REQUEST['id']) ? intval(trim($_REQUEST['id'])) : '';




if(!isCaller())
	exit('{"status":403,"message":"Tài khoản của bạn không phải là caller !"}');


$order = $_db->query("select * from `core_orders` where `id`='".escape_string($_id)."' ")->fetch();
$calling = getCalling($_user);
$group = getGroup($_user['group']);

if(!$order['id'])
	exit('{"status":403,"message":"Không tìm thấy đơn hàng!"}');

if(isBanned())
	exit('{"status":403,"message":"Không thể gọi hàng khi đang bị cấm."}');

if($calling && !isAdmin())
	exit('{"status":403,"message":"Bạn chỉ có thể gọi cùng lúc tối đa 01 đơn hàng."}');

if($order['status'] != "uncheck" && $order['user_call'])
	exit('{"status":403,"message":"Đơn hàng đã được gọi bởi một ai đó."}');
else if(isset($calling['id']) && $calling['id'] == $order['id'])
	exit('{"status":403,"message":"Đơn hàng đã được thêm vào Calling."}');
else {
	$orderOffer = getOffer($order['offer']);
	
	if(isset($orderOffer['payout']) && $orderOffer['payout']) {
		$payoutLeader = $orderOffer['payout'];
		$payoutMember = $orderOffer['payout'];
		$orderPayoutType =  $orderOffer['payout_type'];
	}else{
		$payoutLeader = $group['payout'];
		$payoutMember = $_user['group_payout'];
		$orderPayoutType =  $group['payout_type'];
	}

    if($_db->exec_query("update `core_orders` set `user_call`='".escape_string($_user['id'])."',`call_time`='".time()."',`group`='".escape_string($_user['group'])."',`status`='calling',`payout_leader`='".escape_string($payoutLeader)."',`payout_member`='".escape_string($payoutMember)."',`payout_type`='".escape_string($orderPayoutType)."',`deduct_leader`='".escape_string($group['deduct'])."',`deduct_member`='".escape_string($_user['group_deduct'])."' where `id`='".escape_string($order['id'])."' ")){

    	exit('{"status":200,"message":"Đơn hàng đã được chuyển đến Calling."}');
    }
    else
    	exit('{"status":403,"message":"Có lỗi xảy ra vui lòng thử lại sau."}');

} 
	

?>