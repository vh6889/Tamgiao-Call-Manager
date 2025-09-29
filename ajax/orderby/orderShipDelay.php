<?php



$_status = 'shipdelay';



$offer = isset($_GET['offer']) ? trim($_GET['offer']) : (isset($_COOKIE[$_status.'_offer']) ? $_COOKIE[$_status.'_offer'] : 'all');



$ts = isset($_GET['ts']) && !empty($_GET['ts']) ? $_GET['ts'] :  date('d/m/Y',strtotime('-29 days GMT+7 00:00'));



$te = isset($_GET['te']) && !empty($_GET['te']) ? $_GET['te'] :  date('d/m/Y',time());



$view = isset($_GET['view']) && in_array($_GET['view'],['me','all','group']) ? $_GET['view'] : (isset($_COOKIE[$_status.'_view']) ? $_COOKIE[$_status.'_view'] : (isAller() ? 'all' : 'group'));



$showall = isset($_GET['showall']) ? $_GET['showall'] :false;



$user = isset($_GET['user']) ? $_GET['user'] : '';



if(((!isLeader() && $view == "all") && !isAller()) || ((!isLeader() && $view == "group") && !isAller()))



  $view = "me";



else if((isLeader() && $view == "all") && !isAller())



  $view = "group";



if(isset($_GET['offer']) && $_GET['offer'])



  setcookie($_status.'_offer',$offer,time()+3600*24*365);



if(isset($_GET['view']) && $_GET['view'])



  setcookie($_status.'_view',$view,time()+3600*24*365);



if(isset($_GET['offer']) && !$_GET['offer'])



  setcookie($_status.'_offer',"");



if(isset($_GET['view']) && !$_GET['view'] || $_GET['view'] == "me")



  setcookie($_status.'_view',"");



$_group = getGroup($_user['group']);



$_offers = getOffer();



$sql = "";



if($offer != "all")



  $sql = " and `offer`='".escape_string($offer)."' ";



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

if(isAller()){
  $result = $_db->query("select * from `core_orders` where `status` = '".$_status."' ".$sql." and (`time` >= '".(strtotime(str_replace("/","-", $ts)." GMT+7 00:00"))."' and `time` < '".(strtotime(str_replace("/","-", $te)." GMT+7 23:59"))."') ".($showall == false && !$user ? " and `user_ship`!='' ":"")." ".$_sql_offer." ".$sql." order by $columnName $sortOrder LIMIT $perPage")->fetch_array();
}
else{
  $result = $_db->query("select * from `core_orders` where `status` = '".$_status."' ".$sql." and (`time` >= '".(strtotime(str_replace("/","-", $ts)." GMT+7 00:00"))."' and `time` < '".(strtotime(str_replace("/","-", $te)." GMT+7 23:59"))."') ".$_sql_offer." ".$sql." order by $columnName $sortOrder LIMIT $perPage")->fetch_array();
}

$paginationHtml = '';



$isCaller = isCaller() || isColler() ? true: false;



$isShipper = isShipper() ? true: false;    



$sql_check = [];



foreach ($result as $arr){



    $sql_check[] = " ( `order_phone` like '".$arr['order_phone']."' and `id`!='".$arr['id']."') ";







}



$sql_check = $sql_check ? ' where '.implode(" or ", $sql_check) : '';



$checkOrder = checkOrders($sql_check);



foreach ($result as $row) {   



    $check = isset($checkOrder[$row['order_phone']]) ?  $checkOrder[$row['order_phone']] : '';



    $caller = getUser($row['user_call']);



    $shipper = getUser($row['user_ship']);        



    $paginationHtml.='<tr data-id='.$row["id"].'>';      



    $paginationHtml.= '<td class="text-center">';



    if(isLeader() && isShipper())



        $paginationHtml.= '<input type="checkbox" class="form-check-input" id="order_'.$row['id'].'" value="'.$row['id'].'">



            <label class="form-check-label px-2" for="order_'.$row['id'].'"></label>';



        $paginationHtml.= '<a href="?route=editOrder&id='.$row['id'].'">



                <span class="btn btn-warning btn-sm waves-effect waves-light"><i class="w-fa fas fa-edit ml-1"></i></span>



            </a>';



    if(isLeader() && isShipper()) 



        $paginationHtml.= '<span role="btn-cancelOrder" class="btn btn-danger btn-sm buttonDelete waves-effect waves-light" data-toggle="modal" data-target="#cancelOrder">



                <i class="fas fa-times ml-1"></i>



            </span>';



    $paginationHtml.= '</td>';



    $paginationHtml.= '<td class="text-center">Đơn hàng #<b>'.$row['id'].'</b></td>';



    $paginationHtml.= '<td class="text-center"><b>'._e($row['offer_name']).'</b></td>';



    $paginationHtml.= '<td class="text-center">x<b>'._e($row['number']).'</b></td>';



    $paginationHtml.= '<td class="text-center"><b>'.addDotNumber($row['price_sell']).'</b>k</td>';



    $paginationHtml.= '<td class="text-center">'.date('Y/m/d H:i',$row['time']).'</td>';



    $paginationHtml.= '<td class="text-center">'._ucwords($row['order_name']).'</td>';



    $paginationHtml.= '<td class="text-center">'._e($row['order_phone']).'';



    if($check){



        foreach ($check as $dup) {
            $data_dup = $_db->query("select * from `core_orders` where `id` = '".$dup['id']."' ")->fetch();
            $caller_dup = getUser($data_dup['user_call']);
            $paginationHtml.= '<br><small>- '.$caller_dup['name'].' #<a target="_blank" href="?route=editOrder&id='.$dup['id'].'"><b class="text-danger">'.$dup['id'].' ('.$dup['status'].')</b></a></small>';



        }



    }



    $paginationHtml.='<td class="text-center">';



    $paginationHtml.='<p style="margin-bottom:0;"> '.nl2br(_ucwords($row['note'])).'</p>';    



    $paginationHtml.='</td>';



    $paginationHtml.= '<td><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal'.$arr['id'].'">
                      Chi tiết
                    </button>
            <div class="modal fade" id="exampleModal'.$arr['id'].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">



                  <h5 class="modal-title" id="exampleModalLabel">Địa chỉ mua hàng</h5>



                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">



                    <span aria-hidden="true">&times;</span>



                  </button>



                </div>



                <div class="modal-body">



                    <p style="margin-bottom:0;"><strong>Địa chỉ:</strong> '._ucwords($arr['order_address']).'</p>



                    <p style="margin-bottom:0;"><strong>Commune:</strong> '._ucwords($arr['order_commune']).'</p>



                    <p style="margin-bottom:0;"><strong>District:</strong> '._ucwords($arr['order_district']).'</p>


                    



                </div>



                <div class="modal-footer">



                  <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>                          



                </div>



              </div>



            </div>



          </div></td>';  



    $paginationHtml.= '<td class="text-left">'._ucwords($row['order_province']).'</td>';



    if($isShipper){



        $paginationHtml.= '<td class="text-center">';



        if($caller)



            $paginationHtml.= '<div class="chip align-middle">



                    <a target="_blank" href="?route=statistics&user='.$caller['id'].'">



                    <img src="'.getAvatar($caller['id']).'"> '.(!isBanned($caller) ? _e($caller['name']) : '<strike class="text-dark"><b>'._e($caller['name']).'</b></strike>').'



                    </a>



                </div>';



        $paginationHtml.= '</td>';



    }



    if($isCaller){



        $paginationHtml.= '<td class="text-center">';



        if($shipper)



            $paginationHtml.= '<div class="chip align-middle">



                    <a target="_blank" href="?route=statistics&user='.$shipper['id'].'">



                    <img src="'.getAvatar($shipper['id']).'"> '.(!isBanned($shipper) ? _e($shipper['name']) : '<strike class="text-dark"><b>'._e($shipper['name']).'</b></strike>').'



                    </a>



                </div>';



        $paginationHtml.= '</td>';



    }



	$paginationHtml.='</tr>';  







} 

echo $paginationHtml; 



?>