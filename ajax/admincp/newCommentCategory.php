<?php
$name = isset($_REQUEST['name']) ? trim($_REQUEST['name']) : '';
$key = isset($_REQUEST['key']) ? trim($_REQUEST['key']) : '';
if(!isAdmin())
	exit('{"status":403,"message":"Truy cập bị từ chối!"}');
if(empty($name) || empty($key))
	exit('{"status":403,"message":"Vui lòng nhập đầy đủ các trường bắt buộc!"}');
else {
	$_count = $_db->query("select `id` from `core_comment_categories` where `key_api`='".$key."' order by `id` DESC")->num_rows();	
	if($_count>0){
		exit('{"status":403,"message":"Key đã tồn tại. Vui lòng nhập Key khác"}');
	}else{
		if($_db->exec_query("insert into `core_comment_categories` set `name`='".escape_string($name)."',`key_api`='".escape_string($key)."' "))
			exit('{"status":200,"message":"Thêm Comment Category thành công!"}');
		else
			exit('{"status":403,"message":"Có lỗi xảy ra vui lòng thử lại sau."}');
	}
} 
?>