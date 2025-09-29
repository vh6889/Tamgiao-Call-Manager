<?php

$_id = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : '';
$_action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : '';
$_type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';



$exp_id = explode(",", trim($_id,","));

if(isBanned() && $_action == "export"){
	header('HTTP/1.0 403 Forbidden');
	exit();
}

if(isBanned())
	exit('{"status":403,"message":"Không thể gọi hàng khi đang bị cấm."}');



if(!isShipper() && $_action == "export" ){
	header('HTTP/1.0 403 Forbidden');
	exit();
}

if(!isShipper())
	exit('{"status":403,"message":"Tài khoản của bạn không phải là shipper !"}');

$excel = new Excel();

switch ($_action) {
	case 'export':


		$sql = !isAller() ? " and `user_ship`='".$_user['id']."' " : "";

		$orders = $_db->query("select * from `core_orders` where `id` in ('".implode("','", $exp_id)."') and `status`='pending' ".$sql." order by id DESC")->fetch_array();

		if(!$orders){
			header('HTTP/1.0 403 Forbidden');
			exit();
		}
		$data = array();

		switch ($_type) {

			case 'viettel':

				$_name = isset($_REQUEST['name']) ? trim($_REQUEST['name']) : '';
				$_mass = isset($_REQUEST['mass']) ? trim($_REQUEST['mass']) : '';
				$_service = isset($_REQUEST['service']) ? trim($_REQUEST['service']) : '';
				$_payer = isset($_REQUEST['payer']) ? trim($_REQUEST['payer']) : '';
				$_note = isset($_REQUEST['note']) ? trim($_REQUEST['note']) : '';
				$_time = isset($_REQUEST['time']) ? trim($_REQUEST['time']) : '';

				$i = 1;
				$sql_check = [];
				foreach ($orders as $arr){
				  $sql_check[] = " ( `order_phone` like '".$arr['order_phone']."' and `id`!='".$arr['id']."') ";
				}
				$sql_check = $sql_check ? ' where '.implode(" or ", $sql_check) : '';
				$checkOrder = checkOrders($sql_check);  
				foreach ($orders as $arr) {
					$check = isset($checkOrder[$arr['order_phone']]) ?  $checkOrder[$arr['order_phone']] : '';
					if($check){
						foreach ($check as $dup) {						  
						  $text_dup= $dup['id'].'-'.$dup['status'];
						}
					  }else{
						  $text_dup='';
					  }
					$caller = getUser($arr['user_call']);
					$data[] = array($i,$arr['id'],_ucwords($arr['order_name']),$arr['order_phone'],_ucwords($arr['order_address']),_ucwords($arr['order_province']),_ucwords($arr['order_district']),$_name ? $_name : $arr['offer_name'],$arr['number'],$_mass,($arr['price_sell']*1000),($arr['price_sell']*1000),$_service,"",$_payer,$_note,$_time,$caller['name'],_ucwords($arr['note']),$text_dup);
					$i++;
				}

				$excel->export_viettel($data);
			break;


			case 'vnpost':

				$_name = isset($_REQUEST['name']) ? trim($_REQUEST['name']) : '';
				$_mass = isset($_REQUEST['mass']) ? trim($_REQUEST['mass']) : '';
				$_service = isset($_REQUEST['service']) ? trim($_REQUEST['service']) : '';
				$_view = isset($_REQUEST['view']) ? trim($_REQUEST['view']) : '';
				$_collect = isset($_REQUEST['collect']) ? trim($_REQUEST['collect']) : '';
				$_note = isset($_REQUEST['note']) ? trim($_REQUEST['note']) : '';
				$_bill = isset($_REQUEST['bill']) ? trim($_REQUEST['bill']) : '';
				$_ar = isset($_REQUEST['ar']) ? trim($_REQUEST['ar']) : '';
				$_plus = isset($_REQUEST['plus']) ? trim($_REQUEST['plus']) : '';
		
				$i = 1;
				$sql_check = [];
				foreach ($orders as $arr){
				  $sql_check[] = " ( `order_phone` like '".$arr['order_phone']."' and `id`!='".$arr['id']."') ";
				}
				$sql_check = $sql_check ? ' where '.implode(" or ", $sql_check) : '';
				$checkOrder = checkOrders($sql_check);  
				foreach ($orders as $arr) {
					$check = isset($checkOrder[$arr['order_phone']]) ?  $checkOrder[$arr['order_phone']] : '';
					if($check){
						foreach ($check as $dup) {						  
						  $text_dup= $dup['id'].'-'.$dup['status'];
						}
					  }else{
						  $text_dup='';
					  }
					$caller = getUser($arr['user_call']);
					$data[] = array($i,ucwords($arr['order_name']),$arr['order_phone'],_ucwords($arr['order_address']),_ucwords($arr['order_commune'])." - "._ucwords($arr['order_district'])." - "._ucwords($arr['order_province']),$_service,$_view,$_collect,$_mass,$_note,$_name ? $_name : $arr['offer_name'],($arr['price_sell']*1000),"",$_bill,$_ar,$_plus,$arr['id'],$caller['name'],_ucwords($arr['note']),$text_dup);
					$i++;
				}

				$excel->export_vnpost($data);
			break;


			default:
				header('HTTP/1.0 403 Forbidden');
				exit();

			break;
		}


	break;

	case "import":

		switch ($_type) {

			case 'viettel':

				$_file = isset($_FILES['file']) ? $_FILES['file'] : '';
				$_status = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';

				if($_file){
					$data = $excel->import_viettel($_file['tmp_name'],$_status);

					file_put_contents('tools/data_import/'.$_status.'_viettel.json', json_encode($data));
					if(!$data)
						exit('{"status":403,"message":"Không tìm thấy đơn hàng hợp lệ nào từ file Excel."}');
					else {

						$sql = !isAller() ? " and `user_ship`='".$_user['id']."' " : "";

						$results = array();
						foreach ($data as $arr) {
							if($_db->query("select `id` from `core_orders` where `id`='".$arr['id']."' and `order_phone`='".escape_string($arr['order_phone'])."' and `status`='shipping' ".$sql)->num_rows() > 0)
								$results[] = $arr;
						}
						if(!$results)
							exit('{"status":403,"message":"Không tìm thấy đơn hàng hợp lệ nào từ file Excel."}');
						else
							exit('{"status":200,"message":"Nhập khẩu Excel thành công.","make": "'.$_status.'","data":'.json_encode($results).'}');
					}
				} else 
					exit('{"status":403,"message":"Không thể upload file!"}');
				

			break;

			case 'vnpost':

				$_file = isset($_FILES['file']) ? $_FILES['file'] : '';
				$_status = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';

				if($_file){
					$data = $excel->import_vnpost($_file['tmp_name'],$_status);
					file_put_contents('tools/data_import/'.$_status.'_vnpost.json', json_encode($data));
					if(!$data)
						exit('{"status":403,"message":"Không tìm thấy đơn hàng hợp lệ nào từ file Excel."}');
					else {

						$sql = !isAller() ? " and `user_ship`='".$_user['id']."' " : "";

						$results = array();
						foreach ($data as $arr) {
							if($_db->query("select `id` from `core_orders` where `id`='".$arr['id']."' and  `order_phone`='".escape_string($arr['order_phone'])."' and `status`='shipping' ".$sql)->num_rows() > 0)
								$results[] = $arr;
						}
						if(!$results)
							exit('{"status":403,"message":"Không tìm thấy đơn hàng hợp lệ nào từ file Excel."}');
						else
							exit('{"status":200,"message":"Nhập khẩu Excel thành công.","make": "'.$_status.'","data":'.json_encode($results).'}');
					}
				} else 
					exit('{"status":403,"message":"Không thể upload file!"}');
				

			break;
			

			default:
				exit('{"status":403,"message":"Hành động không tồn tại!"}');

			break;
		}

	break;
	
	default:
		exit('{"status":403,"message":"Hành động không tồn tại!"}');
	break;
}




?>