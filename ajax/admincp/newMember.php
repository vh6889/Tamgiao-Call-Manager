<?php

$_id = isset($_REQUEST['id']) ? intval(trim($_REQUEST['id'])) : '';
$username = isset($_REQUEST['username']) ? trim($_REQUEST['username']) : '';
$password = isset($_REQUEST['password']) ? trim($_REQUEST['password']) : '';
$repassword = isset($_REQUEST['repassword']) ? trim($_REQUEST['repassword']) : '';
$payout = isset($_REQUEST['payout']) ? abs(intval(trim($_REQUEST['payout']))) : 0;
$deduct = isset($_REQUEST['deduct']) ? abs(intval(trim($_REQUEST['deduct']))) : 0;

$_group = getGroup($_id);

if(!isAdmin() && $_group['leader'] != $_user['id'])
	exit('{"status":403,"message":"Truy cập bị từ chối!"}');

if(!$_group['id'])
	exit('{"status":403,"message":"Nhóm không tồn tại!"}');

if(empty($username) || empty($password) || empty($repassword))
	exit('{"status":403,"message":"Vui lòng nhập đầy đủ các trường bắt buộc!"}');
else if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $username))
	exit('{"status":403,"message":"Tên đăng nhập không hợp lệ!"}');
else if($password != $repassword)
	exit('{"status":403,"message":"Mật khẩu nhập lại không chính xác!"}');
else if(($payout< 0 || ($payout > $_group['payout'])) && $group['type'] != "shipping" )
	exit('{"status":403,"message":"Payout chỉ có thể trong khoảng từ 0 - '.$_group['payout'].'"}');
else if(($deduct< 0 || ($deduct > $_group['deduct'])) && $group['type'] != "shipping" )
	exit('{"status":403,"message":"deduct chỉ có thể trong khoảng từ 0 - '.$_group['deduct'].'"}');
else if($payout < $deduct && $group['type'] != "shipping")
	exit('{"status":403,"message":"Payout phải lớn hơn hoặc bằng Deduct"}');
else {

	if($_db->query("select `id` from `core_users` where `name` like '".escape_string($username)."' ")->num_rows() > 0)
		exit('{"status":403,"message":"Username này đã tồn tại trên hệ thống!"}');

	$type = typeMemGroup($_group['type']);


	/* Render user's randomkey */
	$ukey = generateRandomUserKey();

    if($_db->exec_query("insert into `core_users` set `name`='".escape_string($username)."',`pass`='".escape_string(md5(md5($password)))."',`group`='".escape_string($_group['id'])."',`group_payout`='".escape_string($payout)."',`group_deduct`='".escape_string($deduct)."',`type`='".escape_string($type)."',`active`='1',`ukey`='".$ukey."' "))
    	exit('{"status":200,"message":"Thêm thành công: '._e($username).'"}');
    else
    	exit('{"status":403,"message":"Có lỗi xảy ra vui lòng thử lại sau."}');

} 
	

?>