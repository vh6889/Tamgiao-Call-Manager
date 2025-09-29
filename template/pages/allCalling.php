<?php

$offer = isset($_GET['offer']) ? trim($_GET['offer']) : (isset($_COOKIE['allcalling_offer']) ? $_COOKIE['allcalling_offer'] : 'all');
$ts = isset($_GET['ts']) && !empty($_GET['ts']) ? $_GET['ts'] :  date('d/m/Y',strtotime('-29 days GMT+7 00:00'));
$te = isset($_GET['te']) && !empty($_GET['te']) ? $_GET['te'] :  date('d/m/Y',time());
$view = isset($_GET['view']) && $_GET['view'] == "group" ? "group" : 'all';


if(!isAller())
  $view = "group";

if(isset($_GET['offer']) && $_GET['offer'])
  setcookie("allcalling_offer",$offer,time()+3600*24*365);
if(isset($_GET['view']) && $_GET['view'])
  setcookie("allcalling_view",$view,time()+3600*24*365);

if(isset($_GET['offer']) && !$_GET['offer'])
  setcookie("allcalling_offer","");
if(isset($_GET['view']) && !$_GET['view'])
  setcookie("allcalling_view","");


if(!isLeader() || !isCaller()){

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

$sql = "";
if($offer != "all"){
  $sql = " and `offer`='".escape_string($offer)."' ";
}
if($view == "group"){
  $sql .= " and `group`='".$_user['group']."' ";
}
$perPage = 10;
$_count = $_db->query("select `id` from `core_orders` where `status` = 'calling' ".$sql." and (`time` >= '".(strtotime(str_replace("/","-", $ts)." GMT+7 00:00"))."' and `time` < '".(strtotime(str_replace("/","-", $te)." GMT+7 23:59"))."') and `user_call`!='' and `user_call`!='".$_user['id']."' ".$_sql_offer)->num_rows();
$_data = $_db->query("select * from `core_orders` where `status` = 'calling' ".$sql." and (`time` >= '".(strtotime(str_replace("/","-", $ts)." GMT+7 00:00"))."' and `time` < '".(strtotime(str_replace("/","-", $te)." GMT+7 23:59"))."') and `user_call`!='' and `user_call`!='".$_user['id']."' ".$_sql_offer." order by id DESC LIMIT $perPage")->fetch_array();

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
    <h2 class="section-heading mb-4"><span class="badge <?=getBgOrder('calling');?>">Calling</span> Tất cả đơn hàng đang gọi (<?=$_count;?>)</h2>
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
      <div class="mb-5">

      <form name="filter" method="GET">
        <input name="route" value="allCalling" type="hidden">
        <input name="ts" value="" type="hidden">
        <input name="te" value="" type="hidden">
      <div class="row mb-3">
        <div class="col-sx-12 col-md-4 pb20 row">
          <div class="col-sx-12 <?=(isAller() ? 'col-md-6':'col-md-12');?>">
            <select role="filter-select" id="filter-offer" class="mdb-select" name="offer">
              <option value="all" selected>All Offer</option>
              <?php if($_group['offers'] || isAller()){
                foreach ($_offers as $of) {
                  if(preg_match("#\|".$of['id'].",#si", $_group['offers']) || isAller())
                    echo '<option value="'.$of['id'].'" '.($offer == $of['id'] ? 'selected' : '').'>'._e($of['name']).'</option>';
                }
              } ?>
            </select>
          </div>
        <?php if(isAller()){ ?>
          <div class="col-sx-12 col-md-6">
            <select role="filter-select" id="filter-view" class="mdb-select" name="view">
              <option value="all" selected>All Members</option>
              <option value="group" <?=($view == "group" ? 'selected':'');?>>Group Members</option>
            </select>
          </div>
        <?php } ?>
        </div>

        <div class="col-sx-12 col-md-4 pb20">
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

    <input type="hidden" id="limit" name="limit" value="10">

    <input type="hidden" id="totalCount" name="limit" value="<?php echo $_count; ?>">

    <div class="postList" style="overflow-x:auto;margin-bottom:15px;">
      <table id="dtBasicExample" class="table table-sm table-hover table-bordered" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th class="text-left"></th>
            <th class="text-center sort-heading table_asc" id="id-asc" data-toggle="tooltip" title="ID đơn hàng">#ID</th>
            <th class="text-center" data-toggle="tooltip" title="Sản phẩm muốn mua">Offer</th>
            <th class="text-center sort-heading table_asc" id="time-asc" data-toggle="tooltip" title="Thời gian đặt hàng">Time</th>
            <th class="text-center sort-heading table_asc" id="order_name-asc" data-toggle="tooltip" title="Tên người muốn mua">Order Name</th>
            <th class="text-center" data-toggle="tooltip" title="Số điện thoại liên hệ">Order Phone</th>
            <th class="text-center" data-toggle="tooltip" title="Người gọi">Caller</th>
            <th class="text-center" data-toggle="tooltip" title="Thời gian gọi">Call Time</th>
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
                
                $caller = getUser($arr['user_call']);

                echo '<tr data-id="'.$arr['id'].'">';


                echo '<td class="text-center"><a href="?route=editOrder&id='.$arr['id'].'"><span class="btn '.getBgOrder('calling').' btn-sm waves-effect waves-light"><i class="w-fa fas fa-edit ml-1"></i></span></a></td>';


                echo '<td class="text-center">Đơn hàng #<b>'.$arr['id'].'</b></td>';
                echo '<td class="text-center"><b>'._e($arr['offer_name']).'</b></td>';
                echo '<td class="text-center">'.date('Y/m/d H:i',$arr['time']).'</td>';
                echo '<td class="text-center">'._ucwords($arr['order_name']).'';
                echo '<td class="text-center">'._e($arr['order_phone']).'';
                if($check){
                  foreach ($check as $dup) {
                    $data_dup = $_db->query("select * from `core_orders` where `id` = '".$dup['id']."' ")->fetch();
                    $caller_dup = getUser($data_dup['user_call']);
                    echo '<br><small>- '.$caller_dup['name'].' #<a target="_blank" href="?route=editOrder&id='.$dup['id'].'"><b class="text-danger">'.$dup['id'].' ('.$dup['status'].')</b></a></small>';
                  }
                }
                echo '</td>';

                echo '<td class="text-center">';
                echo '<div class="chip align-middle"><a target="_blank" href="?route=statistics&user='.$caller['id'].'"><img src="'.getAvatar($caller['id']).'"> '.(!isBanned($caller) ? _e($caller['name']) : '<strike class="text-dark"><b>'._e($caller['name']).'</b></strike>').'</a></div>';
                echo '</td>';
                echo '<td class="text-center"><b>'.get_time($arr['call_time']).'</b></td>';
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

    </div>
</section>

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
        url:'<?=$_url;?>/ajax.php?act=pagination-allCalling&ts=<?php echo $ts; ?>&te=<?php echo $te; ?>&offer=<?php echo $offer; ?>&view=<?php echo $view; ?>', 

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
    loadOrderBy('<?=$_url;?>/ajax.php?act=orderby-allCalling&ts=<?php echo $ts; ?>&te=<?php echo $te; ?>&offer=<?php echo $offer; ?>&view=<?php echo $view; ?>');   
  });
</script>


<script type="text/javascript">
function loadPagination(limit){           
  $(".load-more").text("Load more");                            
  $.ajax({
    cache:false,
    type:"POST",
    data:{limit : limit},
    beforeSend:function(){
        $(".load-more").text("Loading...");
    },
    url:'<?=$_url;?>/ajax.php?act=pagination-allCalling&ts=<?php echo $ts; ?>&te=<?php echo $te; ?>&offer=<?php echo $offer; ?>&view=<?php echo $view; ?>', 
    success:function(html){  
      $("#content").html(html);             
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