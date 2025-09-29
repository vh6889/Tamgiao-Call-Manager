<?php 
$check_id = isset($_REQUEST['check_id']) ? trim($_REQUEST['check_id']) : '';
$check_status = isset($_REQUEST['check_status']) ? trim($_REQUEST['check_status']) : '';
$check_table = isset($_REQUEST['check_table']) ? trim($_REQUEST['check_table']) : '';
if($check_status == "1"){
    $status = 0;
}elseif($check_status == "0"){
   $status = 1;
}
if($_db->exec_query("update `{$check_table}` set `status`='".$status."' where `id`='".$check_id."' ") == TRUE){    	
    exit('{"status":200,"message":"Cập nhật thành công!"}');
}
?>