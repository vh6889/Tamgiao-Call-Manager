<?php

$_id = isset($_REQUEST['id']) ? intval(trim($_REQUEST['id'])) : '';
$members = isset($_REQUEST['member']) ? trim($_REQUEST['member']) : '';

$_group = getGroup($_id);
$exp_members = explode(",", trim($members,","));

if(!isAdmin())
	exit('{"status":403,"message":"Truy cập bị từ chối!"}');

if(!$_group['id'])
    exit('{"status":403,"message":"Nhóm thêm vào không tồn tại"}');

if(count($exp_members) <= 0)
	exit('{"status":403,"message":"Vui lòng chọn ít nhất một thành viên muốn thêm vào nhóm."}');
else {


    $type = typeMemGroup($_group['type']);

    $users = $_db->query("select `revenue_pending`,`revenue_approve`,`revenue_deduct` from `core_users` where `id` in ('".implode("','", $exp_members)."') ")->fetch_array();

    $pending = 0;
    $approve = 0;
    $deduct = 0;
    foreach ($users as $user) {
    	$pending = $pending + $user['revenue_pending'];
    	$approve = $approve + $user['revenue_approve'];
    	$deduct = $deduct + $user['revenue_deduct'];
    }

    $_db->exec_query("UPDATE `core_groups` set `revenue_pending`=`revenue_pending`+'".$pending."',`revenue_approve`=`revenue_approve`+'".$approve."',`revenue_deduct`=`revenue_deduct`+'".$deduct."' where `id`='".$_group['id']."' ");

    if($_db->exec_query("update `core_users` set `group`='".escape_string($_group['id'])."',`group_payout`='0',`group_deduct`='0',`type`='".escape_string($type)."' where `id` in ('".implode("','", $exp_members)."') ")){

        $notifi_title = '<strong class="trigger green lighten-2 text-white">Bạn vừa được thêm vào nhóm mới</strong>';
        $notifi_text = '<strong>Bạn vừa được thêm vào nhóm (<b>'._e($_group['name']).'</b>) bởi: <span class="text-danger">'.$_user['name'].'</span></strong>.<br> Mọi thắc mắc vui lòng liên hệ với trưởng nhóm hoặc quản trị viên để biết thêm thông tin chi tiết.';
        addNotification($notifi_title,$notifi_text,$exp_members);

    	exit('{"status":200,"message":"Thêm thành công!"}');
    }
    else
    	exit('{"status":403,"message":"Có lỗi xảy ra vui lòng thử lại sau."}');

} 
	

?>