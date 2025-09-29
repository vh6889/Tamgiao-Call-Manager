<?php

$_info = infoOrder($_id);
$_group = getGroup($_info['group']);

$_nav = 'order';
$_nav_li = $_info['status'];

if(!$_info){

  echo '<div class="alert alert-danger" role="alert">
        <h4 class="alert-heading">Lỗi!</h4>
        <p>Đơn hàng không tồn tại hoặc đã bị xóa</p>
        <hr>
        <p class="mb-0">Vui lòng quay trở lại trang trước.</p>
      </div>';

  goto end;
}



$offer_ship = getInfoById('core_offers','id,name,price_ship',$_info['offer']);
$order_name  = isset($_POST['order_name'])?trim($_POST['order_name']):trim($_info['order_name']);
$order_phone = isset($_POST['order_phone'])?trim($_POST['order_phone']):trim($_info['order_phone']);
$number      = isset($_POST['number'])?trim($_POST['number']):trim($_info['number']);
$price_sell  = isset($_POST['price_sell'])?trim($_POST['price_sell']):trim($_info['price_sell']);
$free_ship1 = trim($_info['free_ship']);
if(isset($_POST['free_ship'])){
  $free_ship = 0;
}else{
  $free_ship = 1;
}
$order_address = isset($_POST['order_address'])?trim($_POST['order_address']):trim($_info['order_address']);
$order_commune = isset($_POST['order_commune'])?trim($_POST['order_commune']):trim($_info['order_commune']);
$order_province = isset($_POST['order_province'])?trim($_POST['order_province']):trim($_info['order_province']);
$order_district = isset($_POST['order_district'])?trim($_POST['order_district']):trim($_info['order_district']);
$area = isset($_POST['area'])?trim($_POST['area']):trim($_info['area']);
$note = isset($_POST['note'])?trim($_POST['note']):trim($_info['note']);
$order_info = isset($_POST['order_info'])?trim($_POST['order_info']):trim($_info['order_info']);
$status = isset($_POST['status'])?trim($_POST['status']):trim($_info['status']);

//$provinces = $_db->query("select * from `core_provinces` order by `provinceid` ")->fetch_array();
//$districts = $_db->query("select * from `core_districts` where ".($order_district ? " `name`='".escape_string($order_district)."' ":" `provinceid`='".$provinces[0]['provinceid']."' ")."  order by `name` ")->fetch_array();

if(isAller())
  include __DIR__.'/editOrder/aller.php';
else if(isCaller() || isPublisher()){
  if($status=="shipfail"){
    include __DIR__.'/editOrder/shipfail.php';
  }else{
    include __DIR__.'/editOrder/caller.php';
  }
}
else if(isColler())
  include __DIR__.'/editOrder/coller.php';
else if(isShipper())
  include __DIR__.'/editOrder/shipper.php';
else {
  echo '<div class="alert alert-danger" role="alert">
        <h4 class="alert-heading">Lỗi!</h4>
        <p>Tài khoản của bạn không có quyền truy cập vào mục này</p>
        <hr>
        <p class="mb-0">Vui lòng quay trở lại trang trước.</p>
      </div>';
}



end:

?>
