<?php

$_id = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : '';
$_action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : '';

$exp_id = explode(",", trim($_id,","));

if(isBanned())
	exit('{"status":403,"message":"Không thể hủy chọn đơn hàng khi đang bị cấm."}');


switch ($_action) {
	
	case 'trashed':

		if(!isAller())
			exit('{"status":403,"message":"Bạn không có quyền thực hiện tác vụ này."}');

		$orders = $_db->query("select * from `core_orders` where `id` in ('".implode("','", $exp_id)."') and `status`='uncheck' ")->fetch_array();

		if(!$orders)
			exit('{"status":403,"message":"Không tìm thấy đơn hàng nào!"}');

		if($_db->exec_query("update `core_orders` set `status`='trashed' where `id` in ('".implode("','", $exp_id)."') and `status`='uncheck' ".$sql))
			exit('{"status":200,"message":"Đánh dấu thành công '.count($orders).' đơn hàng rác!"}');
		else
			exit('{"status":403,"message":"Có lỗi xảy ra vui lòng thử lại sau ít phút!"}');

	break;

	case 'shipping':

		if(!isShipper())
			exit('{"status":403,"message":"Bạn không có quyền thực hiện tác vụ này."}');

		$sql = !isAller() ? " and `user_ship`='".$_user['id']."' " : "";

		$orders = $_db->query("select * from `core_orders` where `id` in ('".implode("','", $exp_id)."') and `status`='pending' ".$sql)->fetch_array();

		if(!$orders)
			exit('{"status":403,"message":"Không tìm thấy đơn hàng nào!"}');

		if($_db->exec_query("update `core_orders` set `status`='shipping' where `id` in ('".implode("','", $exp_id)."') and `status`='pending' ".$sql))
			exit('{"status":200,"message":"Đánh dấu vận chuyển thành công '.count($orders).' đơn hàng!"}');
		else
			exit('{"status":403,"message":"Có lỗi xảy ra vui lòng thử lại sau ít phút!"}');

	break;


	case 'approved':

		if(!isShipper())
			exit('{"status":403,"message":"Bạn không có quyền thực hiện tác vụ này."}');

		$sql = !isAller() ? " and `user_ship`='".$_user['id']."' " : "";

		$orders = $_db->query("select * from `core_orders` where `id` in ('".implode("','", $exp_id)."') and `status`='shipping' ".$sql)->fetch_array();

		if(!$orders)
			exit('{"status":403,"message":"Không tìm thấy đơn hàng nào!"}');

		$values_orders = array();
		$values_users = array();
		$values_groups = array();

		$order_pending = array();
		$order_approve = array();
		$order_deduct = array();

		$group_pending = array();
		$group_approve = array();
		$group_deduct = array();
		foreach ($orders as $_info) {

			$user = getUser($_info['user_call']);
			$group = getGroup($_info['group']);

			if(!isset($order_pending[$user['id']]))
				$order_pending[$user['id']] = $user['revenue_pending'];
			if(!isset($order_approve[$user['id']]))
				$order_approve[$user['id']] = $user['revenue_approve'];
			if(!isset($order_deduct[$user['id']]))
				$order_deduct[$user['id']] = $user['revenue_deduct'];

			if(!isset($group_pending[$group['id']]))
				$group_pending[$group['id']] = $group['revenue_pending'];
			if(!isset($group_approve[$group['id']]))
				$group_approve[$group['id']] = $group['revenue_approve'];
			if(!isset($group_deduct[$group['id']]))
				$group_deduct[$group['id']] = $group['revenue_deduct'];

            /* Tính payout cho user call và group tương ứng */
            $_group_payout_amount = $_info['payout_leader'];
            $_user_payout_amount  = ($_group_payout_amount<$_info['payout_member']) ? $_group_payout_amount : $_info['payout_member'];
            if($_info['payout_type']=='percent') {
                /* percent type */
                $_order_amount  = $_info['price']* $_info['number'];
                $payout         = $_order_amount * $_user_payout_amount/100;
                $payout_group   = $_order_amount * $_group_payout_amount/100;
            }else{
                /* fixed type */
                $payout         = $_info['number'] * $_user_payout_amount;
                $payout_group   = $_info['number'] * $_group_payout_amount;
            }
            
			if($_info['r_hold'] > 0){
				$order_pending[$user['id']] = $order_pending[$user['id']] - $payout;
				$group_pending[$group['id']] = $group_pending[$group['id']] - $payout_group;
			}

			if($_info['r_approve'] <= 0){
				/* Update `price_bonus` for order `ukey` */
				$offer_bonus = $_db->query('select `price_bonus` from `core_offers` where `id`='.$_info['offer'])->fetch();
				$price_bonus = $offer_bonus['price_bonus']*$_info['number'];
				if($price_bonus) {
					$_db->exec_query("update `core_users` set `revenue_approve`=`revenue_approve`+$price_bonus where `ukey`='".$_info['ukey']."'");
				}
				
				$order_approve[$user['id']] = $order_approve[$user['id']] + $payout;
				$group_approve[$group['id']] = $group_approve[$group['id']] + $payout_group;
			}

			if($_info['r_deduct'] > 0){
				$order_deduct[$user['id']] = $order_deduct[$user['id']] - $_info['deduct_member'];
				$group_deduct[$group['id']] = $group_deduct[$group['id']] - $_info['deduct_member'];      
			}

			$values_users[$user['id']] = " ('".$user['id']."','".$order_pending[$user['id']]."','".$order_approve[$user['id']]."','".$order_deduct[$user['id']]."') ";
			$values_groups[$group['id']] = " ('".$group['id']."','".$group_pending[$group['id']]."','".$group_approve[$group['id']]."','".$group_deduct[$group['id']]."') ";
			$values_orders[$_info['id']] = " ('".$_info['id']."','0','0','1') ";
		}
		if($_db->exec_query("update `core_orders` set `status`='approved' where `id` in ('".implode("','", $exp_id)."') and `status`='shipping' ".$sql)){

			if($values_users){

				$_db->exec_query("INSERT INTO `core_users` (id,revenue_pending,revenue_approve,revenue_deduct) VALUES ".implode(",", $values_users)." ON DUPLICATE KEY UPDATE revenue_pending = VALUES(revenue_pending),revenue_approve = VALUES(revenue_approve),revenue_deduct = VALUES(revenue_deduct)");

                if($values_groups)
                    $_db->exec_query("INSERT INTO `core_groups` (id,revenue_pending,revenue_approve,revenue_deduct) VALUES ".implode(",", $values_groups)." ON DUPLICATE KEY UPDATE revenue_pending = VALUES(revenue_pending),revenue_approve = VALUES(revenue_approve),revenue_deduct = VALUES(revenue_deduct)");

                if($values_orders)
                    $_db->exec_query("INSERT INTO `core_orders` (id,r_hold,r_approve,r_deduct) VALUES ".implode(",", $values_orders)." ON DUPLICATE KEY UPDATE r_hold = VALUES(r_hold),r_approve = VALUES(r_approve),r_deduct = VALUES(r_deduct)");
			}

			exit('{"status":200,"message":"Đánh dấu giao hàng thành công '.count($orders).' đơn hàng!"}');
		} else
			exit('{"status":403,"message":"Có lỗi xảy ra vui lòng thử lại sau ít phút!"}');


	break;


	case 'shiperror':

		if(!isShipper())
			exit('{"status":403,"message":"Bạn không có quyền thực hiện tác vụ này."}');

		$sql = !isAller() ? " and `user_ship`='".$_user['id']."' " : "";

		$orders = $_db->query("select * from `core_orders` where `id` in ('".implode("','", $exp_id)."') and `status`='shipping' ".$sql)->fetch_array();

		if(!$orders)
			exit('{"status":403,"message":"Không tìm thấy đơn hàng nào!"}');

		$values_orders = array();
		$values_users = array();
		$values_groups = array();

		$order_pending = array();
		$order_approve = array();
		$order_deduct = array();

		$group_pending = array();
		$group_approve = array();
		$group_deduct = array();
		foreach ($orders as $_info) {

			$user = getUser($_info['user_call']);
			$group = getGroup($_info['group']);

			if(!isset($order_pending[$user['id']]))
				$order_pending[$user['id']] = $user['revenue_pending'];
			if(!isset($order_approve[$user['id']]))
				$order_approve[$user['id']] = $user['revenue_approve'];
			if(!isset($order_deduct[$user['id']]))
				$order_deduct[$user['id']] = $user['revenue_deduct'];

			if(!isset($group_pending[$group['id']]))
				$group_pending[$group['id']] = $group['revenue_pending'];
			if(!isset($group_approve[$group['id']]))
				$group_approve[$group['id']] = $group['revenue_approve'];
			if(!isset($group_deduct[$group['id']]))
				$group_deduct[$group['id']] = $group['revenue_deduct'];

			/* Tính payout cho user call và group tương ứng */
			$_group_payout_amount = $_info['payout_leader'];
			$_user_payout_amount  = ($_group_payout_amount<$_info['payout_member']) ? $_group_payout_amount : $_info['payout_member'];
			if($_info['payout_type']=='percent') {
				/* percent type */
				$_order_amount  = $_info['price']* $_info['number'];
				$payout         = $_order_amount * $_user_payout_amount/100;
				$payout_group   = $_order_amount * $_group_payout_amount/100;
			}else{
				/* fixed type */
				$payout         = $_info['number'] * $_user_payout_amount;
				$payout_group   = $_info['number'] * $_group_payout_amount;
			}

			if($_info['r_hold'] > 0){
				$order_pending[$user['id']] = $order_pending[$user['id']] - $payout;
				$group_pending[$group['id']] = $group_pending[$group['id']] - $payout_group;
			}

			if($_info['r_approve'] > 0){
				$order_approve[$user['id']] = $order_approve[$user['id']] - $payout;
				$group_approve[$group['id']] = $group_approve[$group['id']] - $payout_group;
			}

			if($_info['r_deduct'] <= 0){
				$order_deduct[$user['id']] = $order_deduct[$user['id']] + $_info['deduct_member'];
				$group_deduct[$group['id']] = $group_deduct[$group['id']] + $_info['deduct_member'];      
			}

			$values_users[$user['id']] = " ('".$user['id']."','".$order_pending[$user['id']]."','".$order_approve[$user['id']]."','".$order_deduct[$user['id']]."') ";
			$values_groups[$group['id']] = " ('".$group['id']."','".$group_pending[$group['id']]."','".$group_approve[$group['id']]."','".$group_deduct[$group['id']]."') ";
			$values_orders[$_info['id']] = " ('".$_info['id']."','0','0','1') ";
		}



		if($_db->exec_query("update `core_orders` set `status`='shiperror' where `id` in ('".implode("','", $exp_id)."') and `status`='shipping' ".$sql)){

			if($values_users){
				if($_db->exec_query("INSERT INTO `core_users` (id,revenue_pending,revenue_approve,revenue_deduct) VALUES ".implode(",", $values_users)." ON DUPLICATE KEY UPDATE revenue_pending = VALUES(revenue_pending),revenue_approve = VALUES(revenue_approve),revenue_deduct = VALUES(revenue_deduct)")){

					if($values_groups)
						$_db->exec_query("INSERT INTO `core_groups` (id,revenue_pending,revenue_approve,revenue_deduct) VALUES ".implode(",", $values_groups)." ON DUPLICATE KEY UPDATE revenue_pending = VALUES(revenue_pending),revenue_approve = VALUES(revenue_approve),revenue_deduct = VALUES(revenue_deduct)");

					if($values_orders)
						$_db->exec_query("INSERT INTO `core_orders` (id,r_hold,r_approve,r_deduct) VALUES ".implode(",", $values_orders)." ON DUPLICATE KEY UPDATE r_hold = VALUES(r_hold),r_approve = VALUES(r_approve),r_deduct = VALUES(r_deduct)");
				}
			}


			exit('{"status":200,"message":"Đánh dấu không nhận hàng thành công '.count($orders).' đơn hàng!"}');
		} else
			exit('{"status":403,"message":"Có lỗi xảy ra vui lòng thử lại sau ít phút!"}');


	break;

	default:
		exit('{"status":403,"message":"Hành động không tồn tại!"}');
	break;
}


	

?>