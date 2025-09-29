<?php


$perEdit = true;
/*if(isBanned() && !isAdmin())
  $perEdit = false;
else */

if((!isLeader() && $_info['user_ship'] != $_user['id']) || !$_info['user_ship'])
  $perEdit = false;
else
  $perEdit = true;




$check = array("trashed","rejected","callerror");//,"callback"

$permissions = array();


$requestEdit = false;

if($_info['status'] != "approved"){
  $permissions = array("pending","approved","shipping","shipdelay","shiperror");
} else {
  $perEdit = false;
  $requestEdit = true;
}


if(isset($_POST['submitRequest']) && $requestEdit){

  $typeRequest = isset($_POST['typeRequest']) ? $_POST['typeRequest'] : '';
  $textRequest = isset($_POST['textRequest']) ? $_POST['textRequest'] : '';

  $notifi_user = array();
  $aller = listAller();
  if($aller){
    foreach ($aller as $adm) {
      $notifi_user[] = $adm['id'];
    }
  }


 
  if($typeRequest == "cancel"){

    $notifi_title = '<strong class="trigger red lighten-2 text-white">Yêu cầu hủy đơn đặt hàng #'.$_info['id'].'</strong>';
    $notifi_text = 'Đơn đặt hàng <a href="?route=editOrder&id='.$_info['id'].'"><b class="text-danger">#'.$_info['id'].'</b></a> vừa được yêu cầu hủy vận chuyển.<br><strong>Lí do:</strong> '._e($textRequest);


  } else {


    $notifi_title = '<strong class="trigger orange lighten-2 text-white">Yêu cầu cập nhật đơn đặt hàng #'.$_info['id'].'</strong>';
    $notifi_text = 'Đơn đặt hàng <a href="?route=editOrder&id='.$_info['id'].'"><b class="text-danger">#'.$_info['id'].'</b></a> vừa được yêu cầu cập nhật lại thông tin.<br><strong>Yêu cầu:</strong> '._e($textRequest);

  }






  if(addNotification($notifi_title,$notifi_text,$notifi_user)){
    $_statusmessage['type'] = 'success';
    $_statusmessage['message'] = 'Gửi yêu cầu cập nhật đơn hàng thành công.Vui lòng chờ một thời gian để shipper xử lí.';    
  } else {
    $_statusmessage['type'] = 'warning';
    $_statusmessage['message'] = 'Không thể gửi yêu cầu cập nhật đơn hàng thành công.Vui lòng thử lại.'; 
  }


} else if(isset($_POST['cancelOrder']) && $perEdit){

  if($_db->exec_query("UPDATE `core_orders` set `group`='',`payout_leader`='',`payout_member`='',`deduct_leader`='',`deduct_member`='',`user_call`='',`call_time`='',`status`='uncheck',`r_hold`='',`r_deduct`='',`r_approve`='' where `id`='".escape_string($_info['id'])."' and `status`='calling' ")){

    $_statusmessage['type'] = 'success';
    $_statusmessage['message'] = 'Hủy đơn gọi thành công.';

    //header('Location: ?route=newOrder');


  } else {
    $_statusmessage['type'] = 'warning';
    $_statusmessage['message'] = 'Có lỗi xảy ra khi hủy đơn gọi.';
  }


} else if(isset($_POST['submit']) && $perEdit){


      if($_info['status'] == 'uncheck' || !$_info['user_call']){
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Không thể sửa đơn hàng chưa được ai đó xác nhận ('.$status.').';
      } else if(!in_array($status, $permissions)){
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Bạn không có quyền update trạng thái \''.$status.'\'.';
      } else if(empty($order_name) && !in_array($status, $check)){
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Tên người mua không được bỏ trống.';
      } else if(!check_phone($order_phone) && !in_array($status, $check)){
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Số điện thoại không hợp lệ.';
      } else if(empty($order_address)  && !in_array($status, $check)){
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Địa chỉ người mua không được bỏ trống.';
      } else if(empty($order_commune)  && !in_array($status, $check)){
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Phường/Xã không được bỏ trống.';
      } else if(empty($order_district)  && !in_array($status, $check)){
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Quận/Huyện không được bỏ trống.';
      } else if(empty($order_province)  && !in_array($status, $check)){
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Tỉnh/Thành phố không được bỏ trống.';
      } else if($number < 0 || !is_numeric($number) && !in_array($status, $check)){
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Số lượng mua không hợp lệ';
      } else if(($price_sell < 0  || !is_numeric($price_sell)) && !in_array($status, $check)){
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Tổng tiền phải lớn hơn 0.';
      } else if(!in_array($area, ['bac','trung','nam'])){
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Vui lòng chọn ít nhất một khu vực.';
      } else if(empty($status)){
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Trạng thái đơn hàng không chính xác.';
      } else {        
        if(!empty($order_info)){
          $notifi_title = '<strong class="trigger red lighten-2 text-white">Thông báo giao hàng chưa thành công</strong>';  
          $notifi_text = '<strong>Đơn hàng: <a target="_blank" href="?route=editOrder&id='.$_info['id'].'"><span class="text-danger">#'.$_info['id'].'</span></a> vừa được yêu cầu giao hàng chưa thành công</strong>.';
          addNotification($notifi_title,$notifi_text,$_info['user_call']);  
          addNotification($notifi_title,$notifi_text,$_info['user_ship']);  
        }        
        if($status=="callerror"){
          $notifi_title = '<strong class="trigger red lighten-2 text-white">Thông báo không gọi được đơn hàng</strong>';
          $notifi_text = '<strong>Đơn hàng: <a target="_blank" href="?route=editOrder&id='.$_info['id'].'"><span class="text-danger">#'.$_info['id'].'</span></a> vừa được yêu cầu không gọi được với lí do: '._e($note).'</strong>.';
          addNotification($notifi_title,$notifi_text,$_info['user_call']);  
          addNotification($notifi_title,$notifi_text,$_info['user_ship']);  
        }          
        if($status=="shipdelay"){
          $notifi_title = '<strong class="trigger red lighten-2 text-white">Thông báo hẹn giao hàng</strong>';
          $notifi_text = '<strong>Đơn hàng: <a target="_blank" href="?route=editOrder&id='.$_info['id'].'"><span class="text-warning">#'.$_info['id'].'</span></a> vừa được yêu cầu hẹn giao hàng với lí do: '._e($note).'</strong>.';
          addNotification($notifi_title,$notifi_text,$_info['user_call']);  
          addNotification($notifi_title,$notifi_text,$_info['user_ship']);  
        }
        if($status == "rejected"){
          $notifi_title = '<strong class="trigger red lighten-2 text-white">Thông báo từ chối mua hàng</strong>';
          $notifi_text = '<strong>Đơn hàng: <a target="_blank" href="?route=editOrder&id='.$_info['id'].'"><span class="text-danger">#'.$_info['id'].'</span></a> vừa được yêu cầu từ chối mua hàng với lí do: '._e($note).'</strong>.';
          addNotification($notifi_title,$notifi_text,$_info['user_call']);  
          addNotification($notifi_title,$notifi_text,$_info['user_ship']);  
        }
        if($status == "trashed"){
          $notifi_title = '<strong class="trigger red lighten-2 text-white">Thông báo đơn rác</strong>';
          $notifi_text = '<strong>Đơn hàng: <a target="_blank" href="?route=editOrder&id='.$_info['id'].'"><span class="text-danger">#'.$_info['id'].'</span></a> vừa được chuyển sang đơn rác hàng với lí do: '._e($note).'</strong>.';
          addNotification($notifi_title,$notifi_text,$_info['user_call']);  
          addNotification($notifi_title,$notifi_text,$_info['user_ship']);    
        }
        if($status == 'shipping'){
          $notifi_title = '<strong class="trigger green lighten-2 text-white">Thông báo đang giao hàng</strong>';
          $notifi_text = '<strong>Đơn hàng: <a target="_blank" href="?route=editOrder&id='.$_info['id'].'"><span class="text-primary">#'.$_info['id'].'</span></a> vừa được chuyển sang đang giao hàng với lí do: '._e($note).'</strong>.';
          addNotification($notifi_title,$notifi_text,$_info['user_call']);  
          addNotification($notifi_title,$notifi_text,$_info['user_ship']);      
        }
        if($status == 'shiperror'){
          $notifi_title = '<strong class="trigger red lighten-2 text-white">Thông báo không nhận hàng</strong>';
          $notifi_text = '<strong>Đơn hàng: <a target="_blank" href="?route=editOrder&id='.$_info['id'].'"><span class="text-danger">#'.$_info['id'].'</span></a> vừa được yêu cầu không nhận hàng với lí do: '._e($note).'</strong>.';
          addNotification($notifi_title,$notifi_text,$_info['user_call']);  
          addNotification($notifi_title,$notifi_text,$_info['user_ship']); 
        }
        if($status == 'approved'){
          $notifi_title = '<strong class="trigger green lighten-2 text-white">Thông báo giao hàng thành công</strong>';
          $notifi_text = '<strong>Đơn hàng: <a target="_blank" href="?route=editOrder&id='.$_info['id'].'"><span class="text-danger">#'.$_info['id'].'</span></a> vừa được yêu cầu giao hàng thành công với lí do: '._e($note).'</strong>.';
          addNotification($notifi_title,$notifi_text,$_info['user_call']);  
          addNotification($notifi_title,$notifi_text,$_info['user_ship']);  
        }
        if($_db->exec_query("update `core_orders` set `order_name`='".escape_string($order_name)."',`order_phone`='".escape_string($order_phone)."',`price_sell`='".escape_string($price_sell)."',`number`='".escape_string($number)."',`free_ship`='".$free_ship."',`order_address`='".escape_string($order_address)."',`order_commune`='".escape_string($order_commune)."',`order_province`='".escape_string($order_province)."',`order_district`='".escape_string($order_district)."',`status`='".escape_string($status)."',`note`='".escape_string($note)."',`order_info`='".escape_string($order_info)."',`area`='".escape_string($area)."' where `id`='".$_info['id']."'  ")){


          $_statusmessage['type'] = 'success';
          $_statusmessage['message'] = 'Cập nhật đơn hàng thành công.';

          $_db->exec_query("update `core_orders` set `last_edit`='".$_user['id']."',`update_time`='".time()."' where `id`='".$_info['id']."' ");

          $_nav_li = $status;

          if($_info['status'] == "shipping" && ($status == "shiperror" || $status == "rejected") && $_info['user_ship'] != $_user['id']){
            $notifi_title = '<strong class="trigger red lighten-2 text-white">Thông báo hủy đơn hàng</strong>';
            $notifi_text = '<strong>Đơn hàng: <a target="_blank" href="?route=editOrder&id='.$_info['id'].'"><span class="text-danger">#'.$_info['id'].'</span></a> vừa được yêu cầu hủy vận chuyển với lí do: '._e($note).'</strong>.';
            addNotification($notifi_title,$notifi_text,$_info['user_ship']);
          }

          if($_info['payout_type']=='percent') {
            $_postPayout  = $_info['payout_member']/100 * $_info['price']*$number;
            $_pastPayout  = $_info['payout_member']/100 * $_info['price']*$_info['number'];
            $_postPayout2 = $_info['payout_leader']/100 * $_info['price']*$number;
            $_pastPayout2 = $_info['payout_leader']/100 * $_info['price']*$_info['number'];
          }else{
            $_postPayout  = $_info['payout_member'] * $number;
            $_pastPayout  = $_info['payout_member'] * $_info['number'];
            $_postPayout2 = $_info['payout_leader'] * $number;
            $_pastPayout2 = $_info['payout_leader'] * $_info['number'];
          }

          if($_info['status'] != $status || $number != $_info['number']){

            if(in_array($status, ['pending','shipping','shipdelay'])){

              if($status == "pending" && $_info['status'] != $status)
                curl_sendNotifi($_url.'/sendNotifi.php?act=pending&id='.$_info['id']);

              $sql = array();
              $sql2 = array();

              
              if($_info['r_hold'] <= 0){
                $sql[] =" `revenue_pending`=`revenue_pending`+'".$_postPayout."' ";
                $sql2[] =" `revenue_pending`=`revenue_pending`+'".$_postPayout2."' ";
              }

              if($_info['r_hold'] > 0 && $number != $_info['number']){
                $sql[] =" `revenue_pending`=`revenue_pending`-'".$_pastPayout."'+'".$_postPayout."' ";
                $sql2[] =" `revenue_pending`=`revenue_pending`-'".$_pastPayout2."'+'".$_postPayout2."' ";
              }

              if($_info['r_approve'] > 0){
                $sql[] =" `revenue_approve`=`revenue_approve`-'".$_pastPayout."' ";
                $sql2[] =" `revenue_approve`=`revenue_approve`-'".$_pastPayout2."' ";
              }

              if($_info['r_deduct'] > 0){
                $sql[] =" `revenue_deduct`=`revenue_deduct`-'".$_info['deduct_member']."' ";
                $sql2[] =" `revenue_deduct`=`revenue_deduct`-'".$_info['deduct_leader']."' ";
              }

        

              if($sql)
                if($_db->query("update `core_users` set ".implode(" , ", $sql)." where `id`='".$_info['user_call']."' ")){
                  if($sql2)
                    $_db->exec_query("update `core_groups` set ".implode(" , ", $sql2)." where `id` = '".$_info['group']."'");
                  $_db->query("update `core_orders` set `r_hold`='1',`r_approve`='0',`r_deduct`='0' where `id`='".$_info['id']."' ");
                }
            } else if(in_array($status, ['callback','callerror','rejected','trashed'])){

              if($status == "pending")
                curl_sendNotifi($_url.'/sendNotifi.php?act=pending&id='.$_info['id']);

              $sql = array();
              $sql2 = array();

              if($_info['r_hold'] > 0){
                $sql[] =" `revenue_pending`=`revenue_pending`-'".$_pastPayout."' ";
                $sql2[] =" `revenue_pending`=`revenue_pending`-'".$_pastPayout2."' ";
              }

              if($_info['r_approve'] > 0){
                $sql[] =" `revenue_approve`=`revenue_approve`-'".$_pastPayout."' ";
                $sql2[] =" `revenue_approve`=`revenue_approve`-'".$_pastPayout2."' ";
              }

              if($_info['r_deduct'] > 0){
                $sql[] =" `revenue_deduct`=`revenue_deduct`-'".$_info['deduct_member']."' ";
                $sql2[] =" `revenue_deduct`=`revenue_deduct`-'".$_info['deduct_leader']."' ";    
              }



              if($sql)
                if($_db->query("update `core_users` set ".implode(" , ", $sql)." where `id`='".$_info['user_call']."' ")){
                  if($sql2)
                   $_db->exec_query("update `core_groups` set ".implode(" , ", $sql2)." where `id` = '".$_info['group']."'");
                  $_db->query("update `core_orders` set `r_hold`='0',`r_approve`='0',`r_deduct`='0' where `id`='".$_info['id']."' ");
                }
            } else if ($status == "shiperror"){

              $sql = array();
              $sql2 = array();

              if($_info['r_hold'] > 0){
                $sql[] =" `revenue_pending`=`revenue_pending`-'".$_pastPayout."' ";
                $sql2[] =" `revenue_pending`=`revenue_pending`-'".$_pastPayout2."' ";
              }

              if($_info['r_approve'] > 0){
                $sql[] =" `revenue_approve`=`revenue_approve`-'".$_pastPayout."' ";
                $sql2[] =" `revenue_approve`=`revenue_approve`-'".$_pastPayout2."' ";
              }

              if($_info['r_deduct'] <= 0){
                $sql[] =" `revenue_deduct`=`revenue_deduct`+'".$_info['deduct_member']."' ";
                $sql2[] =" `revenue_deduct`=`revenue_deduct`+'".$_info['deduct_leader']."' ";      
              }

              if($sql)
                if($_db->query("update `core_users` set ".implode(" , ", $sql)." where `id`='".$_info['user_call']."' ")){
                  if($sql2)
                    $_db->exec_query("update `core_groups` set ".implode(" , ", $sql2)." where `id` = '".$_info['group']."'");
                  $_db->query("update `core_orders` set `r_hold`='0',`r_approve`='0',`r_deduct`='1' where `id`='".$_info['id']."' ");
                }

                // bạn vừa bị khấu trừ xxxx
            } else if ($status == "approved"){

              $sql = array();
              $sql2 = array();

              if($_info['r_hold'] > 0){
                $sql[] =" `revenue_pending`=`revenue_pending`-'".$_pastPayout."' ";
                $sql2[] =" `revenue_pending`=`revenue_pending`-'".$_pastPayout2."' ";
              }

              if($_info['r_approve'] <= 0){
                /* Update `price_bonus` for order `ukey` */
                $offer_bonus = $_db->query('select `price_bonus` from `core_offers` where `id`='.$_info['offer'])->fetch();
                $price_bonus = $offer_bonus['price_bonus']*$number;
                if($price_bonus) {
                  $_db->exec_query("update `core_users` set `revenue_approve`=`revenue_approve`+$price_bonus where `ukey`='".$_info['ukey']."'");
                }
                
                $sql[] =" `revenue_approve`=`revenue_approve`+'".$_postPayout."' ";
                $sql2[] =" `revenue_approve`=`revenue_approve`+'".$_postPayout2."' ";
              }

              if($_info['r_approve'] > 0 && $number != $_info['number']){
                $sql[] =" `revenue_approve`=`revenue_approve`-'".$_pastPayout."'+'".$_postPayout."' ";
                $sql2[] =" `revenue_approve`=`revenue_approve`-'".$_pastPayout2."'+'".$_postPayout2."' ";
              }

              if($_info['r_deduct'] > 0){
                $sql[] =" `revenue_deduct`=`revenue_deduct`-'".$_info['deduct_member']."' ";
                $sql2[] =" `revenue_deduct`=`revenue_deduct`-'".$_info['deduct_leader']."' ";         
              }

              if($sql)
                if($_db->query("update `core_users` set ".implode(" , ", $sql)." where `id`='".$_info['user_call']."' ")){
                  if($sql2)
                    $_db->exec_query("update `core_groups` set ".implode(" , ", $sql2)." where `id` = '".$_info['group']."'");
                  $_db->query("update `core_orders` set `r_hold`='0',`r_approve`='1',`r_deduct`='0' where `id`='".$_info['id']."' ");
                }
                // bạn vừa nhận được xxx tiền có thể thanh toán

            }

          }

          $_info = infoOrder($_id);
          echo '<meta http-equiv="refresh" content="1;url=?route=order&type='.$status.'" />';


        } else {
          $_statusmessage['type'] = 'warning';
          $_statusmessage['message'] = 'Không có gì cần lưu lại.';
        }
      }
}



$last_edit = getUser($_info['last_edit']);
$caller = getUser($_info['user_call']);
$shipper = getUser($_info['user_ship']);
?>


<h2 class="section-heading mb-4">Chỉnh sửa đơn hàng <span class="badge <?=getBgOrder($_info['status']);?>"><?=$_info['status'];?></span></h2>


<section class="row mb-5 py-3">
    <div class="col-md-12 mx-auto white z-depth-1">

      <?php if($requestEdit) { ?>
        <div class="form-group text-right">
          <button id="btn-requestEdit" class="btn btn-danger btn-md btn-rounded waves-effect text-dark-50" data-toggle="modal" data-target="#requestEdit">Yêu cầu cập nhật</button>
        </div>
      <?php } ?>

      <?php if(!$perEdit) { ?>
      <div class="mx-2 pb-3">
          <p class="note note-danger">Bạn không thể chỉnh sửa đơn hàng này.</p>
      </div>
      <?php } ?>

      <form id="formUpdate" method="POST" onsubmit="return submitUpdate();">

        <div class="row px-2">
          <div class="col-md-12">
            <?php if(!empty($_statusmessage)): ?>
              <div class="alert alert-<?=$_statusmessage["type"]; ?> lert-dismissible fade show" role="alert">
                <?=$_statusmessage["message"]; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
            <?php endif; ?>
          </div>


          <div class="col-md-3">
            <label>ID đơn hàng:</label>
            <input type="text" class="form-control" value="#<?=_e($_info['id']);?>" disabled>
            <?php if($check = checkOrder($_info)){
              echo '<b class="number">'._e($arr['order_phone']).'</b>';
              if($check){
                foreach ($check as $dup) {
                  echo '<p><small>- Trùng đơn #<a target="_blank" href="?route=editOrder&id='.$dup['id'].'"><b class="text-danger">'.$dup['id'].' ('.$dup['status'].')</b></a></small></p>';
                }
              }
            }
            ?>
          </div>
		  <div class="col-md-3">
            <label>Số hiệu:</label>
            <input type="text" class="form-control" value="<?=_e($_info['parcel_code']);?>" disabled> 
			<a target="_blank" href="http://www.vnpost.vn/en-us/dinh-vi/buu-pham?key=<?=_e($_info['parcel_code']);?>"><i class="fas fa-external-link-alt ml-1"></i></a>
          </div>
          
          <div class="col-md-3">
            <label>Bưu điện:</label>
            <input type="text" class="form-control" value="<?=_e($_info['post_office']);?>" disabled /> 
          </div>
          
          <div class="col-md-3">
            <label>Tên sản phẩm:</label>
            <input type="text" class="form-control" value="<?=_e($_info['offer_name']);?>" disabled>
            <?php 
                if (isset($_info['offer'])) $offer = getOffer($_info['offer']);
                echo $offer['offer_link']!='' ? '<a href="'.$offer['offer_link'].'" target="__blank"><i class="fas fa-external-link-alt ml-1"></i></a>' : '';
            ?>
          </div>
          <div class="col-md-3">
            <label>Giá sản phẩm:</label>
            <input type="text" class="form-control" id="price" data-price="<?=_e(addDotNumber($_info['price']));?>" value="<?=_e(addDotNumber($_info['price']));?>k" disabled>
          </div>
          <div class="col-md-3">
            <label>Ngày đặt hàng:</label>
            <input type="text" class="form-control" value="<?=date('Y/m/d H:i',$_info['time']);?>" disabled>
          </div>
        </div>

      <div class="px-2">
        <div class="form-group mt-3">
            <label>Tên người đặt mua:</label>
            <input type="text" class="form-control" id="order_name" name="order_name" value="<?=_e($order_name);?>" <?=(!$perEdit?'disabled':'');?>>
            <div class="invalid-feedback">Tên người đặt mua không được bỏ trống</div>
        </div>

        <div class="form-group mt-3">
            <label>Số điện thoại:</label>
            <input type="text" class="form-control" id="order_phone" name="order_phone" value="<?=_e($order_phone);?>" <?=(!$perEdit?'disabled':'');?>>
            <div class="invalid-feedback">Số điện thoại không được bỏ trống.</div>
        </div>

        <div class="form-group mt-3">
            <label>Số lượng mua:</label>
            <input id="number" type="number" class="form-control" name="number" value="<?=_e($number);?>" <?=(!$perEdit?'disabled':'');?>>
            <div class="invalid-feedback">Số lượng mua không hợp lệ.</div>
        </div>

        <div class="form-group mt-3">
            <label>Tổng tiền(k):</label>
            <input id="price_sell" type="number" class="form-control" name="price_sell" value="<?=_e($price_sell);?>" <?=(!$perEdit?'disabled':'');?>>
            <div class="invalid-feedback">Tổng tiền phải lớn hơn 0.</div>
        </div>
        <div class="form-group mt-3">            
            <input id="free_ship1" style="opacity:1;position:static;pointer-events:visible;" class="" type="checkbox" name="free_ship" value="0" <?php if($free_ship1==0 && $number>0){ echo 'checked';}else{} ?>> Miễn phí ship 
        </div>
        <div class="form-group">
          <label>Địa chỉ người mua (số nhà,đường/phố,xóm,thôn):</label>
          <textarea class="form-control" id="order_address" name="order_address" rows="5"><?=_e($order_address);?></textarea>
          <div class="invalid-feedback">Địa chỉ người mua không được bỏ trống.</div>
        </div>

        <div class="form-group">
          <label>Phường/Xã:</label>
          <input type="text" class="form-control" id="order_commune" name="order_commune" value="<?=_e($order_commune);?>">
          <div class="invalid-feedback">Phường/Xã không được bỏ trống</div>
        </div>

        <div class="form-group">
          <label>Quận/Huyện:</label>
          <input type="text" class="form-control" id="order_district" name="order_district" value="<?=_e($order_district);?>">
          <div class="invalid-feedback">Quận/Huyện không được bỏ trống</div>
        </div>

        <div class="form-group">
          <label>Tỉnh/Thành phố:</label>
          <input type="text" class="form-control" id="order_province" name="order_province" value="<?=_e($order_province);?>">
          <div class="invalid-feedback">Tỉnh/Thành phố không được bỏ trống</div>
        </div>

        
        <div class="form-group">
          <label>Khu vực:</label>
          <select id="area" name="area" class="mdb-select <?=(!$perEdit?'disabled':'');?>" >
            <option selected value="bac"> Miền bắc </option>
            <option <?=($area == "trung" ? 'selected':'');?> value="trung"> Miền trung</option>
            <option <?=($area == "nam" ? 'selected':'');?> value="nam"> Miền nam</option>
          </select>
          <div class="invalid-feedback">Vui lòng chọn một khu vực.</div>
        </div>

        <div class="form-group">
          <label>Ghi chú:</label>
          <textarea class="form-control" name="note" rows="2" <?=(!$perEdit?'disabled':'');?>><?=_e($note);?></textarea>
        </div>   
        <?php 
          if($status == 'shipping' || $status=='shipfail'){
          ?>
          <div class="form-group">
            <label>Order Info:</label>
            <textarea class="form-control" name="order_info" rows="2" <?=(!$perEdit?'disabled':'');?>><?=_e($order_info);?></textarea>
          </div>
          <?php 
          }
        ?>     
        <div class="form-group">
          <label>Tình trạng đơn:</label>
          <select id="status" name="status" class="mdb-select <?=(!$perEdit?'disabled':'');?>" <?=(!$perEdit?'disabled':'');?>>
            <option selected value="pending" <?=(!in_array('pending',$permissions) ? 'disabled':'');?>> Pending (Chuẩn bị hàng)</option>

            <!--<option <?=($status == "callback" ? 'selected':'');?> value="callback" <?=(!isCaller() ? 'disabled':'');?>> Call Back (Hẹn gọi lại)</option> -->
            <option <?=($status == "callerror" ? 'selected':'');?> value="callerror" <?=(!in_array('callerror',$permissions) ? 'disabled':'');?>> Call Error (Không gọi được)</option>
            <option <?=($status == "shipdelay" ? 'selected':'');?> value="shipdelay" <?=(!in_array('shipdelay',$permissions) ? 'disabled':'');?>> ShipDelay (Hẹn ngày giao hàng)</option>
            <option <?=($status == "rejected" ? 'selected':'');?> value="rejected" <?=(!in_array('rejected',$permissions) ? 'disabled':'');?>> Reject (Từ chối mua hàng)</option>
            <option <?=($status == "trashed" ? 'selected':'');?> value="trashed" <?=(!in_array('trashed',$permissions) ? 'disabled':'');?>> Trash (Đơn hàng rác)</option>

            <option <?=($status == "shipping" ? 'selected':'');?>  value="shipping" <?=(!in_array('shipping',$permissions) ? 'disabled':'');?>> Shipping (Đang giao hàng)</option>
            <option <?=($status == "shiperror" ? 'selected':'');?> value="shiperror" <?=(!in_array('shiperror',$permissions) ? 'disabled':'');?>> ShipError (Không nhận hàng)</option>
            <option <?=($status == "shippingfailed" ? 'selected':'');?> value="shippingfailed" <?=(!in_array('shippingfailed',$permissions) ? 'disabled':'');?>> ShippingFailed (Phát chưa thành công)</option>
            <option <?=($status == "approved" ? 'selected':'');?>  value="approved" <?=(!in_array('approved',$permissions) ? 'disabled':'');?>> Approve (Giao hàng thành công)</option>

          </select>
          <div class="invalid-feedback">Vui lòng chọn một trạng thái.</div>
        </div>
      <?php if($perEdit) { ?>
        <div class="form-group text-right">
          <?php if($_info['status'] == "calling"){ ?>
            <input class="btn btn-danger btn-md btn-rounded waves-effect text-white" name="cancel" type="button" value="Cancel Order" data-toggle="modal" data-target="#cancelOrder">
          <?php } ?>
          <input class="btn btn-dark btn-md btn-rounded waves-effect text-dark-50" name="submit" type="submit" value="Update Order">
        </div>
      <?php } ?>


      </form>
    </div>


          <?php if($shipper || $caller || $last_edit) { ?>
            <div class="row px-2">
              <div class="col-md-12">
                <hr>
              </div>

          <?php if($last_edit) { ?>
          <div class="col-md-4">
            <div>
              <b class="text-danger pr-4 ">Last Edit:</b>
              <span class="chip align-middle">
                <a target="_blank" href="?route=statistics&user=<?=$last_edit['id'];?>">
                  <img src="<?=getAvatar($last_edit['id']);?>"> <?=(!isBanned($last_edit) ? _e($last_edit['name']) : '<strike class="text-dark"><b>'._e($last_edit['name']).'</b></strike>');?>
                </a>
              </span>
              <p class="text-muted mt-3"><i class="far fa-clock" aria-hidden="true"></i> <?=get_time($_info['update_time']);?></p>
            </div>
          </div>  
          <?php } ?>

          <?php if($caller) { ?>
          <div class="col-md-4">
            <div>
              <b class="text-danger pr-4 ">Caller:</b>
              <span class="chip align-middle">
                <a target="_blank" href="?route=statistics&user=<?=$caller['id'];?>">
                  <img src="<?=getAvatar($caller['id']);?>"> <?=(!isBanned($caller) ? _e($caller['name']) : '<strike class="text-dark"><b>'._e($caller['name']).'</b></strike>');?>
                </a>
              </span>
              <?php if($_info['call_time']) { ?>
              <p class="text-muted mt-3"><i class="far fa-clock" aria-hidden="true"></i> <?=get_time($_info['call_time']);?></p>
              <?php } ?>
            </div>
          </div>  
          <?php } ?>

          <?php if($shipper) { ?>
          <div class="col-md-4">
            <div>
              <b class="text-danger pr-4 ">Shipper:</b>
              <span class="chip align-middle">
                <a target="_blank" href="?route=statistics&user=<?=$shipper['id'];?>">
                  <img src="<?=getAvatar($shipper['id']);?>"> <?=(!isBanned($shipper) ? _e($shipper['name']) : '<strike class="text-dark"><b>'._e($shipper['name']).'</b></strike>');?>
                </a>
              </span>
              <?php if($_info['ship_time']) { ?>
              <p class="text-muted mt-3"><i class="far fa-clock" aria-hidden="true"></i> <?=get_time($_info['ship_time']);?></p>
              <?php } ?>
            </div>
          </div>  
          <?php } ?>



        <?php } ?>


    </div>
</section>


<?php if($requestEdit){ ?>

<div class="modal fade" id="requestEdit" tabindex="-1" role="dialog"
  aria-hidden="true">
  <div class="modal-dialog modal-md modal-notify modal-warning" role="document">
    <!--Content-->
    <div class="modal-content text-center">
      <!--Header-->
      <div class="modal-header d-flex justify-content-center">
        <h2 class="heading">Yêu cầu cập nhật đơn hàng</h2>
      </div>

      <!--Body-->
      <div class="modal-body">
        <div class="text-left">
        <form id="formRequestEdit" method="POST">
          <div class="form-group">
            <label>Yêu cầu:</label>
            <select id="typeRequest" name="area" class="mdb-select" name="typeRequest" required="">
              <option selected value="update"> Cập nhật thông tin </option>
            </select>
            <div class="invalid-feedback">Vui lòng chọn một yêu cầu.</div>
          </div>

          <div class="form-group">
            <label>Lý do:</label>
            <textarea class="form-control" id="textRequest" name="textRequest" rows="2" required></textarea>
          </div>
        </div>
      </div>

      <!--Footer-->
      <div class="modal-footer flex-center">

        <button class="btn btn-outline-warning" type="submit" name="submitRequest">Gửi yêu cầu </button>

        <button class="btn btn-warning waves-effect" data-dismiss="modal">Đóng</button>
      </div>
      </form>
    </div>
    <!--/.Content-->
  </div>
</div>

<?php } ?>
<?php if($perEdit && $_info['status'] == "calling"){ ?>
<!--Modal: modalConfirmDelete-->
<div class="modal fade" id="cancelOrder" tabindex="-1" role="dialog"
  aria-hidden="true">
  <div class="modal-dialog modal-sm modal-notify modal-danger" role="document">
    <!--Content-->
    <div class="modal-content text-center">
      <!--Header-->
      <div class="modal-header d-flex justify-content-center">
        <p class="heading">Hủy đơn hàng</p>
      </div>

      <!--Body-->
      <div class="modal-body">

        <i class="fas fa-times fa-4x animated rotateIn"></i>
        <div id="deleteBody" data-id="">
          Bạn thực sự muốn hủy đơn hàng về trạng thái uncheck?
        </div>
      </div>

      <!--Footer-->
      <div class="modal-footer flex-center">
      <form id="formCancel" method="POST">
        <button class="btn  btn-outline-danger" type="submit" name="cancelOrder">Xóa</button>
      </form>
        <button class="btn  btn-danger waves-effect" data-dismiss="modal">Hủy bỏ</button>
      </div>
    </div>
    <!--/.Content-->
  </div>
</div>
<!--Modal: modalConfirmDelete-->
<?php } ?>

<script type="text/javascript">

  <?php if($perEdit) { ?>
  $("#number").keyup(function () {
    var number = this.value.replace(/\D/g, "");
    if(number > 0){
      if($("#free_ship1").is(':checked')){
        $("#price_sell").val((number*$("#price").attr("data-price").trim()));
      }else{
        $("#price_sell").val((number*$("#price").attr("data-price").trim()+<?php echo $offer_ship['price_ship']; ?>));
      }
    } else {
      $("#price_sell").val('');
    }    
  });

  $("#number").on("change",function () {
    var number = this.value.replace(/\D/g, "");
    if(number > 0){
      if($("#free_ship1").is(':checked')){
        $("#price_sell").val((number*$("#price").attr("data-price").trim()));
      }else{
        $("#price_sell").val((number*$("#price").attr("data-price").trim()+<?php echo $offer_ship['price_ship']; ?>));
      }
    } else {
      $("#price_sell").val('');
    }    
  });
  loadShip();
  function loadShip(){
    $("#free_ship1").on("change",function(){
      var thisShip = $(this).val();      
      var price_sell = parseInt($("#price_sell").val());
      if($(this).is(':checked')){           
        var sum_self = parseInt(price_sell-<?php echo $offer_ship['price_ship']; ?>);
        $("#price_sell").val(sum_self);
      }else{        
        var sum_self = parseInt(price_sell+<?php echo $offer_ship['price_ship']; ?>);
        $("#price_sell").val(sum_self);
      }
    });    
  }
  function submitUpdate(){

    var order_name = $("#order_name"),
        order_phone = $("#order_phone"),
        number = $("#number"),
        price_sell = $("#price_sell"),
        order_address = $("#order_address"),
        order_province = $("#order_province"),
        order_commune = $("#order_commune"),
        order_district = $("#order_district"),
        area = $("#area"),
        note = $("#note"),
        status = $("#status");

    order_name.siblings(".invalid-feedback").hide();
    order_phone.siblings(".invalid-feedback").hide();
    number.siblings(".invalid-feedback").hide();
    price_sell.siblings(".invalid-feedback").hide();
    order_address.siblings(".invalid-feedback").hide();
    order_commune.siblings(".invalid-feedback").hide();
    order_province.siblings(".invalid-feedback").hide();
    order_district.siblings(".invalid-feedback").hide();
    area.parent().siblings(".invalid-feedback").hide();
    status.parent().siblings(".invalid-feedback").hide();

    var validate_number = /^\d+$/;
    var breakStatus = ["callerror", "rejected", "trashed","callback"];

    if(!order_name.val().trim() && breakStatus.indexOf(status.val()) < 0){
      order_name.siblings(".invalid-feedback").show();
    } else if(!order_phone.val().trim()  && breakStatus.indexOf(status.val()) < 0){
      order_phone.siblings(".invalid-feedback").show();
    } else if((!number.val().trim() || !validate_number.test(number.val().trim()) || number.val().trim() <= 0 ) && breakStatus.indexOf(status.val()) < 0){
      number.siblings(".invalid-feedback").show();
    } else if((!price_sell.val().trim() || !validate_number.test(price_sell.val().trim()) || price_sell.val().trim() <= 0)  && breakStatus.indexOf(status.val()) < 0){
      price_sell.siblings(".invalid-feedback").show();
    } else if (!order_address.val() && breakStatus.indexOf(status.val()) < 0){
      order_address.siblings(".invalid-feedback").show();
    } else if (!order_commune.val() && breakStatus.indexOf(status.val()) < 0){
      order_commune.siblings(".invalid-feedback").show();
    } else if (!order_district.val() && breakStatus.indexOf(status.val()) < 0){
      order_district.siblings(".invalid-feedback").show();
    } else if (!order_province.val() && breakStatus.indexOf(status.val()) < 0){
      order_province.siblings(".invalid-feedback").show();
    } else if (!area.val()){
      area.parent().siblings(".invalid-feedback").show();
    } else if(!status.val().trim()  && breakStatus.indexOf(status.val()) < 0){
      status.parent().siblings(".invalid-feedback").show();
    } else {
      return true;
    }

    return false;
  }

<?php } ?>

</script>
<!--
<script type="text/javascript">
  $("#order_province").on("change",function(){
    $.ajax({
        url: '<?=$_url;?>/ajax.php?act=districts',
        dataType: 'json',
        data: {id: $("#order_province option:selected").attr("data-provinceid") },
        type: 'post',
        success: function (response) {
          $(".loader-overlay").hide();
          if(response){
            var insert_html = '';
            for(var i=0; i < response.length;i++)
              insert_html += '<option value="'+response[i]+'">'+response[i]+'</option>';
            $("#order_district").html(insert_html);
          }
        },
        error: function (response) {
          $(".loader-overlay").hide();
        }
    });
  });
</script>
-->
<?php


end:


?>
