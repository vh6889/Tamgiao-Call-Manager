<?php
$cancel_order = isAller() ? true : false;

if (isBanned()) {
    echo '<div class="alert alert-danger" role="alert">
        <h4 class="alert-heading">Có lỗi xảy ra!</h4>
        <p>Tài khoản của bạn hiện tại không thể nhận đơn hàng mới do đang bị cấm.</p>
        <hr>
        <p class="mb-0">Vui lòng liên hệ trưởng nhóm để biết thêm chi tiết.</p>
      </div>';

    goto end;
}

if (!isCaller()) {
    echo '<div class="alert alert-danger" role="alert">
        <h4 class="alert-heading">An error occurred!</h4>
        <p>Oops!!! You have just accessed a link that does not exist on the system or you do not have enough permissions to access.</p>
        <hr>
        <p class="mb-0">Please return to the previous page.</p>
      </div>';

    goto end;
}

$_calling = getCalling();

$_info = infoOrder($_calling['id']);

if ($_id && !$_info) {
    echo '<div class="alert alert-danger" role="alert">
        <h4 class="alert-heading">Lỗi!</h4>
        <p>Đơn hàng không tồn tại hoặc đã bị xóa</p>
        <hr>
        <p class="mb-0">Vui lòng quay trở lại trang trước.</p>
      </div>';

    goto end;
}

$_group = getGroup($_calling['group']);

if (!$_calling) {

    echo '<div class="note note-danger my-3">
            <b>Không có đơn hàng đang gọi nào.</b>
       </div>';
    goto end;
}

$offer_ship = getInfoById('core_offers', 'id,name,price_ship', $_info['offer']);
$order_name  = isset($_POST['order_name']) ? trim($_POST['order_name']) : trim($_info['order_name']);
$order_phone = isset($_POST['order_phone']) ? trim($_POST['order_phone']) : trim($_info['order_phone']);
$number      = isset($_POST['number']) ? trim($_POST['number']) : trim($_info['number']);
if (isset($_POST['free_ship'])) {
    $free_ship = 0;
} else {
    $free_ship = 1;
}
$price  = isset($_POST['price']) ? trim($_POST['price']) : trim($_info['price']);
$price_sell  = isset($_POST['price_sell']) ? trim($_POST['price_sell']) : trim($_info['price_sell']);
$order_address = isset($_POST['order_address']) ? trim($_POST['order_address']) : trim($_info['order_address']);
$order_commune = isset($_POST['order_commune']) ? trim($_POST['order_commune']) : trim($_info['order_commune']);
$order_province = isset($_POST['order_province']) ? trim($_POST['order_province']) : trim($_info['order_province']);
$order_district = isset($_POST['order_district']) ? trim($_POST['order_district']) : trim($_info['order_district']);
$area = isset($_POST['area']) ? trim($_POST['area']) : trim($_info['area']);
$note = isset($_POST['note']) ? trim($_POST['note']) : trim($_info['note']);
$status = isset($_POST['status']) ? trim($_POST['status']) : trim($_info['status']);

//$provinces = $_db->query("select * from `core_provinces` order by `provinceid` ")->fetch_array();
//$districts = $_db->query("select * from `core_districts` where ".($order_district ? " `name`='".escape_string($order_district)."' ":" `provinceid`='".$provinces[0]['provinceid']."' ")."  order by `name` ")->fetch_array();

if (isset($_POST['cancelOrder']) && $cancel_order == true) {
    
    if ($_db->exec_query("UPDATE `core_orders` set `group`='',`payout_leader`=0,`payout_member`=0,`deduct_leader`=0,`deduct_member`=0,`user_call`='',`call_time`=0,`status`='uncheck',`r_hold`=0,`r_deduct`=0,`r_approve`=0 where `id`='" . escape_string($_info['id']) . "' and `status` in ('calling','callerror') ")) {

        $_statusmessage['type'] = 'success';
        $_statusmessage['message'] = 'Hủy đơn gọi thành công.';

        header('Location: ?route=newOrder');
    } else {
        $_statusmessage['type'] = 'warning';
        $_statusmessage['message'] = 'Có lỗi xảy ra khi hủy đơn gọi.';
    }
} else if (isset($_POST['submit'])) {
    $check = array("trashed", "rejected", "callerror"); //,"callback"

    $permissions = array();

    if (isCaller()) {
        $permissions[] = "pending";
        $permissions[] = "rejected";
        //$permissions[] = "callback";
        $permissions[] = "callerror";
        $permissions[] = "shipdelay";
        $permissions[] = "rejected";
        $permissions[] = "trashed";
    }

    if (!in_array($status, $permissions)) {
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Bạn không có quyền update trạng thái \'' . $status . '\'.';
    } else if (empty($order_name) && !in_array($status, $check)) {
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Tên người mua không được bỏ trống.';
    } else if (empty($order_phone)  && !in_array($status, $check)) {
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Số điện thoại không được bỏ trống.';
    } else if (empty($order_address)  && !in_array($status, $check)) {
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Địa chỉ người mua không được bỏ trống.';
    } else if (empty($order_commune)  && !in_array($status, $check)) {
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Phường/Xã không được bỏ trống.';
    } else if (empty($order_district)  && !in_array($status, $check)) {
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Quận/Huyện không được bỏ trống.';
    } else if (empty($order_province)  && !in_array($status, $check)) {
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Tỉnh/Thành phố không được bỏ trống.';
    } else if ($number < 0 || !is_numeric($number) && !in_array($status, $check)) {
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Số lượng mua không hợp lệ';
    } else if (($price < 0  || !is_numeric($price)) && $status != 'uncheck' && !in_array($status, $check)) {
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Giá sản phẩm phải lớn hơn 0.';
    } else if (($price_sell < 0  || !is_numeric($price_sell)) && !in_array($status, $check)) {
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Tổng tiền phải lớn hơn 0.';
    } else if (!in_array($area, ['bac', 'trung', 'nam'])) {
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Vui lòng chọn ít nhất một khu vực.';
    } else if (empty($status)) {
        $_statusmessage['type'] = 'danger';
        $_statusmessage['message'] = 'Trạng thái đơn hàng không chính xác.';
    } else {
        $number = round($number, 0, PHP_ROUND_HALF_DOWN);
        if ($status == "pending" && $_info['user_call']) {
            $notifi_title = '<strong class="trigger green lighten-2 text-white">Thông báo chuyển đơn hàng</strong>';
            $notifi_text = '<strong>Đơn hàng: <a target="_blank" href="?route=editOrder&id=' . $_info['id'] . '"><span class="text-danger">#' . $_info['id'] . '</span></a> vừa được yêu cầu chuyển sang chuẩn bị đơn hàng với lí do: ' . _e($note) . '</strong>.';
            addNotification($notifi_title, $notifi_text, $_info['user_call']);
        }
        if ($status == "pending" && $_info['user_ship']) {
            $notifi_title = '<strong class="trigger green lighten-2 text-white">Thông báo chuyển đơn hàng</strong>';
            $notifi_text = '<strong>Đơn hàng: <a target="_blank" href="?route=editOrder&id=' . $_info['id'] . '"><span class="text-danger">#' . $_info['id'] . '</span></a> vừa được yêu cầu chuyển sang chuẩn bị đơn hàng với lí do: ' . _e($note) . '</strong>.';
            addNotification($notifi_title, $notifi_text, $_info['user_ship']);
        }

        if ($_db->exec_query("update `core_orders` set `order_name`='" . escape_string($order_name) . "',`order_phone`='" . escape_string($order_phone) . "',`price`='" . escape_string($price) . "',`price_sell`='" . escape_string($price_sell) . "',`number`='" . escape_string($number) . "',`free_ship`='" . $free_ship . "',`order_address`='" . escape_string($order_address) . "',`order_commune`='" . escape_string($order_commune) . "',`order_province`='" . escape_string($order_province) . "',`order_district`='" . escape_string($order_district) . "',`status`='" . escape_string($status) . "',`note`='" . escape_string($note) . "',`area`='" . escape_string($area) . "' where `id`='" . $_info['id'] . "'  ")) {

            $_info2 = infoOrder($_info['id']);
            if (!$_info2['order_commune']) {
                $_db->exec_query("update `core_orders` set `order_commune`='" . escape_string($_POST['order_commune']) . "' where `id`='" . $_info['id'] . "'  ");
            }
            $_statusmessage['type'] = 'success';
            $_statusmessage['message'] = 'Cập nhật đơn hàng thành công.';
            if ($_info['status'] != $status && $status == "pending") {
                
                if($_info['payout_type']=='percent') {
                    $payout = $price*$number * $_info['payout_member']/100;
                    $payout_group = $price*$number * $_info['payout_leader']/100;
                }else{
                    $payout = $number * $_info['payout_member'];
                    $payout_group = $number * $_info['payout_leader'];
                }

                curl_sendNotifi($_url . '/sendNotifi.php?act=pending&id=' . $_info['id']);

                if ($_info['r_hold'] <= 0)
                    if ($_db->query("update `core_users` set `revenue_pending`=`revenue_pending`+'" . $payout . "' where `id`='" . $_user['id'] . "' ")) {
                        $_db->exec_query("update `core_groups` set `revenue_pending`=`revenue_pending`+'" . $payout_group . "' where `id` = '" . $_user['group'] . "'");
                        $_db->query("update `core_orders` set `r_hold`='1',`r_approve`='0',`r_deduct`='0' where `id`='" . $_info['id'] . "' ");
                    }
            }

            header('Location: ?route=newOrder');
        } else {
            $_statusmessage['type'] = 'warning';
            $_statusmessage['message'] = 'Có lỗi xảy ra vui lòng thử lại sau ít phút.';
        }
    }
}

?>


<h2 class="section-heading mb-4"><span class="badge <?= getBgOrder($_info['status']); ?>"><?= $_info['status']; ?></span> Đơn hàng đang gọi </h2>

<section class="row mb-5 pb-3">
    <div class="col-md-12 mx-auto white z-depth-1">

        <form method="POST" onsubmit="return submitUpdate();">

            <div class="row px-2">
                <div class="col-md-12">
                    <?php if (!empty($_statusmessage)) : ?>
                        <div class="alert alert-<?= $_statusmessage["type"]; ?> lert-dismissible fade show" role="alert">
                            <?= $_statusmessage["message"]; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-3" style="margin-bottom:10px;">
                    <label>ID đơn hàng:</label>
                    <input type="text" class="form-control" value="#<?= _e($_info['id']); ?>" disabled>
                    <?php if ($check = checkOrder($_info)) {
                        echo '<b class="number">' . _e($arr['order_phone']) . '</b>';
                        if ($check) {
                            foreach ($check as $dup) {
                                echo '<p><small>- Trùng đơn #<a target="_blank" href="?route=editOrder&id=' . $dup['id'] . '"><b class="text-danger">' . $dup['id'] . ' (' . $dup['status'] . ')</b></a></small></p>';
                            }
                        }
                    }
                    ?>
                </div>
                <div class="col-md-3" style="margin-bottom:10px;">
                    <label>Tên sản phẩm:</label>
                    <input type="text" class="form-control" value="<?= _e($_info['offer_name']); ?>" disabled>
                    <?php 
                        if (isset($_info['offer'])) $offer = getOffer($_info['offer']);
                        echo $offer['offer_link']!='' ? '<a href="'.$offer['offer_link'].'" target="__blank"><i class="fas fa-external-link-alt ml-1"></i></a>' : '';
                    ?>
                </div>
                <div class="col-md-3" style="margin-bottom:10px;">
                    <label>Giá sản phẩm:</label>
                    <?php
                    if ($offer) :
                        $prices = explode('|', $offer['price']);
                        $inputClass = count($prices) > 1 ? 'd-none' : '';
                        $selectClass = count($prices) > 1 ? '' : 'd-none';
                        $selectedPrice = $_info['price'];
                    ?>
                        <input type="text" class="form-control <?= $inputClass; ?>" id="price" data-price="<?= $selectedPrice; ?>" value="<?= $selectedPrice; ?>k" disabled />
                        <input name="price" class="d-none" value="<?= $selectedPrice; ?>" />
                        <select id="price-selector" class="form-control  <?= $selectClass; ?>">
                            <?php
                            foreach ($prices as $price) {
                            ?>
                                <option value="<?= $price ?>" <?php if ($price == $selectedPrice) echo 'selected' ?>><?= $price ?>k</option>
                            <?php
                            }
                            ?>
                        </select>
                    <?php endif; ?>
                </div>
                <div class="col-md-3" style="margin-bottom:10px;">
                    <label>Ngày đặt hàng:</label>
                    <input type="text" class="form-control" value="<?= date('Y/m/d H:i', $_info['time']); ?>" disabled>
                </div>
            </div>

            <div class="row px-2">

                <div class="col-md-3" style="margin-bottom:10px;">
                    <label>Tên người đặt mua:</label>
                    <input type="text" class="form-control" id="order_name" name="order_name" value="<?= _e($order_name); ?>">
                    <div class="invalid-feedback">Tên người đặt mua không được bỏ trống</div>
                </div>
				
                <div class="col-md-3" style="margin-bottom:10px;">
                    <label>Số điện thoại:</label>
                    <input type="text" class="form-control" id="order_phone" name="order_phone" value="<?= _e($order_phone); ?>">
                    <div class="invalid-feedback">Số điện thoại không được bỏ trống.</div>
                </div>
				
                <div class="col-md-3" style="margin-bottom:10px;">
                    <label>Số lượng mua:</label>
                    <input id="number" type="number" class="form-control" name="number" value="<?= _e($number); ?>">
                    <div class="invalid-feedback">Số lượng mua không hợp lệ.</div>
                </div>
				
                <div class="col-md-3" style="margin-bottom:10px;">
                    <label>Tổng tiền(k):</label>
                    <input id="price_sell" type="number" class="form-control" name="price_sell" value="<?= _e($price_sell); ?>">
                    <div class="invalid-feedback">Tổng tiền phải lớn hơn 0.</div>
                    <input id="free_ship1" style="opacity:1;position:static;pointer-events:visible;" class="" type="checkbox" name="free_ship" value="0"> Miễn phí ship
                </div>
            </div>
			
            <div class="px-2">
                <div class="form-group">
                    <label>Địa chỉ người mua (số nhà,đường/phố,xóm,thôn):</label>
                    <textarea class="form-control" id="order_address" name="order_address" rows="2"><?= _e($order_address); ?></textarea>
                    <div class="invalid-feedback">Địa chỉ người mua không được bỏ trống.</div>
                </div>
            </div>
			
            <div class="row px-2">

                <div class="col-md-3" style="margin-bottom:10px;">
                    <label>Phường/Xã:</label>
                    <input type="text" class="form-control" id="order_commune" name="order_commune" value="<?= _e($order_commune); ?>">
                    <div class="invalid-feedback">Phường/Xã không được bỏ trống</div>
                </div>
				
                <div class="col-md-3" style="margin-bottom:10px;">
                    <label>Quận/Huyện:</label>
                    <input type="text" class="form-control" id="order_district" name="order_district" value="<?= _e($order_district); ?>">
                    <div class="invalid-feedback">Quận/Huyện không được bỏ trống</div>
                </div>
				
                <div class="col-md-3" style="margin-bottom:10px;">
                    <label>Tỉnh/Thành phố:</label>
                    <input type="text" class="form-control" id="order_province" name="order_province" value="<?= _e($order_province); ?>">
                    <div class="invalid-feedback">Tỉnh/Thành phố không được bỏ trống</div>
                </div>
				
                <div class="col-md-3" style="margin-bottom:10px;">
                    <label>Khu vực:</label>
                    <select id="area" name="area" class="mdb-select">
                        <option selected value="bac"> Miền bắc </option>
                        <option <?= ($area == "trung" ? 'selected' : ''); ?> value="trung"> Miền trung</option>
                        <option <?= ($area == "nam" ? 'selected' : ''); ?> value="nam"> Miền nam</option>
                    </select>
                    <div class="invalid-feedback">Vui lòng chọn một khu vực.</div>
                </div>
            </div>
			
            <div class="px-2">
                <div class="form-group">
                    <label>Ghi chú:</label>
                    <textarea class="form-control" name="note" rows="3"><?= _e($note); ?></textarea>
                </div>
				
                <div class="form-group">
                    <label>Tình trạng đơn:</label>
                    <select id="status" name="status" class="mdb-select">
                        <option selected value="pending"> Pending (Chuẩn bị hàng)</option>
                        <!-- <option <?= ($status == "callback" ? 'selected' : ''); ?> value="callback"> Call Back (Hẹn gọi lại)</option> -->
                        <option <?= ($status == "callerror" ? 'selected' : ''); ?> value="callerror"> Call Error (Không gọi được)</option>
                        <option <?= ($status == "shipdelay" ? 'selected' : ''); ?> value="shipdelay"> ShipDelay (Hẹn ngày giao hàng)</option>
                        <option <?= ($status == "rejected" ? 'selected' : ''); ?> value="rejected"> Reject (Từ chối mua hàng)</option>
                        <option <?= ($status == "trashed" ? 'selected' : ''); ?> value="trashed"> Trash (Đơn hàng rác)</option>
                    </select>
                    <div class="invalid-feedback">Vui lòng chọn một trạng thái.</div>
                </div>
				
                <div class="form-group float-right">
                    <?php if ($cancel_order == true) { ?>
                        <input class="btn btn-danger btn-md btn-rounded waves-effect waves-light" name="cancel" type="button" value="Cancel Order" data-toggle="modal" data-target="#cancelOrder">
                    <?php } ?>
                    <input class="btn btn-dark btn-md btn-rounded waves-effect waves-light" name="submit" type="submit" value="Save Order">
                </div>
            </div>
        </form>
    </div>
</section>

<?php if ($cancel_order == true) { ?>
    <!--Modal: modalConfirmDelete-->
    <div class="modal fade" id="cancelOrder" tabindex="-1" role="dialog" aria-hidden="true">
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
                        <button class="btn  btn-outline-danger" type="submit" name="cancelOrder">Hủy gọi</button>
                    </form>
                    <button class="btn  btn-danger waves-effect" data-dismiss="modal">Đóng</button>
                </div>
            </div>
            <!--/.Content-->
        </div>
    </div>
    <!--Modal: modalConfirmDelete-->
<?php } ?>
<script type="text/javascript">
    function loadingPriceSell() {
        let shipFee = $("#free_ship1").is(':checked') ? 0 : <?= $offer_ship['price_ship']; ?>;
        let qty = $("#number").val().replace(/\D/g, "");
        let price = $("#price").attr("data-price").trim();

        $("#price_sell").val(qty * price + shipFee);
        $('input[name="price"]').val(price);
    }

    $("#number").keyup(function() {
        loadingPriceSell();
    });

    $("#number").on("change", function() {
        loadingPriceSell();
    });

    $("#price-selector").on("change", function() {
        let price = $(this).val();
        $("#price").attr('data-price', price);
        $("#price").val(price + 'k');
        loadingPriceSell();
    });

    loadShip();

    function loadShip() {
        $("#free_ship1").on("change", function() {
            var thisShip = $(this).val();
            var price_sell = parseInt($("#price_sell").val());
            if ($(this).is(':checked')) {
                var sum_self = parseInt(price_sell - <?php echo $offer_ship['price_ship']; ?>);
                $("#price_sell").val(sum_self);
            } else {
                var sum_self = parseInt(price_sell + <?php echo $offer_ship['price_ship']; ?>);
                $("#price_sell").val(sum_self);
            }
        });
    }

    function submitUpdate() {

        var order_name = $("#order_name"),
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
        var breakStatus = ["callerror", "rejected", "trashed", "callback"];

        if (!order_name.val().trim() && breakStatus.indexOf(status.val()) < 0) {
            order_name.siblings(".invalid-feedback").show();
        } else if (!order_phone.val().trim() && breakStatus.indexOf(status.val()) < 0) {
            order_phone.siblings(".invalid-feedback").show();
        } else if ((!number.val().trim() || !validate_number.test(number.val().trim()) || number.val().trim() <= 0) && breakStatus.indexOf(status.val()) < 0) {
            number.siblings(".invalid-feedback").show();
        } else if ((!price_sell.val().trim() || !validate_number.test(price_sell.val().trim()) || price_sell.val().trim() <= 0) && breakStatus.indexOf(status.val()) < 0) {
            price_sell.siblings(".invalid-feedback").show();
        } else if (!order_address.val() && breakStatus.indexOf(status.val()) < 0) {
            order_address.siblings(".invalid-feedback").show();
        } else if (!order_commune.val() && breakStatus.indexOf(status.val()) < 0) {
            order_commune.siblings(".invalid-feedback").show();
        } else if (!order_district.val() && breakStatus.indexOf(status.val()) < 0) {
            order_district.siblings(".invalid-feedback").show();
        } else if (!order_province.val() && breakStatus.indexOf(status.val()) < 0) {
            order_province.siblings(".invalid-feedback").show();
        } else if (!area.val()) {
            area.parent().siblings(".invalid-feedback").show();
        } else if (!status.val() && breakStatus.indexOf(status.val()) < 0) {
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
        url: '<?= $_url; ?>/ajax.php?act=districts',
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