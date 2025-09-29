<?php


if(!isLeader() && !isCaller() && !isColler()){
  echo '<div class="alert alert-danger" role="alert">
        <h4 class="alert-heading">An error occurred!</h4>
        <p>Oops!!! You have just accessed a link that does not exist on the system or you do not have enough permissions to access.</p>
        <hr>
        <p class="mb-0">Please return to the previous page.</p>
      </div>';

  goto end;
}




?>

<link rel="stylesheet" type="text/css" href="template/assets/css/addons/datatables.min.css">
<link rel="stylesheet" href="template/assets/css/addons/datatables-select.min.css">
<script type="text/javascript" src="template/assets/js/addons/datatables.min.js"></script>
<script type="text/javascript" src="template/assets/js/addons/datatables-select.min.js"></script>



<?php if(!$_id){


$_count = $_db->query("select `id` from `core_payments` where `refid`='0' and `type`='1' and `user_id`='".$_user['id']."'")->num_rows();
$_data = $_db->query("select * from `core_payments` where `refid`='0'  and `type`='1' and `user_id`='".$_user['id']."' order by `time` desc limit $_start,$_max")->fetch_array();

?>

<h2 class="section-heading mb-4">Group payment history</h2>

<section class="row mb-5 pb-3">
    <div class="col-md-12 mx-auto white z-depth-1" style="overflow-x: hidden;">

      <table id="dtBasicExample" class="table table-bordered table-sm" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th class="th-sm text-center" data-toggle="tooltip" title="ID hóa đơn">Invoice</th>
            <th class="th-sm text-center" data-toggle="tooltip" title="Ngày thanh toán">Date</th>
            <th class="th-sm text-center" data-toggle="tooltip" title="Tình trạng thanh toán">Status</th>
            <th class="th-sm text-center" data-toggle="tooltip" title="Tiền đã thanh toán">Paid</th>
            <th class="th-sm text-center" data-toggle="tooltip" title="Số tiền nhận được">Earnings</th>
            <th class="th-sm text-center" data-toggle="tooltip" title="Số tiền tạm giữ còn lại">Hold</th>
            <th class="th-sm text-center" data-toggle="tooltip" title="Số tiền khấu trừ đơn hàng trả lại">Deduct</th>
            <th class="th-sm text-center" data-toggle="tooltip" title="Thanh toán bởi"> Payer</th>
            <th class="th-sm text-center"></th>
          </tr>
        </thead>
        <tbody>
          <?php

            if($_data){

              foreach ($_data as $arr) {
                $user = getUser($arr['user_id']);
                $ugroup = getGroup($user['group']);

                $check = $_db->query("select `id` from `core_payments` where `status`='pending' and `refid`='".$arr['id']."'")->num_rows();
                echo '<tr>
                        <td class="text-center"><b>#'.$arr['id'].'</b></td>
                        <td class="text-center"><b>'.date('Y/m/d',$arr['time']).'</b></td>';


                if($check > 0)
                  echo '<td class="text-center"><b class="number text-danger">Chưa thanh toán xong</b></td>';
                else
                  echo '<td class="text-center"><b class="number text-success">Đã thanh toán xong.</b></td>';

                echo '<td class="text-center"><b class="text-success">'.number_format($arr['paid'], 0, ',', '.').'</b>K</td>
                        <td class="text-center">'.($arr['approve'] > 0 ? '<b class="number">'.number_format($arr['approve'], 0, ',', '.').'</b>K':'0').'</td>
                        <td class="text-center">'.($arr['pending'] > 0 ? '<b class="number">'.number_format($arr['pending'], 0, ',', '.').'</b>K':'0').'</td>
                        <td class="text-center">'.($arr['deduct'] > 0 ? '<b class="text-danger">-'.number_format($arr['deduct'], 0, ',', '.').'</b>K':'0').'</td>
                        <td class="text-center">
                          <div class="chip align-middle">
                            <a target="_blank" href="?route=statistics&user='.$user['id'].'">
                              <img src="'.getAvatar($user['id']).'"> '.(!isBanned($user) ? _e($user['name']) : '<strike class="text-dark"><b>'._e($user['name']).'</b></strike>').'
                            </a>
                          </div>
                        </td>
                        <td class="text-center">
                        	<a href="?route=groupPayment&id='.$arr['id'].'">
                        		<button role="btn-calling" class="btn badge-dark btn-sm btn-rounded waves-effect waves-light"><i class="w-fa fas fa-eye"></i> View</button>
                        	</a>
                        </td>
                      </tr>';
              }

            }

          ?>
        </tbody>
      </table>
      <?php if($_count > $_max) echo pagination($_SERVER['REQUEST_URI'],$_count); ?>
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

	    $('#dtBasicExample').DataTable({
	    	columnDefs: [{ targets: 8, orderable: false }],
        	language: {emptyTable: "Không có số liệu thanh toán."},
	      info: false,
	      paging: false,
	      scrollX: true,
	      scrollY: true,
	      searching: false,
	      order: [ 2, 'asc' ],
	      responsive: false
	    });
	    $('.dataTables_length').addClass('bs-select');

	});
</script>

<?php

}  else {



$_data = $_db->query("select * from `core_payments` where `refid`='0' and `id`='".escape_string($_id)."' ")->fetch();

$_leader = getUser($_data['user_id']);
$_group = getGroup($_leader['group']);
$check = $_db->query("select `id` from `core_payments` where `status`='pending' and `refid`='".$_data['id']."'")->num_rows();


if(!$_data){
  echo '<div class="alert alert-danger" role="alert">
        <h4 class="alert-heading">An error occurred!</h4>
        <p>Oops!!! You have just accessed a link that does not exist on the system or you do not have enough permissions to access.</p>
        <hr>
        <p class="mb-0">Please return to the previous page.</p>
      </div>';

  goto end;
}

if(isset($_POST['checkPayment'])){

  $payment = $_db->query("select * from  `core_payments` where `id`='".escape_string(trim($_POST['idPay']))."' limit 1 ")->fetch();
    if(!$payment){
      $_statusmessage['type'] = 'danger';
      $_statusmessage['message'] = 'Không tìm thấy ID thanh toán.';
    } else if($payment['status'] != "pending"){
      $_statusmessage['type'] = 'warning';
      $_statusmessage['message'] = 'Hóa đơn này đã được đánh dấu thanh toán.';
    } else {
      if($_db->exec_query("update `core_payments` set `status`='complete',`payer`='".$_user['id']."' where `id`='".escape_string($payment['id'])."' and `type`='2' ")){

        $userPay = getUser($payment['user_id']);

        $notifi_title = '<strong class="trigger green lighten-2 text-white">Hóa đơn thanh toán đã được xử lí #'.$payment['id'].'</strong>';
        $notifi_text = '<strong>Hóa đơn thanh toán #'.$payment['id'].' đã được xử lí. [<a href="?route=payments"><span class="text-danger"> Xem hóa đơn </span></a>]</strong>.<br> Nếu bạn chưa nhận được tiền vui lòng liên hệ với trưởng nhóm.';
        addNotification($notifi_title,$notifi_text,$payment['user_id']);

        $_statusmessage['type'] = 'success';
        $_statusmessage['message'] = 'Đánh dấu đã thanh toán thành công cho <b>'._e($userPay['name']).'</b>';


        $_data = $_db->query("select * from `core_payments` where `refid`='0' and `id`='".escape_string($_id)."' ")->fetch();
        $_sub_data = $_db->query("select * from `core_payments` where `refid`='".escape_string($_id)."' ")->fetch_array();

      } else {
        $_statusmessage['type'] = 'warning';
        $_statusmessage['message'] = 'Có lỗi xảy ra khi đánh dấu thanh toán.';
      }
    }
}




?>


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

                <h4 class="h4-responsive"><small>Invoice No.</small><br /><strong><span class="blue-text">#<?=$_data['id'];?></span></strong></h4>
                <p><strong>Invoice date:</strong> <?=date('d/m/Y',$_data['time']);?></p>
                <p><strong>Status:</strong> <?=($check > 0 ? '<b class="text-danger">Chưa thanh toán xong</b>':'<b class="text-success">Đã thanh toán xong</b>');?></p>
              </div>
              <!-- Grid column -->

              <!-- Grid column -->
              <div class="col-md-6 text-right">


                <ul class="list-unstyled text-left">
                  <li><strong>Tổng tiền:</strong><span class="float-right ml-3"><?=($_data['approve'] > 0 ? '<b>'.addDotNumber($_data['approve']*1000).'</b>vnd':'0');?></span></li>
                  <li><strong>Tổng khấu trừ:</strong><span class="float-right ml-3"><?=($_data['deduct'] > 0 ? '-<b class="text-danger">'.addDotNumber($_data['deduct']*1000).'</b>vnd':'0');?></span></li>
                  <li><strong>Tổng thanh toán:</strong><span class="float-right ml-3"><?=($_data['paid'] > 0 ? '<b class="text-success">'.addDotNumber($_data['paid']*1000).'</b>vnd':'0');?></span></li>
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
                    <th class="text-left" width="40%">Member</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Paid</th>
                    <th class="text-center">Earnings</th>
                    <th class="text-center">Hold</th>
                    <th class="text-center">Deduct</th>
                    <th class="text-center"></th>
                  </tr>
                </thead>
                <tbody>
                <?php

                $_sub_data = $_db->query("select * from `core_payments` where `refid`='".escape_string($_id)."' ")->fetch_array();

                if($_sub_data){

                  foreach ($_sub_data as $arr) {
                    $user = getUser($arr['user_id']);

                    echo '<tr data-id="'.$arr['id'].'" data-paid="'.addDotNumber($arr['paid']*1000).'" data-bank="'._e($arr['bank']).'" data-name="'._e($arr['bank_name']).'" data-number="'._e($arr['bank_number']).'" data-branch="'._e($arr['bank_branch']).'">
                      <td class="text-left">
                        <div class="chip align-middle">
                          <a target="_blank" href="?route=statistics&user='.$user['id'].'">
                            <img src="'.getAvatar($user['id']).'"> '.(!isBanned($user) ? _e($user['name']) : '<strike class="text-dark"><b>'._e($user['name']).'</b></strike>').'
                          </a>
                        </div>
                      </td>';

                    if($arr['status'] == "pending")
                      echo '<td class="text-center"><b class="number text-danger">Chưa thanh toán</b></td>';
                    else
                      echo '<td class="text-center"><b class="number text-success">Đã thanh toán.</b></td>';

                    echo '<td class="text-center"><b class="text-success number">'.addDotNumber($arr['paid']).'</b>k</td>
                            <td class="text-center">'.($arr['approve'] > 0 ? '<b class="number">'.addDotNumber($arr['approve']).'</b>k':'0').'</td>
                            <td class="text-center">'.($arr['pending'] > 0 ? '<b class="number">'.addDotNumber($arr['pending']).'</b>k':'0').'</td>
                            <td class="text-center">'.($arr['deduct'] > 0 ? '-<b class="text-danger">'.addDotNumber($arr['deduct']).'</b>k':'0').'</td>';

                    echo '<td class="text-center">
                            '.($arr['status'] == "pending" ? '<button role="btn-check" class="btn badge-success btn-sm btn-rounded waves-effect waves-light"  data-toggle="modal" data-target="#checkPayment"><i class="w-fa fas fa-check"></i> Make Paid</button>':'').'
                      </td>';
                    echo '</tr>';

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
        <div data-id="">
          <p>Vui lòng chuyển khoản theo thông tin dưới đây:</p>
          <span id="paymentInfo"></span>
          <p><b>P/s:</b> <small class="text-danger">Không thể hóa đơn một khi đã đánh dấu thanh toán.</small></p>
        </div>
      </div>

      <!--Footer-->
      <div class="modal-footer flex-center">
      <form id="formCheck" method="POST">
        <input id="idPay" name="idPay" type="hidden" value="">
        <button class="btn btn-outline-success" type="submit" name="checkPayment">Đã thanh toán</button>
      </form>
        <button class="btn btn-success waves-effect" data-dismiss="modal">Đóng</button>
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

<script type="text/javascript">
  $(document).ready(function(){
    $('#dtBasicExample').DataTable({
      columnDefs: [{ targets: 6, orderable: false }],
      language: {emptyTable: "Không có số liệu thống kê."},
      info: false,
      paging: false,
      scrollX: true,
      scrollY: true,
      searching: false,
      order: [ 1, 'asc' ],
      responsive: false
    });
    $('.dataTables_length').addClass('bs-select');
  });

  $("body").on("click","[role=btn-check]",function(){

    var $tr = $(this).parents("tr");
    $("#idPay").val($tr.attr("data-id"));

    var html  = '<p class="text-left"><b>Số tiền:</b> '+$tr.attr("data-paid")+'Vnđ</p>';
        html += '<p class="text-left"><b>Ngân hàng:</b> '+$tr.attr("data-bank")+'</p>';
        html += '<p class="text-left"><b>Chi nhánh:</b> '+$tr.attr("data-branch")+'</p>';
        html += '<p class="text-left"><b>Số tài khoản:</b> '+$tr.attr("data-number")+'</p>';
        html += '<p class="text-left"><b>Họ tên:</b> '+$tr.attr("data-name")+'</p>';

    $("#paymentInfo").html(html);

  });

  $("#formCheck").on("click","[type=submit]",function(){
    $(".loader-overlay").show();
  });

</script>



<?php } ?>


<?php

end:

?>