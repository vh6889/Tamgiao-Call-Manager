<?php
    $_id = isset($_REQUEST['id']) ? intval(trim($_REQUEST['id'])) : '';
    $name = isset($_REQUEST['name']) ? trim($_REQUEST['name']) : '';
    $status = isset($_REQUEST['status']) ? trim($_REQUEST['status']) : '';
    $key = isset($_REQUEST['key']) ? trim($_REQUEST['key']) : '';
    $_comment_category = getCommentCategory($_id);    
    if(!isAdmin())
        exit('{"status":403,"message":"Truy cập bị từ chối!"}');
    if(!$_comment_category['id'])
        exit('{"status":403,"message":"Không tìm thấy ID Offer!"}');
    else {        
        if($status != "1" && $status != "0")
            exit('{"status":403,"message":"Trạng thái không chính xác!"}');
        if($_db->exec_query("update `core_comment_categories` set `name`='".$name."',`key_api`='".$key."',`status`='".$status."' where `id`='".escape_string($_id)."' ") == TRUE){    	
            exit('{"status":200,"message":"Lưu lại thành công!"}');
        }else{
            exit('{"status":403,"message":"Không có gì để sửa!"}');   
        }
    } 
    
?>