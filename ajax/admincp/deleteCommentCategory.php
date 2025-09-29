<?php
$_id = isset($_REQUEST['id']) ? intval(trim($_REQUEST['id'])) : '';
$_offer = getCommentCategory($_id);
if(!isAdmin())
	exit('{"status":403,"message":"Truy cập bị từ chối!"}');
if(!$_offer['id'])
	exit('{"status":403,"message":"Không tìm thấy ID Comment Category!"}');
if($_db->exec_query("delete from `core_comment_categories`  where `id`='".escape_string($_id)."' ")){
	exit('{"status":200,"message":"Xóa thành công!"}');
}
else
	exit('{"status":403,"message":"Có lỗi xảy ra vui lòng thử lại sau."}');
?>