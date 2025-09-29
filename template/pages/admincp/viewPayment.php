<?php

$_data = $_db->query("select * from `core_payments` where `refid`='0' and `id`='".escape_string($_id)."' ")->fetch();
$_sub_data = $_db->query("select * from `core_payments` where `refid`='".escape_string($_id)."' ")->fetch_array();
$_leader = getUser($_data['user_id']);
$_group = getGroup($_leader['group']);


if(!$_data){
  echo '<div class="alert alert-danger" role="alert">
        <h4 class="alert-heading">An error occurred!</h4>
        <p>Oops!!! You have just accessed a link that does not exist on the system or you do not have enough permissions to access.</p>
        <hr>
        <p class="mb-0">Please return to the previous page.</p>
      </div>';

  goto end;
}

if(isset($_POST['cancelPayment'])){

    if($_data['status'] == "pending"){
        if($_db->exec_query("delete from `core_payments` where (`id`='".escape_string($_data['id'])."' and `type`='0') or (`refid`='".escape_string($_data['id'])."' and `type`='1') ")){

          $_statusmessage['type'] = 'success';
          $_statusmessage['message'] = 'Hủy hóa đơn thanh toán thành công.';

          //header('Location: ?route=newOrder');
          echo '<meta http-equiv="refresh" content="2;url=?route=settings&type=payment" />';


        } else {
          $_statusmessage['type'] = 'warning';
          $_statusmessage['message'] = 'Có lỗi xảy ra khi hủy hóa đơn thanh toán.';
        }
    } else {
          $_statusmessage['type'] = 'danger';
          $_statusmessage['message'] = 'Không thể hủy hóa đơn đã đánh dấu thanh toán.';
    }


} else if(isset($_POST['checkPayment'])){
    if($_data['status'] == "pending"){
        if($_db->exec_query("update `core_payments` set `type`='1',`status`='complete',`payer`='".$_user['id']."' where `id`='".escape_string($_data['id'])."' and `type`='0' ")){

          $_db->exec_query("update `core_payments` set `type`='2',`status`='pending',`payer`='' where `refid`='".escape_string($_data['id'])."'  and `user_id`!='".escape_string($_leader['id'])."' ");

          $_db->exec_query("update `core_payments` set `type`='2',`status`='complete',`payer`='".$_user['id']."' where `refid`='".escape_string($_data['id'])."' and `user_id`='".escape_string($_leader['id'])."' ");

          $notifi_title = '<strong class="trigger green lighten-2 text-white">Hóa đơn thanh toán chờ xử lí #'.$_data['id'].'</strong>';
          $notifi_text = '<strong>Bạn vừa nhận được một hóa đơn thanh toán mới: [<a href="?route=groupPayment&id='.$_data['id'].'"><span class="text-danger"> Xem hóa đơn </span></a>]</strong>.<br> Vui lòng thanh toán lại cho từng thành viên theo số tiền mà họ kiếm được.';
          addNotification($notifi_title,$notifi_text,$_leader['id']);


          $notifi_mem = array();

          $list = $_db->query("select * from `core_payments` where `refid`='".escape_string($_data['id'])."'")->fetch_array();

          if($list){
            foreach ($list as $invoice) {
                if($invoice['user_id'] != $_leader['id'])
                  $notifi_mem[] = $invoice['user_id'];
                $_db->exec_query("update `core_users` set `revenue_approve`=`revenue_approve`-'".$invoice['approve']."',`revenue_deduct`=`revenue_deduct`-'".$invoice['deduct']."' where `id`='".$invoice['user_id']."' "); 
            }
          }

          if($notifi_mem){
            $notifi_title = '<strong class="trigger green lighten-2 text-white">Hóa đơn thanh toán mới</strong>';
            $notifi_text = '<strong>Bạn vừa nhận được một hóa đơn thanh toán mới: [<a href="?route=payments"><span class="text-danger"> Xem hóa đơn </span></a>]</strong>.<br> Vui lòng chờ một thời gian cho đến khi hóa đơn được xử lí. Mọi thắc mắc xi liên hệ trưởng nhóm hoặc quản trị viên.';
            addNotification($notifi_title,$notifi_text,$notifi_mem);
          }

          $_db->exec_query("update `core_groups` set `revenue_approve`=`revenue_approve`-'".$_data['approve']."',`revenue_deduct`=`revenue_deduct`-'".$_data['deduct']."'  where `id`='".$_group['id']."'  ");




          $_statusmessage['type'] = 'success';
          $_statusmessage['message'] = 'Đánh dấu đã thanh toán thành công.';


          $_data = $_db->query("select * from `core_payments` where `refid`='0' and `id`='".escape_string($_id)."' ")->fetch();
          $_sub_data = $_db->query("select * from `core_payments` where `refid`='".escape_string($_id)."' ")->fetch_array();

        } else {
          $_statusmessage['type'] = 'warning';
          $_statusmessage['message'] = 'Có lỗi xảy ra khi đánh dấu thanh toán.';
        }
    } else {
          $_statusmessage['type'] = 'danger';
          $_statusmessage['message'] = 'Không thể đánh dấu hóa đơn đã thanh toán.';
    }
}




?>

<link rel="stylesheet" type="text/css" href="template/assets/css/addons/datatables.min.css">
<link rel="stylesheet" href="template/assets/css/addons/datatables-select.min.css">
<script type="text/javascript" src="template/assets/js/addons/datatables.min.js"></script>
<script type="text/javascript" src="template/assets/js/addons/datatables-select.min.js"></script>


      <!-- Section: Heading -->
      <section class="mb-4">

        <?php if(!empty($_statusmessage)): ?>
          <div class="alert alert-<?=$_statusmessage["type"]; ?> lert-dismissible fade show" role="alert">
            <?=$_statusmessage["message"]; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        <?php endif; ?>

      <?php if($_data['status'] == "pending"){ ?>
        <div class="card">
          <div class="card-body d-flex justify-content-between">
            <h4 class="h4-responsive mt-3">Invoice #<?=$_id;?></h4>

            <div>
              <a href="#" class="btn btn-success"  data-toggle="modal" data-target="#checkPayment">Đánh dấu thanh toán</a>
              <a href="#" class="btn btn-danger" data-toggle="modal" data-target="#cancelPayment"><i class="fas fa-print left"></i> Hủy hóa đơn</a>
            </div>

          </div>
        </div>
      <?php } ?>

      </section>
      <!-- Section: Heading -->

      <!-- Section: Invoice details -->
      <section class="mb-4">

        <div class="card">
          <div class="card-body">

            <!-- Grid row -->
            <div class="row">

              <!-- Grid column -->
              <div class="col-md-6 text-left">

                <p><strong>Group:</strong> <?=_e($_group['name']);?></p>
                <p><strong>Invoice date:</strong> <?=date('d/m/Y',$_data['time']);?></p>
                <p><strong>Status:</strong> <?=($_data['status'] == "pending" ? '<b class="text-danger">Chưa thanh toán</b>':'<b class="text-success">Đã thanh toán</b>');?></p>
                <ul class="list-unstyled text-left">
                  <li><strong>Tổng tiền:</strong><span class="float-right ml-3"><?=($_data['approve'] > 0 ? '<b>'.addDotNumber($_data['approve']*1000).'</b>vnd':'0');?></span></li>
                  <li><strong>Tổng khấu trừ:</strong><span class="float-right ml-3"><?=($_data['deduct'] > 0 ? '-<b class="text-danger">'.addDotNumber($_data['deduct']*1000).'</b>vnd':'0');?></span></li>
                  <li><strong>Tổng thanh toán:</strong><span class="float-right ml-3"><?=($_data['paid'] > 0 ? '<b class="text-success">'.addDotNumber($_data['paid']*1000).'</b>vnd':'0');?></span></li>
                </ul>
              </div>
              <!-- Grid column -->

              <!-- Grid column -->
              <div class="col-md-6 text-right">

                <h4 class="h4-responsive"><small>Invoice No.</small><br /><strong><span class="blue-text">#<?=$_data['id'];?></span></strong></h4>
                <ul class="list-unstyled text-left">
                  <li><strong>Ngân hàng:</strong><span class="float-right ml-3"><?=_e($_data['bank']);?></span></li>
                  <li><strong>Chi nhánh:</strong><span class="float-right ml-3"><?=_e($_data['bank_branch']);?></span></li>
                  <li><strong>Số tài khoản:</strong><span class="float-right ml-3"><?=_e($_data['bank_number']);?></span></li>
                  <li><strong>Họ tên:</strong><span class="float-right ml-3"><?=_e($_data['bank_name']);?></span></li>
                </ul>

              </div>
              <!-- Grid column -->

            </div>
            <!-- Grid row -->

          </div>
        </div>

      </section>
      <!-- Section: Invoice details -->

      <!-- Section: Invoice table -->
      <section class="mb-5">

        <div class="card">
          <div class="card-body">

            <div class="">
              <table id="dtBasicExample" class="table  table-hover table-bordered table-sm" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th class="text-left" width="50%">Member</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Paid</th>
                    <th class="text-center">Earnings</th>
                    <th class="text-center">Hold</th>
                    <th class="text-center">Deduct</th>
                  </tr>
                </thead>
                <tbody>
                <?php


                if($_sub_data){

                  foreach ($_sub_data as $arr) {
                    $user = getUser($arr['user_id']);
                    echo '<tr>
                      <td class="text-left">
                        <div class="chip align-middle"><a target="_blank" href="?route=statistics&user='.$user['id'].'"><img src="'.getAvatar($user['id']).'"> '.(!isBanned($user) ? _e($user['name']) : '<strike class="text-dark"><b>'._e($user['name']).'</b></strike>').'</a></div>
                      </td>';
                    if($arr['status'] == "pending")
                      echo '<td class="text-center"><b class="number text-danger">Chưa thanh toán</b></td>';
                    else
                      echo '<td class="text-center"><b class="number text-success">Đã thanh toán.</b></td>';
                    echo '<td class="text-center"><b class="text-success number">'.addDotNumber($arr['paid']).'</b>k</td>
                      <td class="text-center">'.($arr['approve'] > 0 ? '<b class="number">'.addDotNumber($arr['approve']).'</b>k':'0').'</td>
                      <td class="text-center">'.($arr['pending'] > 0 ? '<b class="number">'.addDotNumber($arr['pending']).'</b>k':'0').'</td>
                      <td class="text-center">'.($arr['deduct'] > 0 ? '-<b class="text-danger">'.addDotNumber($arr['deduct']).'</b>k':'0').'</td>
                    </tr>';

                  }


                }
                ?>
                </tbody>
              </table>
            </div>

          </div>
        </div>

      </section>
      <!-- Section: Invoice table -->


<?php if($_data['status'] == "pending"){ ?>
<!--Modal: modalConfirmDelete-->
<div class="modal fade" id="checkPayment" tabindex="-1" role="dialog"
  aria-hidden="true">
  <div class="modal-dialog modal-md modal-notify modal-success" role="document">
    <!--Content-->
    <div class="modal-content text-center">
      <!--Header-->
      <div class="modal-header d-flex justify-content-center">
        <p class="heading">Đánh dấu thanh toán</p>
      </div>

      <!--Body-->
      <div class="modal-body">

        <i class="fas fa-check fa-4x animated rotateIn"></i>
        <div id="deleteBody" data-id="">
          <p>Vui lòng chuyển khoản theo thông tin dưới đây:</p>
          <p class="text-left"><b>Số tiền:</b> <?=addDotNumber($_data['paid']*1000);?>Vnđ</p>
          <p class="text-left"><b>Ngân hàng:</b> <?=_e($_data['bank']);?></p>
          <p class="text-left"><b>Chi nhánh:</b> <?=_e($_data['bank_branch']);?></p>
          <p class="text-left"><b>Số tài khoản:</b> <?=_e($_data['bank_number']);?></p>
          <p class="text-left"><b>Họ tên:</b> <?=_e($_data['bank_name']);?></p>
          <p><b>P/s:</b> <small class="text-danger">Không thể hóa đơn một khi đã đánh dấu thanh toán.</small></p>
        </div>
      </div>

      <!--Footer-->
      <div class="modal-footer flex-center">
      <form id="formCheck" method="POST">
        <button class="btn  btn-outline-success" type="submit" name="checkPayment">Đã thanh toán</button>
      </form>
        <button class="btn  btn-success waves-effect" data-dismiss="modal">Đóng</button>
      </div>
    </div>
    <!--/.Content-->
  </div>
</div>
<!--Modal: modalConfirmDelete-->

<!--Modal: modalConfirmDelete-->
<div class="modal fade" id="cancelPayment" tabindex="-1" role="dialog"
  aria-hidden="true">
  <div class="modal-dialog modal-sm modal-notify modal-danger" role="document">
    <!--Content-->
    <div class="modal-content text-center">
      <!--Header-->
      <div class="modal-header d-flex justify-content-center">
        <p class="heading">Hủy Hóa đơn</p>
      </div>

      <!--Body-->
      <div class="modal-body">

        <i class="fas fa-times fa-4x animated rotateIn"></i>
        <div id="deleteBody" data-id="">
          Bạn thực sự muốn hủy hóa đơn này?
        </div>
      </div>

      <!--Footer-->
      <div class="modal-footer flex-center">
      <form id="formCancel" method="POST">
        <button class="btn  btn-outline-danger" type="submit" name="cancelPayment">Hủy hóa đơn</button>
      </form>
        <button class="btn  btn-danger waves-effect" data-dismiss="modal">Đóng</button>
      </div>
    </div>
    <!--/.Content-->
  </div>
</div>
<!--Modal: modalConfirmDelete-->

<div class="loader-overlay">
	<div class="loader-content-container">
		<div class="loader-content">
			<div class="spinner-grow" role="status" style="width: 6rem; height: 6rem;">
				<span class="sr-only">Loading...</span>
			</div>
		</div>
	</div>
</div>
<?php } ?>

<script type="text/javascript">
	$(document).ready(function(){
    $('#dtBasicExample').DataTable({
      language: {emptyTable: "Không có số liệu thống kê."},
      info: false,
      paging: false,
      scrollX: true,
      scrollY: true,
      searching: false,
      order: [ 1, 'desc' ],
      responsive: false
    });
    $('.dataTables_length').addClass('bs-select');
	});

  $("#formCheck").on("click","[type=submit]",function(){
    $(".loader-overlay").show();
  });
  $("#formCancel").on("click","[type=submit]",function(){
    $(".loader-overlay").show();
  });
</script>

<?php 


end:


?>