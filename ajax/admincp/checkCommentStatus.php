<?php 
$statusId = isset($_REQUEST['statusId']) ? trim($_REQUEST['statusId']) : '';
$status = isset($_REQUEST['status']) ? trim($_REQUEST['status']) : '';
$table_name = isset($_REQUEST['table_name']) ? trim($_REQUEST['table_name']) : '';
if($_db->exec_query("UPDATE {$table_name} SET status='".$status."' where id='".$statusId."' ") == TRUE){    	
    exit('{"status":200,"message":"Cập nhật thành công!"}');
}else{
    exit('{"status":403,"message":"Có lỗi xảy ra vui lòng thử lại sau."}');
}
?>