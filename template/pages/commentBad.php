<?php
$_count = $_db->query("select `id` from `core_comment_bads` order by `id` DESC")->num_rows();
$perPage = 15;
$_data = $_db->query("select * from `core_comment_bads` order by `id` DESC")->fetch_array();
$comment_category = $_db->query("select * from `core_comment_bads` order by `id` DESC")->fetch_array();
$totalRecords = $_count;
$totalPages = ceil($totalRecords/$perPage);
?>
<link rel="stylesheet" type="text/css" href="template/assets/css/addons/datatables.min.css">
<link rel="stylesheet" href="template/assets/css/addons/datatables-select.min.css">
<script type="text/javascript" src="template/assets/js/addons/datatables.min.js"></script>
<script type="text/javascript" src="template/assets/js/addons/datatables-select.min.js"></script>


<link rel="stylesheet" href="template/assets/css/daterangepicker.css">
<script type="text/javascript" src="template/assets/js/daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="template/assets/js/daterangepicker/daterangepicker.min.js"></script>




<section class="row">
  <div class="col-md-6">
    <h2 class="section-heading mb-4">Danh sách Báo xấu</h2>
  </div>
</section>

<section class="row mb-5 pb-3">
    <div class="col-md-12 mx-auto white z-depth-1" style="overflow-x: hidden;">
      <div class="mb-5">
      <table style="margin-top:10px;" id="dtBasicExample" class="table table-hover table-bordered" cellspacing="0" width="100%">
          <thead>
            <tr>            
              <th class="text-center"></th>
              <th class="text-center" data-toggle="tooltip" title="ID Comment"><strong>#ID</strong></th>
              <th class="text-center" data-toggle="tooltip" title="Ngày đăng"><strong>Ngày đăng</strong></th>
              <th class="text-center" data-toggle="tooltip" title="Comment"><strong>Comment</strong></th>                              
              <th class="text-center" data-toggle="tooltip" title="Nội dung"><strong>Nội dung</strong></th> 
            </tr>
          </thead>
          <tbody id="content">     
          </tbody>
      </table>
      <div id="pagination"></div>    
		  <input type="hidden" id="totalPages" value="<?php echo $totalPages; ?>">      
    </div>
      <div class="row">
          <div class="col-md-6">
              <a href="?route=addCommentBad&action=add" type="button" class="btn btn-primary">Thêm mới</a>
          </div>          
        </div>
    </div>
    </div>
</section>
<!--Modal: modalConfirmDelete-->
<div class="modal fade" id="cancelCommentReply" tabindex="-1" role="dialog"
  aria-hidden="true">
  <div class="modal-dialog modal-sm modal-notify modal-danger" role="document">
    <!--Content-->
    <div class="modal-content text-center">
      <!--Header-->
      <div class="modal-header d-flex justify-content-center">
        <p class="heading">Xóa Báo xấu</p>
      </div>

      <!--Body-->
      <div class="modal-body">

        <i class="fas fa-times fa-4x animated rotateIn"></i>
        <div data-id="">
          Bạn thực sự muốn xóa Báo xấu này không?
        </div>
      </div>

      <!--Footer-->
      <div class="modal-footer flex-center">
      <button class="btn btn-outline-danger" type="submit" name="cancelCommentReply">Xóa</button>
      <button class="btn btn-danger waves-effect" data-dismiss="modal">Hủy bỏ</button>
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
  <?php 
        if($_count>0){
    ?>
	var totalPage = parseInt($('#totalPages').val());	
	var pag = $('#pagination').simplePaginator({
		totalPages: totalPage,
		maxButtonsVisible: 5,
		currentPage: 1,
		nextLabel: 'Next',
		prevLabel: 'Prev',
		firstLabel: 'First',
		lastLabel: 'Last',
		clickCurrentPage: true,
		pageChange: function(page) {			
			$("#content").html('<tr><td colspan="6"><strong>loading...</strong></td></tr>');
            $.ajax({
				url:'<?=$_url;?>/ajax.php?act=pagination-commentBad',
				method:"POST",
				dataType: "json",		
				data:{page:	page},
				success:function(responseData){
					$('#content').html(responseData.html);               
				}
			});
		}
	});
  <?php 
        }
  ?>

  });
</script>


<script type="text/javascript">
$("body").on('click','[role=btn-cancelCommentReply]', function(){

var $tr = $(this).parents('tr');

$idOrder = [$tr.attr("data-id")];

});
$("#cancelCommentReply").on("click","[name=cancelCommentReply]",function(){
    $(".loader-overlay").show();
      $.ajax({
          url: '<?=$_url;?>/ajax.php?act=cancelCommentBad',
          dataType: 'json',
          data: {id: $idOrder.join(",")},
          type: 'post',
          success: function (response) {              
            $(".loader-overlay").hide();
            if(response.status == 200){
                toastr.success(response.message);
                setTimeout(function(){
                  location.reload();
                },500);
            } else{
              setTimeout(function(){
                //location.reload();
              },500);
              toastr.error(response.message);
            }

          },
          error: function (response) {
            $(".loader-overlay").hide();
            toastr.error('Could not connect to API!');
          }
      });
});
$(function() {

    var start = '<?=$ts;?>';
    var end = '<?=$te;?>';

    function cb(start, end) {
        $('#reportrange span').html(start.format('DD/MM/YYYY')+ ' - ' + end.format('DD/MM/YYYY'));
        $("form[name=filter] input[name=ts]").val(start.format('DD/MM/YYYY'));
        $("form[name=filter] input[name=te]").val(end.format('DD/MM/YYYY'));
    }

    $('#reportrange').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
           'Hôm nay': [moment(), moment()],
           'Hôm qua': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           '7 ngày gần nhất': [moment().subtract(6, 'days'), moment()],
           '30 ngày gần nhất': [moment().subtract(29, 'days'), moment()],
           'Tháng này': [moment().startOf('month'), moment().endOf('month')],
           'Tháng trước': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        locale: {
            "format": "DD/MM/YYYY",
            "separator": " - ",
            "applyLabel": "Áp dụng",
            "cancelLabel": "Đặt lại",
            "fromLabel": "Từ ngày",
            "toLabel": "Đến ngày",
            "customRangeLabel": "Tùy chỉnh ngày",
            "weekLabel": "Tuần",
            "daysOfWeek": [
                "CN",
                "T2",
                "T3",
                "T4",
                "T5",
                "T6",
                "T7"
            ],
            "monthNames": [
                "Tháng 1",
                "Tháng 2",
                "Tháng 3",
                "Tháng 4",
                "Tháng 5",
                "Tháng 6",
                "Tháng 7",
                "Tháng 8",
                "Tháng 9",
                "Tháng 10",
                "Tháng 11",
                "Tháng 12"
            ],
            "firstDay": 1
        },
        showDropdowns: true,
        alwaysShowCalendars: true,
        linkedCalendars: false,
        autoApply: false,
        autoUpdateInput: true
    }, cb);


    $('#reportrange span').html(start+ ' - ' + end);
    $("form[name=filter] input[name=ts]").val(start);
    $("form[name=filter] input[name=te]").val(end);


    $("#clear-filter").on("click",function(){
      $('#reportrange span').html(moment().subtract(29, 'days').format('DD/MM/YYYY')+ ' - ' + moment().format('DD/MM/YYYY'));
      $('#reportrange').data('daterangepicker').setStartDate(moment().subtract(29, 'days').format('DD/MM/YYYY'));
      $('#reportrange').data('daterangepicker').setEndDate(moment().format('DD/MM/YYYY'));
      $("form[name=filter] input[name=ts]").val('');
      $("form[name=filter] input[name=te]").val('');
      $("#filter-offer").val('all');
      $("#filter-view").val('all');
      $("[role=filter-select]").materialSelect();
    });

  $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
      $("form[name=filter] input[name=ts]").val(picker.startDate.format('DD/MM/YYYY'));
      $("form[name=filter] input[name=te]").val(picker.endDate.format('DD/MM/YYYY'));
  });

  $('#reportrange').on('cancel.daterangepicker', function(ev, picker) {
      $('#reportrange span').html(moment().subtract(29, 'days').format('DD/MM/YYYY')+ ' - ' + moment().format('DD/MM/YYYY'));
      $('#reportrange').data('daterangepicker').setStartDate(moment().subtract(29, 'days').format('DD/MM/YYYY'));
      $('#reportrange').data('daterangepicker').setEndDate(moment().format('DD/MM/YYYY'));
  });
});
</script>

<?php


end:


?>