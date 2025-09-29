<?php

$_id = isset($_REQUEST['id']) ? intval(trim($_REQUEST['id'])) : '';

if(!isAdmin())
	exit('{"status":403,"message":"Truy cập bị từ chối!"}');

if($_db->query("select `id` from `core_groups` where `id`='".escape_string($_id)."' ")->num_rows() < 1)
	exit('{"status":403,"message":"Không tìm thấy ID group!"}');


$member = memberGroup($_id);

$result = array("status"=>200,"members"=>array());

foreach ($member as $mem)
	$result['members'][] = array(
					"id" => $mem['id'],
					"name" => _e($mem['name']),
					"avatar" => getAvatar($mem['id'])
				);

if($result){
	echo json_encode($result);
	exit;
}
else
	exit('{"status":404,"message":"Không có thành viên nào"}');


	

?>