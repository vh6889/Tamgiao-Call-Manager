<?php 
$received_data = json_decode(file_get_contents("php://input"));
$limit = $received_data->limit;
$data = array();
if($received_data->action == 'fetchall'){
    $_status = 'shipfail';    
    $offer = isset($_GET['offer']) ? trim($_GET['offer']) : (isset($_COOKIE[$_status.'_offer']) ? $_COOKIE[$_status.'_offer'] : 'all');
    $ts = isset($_GET['ts']) && !empty($_GET['ts']) ? $_GET['ts'] :  date('d/m/Y',strtotime('-29 days GMT+7 00:00'));
    $te = isset($_GET['te']) && !empty($_GET['te']) ? $_GET['te'] :  date('d/m/Y',time());
    $view = isset($_GET['view']) && in_array($_GET['view'],['me','all','group']) ? $_GET['view'] : (isset($_COOKIE[$_status.'_view']) ? $_COOKIE[$_status.'_view'] : (isAller() ? 'all' : 'group'));
    $user = isset($_GET['user']) ? $_GET['user'] : '';
    if(((!isLeader() && $view == "all") && !isAller()) || ((!isLeader() && $view == "group") && !isAller())){
        $view = "me";
    }else if((isLeader() && $view == "all") && !isAller()){
        $view = "group";
    }
    if(isset($_GET['offer']) && $_GET['offer']){
        setcookie($_status.'_offer',$offer,time()+3600*24*365);
    }
    if(isset($_GET['view']) && $_GET['view']){
        setcookie($_status.'_view',$view,time()+3600*24*365);
    }
    if(isset($_GET['offer']) && !$_GET['offer']){
        setcookie($_status.'_offer',"");
    }
    if(isset($_GET['view']) && !$_GET['view'] || $_GET['view'] == "me"){
        setcookie($_status.'_view',"");
    }
    $_group = getGroup($_user['group']);

    // $_offers = getOffer();
    $_temp = getOffer();
    $_offers = array();
    foreach ($_temp as $key => $value) {
        $_offers[$value['id']] = $value;
    }

    $sql = "";
    if($offer != "all"){
        $sql = " and `offer`='".escape_string($offer)."' ";
    }    
    if(isCaller() || isColler()){
        if($view == "group"){
            $sql .= " and `group`='".$_user['group']."' ";
        }
        if($view == "me"){
            $sql .= " and `user_call`='".$_user['id']."' ";
        }    
        elseif($user){
            $sql .= " and `user_call`='".escape_string($user)."' ";
        }
    } elseif (isPublisher()) {
        if ($view == "group")
            $sql .= " and `group`='" . $_user['group'] . "' ";
        if ($view == "me")
            $sql .= " and `ukey`='" . $_user['ukey'] . "' ";
        else
            $sql .= " and `ukey`='" . $_user['ukey'] . "' ";
    } else {
        $memGroup = memberGroup($_group['id']);
        $user_ship = array();
        foreach ($memGroup as $us)
        $user_ship[] = $us['id'];
        if($view == "group"){
            $sql .= " and `user_ship` in ('".implode("','", $user_ship)."') ";
        }
        if($view == "me"){
            $sql .= " and `user_ship`='".$_user['id']."' ";
        }
        elseif($user){
            $sql .= " and `user_ship`='".escape_string($user)."' ";
        }
    }    
    $limit = $received_data->limit;
    if(isAller())
    {
        if(isset($_GET['countLength'])){
            $result = $_db->query("select * from `core_orders` where `status` = '".$_status."' and (`time` >= '".(strtotime(str_replace("/","-", $ts)." GMT+7 00:00"))."' and `time` < '".(strtotime(str_replace("/","-", $te)." GMT+7 23:59"))."') and `user_ship`!='' ".$_sql_offer." ".$sql." order by id DESC")->fetch_array();
        }else{
            $result = $_db->query("select * from `core_orders` where `status` = '".$_status."' and (`time` >= '".(strtotime(str_replace("/","-", $ts)." GMT+7 00:00"))."' and `time` < '".(strtotime(str_replace("/","-", $te)." GMT+7 23:59"))."') and `user_ship`!='' ".$_sql_offer." ".$sql." order by id DESC LIMIT $limit")->fetch_array();
        }
    } else{
        if(isset($_GET['countLength'])){
            $result = $_db->query("select * from `core_orders` where `status` = '".$_status."' and (`time` >= '".(strtotime(str_replace("/","-", $ts)." GMT+7 00:00"))."' and `time` < '".(strtotime(str_replace("/","-", $te)." GMT+7 23:59"))."') ".$_sql_offer." ".$sql." order by id DESC")->fetch_array(); 
        }else{
            $result = $_db->query("select * from `core_orders` where `status` = '".$_status."' and (`time` >= '".(strtotime(str_replace("/","-", $ts)." GMT+7 00:00"))."' and `time` < '".(strtotime(str_replace("/","-", $te)." GMT+7 23:59"))."') ".$_sql_offer." ".$sql." order by id DESC LIMIT $limit")->fetch_array();        
        }
    }          
    $isCaller = isCaller() || isColler() ? true: false;
    $isShipper = isShipper() ? true: false;    
    $sql_check = [];
    foreach ($result as $arr){
        $sql_check[] = " ( `order_phone` like '".$arr['order_phone']."' and `id`!='".$arr['id']."') ";
    }
    $sql_check = $sql_check ? ' where '.implode(" or ", $sql_check) : '';
    $checkOrder = checkOrders($sql_check);    
    foreach($result as $row){
        $check = isset($checkOrder[$row['order_phone']]) ?  $checkOrder[$row['order_phone']] : '';
        $caller = getUser($row['user_call']);
        $shipper = getUser($row['user_ship']);   
        if(!empty($row['parcel_code'])){
            $parcel_code = $row['parcel_code'].'<a target="_blank" href="http://www.vnpost.vn/en-us/dinh-vi/buu-pham?key='.$row['parcel_code'].'"><i class="fas fa-external-link-alt ml-1"></i></a>';
        }else{
            $parcel_code = '';
        }
        if($check){
            foreach ($check as $dup) {
              $data_dup = $_db->query("select * from `core_orders` where `id` = '".$dup['id']."' ")->fetch();
              $caller_dup = getUser($data_dup['user_call']);
              $order_phone = '<br><small>- '.$caller_dup['name'].' #<a target="_blank" href="?route=editOrder&id='.$dup['id'].'"><b class="text-danger">'.$dup['id'].' ('.$dup['status'].')</b></a></small>';
            }
        }else{
           $order_phone = ''; 
        }
        $note_read = '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal'.$row['id'].'">
                        Chi tiết
                    </button>
                    <div class="modal fade" id="exampleModal'.$row['id'].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Địa chỉ mua hàng</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p style="margin-bottom:0;"><strong>Địa chỉ:</strong> '._ucwords($row['order_address']).'</p>
                            <p style="margin-bottom:0;"><strong>Commune:</strong> '._ucwords($row['order_commune']).'</p>
                            <p style="margin-bottom:0;"><strong>District:</strong> '._ucwords($row['order_district']).'</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>                          
                        </div>
                        </div>
                    </div>
                    </div>';
        if($isShipper || isLeader() || isAller() || isPublisher()){            
            if($caller){
                $order_caller = '<div class="chip align-middle">
                        <a target="_blank" href="?route=statistics&user='.$caller['id'].'">
                        <img src="'.getAvatar($caller['id']).'"> '.(!isBanned($caller) ? _e($caller['name']) : '<strike class="text-dark"><b>'._e($caller['name']).'</b></strike>').'
                        </a>
                    </div>';
            }else{
                $order_caller = '';
            }            
        }
        if($isCaller  || isLeader() || isAller() || isPublisher()){            
            if($shipper){
                $order_shipper = '<div class="chip align-middle">
                        <a target="_blank" href="?route=statistics&user='.$shipper['id'].'">
                        <img src="'.getAvatar($shipper['id']).'"> '.(!isBanned($shipper) ? _e($shipper['name']) : '<strike class="text-dark"><b>'._e($shipper['name']).'</b></strike>').'
                        </a>
                    </div>';
            }else{
                $order_shipper = '';
            }
            
        }

        $_offer_name = strlen($row['offer_name'])>20 ? substr($row['offer_name'],0,20).'...' : $row['offer_name'];
        $_offer_name = ($_offers[$row['offer']]['offer_link']) ? '<a href="'.$_offers[$row['offer']]['offer_link'].'" target="__blank">'.$_offer_name.'<i class="fas fa-external-link-alt ml-1 text-primary"></i></a>' : $_offer_name;

        $data[]=[
            'id'    => $row['id'],
            'update_time' => date('d-m-Y H:i',$row['update_time']),
            'order_info' => $row['order_info'],
            'parcel_code' => $parcel_code,
            'post_office' => '<b>'.$row['post_office'].'</b>',
            'order_info' => nl2br(_ucwords($row['order_info'])),
            'offer_name' => $_offer_name,
            'number' => _e($row['number']),
            'price_sell' => addDotNumber($row['price_sell']),
            'time' => date('Y/m/d H:i',$row['time']),
            'order_name' => _ucwords($row['order_name']),
            'order_phone' => _e($row['order_phone']).$order_phone,
            'note' => nl2br(_ucwords($row['note'])),
            'note_read' => $note_read,
            'order_province' => _ucwords($row['order_province']),
            'order_caller' => $order_caller,
            'order_shipper' => $order_shipper
        ];
        
    }    
    echo json_encode($data);
}elseif($received_data->action == 'cancelAllSelected'){
    $ids = $received_data->id;        
    if(isBanned()){        
        $output = [
            'message' => 'Không thể hủy chọn đơn hàng khi đang bị cấm.'
        ];      
        echo json_encode($output);
        exit;
    }
    if(!isShipper()){        
        $output = [
            'message' => 'Tài khoản của bạn không phải là shipper !'
        ]; 
        echo json_encode($output);  
        exit;
    }
    $sql = "";
    if(!isLeader()){
        $sql = " `id` in ('".implode("','", $ids)."') and `status`='shipfail' and `user_ship`='".$_user['id']."' ";
    }else {
        if(isAller()){
            $sql = " `id` in ('".implode("','", $ids)."') and `status`='shipfail' and `user_ship`!='' ";            
        } else {
            $memGroup = memberGroup($_user['group']);
            $user_ship = array();
            foreach ($memGroup as $us)
                $user_ship[] = $us['id'];
            $sql = " `id` in ('".implode("','", $ids)."') and `status`='shipfail' and `user_ship` in ('".implode("','", $user_ship)."') ";		
        }
    }    
    $orders = $_db->query("select * from `core_orders` where ".$sql)->fetch_array();    
    if(!$orders){        
        $output = [
            'message' => 'Đơn hàng không tồn tại hoặc không thuộc quyền quản lí của bạn!'
        ];          
        echo json_encode($output); 
        exit;
    }    
    if($_db->exec_query("update `core_orders` set `user_ship`='',`ship_time`='' where ".$sql)){        
        $output = [
            'message' => 'Hủy vận chuyển đơn hàng thành công.'
        ]; 
        echo json_encode($output);
        exit;
    } else{        
        $output = [
            'message' => 'Có lỗi xảy ra vui lòng thử lại sau.'
        ]; 
        echo json_encode($output);
        exit;
    }
}elseif($received_data->action == 'makeApprove'){
    $ids = $received_data->id; 
    if(!isShipper()){        
        $output = [
            'message' => 'Bạn không có quyền thực hiện tác vụ này.'
        ]; 
        echo json_encode($output);
        exit;
    }
    $sql = !isAller() ? " and `user_ship`='".$_user['id']."' " : "";
    $orders = $_db->query("select * from `core_orders` where `id` in ('".implode("','", $ids)."') and `status`='shipfail' ".$sql)->fetch_array();
    if(!$orders){        
        $output = [
            'message' => 'Không tìm thấy đơn hàng nào!'
        ]; 
        echo json_encode($output);
        exit;
    }
    $values_orders = [];
    $values_users = [];
    $values_groups = [];
    $values_orders = [];
    $values_users = [];
    $values_groups = [];
    $order_pending = [];
    $order_approve = [];
    $order_deduct = [];
    $group_pending = [];
    $group_approve = [];
    $group_deduct = [];
    foreach ($orders as $_info) {
        $user = getUser($_info['user_call']);
        $group = getGroup($_info['group']);
        if(!isset($order_pending[$user['id']])){
            $order_pending[$user['id']] = $user['revenue_pending'];
        }
        if(!isset($order_approve[$user['id']])){
            $order_approve[$user['id']] = $user['revenue_approve'];
        }
        if(!isset($order_deduct[$user['id']])){
            $order_deduct[$user['id']] = $user['revenue_deduct'];
        }
        if(!isset($group_pending[$group['id']])){
            $group_pending[$group['id']] = $group['revenue_pending'];
        }
        if(!isset($group_approve[$group['id']])){
            $group_approve[$group['id']] = $group['revenue_approve'];
        }
        if(!isset($group_deduct[$group['id']])){
            $group_deduct[$group['id']] = $group['revenue_deduct'];
        }
        if($_info['r_hold'] > 0){
            $order_pending[$user['id']] = $order_pending[$user['id']] - ($_info['payout_member']*$_info['number']);
            $group_pending[$group['id']] = $group_pending[$group['id']] - ($_info['payout_leader']*$_info['number']);
        }
        if($_info['r_approve'] <= 0){
            $order_approve[$user['id']] = $order_approve[$user['id']] + ($_info['payout_member']*$_info['number']);
            $group_approve[$group['id']] = $group_approve[$group['id']] + ($_info['payout_leader']*$_info['number']);
        }
        if($_info['r_deduct'] > 0){
            $order_deduct[$user['id']] = $order_deduct[$user['id']] - $_info['deduct_member'];
            $group_deduct[$group['id']] = $group_deduct[$group['id']] - $_info['deduct_member'];      
        }
        /* Render user's randomkey */
        $ukey = generateRandomUserKey();
        $values_users[$user['id']] = " ('".$user['id']."','".$order_pending[$user['id']]."','".$order_approve[$user['id']]."','".$order_deduct[$user['id']]."','".$ukey."') ";
        $values_groups[$group['id']] = " ('".$group['id']."','".$group_pending[$group['id']]."','".$group_approve[$group['id']]."','".$group_deduct[$group['id']]."') ";
        $values_orders[$_info['id']] = " ('".$_info['id']."','0','0','1') ";
    }
    if($_db->exec_query("update `core_orders` set `status`='approved' where `id` in ('".implode("','", $ids)."') and `status`='shipfail' ".$sql)){
        if($values_users){
            if($_db->exec_query("INSERT INTO `core_users` (id,revenue_pending,revenue_approve,revenue_deduct,ukey) VALUES ".implode(",", $values_users)." ON DUPLICATE KEY UPDATE revenue_pending = VALUES(revenue_pending),revenue_approve = VALUES(revenue_approve),revenue_deduct = VALUES(revenue_deduct)")){
                if($values_groups){
                    $_db->exec_query("INSERT INTO `core_groups` (id,revenue_pending,revenue_approve,revenue_deduct) VALUES ".implode(",", $values_groups)." ON DUPLICATE KEY UPDATE revenue_pending = VALUES(revenue_pending),revenue_approve = VALUES(revenue_approve),revenue_deduct = VALUES(revenue_deduct)");
                }
                if($values_orders){
                    $_db->exec_query("INSERT INTO `core_orders` (id,r_hold,r_approve,r_deduct) VALUES ".implode(",", $values_orders)." ON DUPLICATE KEY UPDATE r_hold = VALUES(r_hold),r_approve = VALUES(r_approve),r_deduct = VALUES(r_deduct)");
                }
            }
        }        
        $output = [
            'message' => 'Đánh dấu giao hàng thành công '.count($orders).' đơn hàng!'
        ]; 
        echo json_encode($output);
        exit;
    } else{        
        $output = [
            'message' => 'Có lỗi xảy ra vui lòng thử lại sau ít phút!'
        ]; 
        echo json_encode($output);
        exit;
    }
}else if($received_data->action == 'makeShiperror'){
    $ids = $received_data->id;     
    if(!isShipper()){        
        $output = [
            'message' => 'Bạn không có quyền thực hiện tác vụ này.'
        ]; 
        echo json_encode($output);
        exit;
    }
    $sql = !isAller() ? " and `user_ship`='".$_user['id']."' " : "";
    $orders = $_db->query("select * from `core_orders` where `id` in ('".implode("','", $ids)."') and `status`='shipfail' ".$sql)->fetch_array();
    if(!$orders){        
        $output = [
            'message' => 'Không tìm thấy đơn hàng nào!'
        ]; 
        echo json_encode($output);
        exit;
    }
    $values_orders = [];
    $values_users = [];
    $values_groups = [];
    $order_pending = [];
    $order_approve = [];
    $order_deduct = [];
    $group_pending = [];
    $group_approve = [];
    $group_deduct = [];
    foreach ($orders as $_info) {
        $user = getUser($_info['user_call']);
        $group = getGroup($_info['group']);
        if(!isset($order_pending[$user['id']])){
            $order_pending[$user['id']] = $user['revenue_pending'];
        }
        if(!isset($order_approve[$user['id']])){
            $order_approve[$user['id']] = $user['revenue_approve'];
        }
        if(!isset($order_deduct[$user['id']])){
            $order_deduct[$user['id']] = $user['revenue_deduct'];
        }
        if(!isset($group_pending[$group['id']])){
            $group_pending[$group['id']] = $group['revenue_pending'];
        }
        if(!isset($group_approve[$group['id']])){
            $group_approve[$group['id']] = $group['revenue_approve'];
        }
        if(!isset($group_deduct[$group['id']])){
            $group_deduct[$group['id']] = $group['revenue_deduct'];
        }
        if($_info['r_hold'] > 0){
            $order_pending[$user['id']] = $order_pending[$user['id']] - ($_info['payout_member']*$_info['number']);
            $group_pending[$group['id']] = $group_pending[$group['id']] - ($_info['payout_leader']*$_info['number']);
        }
        if($_info['r_approve'] > 0){
            $order_approve[$user['id']] = $order_approve[$user['id']] - ($_info['payout_member']*$_info['number']);
            $group_approve[$group['id']] = $group_approve[$group['id']] - ($_info['payout_leader']*$_info['number']);
        }
        if($_info['r_deduct'] <= 0){
            $order_deduct[$user['id']] = $order_deduct[$user['id']] + $_info['deduct_member'];
            $group_deduct[$group['id']] = $group_deduct[$group['id']] + $_info['deduct_member'];      
        }
        /* Render user's randomkey */
        $ukey = generateRandomUserKey();
        $values_users[$user['id']] = " ('".$user['id']."','".$order_pending[$user['id']]."','".$order_approve[$user['id']]."','".$order_deduct[$user['id']]."','".$ukey."') ";
        $values_groups[$group['id']] = " ('".$group['id']."','".$group_pending[$group['id']]."','".$group_approve[$group['id']]."','".$group_deduct[$group['id']]."') ";
        $values_orders[$_info['id']] = " ('".$_info['id']."','0','0','1') ";
    }
    if($_db->exec_query("update `core_orders` set `status`='shiperror' where `id` in ('".implode("','", $ids)."') and `status`='shipfail' ".$sql)){
        if($values_users){
            if($_db->exec_query("INSERT INTO `core_users` (id,revenue_pending,revenue_approve,revenue_deduct,ukey) VALUES ".implode(",", $values_users)." ON DUPLICATE KEY UPDATE revenue_pending = VALUES(revenue_pending),revenue_approve = VALUES(revenue_approve),revenue_deduct = VALUES(revenue_deduct)")){
                if($values_groups){
                    $_db->exec_query("INSERT INTO `core_groups` (id,revenue_pending,revenue_approve,revenue_deduct) VALUES ".implode(",", $values_groups)." ON DUPLICATE KEY UPDATE revenue_pending = VALUES(revenue_pending),revenue_approve = VALUES(revenue_approve),revenue_deduct = VALUES(revenue_deduct)");
                }
                if($values_orders){
                    $_db->exec_query("INSERT INTO `core_orders` (id,r_hold,r_approve,r_deduct) VALUES ".implode(",", $values_orders)." ON DUPLICATE KEY UPDATE r_hold = VALUES(r_hold),r_approve = VALUES(r_approve),r_deduct = VALUES(r_deduct)");
                }
            }
        }        
        $output = [
            'message' => 'Đánh dấu không nhận hàng thành công '.count($orders).' đơn hàng!'
        ]; 
        echo json_encode($output);
        exit;
    } else{        
        $output = [
            'message' => 'Có lỗi xảy ra vui lòng thử lại sau ít phút!'
        ]; 
        echo json_encode($output);
        exit;
    }
}elseif($received_data->action == 'delete'){
    $_id = $received_data->id;
    if(isBanned()){        
        $output = [
            'message' => 'Không thể hủy chọn đơn hàng khi đang bị cấm.'
        ]; 
        echo json_encode($output);
        exit;
    }
    if(!isShipper()){        
        $output = [
            'message' => 'Tài khoản của bạn không phải là shipper !'
        ]; 
        echo json_encode($output);
        exit;
    }
    $sql = "";
    if(!isLeader()){
        $sql = " `id`='".$_id."' and `status`='shipfail' and `user_ship`='".$_user['id']."' ";
    }
    else {
        if(isAller()){
            $sql = " `id`='".$_id."' and `status`='shipfail' and `user_ship`!='' ";
        } else {
            $memGroup = memberGroup($_user['group']);
            $user_ship = array();
            foreach ($memGroup as $us){
                $user_ship[] = $us['id'];
            }
            $sql = " `id`='".$_id."' and `status`='shipfail' and `user_ship` in ('".implode("','", $user_ship)."') ";		
        }

    }    
    $orders = $_db->query("select * from `core_orders` where ".$sql)->fetch_array();	
    if(!$orders){        
        $output = [
            'message' => 'Đơn hàng không tồn tại hoặc không thuộc quyền quản lí của bạn!'
        ]; 
        echo json_encode($output);
        exit;
    }
    if($_db->exec_query("update `core_orders` set `user_ship`='',`ship_time`='' where ".$sql)){        
        $output = [
            'message' => 'Hủy vận chuyển đơn hàng thành công.'
        ]; 
        echo json_encode($output);
        exit;
    } else {        
        $output = [
            'message' => 'Có lỗi xảy ra vui lòng thử lại sau.'
        ]; 
        echo json_encode($output);
        exit;
    }
}
?>