<?php

$_id = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : '';
$_group = isset($_REQUEST['group']) ? intval(trim($_REQUEST['group'])) : '';
$active = isset($_REQUEST['active']) && $_REQUEST['active'] == 1? 1 : 0;
$payout = isset($_REQUEST['payout']) ? abs(intval(trim($_REQUEST['payout']))) : 0;
$deduct = isset($_REQUEST['deduct']) ? abs(intval(trim($_REQUEST['deduct']))) : 0;

$auto_ban = isset($_REQUEST['auto_ban']) ? abs(intval(trim($_REQUEST['auto_ban']))) : 0;
$ban_limit = isset($_REQUEST['ban_limit']) ? abs(intval(trim($_REQUEST['ban_limit']))) : 0;
$ban_rate = isset($_REQUEST['ban_rate']) ? abs(intval(trim($_REQUEST['ban_rate']))) : 0;

$exp_id = explode(",", trim($_id,","));

$group = getGroup($_group);

if(!isAdmin() && $_user['id'] != $group['leader'] && !in_array($_user['id'],$exp_id))
	exit('{"status":403,"message":"Truy cập bị từ chối!"}');

if(!$group['id'])
    exit('{"status":403,"message":"Nhóm không tồn tại!"}');

if(!is_numeric($payout) || $payout < 0)
    exit('{"status":403,"message":"Giá triết khấu phải lớn hơn hoặc bằng 0!"}');


if(!is_numeric($deduct) || $deduct < 0)
    exit('{"status":403,"message":"Giá khấu trừ sau  phải lớn hơn hoặc bằng 0!"}');

if(empty($active) && $active != 0)
	exit('{"status":403,"message":"Trạng thái tài khoản không hợp lệ!"}');

else if(($payout< 0 || ($payout > $group['payout'])) && $group['type'] != "shipping")
    exit('{"status":403,"message":"Payout chỉ có thể trong khoảng từ 0 - '.$group['payout'].'"}');

else if(($deduct< 0 || ($deduct > $group['deduct'])) && $group['type'] != "shipping")
    exit('{"status":403,"message":"deduct chỉ có thể trong khoảng từ 0 - '.$group['deduct'].'"}');

else if($payout < $deduct && $group['type'] != "shipping")
    exit('{"status":403,"message":"Payout phải lớn hơn hoặc bằng Deduct"}');

else {



    $user = array();
    $user_active = array();
    $user_deactive = array();

    foreach ($exp_id as $i){
        $u = getUser(trim($i));
        if($u['id'])
            $user[] = trim($i);
        if(!isBanned($u))
            $user_active[] = $u['id'];
        else
            $user_deactive[] = $u['id'];
    }


    if(!$user)
        exit('{"status":403,"message":"Không tìm thấy ID member nào!"}');

    $sql = "";
    if(isAdmin())
        $sql = ",`auto_ban`='".escape_string($auto_ban)."',`ban_limit`='".escape_string($ban_limit)."',`ban_rate`='".escape_string($ban_rate)."' ";




    if($_db->exec_query("update `core_users` set `group_payout`='".escape_string($payout)."',`group_deduct`='".escape_string($deduct)."',`active`='".escape_string($active)."' ".$sql." where `id` in ('".implode("','",$user)."') ")){

        if($active != 1){
            
            $notifi_title = '<strong class="trigger red lighten-2 text-white">Tài khoản đã bị cấm</strong>';
            $notifi_text = '<strong>Tài khoản của bạn hiện đã bị cấm bởi: <span class="text-danger">'.$_user['name'].'</span></strong>.<br> Vui lòng liên hệ với trưởng nhóm hoặc quản trị viên để biết thêm thông tin chi tiết.';
            addNotification($notifi_title,$notifi_text,$user_active);
        } else {
            $notifi_title = '<strong class="trigger green lighten-2 text-white">Tài khoản đã mở lại</strong>';
            $notifi_text = 'Tài khoản của bạn hiện vừa được mở lại bởi: <span class="text-danger">'.$_user['name'].'</span>.';
            addNotification($notifi_title,$notifi_text,$user_deactive);
        }

        if(in_array($group['leader'], $user))
            $_db->exec_query("update `core_groups` set `payout`='".escape_string($payout)."',`deduct`='".escape_string($deduct)."' where `id`='".$group['id']."' ");

    	exit('{"status":200,"message":"Lưu lại thành công!"}');
    }
    else
    	exit('{"status":403,"message":"Không có gì cần lưu lại."}');

} 
	

?>