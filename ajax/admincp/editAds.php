<?php
$_id = isset($_REQUEST['id']) ? intval(trim($_REQUEST['id'])) : '';
$ads = isset($_REQUEST['ads']) ? trim($_REQUEST['ads']) : '';
if(!isAdmin()){
    exit('{"status":403,"message":"Truy cập bị từ chối!"}');
}
$ads_info = getAds($_id);
if(!$ads_info){
	exit('{"status":403,"message":"Không tìm thấy ID Ads!"}');
}
if(empty($ads)){
    exit('{"status":403,"message":"Vui lòng nhập đầy đủ các trường bắt buộc!"}');
}
else {
    if($_db->exec_query("update `core_ads` set `ads`='".escape_string($ads)."' where `id`='".escape_string($ads_info['id'])."' ")){
    	exit('{"status":200,"message":"Lưu lại thành công!"}');
    }else{
        exit('{"status":403,"message":"Có lỗi xảy ra vui lòng thử lại sau."}');
    }
} 
?>