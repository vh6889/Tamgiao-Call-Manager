<?php


if(!isAdmin())
	exit('{"status":403,"message":"Truy cập bị từ chối!"}');

$member = memberNoGroup();

$result = array("status"=>200,"members"=>array());

foreach ($member as $mem)
	$result['members'][] = array(
					"id" => $mem['id'],
					"name" => isBanned($mem) ? '<strike>'._e($mem['name']).'</strike>': _e($mem['name']),
					"avatar" => getAvatar($mem['id'])
				);

if($result){
	echo json_encode($result);
	exit;
}
else
	exit('{"status":404,"message":"Không có thành viên nào chưa có nhóm."}');


	

?>