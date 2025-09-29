<?php

$_id = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : '';
$_group = isset($_REQUEST['group']) ? intval(trim($_REQUEST['group'])) : '';
$_forever = isset($_REQUEST['forever']) ? intval(trim($_REQUEST['forever'])) : 0;


if(!isAdmin())
	$_forever = 0;



$exp_id = explode(",", trim($_id,","));

$group = getGroup($_group);

if(!$exp_id)
	exit('{"status":403,"message":"ID delete not found!"}');

if(!isAdmin() && $_user['id'] != $group['leader']  && !in_array($_user['id'],$exp_id))
	exit('{"status":403,"message":"Truy cập bị từ chối!"}');


$status = 0;
$error = "Không tìm thấy ID member nào!";

$notifi_mem = array();

foreach ($exp_id as $i){
	$user = $_db->query("select * from `core_users` where `id`='".escape_string(trim($i))."' and `group`='".$group['id']."' ")->fetch();


	if(count($exp_id) <= 1 && $user['id'] == $_user['id'])
		$error = "Bạn không thể xóa chính mình.";

    if($user && $user['id'] != $_user['id']){

    	

		if($_db->exec_query("UPDATE `core_users` set `group`='' where `id`='".$user['id']."' ")){

			$sql = "";
			if($user['id'] == $group['leader'])
				$sql = " ,`leader`='' ";
				
			$_db->exec_query("UPDATE `core_groups` set `revenue_pending`=`revenue_pending`-'".$user['revenue_pending']."',`revenue_approve`=`revenue_approve`-'".$user['revenue_approve']."',`revenue_deduct`=`revenue_deduct`-'".$user['revenue_deduct']."' ".$sql." where `id`='".$group['id']."' ");

			$_db->exec_query("UPDATE `core_orders` set `group`='',`payout_leader`='',`payout_member`='',`deduct_leader`='',`deduct_member`='',`user_call`='',`user_ship`='',`call_time`='',`ship_time`='',`status`='uncheck',`r_hold`='',`r_deduct`='',`r_approve`='' where (`user_call`='".escape_string($user['id'])."' or  `user_ship`='".escape_string($user['id'])."') and `status`='calling' ");

			if( $_forever == 1){
				$_db->exec_query("delete from `core_users` where `id`='".$user['id']."' ");
			} else 
				$notifi_mem[] = $user['id'];
			$status++;
		}
    }
}


if($status < 1)
    exit('{"status":403,"message":"'.$error.'"}');
else {
    $notifi_title = '<strong class="trigger red lighten-2 text-white">Tài khoản bị xóa khỏi nhóm</strong>';
    $notifi_text = '<strong>Tài khoản của bạn đã bị xóa khỏi nhóm (<b>'._e($group['name']).'</b>) bởi: <span class="text-danger">'.$_user['name'].'</span></strong>.<br> Vui lòng liên hệ với trưởng nhóm hoặc quản trị viên để biết thêm thông tin chi tiết.';
    addNotification($notifi_title,$notifi_text,$notifi_mem);
	exit('{"status":200,"message":"Xóa thành công!"}');
}


	

?>