<?php


if(isBanned()){

  echo '<div class="alert alert-danger" role="alert">
        <h4 class="alert-heading">Có lỗi xảy ra!</h4>
        <p>Tài khoản của bạn hiện tại không thể nhận đơn hàng mới do đang bị cấm.</p>
        <hr>
        <p class="mb-0">Vui lòng liên hệ trưởng nhóm để biết thêm chi tiết.</p>
      </div>';

  goto end;
}

if(!isColler() && !isPublisher()){

  echo '<div class="alert alert-danger" role="alert">
        <h4 class="alert-heading">An error occurred!</h4>
        <p>Oops!!! You have just accessed a link that does not exist on the system or you do not have enough permissions to access.</p>
        <hr>
        <p class="mb-0">Please return to the previous page.</p>
      </div>';

  goto end;
}


$_group = getGroup($_user['group']);
$_offers = getOffer();


if(!$_group['id']){

  echo '<div class="alert alert-danger" role="alert">
        <h4 class="alert-heading">Có lỗi xảy ra!</h4>
        <p>Nhóm không tồn tại hoặc đã bị xóa.</p>
        <hr>
        <p class="mb-0">Vui lòng liên hệ quản trị viên để biết thêm chi tiết.</p>
      </div>';

  goto end;
}

$offer      = isset($_POST['offer'])? getOffer(trim($_POST['offer'])):'';
$order_name  = isset($_POST['order_name'])?trim($_POST['order_name']):'';
$order_phone = isset($_POST['order_phone'])? trim($_POST['order_phone']):'';
$number      = isset($_POST['number'])?trim($_POST['number']):'';
$price  = isset($_POST['price'])?trim($_POST['price']):'';
$price_sell  = isset($_POST['price_sell'])?trim($_POST['price_sell']):'';
$order_address = isset($_POST['order_address'])?trim($_POST['order_address']):'';
$order_commune = isset($_POST['order_commune'])?trim($_POST['order_commune']):'';
$order_province = isset($_POST['order_province'])?trim($_POST['order_province']):'';
$order_district = isset($_POST['order_district'])?trim($_POST['order_district']):'';
$area = isset($_POST['area'])?trim($_POST['area']):'';
$note = isset($_POST['note'])?trim($_POST['note']):'';
$status = isset($_POST['status'])?trim($_POST['status']):'';
//$provinces = $_db->query("select * from `core_provinces` order by `provinceid` ")->fetch_array();
//$districts = $_db->query("select * from `core_districts` where ".($order_district ? " `name`='".escape_string($order_district)."' ":" `provinceid`='".$provinces[0]['provinceid']."' ")."  order by `name` ")->fetch_array();

if(isset($_POST['submit'])){

      $check = array();//,"callback"

      $permissions = array();

      if(isPublisher()) {
        $permissions[] = "uncheck";
      }
      if(isColler() || isPublisher()){
        $permissions[] = "pending";
      }
      
      if(!in_array($status, $permissions)){
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Bạn không có quyền update trạng thái \''.$status.'\'.';
      } else if(!$offer['name']){
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Vui lòng chọn một sản phẩm';
      } else if(empty($order_name)){
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Tên người mua không được bỏ trống.';
      } else if(!check_phone($order_phone)){
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Số điện thoại không hợp lệ.';
      } else if(empty($order_address) && $status!='uncheck' && !in_array($status, $check)){
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Địa chỉ người mua không được bỏ trống.';
      } else if(empty($order_commune) && $status!='uncheck' && !in_array($status, $check)){
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Phường/Xã không được bỏ trống.';
      } else if(empty($order_district) && $status!='uncheck' && !in_array($status, $check)){
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Quận/Huyện không được bỏ trống.';
      } else if(empty($order_province) && $status!='uncheck' && !in_array($status, $check)){
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Tỉnh/Thành phố không được bỏ trống.';
      } else if($number < 0 || !is_numeric($number) && $status!='uncheck' && !in_array($status, $check)){
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Số lượng mua không hợp lệ';
      } else if(($price < 0  || !is_numeric($price)) && $status!='uncheck' && !in_array($status, $check)){
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Giá sản phẩm phải lớn hơn 0.';
      } else if(($price_sell < 0  || !is_numeric($price_sell)) && $status!='uncheck' && !in_array($status, $check)){
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Tổng tiền phải lớn hơn 0.';
      } else if(!in_array($area, ['bac','trung','nam']) && $status!='uncheck'){
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Vui lòng chọn ít nhất một khu vực.';
      } else if(empty($status)){
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Trạng thái đơn hàng không chính xác.';
      } else {

        /* Get user's key */
        $ukey = getUkey();
        
        $number = round($number, 0, PHP_ROUND_HALF_DOWN);

        $user_call = ($status=='uncheck') ? '' : escape_string($_user['id']);
        $call_time = ($status=='uncheck') ? 0 : time();

        $uPayout = $_user['group_payout'];
        $gPayout = $_group['payout'];
        $orderPayoutType =  $_group['payout_type'];
        if(isset($offer['payout']) && $offer['payout']) {
          $uPayout = $offer['payout'];
          $gPayout = $offer['payout'];
          $orderPayoutType =  $offer['payout_type'];
        }
        if($orderPayoutType=='percent') {
          $payout = $price*$number * $uPayout/100;
          $payout2 = $price*$number * $gPayout/100;
        }else{
          $payout = $number * $uPayout;
          $payout2 = $number * $gPayout;
        }

        if($_db->exec_query("insert into `core_orders` set
            `offer`='".escape_string($offer['id'])."',
            `offer_name`='".escape_string($offer['name'])."',
            `group`='".escape_string($_group['id'])."',
            `price`='".escape_string($price)."',
            `price_deduct`='".escape_string($offer['price_deduct'])."',
            `price_sell`='".escape_string($price_sell)."',
            `number`='".escape_string($number)."',
            `status`='".escape_string($status)."',
            `time`='".time()."',
            `date`='".date('Y/m/d',time())."',
            `order_name`='".escape_string($order_name)."',
            `order_phone`='".escape_string($order_phone)."',
            `order_address`='".escape_string($order_address)."',
            `order_commune`='".escape_string($order_commune)."',
            `order_province`='".escape_string($order_province)."',
            `order_district`='".escape_string($order_district)."',
            `area`='".escape_string($area)."',
            `note`='".escape_string($note)."',
            `payout_leader`='".escape_string($payout2)."',
            `payout_member`='".escape_string($payout)."',
            `payout_type`='".escape_string($orderPayoutType)."',
            `deduct_leader`='".escape_string($_group['deduct'])."',
            `deduct_member`='".escape_string($_user['group_deduct'])."',
            `user_call`='".$user_call."',
            `call_time`='".$call_time."',
            `r_hold`='".($status == "pending" ? 1 : 0)."',
            `r_approve`='0',
            `r_deduct`='0',
            `typeOrder`='".($_group['type'] == "collaborator" ? 1 : 0)."',
            `ukey`='".$ukey."' ")){

          if($status == "pending"){
            if($_db->query("update `core_users` set `revenue_pending`=`revenue_pending`+'".$payout."' where `id`='".$_user['id']."' "))
              $_db->exec_query("update `core_groups` set `revenue_pending`=`revenue_pending`+'".$payout2."' where `id` = '".$_group['id']."'");            
          }


          $_info = infoOrder($_db->insert_id());
          if(!$_info['order_commune'])
            $_db->exec_query("update `core_orders` set `order_commune`='".escape_string($_POST['order_commune'])."' where `id`='".$_info['id']."'  ");

          $_statusmessage['type'] = 'success';
          $_statusmessage['message'] = 'Tạo đơn hàng thành công.';

          header('Location: ?route=order&type=pending');


        } else {
          $_statusmessage['type'] = 'warning';
          $_statusmessage['message'] = 'Có lỗi xảy ra vui lòng thử lại sau ít phút.';
        }
      }
}

?>

<section class="row">
  <div class="col-md-6">
    <h2 class="section-heading mb-4"><span class="badge green">New</span> Tạo đơn hàng mới </h2>
  </div>
  <div class="col-md-6 searchBox">
    <div class="input-group form-sm form-2 pl-0 mb-3">
      <input id="searchAll" class="form-control my-0 py-1 bg-white" type="text" placeholder="Search by Name or Phone" aria-label="Search">
      <div class="input-group-append">
        <span class="input-group-text dark lighten-3" id="basic-text1"><i class="fas fa-search text-grey" aria-hidden="true"></i></span>
      </div>
      <div id="result_box"> </div>
    </div>
  </div>
</section>

<section class="row mb-5 pb-3">
    <div class="col-md-12 mx-auto white z-depth-1">

      <form method="POST" onsubmit="return submitUpdate();">
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
          <div class="col-md-6">
            <label>Sản phẩm:</label>
            <select id="offer" class="mdb-select" name="offer">
              <option data-price="0" selected value=''>Chọn một sản phẩm</option>
              <?php if($_group['offers'] || isAller()){
                foreach ($_offers as $of) {
                  if(preg_match("#\|".$of['id'].",#si", $_group['offers']) || isAller())
                    echo '<option '.(isset($offer['id']) && $offer['id'] == $of['id'] ? 'selected' :'').' value="'.$of['id'].'" data-price="'.$of['price'].'" '.($offer == $of['id'] ? 'selected' : '').'>'._e($of['name']).'</option>';
                }
              } ?>
            </select>            
            <div class="invalid-feedback invalid-offer">Vui lòng chọn 1 sản phẩm</div>
          </div>
          <div class="col-md-6">
            <label>Giá sản phẩm:</label>
            <?php
                if(isset($offer['id'])):
                    $price = explode('|',$offer['price']);
            ?>
                <input type="text" class="form-control" id="price"  data-price="<?=$price[0];?>" value="<?=$price[0];?>k" disabled />
                <input name="price" class="d-none" value="<?=$price[0];?>" />
            <?php else: ?>
                <input type="text" class="form-control" id="price" data-price="0" value="0k" disabled />
                <input name="price" class="d-none" value="0" />
            <?php endif; ?>
            <select id="price-selector" class="form-control d-none"></select>
          </div>
        </div>

      <div class="px-2">
        <div class="form-group mt-3">
            <label>Tên người đặt mua:</label>
            <input type="text" class="form-control" id="order_name" name="order_name" value="<?=_e($order_name);?>">
            <div class="invalid-feedback">Tên người đặt mua không được bỏ trống</div>
        </div>

        <div class="form-group mt-3">
            <label>Số điện thoại:</label>
            <input type="text" class="form-control" id="order_phone" name="order_phone" value="<?=_e($order_phone);?>">
            <div class="invalid-feedback">Số điện thoại không được bỏ trống.</div>
        </div>

        <div class="form-group mt-3">
            <label>Số lượng mua:</label>
            <input id="number" type="number" class="form-control" name="number" value="<?=_e($number);?>">
            <div class="invalid-feedback">Số lượng mua không hợp lệ.</div>
        </div>

        <div class="form-group mt-3">
            <label>Tổng tiền(k):</label>
            <input id="price_sell" type="number" class="form-control" name="price_sell" value="<?=_e($price_sell);?>">
            <div class="invalid-feedback">Tổng tiền phải lớn hơn 0.</div>
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
          <select id="area" name="area" class="mdb-select">
            <option selected value="bac"> Miền bắc </option>
            <option <?=($area == "trung" ? 'selected':'');?> value="trung"> Miền trung</option>
            <option <?=($area == "nam" ? 'selected':'');?> value="nam"> Miền nam</option>
          </select>
          <div class="invalid-feedback">Vui lòng chọn một khu vực.</div>
        </div>

        <div class="form-group">
          <label>Ghi chú:</label>
          <textarea class="form-control" name="note" rows="2"><?=_e($note);?></textarea>
        </div>

        <div class="form-group">
          <label>Tình trạng đơn:</label>
          <select id="status" name="status" class="mdb-select">
            <option selected value="uncheck"> New Orders (Đơn hàng mới)</option>
            <option value="pending"> Pending (Chuẩn bị hàng)</option>
            <option <?=($status == "shipdelay" ? 'selected':'');?> value="shipdelay"> ShipDelay (Hẹn ngày giao hàng)</option>
          </select>
          <div class="invalid-feedback">Vui lòng chọn một trạng thái.</div>
        </div>
        <div class="form-group float-right">
          <input class="btn btn-dark btn-md btn-rounded waves-effect waves-light" name="submit" type="submit" value="Save Order">
        </div>
      </form>
    </div>
    </div>
</section>


<script type="text/javascript">

function loadingPriceSell(){
    // let shipFee = $("#free_ship1").is(':checked') ? 0 : <?= $offer_ship['price_ship']; ?>;
    let qty = $("#number").val().replace(/\D/g, "");
    let price = $("#price").attr("data-price").trim();

    // $("#price_sell").val(qty*price+shipFee);
    $("#price_sell").val(qty*price);
    $('input[name="price"]').val(price);
}

  $("#number").keyup(function () {
    loadingPriceSell();
  });

  $("#number").on("change",function () {
    loadingPriceSell();
  });


  $("#offer").on("change",function() {
    var selected = $(this).find('option:selected');
    let prices = selected.attr("data-price").split('|');
    if(prices.length>1) {
        cloneHtml = '';
        for (i = 0; i < prices.length; i++) {
            selected = i==0 ? 'selected' : '';
            cloneHtml += '<option value="'+prices[i]+'" '+selected+'>'+prices[i]+'k</option>';
        }
        $("#price-selector").html(cloneHtml).removeClass('d-none').show();
        $("#price").attr('data-price',prices[0]).hide();
        $("#price").val(prices[0]+'k');
    } else {
        $("#price-selector").html('').hide();
        $("#price").attr('data-price',selected.attr("data-price"));
        $("#price").val(selected.attr("data-price")+'k');
    }
    loadingPriceSell();
  });

  $("#price-selector").on("change",function() {
    let price = $(this).val();
    $("#price").attr('data-price',price);
    $("#price").val(price+'k');
    loadingPriceSell();
  });

  function submitUpdate(){

    var offer = $("#offer"),
        order_name = $("#order_name"),
        order_phone = $("#order_phone"),
        number = $("#number"),
        price_sell = $("#price_sell"),
        order_address = $("#order_address"),
        order_commune = $("#order_commune"),
        order_province = $("#order_province"),
        order_district = $("#order_district"),
        area = $("#area"),
        note = $("#note"),
        status = $("#status");

    $(".invalid-offer").hide();
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
    var breakStatus = [];
// console.log(status);
    if(status.val()=='uncheck') {
        if(!offer.val()){
            $(".invalid-offer").show();
            return false;
        } else if(!order_name.val().trim()){
            order_name.siblings(".invalid-feedback").show();
            return false;
        } else if(!order_phone.val().trim()){
            order_phone.siblings(".invalid-feedback").show();
            return false;
        }
        return true;
    }

    if(!offer.val().trim() && breakStatus.indexOf(status.val()) < 0){
        $(".invalid-offer").show();
    } else if(!order_name.val().trim() && breakStatus.indexOf(status.val()) < 0){
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
    } else if(!status.val() && breakStatus.indexOf(status.val()) < 0){
      status.parent().siblings(".invalid-feedback").show();
    } else {
      return true;
    }

    return false;
  }

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
