<?php

$name = isset($_REQUEST['name']) ? trim($_REQUEST['name']) : '';
$bank = isset($_REQUEST['bank']) ? trim($_REQUEST['bank']) : '';
$branch = isset($_REQUEST['branch']) ? trim($_REQUEST['branch']) : '';
$number = isset($_REQUEST['number']) ? trim($_REQUEST['number']) : '';

if(strtolower($name) == strtolower($_user['payment_name']) && strtolower($bank) == strtolower($_user['payment_bank']) && strtolower($branch) == strtolower($_user['payment_branch']) && $_user['payment_number'] == $number )
	exit('{"status":200,"message":"Không có thông tin cần lưu lại!"}');
else {

    if($_db->exec_query("update `core_users` set `payment_name`='".escape_string($name)."',`payment_bank`='".escape_string($bank)."',`payment_branch`='".escape_string($branch)."',`payment_number`='".$number."' where `id`='".$_user['id']."' "))
    	exit('{"status":200,"message":"Lưu thành công!"}');
    else
    	exit('{"status":403,"message":"Có lỗi xảy ra vui lòng thử lại sau."}');

} 
	

?>