<?php
$_status = 'callerror';
$offer = isset($_GET['offer']) ? trim($_GET['offer']) : (isset($_COOKIE[$_status.'_offer']) ? $_COOKIE[$_status.'_offer'] : 'all');
$ts = isset($_GET['ts']) && !empty($_GET['ts']) ? $_GET['ts'] :  date('d/m/Y',strtotime('-29 days GMT+7 00:00'));
$te = isset($_GET['te']) && !empty($_GET['te']) ? $_GET['te'] :  date('d/m/Y',time());
$view = isset($_GET['view']) && in_array($_GET['view'],['me','all','group']) ? $_GET['view'] : (isset($_COOKIE[$_status.'_view']) ? $_COOKIE[$_status.'_view'] : (isAller() ? 'all' : 'group'));
$user = isset($_GET['user']) ? $_GET['user'] : '';
if(((!isLeader() && $view == "all") && !isAller()) || ((!isLeader() && $view == "group") && !isAller())){
    $view = "me";
}
else if((isLeader() && $view == "all") && !isAller()){
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
$_offers = getOffer();
$sql = "";
if($offer != "all"){
    $sql = " and `offer`='".escape_string($offer)."' ";
}























if(isCaller() || isColler()){















    if($view == "group")







    $sql .= " and `group`='".$_user['group']."' ";















    if($view == "me")







    $sql .= " and `user_call`='".$_user['id']."' ";







    else if($user)







    $sql .= " and `user_call`='".escape_string($user)."' ";















} else {







    $memGroup = memberGroup($_group['id']);







    $user_ship = array();







    foreach ($memGroup as $us)







    $user_ship[] = $us['id'];















    if($view == "group")







    $sql .= " and `user_ship` in ('".implode("','", $user_ship)."') ";















    if($view == "me")







    $sql .= " and `user_ship`='".$_user['id']."' ";







    else if($user)







    $sql .= " and `user_ship`='".escape_string($user)."' ";















}

if(isset($_POST['column']) && isset($_POST['sortOrder'])){
    $columnName = strip_tags(str_replace("","_",strtolower($_POST['column'])));
    $sortOrder  = $_POST['sortOrder'];
}
if(isset($_POST['limit'])){
    $perPage = $_POST['limit'];
}else{
    $perPage = 10;
}
$_data = $_db->query("select * from `core_orders` where `status` = '".$_status."' and (`time` >= '".(strtotime(str_replace("/","-", $ts)." GMT+7 00:00"))."' and `time` < '".(strtotime(str_replace("/","-", $te)." GMT+7 23:59"))."') ".$_sql_offer." ".$sql." order by id DESC")->fetch_array();
$result = $_db->query("select * from `core_orders` where `id` > '".$_POST['last_video_id']."' and `status` = '".$_status."' and (`time` >= '".(strtotime(str_replace("/","-", $ts)." GMT+7 00:00"))."' and `time` < '".(strtotime(str_replace("/","-", $te)." GMT+7 23:59"))."') ".$_sql_offer." ".$sql." order by $columnName $sortOrder LIMIT $perPage")->fetch_array();
$paginationHtml = '';
$isCaller = isCaller() || isColler() ? true: false;






$isShipper = isShipper() ? true: false;    







$sql_check = array();







foreach ($_data as $arr){







    $sql_check[] = " ( `order_phone` like '".$arr['order_phone']."' and `id`!='".$arr['id']."') ";







}







$sql_check = $sql_check ? ' where '.implode(" or ", $sql_check) : '';







$checkOrder = checkOrders($sql_check);    



foreach ($result as $row) {   



    $video_id = $row['id'];



    $check = isset($checkOrder[$row['order_phone']]) ?  $checkOrder[$row['order_phone']] : '';







    $caller = getUser($row['user_call']);







    $shipper = getUser($row['user_ship']);        







    $paginationHtml.='<tr data-id='.$row["id"].'>';  







    $paginationHtml.='<td class="text-center"><a href="?route=editOrder&id='.$row['id'].'"><span class="btn '.getBgOrder($_status).' btn-sm waves-effect waves-light"><i class="w-fa fas fa-edit ml-1"></i></span></a></td>';







    $paginationHtml.='<td>Đơn hàng #<b>'.$row['id'].'</b></td>';







    $paginationHtml.='<td class="text-center"><b>'._e($row['offer_name']).'</b></td>';







    $paginationHtml.='<td class="text-center">'.date('Y/m/d H:i',$row['time']).'</td>';







    $paginationHtml.='<td class="text-center">'._ucwords($row['order_name']).'</td>';







    $paginationHtml.='<td class="text-center">'._e($row['order_phone']).'';







    if($check){







        foreach ($check as $dup) {
            $data_dup = $_db->query("select * from `core_orders` where `id` = '".$dup['id']."' ")->fetch();
            $caller_dup = getUser($data_dup['user_call']);
            $paginationHtml.='<br><small>- '.$caller_dup['name'].' #<a target="_blank" href="?route=editOrder&id='.$dup['id'].'"><b class="text-danger">'.$dup['id'].' ('.$dup['status'].')</b></a></small>';







        }







    }      







    $paginationHtml.='<td class="text-center">'.get_time($row['call_time']).'</td>';    







    $paginationHtml.='<td class="text-left">';        



    $paginationHtml.=nl2br(_ucwords($row['note']));



    $paginationHtml.='</td>';



    if(isLeader() || isAller()){







        $paginationHtml.='<td class="text-center">';







        if($caller){







            $paginationHtml.= '<div class="chip align-middle">







                            <a target="_blank" href="?route=statistics&user='.$caller['id'].'">







                            <img src="'.getAvatar($caller['id']).'"> '.(!isBanned($caller) ? _e($caller['name']) : '<strike class="text-dark"><b>'._e($caller['name']).'</b></strike>').'







                            </a>







                        </div>';







        }







        $paginationHtml.='</td>';







    }            



    $paginationHtml.='</tr>';            



} 



echo $paginationHtml;



?>