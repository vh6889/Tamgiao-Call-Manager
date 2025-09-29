<?php
if(isset($_GET['type'])){
  if($_GET['type']=='uncheck'){
      $status = 0;
  }elseif($_GET['type']=='accept'){
      $status = 1;
  }elseif($_GET['type']=='refuse'){
      $status = 2;
  }
}else{
  $status = 0;
}
$_count = $_db->query("select `id` from `core_comment_replys` where `status`='".$status."' order by `id` DESC")->num_rows();
$perPage = 10;
$_data = $_db->query("select * from `core_comment_replys` where `status`='".$status."' order by id DESC LIMIT $perPage")->fetch_array();
$comment_category = $_db->query("select * from `core_comment_replys` where `status`='".$status."' order by `id` DESC")->fetch_array();
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
    <h2 class="section-heading mb-4">
    <?php 
      if(isset($_GET['type'])){
        if($_GET['type']=='uncheck'){
          echo 'Danh sách Trả lời Comment mới';
        }elseif($_GET['type']=='accept'){
          echo 'Danh sách chấp nhận trả lời comment';
        }elseif($_GET['type']=='refuse'){
          echo 'Danh sách từ chối trả lời comment';
        }
      }else{
        echo 'Danh sách Trả lời Comment';
      }
    ?>
    </h2>
  </div>
</section>

<section class="row mb-5 pb-3">
    <div class="col-md-12 mx-auto white z-depth-1" style="overflow-x: hidden;">
    <div class="row">

<div class="col-md-10" style="margin-bottom:15px;">

    <span class="custom_select_show">Hiện thị</span>

    <select name="pageSize" id="pageSize" class="custom_select">
        <option value="10">10</option>
        <option value="20">20</option>
        <option value="30">30</option>
        <option value="50">50</option>
        <option value="100">100</option>   
        <option value="250">250</option>     
        <option value="500">500</option>

    </select>

    <span class="custom_select_record">bản ghi</span> 

    <div class="clear-fix"></div>

</div>

</div>
      <div class="mb-5">  
      <input type="hidden" id="limit" name="limit" value="10">

<input type="hidden" id="totalCount" name="limit" value="<?php echo $_count; ?>">
<div class="postList" style="overflow-x:auto;margin-bottom:15px;">    
      <table style="margin-top:10px;" id="dtBasicExample" class="table table-hover table-bordered" cellspacing="0" width="100%">
          <thead>
            <tr>      
              <?php 
                  if(isset($_GET['type']))
                  {
                    if($_GET['type']=='uncheck'){

                    }else{
                      ?>
                      <th class="text-center"></th>
                      <?php
                    }
                  }
              ?>                    
              <th style="cursor:pointer" class="text-center sort-heading table_asc" id="id-asc" data-toggle="tooltip" title="ID Comment"><strong>#ID</strong></th>
              <th style="cursor:pointer" class="text-center sort-heading table_asc" id="created-asc" data-toggle="tooltip" title="Ngày đăng"><strong>Ngày đăng</strong></th>
              <th class="text-center" data-toggle="tooltip" title="Comment"><strong>Tiêu để Comment</strong></th>                              
              <th class="text-center" data-toggle="tooltip" title="Nội dung"><strong>Trả lời comment</strong></th> 
            </tr>
          </thead>
          <tbody id="content">  
            <?php 
                foreach ($_data as $row) {       
                  $comment_category = $_db->query("select * from `core_comments` where id='".$row['comment_id']."' order by `id` desc limit 1 ")->fetch();   
                  echo '<tr data-id='.$row["id"].'>';
                  if(isset($_GET['type'])) {
                      if($_GET['type']=='uncheck'){
                          
                      }else{            
                          echo '<td class="text-center" width="100">';
                          echo '<a href="?route=addCommentReply&type='.$_GET['type'].'&id='.$row["id"].'"><span class="btn btn-warning btn-sm waves-effect waves-light"><i class="w-fa fas fa-edit ml-1"></i></span></a>';
                          echo '<span role="btn-cancelCommentReply" class="btn btn-danger btn-sm buttonDelete waves-effect waves-light" data-toggle="modal" data-target="#cancelCommentReply"><i class="fas fa-times ml-1"></i></span>';
                          echo '</td>';
                      }
                  }    
                  echo'<td class="text-center">Trả lời Comment #<strong>'.$row["id"].'</strong></td>';   
                  echo '<td>'.date('d-m-Y',strtotime($row["created"])).'</td>'; 
                  if(!empty($comment_category)){
                      echo '<td class="text-left">';
                      
                      echo str_replace('.','<br>',$comment_category["comment"]);
                      
                      echo '</td>';
                  }else{
                      echo '<td></td>';
                  }    
                  echo '<td class="text-center">'; 
                  echo'<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal'.$row["id"].'">Chi tiết</button>';
                  echo '<div class="modal fade" id="exampleModal'.$row["id"].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">';
                  echo '<div class="modal-dialog" role="document">';
                  echo '<div class="modal-content">';
                  echo '<div class="modal-header">';
                  echo '<h5 class="modal-title" id="exampleModalLabel">Thông tin</h5>';
                  echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                  echo '</div>';
                  echo '<div class="modal-body text-left" style="white-space:normal;"><img style="float:left;margin-right:10px;" src='.$row["avatar"].' width="100">';
                  echo '<div style="padding-bottom:10px;">';
                  echo '<p style="margin-bottom:0;"><strong>Họ tên:</strong> '.$row["name"].'</p>';
                  echo '<p style="margin-bottom:0;"><strong>Email:</strong> '.$row["email"].'</p>';    
                  if(isset($_GET['type'])){  
                      if($_GET['type']=='uncheck'){
                          echo '<p style="margin-bottom:0;"><strong style="display:block;">Trạng thái:</strong>';    
                          echo '<div class="status_all" data-status-id='.$row["id"].' data-table="core_comment_replys">';
                          echo'<label class="radio-inline"><input style="opacity:1;position:static;" type="radio" name="status" value="1">&nbsp;&nbsp;Accept</label>&nbsp;&nbsp;';
                          echo '<label class="radio-inline"><input style="opacity:1;position:static;" type="radio" name="status" value = "2">&nbsp;&nbsp;Refuse</label>&nbsp;&nbsp;';
                          echo '</div>';
                      }
                  }
                  echo '<div style="clear:both;"></div></div>';    
                  echo '<p style="margin-bottom:0;"><strong style="display:block;">Nội dung:</strong> '.$row["content"].'</p>';    
                  echo '</p>';
                  echo '</div>';
                  echo '<div class="modal-footer">';
                  echo '<button type="button" class="btn btn-primary" data-dismiss="modal">Đóng</button>';
                  echo '</div>';
                  echo '</div>';
                  echo '</div>';
                  echo'</div>';
                  echo '</td>';
                echo '</tr>';  
              } 
            ?>   
          </tbody>
      </table>
  </div>
      <?php 
        if($_count > 10){
        ?> 
        <h3 class="load-more">Xem thêm</h3> 
        <?php 
        }

        ?>            
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
        <p class="heading">Xóa Comment Reply</p>
      </div>

      <!--Body-->
      <div class="modal-body">

        <i class="fas fa-times fa-4x animated rotateIn"></i>
        <div data-id="">
          Bạn thực sự muốn xóa Comment Reply này không?
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
  $("#pageSize").on('change',function(){

var limit_curent = parseInt($(this).val());

$("#limit").val((limit_curent));    

limit = parseInt($(this).val());          
$.ajax({

  cache:false,

  type:"POST",

  data:{limit : limit},            
  <?php 
          if(isset($_GET['type'])){
            if($_GET['type']=='uncheck'){
              ?>
              url:'<?=$_url;?>/ajax.php?act=pagination-commentReply&type=uncheck',
              <?php
            }elseif($_GET['type']=='accept'){
              ?>
              url:'<?=$_url;?>/ajax.php?act=pagination-commentReply&type=accept',
              <?php
            }elseif($_GET['type']=='refuse'){
              ?>
              url:'<?=$_url;?>/ajax.php?act=pagination-commentReply&type=refuse',
              <?php
            }
          }else{
           ?>
           url:'<?=$_url;?>/ajax.php?act=pagination-commentReply',
           <?php 
          }
        ?>			

  success:function(html){  

    $("#content").html(html);  

    loadPagination(limit);                                 											

  }                                                          

});	          

});           

$(document).on('click', '.load-more', function(){    
  var limit_curent = parseInt($("#limit").val());          
 $("#limit").val((limit_curent + 10));                

 loadPagination(limit_curent + 10);           

});   
<?php 
  if(isset($_GET['type'])){
    if($_GET['type']=='uncheck'){
      ?>
      loadOrderBy('<?=$_url;?>/ajax.php?act=orderby-commentReply&type=uncheck');  
      <?php
    }elseif($_GET['type']=='accept'){
      ?>
      loadOrderBy('<?=$_url;?>/ajax.php?act=orderby-commentReply&type=accept'); 
      <?php 
    }elseif($_GET['type']=='refuse'){
      ?>
      loadOrderBy('<?=$_url;?>/ajax.php?act=orderby-commentReply&type=refuse'); 
      <?php
    }
  }else{
    ?>
    loadOrderBy('<?=$_url;?>/ajax.php?act=orderby-commentReply');  
    <?php
  }
?>
});
function loadPagination(limit){           
  $(".load-more").text("Load more");                            
  $.ajax({
    cache:false,
    type:"POST",
    data:{limit : limit},
    beforeSend:function(){
        $(".load-more").text("Loading...");
    },
    <?php 
          if(isset($_GET['type'])){
            if($_GET['type']=='uncheck'){
              ?>
              url:'<?=$_url;?>/ajax.php?act=pagination-commentReply&type=uncheck',
              <?php
            }elseif($_GET['type']=='accept'){
              ?>
              url:'<?=$_url;?>/ajax.php?act=pagination-commentReply&type=accept',
              <?php
            }elseif($_GET['type']=='refuse'){
              ?>
              url:'<?=$_url;?>/ajax.php?act=pagination-commentReply&type=refuse',
              <?php
            }
          }else{
           ?>
           url:'<?=$_url;?>/ajax.php?act=pagination-commentReply',
           <?php 
          }
        ?>			
    success:function(html){  
      $("#content").html(html);             
      $(".load-more").text("Load more");
        var limit_check = parseInt($("#limit").val());
        var totalCount = parseInt($("#totalCount").val());    
        if(totalCount <= limit_check){
          $(".load-more").css("display",'none');
        }											
    }                                                          
  });	         
}
function checkStatus()
{
  $('.status_all input[name="status"]').click(function(){
      var table_name = 'core_comment_replys';
      var statusCheck = $(this);
      var status = $(this).val();
      var statusId = $(this).parent().parent().attr("data-status-id"); 
      var table_name = $(this).parent().parent().attr("data-table"); 
      $.ajax({
            url: '<?=$_url;?>/ajax.php?act=admincp-checkCommentStatus',
            dataType: 'json',
            data: {statusId : statusId,status : status,table_name: table_name},
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
}
$("body").on('click','[role=btn-cancelCommentReply]', function(){

var $tr = $(this).parents('tr');

$idOrder = [$tr.attr("data-id")];

});
$("#cancelCommentReply").on("click","[name=cancelCommentReply]",function(){
    $(".loader-overlay").show();
      $.ajax({
          url: '<?=$_url;?>/ajax.php?act=cancelCommentReply',
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