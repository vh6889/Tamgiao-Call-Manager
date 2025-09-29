<?php



if(!isCaller()){

  echo '<div class="alert alert-danger" role="alert">
        <h4 class="alert-heading">An error occurred!</h4>
        <p>Oops!!! You have just accessed a link that does not exist on the system or you do not have enough permissions to access.</p>
        <hr>
        <p class="mb-0">Please return to the previous page.</p>
      </div>';

  goto end;
}



  $moneyApprove = number_format($_user['revenue_approve'], 0, ',', '.')."K";
  $moneyPending = number_format($_user['revenue_pending'], 0, ',', '.')."K";
  $moneyDeduct  = number_format($_user['revenue_deduct'], 0, ',', '.')."K";


  $_count = $_db->query("select `id` from `core_payments` where `user_id`='".$_user['id']."' and `type`='2'")->num_rows();
  $_data = $_db->query("select * from `core_payments` where `user_id`='".$_user['id']."' and `type`='2' order by `time` desc limit $_start,$_max")->fetch_array();



?>

<link rel="stylesheet" type="text/css" href="template/assets/css/addons/datatables.min.css">
<link rel="stylesheet" href="template/assets/css/addons/datatables-select.min.css">
<script type="text/javascript" src="template/assets/js/addons/datatables.min.js"></script>
<script type="text/javascript" src="template/assets/js/addons/datatables-select.min.js"></script>

<h2 class="section-heading mb-4">Payments History</h2>
<section  class="mb-4">

  <!-- Grid row -->
  <div class="row">

    <!-- Grid column -->
    <div class="col-xl-4 col-md-4 mb-4">

      <!-- Card -->
      <div class="card">

        <!-- Card Data -->
        <div class="row mt-3">

          <div class="col-md-5 col-5 text-left pl-4">

            <a type="button" class="btn-floating btn-lg success-color ml-4 waves-effect waves-light"><i class="far fa-money-bill-alt" aria-hidden="true"></i></a>

          </div>

          <div class="col-md-7 col-7 text-right pr-5">

            <h5 class="ml-4 mt-4 mb-2 font-weight-bold"><?=$moneyApprove;?></h5>

            <p class="font-small grey-text">Earnings</p>
          </div>

        </div>
        <!-- Card Data -->

        <!-- Card content -->
        <div class="row my-3">
          <p class="font-small dark-grey-text font-up ml-4 font-weight-small text-center">Tiền nhận được cho mỗi đơn hàng giao thành công.</p>
        </div>
        <!-- Card content -->

      </div>
      <!-- Card -->

    </div>
    <!-- Grid column -->

    <!-- Grid column -->
    <div class="col-xl-4 col-md-4 mb-4">

      <!-- Card -->
      <div class="card">

        <!-- Card Data -->
        <div class="row mt-3">

          <div class="col-md-5 col-5 text-left pl-4">

            <a type="button" class="btn-floating btn-lg warning-color ml-4 waves-effect waves-light"><i class="fas fa-dollar-sign" aria-hidden="true"></i></a>

          </div>

          <div class="col-md-7 col-7 text-right pr-5">

            <h5 class="ml-4 mt-4 mb-2 font-weight-bold"><?=$moneyPending;?></h5>
            <p class="font-small grey-text">Hold</p>

          </div>

        </div>
        <!-- Card Data -->

        <!-- Card content -->
        <div class="row my-3">
          <p class="font-small dark-grey-text font-up ml-4 font-weight-small">Tiền ước tính cho mỗi đơn hàng gọi thành công.</p>
        </div>
        <!-- Card content -->

      </div>
      <!-- Card -->

    </div>
    <!-- Grid column -->

    <!-- Grid column -->
    <div class="col-xl-4 col-md-4 mb-4">

      <!-- Card -->
      <div class="card">

        <!-- Card Data -->
        <div class="row mt-3">

          <div class="col-md-5 col-5 text-left pl-4">
            <a type="button" class="btn-floating btn-lg red accent-2 ml-4 waves-effect waves-light"><i class="fas fa-dollar-sign" aria-hidden="true"></i></a>
          </div>

          <div class="col-md-7 col-7 text-right pr-5">
            <h5 class="ml-4 mt-4 mb-2 font-weight-bold"><?=$moneyDeduct;?> </h5>
            <p class="font-small grey-text">Deduct</p>
          </div>

        </div>
        <!-- Card Data -->

        <!-- Card content -->
        <div class="row my-3">
          <p class="font-small dark-grey-text font-up ml-4 font-weight-small">Tiền khấu trừ cho mỗi đơn hàng bị trả lại.</p>
        </div>
        <!-- Card content -->

      </div>
      <!-- Card -->

    </div>
    <!-- Grid column -->

  </div>
  <!-- Grid row -->

</section>

<section>
  <div class="card">
    <div class="card-body">
      <table id="dtBasicExample" class="table  table-hover table-bordered table-sm" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th class="th-sm text-center" data-toggle="tooltip" title="ID hóa đơn">Invoice</th>
            <th class="th-sm text-center" data-toggle="tooltip" title="Ngày thanh toán">Date</th>
            <th class="th-sm text-center" data-toggle="tooltip" title="Tình trạng thanh toán">Status</th>
            <th class="th-sm text-center" data-toggle="tooltip" title="Tiền đã thanh toán">Paid</th>
            <th class="th-sm text-center" data-toggle="tooltip" title="Số tiền nhận được">Earnings</th>
            <th class="th-sm text-center" data-toggle="tooltip" title="Số tiền tạm giữ còn lại">Hold</th>
            <th class="th-sm text-center" data-toggle="tooltip" title="Số tiền khấu trừ đơn hàng trả lại">Deduct</th>
            <th class="th-sm text-center" data-toggle="tooltip" title="Người thanh toán">Payer</th>
          </tr>
        </thead>
        <tbody>
          <?php

            if($_data){

              foreach ($_data as $arr) {
                $payer = getUser($arr['payer']);
                echo '<tr>
                        <td class="text-center"><b>#'.$arr['id'].'</b></td>
                        <td class="text-center"><b>'.date('Y/m/d',$arr['time']).'</b></td>';

                if($arr['status'] == "pending")
                  echo '<td class="text-center"><b class="number text-danger">Đang xử lí</b></td>';
                else
                  echo '<td class="text-center"><b class="number text-success">Đã thanh toán.</b></td>';

                echo '<td class="text-center"><b class="text-success">'.number_format($arr['paid'], 0, ',', '.').'</b>K</td>
                        <td class="text-center">'.($arr['approve'] > 0 ? '<b class="number">'.number_format($arr['approve'], 0, ',', '.').'</b>K':'0').'</td>
                        <td class="text-center">'.($arr['pending'] > 0 ? '<b class="number">'.number_format($arr['pending'], 0, ',', '.').'</b>K':'0').'</td>
                        <td class="text-center">'.($arr['deduct'] > 0 ? '<b class="text-danger">-'.number_format($arr['deduct'], 0, ',', '.').'</b>K':'0').'</td>
                        <td class="text-center">
                          '.($payer ? '<div class="chip align-middle">
                            <img src="'.getAvatar($payer['id']).'"> '._e($payer['name']).'
                          </div>':'').'
                        </td>
                      </tr>';
              }

            }

          ?>
        </tbody>
      </table>
      <?php if($_count > $_max) echo pagination($_SERVER['REQUEST_URI'],$_count); ?>
    </div>
  </div>

</section>

<script type="text/javascript">
  $(document).ready(function () {
    $('#dtBasicExample').DataTable({
      language: {emptyTable: "Không có số liệu thống kê."},
      info: false,
      paging: false,
      scrollX: true,
      scrollY: true,
      searching: false,
      order: [ 2, 'desc' ],
      responsive: false
    });
    $('.dataTables_length').addClass('bs-select');
  });
</script>

<?php

end:


?>
