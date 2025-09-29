<?php

$_id = isset($_REQUEST['id']) ? intval(trim($_REQUEST['id'])) : '';
$ads = getAds($_id);
if(!isAdmin())
	exit('{"status":403,"message":"Truy cập bị từ chối!"}');
if(!$ads)
	exit('{"status":403,"message":"Không tìm thấy ID Ads!"}');
if($_db->exec_query("delete from `core_ads`  where `id`='".escape_string($_id)."' ")){
	exit('{"status":200,"message":"Xóa thành công!"}');
}
else
	exit('{"status":403,"message":"Có lỗi xảy ra vui lòng thử lại sau."}');
?>