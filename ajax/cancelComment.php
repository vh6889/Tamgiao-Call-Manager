<?php 
    $_id = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : '';
    $exp_id = explode(",", trim($_id,","));    
if($_db->exec_query("delete from `core_comments` where id=".$exp_id[0])){
	exit('{"status":200,"message":"Xóa Comment thành công."}');
} else 
	exit('{"status":403,"message":"Có lỗi xảy ra vui lòng thử lại sau."}');
?>