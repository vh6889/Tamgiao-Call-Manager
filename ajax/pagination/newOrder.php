<?php 
$offer = isset($_GET['offer']) ? trim($_GET['offer']) : (isset($_COOKIE['uncheck_offer']) ? $_COOKIE['uncheck_offer'] : 'all');
$ts = isset($_GET['ts']) && !empty($_GET['ts']) ? $_GET['ts'] :  date('d/m/Y',strtotime('-29 days GMT+7 00:00'));
$te = isset($_GET['te']) && !empty($_GET['te']) ? $_GET['te'] :  date('d/m/Y',time());
if(isset($_GET['offer']) && $_GET['offer'])
  setcookie("uncheck_offer",$offer,time()+3600*24*365);
if(isset($_GET['offer']) && !$_GET['offer'])
  setcookie("uncheck_offer","");
if(isShipper() && !isAller())
  autoBan();
$_calling = getCalling();
$_group = getGroup($_user['group']);
$_offers = getOffer();

$sql = "";
if($offer != "all")
  $sql = " and `offer`='".escape_string($offer)."' ";
if(isset($_POST['limit'])){
  $perPage = $_POST['limit'];
}else{
  $perPage = 10;
}
$result = $_db->query("select * from `core_orders` where `status` = 'uncheck' ".$sql." and (`time` >= '".(strtotime(str_replace("/","-", $ts)." GMT+7 00:00"))."' and `time` < '".(strtotime(str_replace("/","-", $te)." GMT+7 23:59"))."') and `user_call`=''".$_sql_offer." order by id DESC LIMIT $perPage")->fetch_array();
$paginationHtml = '';
$sql_check = [];
foreach ($result as $arr){
  $sql_check[] = " ( `order_phone` like '".$arr['order_phone']."' and `id`!='".$arr['id']."') ";
}
$sql_check = $sql_check ? ' where '.implode(" or ", $sql_check) : '';
$checkOrder = checkOrders($sql_check);
foreach ($result as $row) {   
    $check = isset($checkOrder[$row['order_phone']]) ?  $checkOrder[$row['order_phone']] : '';     
    $paginationHtml.= '<tr data-id="'.$row['id'].'">';
    if($_calling)
      $paginationHtml.= '<td class="text-center">'.(isAller() ? '<input type="checkbox" class="form-check-input" id="order_'.$row['id'].'" value="'.$row['id'].'">
            <label class="form-check-label px-3" for="order_'.$row['id'].'"></label>':'').'<button role="btn-calling" class="btn '.getBgOrder('uncheck').' btn-sm btn-rounded waves-effect waves-light"  disabled><i class="w-fa fas fa-phone-volume ml-1"></i> Get Order</button></td>';
    else
      $paginationHtml.= '<td class="text-center">'.(isAller() ? '<input type="checkbox" class="form-check-input" id="order_'.$row['id'].'" value="'.$row['id'].'">
            <label class="form-check-label px-3" for="order_'.$row['id'].'"></label>':'').'<button role="btn-calling" class="btn '.getBgOrder('uncheck').' btn-sm btn-rounded waves-effect waves-light"><i class="w-fa fas fa-phone-volume ml-1"></i> Get Order</button></td>';
    $paginationHtml.= '<td class="text-center">Đơn hàng #<b>'.$row['id'].'</b></td>';
    $paginationHtml.= '<td class="text-center"><b>'._e($row['offer_name']).'</b></td>';
    $paginationHtml.= '<td class="text-center">'.date('Y/m/d H:i',$row['time']).'</td>';
    if(isAdmin()){
      $paginationHtml.= '<td class="text-center">'.$arr['landing'].'</td>';  
    }
    $paginationHtml.= '<td class="text-center">'._ucwords($row['order_name']).'</td>';    
    $paginationHtml.= '<td class="text-left">';
    if($check){
      foreach ($check as $dup) {
        $data_dup = $_db->query("select * from `core_orders` where `id` = '".$dup['id']."' ")->fetch();
        $caller_dup = getUser($data_dup['user_call']);
        $paginationHtml.= '<p class="my-1"><small>- '.$caller_dup['name'].' #<a target="_blank" href="?route=editOrder&id='.$dup['id'].'"><b class="text-danger">'.$dup['id'].' ('.$dup['status'].')</b></a></small></p>';
      }
    }
    $paginationHtml.= '</td>';

    $paginationHtml.= '</tr>';
}
echo $paginationHtml; 
?>