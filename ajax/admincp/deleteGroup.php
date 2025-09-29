<?php

$_id = isset($_REQUEST['id']) ? intval(trim($_REQUEST['id'])) : '';

$_group = getGroup($_id);
if(!isAdmin())
	exit('{"status":403,"message":"Truy cập bị từ chối!"}');

if(!$_group['id'])
	exit('{"status":403,"message":"Không tìm thấy ID group!"}');

if($_user['id'] == $_group['leader'] && isAdmin())
	exit('{"status":403,"message":"Không thể xóa nhóm này!"}');

if($_db->exec_query("delete from `core_groups`  where `id`='".escape_string($_id)."' ")){
	$_db->exec_query("update `core_users` set `group`='',`group_payout`='0',`group_deduct`='0',`active`='0' where `group`='".escape_string($_id)."' ");

	$_db->exec_query("UPDATE `core_orders` set `group`='',`payout_leader`='',`payout_member`='',`deduct_leader`='',`deduct_member`='',`user_call`='',`user_ship`='',`call_time`='',`ship_time`='',`status`='uncheck',`r_hold`='',`r_deduct`='',`r_approve`='' where `group`='".escape_string($_id)."' and `status`='calling' ");

	$memGroup = memGroup($_group['id']);
	$notifi_mem = array();
	foreach ($memGroup as $mem)
		$notifi_mem[] = $mem['id'];

    $notifi_title = '<strong class="trigger red lighten-2 text-white">Nhóm đã bị xóa</strong>';
    $notifi_text = '<strong>Nhóm (<b>'._e($group['name']).'</b>) của bạn đã bị xóa bởi: <span class="text-danger">'.$_user['name'].'</span></strong>.<br> Vui lòng liên hệ với quản trị viên để biết thêm thông tin chi tiết.';
    addNotification($notifi_title,$notifi_text,$notifi_mem);

	exit('{"status":200,"message":"Xóa thành công!"}');
}
else
	exit('{"status":403,"message":"Có lỗi xảy ra vui lòng thử lại sau."}');


	

?>