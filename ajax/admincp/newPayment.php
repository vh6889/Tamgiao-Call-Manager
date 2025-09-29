<?php

$_id = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : '';


if(!isAdmin())
	exit('{"status":403,"message":"Truy cập bị từ chối!"}');

$_group = getGroup($_id);
$_leader = getUser($_group['leader']);
$_mem = memberGroup($_group['id']);

if(!$_group['id'])
	exit('{"status":403,"message":"Nhóm không tồn tại!"}');

if(!$_leader)
	exit('{"status":403,"message":"Nhóm phải có leader để nhận thanh toán!"}');

if($_group['type'] == "ship")
	exit('{"status":403,"message":"Chỉ có thể thanh toán cho nhóm Call!"}');

$status = 0;


$paid = $_group['revenue_approve']-$_group['revenue_deduct'];
$approve = $_group['revenue_approve'];
$pending = $_group['revenue_pending'];
$deduct = $_group['revenue_deduct'];

if($_db->query("select `id` from `core_payments` where `status`='pending' and `type`='1' and `user_id`='".$_group['leader']."' ")->num_rows() > 0)
	exit('{"status":403,"message":"Không thể tạo hóa đơn mới khi vẫn tồn tại một hóa đơn cũ chưa xử lí."}');

if($paid < 1)
	exit('{"status":403,"message":"Không đủ số tiền thanh toán tối thiểu."}');

if($_db->exec_query("insert into `core_payments` set `user_id` = '".$_leader['id']."',`type`='0',`status`='pending',`time`='".time()."',`paid`='".$paid."',`approve`='".$approve."',`pending`='".$pending."',`deduct`='".$deduct."',`payer`='".$_user['id']."',`bank`='".escape_string($_leader['payment_bank'])."',`bank_name`='".escape_string($_leader['payment_name'])."',`bank_number`='".escape_string($_leader['payment_number'])."',`bank_branch`='".escape_string($_leader['payment_branch'])."' ")){
	$pid = $_db->insert_id();


	if($_mem){
		foreach ($_mem as $m) {

			if($m['revenue_approve'] > 0)
				$_db->exec_query("insert into `core_payments` set `refid`='".$pid."',`user_id` = '".$m['id']."',`type`='0',`status`='pending',`time`='".time()."',`paid`='".($m['revenue_approve']-$m['revenue_deduct'])."',`approve`='".$m['revenue_approve']."',`pending`='".$m['revenue_pending']."',`deduct`='".$m['revenue_deduct']."',`payer`=0,`bank`='".escape_string($m['payment_bank'])."',`bank_name`='".escape_string($m['payment_name'])."',`bank_number`='".escape_string($m['payment_number'])."',`bank_branch`='".escape_string($m['payment_branch'])."' ");
			

		}
	}


	exit('{"status":200,"message":"Tạo hóa đơn thành công."}');
} else
	exit('{"status":403,"message":"Có lỗi xảy ra vui lòng thử lại sau ít phút."}');


	

?>