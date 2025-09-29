<?php

$name = isset($_REQUEST['name']) ? trim($_REQUEST['name']) : '';
$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';
$offers = isset($_REQUEST['offers']) ? trim($_REQUEST['offers']) : '';
$payout = isset($_REQUEST['payout']) ? abs(intval(trim($_REQUEST['payout']))) : 0;
$payout_type = isset($_REQUEST['payout_type']) ? trim($_REQUEST['payout_type']) : 'fixed';
$deduct = isset($_REQUEST['deduct']) ? abs(intval(trim($_REQUEST['deduct']))) : 0;

if($offers)
	$exp_offers = explode(",", trim($offers,","));

if(!isAdmin())
	exit('{"status":403,"message":"Truy cập bị từ chối!"}');

if(empty($name) || empty($type) )
	exit('{"status":403,"message":"Vui lòng nhập đầy đủ các trường bắt buộc!"}');
else {

	if(!typeGroup($type))
		exit('{"status":403,"message":"Loại nhóm không tồn tại!"}');


	$off = "";

	if(isset($exp_offers)){
		foreach ($exp_offers as $of){
			if($_db->query("select `id` from `core_offers` where `id`='".escape_string($of)."' ")->num_rows() > 0)
				$off .= '|'.trim($of).',';
		}		
	}



	if($off == "" && $exp_offers)
		exit('{"status":403,"message":"Không tìm thấy ID offer nào!"}');

    if($_db->exec_query("insert into `core_groups` set `name`='".escape_string($name)."',`type`='".escape_string($type)."',`payout`='".escape_string($payout)."',`payout_type`='".escape_string($payout_type)."',`deduct`='".escape_string($deduct)."',`offers`='".escape_string($off)."' "))
    	exit('{"status":200,"message":"Thêm thành công group!"}');
    else
    	exit('{"status":403,"message":"Có lỗi xảy ra vui lòng thử lại sau."}');

} 
	

?>