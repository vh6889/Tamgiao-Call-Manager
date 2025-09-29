<?php 
$offer = isset($_GET['offer']) ? trim($_GET['offer']) : (isset($_COOKIE['allcalling_offer']) ? $_COOKIE['allcalling_offer'] : 'all');
$ts = isset($_GET['ts']) && !empty($_GET['ts']) ? $_GET['ts'] :  date('d/m/Y',strtotime('-29 days GMT+7 00:00'));
$te = isset($_GET['te']) && !empty($_GET['te']) ? $_GET['te'] :  date('d/m/Y',time());
$view = isset($_GET['view']) && $_GET['view'] == "group" ? "group" : 'all';
if(!isAller()){
  $view = "group";
}
if(isset($_GET['offer']) && $_GET['offer']){
  setcookie("allcalling_offer",$offer,time()+3600*24*365);
}
if(isset($_GET['view']) && $_GET['view']){
  setcookie("allcalling_view",$view,time()+3600*24*365);
}
if(isset($_GET['offer']) && !$_GET['offer']){
  setcookie("allcalling_offer","");
}
if(isset($_GET['view']) && !$_GET['view']){
  setcookie("allcalling_view","");
}
$_group = getGroup($_user['group']);
$_offers = getOffer();

$sql = "";
if($offer != "all"){
  $sql = " and `offer`='".escape_string($offer)."' ";
}
if($view == "group"){
  $sql .= " and `group`='".$_user['group']."' ";
}
if(isset($_POST['limit'])){
  $perPage = $_POST['limit'];
}else{
  $perPage = 10;
}
$result = $_db->query("select * from `core_orders` where `status` = 'calling' ".$sql." and (`time` >= '".(strtotime(str_replace("/","-", $ts)." GMT+7 00:00"))."' and `time` < '".(strtotime(str_replace("/","-", $te)." GMT+7 23:59"))."') and `user_call`!='' and `user_call`!='".$_user['id']."' ".$_sql_offer." order by id DESC LIMIT $perPage")->fetch_array();
$paginationHtml = '';
$sql_check = array();
foreach ($_data as $arr){
    $sql_check[] = " ( `order_phone` like '".$arr['order_phone']."' and `id`!='".$arr['id']."') ";
}
$sql_check = $sql_check ? ' where '.implode(" or ", $sql_check) : '';
$checkOrder = checkOrders($sql_check);
foreach ($result as $arr) {
    $check = isset($checkOrder[$arr['order_phone']]) ?  $checkOrder[$arr['order_phone']] : '';
    $caller = getUser($arr['user_call']);
    $paginationHtml.= '<tr data-id="'.$arr['id'].'">';
    $paginationHtml.= '<td class="text-center"><a href="?route=editOrder&id='.$arr['id'].'"><span class="btn '.getBgOrder('calling').' btn-sm waves-effect waves-light"><i class="w-fa fas fa-edit ml-1"></i></span></a></td>';
    $paginationHtml.= '<td class="text-center">Đơn hàng #<b>'.$arr['id'].'</b></td>';
    $paginationHtml.= '<td class="text-center"><b>'._e($arr['offer_name']).'</b></td>';
    $paginationHtml.= '<td class="text-center">'.date('Y/m/d H:i',$arr['time']).'</td>';
    $paginationHtml.= '<td class="text-center">'._ucwords($arr['order_name']).'';
    $paginationHtml.= '<td class="text-center">'._e($arr['order_phone']).'';
    if($check){
      foreach ($check as $dup) {
        $data_dup = $_db->query("select * from `core_orders` where `id` = '".$dup['id']."' ")->fetch();
        $caller_dup = getUser($data_dup['user_call']);
        $paginationHtml.= '<br><small>- '.$caller_dup['name'].' #<a target="_blank" href="?route=editOrder&id='.$dup['id'].'"><b class="text-danger">'.$dup['id'].' ('.$dup['status'].')</b></a></small>';
      }
    }
    $paginationHtml.= '</td>';

    $paginationHtml.= '<td class="text-center">';
    $paginationHtml.= '<div class="chip align-middle"><a target="_blank" href="?route=statistics&user='.$caller['id'].'"><img src="'.getAvatar($caller['id']).'"> '.(!isBanned($caller) ? _e($caller['name']) : '<strike class="text-dark"><b>'._e($caller['name']).'</b></strike>').'</a></div>';
    $paginationHtml.= '</td>';
    $paginationHtml.= '<td class="text-center"><b>'.get_time($arr['call_time']).'</b></td>';
    $paginationHtml.= '</tr>';
}
echo $paginationHtml; 
?>