<?php
$_id = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : '';
$_action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : '';
$_type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';
$exp_id = explode(",", trim($_id,","));
$excel = new Excel();
switch ($_action) {	
	case 'export':				
		$data = [];
		switch ($_type) {
			case 'postback':												
				$response_code = isset($_REQUEST['response_code']) ? trim($_REQUEST['response_code']) : '';
				$sql = "";
				$ts = date("Y-m-d",strtotime(str_replace('/','-',$_REQUEST['ts'])));
				$te = date("Y-m-d",strtotime(str_replace('/','-',$_REQUEST['te'])));
				$sql.=" and DATE_FORMAT(created,'%Y-%m-%d') >='".$ts."' and DATE_FORMAT(created,'%Y-%m-%d') <='".$te."' ";							
				$offer_id = $_REQUEST['offer_id'];
				if($offer_id > 0){
					$sql.=" AND offer_id='".$offer_id."' ";
				}
				$type_ads_id = $_REQUEST['type_ads_id'];
				if($type_ads_id > 0){    
					$sql.=" OR type_ads_id='".$type_ads_id."' ";
				}
				$result = $_db->query("SELECT * FROM core_s2s_postback WHERE response_code='".$response_code."' ".$sql." order by id DESC")->fetch_array();									
				$i = 1;
				foreach ($result as $item_postback) {
					$offer_id_s2s = $item_postback["offer_id"];
					$type_ads_id_s2s = $item_postback["type_ads_id"];
					$query_offer = $_db->query("SELECT * FROM core_offers WHERE id='".$offer_id_s2s."' order by id DESC limit 1")->fetch();
					$query_ads = $_db->query("SELECT * FROM core_ads WHERE id='".$type_ads_id_s2s."' order by id DESC limit 1")->fetch();
					if(!empty($query_offer)){
						$offer_id_s2s_go = $query_offer['name'];
					}else{
						$offer_id_s2s_go='';
					}
					if(!empty($query_ads)){
						$type_ads_id_s2s_go = $query_ads['ads'];
					}else{
						$type_ads_id_s2s_go='';
					}
					$data[] = array($i,$offer_id_s2s_go,$item_postback['landing_page'],$item_postback['created'],$type_ads_id_s2s_go,$item_postback['state'],$item_postback['request_url'],$item_postback['response_code']);
					$i++;
				}
				$excel->export_postback($data);
			break;			
			default:
				header('HTTP/1.0 403 Forbidden');
				exit();

			break;
		}

	break;

	case "import":

		switch ($_type) {

			case 'order':
				if(!empty($_FILES["file"])){					
					$file_array = explode(".", $_FILES["file"]["name"]);					
					if($file_array[1] == "xlsx" || $file_array[1]=="xls")  {						
						include("includes/class/lib/PHPExcel/IOFactory.php");  
						$output = '';  
						$output.= "
						<label class='text-success'>Cập nhật trạng thái thành công</label>  
                			<table class='table table-bordered'>  
								<tr style='background:#3883cc;color:#fff;'>  
									<th>STT</th>  
									<th>Mã đơn hàng</th>  
									<th>Số hiệu</th>
									<th>Thông tin đơn hàng</th>
									<th>Bưu điện</th>
									<th>Trạng thái</th>  									  
								</tr>  
					 ";  						 			 
					 $object = PHPExcel_IOFactory::load($_FILES["file"]["tmp_name"]);  					 					 					 
					 foreach($object->getWorksheetIterator() as $worksheet){
						$highestRow = $worksheet->getHighestRow(); 						
						for($row=2; $row<=$highestRow; $row++){
							$order_id = $worksheet->getCellByColumnAndRow(1, $row)->getValue();	
							$parcel_code = $worksheet->getCellByColumnAndRow(2, $row)->getValue();						
							$order_info = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
							$post_office = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
							$status = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
							$check_trung = $_db->query("select * from `core_orders` where `id`='".$order_id."' ")->fetch();																				
							if(!$check_trung){
								$output.= '  
									<tr>  
										<td class="text-danger">'.($row-1).'</td>  
										<td class="text-danger">'.$order_id.'</td>  
										<td class="text-success">'.$parcel_code.'</td>
										<td class="text-danger">'.$order_info.'</td>  
										<td class="text-danger">'.$post_office.'</td>  
										<td class="text-danger">'.$status.'</td>  									
									</tr>  
								';  
							}else{	
								$order_info_up = $check_trung['order_info'].'<br>'.$order_info;														
								$_db->exec_query("update `core_orders` set `parcel_code`='".$parcel_code."',`status`='".$status."',`post_office`='".$post_office."',`order_info`='".$order_info_up."',`last_edit`='".$_user['id']."',`update_time`='".time()."' where `id`='".$order_id."' ");																																											
								if($check_trung['status'] != $status){
									$number = $check_trung['number'];
									if($check_trung['payout_type']=='percent') {
										$_postPayout  = $check_trung['payout_member']/100 * $check_trung['price']*$number;
										$_pastPayout  = $check_trung['payout_member']/100 * $check_trung['price']*$check_trung['number'];
										$_postPayout2 = $check_trung['payout_leader']/100 * $check_trung['price']*$number;
										$_pastPayout2 = $check_trung['payout_leader']/100 * $check_trung['price']*$check_trung['number'];
									}else{
										$_postPayout  = $check_trung['payout_member'] * $number;
										$_pastPayout  = $check_trung['payout_member'] * $check_trung['number'];
										$_postPayout2 = $check_trung['payout_leader'] * $number;
										$_pastPayout2 = $check_trung['payout_leader'] * $check_trung['number'];
									}
									
									if(in_array($status, ['pending','shipping','shipdelay'])){						
									  if($status == "pending" && $check_trung['status'] != $status){
										curl_sendNotifi($_url.'/sendNotifi.php?act=pending&id='.$check_trung['id']);
									  }						
									  $sql = array();
									  $sql2 = array();															  
									  if($check_trung['r_hold'] <= 0){
										$sql[] =" `revenue_pending`=`revenue_pending`+'".$_postPayout."' ";
										$sql2[] =" `revenue_pending`=`revenue_pending`+'".$_postPayout2."' ";
									  }						
									  if($check_trung['r_hold'] > 0){
										$sql[] =" `revenue_pending`=`revenue_pending`-'".$_pastPayout."'+'".$_postPayout."' ";
										$sql2[] =" `revenue_pending`=`revenue_pending`-'".$_pastPayout2."'+'".$_postPayout2."' ";
									  }						
									  if($check_trung['r_approve'] > 0){
										$sql[] =" `revenue_approve`=`revenue_approve`-'".$_pastPayout."' ";
										$sql2[] =" `revenue_approve`=`revenue_approve`-'".$_pastPayout2."' ";
									  }						
									  if($check_trung['r_deduct'] > 0){
										$sql[] =" `revenue_deduct`=`revenue_deduct`-'".$check_trung['deduct_member']."' ";
										$sql2[] =" `revenue_deduct`=`revenue_deduct`-'".$check_trung['deduct_leader']."' ";
									  }																				
									  if($sql)
										if($_db->query("update `core_users` set ".implode(" , ", $sql)." where `id`='".$check_trung['user_call']."' ")){
										  if($sql2){
											$_db->exec_query("update `core_groups` set ".implode(" , ", $sql2)." where `id` = '".$check_trung['group']."'");
										  }
										  $_db->query("update `core_orders` set `r_hold`='1',`r_approve`='0',`r_deduct`='0' where `id`='".$check_trung['id']."' ");
										}
									} elseif(in_array($status, ['callback','callerror','rejected','trashed'])){
									  if($status == "pending"){
										curl_sendNotifi($_url.'/sendNotifi.php?act=pending&id='.$check_trung['id']);
									  }
									  $sql = array();
									  $sql2 = array();						
									  if($check_trung['r_hold'] > 0){
										$sql[] =" `revenue_pending`=`revenue_pending`-'".$_pastPayout."' ";
										$sql2[] =" `revenue_pending`=`revenue_pending`-'".$_pastPayout2."' ";
									  }
						
									  if($check_trung['r_approve'] > 0){
										$sql[] =" `revenue_approve`=`revenue_approve`-'".$_pastPayout."' ";
										$sql2[] =" `revenue_approve`=`revenue_approve`-'".$_pastPayout2."' ";
									  }
						
									  if($check_trung['r_deduct'] > 0){
										$sql[] =" `revenue_deduct`=`revenue_deduct`-'".$check_trung['deduct_member']."' ";
										$sql2[] =" `revenue_deduct`=`revenue_deduct`-'".$check_trung['deduct_leader']."' ";    
									  }

									  if($sql) {
										if($_db->query("update `core_users` set ".implode(" , ", $sql)." where `id`='".$check_trung['user_call']."' ")){
										  if($sql2){
										   $_db->exec_query("update `core_groups` set ".implode(" , ", $sql2)." where `id` = '".$check_trung['group']."'");
										  }
										  $_db->query("update `core_orders` set `r_hold`='0',`r_approve`='0',`r_deduct`='0' where `id`='".$check_trung['id']."' ");
										}
										}
									} elseif($status == "shiperror"){
						
									  $sql = array();
									  $sql2 = array();
						
									  if($check_trung['r_hold'] > 0){
										$sql[] =" `revenue_pending`=`revenue_pending`-'".$_pastPayout."' ";
										$sql2[] =" `revenue_pending`=`revenue_pending`-'".$_pastPayout2."' ";
									  }
						
									  if($check_trung['r_approve'] > 0){
										$sql[] =" `revenue_approve`=`revenue_approve`-'".$_pastPayout."' ";
										$sql2[] =" `revenue_approve`=`revenue_approve`-'".$_pastPayout2."' ";
									  }
						
									  if($check_trung['r_deduct'] <= 0){
										$sql[] =" `revenue_deduct`=`revenue_deduct`+'".$check_trung['deduct_member']."' ";
										$sql2[] =" `revenue_deduct`=`revenue_deduct`+'".$check_trung['deduct_leader']."' ";      
									  }
						
									  if($sql)
										if($_db->query("update `core_users` set ".implode(" , ", $sql)." where `id`='".$check_trung['user_call']."' ")){
										  if($sql2){
											$_db->exec_query("update `core_groups` set ".implode(" , ", $sql2)." where `id` = '".$check_trung['group']."'");
										  }
										  $_db->query("update `core_orders` set `r_hold`='0',`r_approve`='0',`r_deduct`='1' where `id`='".$check_trung['id']."' ");
										}
						
										// bạn vừa bị khấu trừ xxxx
									} elseif ($status == "approved"){						
									  $sql = array();
									  $sql2 = array();						
									  if($check_trung['r_hold'] > 0){
										$sql[] =" `revenue_pending`=`revenue_pending`-'".$_pastPayout."' ";
										$sql2[] =" `revenue_pending`=`revenue_pending`-'".$_pastPayout2."' ";
									  }
						
									  if($check_trung['r_approve'] <= 0){
										$sql[] =" `revenue_approve`=`revenue_approve`+'".$_postPayout."' ";
										$sql2[] =" `revenue_approve`=`revenue_approve`+'".$_postPayout2."' ";
									  }
						
									  if($check_trung['r_approve'] > 0){
										$sql[] =" `revenue_approve`=`revenue_approve`-'".$_pastPayout."'+'".$_postPayout."' ";
										$sql2[] =" `revenue_approve`=`revenue_approve`-'".$_pastPayout2."'+'".$_postPayout2."' ";
									  }
						
									  if($check_trung['r_deduct'] > 0){
										$sql[] =" `revenue_deduct`=`revenue_deduct`-'".$check_trung['deduct_member']."' ";
										$sql2[] =" `revenue_deduct`=`revenue_deduct`-'".$check_trung['deduct_leader']."' ";         
									  }
						
									  if($sql)
										if($_db->query("update `core_users` set ".implode(" , ", $sql)." where `id`='".$check_trung['user_call']."' ")){
										  if($sql2){
											$_db->exec_query("update `core_groups` set ".implode(" , ", $sql2)." where `id` = '".$check_trung['group']."'");
										  }
										  $_db->query("update `core_orders` set `r_hold`='0',`r_approve`='1',`r_deduct`='0' where `id`='".$check_trung['id']."' ");
										}
										// bạn vừa nhận được xxx tiền có thể thanh toán
						
									}
						
								}																				
								$output.= '  
									<tr>  
										<td class="text-success">'.($row-1).'</td>  
										<td class="text-success">'.$order_id.'</td>  
										<td class="text-success">'.$parcel_code.'</td>
										<td class="text-success">'.$order_info.'</td>
										<td class="text-success">'.$post_office.'</td>
										<td class="text-success">'.$status.'</td>  									
									</tr>  
								';  
							}							
						}						
					 }
					 $output .= '</table>';  
					 echo $output;  					 
					}else{
						echo '<label class="text-danger">Invalid File</label>'; 
					}
				}

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