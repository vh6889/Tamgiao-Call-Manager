<?php

$name = isset($_REQUEST['name']) ? trim($_REQUEST['name']) : '';
$mail = isset($_REQUEST['mail']) ? trim($_REQUEST['mail']) : '';
$phone = isset($_REQUEST['phone']) ? trim($_REQUEST['phone']) : '';
$notifi = isset($_REQUEST['notifi']) && $_REQUEST['notifi'] == "true" ? 1 : 0;

if(strtolower($name) == strtolower($_user['full_name']) && strtolower($phone) == strtolower($_user['phone']) && strtolower($mail) == strtolower($_user['mail']) && $_user['notifi'] == $notifi )
	exit('{"status":200,"message":"Không có thông tin cần lưu lại!"}');
else {

	if(empty($mail) || !preg_match("#^(.*?)@gmail\.com$#si",$mail))
		exit('{"status":405,"message":"Định dạng gmail không chính xác!"}');


	if($phone && !is_numeric($phone))
		exit('{"status":406,"message":"Định dạng phone không chính xác!"}');

    if($_db->exec_query("update `core_users` set `full_name`='".escape_string($name)."',`phone`='".escape_string($phone)."',`mail`='".escape_string($mail)."',`notifi`='".$notifi."' where `id`='".$_user['id']."' "))
    	exit('{"status":200,"message":"Lưu thành công!"}');
    else
    	exit('{"status":403,"message":"Có lỗi xảy ra vui lòng thử lại sau."}');

} 
	

?>