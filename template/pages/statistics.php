<?php


$u = isset($_GET['user']) ? trim($_GET['user']) : '';
$g = isset($_GET['group']) ? trim($_GET['group']) : '';
$offer = isset($_GET['offer']) ? trim($_GET['offer']) : 'all';
$ts = isset($_GET['ts']) && !empty($_GET['ts']) ? $_GET['ts'] : date('d/m/Y',strtotime('-6 days GMT+7 00:00'));
$te = isset($_GET['te']) && !empty($_GET['te']) ? $_GET['te'] : date('d/m/Y',time());
$view = isset($_GET['view']) && $_GET['view'] == "hour" ? "hour" : "date";
$coll = isset($_GET['coll']) ? true : false;

if($coll == true)
  $_nav = "statistics2";

$filter_status = isset($_COOKIE['statistics_status']) ? explode(",", $_COOKIE['statistics_status']) : ['sales','approved','uncheck','calling','pending','shipping','shipdelay','callerror','rejected','shiperror','shipfail','trashed'];

$nav = isset($_GET['nav']) ? $_GET['nav'] : "statistics";
if($nav == "groupStats"){
  $_nav = "group";
  $_nav_li = "groupStats";
}

$time_ts = strtotime(str_replace('/', '-', $ts)." GMT+7 00:00");
$time_te = strtotime(str_replace('/', '-', $te)." GMT+7 23:59");

$results = array();
$list_time = array();


if($time_ts && $time_te){
  for ($i=$time_ts; $i <= $time_te ; $i+=86400){
    $list_time[] = date("Y/m/d",$i);
  }
}
if($view == "hour"){

  if($time_ts && $time_te){
    for ($i=0; $i <= 23 ; $i++) { 
      $results[($i < 10 ? '0'.$i : $i).':00']['count_total'] = 0;
      $results[($i < 10 ? '0'.$i : $i).':00']['count_presales'] = 0;
      $results[($i < 10 ? '0'.$i : $i).':00']['count_sales'] = 0;
      $results[($i < 10 ? '0'.$i : $i).':00']['count_earnings'] = 0;
      $results[($i < 10 ? '0'.$i : $i).':00']['count_uncheck'] = 0;
      $results[($i < 10 ? '0'.$i : $i).':00']['count_calling'] = 0;
      //$results[($i < 10 ? '0'.$i : $i).':00']['count_callback'] = 0;
      $results[($i < 10 ? '0'.$i : $i).':00']['count_callerror'] = 0;
      $results[($i < 10 ? '0'.$i : $i).':00']['count_pending'] = 0;
      $results[($i < 10 ? '0'.$i : $i).':00']['count_shipping'] = 0;
      $results[($i < 10 ? '0'.$i : $i).':00']['count_shipdelay'] = 0;
      $results[($i < 10 ? '0'.$i : $i).':00']['count_shiperror'] = 0;
      $results[($i < 10 ? '0'.$i : $i).':00']['count_shipfail'] = 0;
      $results[($i < 10 ? '0'.$i : $i).':00']['count_rejected'] = 0;
      $results[($i < 10 ? '0'.$i : $i).':00']['count_trashed'] = 0;
      $results[($i < 10 ? '0'.$i : $i).':00']['count_approved'] = 0;
    }

  }

} else {

  if($time_ts && $time_te){
    for ($i=$time_ts; $i <= $time_te ; $i+=86400){
      $results[date("Y/m/d",$i)]['count_total'] = 0;
      $results[date("Y/m/d",$i)]['count_presales'] = 0;
      $results[date("Y/m/d",$i)]['count_sales'] = 0;
      $results[date("Y/m/d",$i)]['count_earnings'] = 0;
      $results[date("Y/m/d",$i)]['count_uncheck'] = 0;
      $results[date("Y/m/d",$i)]['count_calling'] = 0;
      //$results[date("Y/m/d",$i)]['count_callback'] = 0;
      $results[date("Y/m/d",$i)]['count_callerror'] = 0;
      $results[date("Y/m/d",$i)]['count_pending'] = 0;
      $results[date("Y/m/d",$i)]['count_shipping'] = 0;
      $results[date("Y/m/d",$i)]['count_shipdelay'] = 0;
      $results[date("Y/m/d",$i)]['count_shiperror'] = 0;
      $results[date("Y/m/d",$i)]['count_shipfail'] = 0;
      $results[date("Y/m/d",$i)]['count_rejected'] = 0;
      $results[date("Y/m/d",$i)]['count_trashed'] = 0;
      $results[date("Y/m/d",$i)]['count_approved'] = 0;
    }
  }

}

$sql = "";
if($offer != "all")
  $sql = " and `offer`='".escape_string($offer)."' ";

$name_statistics = "";
$isType = "admin";

if($u){
  $user  = getUser($u);
  if(($user['id'] && isLeader() && $_user['group'] == $user['group']) || $user['id'] == $_user['id'] || isAller()){

    if(isCaller($user)){
      $isType = "call";
      $name_statistics = '(Caller: <b class="text-danger">'._e($user['name']).'</b>)';
      $_data = $_db->query("select * from `core_orders` where `status`!='uncheck' and `user_call`='".$user['id']."' and `date` in ('".implode("','", $list_time)."') ".$sql." order by `time` asc ")->fetch_array();

      $all_sales = getSaleCall(['approved'],$user['id'],null);
      $all_approved = getOrderCall("approved",$user['id'],null,true);
      //$all_callback = getOrderCall("callback",$user['id'],null,true);
      $all_calling = getOrderCall("calling",$user['id'],null,true);
      $all_pending = getOrderCall("pending",$user['id'],null,true);
      $all_shipping = getOrderCall("shipping",$user['id'],null,true);
      $all_shipdelay = getOrderCall("shipdelay",$user['id'],null,true);
      $all_shiperror = getOrderCall("shiperror",$user['id'],null,true);
      $all_shiperror = getOrderCall("shipfail",$user['id'],null,true);
      $all_callerror = getOrderCall("callerror",$user['id'],null,true);
      $all_rejected = getOrderCall("rejected",$user['id'],null,true);
      $all_trashed = getOrderCall("trashed",$user['id'],null,true);

    } else if(isColler($user)){
      $isType = "coll";
      $name_statistics = '(Collaborator: <b class="text-danger">'._e($user['name']).'</b>)';
      $_data = $_db->query("select * from `core_orders` where `status` in ('approved','pending','shipping','shipdelay','shiperror','shipfail') and `user_call`='".$user['id']."' and `date` in ('".implode("','", $list_time)."') ".$sql." order by `time` asc ")->fetch_array();

      $all_sales = getSaleCall(['approved'],$user['id'],null);
      $all_approved = getOrderCall("approved",$user['id'],null,true);
      $all_pending = getOrderCall("pending",$user['id'],null,true);
      $all_shipping = getOrderCall("shipping",$user['id'],null,true);
      $all_shipdelay = getOrderCall("shipdelay",$user['id'],null,true);
      $all_shiperror = getOrderCall("shiperror",$user['id'],null,true);
      $all_shiperror = getOrderCall("shipfail",$user['id'],null,true);

    } else if(isPublisher($user)){
      $isType = "publisher";
      $name_statistics = '(Publisher: <b class="text-danger">'._e($user['name']).'</b>)';
      $_data = $_db->query("select * from `core_orders` where `ukey`='".$user['ukey']."' and `date` in ('".implode("','", $list_time)."') ".$sql." order by `time` asc ")->fetch_array();

      $all_sales = getPublisherSale($_user['ukey'],['approved'],null);
      $all_approved = getPublisherOrder($_user['ukey'],"approved",null,true);
      $all_uncheck = getPublisherOrder($_user['ukey'],"uncheck",null,true);
      $all_calling = getPublisherOrder($_user['ukey'],"calling",null,true);
      $all_pending = getPublisherOrder($_user['ukey'],"pending",null,true);
      $all_shipping = getPublisherOrder($_user['ukey'],"shipping",null,true);
      $all_shipdelay = getPublisherOrder($_user['ukey'],"shipdelay",null,true);
      $all_shiperror = getPublisherOrder($_user['ukey'],"shiperror",null,true);
      $all_shipfail = getPublisherOrder($_user['ukey'],"shipfail",null,true);
      $all_callerror = getPublisherOrder($_user['ukey'],"callerror",null,true);
      $all_rejected = getPublisherOrder($_user['ukey'],"rejected",null,true);
      $all_trashed = getPublisherOrder($_user['ukey'],"trashed",null,true);

    } else {
      $isType = "ship";
      $name_statistics = '(Shipper: <b class="text-danger">'._e($user['name']).'</b>)';
      $_data = $_db->query("select * from `core_orders` where `status` in ('pending','shipping','shipdelay','shiperror','approved','shipfail') and `user_ship`='".$user['id']."' and `date` in ('".implode("','", $list_time)."') ".$sql." order by `time` asc ")->fetch_array();
      $all_approved = getOrderShip("approved",$user['id'],null,true);
      $all_pending = getOrderShip("pending",$user['id'],null,true);
      $all_shipping = getOrderShip("shipping",$user['id'],null,true);
      $all_shipdelay = getOrderShip("shipdelay",$user['id'],null,true);
      $all_shiperror = getOrderShip("shiperror",$user['id'],null,true);
      $all_shiperror = getOrderShip("shipfail",$user['id'],null,true);
    }

  } else {
    echo '<div class="alert alert-warning" role="alert">
            <h4 class="alert-heading">Truy cập bị từ chối!</h4>
            <p>Bạn không thể xem thống kê của thành viên này do người này không thuộc quyền quản lí của bạn.</p>
            <hr>
            <p class="mb-0">Vui lòng trở lại trang trước đó.</p>
          </div>';
    goto end;
  }

} else if ($g) {
  $group  = getGroup($g);


  if(($group['id'] && $_user['group'] == $group['id']) || isAller()){

    $memGroup = memberGroup($group['id']);
    $user_sql = array();
    foreach ($memGroup as $us){
      if(isPublisher())
        $user_sql[] = $us['ukey'];
      else
        $user_sql[] = $us['id'];
    }

    $name_statistics = '(Group: <b class="text-danger">'._e($group['name']).'</b>)';

    if(in_array($group['type'],["all","call"])){
      $isType = "call";
      $_data = $_db->query("select * from `core_orders` where `status`!='uncheck' and `user_call` in ('".implode("','", $user_sql)."') and `date` in ('".implode("','", $list_time)."') ".$sql." order by `time` asc ")->fetch_array();

      $all_sales = getSaleCall(['approved'],null,$group['id']);
      $all_approved = getOrderCall("approved",null,$group['id'],true);
      //$all_callback = getOrderCall("callback",null,$group['id'],true);
      $all_calling = getOrderCall("calling",null,$group['id'],true);
      $all_pending = getOrderCall("pending",null,$group['id'],true);
      $all_shipping = getOrderCall("shipping",null,$group['id'],true);
      $all_shipdelay = getOrderCall("shipdelay",null,$group['id'],true);
      $all_shiperror = getOrderCall("shiperror",null,$group['id'],true);
      $all_shipfail = getOrderCall("shiperror",null,$group['id'],true);
      $all_callerror = getOrderCall("callerror",null,$group['id'],true);
      $all_rejected = getOrderCall("rejected",null,$group['id'],true);
      $all_trashed = getOrderCall("trashed",null,$group['id'],true);

    } else if(in_array($group['type'],["collaborator"])){
      $isType = "coll";
      $_data = $_db->query("select * from `core_orders` where `status` in ('approved','pending','shipping','shipdelay','shiperror','shipfail') and `user_call` in ('".implode("','", $user_sql)."') and `date` in ('".implode("','", $list_time)."') ".$sql." order by `time` asc ")->fetch_array();

      $all_sales = getSaleCall(['approved'],null,$group['id']);
      $all_approved = getOrderCall("approved",null,$group['id'],true);
      $all_pending = getOrderCall("pending",null,$group['id'],true);
      $all_shipping = getOrderCall("shipping",null,$group['id'],true);
      $all_shipdelay = getOrderCall("shipdelay",null,$group['id'],true);
      $all_shiperror = getOrderCall("shiperror",null,$group['id'],true);
      $all_shipfail = getOrderCall("shipfail",null,$group['id'],true);

    } else if(in_array($group['type'],["publisher"])){
      $isType = "publisher";
      $_data = $_db->query("select * from `core_orders` where `ukey` in ('".implode("','", $user_sql)."') and `date` in ('".implode("','", $list_time)."') ".$sql." order by `time` asc ")->fetch_array();

      $all_sales = getPublisherSale(null,['approved'],$group['id']);
      $all_approved = getPublisherOrder(null,"approved",$group['id'],true);
      $all_uncheck = getPublisherOrder(null,"uncheck",$group['id'],true);
      $all_calling = getPublisherOrder(null,"calling",$group['id'],true);
      $all_pending = getPublisherOrder(null,"pending",$group['id'],true);
      $all_shipping = getPublisherOrder(null,"shipping",$group['id'],true);
      $all_shipdelay = getPublisherOrder(null,"shipdelay",$group['id'],true);
      $all_shiperror = getPublisherOrder(null,"shiperror",$group['id'],true);
      $all_shipfail = getPublisherOrder(null,"shipfail",$group['id'],true);
      $all_callerror = getPublisherOrder(null,"callerror",$group['id'],true);
      $all_rejected = getPublisherOrder(null,"rejected",$group['id'],true);
      $all_trashed = getPublisherOrder(null,"trashed",$group['id'],true);

    } else {
      $isType = "ship";

      $_data = $_db->query("select * from `core_orders` where `status` in ('pending','shipping','shipdelay','shiperror','shipfail','approved') and `user_ship` in ('".implode("','", $user_sql)."') and `date` in ('".implode("','", $list_time)."') ".$sql." order by `time` asc ")->fetch_array();
      $all_approved = getOrderShip("approved",$user_sql,null,true);
      $all_pending = getOrderShip("pending",$user_sql,null,true);
      $all_shipping = getOrderShip("shipping",$user_sql,null,true);
      $all_shipdelay = getOrderShip("shipdelay",$user_sql,null,true);
      $all_shiperror = getOrderShip("shiperror",$user_sql,null,true);
      $all_shipfail = getOrderShip("shipfail",$user_sql,null,true);
    }



  } else {
    echo '<div class="alert alert-warning" role="alert">
            <h4 class="alert-heading">Truy cập bị từ chối!</h4>
            <p>Bạn không thể xem thống kê của nhóm này do nhóm này không thuộc quyền quản lí của bạn.</p>
            <hr>
            <p class="mb-0">Vui lòng trở lại trang trước đó.</p>
          </div>';
    goto end;
  }
} else {

  if(!isAller()){

    if(isCaller()){
      $isType = "call";
      $name_statistics = '(<b>Caller</b>)';
      $_data = $_db->query("select * from `core_orders` where `status`!='uncheck' and `user_call`='".$_user['id']."' and `date` in ('".implode("','", $list_time)."') ".$sql." order by `time` asc ")->fetch_array();

      $all_sales = getSaleCall(['approved'],$_user['id'],null);
      $all_approved = getOrderCall("approved",$_user['id'],null,true);
      //$all_callback = getOrderCall("callback",$_user['id'],null,true);
      $all_calling = getOrderCall("calling",$_user['id'],null,true);
      $all_pending = getOrderCall("pending",$_user['id'],null,true);
      $all_shipping = getOrderCall("shipping",$_user['id'],null,true);
      $all_shipdelay = getOrderCall("shipdelay",$_user['id'],null,true);
      $all_shiperror = getOrderCall("shiperror",$_user['id'],null,true);
      $all_shipfail = getOrderCall("shipfail",$_user['id'],null,true);
      $all_callerror = getOrderCall("callerror",$_user['id'],null,true);
      $all_rejected = getOrderCall("rejected",$_user['id'],null,true);
      $all_trashed = getOrderCall("trashed",$_user['id'],null,true);

    } else if(isColler()){
      $isType = "coll";
      $name_statistics = '(<b>Collaborator</b>)';
      $_data = $_db->query("select * from `core_orders` where `status` in ('pending','shipping','shipdelay','shiperror','shipfail','approved') and `user_call`='".$_user['id']."' and `date` in ('".implode("','", $list_time)."') ".$sql." order by `time` asc ")->fetch_array();

      $all_sales = getSaleCall(['approved'],$_user['id'],null);
      $all_approved = getOrderCall("approved",$_user['id'],null,true);
      $all_pending = getOrderCall("pending",$_user['id'],null,true);
      $all_shipping = getOrderCall("shipping",$_user['id'],null,true);
      $all_shipdelay = getOrderCall("shipdelay",$_user['id'],null,true);
      $all_shiperror = getOrderCall("shiperror",$_user['id'],null,true);
      $all_shipfail = getOrderCall("shipfail",$_user['id'],null,true);

    } else if(isPublisher()){
      $isType = "publisher";
      $name_statistics = '(<b>Publisher</b>)';
      $_data = $_db->query("select * from `core_orders` where `typeOrder`='".($coll == true ? 1 : 0)."' and `ukey`='".$_user['ukey']."' and `date` in ('".implode("','", $list_time)."') ".$sql." order by `time` asc ")->fetch_array();

      $all_sales = getPublisherSale($_user['ukey'],['approved'],null);
      $all_approved = getPublisherOrder($_user['ukey'],"approved",null,true);
      $all_uncheck = getPublisherOrder($_user['ukey'],"uncheck",null,true);
      $all_calling = getPublisherOrder($_user['ukey'],"calling",null,true);
      $all_pending = getPublisherOrder($_user['ukey'],"pending",null,true);
      $all_shipping = getPublisherOrder($_user['ukey'],"shipping",null,true);
      $all_shipdelay = getPublisherOrder($_user['ukey'],"shipdelay",null,true);
      $all_shiperror = getPublisherOrder($_user['ukey'],"shiperror",null,true);
      $all_shipfail = getPublisherOrder($_user['ukey'],"shipfail",null,true);
      $all_callerror = getPublisherOrder($_user['ukey'],"callerror",null,true);
      $all_rejected = getPublisherOrder($_user['ukey'],"rejected",null,true);
      $all_trashed = getPublisherOrder($_user['ukey'],"trashed",null,true);

    } else {
      $isType = "ship";
      $name_statistics = '(<b>Shipper</b>)';

      $_data = $_db->query("select * from `core_orders` where `status` in ('pending','shipping','shipdelay','shiperror','shipfail','approved') and `user_ship`='".$_user['id']."' and `date` in ('".implode("','", $list_time)."') ".$sql." order by `time` asc ")->fetch_array();

      $all_approved = getOrderShip("approved",$_user['id'],null,true);
      $all_pending = getOrderShip("pending",$_user['id'],null,true);
      $all_shipping = getOrderShip("shipping",$_user['id'],null,true);
      $all_shipdelay = getOrderShip("shipdelay",$_user['id'],null,true);
      $all_shiperror = getOrderShip("shiperror",$_user['id'],null,true);
      $all_shipfail = getOrderShip("shipfail",$_user['id'],null,true);
    }


  }
  else {
    $name_statistics = $coll == false ? '(<b>Tổng Ads</b>)' : '(<b>Tổng Collaborator</b>)';
    $_data = $_db->query("select * from `core_orders` where `typeOrder`='".($coll == true ? 1 : 0)."' and `date` in ('".implode("','", $list_time)."') ".$sql." order by `time` asc ")->fetch_array();

    $all_sales = getSaleCall(['approved'],null,null,'price_deduct',($coll == true ? 1 : 0));
    $all_approved = getOrderAll("approved",null,null,true,'',($coll == true ? 1 : 0));
    $all_uncheck = getOrderAll("uncheck",null,null,true,'',($coll == true ? 1 : 0));
    //$all_callback = getOrderAll("callback",null,null,true);
    $all_calling = getOrderAll("calling",null,null,true,'',($coll == true ? 1 : 0));
    $all_pending = getOrderAll("pending",null,null,true,'',($coll == true ? 1 : 0));
    $all_shipping = getOrderAll("shipping",null,null,true,'',($coll == true ? 1 : 0));
    $all_shipdelay = getOrderAll("shipdelay",null,null,true,'',($coll == true ? 1 : 0));
    $all_shiperror = getOrderAll("shiperror",null,null,true,'',($coll == true ? 1 : 0));
    $all_shipfail = getOrderAll("shipfail",null,null,true,'',($coll == true ? 1 : 0));
    $all_callerror = getOrderAll("callerror",null,null,true,'',($coll == true ? 1 : 0));
    $all_rejected = getOrderAll("rejected",null,null,true,'',($coll == true ? 1 : 0));
    $all_trashed = getOrderAll("trashed",null,null,true,'',($coll == true ? 1 : 0));

    $isType = "admin";
  }
}

$_group = getGroup($_user['group']);
$_offers = getOffer();
$_offers_deduct = [];
$_offers_cost = [];
foreach ($_offers as $off) {
    $_offers_deduct[$off['id']] = $off['price_deduct'];
    $_offers_cost[$off['id']] = $off['cost'];
}

// echo'<pre>';
// print_r($_offers_cost);
// print_r($_data);
// echo'</pre>';

foreach ($_data as $orders) {
  $type = $view == "hour" ? date('H',$orders['time']).':00' : $orders['date'];
  $results[$type]['count_total'] = $results[$type]['count_total']+1;
  if(in_array($orders['status'], ['approved','shipping','pending','shipdelay','shiperror','shipfail'])){
    /* Calculate row count_presales */
    $results[$type]['count_presales'] = $results[$type]['count_presales']+$orders['number'];
    /* Calculate row count_sales */
    if($orders['status']=='approved')
        $results[$type]['count_sales'] = $results[$type]['count_sales']+$orders['number'];

    /* Calculate row count_earnings */
    $_order_earning  = $orders['number']*$orders['price_deduct'];  // Tổng tiền đơn hàng
    // echo $_order_earning.'</br>';
    if($isType == "admin") {

      /* Tính lợi nhuận mỗi đơn Earning Per Order (EPO) */
      $_order_earning -= $_order_earning*$_offers_cost[$orders['offer']]/100; // Trừ chi phí (sale, telesale,....)
      // $_order_earning -= $orders['number']*$_offers_deduct[$orders['offer']]; // Trừ giá vốn sản phẩm
      // echo $_order_earning.'</br>';

    } else {
      // TODO: cần thêm tính lũy kế cho caller có đơn hàng bán được nhiều sản phẩm.
      // Hiển thị payout cho caller
      if($orders['payout_type']=='percent') {
        $_order_earning = $_order_earning*$orders['payout_member']/100;
      }else{
        $_order_earning = $orders['number']*$orders['payout_member'];
      }
    }
    // $results[$type]['count_earnings'] = $results[$type]['count_earnings']+($orders['number']*$orders[($isType == "admin" ? 'price_deduct':'payout_member')]);
    $results[$type]['count_earnings'] = $results[$type]['count_earnings']+$_order_earning;
  }
  $results[$type]['count_'.$orders['status']] = $results[$type]['count_'.$orders['status']] + 1;
  $results[$type]['orders'][] = $orders;
  $results[$type]['time'] = $orders['time'];

}


?>
<link rel="stylesheet" type="text/css" href="template/assets/css/addons/datatables.min.css">
<link rel="stylesheet" href="template/assets/css/addons/datatables-select.min.css">
<script type="text/javascript" src="template/assets/js/addons/datatables.min.js"></script>
<script type="text/javascript" src="template/assets/js/addons/datatables-select.min.js"></script>

<link rel="stylesheet" href="template/assets/css/daterangepicker.css">
<script type="text/javascript" src="template/assets/js/daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="template/assets/js/daterangepicker/daterangepicker.min.js"></script>

<h2 class=" section-heading">Thống kê <?=($name_statistics ? $name_statistics : '');?> <a type="button" class="btn btn-success xem_tk">Xem thống kê</a></h2>

<section class="row mb-4">

  <!-- Grid row -->
  <!--<div class="auto-margin" style="overflow-x: scroll;white-space: nowrap;">

      <?php if($isType == "call" || $isType == "coll" || $isType == "admin") { ?>
        <p class="btn white btn-rounded noBorder font-weight-bold py-2 mx-2">Sales: <span class="text-dark"><?=addDotNumber($all_sales['sale']);?></span></p>
      <?php } ?>
        <p class="btn white btn-rounded noBorder font-weight-bold py-2 mx-2">Approve: <span class="text-dark"><?=addDotNumber($all_approved);?></span></p>

      <?php if($isType == "admin" && $coll == false) { ?>
        <p class="btn white btn-rounded noBorder font-weight-bold py-2 mx-2">Uncheck: <span class="text-dark"><?=addDotNumber($all_uncheck);?></span></p>
      <?php } ?>
      <?php if($isType == "call" || $isType == "admin"  && $coll == false) { ?>        
        <p class="btn white btn-rounded noBorder font-weight-bold py-2 mx-2">Calling: <span class="text-dark"><?=addDotNumber($all_calling);?></span></p>
      <?php } ?>
        <p class="btn white btn-rounded noBorder font-weight-bold py-2 mx-2">Pending: <span class="text-dark"><?=addDotNumber($all_pending);?></span></p>
        <p class="btn white btn-rounded noBorder font-weight-bold py-2 mx-2">Shipping: <span class="text-dark"><?=addDotNumber($all_shipping);?></span></p>
        <p class="btn white btn-rounded noBorder font-weight-bold py-2 mx-2">ShipDelay: <span class="text-dark"><?=addDotNumber($all_shipdelay);?></span></p>
      <?php if($isType == "call" || $isType == "admin"  && $coll == false) { ?>
        <p class="btn white btn-rounded noBorder font-weight-bold py-2 mx-2">CallError: <span class="text-dark"><?=addDotNumber($all_callerror);?></span></p>
        <p class="btn white btn-rounded noBorder font-weight-bold py-2 mx-2">Rejected: <span class="text-dark"><?=addDotNumber($all_rejected);?></span></p>
      <?php } ?>
        <p class="btn white btn-rounded noBorder font-weight-bold py-2 mx-2">ShipError: <span class="text-dark"><?=addDotNumber($all_shiperror);?></span></p>
      <?php if($isType == "call" || $isType == "admin"  && $coll == false) { ?>
        <p class="btn white btn-rounded noBorder font-weight-bold py-2 mx-2">Trashes: <span class="text-dark"><?=addDotNumber($all_trashed);?></span></p>
      <?php } ?>


  </div>-->
  <!-- Grid row -->
</section>

<section class="row mb-5 pb-3">
<?php 
  $data_rs = array(
    'name' => 'Sales -'.addDotNumber($all_sales['sale']),
    'name2' => 'Approve -'.addDotNumber($all_approved),      
    'name3' => 'Uncheck-'.addDotNumber($all_uncheck),
    'name4' =>  'Calling-'. addDotNumber($all_calling),          
    'name5' =>  'Pending-'. addDotNumber($all_pending),       
    'name6' =>  'Shipping-'. addDotNumber($all_shipping), 
    'name7' =>  'ShipDelay-'. addDotNumber($all_shipdelay),
    'name8' =>  'CallError-'. addDotNumber($all_callerror),
    'name9' =>  'Rejected-'. addDotNumber($all_rejected),
    'name10' =>  'ShipError-'. addDotNumber($all_shiperror),
    'name11' =>  'ShipFail-'. addDotNumber($all_shipfail),
    'name12' =>  'Trashes-'. addDotNumber($all_trashed),                              
  );  
?>
    <div class="col-md-12 mx-auto white z-depth-1" style="overflow-x: hidden;">
      <form name="filter" method="GET">
        <input name="route" value="statistics" type="hidden">
      <?php if($coll == true){ ?>
        <input name="coll" value="true" type="hidden">
      <?php } ?>
      <?php if($nav) { ?>
        <input name="nav" value="<?=$nav;?>" type="hidden">
      <?php } ?>
      <?php if($u) { ?>
        <input name="user" value="<?=$user['id'];?>" type="hidden">
      <?php } ?>
      <?php if($g) { ?>
        <input name="group" value="<?=$group['id'];?>" type="hidden">
      <?php } ?>
        <input name="ts" value="" type="hidden">
        <input name="te" value="" type="hidden">
      <div class="row mb-3">
        <div class="col-sx-12 col-md-2 pb20">
          <select role="filter-select" id="filter-offer" class="mdb-select" name="offer">
            <option value="all" selected>All Offer</option>
            <?php if($_group['offers'] || isAller()){                      
              foreach ($_offers as $of) {      
                $data1=$_db->query("select * from `core_orders` where `date` in ('".implode("','", $list_time)."') and `offer`='".$of['id']."' ".$sql." order by `time` asc ")->fetch_array();                            
                $_db->query("UPDATE `core_marks` SET `name`='".$of['name']."',`mark`='".count($data1)."' WHERE name='".$of['name']."'");                            
                if(preg_match("#\|".$of['id'].",#si", $_group['offers']) || isAller())
                  echo '<option value="'.$of['id'].'" '.($offer == $of['id'] ? 'selected' : '').'>'._e($of['name']).' <strong>('.count($data1).'</strong>)</option>';
              }
            }
			?>
          </select>
        </div>
        <div class="col-sx-12 col-md-2 pb20">
          <select role="filter-select" id="filter-view" class="mdb-select" name="view">
            <option value="date" selected>Xem theo ngày</option>
            <option value="hour" <?=($view == "hour" ? 'selected' :'');?>>Xem theo giờ</option>
          </select>
        </div>

        <div class="col-sx-12 col-md-2 pb20">
          <select role="filter-select" id="filter-status" class="mdb-select" multiple>
            <option selected disabled>Lọc trạng thái</option>
          <?php if($isType=='publisher' || $isType == "call" || $isType == "coll" || $isType == "admin") { ?>
            <option value="pre-sales" <?=(in_array('pre-sales', $filter_status) ? 'selected':'');?>>Pre Sales</option>
            <option value="sales" <?=(in_array('sales', $filter_status) ? 'selected':'');?>>Sales</option>
          <?php } ?>
            <option value="approved" <?=(in_array('approved', $filter_status) ? 'selected':'');?>>Approve</option>
          <?php if($isType=='publisher' || $isType == "admin" && $coll == false) { ?>
            <option value="uncheck" <?=(in_array('uncheck', $filter_status) ? 'selected':'');?>>UnCheck</option>
          <?php } ?>
          <?php if($isType=='publisher' || $isType == "call" || $isType == "admin" && $coll == false) { ?>
            <option value="calling" <?=(in_array('calling', $filter_status) ? 'selected':'');?>>Calling</option>
          <?php } ?>
            <option value="pending" <?=(in_array('pending', $filter_status) ? 'selected':'');?>>Pending</option>
            <option value="shipping" <?=(in_array('shipping', $filter_status) ? 'selected':'');?>>Shipping</option>
            <option value="shipdelay" <?=(in_array('shipdelay', $filter_status) ? 'selected':'');?>>ShipDelay</option>
          <?php if($isType=='publisher' || $isType == "call" || $isType == "admin" && $coll == false) { ?>
            <option value="callerror" <?=(in_array('callerror', $filter_status) ? 'selected':'');?>>CallError</option>
            <option value="rejected" <?=(in_array('rejected', $filter_status) ? 'selected':'');?>>Rejected</option>
          <?php } ?>
            <option value="shiperror" <?=(in_array('shiperror', $filter_status) ? 'selected':'');?>>ShipError</option>
            <option value="shipfail" <?=(in_array('shipfail', $filter_status) ? 'selected':'');?>>ShipFail</option>
          <?php if($isType=='publisher' || $isType == "call" || $isType == "admin" && $coll == false) { ?>
            <option value="trashed" <?=(in_array('trashed', $filter_status) ? 'selected':'');?>>Trashes</option>
          <?php } ?>
          </select>
        </div>


        <div class="col-sx-12 col-md-4 pb20">

          <span id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;width: 100%;display: block;margin-top: 5px;">
              <i class="fa fa-calendar"></i>&nbsp;
              <span></span>
              <i class="fa fa-caret-down"></i>
          </span>
        </div>
        <div class="col-sx-12 col-md-2 pb20">
          <button class="btn btn-primary waves-effect waves-light mx-3" type="submit">Apply Filter</button>
          <button class="btn btn-danger waves-effect waves-light" type="button" id="clear-filter">Clear</button>
        </div>
      </div>
    </form>       
    <style>
#chart-container {
    width: 100%;
}
</style>
<div id="chart-container" style="margin-bottom:15px;">
  <div class="row">
      <div class="col-md-6">
        <div class="panel panel-default" style="border:1px solid #f0645e;">
          <div class="panel-heading" style="background:#ff3547;height:40px;line-height:40px;color:#fff;padding-left:10px;font-weight:bold;">Thống kê Total Order</div>
          <div class="panel-body" style="padding:15px;">
            <canvas id="chart-student-ages"></canvas>
          </div>
        </div>        
      </div>
      <div class="col-md-6">
      <div class="panel panel-default" style="border:1px solid #04b2dc;">
          <div class="panel-heading" style="background:#04b2dc;height:40px;line-height:40px;color:#fff;padding-left:10px;font-weight:bold;">Thống kê Trạng thái Order</div>
          <div class="panel-body" style="padding:15px;">
          <canvas id="graphCanvas"></canvas>
          </div>
        </div>           
      </div>
  </div>        
</div>
    <script type="text/javascript" src="template/assets/js/canvasjs.min.js"></script>
    <?php 
    $data_mark = '[';
    $data_mark_count = '[';
    $data_mark_count_no = '[';    
    $grap=$_db->query("select * from `core_marks`")->fetch_array();    
    $gra_sum=$_db->query("select SUM(mark) as totalCount from `core_marks`order by id limit 1")->fetch();      
    foreach($grap as $item_grap){
      $tong = ($gra_sum["totalCount"]>0) ? round($item_grap['mark']*100/($gra_sum["totalCount"])) : 0;
      $data_mark.= '"'.$item_grap['name'].' ('.$tong.'%)",';      
      $data_mark_count.='"'.$item_grap['mark'].'đơn ('.$tong.'%)",';
      $data_mark_count_no.='"'.$item_grap['mark'].'",';      
    }    
    $data_mark.=']';
    $data_mark_count.=']';
    $data_mark_count_no.=']';    
    ?>
    <script>
        $(document).ready(function () {
          loadData(); 
          draw_students_ages_diagram();          
        });
        var sort_ages = <?php echo $data_mark_count_no; ?>;
        var sort_ages_callback = <?php echo $data_mark_count; ?>;
      function draw_students_ages_diagram() {
          var ctx = document.getElementById("chart-student-ages");
          var chart;
          var dataDraw = {
              'labels': <?php echo $data_mark; ?>,
              'values': sort_ages,
              'colors': ["#4bc0c0", "#ff817c", "#f0ad4e", "#8ed2ff","#c2c2a3"],
              'labelsCallback':sort_ages_callback
          };
          return drawMap(dataDraw, chart, ctx);
      }      
    function drawMap(dataDraw, chart, ctx) {
        var labels = dataDraw.labels;
        var newData = dataDraw.values;
        var allColor = dataDraw.colors;
        var labelsCallback = dataDraw.labelsCallback;
        var color = [];
        for (i = 0; i < labels.length; i++) {
            color[i] = allColor[i];
        }

        if (dataDraw.borderColors != undefined)
            var borderColors = dataDraw.borderColors;
        else
            var borderColors = color;

        data = {
            labels: labels,

            datasets: [
                {
                    data: newData,
                    hoverBorderWidth: [0, 0, 0],
                    backgroundColor: color,
                    hoverBackgroundColor: color
                }]
        };

        if (!chart === undefined)
            chart.destroy();

        var options = {
            tooltips: {
                callbacks: {
                    label: function (tooltipItem,data) {
                        return labelsCallback[tooltipItem.index];
                        //return data['labels'][tooltipItem['index']] + ': ' + data['datasets'][0]['data'][tooltipItem['index']] + '%';
                    }
                }
            }
        };


        chart = new Chart(ctx, {
            type: 'pie',
            data: data,
            options: options
        });

        return chart;
    }          
        function loadData(){
          $("#chart-container").css('display','none');
            $(".xem_tk").click(function(){
              $(this).addClass('collapse_read');  
              $(this).removeClass('xem_tk');             
              $("#chart-container").css('display','block').show(500);              
              showGraph();
              hiddenData();
            });    
          
        }

        function hiddenData(){          
          $(".collapse_read").click(function(){               
             $(this).removeClass('collapse_read');   
             $(this).addClass('xem_tk');            
             $("#chart-container").css('display','none');                            
              loadData(); 
            });  
              
        }

        function showGraph()
        {
        	var name = [];
            var marks = [];
            <?php 
              $tong = 0;
              foreach($data_rs as $item_r){
                $tach_mang = explode('-',$item_r);
                $tach_name = $tach_mang[0];
                $tach_mark = str_replace('.','',$tach_mang[1]);                  
                $tong+= $tach_mark;
                //$tong = round($tach_mark*100/($tong));
                ?>
                name.push('<?php echo $tach_name;?>');
                marks.push('<?php echo $tach_mark;?>');
                <?php
              }
            ?>                

            var chartdata = {
                labels: name,
                datasets: [
                    {
                        label: ['Thống kê'],
                        backgroundColor: [
                          "#f38b4a",
                          "#56d798",
                          "#ff8397",
                          "#6970d5" ,
                          "#F7D358",
                          "#FA58D0",
                          "#0404B4",
                          "#80FF00",
                          "#8A0808",
                          "#FF4000",
                          "#8258FA"
                        ],
                        borderColor: [
                          "rgb(255, 99, 132)",
                          "rgb(255, 159, 64)",
                          "rgb(255, 205, 86)",
                          "rgb(75, 192, 192)",
                          "rgb(54, 162, 235)",
                          "rgb(153, 102, 255)",
                          "rgb(201, 203, 207)"
                        ],
                        hoverBackgroundColor: [
                          "#A9A9F5",
                          "#F7819F",
                          "#140718",
                          "#2A0A29",
                          "#9FF781",
                          "#E2A9F3",
                          "#F5A9D0",
                          "#A9A9F5",
                          "#F78181",
                          "#2ECCFA",
                          "#81F7F3",
                        ],
                        hoverBorderColor: '#666666',
                        data: marks,
                        responsive:true,
                        maintainAspectRatio: false,
                    }
                    
                ]
            };

            var graphTarget = $("#graphCanvas");

            var barGraph = new Chart(graphTarget, {
                type: 'bar',
                data: chartdata,
                options: {  
                  tooltipTemplate: "<%= label %>: <%= value %>%",            
                  tooltips: {
                    callbacks: {
                      label: function(tooltipItem, data) {
                        return data['labels'][tooltipItem['index']] + ': ' + data['datasets'][0]['data'][tooltipItem['index']] + '';
                      }
                    }
                  }
                }
            });
        }
        </script>   
      <table id="dtBasicExample" class="table table-sm table-hover scrollbar scrollbar-black bordered-black" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th class="th-sm text-center" data-toggle="tooltip" title="Thời gian">Time</th>
          <?php if($isType == "call" || $isType == "coll" || $isType == "admin" || $isType == "publisher") { ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Tỉ lệ chấp nhận đơn hàng">APO (%)</th>
            <?php if($isType == "call" || $isType == "coll"){ ?>
              <th class="th-sm text-center" data-toggle="tooltip" title="Thu nhập ước tính">ESE (k)</th>
            <?php } elseif($isType != 'publisher') { ?>
              <th class="th-sm text-center" data-toggle="tooltip" title="Thu nhập bình quân mỗi đơn hàng">EPO (k)</th>
            <?php } ?>
            <?php if(in_array('pre-sales', $filter_status)){ ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Sản phẩm bán được ban đầu">Pre Sales</th>
            <?php }?>
            <?php if(in_array('sales', $filter_status)){ ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Sản phẩm bán được thực tế">Sales</th>
            <?php }?>
          <?php } ?>
            <?php if(in_array('approved', $filter_status)){ ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Giao thành công">Approve</th>
            <?php } ?>
          <?php if(($isType == "admin" && $coll == false) || $isType=='publisher') { ?>
            <?php if(in_array('uncheck', $filter_status)){ ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Đơn hàng mới">Uncheck</th>
            <?php } ?>
          <?php } ?>
          <?php if($isType=='publisher' || $isType == "call" || $isType == "admin"  && $coll == false) { ?>
            <?php if(in_array('calling', $filter_status)){ ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Đang gọi">Calling</th>
            <?php } ?>
          <?php } ?>
            <?php if(in_array('pending', $filter_status)){ ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Chờ giao hàng">Pending</th>
            <?php } ?>
            <?php if(in_array('shipping', $filter_status)){ ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Đang giao hàng">Shipping</th>
            <?php } ?>
            <?php if(in_array('shipdelay', $filter_status)){ ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Hẹn ngày giao hàng">ShipDelay</th>
            <?php } ?>
          <?php if($isType == "publisher" || $isType == "call" || $isType == "admin" && $coll == false) { ?>
            <!-- <th class="th-sm" data-toggle="tooltip" title="Hẹn gọi lại">Call Back</th> -->
            <?php if(in_array('callerror', $filter_status)){ ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Không gọi được">Call Error</th>
            <?php } ?>
            <?php if(in_array('rejected', $filter_status)){ ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Từ chối mua hàng">Rejected</th>
            <?php } ?>
          <?php } ?>
            <?php if(in_array('shiperror', $filter_status)){ ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Không nhận hàng">ShipError</th>
            <?php } ?>
            <?php if(in_array('shipfail', $filter_status)){ ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Phát không thành công">ShipFail</th>
            <?php } ?>
          <?php if($isType=='publisher' || $isType == "call" || $isType == "admin"  && $coll == false) { ?>
            <?php if(in_array('trashed', $filter_status)){ ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Đơn hàng rác">Trashes</th>
            <?php } ?>
          <?php } ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Tổng đơn hàng">Total Order</th>
          </tr>
        </thead>
        <tbody>
          <?php

            if($results){

              $total_presales = 0;
              $total_sales = 0;
              $total_earnings = 0;
              $total_approved = 0;
              $total_uncheck = 0;
              $total_calling = 0;
              //$total_callback = 0;
              $total_callerror = 0;
              $total_pending = 0;
              $total_shipping = 0;
              $total_shipdelay = 0;
              $total_shiperror = 0;
              $total_shipfail = 0;
              $total_rejected = 0;
              $total_trashed = 0;
              foreach ($results as $date => $arr) {

                $target_date = date('d/m/Y',$arr['time']);
                $count_apo = ($arr['count_approved']+$arr['count_pending']+$arr['count_shipping']+$arr['count_shipdelay']+$arr['count_shiperror']+$arr['count_shipfail']);
                $apo = $arr['count_total'] > 0 ? round($count_apo/$arr['count_total']*100,2) : 0;

                $epo = $arr['count_total'] > 0 ? addDotNumber(round($arr['count_earnings']/$arr['count_total'])) : 0;

                echo '<tr class="tr-statistic '.($date == date('Y/m/d',time()) ? 'grey lighten-2' :'').'">';

                echo '
                        <td class="text-center">
                          '.str_replace("/","-",$date).'
                        </td>';

                  if($isType == "call" || $isType == "coll" || $isType == "admin" || $isType == "publisher"){
                    echo  '<td class="text-center">
                          '.($apo > 0 ? '<b class="number">'.$apo.'</b>% (<small>'.$count_apo.'</small>)':'0').'
                        </td>';

                    if($isType != "publisher")
                        echo '<td class="text-center">
                          '.($epo > 0 ? '<b class="number">'.$epo.'</b>k':'0').'
                        </td>';

                    if(in_array('pre-sales', $filter_status))
                      echo '<td class="text-center">
                            '.($arr['count_presales'] > 0 ? '<b class="number">'.$arr['count_presales'].'</b>':'0').'
                          </td>';
                    if(in_array('sales', $filter_status))
                      echo '<td class="text-center">
                            '.($arr['count_sales'] > 0 ? '<b class="number">'.$arr['count_sales'].'</b>':'0').'
                          </td>';
                  }

                  if(in_array('approved', $filter_status))
                    echo  '<td class="text-center">
                          '.($view == "date" && $arr['count_approved'] > 0 ? '<a target="_blank" href="?route=order&type=approved&ts='.$target_date.'&te='.$target_date.'&view='.($isType == "admin" ? 'all&showall=true':'all').($user ? '&user='.$user['id'] : '').'">' : '' ).'
                          '.($arr['count_approved'] > 0 ? '<b class="number">'.$arr['count_approved'].'</b>':'0').'
                          '.($view == "date" && $arr['count_approved'] > 0 ? '</a>' : '' ).'
                        </td>';

                  if(in_array('uncheck', $filter_status)  && $coll == false)
                    echo ($isType == "admin"  || $isType == "publisher" ? '<td class="text-center">
                          '.($view == "date" && $arr['count_uncheck'] > 0 ? '<a target="_blank" href="?route=newOrder&ts='.$target_date.'&te='.$target_date.'&view='.($isType == "admin" ? 'all&showall=true':'all').($user ? '&user='.$user['id'] : '').'">' : '' ).'
                          '.($arr['count_uncheck'] > 0 ? '<b class="number">'.$arr['count_uncheck'].'</b>':'0').'
                          '.($view == "date" && $arr['count_uncheck'] > 0 ? '</a>' : '' ).'
                        </td>':'');

                  if($isType == "publisher" || $isType == "call" || $isType == "admin"  && $coll == false){
                    if(in_array('calling', $filter_status))
                      echo '<td class="text-center">
                          '.($view == "date" && $arr['count_calling'] > 0 ? '<a target="_blank" href="?route=allCalling&ts='.$target_date.'&te='.$target_date.'&view='.($isType == "admin" ? 'all&showall=true':'all').($user ? '&user='.$user['id'] : '').'">' : '' ).'
                          '.($arr['count_calling'] > 0 ? '<b class="number">'.$arr['count_calling'].'</b>':'0').'
                          '.($view == "date" && $arr['count_calling'] > 0 ? '</a>' : '' ).'
                        </td>';
                  }

                  if(in_array('pending', $filter_status))
                    echo '<td class="text-center">
                          '.($view == "date" && $arr['count_pending'] > 0 ? '<a target="_blank" href="?route=order&type=pending&ts='.$target_date.'&te='.$target_date.'&view='.($isType == "admin" ? 'all&showall=true':'all').($user ? '&user='.$user['id'] : '').'">' : '' ).'
                          '.($arr['count_pending'] > 0 ? '<b class="number">'.$arr['count_pending'].'</b>':'0').'
                          '.($view == "date" && $arr['count_pending'] > 0 ? '</a>' : '' ).'
                        </td>';

                  if(in_array('shipping', $filter_status))
                    echo '<td class="text-center">
                          '.($view == "date" && $arr['count_shipping'] > 0 ? '<a target="_blank" href="?route=order&type=shipping&ts='.$target_date.'&te='.$target_date.'&view='.($isType == "admin" ? 'all&showall=true':'all').($user ? '&user='.$user['id'] : '').'">' : '' ).'
                          '.($arr['count_shipping'] > 0 ? '<b class="number">'.$arr['count_shipping'].'</b>':'0').'
                          '.($view == "date" && $arr['count_shipping'] > 0 ? '</a>' : '' ).'
                        </td>';
                  if(in_array('shipdelay', $filter_status))
                    echo '<td class="text-center">
                          '.($view == "date" && $arr['count_shipdelay'] > 0 ? '<a target="_blank" href="?route=order&type=shipDelay&ts='.$target_date.'&te='.$target_date.'&view='.($isType == "admin" ? 'all&showall=true':'all').($user ? '&user='.$user['id'] : '').'">' : '' ).'
                          '.($arr['count_shipdelay'] > 0 ? '<b class="number">'.$arr['count_shipdelay'].'</b>':'0').'
                          '.($view == "date" && $arr['count_shipdelay'] > 0 ? '</a>' : '' ).'
                        </td>';
                  if($isType == "publisher" || $isType == "call" || $isType == "admin"  && $coll == false){
                    /*<td class="text-center">
                          '.($arr['count_callback'] > 0 ? '<b class="number">'.$arr['count_callback'].'</b>':'0').'
                        </td>*/

                    if(in_array('callerror', $filter_status))
                      echo ' <td class="text-center">
                          '.($view == "date" && $arr['count_callerror'] > 0 ? '<a target="_blank" href="?route=order&type=callError&ts='.$target_date.'&te='.$target_date.'&view='.($isType == "admin" ? 'all&showall=true':'all').($user ? '&user='.$user['id'] : '').'">' : '' ).'
                          '.($arr['count_callerror'] > 0 ? '<b class="number">'.$arr['count_callerror'].'</b>':'0').'
                          '.($view == "date" && $arr['count_callerror'] > 0 ? '</a>' : '' ).'
                        </td>';
                    if(in_array('rejected', $filter_status))
                      echo '<td class="text-center">
                          '.($view == "date" && $arr['count_rejected'] > 0 ? '<a target="_blank" href="?route=order&type=rejected&ts='.$target_date.'&te='.$target_date.'&view='.($isType == "admin" ? 'all&showall=true':'all').($user ? '&user='.$user['id'] : '').'">' : '' ).'
                          '.($arr['count_rejected'] > 0 ? '<b class="number">'.$arr['count_rejected'].'</b>':'0').'
                          '.($view == "date" && $arr['count_rejected'] > 0 ? '</a>' : '' ).'
                        </td>';
                  }

                  if(in_array('shiperror', $filter_status)){
                    echo '<td class="text-center">
                          '.($view == "date" && $arr['count_shiperror'] > 0 ? '<a target="_blank" href="?route=order&type=shipError&ts='.$target_date.'&te='.$target_date.'&view='.($isType == "admin" ? 'all&showall=true':'all').($user ? '&user='.$user['id'] : '').'">' : '' ).'
                          '.($arr['count_shiperror'] > 0 ? '<b class="number">'.$arr['count_shiperror'].'</b>':'0').'
                          '.($view == "date" && $arr['count_shiperror'] > 0 ? '</a>' : '' ).'
                        </td>';
                  }
                  if(in_array('shipfail', $filter_status)){
                    echo '<td class="text-center">
                          '.($view == "date" && $arr['count_shipfail'] > 0 ? '<a target="_blank" href="?route=order&type=shipfail&ts='.$target_date.'&te='.$target_date.'&view='.($isType == "admin" ? 'all&showall=true':'all').($user ? '&user='.$user['id'] : '').'">' : '' ).'
                          '.($arr['count_shipfail'] > 0 ? '<b class="number">'.$arr['count_shipfail'].'</b>':'0').'
                          '.($view == "date" && $arr['count_shipfail'] > 0 ? '</a>' : '' ).'
                        </td>';
                  }
                  if($isType == "publisher" || $isType == "call" || $isType == "admin"  && $coll == false){
                    if(in_array('trashed', $filter_status))
                      echo '<td class="text-center">
                          '.($view == "date" && $arr['count_trashed'] > 0 ? '<a target="_blank" href="?route=order&type=trashed&ts='.$target_date.'&te='.$target_date.'&view='.($isType == "admin" ? 'all&showall=true':'all').($user ? '&user='.$user['id'] : '').'">' : '' ).'
                          '.($arr['count_trashed'] > 0 ? '<b class="number">'.$arr['count_trashed'].'</b>':'0').'
                          '.($view == "date" && $arr['count_trashed'] > 0 ? '</a>' : '' ).'
                        </td>';
                  }
                    echo '<td class="text-center">
                          '.($arr['count_total'] > 0 ? '<b class="number">'.$arr['count_total'].'</b>':'0').'
                        </td>
                      </tr>';

                  $total_presales = $total_presales+$arr['count_presales'];
                  $total_sales = $total_sales+$arr['count_sales'];
                  $total_earnings = $total_earnings+$arr['count_earnings'];
                  $total_approved = $total_approved+$arr['count_approved'];
                  $total_uncheck = $total_uncheck+$arr['count_uncheck'];
                  $total_calling = $total_calling+$arr['count_calling'];
                  //$total_callback = $total_callback+$arr['count_callback'];
                  $total_callerror = $total_callerror+$arr['count_callerror'];
                  $total_pending = $total_pending+$arr['count_pending'];
                  $total_shipping = $total_shipping+$arr['count_shipping'];
                  $total_shipdelay = $total_shipdelay+$arr['count_shipdelay'];
                  $total_shiperror = $total_shiperror+$arr['count_shiperror'];
                  $total_shipfail = $total_shipfail+$arr['count_shipfail'];
                  $total_rejected = $total_rejected+$arr['count_rejected'];
                  $total_trashed = $total_trashed+$arr['count_trashed'];
              }

            }

          ?>
        </tbody>
        <tfoot>
          <?php

          	$total_count_apo = ($total_approved+$total_pending+$total_shipping+$total_shipdelay+$total_shiperror+$total_shipfail);
            $total_apo = count($_data) > 0 ? round($total_count_apo/count($_data)*100,2) : 0;

            // if($isType == "admin")
              $total_epo = count($_data) > 0 ? addDotNumber(round($total_earnings/count($_data))) : 0;
            // else
            //   $total_epo = addDotNumber($total_earnings);

          ?>
          <tr>
            <th class="th-sm text-center" data-toggle="tooltip" title="Tổng">Total</th>
          <?php if($isType == "call" || $isType == "coll" || $isType == "admin" || $isType == "publisher") { ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Tỉ lệ chấp nhận đơn hàng"><?=($total_apo > 0 ? '<b class="number">'.$total_apo.'</b>% (<small>'.$total_count_apo.'</small>)':'0');?></th>
            <?php if($isType != "publisher"): ?>
                <th class="th-sm text-center" data-toggle="tooltip" title="Thu nhập bình quân mỗi đơn hàng"><?=($total_epo > 0 ? '<b class="number">'.$total_epo.'</b>k':'0');?></th>
            <?php endif; ?>
            <?php if(in_array('pre-sales', $filter_status)){ ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Sản phẩm bán được"><?=($total_presales > 0 ? '<b class="number">'.$total_presales.'</b>':'0');?></th>
            <?php } ?>
            <?php if(in_array('sales', $filter_status)){ ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Sản phẩm bán được"><?=($total_sales > 0 ? '<b class="number">'.$total_sales.'</b>':'0');?></th>
            <?php } ?>
          <?php } ?>
            <?php if(in_array('approved', $filter_status)){ ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Giao thành công"><?=($total_approved > 0 ? '<b class="number">'.$total_approved.'</b>':'0');?></th>
            <?php } ?>
          <?php if(($isType == "admin" && $coll == false) || $isType == "publisher") { ?>
            <?php if(in_array('uncheck', $filter_status)){ ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Đơn hàng mới"><?=($total_uncheck > 0 ? '<b class="number">'.$total_uncheck.'</b>':'0');?></th>
            <?php } ?>
          <?php } ?>
          <?php if($isType == "publisher" || $isType == "call" || $isType == "admin" && $coll == false) { ?>
            <?php if(in_array('calling', $filter_status)){ ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Đang gọi"><?=($total_calling > 0 ? '<b class="number">'.$total_calling.'</b>':'0');?></th>
            <?php } ?>
          <?php } ?>
            <?php if(in_array('pending', $filter_status)){ ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Chờ giao hàng"><?=($total_pending > 0 ? '<b class="number">'.$total_pending.'</b>':'0');?></th>
            <?php } ?>
            <?php if(in_array('shipping', $filter_status)){ ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Đang giao hàng"><?=($total_shipping > 0 ? '<b class="number">'.$total_shipping.'</b>':'0');?></th>
            <?php } ?>
            <?php if(in_array('shipdelay', $filter_status)){ ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Hẹn ngày giao hàng"><?=($total_shipdelay > 0 ? '<b class="number">'.$total_shipdelay.'</b>':'0');?></th>
            <?php } ?>
          <?php if($isType == "publisher" || $isType == "call" || $isType == "admin" && $coll == false) { ?>
            <!-- <th class="th-sm text-center" data-toggle="tooltip" title="Hẹn gọi lại"><?=($total_callback > 0 ? '<b class="number">'.$total_callback.'</b>':'0');?></th> -->
            <?php if(in_array('callerror', $filter_status)){ ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Không gọi được"><?=($total_callerror > 0 ? '<b class="number">'.$total_callerror.'</b>':'0');?></th>
            <?php } ?>
            <?php if(in_array('rejected', $filter_status)){ ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Từ chối mua hàng"><?=($total_rejected > 0 ? '<b class="number">'.$total_rejected.'</b>':'0');?></th>
            <?php } ?>
          <?php } ?>
            <?php if(in_array('shiperror', $filter_status)){ ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Không nhận hàng"><?=($total_shiperror > 0 ? '<b class="number">'.$total_shiperror.'</b>':'0');?></th>
            <?php } ?>
            <?php if(in_array('shipfail', $filter_status)){ ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Không nhận hàng"><?=($total_shipfail > 0 ? '<b class="number">'.$total_shipfail.'</b>':'0');?></th>
            <?php } ?>
          <?php if($isType == "publisher" || $isType == "call" || $isType == "admin" && $coll == false) { ?>
            <?php if(in_array('trashed', $filter_status)){ ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Đơn hàng rác"><?=($total_trashed > 0 ? '<b class="number">'.$total_trashed.'</b>':'0');?></th>
            <?php } ?>
          <?php } ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Tổng đơn hàng"><?=(count($_data) > 0 ? '<b class="number">'.count($_data).'</b>':'0');?></th>
          </tr>
        </tfoot>
      </table>

    </div>

</section>

<div class="loader-overlay">
  <div class="loader-content-container">
    <div class="loader-content">
      <div class="spinner-grow" role="status" style="width: 6rem; height: 6rem;">
        <span class="sr-only">Loading...</span>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
  $(document).ready(function(){



      $('#dtBasicExample').DataTable({
        language: {emptyTable: "Không có số liệu thống kê."},
        info: false,
        paging: true,
        pageLength: <?=(isset($_COOKIE['page_entries']) && in_array($_COOKIE['page_entries'], [ 10, 25, 50,100,250,500 ]) ? $_COOKIE['page_entries'] :100 );?>,
        lengthMenu: [
            [ 10, 25, 50,100,250,500 ],
            [ '10', '25', '50','100','250','500' ]
        ],
        sDom: 'Rfrtlip',
        scrollX: true,
        scrollY: <?=(count($results) > 10 ? '300' : 'true');?>,
        searching: false,
        order: [ 0, 'asc' ],
        responsive: false
      });
      $('.dataTables_length').addClass('bs-select');

      $("#dtBasicExample_length").on("change",function(){
        setCookie("page_entries",$(this).find("select").val(),365);
      });
  });
</script>

<script type="text/javascript">
$(function() {

    var start = '<?=$ts;?>';
    var end = '<?=$te;?>';

    function cb(start, end) {
        $('#reportrange span').html(start.format('DD/MM/YYYY')+ ' - ' + end.format('DD/MM/YYYY'));
        $("form[name=filter] input[name=ts]").val(start.format('DD/MM/YYYY'));
        $("form[name=filter] input[name=te]").val(end.format('DD/MM/YYYY'));
    }

    $('#reportrange').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
           'Hôm nay': [moment(), moment()],
           'Hôm qua': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           '7 ngày gần nhất': [moment().subtract(6, 'days'), moment()],
           '30 ngày gần nhất': [moment().subtract(29, 'days'), moment()],
           'Tháng này': [moment().startOf('month'), moment().endOf('month')],
           'Tháng trước': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        locale: {
            "format": "DD/MM/YYYY",
            "separator": " - ",
            "applyLabel": "Áp dụng",
            "cancelLabel": "Đặt lại",
            "fromLabel": "Từ ngày",
            "toLabel": "Đến ngày",
            "customRangeLabel": "Tùy chỉnh ngày",
            "weekLabel": "Tuần",
            "daysOfWeek": [
                "CN",
                "T2",
                "T3",
                "T4",
                "T5",
                "T6",
                "T7"
            ],
            "monthNames": [
                "Tháng 1",
                "Tháng 2",
                "Tháng 3",
                "Tháng 4",
                "Tháng 5",
                "Tháng 6",
                "Tháng 7",
                "Tháng 8",
                "Tháng 9",
                "Tháng 10",
                "Tháng 11",
                "Tháng 12"
            ],
            "firstDay": 1
        },
        showDropdowns: true,
        alwaysShowCalendars: true,
        linkedCalendars: false,
        autoApply: false,
        autoUpdateInput: true
    }, cb);


    $('#reportrange span').html(start+ ' - ' + end);
    $("form[name=filter] input[name=ts]").val(start);
    $("form[name=filter] input[name=te]").val(end);


    $("#clear-filter").on("click",function(){
      $('#reportrange span').html(moment().subtract(6, 'days').format('DD/MM/YYYY')+ ' - ' + moment().format('DD/MM/YYYY'));
      $('#reportrange').data('daterangepicker').setStartDate(moment().subtract(6, 'days').format('DD/MM/YYYY'));
      $('#reportrange').data('daterangepicker').setEndDate(moment().format('DD/MM/YYYY'));
      $("form[name=filter] input[name=ts]").val('');
      $("form[name=filter] input[name=te]").val('');
      $("#filter-offer").val('all');
      $("#filter-view").val('date');
      $('#filter-status option').prop('selected', true);
      $("[role=filter-select]").materialSelect();

      var status = ['sales','approved','uncheck','calling','pending','shipping','shipdelay','callerror','rejected','shiperror','shipfail','trashed'];
      setCookie("statistics_status",status.join(","),365);


    });

  $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
      $("form[name=filter] input[name=ts]").val(picker.startDate.format('DD/MM/YYYY'));
      $("form[name=filter] input[name=te]").val(picker.endDate.format('DD/MM/YYYY'));
  });

  $('#reportrange').on('cancel.daterangepicker', function(ev, picker) {
      $('#reportrange span').html(moment().subtract(6, 'days').format('DD/MM/YYYY')+ ' - ' + moment().format('DD/MM/YYYY'));
      $('#reportrange').data('daterangepicker').setStartDate(moment().subtract(6, 'days').format('DD/MM/YYYY'));
      $('#reportrange').data('daterangepicker').setEndDate(moment().format('DD/MM/YYYY'));
  });


  $("#filter-status").on("change",function(){

    setCookie("statistics_status",$(this).val().join(","),365);

  });
});
</script>
<?php


end:


?>
