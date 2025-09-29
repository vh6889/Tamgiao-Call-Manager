<?php
$debug = false;
if($debug == false) {
    error_reporting(0);
    ini_set('display_errors', '0');
}
if($debug == true) {
    error_reporting(E_ALL & ~E_NOTICE);
    ini_set('display_errors', '1');
}

$_action = $_GET['action'];
$_return = array();

if(!$_action || $_action==''){
    http_response_code(404);
    $_return['error']='404 Not Found!';
    exit(json_encode($_return));
}else{
	include('includes/config.php');
	include('includes/api_functions.php');

    /* Check empty key */
    if(!isset($_REQUEST['key']) || trim($_REQUEST['key'])=='') {
        $_return['error']='API Key not found!';
        exit(json_encode($_return));
    }
    
    /* Get request user key */
    $ukey = isset($_REQUEST['ukey']) ? trim(escape_string($_REQUEST['ukey'])) : null;

    /* Check offer */
    $_offer = array();
    $_offers = getOffers(trim($_REQUEST['key']));
    if(count($_offers)){
        foreach ($_offers as $_off) {
            if(array_key_exists($_off['tracking_token'], $_REQUEST)) {
                $_offer = $_off;
                $_return['offer_name']=$_off['name'];
                $_return['tracking_token']=$_off['tracking_token'];
                break;
            }
        }
        /* Set is first offer if empty or can not find valid `tracking_token` */
        if(!count($_offer)){
            $_offer = $_offers[0];
            $_return['offer_name']=$_offers[0]['name'];
            $_return['tracking_token']='';
        }
    }
    if(!count($_offer)) {
        $_return['error']='Offer not found!';
        exit(json_encode($_return));
    }

    /* ACTION: landing_visit_counter */
    if($_action=='landing_visit_counter') {
        if(isset($_REQUEST['key']) && isset($_REQUEST['landing']) && isset($_offer['tracking_token'])) {
            $_landing_stats = landing_visit_today_counter(
                            trim($_REQUEST['key']),
                            trim($_REQUEST['landing']),
                            trim($_offer['tracking_token']),
                            $ukey
                        );
            if($_landing_stats) {
                $_landing_stats['viewPage']+=1; //Add this view for return data.
                $_return['landing_stats']=$_landing_stats;
                $_return['success']='API increament landing visit counter success!';
            } else {
                $_return['error']='API can not increament landing visit counter!';
            }
        } else {
            $_return['error']='Invalid parameters!';
        }
        exit(json_encode($_return));
    }
    /* end ACTION: landing_visit_counter */
    

    /* ACTION: save_order */
    if($_action=='save_order') {
        /* Check empty name and phone */
        if(!isset($_REQUEST['order_name']) || trim($_REQUEST['order_name'])=='' ||
          !isset($_REQUEST['order_phone']) || trim($_REQUEST['order_phone'])=='') {
            $_return['error']='Name or Phone empty!';
            exit(json_encode($_return));
        }
        $order_name = trim(escape_string($_REQUEST['order_name']));
        $order_phone = trim(escape_string($_REQUEST['order_phone']));
        $order_number = isset($_REQUEST['number']) ? $_REQUEST['number'] : 0;
        $price_sell = isset($_REQUEST['price']) ? $_REQUEST['price'] : $_offer['price']*$order_number;
        $landing = isset($_REQUEST['landing']) ? trim(escape_string($_REQUEST['landing'])) : 'unknown';
        $time = time();
        $date = date('Y/m/d',$time);
        
        /* Validate phone number */
        $order_phone = check_phone($order_phone);
        if(!$order_phone){
            $_return['error']='Phone invalid!';
            exit(json_encode($_return));
        }
        if(check_phone_banned($order_phone)){
            $_return['error']='Phone banned!';
            exit(json_encode($_return));
        }
        if(check_phone_blacklist($order_phone)){
            $_return['error']='Phone in backlist!';
            exit(json_encode($_return));
        }
        if(check_phone_today_order($order_phone, $_offer['id'])){
           $_return['error']='Phone has been ordered!';
            exit(json_encode($_return));
        }

        /* Set order datas */
        $_order_datas = array(
            'offer' => $_offer['id'],
            'offer_name' => $_offer['name'],
            'price' => $_offer['price'],
            'price_deduct' => $_offer['price_deduct'],
            'price_sell' => $price_sell,
            'number' => $order_number,
            'status' => 'uncheck',
            'time' => $time,
            'date' => $date,
            'order_name' => $order_name,
            'order_phone' => $order_phone,
            'landing' => $landing,
            'ukey' => $ukey,
        );
        /* Excute to save order */
        $_save_order = db_insert_data('core_orders', $_order_datas);
        
        if($_save_order) {
            $today = date('d-m-Y',time());
            /* Update talbe `core_landing_stats` */
            $_ld_stats = db_get_data('core_landing_stats',
                                    array('date'=>$today, 'landing'=>$landing, 'offer'=>$_offer['id'], 'ukey' => $ukey),
                                    '*',1);
            $_update_datas = array();
            if(!$_ld_stats){
                $_update_datas = array(
                    'offer' => $_offer['id'],
                    'date' => $today,
                    'landing' => $landing,
                    'viewPage' => 1,
                    'order' => 1,
                    'ukey' => $ukey,
                );
                $_update_stats = db_insert_data('core_landing_stats', $_update_datas);
            } else {
                $_update_datas = array(
                    'order' => $_ld_stats['order']+1
                );
                $_update_stats = db_update_data('core_landing_stats', $_update_datas, array('id' => $_ld_stats['id']));
            }
        
            /* Create footect.work backend notification */
            // curl_sendNotifi($_url.'/sendNotifi.php?act=uncheck&id='.$_db->insert_id());
            
            /* Check if URL has postback token for send postback */
            if(isset($_REQUEST[$_offer['tracking_token']]) && $_REQUEST[$_offer['tracking_token']]!='') {
                /* Send Postback */
                $_tracking_id = $_REQUEST[$_offer['tracking_token']];
                $_postback_url = str_replace("{".$_offer['tracking_token']."}", $_tracking_id, $_offer['s2s_postback_url']);
                $response = send_postback($_postback_url);

                /* Update data to `core_s2s_postback` */
                $update_s2s_postback = update_s2s_postback($_postback_url,$response,$_offer,$landing,$ukey);
            }
            $_return['order'] = [
                'order_name' => $order_name,
                'order_phone' => $order_phone,
                'time' => $time,
            ];
            $_return['success']='API create order success!';
        } else {
            $_return['error']='Can not create order, please try again later!';
        }
    }
    /* end ACTION: save_order */

    
    /* ACTION: update_order */
    if($_action=='update_order') {
        /* Check empty name and phone */
        if(!isset($_REQUEST['order_name']) || trim($_REQUEST['order_name'])=='' ||
          !isset($_REQUEST['order_phone']) || trim($_REQUEST['order_phone'])=='') {
            $_return['error']='Name or Phone empty!';
            exit(json_encode($_return));
        }
        $order_name = trim(escape_string($_REQUEST['order_name']));
        $order_phone = trim(escape_string($_REQUEST['order_phone']));
        $order_time = trim(escape_string($_REQUEST['time']));
        
        /* Validate phone number */
        $order_phone = check_phone($order_phone);
        if(!$order_phone){
            $_return['error']='Phone invalid!';
            exit(json_encode($_return));
        }
        if(check_phone_banned($order_phone)){
            $_return['error']='Phone banned!';
            exit(json_encode($_return));
        }
        if(check_phone_blacklist($order_phone)){
            $_return['error']='Phone in backlist!';
            exit(json_encode($_return));
        }
        if(check_phone_today_order($order_phone, $_offer['id'],$order_time)){
           $_return['error']='Phone has been ordered!';
            exit(json_encode($_return));
        }

        /* Set order datas */
        $_order_datas = array(
            'order_name' => $order_name,
            'order_phone' => $order_phone,
            'order_address' => trim(escape_string($_REQUEST['order_address'])),
            'order_commune' => trim(escape_string($_REQUEST['order_commune'])),
            'order_district' => trim(escape_string($_REQUEST['order_district'])),
            'order_province' => trim(escape_string($_REQUEST['order_province'])),
        );
        /* Excute to save order */
        $_update_order = db_update_data('core_orders', $_order_datas, " `time`=$order_time and `status` IN ('uncheck', 'calling')");
        if($_update_order) {
            $_return['order'] = [
                'order_name' => $order_name,
                'order_phone' => $order_phone,
            ];
            $_return['success']='Update order successfully!';
        } else {
            $_return['error']='Can not update order, please try again later!';
        }
    }
    /* end ACTION: update_order */
    
    exit(json_encode($_return));
}

?>