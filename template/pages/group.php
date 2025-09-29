<?php


if(!isAdmin())
  $_id = '';

$id = $_id ? $_id : $_user['group'];

$_group = getGroup($id);
$_memebers = memberGroup($id);
$_offer = getOffer($_group['offer']);

$_group_payout_type = $_group['payout_type']=='percent' ? '%' : 'K';

if(!$_group){

  echo '<div class="alert alert-danger" role="alert">
        <h4 class="alert-heading">An error occurred!</h4>
        <p>Oops!!! You have just accessed a link that does not exist on the system or you do not have enough permissions to access.</p>
        <hr>
        <p class="mb-0">Please return to the previous page.</p>
      </div>';

  goto end;
}


$offers = getNameOffers($_group['offers']);
$html_offer = "";
foreach ($offers as $of)
  $html_offer .= '<a class="trigger dark lighten-2 text-white">'._e($of['name']).'</a>';

?>
<link rel="stylesheet" type="text/css" href="template/assets/css/addons/datatables.min.css">
<link rel="stylesheet" href="template/assets/css/addons/datatables-select.min.css">
<script type="text/javascript" src="template/assets/js/addons/datatables.min.js"></script>
<script type="text/javascript" src="template/assets/js/addons/datatables-select.min.js"></script>


<h2 class="section-heading mb-4">Group: <?=_e($_group['name']);?></h2>
<section class="row pb-3">
    <div class="col-md-12 mx-auto white z-depth-1">
      <?php if(isLeader() && !in_array($_group['type'],["shipping","publisher"])){ ?>

          <div class="body-price">
            <div class="price-1">
              G-Earning:
              <b class="text-dark"><?=addDotNumber($_group['revenue_approve']);?></b>
            </div>
            <div class="price-2">
              G-Hold:
              <b class="text-dark"><?=addDotNumber($_group['revenue_pending']);?></b>
            </div>
            <div class="price-3">
              G-Deduct:
              <b class="text-dark"><?=addDotNumber($_group['revenue_deduct']);?></b>
            </div>
          </div>
      <?php } ?>
      <div class="note note-warning my-3">
        <div><b>Sản phẩm: </b><span class="ml-2"><?=$html_offer;?></span></div>
        <div><b>Loại nhóm: </b><b class="text-danger"><?=typeGroup($_group['type']);?></b></div>
        <?php if(!in_array($_group['type'],["shipping"])){ ?>
        <div><b>Payout: </b><b class="trigger green lighten-2 text-white">+<?=$_group['payout'].$_group_payout_type;?></b><b class="text-dark-50">/sản phẩm</b></div>
        <div><b>Deduct: </b><b class="trigger red lighten-2 text-white">-<?=$_user['group_deduct'];?>k</b><b class="text-dark-50">/đơn hàng</b></div>
      <?php } ?>
      </div>

  <?php if($_group['note']){ ?>
    <div class="note note-info my-3">
      <?=$_group['note'];?>
    </div>
  <?php } ?>

    <?php if(isLeader()){ ?>
      <div class="text-right pb-2">
        <a class="btn btn-dark btn-sm btn-rounded waves-effect waves-light" href="?route=editNote<?=($_id?'&id='.$_id : '');?>">Edit Note<i class="fas fa-pen-square ml-1"></i></a>
      </div>
    <?php } ?>

    </div>
</section>


<h2 class="section-heading mb-4">Group Members</h2>


<?php if(isAdmin() || isLeader()){ ?>
<!-- Modal: newMember -->
<div class="modal fade" id="newMember" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <!--Header-->
      <div class="modal-header">
        <h4 class="modal-title">Add Member</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cancel">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <!--Body-->
      <div class="modal-body">

        <form class="noSubmit" autocomplete="off">
        <div class="row">
          <div class="col-md-12">
            <div class="md-form form-sm mb-0">
              <input id="i-username" type="text" class="form-control form-control-sm" autocomplete="oaaaaff">
              <label for="i-username" data-toggle="tooltip" title="Tên đăng nhập">Username</label>
              <div class="invalid-feedback">Tên đăng nhập không hợp lệ.</div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="md-form form-sm mb-0">
              <input id="i-password" type="password" class="form-control form-control-sm" autocomplete="aaaaaâ">
              <label for="i-password" data-toggle="tooltip" title="Mật khẩu">Password</label>
              <div class="invalid-feedback">Mật khẩu không được bỏ trống</div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="md-form form-sm mb-0">
              <input id="i-repassword" type="password" class="form-control form-control-sm" autocomplete="aaaa">
              <label for="i-repassword" data-toggle="tooltip" title="Nhập lại mật khẩu">rePassword</label>
              <div class="invalid-feedback">Mật khẩu nhập lại không chính xác.</div>
            </div>
          </div>
          <?php if(!in_array($_group['type'],["shipping"])){ ?>
          <div class="col-md-6">
            <div class="md-form form-sm payout-type-wrapper">
              <label for="i-payout" data-toggle="tooltip" title="Tiền trả cho mỗi đơn hàng giao thành công">Payout:</label>
              <div class="input-group mb-3">
                <input id="i-payout" type="text" class="form-control form-control-sm">
                <div class="input-group-append">
                    <span class="input-group-text" id="basic-addon2"><?php echo $_group_payout_type ?></span>
                </div>
              </div>
              <div class="invalid-feedback">Tiền chiết khấu phải lớn hơn hoặc bằng 0.</div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="md-form form-sm mb-0">
              <input id="i-deduct" type="text" class="form-control form-control-sm">
              <label for="i-deduct" data-toggle="tooltip" title="Tiền khấu trừ mỗi đơn hàng bị trả lại (đơn vị k)">Deduct:</label>
              <div class="invalid-feedback">Tiền khấu trừ phải lớn hơn hoặc bằng 0.</div>
            </div>
          </div>
        <?php } ?>

        </div>
        </form>

      </div>
      <!--Footer-->
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-dark" data-dismiss="modal">Cancel</button>
        <button class="btn btn-dark waves-effect waves-light" type="submit">Add Member</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal: newMember -->


<!-- Modal: editMember -->
<div class="modal fade" id="editMember" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <!--Header-->
      <div class="modal-header">
        <h4 class="modal-title">Edit Member<span id="countSelect"></span></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cancel">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <!--Body-->
      <div class="modal-body">

        <form class="noSubmit" autocomplete="off">
        <div class="row">

        <?php if(!in_array($_group['type'],["shipping"])){ ?>
          <div class="col-md-6">
            <div class="md-form form-sm payout-type-wrapper">
              <label for="e-payout" data-toggle="tooltip" title="Tiền trả cho mỗi đơn hàng giao thành công">Payout:</label>
              <div class="input-group mb-3">
                <input id="e-payout" type="text" class="form-control form-control-sm">
                <div class="input-group-append">
                    <span class="input-group-text" id="basic-addon2"><?php echo $_group_payout_type ?></span>
                </div>
              </div>
              <div class="invalid-feedback">Tiền chiết khấu phải lớn hơn hoặc bằng 0.</div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="md-form form-sm mb-0">
              <input id="e-deduct" type="text" class="form-control form-control-sm">
              <label for="e-deduct" data-toggle="tooltip" title="Tiền khấu trừ mỗi đơn hàng bị trả lại (đơn vị k)">Deduct:</label>
              <div class="invalid-feedback">Tiền khấu trừ phải lớn hơn hoặc bằng 0.</div>
            </div>
          </div>
        <?php } ?>


          <div class="col-md-12">
            <div class="custom-control custom-radio">
              <input type="radio" class="custom-control-input" id="e-active" name="groupOfDefaultRadios">
              <label class="custom-control-label text-success" for="e-active"><b>Active</b></label>
            </div>

            <div class="custom-control custom-radio">
              <input type="radio" class="custom-control-input red" id="e-banned" name="groupOfDefaultRadios" checked>
              <label class="custom-control-label text-danger" for="e-banned"><b>Banned</b></label>
            </div>
          </div>

          <?php if(isAdmin()){ ?>
          
          <div class="col-md-12">
            <hr>
            <div class="custom-control custom-switch">
              <input type="checkbox" class="custom-control-input" id="e-auto_ban">
              <label class="custom-control-label" for="e-auto_ban" data-toggle="tooltip" title="Tự động cấm tài khoản nếu như tỉ lệ lỗi vượt quá mức cho phép">Auto Banned</label>
            </div>
          </div>

          <div class="col-md-6">
            <div class="md-form form-sm mb-0">
              <input id="e-ban_limit" type="text" class="form-control form-control-sm">
              <label for="e-ban_limit" data-toggle="tooltip" title="Tổng số đơn hàng gần nhất muốn kiểm tra">Ban limit:</label>
              <div class="invalid-feedback">Tổng số đơn check phải lớn hơn hoặc bằng 0.</div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="md-form form-sm mb-0">
              <input id="e-ban_rate" type="text" class="form-control form-control-sm">
              <label for="e-ban_rate" data-toggle="tooltip" title="Tỉ lệ APR tối thiểu (%)">Ban rate:</label>
              <div class="invalid-feedback">Tỉ lệ Ban phải trong khoảng từ 0 đến 100.</div>
            </div>
          </div>
        <?php } ?>

        </div>
        </form>

      </div>
      <!--Footer-->
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-dark" data-dismiss="modal">Cancel</button>
        <button class="btn btn-dark waves-effect waves-light" type="submit">Save</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal: editMember -->

<!--Modal: modalConfirmDelete-->
<div class="modal fade" id="deleteMember" tabindex="-1" role="dialog"
  aria-hidden="true">
  <div class="modal-dialog modal-sm modal-notify modal-danger" role="document">
    <!--Content-->
    <div class="modal-content text-center">
      <!--Header-->
      <div class="modal-header d-flex justify-content-center">
        <p class="heading">Bạn thực sự muốn xóa?</p>
      </div>

      <!--Body-->
      <div class="modal-body">

        <i class="fas fa-times fa-4x animated rotateIn"></i>
        <div id="deleteBody" data-id="">
        </div>
        
		<div class="form-check <?=(!isAdmin() ? 'disabled' : ''); ?>">
		    <input type="checkbox" class="form-check-input" id="d-forever">
		    <label class="form-check-label" for="d-forever">Xóa vĩnh viễn</label>
		</div>

      </div>

      <!--Footer-->
      <div class="modal-footer flex-center">
        <button class="btn  btn-outline-danger" type="submit">Xóa</button>
        <button class="btn  btn-danger waves-effect" data-dismiss="modal">Hủy bỏ</button>
      </div>
    </div>
    <!--/.Content-->
  </div>
</div>
<!--Modal: modalConfirmDelete-->
<?php } ?>

<section class="row mb-5 pb-3">
    <div class="col-md-12 mx-auto white z-depth-1" style="overflow-x: hidden;">
      <?php if(isLeader()){ ?>
      <div class="text-right pb-2">
        <button role="btn-newMember" class="btn btn-dark btn-sm btn-rounded waves-effect waves-light" data-toggle="modal" data-target="#newMember">Add Member<i class="fas fa-plus-square ml-1"></i></button>
      </div>
    <?php } ?>
      <table id="dtBasicExample" class="table table-sm table-hover table-bordered" cellspacing="0" width="100%">
        <thead>
          <tr>
          <?php if(isLeader()){ ?>
            <th class="th-sm text-left">
              <div class="form-check text-center">
                <input type="checkbox" class="form-check-input" id="checkAll">
                <label class="form-check-label" for="checkAll"></label>
              </div>
            </th>
          <?php } ?>
            <th class="th-sm" data-toggle="tooltip" title="Thành viên nhóm">User</th>
            <th class="th-sm"></th>
          <?php if(!in_array($_group['type'],["shipping"])){ ?>
            <?php if(!in_array($_group['type'],["publisher"])){ ?>
                <th class="th-sm" data-toggle="tooltip" title="Cứ mỗi X đơn hàng mà tỉ lệ APR <= Y% thì sẽ tự động bị cấm đến khi được mở lại.">Auto Ban</th>
                <th class="th-sm" data-toggle="tooltip" title="Thu nhập thực tế">Earning (k)</th>
                <th class="th-sm" data-toggle="tooltip" title="Thu nhập ước tính">Hold (k)</th>
                <th class="th-sm" data-toggle="tooltip" title="Khấu trừ thu nhập">Deduct (k)</th>
            <?php } ?>
            <th class="th-sm" data-toggle="tooltip" title="Tỉ lệ chấp nhận đơn hàng">APO (%)</th>
            <?php if(!in_array($_group['type'],["publisher"])){ ?>
                <th class="th-sm" data-toggle="tooltip" title="Thu nhập bình quân mỗi đơn hàng">EPO (k)</th>
            <?php } ?>
            <th class="th-sm" data-toggle="tooltip" title="Sản phẩm đã bán được ban đầu">Pre Sales</th>
            <th class="th-sm" data-toggle="tooltip" title="Sản phẩm đã bán được thực tế">Sales</th>
          <?php } ?>
            <th class="th-sm" data-toggle="tooltip" title="Đơn hàng giao thành công">Approved</th>
          <?php if(!in_array($_group['type'],["shipping","collaborator"])){ ?>
            <th class="th-sm" data-toggle="tooltip" title="Đơn hàng đang gọi">Calling</th>
          <?php } ?>
            <th class="th-sm" data-toggle="tooltip" title="Đơn hàng chuẩn bị giao">Pending</th>
            <th class="th-sm" data-toggle="tooltip" title="Đơn hàng đang được giao">Shipping</th>
            <th class="th-sm" data-toggle="tooltip" title="Đơn hàng hẹn ngày giao">ShipDelay</th>
          <?php if(!in_array($_group['type'],["shipping","collaborator"])){ ?>
            <th class="th-sm" data-toggle="tooltip" title="Đơn hàng bị từ chối">Rejected</th>
            <th class="th-sm" data-toggle="tooltip" title="Đơn hàng không thể gọi">Call Error</th>
            <!--<th class="th-sm" data-toggle="tooltip" title="Đơn hàng hẹn gọi lại">Call Back</th>-->
          <?php } ?>
            <th class="th-sm" data-toggle="tooltip" title="Đơn hàng bị trả lại">Ship Error</th>
			<th class="th-sm" data-toggle="tooltip" title="Đơn hàng bị trả lại">Ship Fail</th>
          <?php if(!in_array($_group['type'],["shipping","collaborator"])){ ?>
            <th class="th-sm" data-toggle="tooltip" title="Đơn hàng rác">Trashed</th>
          <?php } ?>
            <th class="th-sm" data-toggle="tooltip" title="Tổng số đơn hàng">Total Order</th>
          </tr>
        </thead>
        <tbody>
          <?php

            if($_memebers){
              $total_orders = 0;
              $total_revenue_approve = 0;
              $total_revenue_pending = 0;
              $total_revenue_deduct = 0;
              $total_sales = 0;
              $total_presales = 0;
              $total_earnings = 0;
              $total_approved = 0;
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

              foreach ($_memebers as $arr) {

                $type = $_group['leader'] == $arr['id'] ? '<b class="text-danger">Leader</b>' : 'Member';

                if($_group['type'] == "shipping"){
                    $order_total = getOrderShip(['approved','pending','shipping','shiperror','shipdelay'],$arr['id'],$_group['id'],true);
                    $pending = getOrderShip('pending',$arr['id'],$_group['id'],true);
                    $shipping = getOrderShip('shipping',$arr['id'],$_group['id'],true);
                    $shipdelay = getOrderShip('shipdelay',$arr['id'],$_group['id'],true);
                    $approved = getOrderShip('approved',$arr['id'],$_group['id'],true);
                    $shiperror = getOrderShip('shiperror',$arr['id'],$_group['id'],true);
                    $shipfail = getOrderShip('shipfail',$arr['id'],$_group['id'],true);

                } elseif ($_group['type'] == "publisher"){

                    $sales = getPublisherSale($arr['ukey'],['approved'],null);
                    $presales = getPublisherSale($arr['ukey'],['approved','shipping','pending','shipdelay','shiperror','shipfail'],null);

                    $order_total = getPublisherOrder($arr['ukey'],['calling','pending','shipping','shipdelay','approved','rejected','callerror','shiperror','shipfail','trashed'],null,true);
                    
                    $order_apo = getPublisherOrder($arr['ukey'],['approved','shipping','pending','shipdelay','shiperror','shipfail'],null,true);

                    $pending     = getPublisherOrder($arr['ukey'],'pending',null,true);
                    $shipping    = getPublisherOrder($arr['ukey'],'shipping',null,true);
                    $shipdelay   = getPublisherOrder($arr['ukey'],'shipdelay',null,true);
                    $approved    = getPublisherOrder($arr['ukey'],'approved',null,true);
                    $shiperror   = getPublisherOrder($arr['ukey'],'shiperror',null,true);
                    $shipfail    = getPublisherOrder($arr['ukey'],'shipfail',null,true);
                    $calling     = getPublisherOrder($arr['ukey'],'calling',null,true);
                    $rejected    = getPublisherOrder($arr['ukey'],'rejected',null,true);
                    $callerror   = getPublisherOrder($arr['ukey'],'callerror',null,true);
                    $trashed     = getPublisherOrder($arr['ukey'],'trashed',null,true);

                } else {
                    $sales = getSaleCall(['approved'],$arr['id'],$_group['id']);
                    $earnings = getSaleCall(['approved','pending','shipping','shipdelay','shipfail'],$arr['id'],$_group['id'], ($_group['type'] == "collaborator" ? 'payout_member':'price_deduct') );

                    $presales = getSaleCall(['approved','shipping','pending','shipdelay','shiperror','shipfail'],$arr['id'],$_group['id']);
  
                    $order_total = getOrderCall(['calling','pending','shipping','shipdelay','approved','rejected','callerror','shiperror','shipfail','trashed'],$arr['id'],$_group['id'],true);//,'callback'
                    $order_apo = getOrderCall(['approved','shipping','pending','shipdelay','shiperror','shipfail'],$arr['id'],$_group['id'],true);
  
                    $order_apr = isAutoBan($arr) ? getOrderCall(['approved','shipping','pending','shipdelay','shipfail'],$arr['id'],$_group['id'],true,$arr['ban_limit']) : 0;
  
                    $calling = getOrderCall('calling',$arr['id'],$_group['id'],true);
                    //$callback = getOrderCall('callback',$arr['id'],$_group['id'],true);
                    $pending = getOrderCall('pending',$arr['id'],$_group['id'],true);
                    $shipping = getOrderCall('shipping',$arr['id'],$_group['id'],true);
                    $shipdelay = getOrderCall('shipdelay',$arr['id'],$_group['id'],true);
                    $approved = getOrderCall('approved',$arr['id'],$_group['id'],true);
                    $rejected = getOrderCall('rejected',$arr['id'],$_group['id'],true);
                    $callerror = getOrderCall('callerror',$arr['id'],$_group['id'],true);
                    $shiperror = getOrderCall('shiperror',$arr['id'],$_group['id'],true);
                    $shipfail = getOrderCall('shipfail',$arr['id'],$_group['id'],true);
                    $trashed = getOrderCall('trashed',$arr['id'],$_group['id'],true);
                }

                echo '<tr data-id="'.$arr['id'].'" data-name="'._e($arr['name']).'" data-payout="'._e($arr['group_payout']).'" data-deduct="'._e($arr['group_deduct']).'" data-active="'._e($arr['active']).'" data-auto-ban="'.$arr['auto_ban'].'" data-ban-limit="'.$arr['ban_limit'].'" data-ban-rate="'.$arr['ban_rate'].'" class="'.($arr['id'] == $_user['id'] ? ' brown lighten-5' : '').'">';


                if(isLeader())
                  echo '<td class="text-center">
                          '.($arr['id']!=$_user['id'] || isAdmin() ?'<div class="form-check text-center">
                            <input type="checkbox" class="form-check-input" id="mem_'.$arr['id'].'" value="'.$arr['id'].'">
                            <label class="form-check-label" for="mem_'.$arr['id'].'"></label>
                            <span role="btn-editMember" class="btn btn-dark btn-sm buttonEdit waves-effect waves-light" data-toggle="modal" data-target="#editMember"><i class="fas fa-pen-square ml-1"></i></span>
                            <span role="btn-deleteMember" class="btn btn-danger btn-sm buttonDelete waves-effect waves-light" data-toggle="modal" data-target="#deleteMember"><i class="fas fa-times ml-1"></i></span>
                          </div>':'').'
                        </td>';

                echo '<td class="text-left">
                          <div class="chip align-middle">
                            <a target="_blank" href="?route=statistics&user='.$arr['id'].'">
                              <img src="'.getAvatar($arr['id']).'"> '.(!isBanned($arr) ? _e($arr['name']) : '<strike class="text-dark"><b>'._e($arr['name']).'</b></strike>').'
                            </a>
                          </div>
                        </td>
                        <td><span class="text-dark-50">'.$type.'</span>';

                if(isLeader() && !in_array($_group['type'],["shipping"]))
                  
                  echo '<span class="text-dark"> (<span class="text-success">+'._e(addDotNumber($arr['group_payout'])).$_group_payout_type.'</span>/<span class="text-danger">-'._e(addDotNumber($arr['group_deduct'])).'k</span>)</span>';

                echo '</td>';

                if(!in_array($_group['type'],["shipping"])){
                    if(!in_array($_group['type'],["publisher"])) {
                        if(!isAutoBan($arr)){
                            echo '<td class="text-center"><b class="text-danger">Off</b></td>';
                        } else {

                            $apr = ($order_total >0 ? round($order_apr/($order_total < $arr['ban_limit'] ? $order_total : $arr['ban_limit'])*100,2) : 100);
                            echo '<td class="text-center">(<b>'.$arr['ban_limit'].'</b> orders/APR <= <b>'.$arr['ban_rate'].'</b>% )<br>
                                <i>APR: <span class="'.($apr < $arr['ban_rate'] ? 'text-danger' : 'text-success' ).'">'.$apr.'%</span> </i>
                                </td>';
                        }
                            
                        echo '<td class="text-center">'.($arr['revenue_approve'] > 0 ? '<b class="trigger green lighten-2 text-white">'._e(addDotNumber($arr['revenue_approve'])).'</b>':'0').'</td>';

                        echo '<td class="text-center">'.($arr['revenue_pending'] > 0 ? '<b class="trigger orange lighten-2 text-white">'._e(addDotNumber($arr['revenue_pending'])).'</b>':'0').'</td>';

                        echo '<td class="text-center">'.($arr['revenue_deduct'] > 0 ? '<b class="trigger red lighten-2 text-white">'._e(addDotNumber($arr['revenue_
                        }deduct'])).'</b>':'0').'</td>';

                    }
                    
                    echo '<td class="text-center">'.($order_total >0 ? '<b class="number">'.round($order_apo/$order_total*100,2).'</b> % (<small>'.$order_apo.'</small>)': 0).'</td>';

                    if(!in_array($_group['type'],["publisher"])) {
                        echo '<td class="text-center">'.($order_total >0 ? '<b class="number">'.addDotNumber(round($earnings['earning']/$order_total)).'</b> k' : 0).'</td>';
                    }

                    echo '<td class="text-center">'.($presales['sale'] > 0 ?'<b class="number">'.$presales['sale'].'</b>':'0').'</td>';

                    echo '<td class="text-center">'.($sales['sale'] > 0 ?'<b class="number">'.$sales['sale'].'</b>':'0').'</td>';

                  }
                  echo '<td class="text-center">

                          '.($approved > 0 ?'<a target="_blank" href="?route=order&type=approved&view=all&user='.$arr['id'].'"><b class="number">'.$approved.'</b></a>':'0').'
                        </td>';
                  if(!in_array($_group['type'],["shipping","collaborator"]))
                    echo '<td class="text-center">
                          '.($calling > 0 ?'<a target="_blank" href="?route=order&type=calling&view=all&user='.$arr['id'].'"><b class="number">'.$calling.'</b></a>':'0').'
                        </td>';

                  echo '<td class="text-center">
                          '.($pending > 0 ?'<a target="_blank" href="?route=order&type=pending&view=all&user='.$arr['id'].'"><b class="number">'.$pending.'</b></a>':'0').'
                        </td>
                        <td class="text-center">
                          '.($shipping > 0 ?'<a target="_blank" href="?route=order&type=shipping&view=all&user='.$arr['id'].'"><b class="number">'.$shipping.'</b></a>':'0').'
                        </td>
                        <td class="text-center">
                          '.($shipdelay > 0 ?'<a target="_blank" href="?route=order&type=shipdelay&view=all&user='.$arr['id'].'"><b class="number">'.$shipdelay.'</b></a>':'0').'
                        </td>';
                  if(!in_array($_group['type'],["shipping","collaborator"]))
                    echo '<td class="text-center">
                          '.($rejected > 0 ?'<a target="_blank" href="?route=order&type=rejected&view=all&user='.$arr['id'].'"><b class="number">'.$rejected.'</b></a>':'0').'
                        </td>
                        <td class="text-center">
                          '.($callerror > 0 ?'<a target="_blank" href="?route=order&type=callerror&view=all&user='.$arr['id'].'"><b class="number">'.$callerror.'</b></a>':'0').'
                        </td>';
                      /*  
                        <td class="text-center">
                          '.($callback > 0 ?'<b class="number">'.$callback.'</b>':'0').'
                        </td>';*/

                  echo '<td class="text-center">
                          '.($shiperror > 0 ?'<a target="_blank" href="?route=order&type=shiperror&view=all&user='.$arr['id'].'"><b class="number">'.$shiperror.'</b></a>':'0').'
                        </td>';
					echo '<td class="text-center">
                          '.($shipfail > 0 ?'<a target="_blank" href="?route=order&type=shipfail&view=all&user='.$arr['id'].'"><b class="number">'.$shipfail.'</b></a>':'0').'
                        </td>';							
                  if(!in_array($_group['type'],["shipping","collaborator"]))
                    echo '<td class="text-center">
                          '.($trashed > 0 ?'<a target="_blank" href="?route=order&type=trashed&view=all&user='.$arr['id'].'"><b class="number">'.$trashed.'</b></a>':'0').'
                        </td>';

                  echo '<td class="text-center">
                          '.($order_total > 0 ?'<b class="number">'.$order_total.'</b>':'0').'
                        </td>
                      </tr>';

                  $total_orders = $total_orders+$order_total;
                  $total_revenue_approve = $total_revenue_approve+$arr['revenue_approve'];
                  $total_revenue_pending = $total_revenue_pending+$arr['revenue_pending'];
                  $total_revenue_deduct = $total_revenue_deduct+$arr['revenue_deduct'];
                  $total_sales = $total_sales+$sales['sale'];
                  $total_presales = $total_presales+$presales['sale'];
                  $total_earnings = $total_earnings+$earnings['earning'];
                  $total_approved = $total_approved+$approved;
                  $total_calling = $total_calling+$calling;
                  //$total_callback = $total_callback+$callback;
                  $total_callerror = $total_callerror+$callerror;
                  $total_pending = $total_pending+$pending;
                  $total_shipping = $total_shipping+$shipping;
                  $total_shipdelay = $total_shipdelay+$shipdelay;
                  $total_shiperror = $total_shiperror+$shiperror;
                  $total_shipfail = $total_shipfail + $shipfail;
                  $total_rejected = $total_rejected+$rejected;
                  $total_trashed = $total_trashed+$trashed;
              }


            }

          ?>
        </tbody>
        <?php if(isLeader()){ ?>
        <tfoot>
          <?php

            $total_count_apo = ($total_approved+$total_pending+$total_shipping+$total_shipdelay+$total_shiperror+$total_shipfail);
            $total_apo = $total_orders > 0 ? round($total_count_apo/$total_orders*100,2) : 0;

            $total_epo = $total_orders > 0 ? addDotNumber(round($total_earnings/$total_orders)) : 0;

          ?>
          <tr>
            <th class="th-sm text-center" data-toggle="tooltip" title="Tổng">Total</th>
            <th class="th-sm text-center"></th>
            <th class="th-sm text-center"></th>
          <?php if(!in_array($_group['type'],["shipping"])){ ?>
          
            <?php if(!in_array($_group['type'],["publisher"])){ ?>
                <th class="th-sm text-center"></th>
                <th class="th-sm text-center" data-toggle="tooltip" title="Tổng thu nhập thực tế"><?=($total_revenue_approve > 0 ? '<b class="trigger green lighten-2 text-white">'.addDotNumber($total_revenue_approve).'</b>':'0');?></th>
                <th class="th-sm text-center" data-toggle="tooltip" title="Tổng thu nhập ước tính"><?=($total_revenue_pending > 0 ? '<b class="trigger orange lighten-2 text-white">'.addDotNumber($total_revenue_pending).'</b>':'0');?></th>
                <th class="th-sm text-center" data-toggle="tooltip" title="Tổng thu nhập khấu trừ"><?=($total_revenue_deduct > 0 ? '<b class="trigger red lighten-2 text-white">'.addDotNumber($total_revenue_deduct).'</b>':'0');?></th>
            <?php } ?>

            <th class="th-sm text-center" data-toggle="tooltip" title="Tỉ lệ chấp nhận đơn hàng"><?=($total_apo > 0 ? '<b class="number">'.$total_apo.'</b> % (<small>'.$total_count_apo.'</small>)':'0');?></th>
            <?php if(!in_array($_group['type'],["publisher"])){ ?>
                <th class="th-sm text-center" data-toggle="tooltip" title="Thu nhập bình quân mỗi đơn hàng"><?=($total_epo > 0 ? '<b class="number">'.$total_epo.'</b> k':'0');?></th>
            <?php } ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Sản phẩm bán được"><?=($total_presales > 0 ? '<b class="number">'.$total_presales.'</b>':'0');?></th>
            <th class="th-sm text-center" data-toggle="tooltip" title="Sản phẩm bán được"><?=($total_sales > 0 ? '<b class="number">'.$total_sales.'</b>':'0');?></th>
          <?php } ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Giao thành công"><?=($total_approved > 0 ? '<b class="number">'.$total_approved.'</b>':'0');?></th>
          <?php if(!in_array($_group['type'],["shipping","collaborator"])){ ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Đang gọi"><?=($total_calling > 0 ? '<b class="number">'.$total_calling.'</b>':'0');?></th>
          <?php } ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Chờ giao hàng"><?=($total_pending > 0 ? '<b class="number">'.$total_pending.'</b>':'0');?></th>
            <th class="th-sm text-center" data-toggle="tooltip" title="Đang giao hàng"><?=($total_shipping > 0 ? '<b class="number">'.$total_shipping.'</b>':'0');?></th>
            <th class="th-sm text-center" data-toggle="tooltip" title="Hẹn ngày giao hàng"><?=($total_shipdelay > 0 ? '<b class="number">'.$total_shipdelay.'</b>':'0');?></th>

          <?php if(!in_array($_group['type'],["shipping","collaborator"])){ ?>
            <!-- <th class="th-sm text-center" data-toggle="tooltip" title="Hẹn gọi lại"><?=($total_callback > 0 ? '<b class="number">'.$total_callback.'</b>':'0');?></th> -->
            <th class="th-sm text-center" data-toggle="tooltip" title="Không gọi được"><?=($total_callerror > 0 ? '<b class="number">'.$total_callerror.'</b>':'0');?></th>

            <th class="th-sm text-center" data-toggle="tooltip" title="Từ chối mua hàng"><?=($total_rejected > 0 ? '<b class="number">'.$total_rejected.'</b>':'0');?></th>
          <?php } ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Không nhận hàng"><?=($total_shiperror > 0 ? '<b class="number">'.$total_shiperror.'</b>':'0');?></th>
			<th class="th-sm text-center" data-toggle="tooltip" title="Không nhận hàng"><?=($total_shipfail > 0 ? '<b class="number">'.$total_shipfail.'</b>':'0');?></th>			
          <?php if(!in_array($_group['type'],["shipping","collaborator"])){ ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Đơn hàng rác"><?=($total_trashed > 0 ? '<b class="number">'.$total_trashed.'</b>':'0');?></th>
          <?php } ?>
            <th class="th-sm text-center" data-toggle="tooltip" title="Tổng đơn hàng"><?=($total_orders > 0 ? '<b class="number">'.$total_orders.'</b>':'0');?></th>
          </tr>
        </tfoot>
      <?php } ?>
      </table>
      <?php if(isLeader()){ ?>
      <div class="text-right pt-2">
        <button role="btn-editSelect" class="btn btn-dark btn-sm btn-rounded waves-effect waves-light"  data-toggle="modal" data-target="#editMember" disabled><i class="fas fa-pen-square ml-1"></i> Edit </button>
        <button role="btn-deleteSelect" class="btn btn-danger btn-rounded btn-sm waves-effect waves-light text-dark"  data-toggle="modal" data-target="#deleteMember" disabled><i class="fas fa-trash-alt ml-1"></i> Delete </button>
      </div>
    <?php } ?>
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


    <?php if(isAdmin() || isLeader()){ ?>
      $('#checkAll').on("change",function(){
          var checkboxes = $('td input[type=checkbox]');
          if($(this).prop('checked')) {
            checkboxes.prop('checked', true);
          } else {
            checkboxes.prop('checked', false);
          }

          var checked = $('td input[type=checkbox]:checked');
          if(checked.length > 0) {
            $("[role=btn-editSelect]").prop('disabled', false);
            $("[role=btn-deleteSelect]").prop('disabled', false);
          } else {
            $("[role=btn-editSelect]").prop('disabled', true);
            $("[role=btn-deleteSelect]").prop('disabled', true);
          }
      });

      $('td input[type=checkbox]').on("change",function(){
          var checked = $('td input[type=checkbox]:checked');
          if(checked.length > 0) {
            $("[role=btn-editSelect]").prop('disabled', false);
            $("[role=btn-deleteSelect]").prop('disabled', false);
          } else {
            $("[role=btn-editSelect]").prop('disabled', true);
            $("[role=btn-deleteSelect]").prop('disabled', true);
          }
      });

      var $idGroup = '<?=$_group['id'];?>';
      $("body").on('click','button[role=btn-newMember]', function(){

            var username = $("#i-username"),
                password = $("#i-password"),
                repassword = $("#i-repassword"),
                payout = $("#i-payout"),
                deduct = $("#i-deduct");

            username.siblings(".invalid-feedback").hide();
            password.siblings(".invalid-feedback").hide();
            repassword.siblings(".invalid-feedback").hide();
            payout.siblings(".invalid-feedback").hide();
            deduct.siblings(".invalid-feedback").hide();
            username.val('').trigger("change");
            password.val('').trigger("change");
            repassword.val('').trigger("change");
            payout.val('0').trigger("change");
            deduct.val('0').trigger("change");
      });

    
        $("#newMember").on("click","button[type=submit]",function(){

          var validate_number = /^\d+$/;
          var validate_username = /[ !@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/;

            var username = $("#i-username"),
                password = $("#i-password"),
                repassword = $("#i-repassword"),
                payout = $("#i-payout"),
                deduct = $("#i-deduct");

            username.siblings(".invalid-feedback").hide();
            password.siblings(".invalid-feedback").hide();
            repassword.siblings(".invalid-feedback").hide();
            payout.siblings(".invalid-feedback").hide();
            deduct.siblings(".invalid-feedback").hide();

          if(!username.val().trim() || validate_username.test(username.val().trim())){
              username.siblings(".invalid-feedback").show();
          } else if(!password.val().trim()){
            password.siblings(".invalid-feedback").show();
          } else if(password.val().trim() != repassword.val().trim()){
            repassword.siblings(".invalid-feedback").show();
          <?php if(!in_array($_group['type'],["shipping","publisher"])){ ?>
          } else if(!payout.val().trim() || !validate_number.test(payout.val().trim()) || payout.val().trim() < 0){
              payout.siblings(".invalid-feedback").show();
          } else if(!deduct.val().trim() || !validate_number.test(deduct.val().trim()) || deduct.val().trim() < 0){
              deduct.siblings(".invalid-feedback").show();
          <?php } ?>
          } else {

              $(".loader-overlay").show();

              $.ajax({
                  url: '<?=$_url;?>/ajax.php?act=admincp-newMember',
                  dataType: 'json',
                  data: {id: $idGroup, username: username.val(), password: password.val(), repassword: repassword.val(), payout: payout.val(), deduct: deduct.val()},
                  type: 'post',
                  success: function (response) {
                    $(".loader-overlay").hide();
                    if(response.status == 200){
                        toastr.success(response.message);
                        $('#newMember').modal('hide');
                        setTimeout(function(){
                          location.reload();
                        },500);
                    } else
                      toastr.error(response.message);
                  },
                  error: function (response) {
                    $(".loader-overlay").hide();
                    toastr.error('Could not connect to API!');
                  }
              });
          } 
        });




      var $idMember = [];
      $("body").on('click','[role=btn-editMember]', function(){

            var active = $("#e-active"),
                banned = $("#e-banned"),
                payout = $("#e-payout"),
                deduct = $("#e-deduct");

            var $tr = $(this).parents('tr');

            <?php if( isAdmin()){
              echo 'var auto_ban = $("#e-auto_ban"),ban_limit = $("#e-ban_limit"),ban_rate = $("#e-ban_rate");';
              echo 'ban_limit.siblings(".invalid-feedback").hide();';
              echo 'ban_rate.siblings(".invalid-feedback").hide();';
              echo 'if($tr.attr("data-auto-ban") == 1){auto_ban.prop("checked", true);}else {auto_ban.prop("checked", false);}';
              echo 'ban_limit.val($tr.attr("data-ban-limit")).trigger("change");';
              echo 'ban_rate.val($tr.attr("data-ban-rate")).trigger("change");';
            }
            ?>



            payout.siblings(".invalid-feedback").hide();
            deduct.siblings(".invalid-feedback").hide();


            $idMember = [$tr.attr("data-id")];

            if($tr.attr("data-active").trim() == 1){
              active.prop("checked", true);
              banned.prop("checked", false);
            }
            else {
              active.prop("checked", false);
              banned.prop("checked", true);
            }

            $("#countSelect").html(' ('+$tr.attr("data-name").trim()+') ');

            payout.val($tr.attr("data-payout").trim()).trigger("change");
            deduct.val($tr.attr("data-deduct").trim()).trigger("change");
      });

    
        $("#editMember").on("click","button[type=submit]",function(){

          var validate_number = /^\d+$/;

            var active = $("#e-active"),
                banned = $("#e-banned"),
                payout = $("#e-payout"),
                deduct = $("#e-deduct");

            <?php if( isAdmin()){
              echo 'var auto_ban = $("#e-auto_ban"),ban_limit = $("#e-ban_limit"),ban_rate = $("#e-ban_rate");';
              echo 'ban_limit.siblings(".invalid-feedback").hide();';
              echo 'ban_rate.siblings(".invalid-feedback").hide();';
            }
            ?>

            payout.siblings(".invalid-feedback").hide();
            deduct.siblings(".invalid-feedback").hide();

        <?php if(!in_array($_group['type'],["shipping","publisher"])){ ?>
          if(!payout.val().trim() || !validate_number.test(payout.val().trim()) || payout.val().trim() < 0){
              payout.siblings(".invalid-feedback").show();
          } else if(!deduct.val().trim() || !validate_number.test(deduct.val().trim()) || deduct.val().trim() < 0){
              deduct.siblings(".invalid-feedback").show();
          } else 
        <?php } ?>
          if(!active.is(":checked") && !banned.is(":checked")){
            toastr.error('Trạng thái tài khoản không hợp lệ.');

          <?php if( isAdmin()){
            echo '} else if(!ban_limit.val().trim() || !validate_number.test(ban_limit.val().trim()) || ban_limit.val().trim() < 0){
                    ban_limit.siblings(".invalid-feedback").show();';
            echo '} else if(!ban_rate.val().trim() || !validate_number.test(ban_rate.val().trim()) || ban_rate.val().trim() < 0 || ban_rate.val().trim() > 100){
                    ban_rate.siblings(".invalid-feedback").show();';
          }
          ?>

          } else {

              $(".loader-overlay").show();

              var type;
              if(active.is(":checked")){
                type = 1;
              }
              if(banned.is(":checked")){
                type = 0;
              }

              <?php if(isAdmin()) { ?>

              var ban = 0;
              if(auto_ban.is(":checked")){
                ban = 1;
              }

             <?php  } ?>

              $.ajax({
                  url: '<?=$_url;?>/ajax.php?act=admincp-editMember',
                  dataType: 'json',
                  data: {id: $idMember.join(","),group: $idGroup, payout: payout.val(), deduct: deduct.val(), active: type <?=(isAdmin() ? ',auto_ban: ban, ban_limit: ban_limit.val(), ban_rate: ban_rate.val()' : '' );?> },
                  type: 'post',
                  success: function (response) {
                    $(".loader-overlay").hide();
                    if(response.status == 200){
                        toastr.success(response.message);
                        $('#editMember').modal('hide');
                        setTimeout(function(){
                          location.reload();
                        },500);
                    } else
                      toastr.error(response.message);
                  },
                  error: function (response) {
                    $(".loader-overlay").hide();
                    toastr.error('Could not connect to API!');
                  }
              });
          } 
        });


      $("body").on('click','[role=btn-editSelect]', function(){

            var active = $("#e-active"),
                banned = $("#e-banned"),
                payout = $("#e-payout"),
                deduct = $("#e-deduct");

            <?php if( isAdmin()){
              echo 'var auto_ban = $("#e-auto_ban"),ban_limit = $("#e-ban_limit"),ban_rate = $("#e-ban_rate");';
              echo 'ban_limit.siblings(".invalid-feedback").hide();';
              echo 'ban_rate.siblings(".invalid-feedback").hide();';
              echo 'ban_limit.val("0").trigger("change");';
              echo 'ban_rate.val("0").trigger("change");';
            }
            ?>

            payout.siblings(".invalid-feedback").hide();
            deduct.siblings(".invalid-feedback").hide();

            active.prop("checked", true);

            <?php if( isAdmin()){
              echo 'auto_ban.prop("checked", false);';
            }
            ?>

            $idMember = [];

            $("td input[type=checkbox]:checked").each(function() {
                $idMember.push(this.value);
            });

            if($idMember.length <= 0)
              $('#editMember').modal('hide');

            $("#countSelect").html(' ( '+$idMember.length+' selected )');

            payout.val('0').trigger("change");
            deduct.val('0').trigger("change");
      });


      $("body").on('click','[role=btn-deleteMember]', function(){

        var $tr = $(this).parents('tr');
        $("#deleteBody").html('<b>Member:</b> '+$tr.attr("data-name"));

        $idMember = [$tr.attr("data-id")];


      });

      $("body").on('click','button[role=btn-deleteSelect]', function(){

        var $tr = $(this).parents('tr');

        $idMember = [];

        $("td input[type=checkbox]:checked").each(function() {
            $idMember.push(this.value);
        });

        $("#deleteBody").html('<b>Member select:</b> '+$idMember.length);

        if($idMember.length <= 0)
          $('#deleteMember').modal('hide');

      }); 

        $("#deleteMember").on("click","button[type=submit]",function(){

            $(".loader-overlay").show();

          var forever = 0;
          if( $("#d-forever").is(":checked")){
            forever = 1;
          }


            $.ajax({
                url: '<?=$_url;?>/ajax.php?act=admincp-deleteMember',
                dataType: 'json',
                data: {id: $idMember.join(","), group: $idGroup, forever: forever},
                type: 'post',
                success: function (response) {
                  $(".loader-overlay").hide();
                  if(response.status == 200){
                      toastr.success(response.message);
                      $('#deleteMember').modal('hide');
                      setTimeout(function(){
                        location.reload();
                      },500);
                      
                  } else
                    toastr.error(response.message);
                },
                error: function (response) {
                  $(".loader-overlay").hide();
                  toastr.error('Could not connect to API!');
                }
            });

        });
      <?php } ?>


      $('#dtBasicExample').DataTable({
        columnDefs: [{ targets: 0, orderable: false }],
        language: {emptyTable: "Không có thành viên nào."},
        info: false,
        paging: false,
        scrollX: true,
        scrollY: true,
        searching: true,
        order: [ <?=(isAdmin() || $_group['leader'] == $_user['id'] ? '2' : '1')?>, 'asc' ],
        responsive: false
      });
      $('.dataTables_length').addClass('bs-select');
  });
</script>
<?php


end:


?>