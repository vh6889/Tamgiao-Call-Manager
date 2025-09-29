<?php 
$offer = isset($_GET['offer']) ? trim($_GET['offer']) : (isset($_COOKIE['newDelay_offer']) ? $_COOKIE['newDelay_offer'] : 'all');
$ts = isset($_GET['ts']) && !empty($_GET['ts']) ? $_GET['ts'] :  date('d/m/Y',strtotime('-29 days GMT+7 00:00'));
$te = isset($_GET['te']) && !empty($_GET['te']) ? $_GET['te'] :  date('d/m/Y',time());
$area = isset($_GET['area']) ? $_GET['area'] : (isset($_COOKIE['newDelay_area']) ? $_COOKIE['newDelay_area'] : 'all');
if(isset($_GET['offer']) && $_GET['offer']){
  setcookie("newDelay_offer",$offer,time()+3600*24*365);
}
if(isset($_GET['area']) && in_array(trim($_GET['area']),['bac','trung','nam'])){
  setcookie("newDelay_area",$area,time()+3600*24*365);
}
if(isset($_GET['offer']) && !$_GET['offer']){
  setcookie("newDelay_offer","");
}
if(isset($_GET['area']) && !$_GET['area'] || $_GET['area'] == "all"){
  setcookie("newDelay_area","");
}
$_group = getGroup($_user['group']);
$_offers = getOffer();
$sql = "";
if($offer != "all"){
  $sql = " and `offer`='".escape_string($offer)."' ";
}
if($area != "all"){
  $sql .= " and `area`='".escape_string($area)."' ";
}
if(isset($_POST['limit'])){
  $perPage = $_POST['limit'];
}else{
  $perPage = 10;
}
$result = $_db->query("select * from `core_orders` where `status` = 'shipdelay' ".$sql." and (`time` >= '".(strtotime(str_replace("/","-", $ts)." GMT+7 00:00"))."' and `time` < '".(strtotime(str_replace("/","-", $te)." GMT+7 23:59"))."') and `user_ship`='' ".$_sql_offer." order by id DESC LIMIT $perPage")->fetch_array();
$paginationHtml = '';
$isCaller = isCaller() ? true: false;
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
    $paginationHtml.= '<tr data-id="'.$row['id'].'">';
    $paginationHtml.= '<td class="text-center">
            <input type="checkbox" class="form-check-input" id="order_'.$row['id'].'" value="'.$row['id'].'">
            <label class="form-check-label" for="order_'.$row['id'].'"></label>';
    if(isAller()){
        $paginationHtml.= '<a href="?route=editOrder&id='.$row['id'].'">
                <span class="btn btn-warning btn-sm waves-effect waves-light"><i class="w-fa fas fa-edit ml-1"></i></span>
            </a>';
    }
    $paginationHtml.= '<button role="btn-getOrder" class="btn '.getBgOrder('shipdelay').' btn-sm btn-rounded waves-effect waves-light"><i class="w-fa fas fa-dolly ml-1"></i> Get Order</button>
    </td>';
    $paginationHtml.= '<td class="text-center">Đơn hàng #<b>'.$row['id'].'</b></td>';
    $paginationHtml.= '<td class="text-center"><b>'._e($row['offer_name']).'</b></td>';
    $paginationHtml.= '<td class="text-center">x<b>'._e($row['number']).'</b></td>';
    $paginationHtml.= '<td class="text-center"><b>'.addDotNumber($row['price_sell']).'</b>k</td>';
    $paginationHtml.= '<td class="text-center">'.date('Y/m/d H:i',$row['time']).'</td>';
    if(isAdmin()){
      $paginationHtml.= '<td class="text-center">'.$arr['landing'].'</td>';
    }
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
                            <p><strong>Địa chỉ:</strong>&nbsp;'._ucwords($arr['order_address']).'</p>
                            <p><strong>Commune:</strong>&nbsp;'._ucwords($arr['order_commune']).'</p>
                            <p><strong>District:</strong>&nbsp;'._ucwords($arr['order_district']).'</p>                            
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">Close</button>                      
                    </div>
                  </div>
                </div>
              </div></td>';                     
    $paginationHtml.= '<td class="text-left">'._ucwords($row['order_province']).'</td>';
    if($isShipper){
        $paginationHtml.= '<td class="text-center">';
        if($caller){
            $paginationHtml.= '<div class="chip align-middle">
                <a target="_blank" href="?route=statistics&user='.$caller['id'].'">
                    <img src="'.getAvatar($caller['id']).'"> '.(!isBanned($caller) ? _e($caller['name']) : '<strike class="text-dark"><b>'._e($caller['name']).'</b></strike>').'
                </a>
                </div>';
        }
        $paginationHtml.= '</td>';
    }
    if($isCaller){
        $paginationHtml.= '<td class="text-center">';
        if($shipper){
            $paginationHtml.= '<div class="chip align-middle">
                <a target="_blank" href="?route=statistics&user='.$shipper['id'].'">
                    <img src="'.getAvatar($shipper['id']).'"> '.(!isBanned($shipper) ? _e($shipper['name']) : '<strike class="text-dark"><b>'._e($shipper['name']).'</b></strike>').'
                </a>
                </div>';
        }
        $paginationHtml.= '</td>';
    }
    $paginationHtml.= '</tr>';
}
echo $paginationHtml; 
?>