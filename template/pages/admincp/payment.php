<?php


$_count = $_db->query("select `id` from `core_payments` where `refid`='0'")->num_rows();
$_data = $_db->query("select * from `core_payments` where `refid`='0' order by `time` desc limit $_start,$_max")->fetch_array();

?>

<link rel="stylesheet" type="text/css" href="template/assets/css/addons/datatables.min.css">
<link rel="stylesheet" href="template/assets/css/addons/datatables-select.min.css">
<script type="text/javascript" src="template/assets/js/addons/datatables.min.js"></script>
<script type="text/javascript" src="template/assets/js/addons/datatables-select.min.js"></script>


<h2 class="section-heading mb-4">Payment Management</h2>

<section class="row mb-5 pb-3">
    <div class="col-md-12 mx-auto white z-depth-1" style="overflow-x: hidden;">
    	<div class="text-right pb-2">
  			<button role="btn-newOffer" class="btn btn-dark btn-sm btn-rounded waves-effect waves-light" data-toggle="modal" data-target="#newPayment">New Payment<i class="fas fa-plus-square ml-1"></i></button>
  		</div>
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
            <th class="th-sm text-center" data-toggle="tooltip" title="Người nhận thanh toán"> Payment Recipient</th>
            <th class="th-sm text-center"></th>
          </tr>
        </thead>
        <tbody>
          <?php

            if($_data){

              foreach ($_data as $arr) {
                $user = getUser($arr['user_id']);
                $ugroup = getGroup($user['group']);
                echo '<tr>
                        <td class="text-center"><b>#'.$arr['id'].'</b></td>
                        <td class="text-center"><b>'.date('Y/m/d',$arr['time']).'</b></td>';
                if($arr['status'] == "pending")
                  echo '<td class="text-center"><b class="number text-danger">Chưa thanh toán</b></td>';
                else
                  echo '<td class="text-center"><b class="number text-success">Đã thanh toán.</b></td>';

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
                          <div class="my-2">
                          	(<a target="_blank" href="?route=group&id='.$user['group'].'"><b class="number text-info"> <i class="fas fa-external-link-alt ml-1"></i> '._e($ugroup['name']).'</b></a>)
                          </div>
                        </td>
                        <td class="text-center">
                        	<a href="?route=settings&type=viewPayment&id='.$arr['id'].'">
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


<!-- Modal: newPayment -->
<div class="modal fade" id="newPayment" tabindex="-1" role="dialog"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <!--Header-->
      <div class="modal-header">
        <h4 class="modal-title">New Payment</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cancel">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <!--Body-->
      <div class="modal-body">

        <form name="addOffer" class="noSubmit">
        <div class="row">

          <div class="col-md-12">
            <div class="md-form form-sm mb-0">
              <select id="i-group" class="mdb-select md-form">
                <option disabled selected>Choose a group</option>
                <?php
                	if($_group = getGroup("all",["call","all","collaborator","publisher"])){
                		foreach ($_group as $g) {
                			echo '<option value="'.$g['id'].'">'._e($g['name']).' ('.addDotNumber($g['revenue_approve']*1000).'vnd)</option>';
                		}
                	}

                ?>
              </select>
              <div class="invalid-feedback">Vui lòng chọn ít nhất một nhóm muốn tạo thanh toán.</div>
            </div>
            
          </div>

        </div>
        </form>

      </div>
      <!--Footer-->
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-dark" data-dismiss="modal">Cancel</button>
        <button class="btn btn-dark waves-effect waves-light" type="submit">Create Payment</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal: newPayment -->

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



        $("#newPayment").on("click","button[type=submit]",function(){


            var group = $("#i-group");

            group.parent().siblings(".invalid-feedback").hide();

          if(!group.val()){
            group.parent().siblings(".invalid-feedback").show();
          } else {

              $(".loader-overlay").show();

              $.ajax({
                  url: '<?=$_url;?>/ajax.php?act=admincp-newPayment',
                  dataType: 'json',
                  data: {id: group.val()},
                  type: 'post',
                  success: function (response) {
                    $(".loader-overlay").hide();
                    if(response.status == 200){
                        toastr.success(response.message);
                        $('#newPayment').modal('hide');
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


	});
</script>