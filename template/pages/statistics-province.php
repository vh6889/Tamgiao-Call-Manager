<?php
$offer = isset($_GET['offer']) ? trim($_GET['offer']) : (isset($_COOKIE['statistic_province_offer']) ? $_COOKIE['statistic_province_offer'] : 'all');
$ts = isset($_GET['ts']) && !empty($_GET['ts']) ? $_GET['ts'] : date('d/m/Y', strtotime('-1 week GMT+7 00:00'));
$te = isset($_GET['te']) && !empty($_GET['te']) ? $_GET['te'] : date('d/m/Y', time());
$time_ts = strtotime(str_replace('/', '-', $ts) . " GMT+7 00:00");
$time_te = strtotime(str_replace('/', '-', $te) . " GMT+7 23:59");

$results = array();

$_group = getGroup($_user['group']);
$_offers = getOffer();

$sql_offer = $offer != "all" ? "AND `offer`='" . escape_string($offer) . "' " : "";

$query = $_db->query("SELECT `order_province`,`status`,COUNT(id) AS `so_luong` from `core_orders` WHERE (`time` BETWEEN '$time_ts' AND '$time_te') $sql_offer GROUP BY `order_province`,`status`")->fetch_array();
foreach ($query as $province) {
  $province_title = mb_strtolower(trim(escape_string($province['order_province'])));
  $province_title = str_replace(',', '', $province_title);

  $province_code  = Locdau($province_title);
  if (strpos($province_code, 'ho-chi-minh'))
    $province_code = 'ho-chi-minh';

  if (!isset($results[$province_code]['title']))
    $results[$province_code]['title'] = $province_title;
  $results[$province_code][$province['status']] = isset($results[$province_code][$province['status']]) ? $results[$province_code][$province['status']] + $province['so_luong'] : $province['so_luong'];

  $results[$province_code]['total'] = isset($results[$province_code]['total']) ? $results[$province_code]['total'] + $results[$province_code][$province['status']] : $results[$province_code][$province['status']];
}

/* Sort results by total number asc */
usort($results, function ($a, $b) {
  return $a['total'] <=> $b['total'];
});
/* reverse result to desc */
$results = array_reverse($results);

// echo '<pre>';
// print_r($results);die;
?>

<link rel="stylesheet" type="text/css" href="template/assets/css/addons/datatables.min.css">
<link rel="stylesheet" href="template/assets/css/addons/datatables-select.min.css">
<script type="text/javascript" src="template/assets/js/addons/datatables.min.js"></script>
<script type="text/javascript" src="template/assets/js/addons/datatables-select.min.js"></script>

<!-- Date range picker -->
<link rel="stylesheet" href="template/assets/css/daterangepicker.css">
<script type="text/javascript" src="template/assets/js/daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="template/assets/js/daterangepicker/daterangepicker.min.js"></script>

<h2 class=" section-heading">Statistics Provinces</h2>

<section class="row mb-5 pb-3">
  <div class="col-md-12 mx-auto white z-depth-1" style="overflow-x: hidden;">

    <!-- FILTER FORM -->
    <form name="filter" method="GET">
      <input name="route" value="statistics-province" type="hidden">
      <input name="ts" value="" type="hidden">
      <input name="te" value="" type="hidden">
      <div class="row mb-3">
        <div class="col-sx-12 col-md-2 pb20">
          <select role="filter-select" id="filter-offer" class="mdb-select" name="offer">
            <option value="all" selected>All Offer</option>
            <?php if ($_group['offers'] || isAller()) {
              foreach ($_offers as $of) {
                $data1 = $_db->query("select * from `core_orders` where `date` in ('" . implode("','", $list_time) . "') and `offer`='" . $of['id'] . "' " . $sql . " order by `time` asc ")->fetch_array();
                $_db->query("UPDATE `core_marks` SET `name`='" . $of['name'] . "',`mark`='" . count($data1) . "' WHERE name='" . $of['name'] . "'");
                if (preg_match("#\|" . $of['id'] . ",#si", $_group['offers']) || isAller())
                  echo '<option value="' . $of['id'] . '" ' . ($offer == $of['id'] ? 'selected' : '') . '>' . _e($of['name']) . '</option>';
              }
            }
            ?>
          </select>
        </div>
        <div class="col-sx-12 col-md-4 pb20">
          <span id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;width: 100%;display: block;margin-top: 5px;">
            <i class="fa fa-calendar"></i>&nbsp;
            <span></span>
            <i class="fa fa-caret-down"></i>
          </span>
        </div>
        <div class="col-sx-12 col-md-2 pb20">
          <button class="btn btn-primary waves-effect waves-light mx-3" type="submit">Apply Filter</button>
          <button class="btn btn-danger waves-effect waves-light" type="button" id="clear-filter">Clear</button>
        </div>
      </div>
    </form>

    <table id="dtBasicExample" class="table table-sm table-hover table-bordered scrollbar scrollbar-black bordered-black" cellspacing="0" width="100%">
      <thead>
        <tr>
          <th class="th-sm text-center sort-heading table_desc" id="title-asc" data-toggle="tooltip" title="Thành phố">T.Phố</th>
          <th class="th-sm text-center sort-heading table_desc" id="total-asc" data-toggle="tooltip" title="Tổng đơn">Total</th>
          <th class="th-sm text-center sort-heading table_desc" id="approved-asc" data-toggle="tooltip" title="Tổng đơn hàng đã giao thành công">Approved</th>
          <th class="th-sm text-center sort-heading table_desc" id="uncheck-asc" data-toggle="tooltip" title="Tổng đơn hàng chờ sử lý">Uncheck</th>
          <th class="th-sm text-center sort-heading table_desc" id="pending-asc" data-toggle="tooltip" title="Tổng đơn hàng đang sử lý">Pending</th>
          <th class="th-sm text-center sort-heading table_desc" id="rejected-asc" data-toggle="tooltip" title="Tổng đơn hàng đã từ chối">Rejected</th>
          <th class="th-sm text-center sort-heading table_desc" id="shipdelay-asc" data-toggle="tooltip" title="Tổng đơn hàng đã hẹn giao">Shipdelay</th>
          <th class="th-sm text-center sort-heading table_desc" id="shipping-asc" data-toggle="tooltip" title="Tổng đơn hàng đang giao">Shipping</th>
          <th class="th-sm text-center sort-heading table_desc" id="shiperror-asc" data-toggle="tooltip" title="Tổng đơn hàng không nhận">Shiperror</th>
          <th class="th-sm text-center sort-heading table_desc" id="shipfail-asc" data-toggle="tooltip" title="Tổng đơn hàng giao không thành công">Shipfail</th>
          <th class="th-sm text-center sort-heading table_desc" id="trashed-asc" data-toggle="tooltip" title="Tổng đơn hàng rác">Trashed</th>
        </tr>
      </thead>
      <tbody id="content">
        <?php foreach ($results as $key => $value) { ?>
          <tr class="tr-statistic <?= ($value['title'] && $value['title']!='n-a') ? '' : 'bg-light' ?>">
            <td class="text-center text-capitalize"><?= ($value['title'] && $value['title']!='n-a') ? $value['title'] : 'Null' ?></td>
            <td class="text-center"><?= isset($value['total']) ? $value['total'] : 0 ?></td>
            <td class="text-center"><?= isset($value['approved']) ? $value['approved'] : 0 ?></td>
            <td class="text-center"><?= isset($value['uncheck']) ? $value['uncheck'] : 0 ?></td>
            <td class="text-center"><?= isset($value['pending']) ? $value['pending'] : 0 ?></td>
            <td class="text-center"><?= isset($value['rejected']) ? $value['rejected'] : 0 ?></td>
            <td class="text-center"><?= isset($value['shipdelay']) ? $value['shipdelay'] : 0 ?></td>
            <td class="text-center"><?= isset($value['shipping']) ? $value['shipping'] : 0 ?></td>
            <td class="text-center"><?= isset($value['shiperror']) ? $value['shiperror'] : 0 ?></td>
            <td class="text-center"><?= isset($value['shipfail']) ? $value['shipfail'] : 0 ?></td>
            <td class="text-center"><?= isset($value['trashed']) ? $value['trashed'] : 0 ?></td>
          </tr>
        <?php } ?>
      </tbody>
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
  $(function() {
    var start = '<?= $ts; ?>';
    var end = '<?= $te; ?>';
  
    loadOrderBy('<?= $_url; ?>/ajax.php?act=statistic-province&ts='+start+'&te='+end+'&offer=<?php echo $offer; ?>');

    function cb(start, end) {
      $('#reportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
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

    $('#reportrange span').html(start + ' - ' + end);
    $("form[name=filter] input[name=ts]").val(start);
    $("form[name=filter] input[name=te]").val(end);

    $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
      $("form[name=filter] input[name=ts]").val(picker.startDate.format('DD/MM/YYYY'));
      $("form[name=filter] input[name=te]").val(picker.endDate.format('DD/MM/YYYY'));
    });

    $('#reportrange').on('cancel.daterangepicker', function(ev, picker) {
      $('#reportrange span').html(moment().subtract(6, 'days').format('DD/MM/YYYY') + ' - ' + moment().format('DD/MM/YYYY'));
      $('#reportrange').data('daterangepicker').setStartDate(moment().subtract(6, 'days').format('DD/MM/YYYY'));
      $('#reportrange').data('daterangepicker').setEndDate(moment().format('DD/MM/YYYY'));
    });
  });
</script>