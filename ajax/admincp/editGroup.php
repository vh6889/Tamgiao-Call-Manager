<?php

$_id = isset($_REQUEST['id']) ? intval(trim($_REQUEST['id'])) : '';
$name = isset($_REQUEST['name']) ? trim($_REQUEST['name']) : '';
$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';
$offers = isset($_REQUEST['offers']) ? trim($_REQUEST['offers']) : '';
$leader = isset($_REQUEST['leader']) ? trim($_REQUEST['leader']) : '';
$payout = isset($_REQUEST['payout']) ? abs(intval(trim($_REQUEST['payout']))) : 0;
$payout_type = isset($_REQUEST['payout_type']) ? trim($_REQUEST['payout_type']) : 'fixed';
$deduct = isset($_REQUEST['deduct']) ? abs(intval(trim($_REQUEST['deduct']))) : 0;

$exp_offers = explode(",", trim($offers,","));


$group = getGroup($_id);
if(!isAdmin())
	exit('{"status":403,"message":"Truy cập bị từ chối!"}');

if(!$group['id'])
    exit('{"status":403,"message":"Nhóm không tồn tại!"}');

if(empty($name) || empty($type) || count($exp_offers) <= 0)
	exit('{"status":403,"message":"Vui lòng nhập đầy đủ các trường bắt buộc!"}');
else {

    if(!typeGroup($type))
        exit('{"status":403,"message":"Loại nhóm không tồn tại!"}');

    $off = "";

    foreach ($exp_offers as $of){
        if($_db->query("select `id` from `core_offers` where `id`='".escape_string($of)."' ")->num_rows() > 0)
            $off .= '|'.trim($of).',';
    }


    if($off == "")
        exit('{"status":403,"message":"Không tìm thấy ID offer nào!"}');

    $user  =  $_db->query("select `id`,`adm` from `core_users` where `id`='".escape_string($leader)."' and `group`='".escape_string($_id)."' limit 1 ")->fetch();
    if($leader && !$user)
        exit('{"status":403,"message":"Leader phải là người trong nhóm này!"}');


    if($_db->exec_query("update `core_groups` set `name`='".escape_string($name)."',`type`='".escape_string($type)."',`payout`='".escape_string($payout)."',`payout_type`='".escape_string($payout_type)."',`deduct`='".escape_string($deduct)."',`leader`='".escape_string($leader)."',`offers`='".escape_string($off)."' where `id`='".escape_string($_id)."' ")){

        $type = isAdmin($user) ? 'admin' : typeMemGroup($type,true); 

    	if($leader){
            $member_type = $type=='publisher_leader' ? 'publisher_member' : 'call_member';
            $_db->exec_query("update `core_users` set `type`='".$member_type."' where `group`='".escape_string($_id)."' ");
    		$_db->exec_query("update `core_users` set `group_payout`='".escape_string($payout)."',`group_deduct`='".escape_string($deduct)."',`type`='".escape_string($type)."' where `id`='".escape_string($leader)."' ");
        }
    	exit('{"status":200,"message":"Lưu lại thành công!"}');
    }
    else
    	exit('{"status":403,"message":"Có lỗi xảy ra vui lòng thử lại sau."}');

} 
	

?>