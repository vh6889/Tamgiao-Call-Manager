<?php

$pass = isset($_REQUEST['pass']) ? trim($_REQUEST['pass']) : '';
$passNew = isset($_REQUEST['passNew']) ? trim($_REQUEST['passNew']) : '';
$passRe = isset($_REQUEST['passRe']) ? trim($_REQUEST['passRe']) : '';



if($_db->query("select * from `core_users` where `id` = '".escape_string($_user['id'])."' and `pass` = '".escape_string(md5(md5($pass)))."' limit 1 ")->num_rows() <= 0){
	exit('{"status":403,"message":"Mật khẩu cũ không chính xác."}');
} else if(empty($passNew)){
	exit('{"status":403,"message":"Mật khẩu mới không được bỏ trống."}');
} else if($passNew != $passRe){
	exit('{"status":403,"message":"Mật khẩu mới nhập lại không chính xác."}');
} else {

	if($_db->exec_query("update `core_users` set `pass`='".escape_string(md5(md5($passNew)))."' where `id`='".$_user['id']."'  ")){
		setcookie('userSESS',md5(md5($passNew)),time()+3600*24*365);
		exit('{"status":200,"message":"Đổi mật khẩu thành công."}');
	} else {
		exit('{"status":403,"message":"Có lỗi xảy ra vui lòng thử lại sau."}');
	}
}




if(strtolower($name) == strtolower($_user['full_name']) && strtolower($phone) == strtolower($_user['phone']) && strtolower($mail) == strtolower($_user['mail']) )
	exit('{"status":200,"message":"Không có thông tin cần lưu lại!"}');
else {

	if(empty($mail) || !preg_match("#^(.*?)@gmail\.com$#si",$mail))
		exit('{"status":405,"message":"Định dạng gmail không chính xác!"}');


	if($phone && !is_numeric($phone))
		exit('{"status":406,"message":"Định dạng phone không chính xác!"}');

    if($_db->exec_query("update `core_users` set `full_name`='".escape_string($name)."',`phone`='".escape_string($phone)."',`mail`='".escape_string($mail)."' where `id`='".$_user['id']."' "))
    	exit('{"status":200,"message":"Lưu thành công!"}');
    else
    	exit('{"status":403,"message":"Có lỗi xảy ra vui lòng thử lại sau."}');

} 
	

?>