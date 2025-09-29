<?php
$category_id = isset($_REQUEST['category_id']) ? trim($_REQUEST['category_id']) : '';
$category_id1 = isset($_REQUEST['category_id1']) ? trim($_REQUEST['category_id1']) : '';
$category_name = isset($_REQUEST['category_name']) ? trim($_REQUEST['category_name']) : '';
if(!isAdmin())
	exit('{"status":403,"message":"Truy cập bị từ chối!"}');
else {
    if($_db->exec_query("insert into `core_comment_categories2` set `category_id`='".$category_id."',`category_id1`='".$category_id1."',`category_name2`='".$category_name."' "))
    	exit('{"status":200,"message":"Thêm Comment Category 2 thành công!"}');
    else
    	exit('{"status":403,"message":"Có lỗi xảy ra vui lòng thử lại sau."}');
} 
?>