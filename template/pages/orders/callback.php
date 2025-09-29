<?php

$_status = 'callback';

$offer = isset($_GET['offer']) ? trim($_GET['offer']) : (isset($_COOKIE[$_status.'_offer']) ? $_COOKIE[$_status.'_offer'] : 'all');
$ts = isset($_GET['ts']) && !empty($_GET['ts']) ? $_GET['ts'] :  date('d/m/Y',strtotime('-29 days GMT+7 00:00'));
$te = isset($_GET['te']) && !empty($_GET['te']) ? $_GET['te'] :  date('d/m/Y',time());
$view = isset($_GET['view']) && in_array($_GET['view'],['me','all','group']) ? $_GET['view'] : (isset($_COOKIE[$_status.'_view']) ? $_COOKIE[$_status.'_view'] : (isAller() ? 'all' : 'group'));


if((!isLeader() || $view == "all") && !isAller())
  $view = "me";




if(isset($_GET['offer']) && $_GET['offer'])
  setcookie($_status.'_offer',$offer,time()+3600*24*365);
if(isset($_GET['view']) && $_GET['view'])
  setcookie($_status.'_view',$view,time()+3600*24*365);

if(isset($_GET['offer']) && !$_GET['offer'])
  setcookie($_status.'_offer',"");
if(isset($_GET['view']) && !$_GET['view'] || $_GET['view'] == "me")
  setcookie($_status.'_view',"");


if(!isCaller()){

  echo '<div class="alert alert-danger" role="alert">
        <h4 class="alert-heading">An error occurred!</h4>
        <p>Oops!!! You have just accessed a link that does not exist on the system or you do not have enough permissions to access.</p>
        <hr>
        <p class="mb-0">Please return to the previous page.</p>
      </div>';

  goto end;
}

$_group = getGroup($_user['group']);
// $_offers = getOffer();
$_temp = getOffer();
$_offers = array();
foreach ($_temp as $key => $value) {
    $_offers[$value['id']] = $value;
}

$sql = "";
if($offer != "all")
  $sql = " and `offer`='".escape_string($offer)."' ";



if(isCaller() || isColler()){

  if($view == "group")
    $sql .= " and `group`='".$_user['group']."' ";

  if($view == "me")
    $sql .= " and `user_call`='".$_user['id']."' ";

} else {
  $memGroup = memberGroup($_group['id']);
  $user_ship = array();
  foreach ($memGroup as $us)
    $user_ship[] = $us['id'];

  if($view == "group")
    $sql .= " and `user_ship` in ('".implode("','", $user_ship)."') ";

  if($view == "me")
    $sql .= " and `user_ship`='".$_user['id']."' ";
}


$_data = $_db->query("select * from `core_orders` where `status` = '".$_status."' ".$sql." and (`time` >= '".(strtotime(str_replace("/","-", $ts)." GMT+7 00:00"))."' and `time` < '".(strtotime(str_replace("/","-", $te)." GMT+7 23:59"))."') ".$_sql_offer." ".$sql)->fetch_array();

$_count = count($_data);

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
    <h2 class="section-heading mb-4"><span class="badge <?=getBgOrder($_status);?>">CallBack</span> Đơn hàng hẹn gọi lại (<?=$_count;?>)</h2>
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
    <div class="col-md-12 mx-auto white z-depth-1" style="overflow-x: hidden;">
      <div class="mb-5">

      <form name="filter" method="GET">
        <input name="route" value="order" type="hidden">
        <input name="type" value="callback" type="hidden">
        <input name="ts" value="" type="hidden">
        <input name="te" value="" type="hidden">
      <div class="row mb-3">
        <div class="col-sx-12 col-md-5 pb20 row">
          <div class="col-sx-12 col-md-6">
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

          <div class="col-sx-12 col-md-6">
            <select role="filter-select" id="filter-view" class="mdb-select" name="view">
              <option value="me" selected>Yourself</option>
              <?php if(isAller()){ ?>
                <option value="all" <?=($view == "all" ? 'selected' :'');?>>All Members</option>
              <?php } ?>
              <?php if(isLeader()){ ?>
              <option value="group" <?=($view == "group" ? 'selected':'');?>>Group Members</option>
              <?php } ?>
            </select>
          </div>

        </div>

        <div class="col-sx-12 col-md-4 pb20">
          <span id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;width: 100%;display: block;margin-top: 5px;">
              <i class="fa fa-calendar"></i>&nbsp;
              <span></span>
              <i class="fa fa-caret-down"></i>
          </span>
        </div>
        <div class="col-sx-12 col-md-3 pb20">
          <button class="btn btn-dark btn-sm btn-rounded waves-effect waves-light" type="submit">Apply Filter</button>
          <button class="btn btn-outline-dark btn-sm btn-rounded waves-effect waves-light" type="button" id="clear-filter">Clear</button>
        </div>
      </div>
    </form>

      <table id="dtBasicExample" class="table table-sm  table-bordered table-hover" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th class="text-left"></th>
            <th class="text-center" data-toggle="tooltip" title="ID đơn hàng">#ID</th>
            <th class="text-center" data-toggle="tooltip" title="Sản phẩm muốn mua">Offer</th>
            <th class="text-center" data-toggle="tooltip" title="Thời gian đặt hàng">Time</th>
            <th class="text-center" data-toggle="tooltip" title="Tên người muốn mua">Order Name</th>
            <th class="text-center" data-toggle="tooltip" title="Số điện thoại liên hệ">Order Phone</th>
            <th class="text-center" data-toggle="tooltip" title="Ghi chú">Note</th>
          <?php if(isLeader() || isAller()){ ?>
            <th class="text-center" data-toggle="tooltip" title="Người gọi">Caller</th>
          <?php } ?>
          </tr>
        </thead>
        <tbody>
          <?php

            if($_data){

              $isCaller = isCaller() || isColler() ? true: false;
              $isShipper = isShipper() ? true: false;

              $sql_check = array();
              foreach ($_data as $arr)
                $sql_check[] = " ( `order_phone` like '".$arr['order_phone']."' and `id`!='".$arr['id']."') ";

              $sql_check = $sql_check ? ' where '.implode(" or ", $sql_check) : '';

              $checkOrder = checkOrders($sql_check);

              foreach ($_data as $arr) {

                $check = isset($checkOrder[$arr['order_phone']]) ?  $checkOrder[$arr['order_phone']] : '';
                
                $_offer_name = strlen($arr['offer_name'])>20 ? substr($arr['offer_name'],0,20).'...' : $arr['offer_name'];
                $_offer_name = ($_offers[$arr['offer']]['offer_link']) ? '<a href="'.$_offers[$arr['offer']]['offer_link'].'" target="__blank">'.$_offer_name.'<i class="fas fa-external-link-alt ml-1 text-primary"></i></a>' : $_offer_name;

                $caller = getUser($arr['user_call']);
                $shipper = getUser($arr['user_ship']);

                echo '<tr data-id="'.$arr['id'].'">';


                echo '<td class="text-center"><a href="?route=editOrder&id='.$arr['id'].'"><span class="btn '.getBgOrder($_status).' btn-sm waves-effect waves-light"><i class="w-fa fas fa-edit ml-1"></i></span></a></td>';


                echo '<td>Đơn hàng #<b>'.$arr['id'].'</b></td>';
                echo '<td class="text-center"><b>'.$_offer_name.'</b></td>';
                echo '<td class="text-center">'.date('Y/m/d H:i',$arr['time']).'</td>';
                echo '<td class="text-center">'._e($arr['order_name']).'</td>';
                echo '<td class="text-center">'._e($arr['order_phone']).'';
                if($check){
                  foreach ($check as $dup) {
                    echo '<br><small>- Trùng đơn #<a target="_blank" href="?route=editOrder&id='.$dup['id'].'"><b class="text-danger">'.$dup['id'].' ('.$dup['status'].')</b></a></small>';
                  }
                }
                echo '<td class="text-center">'.nl2br(_e($arr['note'])).'</td>';
                if(isLeader() || isAller()){
                  echo '<td class="text-center">';
                  if($caller)
                    echo '<div class="chip align-middle">
                            <a target="_blank" href="?route=statistics&user='.$caller['id'].'">
                              <img src="'.getAvatar($caller['id']).'"> '.(!isBanned($caller) ? _e($caller['name']) : '<strike class="text-dark"><b>'._e($caller['name']).'</b></strike>').'
                            </a>
                  </div>';
                  echo '</td>';
                }
                echo '</tr>';
              }

            }

          ?>
        </tbody>
      </table>
    </div>

    </div>
</section>

<script type="text/javascript">
  $(document).ready(function(){

      $('#dtBasicExample').DataTable({
        columnDefs: [{ targets: 0, orderable: false }],
        language: {emptyTable: "Không có đơn hàng nào."},
        info: false,
        paging: true,
        pageLength: <?=(isset($_COOKIE['page_entries']) && in_array($_COOKIE['page_entries'], [ 10, 25, 50,100,250,500 ]) ? $_COOKIE['page_entries'] :100 );?>,
        lengthMenu: [
            [ 10, 25, 50,100,250,500 ],
            [ '10', '25', '50','100','250','500' ]
        ],
        scrollX: true,
        scrollY: <?=($_count > 5 ? 300 : 'true');?>,
        searching: true,
        order: [ 3, 'desc' ],
        responsive: false
      });
      $('.dataTables_length').addClass('bs-select');
      $("#dtBasicExample_length").on("change",function(){
        setCookie("page_entries",$(this).find("select").val(),365);
      });
  });
</script>


<script type="text/javascript">
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
