<?php
$ads = isset($_REQUEST['ads']) ? trim($_REQUEST['ads']) : '';
if(!isAdmin()){
	exit('{"status":403,"message":"Truy cập bị từ chối!"}');
}
$ads_info = getAdsByName($ads);
if($ads_info){
	exit('{"status":403,"message":"Ads đã tồn tại!"}');
}
if(empty($ads)){
    exit('{"status":403,"message":"Vui lòng nhập đầy đủ các trường bắt buộc!"}');
}
else{
    if(empty($ads_info)){
        if($_db->exec_query("insert into `core_ads` set `ads`='".escape_string($ads)."' ")){
            exit('{"status":200,"message":"Thêm thành công!"}');
        }else{
            exit('{"status":403,"message":"Có lỗi xảy ra vui lòng thử lại sau."}');
        }
    }else{
        exit('{"status":403,"message":"Ads đã tồn tại!"}');   
    }
} 
?>