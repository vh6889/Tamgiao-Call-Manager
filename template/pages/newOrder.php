<?php
$offer = isset($_GET['offer']) ? trim($_GET['offer']) : (isset($_COOKIE['uncheck_offer']) ? $_COOKIE['uncheck_offer'] : 'all');
$ts = isset($_GET['ts']) && !empty($_GET['ts']) ? $_GET['ts'] :  date('d/m/Y',strtotime('-29 days GMT+7 00:00'));
$te = isset($_GET['te']) && !empty($_GET['te']) ? $_GET['te'] :  date('d/m/Y',time());

if(isset($_GET['offer']) && $_GET['offer'])
  setcookie("uncheck_offer",$offer,time()+3600*24*365);


if(isset($_GET['offer']) && !$_GET['offer'])
  setcookie("uncheck_offer","");

if(!isCaller()){

  echo '<div class="alert alert-danger" role="alert">
        <h4 class="alert-heading">An error occurred!</h4>
        <p>Oops!!! You have just accessed a link that does not exist on the system or you do not have enough permissions to access.</p>
        <hr>
        <p class="mb-0">Please return to the previous page.</p>
      </div>';

  goto end;
}


if(isBanned() && !isAdmin()){

  echo '<div class="alert alert-danger" role="alert">
        <h4 class="alert-heading">Có lỗi xảy ra!</h4>
        <p>Tài khoản của bạn hiện tại không thể nhận đơn hàng mới do đang bị cấm.</p>
        <hr>
        <p class="mb-0">Vui lòng liên hệ trưởng nhóm để biết thêm chi tiết.</p>
      </div>';

  goto end;
}



if(isShipper() && !isAller())
  autoBan();


$_calling = getCalling();

$_group = getGroup($_user['group']);
// $_offers = getOffer();
$_offers_new_number = getOfferNewOrderNumber();
$_temp = getOffer();
$_offers = array();
foreach ($_temp as $key => $value) {
    $_offers[$value['id']] = $value;
}

$sql = "";
if($offer != "all"){
  $sql = " and `offer`='".escape_string($offer)."' ";
}
$perPage = 100;
$_count = $_db->query("select * from `core_orders` where `status` = 'uncheck' ".$sql." and (`time` >= '".(strtotime(str_replace("/","-", $ts)." GMT+7 00:00"))."' and `time` < '".(strtotime(str_replace("/","-", $te)." GMT+7 23:59"))."') and `user_call`=''".$_sql_offer)->num_rows();
$_data = $_db->query("select * from `core_orders` where `status` = 'uncheck' ".$sql." and (`time` >= '".(strtotime(str_replace("/","-", $ts)." GMT+7 00:00"))."' and `time` < '".(strtotime(str_replace("/","-", $te)." GMT+7 23:59"))."') and `user_call`=''".$_sql_offer." order by id DESC LIMIT $perPage")->fetch_array();

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
    <h2 class="section-heading mb-4"><span class="badge <?=getBgOrder('uncheck');?>">UnCheck</span> Đơn hàng mới (<?=$_count_uncheck;?>)</h2>
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

<?php if($_calling) { ?>
  <div class="note note-dark my-3">
    <b class="text-dark">Chú ý:</b>
    <div>Mỗi Thành viên chỉ có thể gọi tối đa cùng lúc 1 đơn hàng. Tài khoản sẽ bị cấm nếu phát hiện cố tình ngâm đơn hàng vào các mục lỗi.</div>
    <div>Hiện tại bạn đang có một đơn hàng <a class="btn btn-dark btn-sm btn-rounded waves-effect waves-light mx-3" href="?route=calling&id=<?=$calling['id'];?>"><b>#<?=$_calling['id'];?></b></a> đang gọi.</div>
  </div>

<?php } ?>

<section class="row mb-5 pb-3">
    <div class="col-md-12 mx-auto white z-depth-1">

      <form name="filter" method="GET">
        <input name="route" value="newOrder" type="hidden">
        <input name="ts" value="" type="hidden">
        <input name="te" value="" type="hidden">
      <div class="row mb-3">
        <div class="col-sx-12 col-md-3 pb20">
          <select id="filter-offer" class="mdb-select" name="offer">
            <option value="all" selected>All Offer</option>
            <?php if($_group['offers'] || isAller()){
              foreach ($_offers as $of) {
                $orderNumber = isset($_offers_new_number[$of['id']]['new_orders']) ? $_offers_new_number[$of['id']]['new_orders'] : 0;
                if(preg_match("#\|".$of['id'].",#si", $_group['offers']) || isAller())
                  echo '<option value="'.$of['id'].'" '.($offer == $of['id'] ? 'selected' : '').'>'._e($of['name']).' <b>('.$orderNumber.')</b></option>';
              }
            } ?>
          </select>
        </div>

        <div class="col-sx-12 col-md-5 pb20">
          <span id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;width: 100%;display: block;margin-top: 5px;">
              <i class="fa fa-calendar"></i>&nbsp;
              <span></span>
              <i class="fa fa-caret-down"></i>
          </span>
        </div>
        <div class="col-sx-12 col-md-4 pb20">
          <button class="btn btn-primary waves-effect waves-light" type="submit">Apply Filter</button>
          <button class="btn btn-danger waves-effect waves-light" type="button" id="clear-filter">Clear</button>
        </div>
      </div>
    </form>
    <div class="row">  
      <div class="col-md-10" style="margin-bottom:15px;">
          <span class="custom_select_show">Hiện thị</span>
          <select name="pageSize" id="pageSize" class="custom_select">
              <option value="10">10</option>              
              <option value="50">50</option>
              <option value="100">100</option>        
              <option value="250">250</option>
              <option value="500">500</option>
              <option value="700">700</option>
              <option value="1000">1000</option>
          </select>
          <span class="custom_select_record">bản ghi</span> 
          <div class="clear-fix"></div>
      </div>
      <div class="col-md-2 text-right">
      <?php if(isAller()){ ?>
        <div class="text-right">
          <button role="btn-trashSelected" class="btn btn-danger waves-effect waves-light"  data-toggle="modal" data-target="#makeTrashed" disabled="">Make Trashes<i class="fas fa-trash ml-1"></i></button>
        </div>
        <?php } ?>
      </div>
    </div>
    <input type="hidden" id="limit" name="limit" value="10">
    <input type="hidden" id="totalCount" name="limit" value="<?php echo $_count; ?>">
    <div class="postList" style="overflow-x:auto;margin-bottom:15px;">      
      <table id="dtBasicExample" class="table table-sm table-hover table-bordered" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th class="text-left">
            <?php if(isAller()){ ?>
              <span class="form-check text-center">
                <input type="checkbox" class="form-check-input" id="checkAll">
                <label class="form-check-label" for="checkAll"></label>
              </span>
            <?php } ?>
            </th>
            <th class="text-center" data-toggle="tooltip" title="ID đơn hàng">#ID</th>
            <th class="text-center" data-toggle="tooltip" title="Sản phẩm muốn mua">Offer</th>
            <th class="text-center" data-toggle="tooltip" title="Thời gian đặt hàng">Time</th>
            <?php 
              if(isAdmin()){
              ?>
              <th class="text-center" data-toggle="tooltip" title="Landing">Landing</th>
              <?php
              }
            ?>
            <th class="text-center" data-toggle="tooltip" title="Tên người muốn mua">Order Name</th>
            <!--<th class="text-center" data-toggle="tooltip" title="Số điện thoại liên hệ">Order Phone</th>-->
            <th class="text-left" data-toggle="tooltip" title="Đơn hàng có số điện thoại giống nhau">Duplicate</th>
          </tr>
        </thead>
        <tbody id="content">
        <?php
          if($_data){

            $sql_check = array();
            foreach ($_data as $arr)
              $sql_check[] = " ( `order_phone` like '".$arr['order_phone']."' and `id`!='".$arr['id']."') ";

            $sql_check = $sql_check ? ' where '.implode(" or ", $sql_check) : '';

            $checkOrder = checkOrders($sql_check);

            foreach ($_data as $arr) {

              $check = isset($checkOrder[$arr['order_phone']]) ?  $checkOrder[$arr['order_phone']] : '';
              $_offer_name = strlen($arr['offer_name'])>20 ? substr($arr['offer_name'],0,20).'...' : $arr['offer_name'];
              $_offer_name = ($_offers[$arr['offer']]['offer_link']) ? '<a href="'.$_offers[$arr['offer']]['offer_link'].'" target="__blank">'.$_offer_name.'<i class="fas fa-external-link-alt ml-1 text-primary"></i></a>' : $_offer_name;

              echo '<tr data-id="'.$arr['id'].'">';

              if($_calling)
                echo '<td class="text-center">'.(isAller() ? '<input type="checkbox" class="form-check-input" id="order_'.$arr['id'].'" value="'.$arr['id'].'">
                      <label class="form-check-label px-3" for="order_'.$arr['id'].'"></label>':'').'<button role="btn-calling" class="btn '.getBgOrder('uncheck').' btn-sm btn-rounded waves-effect waves-light"  disabled><i class="w-fa fas fa-phone-volume ml-1"></i> Get Order</button></td>';
              else
                echo '<td class="text-center">'.(isAller() ? '<input type="checkbox" class="form-check-input" id="order_'.$arr['id'].'" value="'.$arr['id'].'">
                      <label class="form-check-label px-3" for="order_'.$arr['id'].'"></label>':'').'<button role="btn-calling" class="btn '.getBgOrder('uncheck').' btn-sm btn-rounded waves-effect waves-light"><i class="w-fa fas fa-phone-volume ml-1"></i> Get Order</button></td>';

              echo '<td class="text-center">Đơn hàng #<b>'.$arr['id'].'</b></td>';
              echo '<td class="text-center"><b>'.$_offer_name.'</b></td>';
              echo '<td class="text-center">'.date('Y/m/d H:i',$arr['time']).'</td>';
              if(isAdmin()){
                echo '<td class="text-center">'.$arr['landing'].'</td>';  
              }
              echo '<td class="text-center">'._ucwords($arr['order_name']).'</td>';              
              echo '<td class="text-left">';
              if($check){
                foreach ($check as $dup) {
                  $data_dup = $_db->query("select * from `core_orders` where `id` = '".$dup['id']."' ")->fetch();
                  $caller_dup = getUser($data_dup['user_call']);
                  echo '<p class="my-1"><small>- '.$caller_dup['name'].' #<a target="_blank" href="?route=editOrder&id='.$dup['id'].'"><b class="text-danger">'.$dup['id'].' ('.$dup['status'].')</b></a></small></p>';
                }
              }
              echo '</td>';

              echo '</tr>';
            }

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

<?php if(isAller()){ ?>
<!--Modal: modalConfirmDelete-->
<div class="modal fade" id="makeTrashed" tabindex="-1" role="dialog"
  aria-hidden="true">
  <div class="modal-dialog modal-sm modal-notify modal-danger" role="document">
    <!--Content-->
    <div class="modal-content text-center">
      <!--Header-->
      <div class="modal-header d-flex justify-content-center">
        <p class="heading">Đánh dấu là đơn hàng rác</p>
      </div>

      <!--Body-->
      <div class="modal-body">

        <i class="fas fa-times fa-4x animated rotateIn"></i>
        <div data-id="">
          Bạn thực sự muốn di chuyển những đơn hàng này vào đơn hàng rác?
        </div>
      </div>

      <!--Footer-->
      <div class="modal-footer flex-center">
      <button class="btn btn-outline-danger" type="submit">Đồng ý</button>
      <button class="btn btn-danger waves-effect" data-dismiss="modal">Hủy bỏ</button>
      </div>
    </div>
    <!--/.Content-->
  </div>
</div>
<!--Modal: modalConfirmDelete-->
<?php } ?>

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
            url:'<?=$_url;?>/ajax.php?act=pagination-newOrder&ts=<?php echo $ts; ?>&te=<?php echo $te; ?>&&offer=<?php echo $offer; ?>', 
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
        $("body").on("click","[role=btn-calling]",function(){

          var $tr = $(this).parents('tr');

            $(".loader-overlay").show();

            $.ajax({
                url: '<?=$_url;?>/ajax.php?act=selectOrder',
                dataType: 'json',
                data: {id: $tr.attr("data-id")},
                type: 'post',
                success: function (response) {
                  $(".loader-overlay").hide();
                  if(response.status == 200){
                      toastr.success(response.message);
                      setTimeout(function(){
                        location.href = "index.php?route=calling";
                      },500);
                  } else{
                    setTimeout(function(){
                      location.reload();
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
  });
</script>


<script type="text/javascript">
function loadPagination(limit){           
    $(".load-more").text("Load more");                         
    $.ajax({
      cache:false,
      type:"POST",
      data:{limit : limit},     
      url:'<?=$_url;?>/ajax.php?act=pagination-newOrder&ts=<?php echo $ts; ?>&te=<?php echo $te; ?>&&offer=<?php echo $offer; ?>', 
      success:function(html){  	  
        $("#content").html(html);      
        $('td input[type=checkbox]').on("change",function(){
          var checked = $('td input[type=checkbox]:checked');
          if(checked.length > 0) {
            $("[role=btn-trashSelected]").prop('disabled', false);
          } else {
            $("[role=btn-trashSelected]").prop('disabled', true);
          }
      });       
         $(".load-more").text("Load more");
          var limit_check = parseInt($("#limit").val());
          var totalCount = parseInt($("#totalCount").val());    
          if(totalCount < limit_check){
            $(".load-more").css("display",'none');
          }											
      }                                                          
    });	         
}
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
      $("#filter-offer").materialSelect();
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


<?php if(isAller()){ ?>
<script type="text/javascript">
  var $idOrder;
  $('#checkAll').on("change",function(){
      var checkboxes = $('td input[type=checkbox]');
      if($(this).prop('checked')) {
        checkboxes.prop('checked', true);
      } else {
        checkboxes.prop('checked', false);
      }

      var checked = $('td input[type=checkbox]:checked');
      if(checked.length > 0) {
        $("[role=btn-trashSelected]").prop('disabled', false);
      } else {
        $("[role=btn-trashSelected]").prop('disabled', true);
      }
  });
  $('td input[type=checkbox]').on("change",function(){
      var checked = $('td input[type=checkbox]:checked');
      if(checked.length > 0) {
        $("[role=btn-trashSelected]").prop('disabled', false);
      } else {
        $("[role=btn-trashSelected]").prop('disabled', true);
      }
  });

  $("#makeTrashed").on('click','[type=submit]', function(){

        $idOrder = [];

        $("td input[type=checkbox]:checked").each(function() {
            $idOrder.push(this.value);
        });

        $(".loader-overlay").show();

        $.ajax({
            url: '<?=$_url;?>/ajax.php?act=actionMake',
            dataType: 'json',
            data: {id: $idOrder.join(","),action: "trashed"},
            type: 'post',
            success: function (response) {
              $(".loader-overlay").hide();
              if(response.status == 200){
                  toastr.success(response.message);
                  setTimeout(function(){
                    location.reload();
                  },500);
              } else{
                toastr.error(response.message);
              }

            },
            error: function (response) {
              $(".loader-overlay").hide();
              toastr.error('Could not connect to API!');
            }
        });  

  });

</script>
<?php } ?>
<?php


end:


?>